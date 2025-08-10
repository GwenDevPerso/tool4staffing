<?php
$currentClient = $_COOKIE['currentClient'] ?? 'clientb';

$apiUrl = 'http://localhost:8000/api/get_cars.php';
?>

<div class="space-y-4">
    <h2 class="text-xl font-bold text-green-600">🚙 Voitures Client B</h2>

    <!-- Zone pour afficher les erreurs -->
    <div id="errorBox" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
        <p id="errorMessage" class="text-red-600"></p>
    </div>

    <!-- Zone pour afficher les voitures -->
    <div id="carsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

    <!-- Footer de collection -->
    <div id="carsFooter" class="hidden bg-green-50 border border-green-200 rounded-lg p-3">
        <p id="carsCount" class="text-sm text-green-600"></p>
    </div>
</div>

<!-- jQuery depuis CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $.ajax({
            url: "<?php echo $apiUrl; ?>",
            method: "GET",
            data: {
                client: "clientb"
            },
            dataType: "json",
            success: function(response) {
                console.log("Réponse API:", response);

                if (response.success) {
                    $("#errorBox").addClass("hidden");

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
                        <div class="car-card bg-green-50 p-4 rounded-lg border border-green-200 hover:bg-green-100 transition-colors cursor-pointer"
                             data-car-id="${car.id}"
                             tabindex="0"
                             role="button"
                             aria-label="Voir les détails de ${car.nom}">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-semibold text-green-800">${car.nom.toLowerCase()}</h3>
                                <span class="text-xs text-green-600">→</span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-700"><span class="font-medium">Marque:</span> ${car.marque}</p>
                                <p class="text-gray-700"><span class="font-medium">Garage:</span> ${car.garage ? car.garage.title : 'Aucun garage'}</p>
                            </div>
                        </div>
                    `);
                    });

                    $("#carsFooter").removeClass("hidden");
                    $("#carsCount").html(`🌱 Gamme Écologique - Client B <span class="font-medium">(${cars.length} voiture${cars.length > 1 ? 's' : ''})</span>`);

                    addCarClickHandlers('green');
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
</script>

<script src="../../../../customs/car-details.js"></script>

<script>
    window.loadDynamicContent = createLoadDynamicContent('clientb', 'cars');
</script>