<?php

declare(strict_types=1);

namespace Baselinker;

class SpringCourier
{
    const string PRODUCTION_URL = 'https://mtapi.net/';
    const string TEST_URL = 'https://mtapi.net/?testMode=1';
    const array HEADERS = ['Content-Type: application/json'];

    private string $apiKey;
    private string $apiUrl;

    // test mode should be false by default, but I don't want to change construct
    public function __construct(string $apiKey, bool $testMode = true)
    {
        $this->apiKey = $apiKey;
        $this->apiUrl = $testMode ? self::TEST_URL : self::PRODUCTION_URL;
    }

    public function newPackage(array $order, array $params): string
    {
        $endpoint = $this->apiUrl;
        $headers = self::HEADERS;

        $payload = json_encode([
            'Apikey' => $this->apiKey,
            'Command' => 'OrderShipment',
            'Shipment' => [
                'LabelFormat' => $params['label_format'] ?? 'PDF',
                'ShipperReference' => uniqid('SHIP_', true),
                'Service' => $params['service'] ?? 'TRCK',
                'Weight' => $params['weight'] ?? '1.0',
                'WeightUnit' => 'kg',
                'Value' => $params['value'] ?? '10.00',
                'Currency' => 'EUR',
                'ConsignorAddress' => [
                    'Name' => $order['sender_fullname'] ?? '',
                    'Company' => $order['sender_company'] ?? '',
                    'AddressLine1' => substr($order['sender_address'] ?? '', 0, 35),
                    'City' => substr($order['sender_city'] ?? '', 35),
                    'Zip' => $order['sender_postalcode'] ?? '',
                    'Country' => 'PL',
                    'Phone' => $order['sender_phone'] ?? '',
                ],
                'ConsigneeAddress' => [
                    'Name' => $order['delivery_fullname'] ?? '',
                    'Company' => $order['delivery_company'] ?? '',
                    'AddressLine1' => substr($order['delivery_address'] ?? '', 35),
                    'City' => substr($order['delivery_city'] ?? '', 35),
                    'Zip' => $order['delivery_postalcode'] ?? '',
                    'Country' => $order['delivery_country'] ?? '',
                    'Phone' => $order['delivery_phone'] ?? '',
                ],
            ]
        ]);

        $response = $this->makeRequest($endpoint, $headers, $payload);

        if (!isset($response['Shipment']['TrackingNumber'])) {
            throw new \Exception('Błąd: ' . ($response['Error'] ?? 'Nieznany błąd'));
        }

        return $response['Shipment']['TrackingNumber'];
    }

    public function packagePDF(string $trackingNumber): void
    {
        $endpoint = $this->apiUrl;
        $headers = ['Content-Type: application/json'];

        $payload = json_encode([
            'Apikey' => $this->apiKey,
            'Command' => 'GetShipmentLabel',
            'Shipment' => [
                'LabelFormat' => 'PDF',
                'TrackingNumber' => $trackingNumber,
            ]
        ]);

        $response = $this->makeRequest($endpoint, $headers, $payload);

        if (!isset($response['Shipment']['LabelImage'])) {
            throw new \Exception('Błąd pobierania etykiety: ' . ($response['Error'] ?? 'Nieznany błąd'));
        }


        if (!is_dir(__DIR__ . '/../public')) {
            mkdir(__DIR__ . '/../public');
        }

        $pdfData = base64_decode($response['Shipment']['LabelImage']);
        $filePath = __DIR__ . '/../public/output.pdf';
        file_put_contents($filePath, $pdfData);
    }

    private function makeRequest(string $url, array $headers, ?string $payload = null): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            throw new \Exception('Błąd API: ' . ($error ?: 'Niepoprawna odpowiedź'));
        }

        return json_decode($response, true) ?? [];
    }
}
