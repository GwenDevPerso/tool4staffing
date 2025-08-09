<?php
// customs/edit.php - Vue détaillée d'une voiture
header('Content-Type: text/html; charset=UTF-8');

// Récupérer l'ID de la voiture depuis l'URL
$carId = $_GET['id'] ?? '';

// Validation de l'ID
if (empty($carId) || !is_numeric($carId)) {
    echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
    echo '<p class="text-red-600">ID de voiture invalide ou manquant</p>';
    echo '<button onclick="goBack()" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Retour</button>';
    echo '</div>';
    exit;
}

// Charger les données des voitures
$carsFile = __DIR__ . '/../data/cars.json';
$garagesFile = __DIR__ . '/../data/garages.json';

if (!file_exists($carsFile)) {
    echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
    echo '<p class="text-red-600">Fichier de données des voitures non trouvé</p>';
    echo '<button onclick="goBack()" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Retour</button>';
    echo '</div>';
    exit;
}

// Charger et décoder les données
$carsData = json_decode(file_get_contents($carsFile), true);
$garagesData = [];

if (file_exists($garagesFile)) {
    $garagesData = json_decode(file_get_contents($garagesFile), true);
}

// Créer un tableau associatif des garages par ID
$garages = [];
foreach ($garagesData as $garage) {
    $garages[$garage['id']] = $garage;
}

// Trouver la voiture correspondante
$selectedCar = null;
foreach ($carsData as $car) {
    if ($car['id'] == $carId) {
        $selectedCar = $car;
        break;
    }
}

if (!$selectedCar) {
    echo '<div class="bg-red-50 border border-red-200 rounded-lg p-4">';
    echo '<p class="text-red-600">Voiture avec l\'ID ' . htmlspecialchars($carId) . ' non trouvée</p>';
    echo '<button onclick="goBack()" class="mt-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Retour</button>';
    echo '</div>';
    exit;
}

// Récupérer les informations du garage
$garage = $garages[$selectedCar['garageId']] ?? null;

// Convertir le timestamp en date lisible
$year = date('Y', $selectedCar['year']);
$fullDate = date('d/m/Y', $selectedCar['year']);

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
?>

<div class="max-w-4xl mx-auto bg-white">
    <!-- Header avec bouton retour -->
    <div class="flex items-center justify-between mb-6 pb-4 border-b">
        <div class="flex items-center gap-4">
            <button onclick="goBack()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 p-2 rounded-lg transition-colors" aria-label="Retour à la liste">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h1 class="text-2xl font-bold text-gray-900">Détails du véhicule</h1>
        </div>
        <div class="text-sm text-gray-500">
            ID: #<?php echo htmlspecialchars($selectedCar['id']); ?>
        </div>
    </div>

    <!-- Carte principale du véhicule -->
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informations principales -->
            <div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-8 h-8 rounded-full" style="background-color: <?php echo htmlspecialchars($selectedCar['colorHex']); ?>"></div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900"><?php echo htmlspecialchars($selectedCar['modelName']); ?></h2>
                        <p class="text-xl text-gray-600"><?php echo htmlspecialchars($selectedCar['brand']); ?></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Année de fabrication</span>
                        <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($year); ?></span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Puissance</span>
                        <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($selectedCar['power']); ?> ch</span>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Couleur</span>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded border" style="background-color: <?php echo htmlspecialchars($selectedCar['colorHex']); ?>"></div>
                            <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($selectedCar['colorHex']); ?></span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-3 border-b border-gray-200">
                        <span class="text-gray-600 font-medium">Client</span>
                        <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars(getClientName($selectedCar['customer'])); ?></span>
                    </div>

                    <div class="flex items-center justify-between py-3">
                        <span class="text-gray-600 font-medium">Date d'enregistrement</span>
                        <span class="text-gray-900 font-semibold"><?php echo htmlspecialchars($fullDate); ?></span>
                    </div>
                </div>
            </div>

            <!-- Informations du garage -->
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-4">🏢 Garage associé</h3>

                <?php if ($garage): ?>
                    <div class="bg-white rounded-lg p-6 shadow-sm border">
                        <div class="space-y-3">
                            <div>
                                <h4 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($garage['title']); ?></h4>
                                <p class="text-sm text-gray-500">ID Garage: #<?php echo htmlspecialchars($garage['id']); ?></p>
                            </div>

                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-gray-700"><?php echo htmlspecialchars($garage['address']); ?></p>
                                    <p class="text-sm text-gray-500">Client: <?php echo htmlspecialchars(getClientName($garage['customer'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <p class="text-yellow-700">
                                Informations du garage non disponibles
                                <br><span class="text-sm">ID Garage: #<?php echo htmlspecialchars($selectedCar['garageId']); ?></span>
                            </p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex justify-between items-center pt-4 border-t">
        <button onclick="goBack()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2 rounded-lg transition-colors">
            ← Retour à la liste
        </button>
    </div>
</div>

<!-- Inclure le script des fonctions de détails des voitures -->
<script src="car-details.js"></script>

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

    console.log('Vue détaillée chargée pour la voiture ID:', <?php echo json_encode($selectedCar['id']); ?>);
</script>