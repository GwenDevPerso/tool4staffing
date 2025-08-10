<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $client = $_GET['client'] ?? '';

    if (empty($client)) {
        throw new Exception('Paramètre client manquant');
    }

    $validClients = ['clienta', 'clientb', 'clientc'];
    if (!in_array($client, $validClients)) {
        throw new Exception('Paramètre client invalide. Valeurs autorisées: ' . implode(', ', $validClients));
    }

    $jsonFile = __DIR__ . '/../data/garages.json';
    if (!file_exists($jsonFile)) {
        throw new Exception('Fichier de données non trouvé');
    }

    $jsonData = file_get_contents($jsonFile);
    $garages = json_decode($jsonData, true);

    if ($garages === null) {
        throw new Exception('Erreur lors du décodage JSON');
    }

    $totalGarages = count($garages);

    $clientGarages = array_filter($garages, function ($garage) use ($client) {
        return isset($garage['customer']) && $garage['customer'] === $client;
    });

    $filteredCount = count($clientGarages);

    $carsFile = __DIR__ . '/../data/cars.json';
    $carsArray = [];
    if (file_exists($carsFile)) {
        $carsJsonData = file_get_contents($carsFile);
        $carsArray = json_decode($carsJsonData, true) ?? [];
    }

    $carsCountByGarage = [];
    foreach ($carsArray as $car) {
        if (isset($car['garageId'])) {
            $garageId = $car['garageId'];
            if (!isset($carsCountByGarage[$garageId])) {
                $carsCountByGarage[$garageId] = 0;
            }
            $carsCountByGarage[$garageId]++;
        }
    }

    $formattedGarages = array_map(function ($garage) use ($carsCountByGarage) {
        $formatted = [
            'id' => $garage['id'],
            'titre' => $garage['title'],
            'adresse' => $garage['address'],
            'client' => $garage['customer'],
            'nbVoitures' => $carsCountByGarage[$garage['id']] ?? 0
        ];

        return $formatted;
    }, $clientGarages);

    echo json_encode([
        'success' => true,
        'client' => $client,
        'garages' => array_values($formattedGarages),
        'total' => count($formattedGarages),
        'debug' => [
            'total_garages_in_json' => $totalGarages,
            'filtered_garages_count' => $filteredCount,
            'client_requested' => $client,
            'cars_loaded' => count($carsArray)
        ]
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'client' => $_GET['client'] ?? 'non défini'
    ]);
}
