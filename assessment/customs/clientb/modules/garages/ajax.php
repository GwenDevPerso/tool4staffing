<?php
$currentClient = $_COOKIE['currentClient'] ?? 'clientb';

$apiUrl = 'http://localhost:8000/api/get_garages.php';
?>

<div class="space-y-4">
    <h2 class="text-xl font-bold text-green-600">🏢 Garages Client B - Écologique</h2>

    <div id="errorBox" class="hidden bg-red-50 border border-red-200 rounded-lg p-4">
        <p id="errorMessage" class="text-red-600"></p>
    </div>

    <div id="garagesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"></div>

    <div id="garagesFooter" class="hidden bg-green-50 border border-green-200 rounded-lg p-3">
        <p id="garagesCount" class="text-sm text-green-600"></p>
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

                if (response.success) {
                    $("#errorBox").addClass("hidden");

                    const garages = response.garages || [];
                    const container = $("#garagesContainer");
                    container.empty();

                    if (garages.length === 0) {
                        container.html('<div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"><p class="text-yellow-600">Aucun garage disponible pour ce client.</p></div>');
                        $("#garagesFooter").addClass("hidden");
                        return;
                    }

                    garages.forEach(garage => {
                        container.append(`
                        <div class="garage-card bg-green-50 p-4 rounded-lg border border-green-200 hover:bg-green-100 transition-colors cursor-pointer"
                             data-garage-id="${garage.id}"
                             tabindex="0"
                             role="button"
                             aria-label="Voir les détails de ${garage.titre}">
                            <div class="flex items-center gap-2 mb-2">
                                <h3 class="font-semibold text-green-800">${garage.titre}</h3>
                                <span class="text-xs text-green-600">→</span>
                            </div>
                            <div class="space-y-1 text-sm">
                                <p class="text-gray-700"><span class="font-medium">Adresse:</span> ${garage.adresse}</p>
                                <p class="text-gray-700"><span class="font-medium">Voitures:</span> ${garage.nbVoitures} véhicule${garage.nbVoitures > 1 ? 's' : ''}</p>
                            </div>
                            <div class="mt-3 pt-2 border-t border-green-200">
                                <p class="text-xs text-green-600">ID: ${garage.id}</p>
                            </div>
                        </div>
                    `);
                    });

                    // Afficher le footer avec le nombre de garages
                    $("#garagesFooter").removeClass("hidden");
                    $("#garagesCount").html(`🌱 Réseau Écologique - Client B <span class="font-medium">(${garages.length} garage${garages.length > 1 ? 's' : ''})</span>`);

                    // Ajouter les gestionnaires d'événements pour les clics sur les garages
                    addGarageClickHandlers('green');
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

    window.loadDynamicContent = createLoadDynamicContent('clientb', 'garages');
</script>

<script src="http://localhost:8000/customs/garage-details.js"></script>