<?php
// customs/edit_garage.php - Vue détaillée d'un garage
header('Content-Type: text/html; charset=UTF-8');

// Récupérer l'ID du garage depuis l'URL
$garageId = $_GET['id'] ?? '';

// Validation de l'ID
if (empty($garageId) || !is_numeric($garageId)) {
    echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
    echo '<p class="text-red-600">ID de garage invalide ou manquant</p>';
    echo '<button onclick="goBack()" class="mt-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Retour</button>';
    echo '</div>';
    exit;
}

// Charger les données des garages et des voitures
$garagesFile = __DIR__ . '/../data/garages.json';
$carsFile = __DIR__ . '/../data/cars.json';

if (!file_exists($garagesFile)) {
    echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
    echo '<p class="text-red-600">Fichier de données des garages non trouvé</p>';
    echo '<button onclick="goBack()" class="mt-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Retour</button>';
    echo '</div>';
    exit;
}

// Charger et décoder les données
$garagesData = json_decode(file_get_contents($garagesFile), true);
$carsData = [];

if (file_exists($carsFile)) {
    $carsData = json_decode(file_get_contents($carsFile), true);
}

// Trouver le garage correspondant
$selectedGarage = null;
foreach ($garagesData as $garage) {
    if ($garage['id'] == $garageId) {
        $selectedGarage = $garage;
        break;
    }
}

if (!$selectedGarage) {
    echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
    echo '<p class="text-red-600">Garage avec l\'ID ' . htmlspecialchars($garageId) . ' non trouvé</p>';
    echo '<button onclick="goBack()" class="mt-2 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">Retour</button>';
    echo '</div>';
    exit;
}

// Récupérer les voitures de ce garage
$garageCars = array_filter($carsData, function ($car) use ($garageId) {
    return isset($car['garageId']) && $car['garageId'] == $garageId;
});

// Fonction pour obtenir le nom du client
function getClientName($customer)
{
    $clientNames = [
        'clienta' => 'Client A',
        'clientb' => 'Client B',
        'clientc' => 'Client C'
    ];
    return $clientNames[$customer] ?? $customer;
}

// Calculer les statistiques
$totalCars = count($garageCars);
$totalPower = array_sum(array_column($garageCars, 'power'));
$avgPower = $totalCars > 0 ? round($totalPower / $totalCars) : 0;

// Obtenir les marques uniques
$brands = array_unique(array_column($garageCars, 'brand'));
?>

<div class="max-w-6xl mx-auto bg-white">
    <!-- Header avec bouton retour -->
    <div class="flex items-center justify-between mb-6 pb-4 border-b">
        <div class="flex items-center gap-4">
            <button onclick="goBack()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-lg transition-colors" aria-label="Retour à la liste">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Détails du garage</h1>
        </div>
        <div class="text-sm text-gray-500">
            ID: #<?php echo htmlspecialchars($selectedGarage['id']); ?>
        </div>
    </div>

    <!-- Informations principales du garage -->
    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informations du garage -->
            <div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-green-600 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($selectedGarage['title']); ?></h2>
                        <p class="text-lg text-gray-600"><?php echo htmlspecialchars(getClientName($selectedGarage['customer'])); ?></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3 py-3 border-b border-green-200">
                        <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <span class="text-gray-600 font-medium">Adresse</span>
                            <p class="text-gray-900 font-semibold"><?php echo htmlspecialchars($selectedGarage['address']); ?></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-green-200">
                        <span class="text-gray-600 font-medium">Client propriétaire</span>
                        <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars(getClientName($selectedGarage['customer'])); ?></span>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">📊 Statistiques</h3>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white rounded-lg p-4 shadow-sm border">
                        <div class="text-2xl font-bold text-green-600"><?php echo $totalCars; ?></div>
                        <div class="text-sm text-gray-600">Véhicule<?php echo $totalCars > 1 ? 's' : ''; ?></div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm border">
                        <div class="text-2xl font-bold text-green-600"><?php echo count($brands); ?></div>
                        <div class="text-sm text-gray-600">Marque<?php echo count($brands) > 1 ? 's' : ''; ?></div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm border">
                        <div class="text-2xl font-bold text-green-600"><?php echo $totalPower; ?></div>
                        <div class="text-sm text-gray-600">Puissance totale</div>
                    </div>

                    <div class="bg-white rounded-lg p-4 shadow-sm border">
                        <div class="text-2xl font-bold text-green-600"><?php echo $avgPower; ?></div>
                        <div class="text-sm text-gray-600">Puissance moyenne</div>
                    </div>
                </div>

                <?php if (count($brands) > 0): ?>
                    <div class="mt-4 p-4 bg-white rounded-lg shadow-sm border">
                        <h4 class="font-medium text-gray-900 mb-2">Marques présentes</h4>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($brands as $brand): ?>
                                <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full"><?php echo htmlspecialchars($brand); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Liste des voitures du garage -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">🚗 Véhicules du garage</h3>

        <?php if ($totalCars > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach ($garageCars as $car): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: <?php echo htmlspecialchars($car['colorHex']); ?>;"></div>
                            <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($car['modelName']); ?></h4>
                        </div>
                        <div class="space-y-1 text-sm">
                            <p class="text-gray-700"><span class="font-medium">Marque:</span> <?php echo htmlspecialchars($car['brand']); ?></p>
                            <p class="text-gray-700"><span class="font-medium">Année:</span> <?php echo date('Y', $car['year']); ?></p>
                            <p class="text-gray-700"><span class="font-medium">Puissance:</span> <?php echo htmlspecialchars($car['power']); ?> ch</p>
                        </div>
                        <div class="mt-2 pt-2 border-t border-gray-200">
                            <p class="text-xs text-gray-500">ID: <?php echo htmlspecialchars($car['id']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="text-yellow-700">Aucun véhicule n'est actuellement présent dans ce garage.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center pt-4 border-t">
        <button onclick="goBack()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition-colors">
            ← Retour à la liste
        </button>

        <div class="flex gap-3">
            <button onclick="printGarageDetails()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors">
                🖨️ Imprimer
            </button>
            <button onclick="exportGarageData()" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                📊 Exporter
            </button>
        </div>
    </div>
</div>

<!-- Inclure le script des fonctions de détails des garages -->
<script src="garage-details.js"></script>

<script>
    // Fonction pour revenir à la liste
    function goBack() {
        // Recharger le contenu dynamique principal
        if (typeof loadDynamicContent === 'function') {
            loadDynamicContent();
        } else {
            // Fallback : recharger la page si la fonction n'est pas disponible
            window.location.reload();
        }
    }

    // Fonction pour imprimer les détails du garage
    function printGarageDetails() {
        const garageData = {
            id: <?php echo json_encode($selectedGarage['id']); ?>,
            title: <?php echo json_encode($selectedGarage['title']); ?>,
            address: <?php echo json_encode($selectedGarage['address']); ?>,
            customer: <?php echo json_encode(getClientName($selectedGarage['customer'])); ?>,
            totalCars: <?php echo json_encode($totalCars); ?>,
            totalPower: <?php echo json_encode($totalPower); ?>,
            avgPower: <?php echo json_encode($avgPower); ?>,
            brands: <?php echo json_encode($brands); ?>
        };

        console.log('Impression des détails du garage:', garageData);

        // Créer une nouvelle fenêtre pour l'impression
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
        <html>
            <head>
                <title>Détails du garage #${garageData.id}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    .header { border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                    .info-row { margin: 10px 0; }
                    .label { font-weight: bold; }
                    .stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; margin: 20px 0; }
                    .stat { border: 1px solid #ddd; padding: 10px; text-align: center; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Détails du garage #${garageData.id}</h1>
                    <p>Document généré le ${new Date().toLocaleDateString('fr-FR')}</p>
                </div>
                <div class="info-row"><span class="label">Nom:</span> ${garageData.title}</div>
                <div class="info-row"><span class="label">Adresse:</span> ${garageData.address}</div>
                <div class="info-row"><span class="label">Client:</span> ${garageData.customer}</div>
                <div class="stats">
                    <div class="stat"><strong>${garageData.totalCars}</strong><br>Véhicules</div>
                    <div class="stat"><strong>${garageData.brands.length}</strong><br>Marques</div>
                    <div class="stat"><strong>${garageData.totalPower}</strong><br>Puissance totale</div>
                    <div class="stat"><strong>${garageData.avgPower}</strong><br>Puissance moyenne</div>
                </div>
                <div class="info-row"><span class="label">Marques:</span> ${garageData.brands.join(', ')}</div>
            </body>
        </html>
    `);
        printWindow.document.close();
        printWindow.print();
    }

    // Fonction pour exporter les données du garage
    function exportGarageData() {
        const garageData = {
            id: <?php echo json_encode($selectedGarage['id']); ?>,
            title: <?php echo json_encode($selectedGarage['title']); ?>,
            address: <?php echo json_encode($selectedGarage['address']); ?>,
            customer: <?php echo json_encode($selectedGarage['customer']); ?>,
            statistics: {
                totalCars: <?php echo json_encode($totalCars); ?>,
                totalPower: <?php echo json_encode($totalPower); ?>,
                avgPower: <?php echo json_encode($avgPower); ?>,
                brands: <?php echo json_encode($brands); ?>
            },
            cars: <?php echo json_encode(array_values($garageCars)); ?>,
            exportDate: new Date().toISOString()
        };

        const dataStr = JSON.stringify(garageData, null, 2);
        const dataBlob = new Blob([dataStr], {
            type: 'application/json'
        });

        const link = document.createElement('a');
        link.href = URL.createObjectURL(dataBlob);
        link.download = `garage_${garageData.id}_${new Date().toISOString().split('T')[0]}.json`;
        link.click();

        console.log('Données du garage exportées');
    }

    console.log('Vue détaillée chargée pour le garage ID:', <?php echo json_encode($selectedGarage['id']); ?>);
</script>