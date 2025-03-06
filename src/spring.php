<?php

declare(strict_types=1);

use Baselinker\SpringCourier;

require_once __DIR__ . DIRECTORY_SEPARATOR . 'SpringCourier.php';

$order = [
    'sender_company' => 'BaseLinker',
    'sender_fullname' => 'Jan Kowalski',
    'sender_address' => 'Kopernika 10',
    'sender_city' => 'Gdansk',
    'sender_postalcode' => '80208',
    'sender_email' => '',
    'sender_phone' => '666666666',

    'delivery_company' => 'Spring GDS',
    'delivery_fullname' => 'Maud Driant',
    'delivery_address' => 'Strada Foisorului, Nr. 16, Bl. F11C, Sc. 1, Ap. 10',
    'delivery_city' => 'Bucuresti, Sector 3',
    'delivery_postalcode' => '031179',
    'delivery_country' => 'RO',
    'delivery_email' => 'john@doe.com',
    'delivery_phone' => '555555555',
];


$params = [
    'api_key' => 'ed1e2e1567b781d6',
    'label_format' => 'PDF',
    'service' => 'EXPR',
];


try {
    // 1. Create courier object
    $courier = new SpringCourier($params['api_key']);

    // 2. Create shipment
    $trackingNumber = $courier->newPackage($order, $params);

    // 3. Get shipping label and force a download dialog
    $courier->packagePDF($trackingNumber);
} catch (Exception $e) {
    echo $e->getMessage() . PHP_EOL;
    exit;
}