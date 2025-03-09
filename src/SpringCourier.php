<?php

namespace Baselinker;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'ServicesRules.php';

class SpringCourier
{
    const string PRODUCTION_URL = 'https://mtapi.net/';
    const string TEST_URL = 'https://mtapi.net/?testMode=1';

    const string ORDER_SHIPMENT_COMMAND = 'OrderShipment';
    const string SHIPMENT_LABEL_COMMAND = 'GetShipmentLabel';
    const string SERVICES_LIST_COMMAND = 'GetServices';

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
        $this->validatePackageData($order, $params);
        $payload = $this->getPackageData($order, $params);
        $response = $this->makeRequest(json_encode($payload));

        if (!isset($response['Shipment']['TrackingNumber'])) {
            $this->handleError(sprintf("Error: %s", $response['Error']));
        }

        return $response['Shipment']['TrackingNumber'];
    }

    public function packagePDF(string $trackingNumber): void
    {
        $payload = json_encode([
            'Apikey' => $this->apiKey,
            'Command' => self::SHIPMENT_LABEL_COMMAND,
            'Shipment' => [
                'LabelFormat' => 'PDF',
                'TrackingNumber' => $trackingNumber,
            ]
        ]);

        $response = $this->makeRequest($payload);

        if (!isset($response['Shipment']['LabelImage'])) {
            $this->handleError('Błąd pobierania etykiety: ' . ($response['Error'] ?? 'Nieznany błąd'));
        }


        if (!is_dir(__DIR__ . '/../public')) {
            mkdir(__DIR__ . '/../public');
        }

        $pdfData = base64_decode($response['Shipment']['LabelImage']);
        $filePath = __DIR__ . '/../public/output.pdf';
        file_put_contents($filePath, $pdfData);
    }

    private function validatePackageData(array $order, array $params): void
    {
        $packageData = array_merge($order, $params);

        // Validate mandatory fields
        foreach (ServicesRules::PACKAGE_REQUIRED_PARAMS as $key) {
            if (!isset($packageData[$key])) $this->handleError("Missing mandatory parameter: $key");
        }

        $postalCodeField = in_array($order['delivery_country'], ["US", "CA", "AU"]) ? 'delivery_state' : 'delivery_postalcode';
        if (!isset($order[$postalCodeField])) {
            $this->handleError("Missing mandatory parameter: $postalCodeField");
        }

        $service = $packageData['service'];
        $response = $this->makeRequest(json_encode([
            'Apikey' => $this->apiKey,
            'Command' => static::SERVICES_LIST_COMMAND,
        ]));

        // Validate is service is available
        if (!isset($response['Services']['List'][$service])) {
            $this->handleError("Service $service not available");
        }

        // Validate service rules
        $serviceRules = ServicesRules::getRules();
        if (!isset($serviceRules[$service])) {
            return;
        }

        foreach ($serviceRules[$service] as $field => $value) {
            $fieldType = ServicesRules::getFieldType($field);
            if (!isset($packageData[$field]) || !isset($fieldType)) {
                continue;
            }

            if ($fieldType === ServicesRules::RULE_LENGTH) {
                if (strlen($packageData[$field]) > $value) {
                    $this->handleError("Field $field is too long");
                }
            }

            if ($fieldType === ServicesRules::RULE_COUNTRY_CONTAINS) {
                if (!str_contains($value, $packageData[$field])) {
                    $this->handleError("Service is not enabled for this country $packageData[$field]");
                }
            }

            if ($fieldType === ServicesRules::RULE_VALUE) {
                if ($packageData[$field] > $value) {
                    $this->handleError("Field $field value is too high");
                }
            }
        }
    }

    private function getPackageData(array $order, array $params): array
    {
        $data = [
            'Apikey' => $this->apiKey,
            'Command' => self::ORDER_SHIPMENT_COMMAND,
            'Shipment' => [
                'LabelFormat' => $params['label_format'] ?? 'PDF',
                'ShipperReference' => uniqid('PACKAGE_', true),
                'Service' => $params['service'],
                'Weight' => $params['weight'] ?? '1.0',
                'ConsignorAddress' => [
                    'Name' => $order['sender_fullname'],
                    'Company' => $order['sender_company'],
                    'AddressLine1' => $order['sender_address'],
                    'City' => $order['sender_city'],
                    'Zip' => $order['sender_postalcode'],
                    'Phone' => $order['sender_phone'],
                ],
                'ConsigneeAddress' => [
                    'Name' => $order['delivery_fullname'],
                    'Company' => $order['delivery_company'] ?? '',
                    'AddressLine1' => $order['delivery_address'],
                    'City' => $order['delivery_city'],
                    'Vat' => $order['delivery_vat'] ?? '',
                    'Country' => $order['delivery_country'],
                    'Phone' => $order['delivery_phone'],
                    'Email' => $order['delivery_email']
                ],
            ]
        ];

        if (in_array($order['delivery_country'], ["US", "CA", "AU"])) {
            $data['Shipment']['ConsigneeAddress']['State'] = $order['delivery_state'];
            $data['Shipment']['ConsignorAddress']['State'] = $order['delivery_state'];
        } else {
            $data['Shipment']['ConsigneeAddress']['Zip'] = $order['delivery_postalcode'];
            $data['Shipment']['ConsignorAddress']['Zip'] = $order['delivery_postalcode'];
        }

        return $data;
    }

    private function makeRequest(?string $payload = null): array
    {
        $ch = curl_init($this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            $this->handleError('Api Error: ' . ($error ?: 'Wrong API response'));
        }

        $result = json_decode($response, true) ?? [];

        if (in_array($result['ErrorLevel'], [1, 10])) {
            var_dump($payload);
            $this->handleError('Spring error: ' . ($result['Error'] ?: 'Undefined error'));
        }

        return $result;
    }

    private function handleError(string $error): void
    {
        throw new \Exception($error);
    }
}
