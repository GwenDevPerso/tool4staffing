<?php
// api/get_cars.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Récupérer le paramètre client
    $client = $_GET['client'] ?? '';

    // Validation du paramètre client
    if (empty($client)) {
        throw new Exception('Paramètre client manquant');
    }

    // Validation de sécurité - seuls clienta, clientb, clientc sont autorisés
    $validClients = ['clienta', 'clientb', 'clientc'];
    if (!in_array($client, $validClients)) {
        throw new Exception('Paramètre client invalide. Valeurs autorisées: ' . implode(', ', $validClients));
    }

    // Charger les données depuis le fichier JSON
    $jsonFile = __DIR__ . '/../data/cars.json';
    if (!file_exists($jsonFile)) {
        throw new Exception('Fichier de données non trouvé');
    }

    $jsonData = file_get_contents($jsonFile);
    $cars = json_decode($jsonData, true);

    if ($cars === null) {
        throw new Exception('Erreur lors du décodage JSON');
    }

    // Debug: compter toutes les voitures
    $totalCars = count($cars);

    // Filtrer les voitures par client
    $clientCars = array_filter($cars, function ($car) use ($client) {
        return isset($car['customer']) && $car['customer'] === $client;
    });

    // Debug: compter les voitures filtrées
    $filteredCount = count($clientCars);

    // Charger les données des garages (optionnel)
    $garagesjsonFile = __DIR__ . '/../data/garages.json';
    $garagesArray = [];
    if (file_exists($garagesjsonFile)) {
        $garagesJsonData = file_get_contents($garagesjsonFile);
        $garagesArray = json_decode($garagesJsonData, true) ?? [];
    }

    // Convertir les garages en tableau associatif par ID pour faciliter l'accès
    $garages = [];
    foreach ($garagesArray as $garage) {
        $garages[$garage['id']] = $garage;
    }

    // Transformer les données pour l'affichage
    $formattedCars = array_map(function ($car) use ($garages) {
        $formatted = [
            'id' => $car['id'],
            'nom' => $car['modelName'],
            'marque' => $car['brand'],
            'annee' => date('Y', $car['year']), // Convertir timestamp en année
            'puissance' => $car['power'],
            'couleur' => $car['colorHex'],
            'garageId' => $car['garageId']
        ];

        // Ajouter les infos du garage si disponibles
        if (isset($garages[$car['garageId']])) {
            $formatted['garage'] = $garages[$car['garageId']];
        }

        return $formatted;
    }, $clientCars);

    // Retourner les données avec debug
    echo json_encode([
        'success' => true,
        'client' => $client,
        'cars' => array_values($formattedCars),
        'total' => count($formattedCars),
        'debug' => [
            'total_cars_in_json' => $totalCars,
            'filtered_cars_count' => $filteredCount,
            'client_requested' => $client,
            'garages_loaded' => count($garages)
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
