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
        $payload = json_encode($this->getPackageData($order, $params));
        $response = $this->makeRequest($payload);

        if (!isset($response['Shipment']['TrackingNumber'])) {
            $this->handleError("Error: " . ($response['Error'] ?? 'Unknown error'));
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

        $this->saveLabel($response['Shipment']['LabelImage']);
        $this->emitLabel($response['Shipment']['LabelImage']);
        echo "Label is created!\n";
    }

    private function saveLabel(string $labelImage): void
    {
        $dirPath = __DIR__ . '/../public';
        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        file_put_contents("$dirPath/output.pdf", base64_decode($labelImage));
    }

    private function emitLabel(string $labelImage): void
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="label.pdf"');
        echo base64_decode($labelImage);
    }

    private function validatePackageData(array $order, array $params): void
    {
        $packageData = array_merge($order, $params);

        foreach (ServicesRules::PACKAGE_REQUIRED_PARAMS as $key) {
            if (empty($packageData[$key])) {
                $this->handleError("Missing mandatory parameter: $key");
            }
        }

        $this->validatePostalCode($order);
        $this->validateServiceAvailability($packageData['service']);
        $this->validateServiceRules($packageData);
    }

    private function validatePostalCode(array $order): void
    {
        $postalCodeField = in_array($order['delivery_country'], ["US", "CA", "AU"]) ? 'delivery_state' : 'delivery_postalcode';
        if (empty($order[$postalCodeField])) {
            $this->handleError("Missing mandatory parameter: $postalCodeField");
        }
    }

    private function validateServiceAvailability(string $service): void
    {
        $response = $this->makeRequest(json_encode([
            'Apikey' => $this->apiKey,
            'Command' => self::SERVICES_LIST_COMMAND,
        ]));

        if (
            !isset($response['Services']['AllowedServices'])
            || !in_array($service, $response['Services']['AllowedServices'])
        ) {
            $this->handleError("Service $service not available");
        }
    }

    private function validateServiceRules(array $packageData): void
    {
        $service = $packageData['service'];
        $serviceRules = ServicesRules::getRules();

        if (!isset($serviceRules[$service])) {
            return;
        }

        foreach ($serviceRules[$service] as $field => $value) {
            $fieldType = ServicesRules::getFieldType($field);

            if (!isset($packageData[$field]) || !$fieldType) {
                continue;
            }

            switch ($fieldType) {
                case ServicesRules::RULE_LENGTH:
                    if (strlen($packageData[$field]) > $value) {
                        $this->handleError("Field $field is too long");
                    }
                    break;
                case ServicesRules::RULE_COUNTRY_CONTAINS:
                    if (!str_contains($value, $packageData[$field])) {
                        $this->handleError("Service not enabled for country: $packageData[$field]");
                    }
                    break;
                case ServicesRules::RULE_VALUE:
                    if ($packageData[$field] > $value) {
                        $this->handleError("Field $field value is too high");
                    }
                    break;
            }
        }
    }

    private function getPackageData(array $order, array $params): array
    {
        return [
            'Apikey' => $this->apiKey,
            'Command' => self::ORDER_SHIPMENT_COMMAND,
            'Shipment' => array_merge([
                'LabelFormat' => $params['label_format'] ?? 'PDF',
                'ShipperReference' => uniqid('PACKAGE_', true),
                'Service' => $params['service'],
                'Weight' => $params['weight'] ?? '1.0',
                'ConsignorAddress' => $this->formatAddress($order, 'sender'),
                'ConsigneeAddress' => $this->formatAddress($order, 'delivery')
            ], $this->getCountrySpecificFields($order))
        ];
    }

    private function formatAddress(array $order, string $prefix): array
    {
        return [
            'Name' => $order["{$prefix}_fullname"],
            'Company' => $order["{$prefix}_company"] ?? '',
            'AddressLine1' => $order["{$prefix}_address"],
            'City' => $order["{$prefix}_city"],
            'Zip' => $order["{$prefix}_postalcode"] ?? '',
            'Phone' => $order["{$prefix}_phone"],
            'Email' => $order["{$prefix}_email"] ?? ''
        ];
    }

    private function getCountrySpecificFields(array $order): array
    {
        return in_array($order['delivery_country'], ["US", "CA", "AU"]) ?
            ['State' => $order['delivery_state']] :
            ['Zip' => $order['delivery_postalcode']];
    }

    private function makeRequest(string $payload): array
    {
        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 10,
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? [];
    }

    private function handleError(string $error): void
    {
        throw new \Exception($error);
    }
}