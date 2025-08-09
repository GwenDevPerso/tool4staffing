<?php
// Récupérer juste le cookie pour affichage (pas pour requête)
$currentClient = $_COOKIE['currentClient'] ?? 'clientc';

// URL relative vers l'API depuis le dossier customs/clientc/modules/cars/
$apiUrl = '../../../../api/get_cars.php';
?>

<div class="space-y-4">
    <h2 class="text-xl font-bold text-orange-600">🏎️ Voitures Client C - Sport</h2>

    <!-- Debug: afficher le client actuel -->
    <div class="text-xs text-gray-500 mb-2">
        Cookie: <strong><?php echo htmlspecialchars($currentClient); ?></strong> |
        API: <strong><?php echo htmlspecialchars($apiUrl); ?></strong>
    </div>

    <!-- Zone pour afficher les erreurs -->
    <div id="errorBox" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
        <p id="errorMessage" class="text-red-600"></p>
    </div>

    <!-- Zone pour afficher les voitures -->
    <div id="carsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

    <!-- Footer de collection -->
    <div id="carsFooter" class="hidden bg-orange-50 border border-orange-200 rounded-lg p-3">
        <p id="carsCount" class="text-sm text-orange-600"></p>
    </div>
</div>

<!-- jQuery depuis CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        console.log("Tentative de connexion à:", "<?php echo $apiUrl; ?>");

        $.ajax({
            url: "<?php echo $apiUrl; ?>",
            method: "GET",
            data: {
                client: "clientc"
            },
            dataType: "json",
            success: function(response) {
                console.log("Réponse API:", response);

                if (response.success) {
                    // Cacher les erreurs
                    $("#errorBox").addClass("hidden");

                    // Remplir la liste des voitures
                    const cars = response.cars || [];
                    const container = $("#carsContainer");
                    container.empty();

                    if (cars.length === 0) {
                        container.html('<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"><p class="text-yellow-600">Aucune voiture disponible pour ce client.</p></div>');
                        $("#carsFooter").addClass("hidden");
                        return;
                    }

                    cars.forEach(car => {
                        container.append(`
                        <div class="car-card bg-orange-50 p-4 rounded-lg border border-orange-200 hover:bg-orange-100 transition-colors cursor-pointer"
                             data-car-id="${car.id}"
                             tabindex="0"
                             role="button"
                             aria-label="Voir les détails de ${car.nom}">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-4 h-4 rounded-full border border-gray-300" style="background-color: ${car.couleur};"></div>
                                <h3 class="font-semibold text-orange-800">${car.nom}</h3>
                                <span class="text-xs text-orange-600">→</span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-700"><span class="font-medium">Marque:</span> ${car.marque}</p>
                                <p class="text-gray-700"><span class="font-medium">Année:</span> ${car.annee}</p>
                                <p class="text-gray-700"><span class="font-medium">Puissance:</span> ${car.puissance} chevaux</p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-orange-200">
                                <p class="text-xs text-orange-600">ID: ${car.id}</p>
                            </div>
                        </div>
                    `);
                    });

                    // Afficher le footer avec le nombre de voitures
                    $("#carsFooter").removeClass("hidden");
                    $("#carsCount").html(`🏁 Collection Sport - Client C <span class="font-medium">(${cars.length} voiture${cars.length > 1 ? 's' : ''})</span>`);

                    // Ajouter les gestionnaires d'événements pour les clics sur les voitures
                    addCarClickHandlers('orange');
                } else {
                    $("#errorBox").removeClass("hidden");
                    $("#errorMessage").text(response.error || "Erreur inconnue");
                }
            },
            error: function(xhr, status, error) {
                console.log("Erreur AJAX:", {
                    xhr: xhr,
                    status: status,
                    error: error,
                    responseText: xhr.responseText
                });

                $("#errorBox").removeClass("hidden");
                $("#errorMessage").html(`
                    Erreur AJAX : ${error}<br>
                    Status: ${status}<br>
                    URL: <?php echo $apiUrl; ?><br>
                    Response: ${xhr.responseText}
                `);
            }
        });
    });

    // Créer la fonction loadDynamicContent pour ce client
    window.loadDynamicContent = createLoadDynamicContent('clientc');
</script>

<!-- Inclure le script des fonctions de détails des voitures -->
<script src="../../../../customs/car-details.js"></script>