// customs/car-details.js - Fonctions pour la gestion des détails des voitures

/**
 * Fonction pour charger les détails d'une voiture dans la div dynamique
 * @param {number|string} carId - L'ID de la voiture à afficher
 * @param {string} clientColor - Couleur du thème client (blue, green, orange)
 */
function loadCarDetails(carId, clientColor = 'blue') {
    console.log('Chargement des détails pour la voiture ID:', carId);
    
    // Afficher un indicateur de chargement avec la couleur du client
    $('.dynamic-div').html(`
        <div class="text-center p-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-${clientColor}-600"></div>
            <p class="mt-2 text-gray-600">Chargement des détails...</p>
        </div>
    `);
    
    // Charger edit.php avec l'ID de la voiture
    $.ajax({
        url: '../../../../customs/edit.php',
        method: 'GET',
        data: {
            id: carId
        },
        success: function(response) {
            $('.dynamic-div').html(response);
            console.log('Détails de la voiture chargés avec succès');
        },
        error: function(xhr, status, error) {
            console.error('Erreur lors du chargement des détails:', error);
            const $box = $('<div/>', { class: 'bg-red-50 border border-red-200 rounded-lg p-4' });
            $('<p/>', { class: 'text-red-600' })
                .text(`Erreur lors du chargement des détails: ${String(error)}`)
                .appendTo($box);
            $('<button/>', {
                class: `mt-2 bg-${clientColor}-500 hover:bg-${clientColor}-600 text-white px-4 py-2 rounded`,
                text: 'Réessayer',
                click: () => location.reload()
            }).appendTo($box);
            $('.dynamic-div').empty().append($box);
        }
    });
}

/**
 * Fonction pour ajouter les gestionnaires de clic sur les voitures
 * @param {string} clientColor - Couleur du thème client (blue, green, orange)
 */
function addCarClickHandlers(clientColor = 'blue') {
    $('.car-card').off('click keydown').on('click keydown', function(e) {
        // Gérer le clic ou la touche Entrée
        if (e.type === 'click' || (e.type === 'keydown' && e.key === 'Enter')) {
            const carId = $(this).data('car-id');
            if (carId) {
                loadCarDetails(carId, clientColor);
            }
        }
    });
}

/**
 * Fonction pour créer une fonction loadDynamicContent spécifique à un client et module
 * @param {string} clientId - L'ID du client (clienta, clientb, clientc)
 * @param {string} module - Le module (cars, garages, etc.) - par défaut 'cars'
 */
function createLoadDynamicContent(clientId, module = 'cars') {
    return function() {
        // Recharger le contenu du module spécifié
        const dynamicDiv = $('.dynamic-div');
        const script = "ajax";

        console.log('Rechargement du contenu dynamique pour:', clientId, 'module:', module);

        $.ajax({
            url: '../../../../api/load_content.php',
            type: 'GET',
            data: {
                client: clientId,
                module: module,
                script: script
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    dynamicDiv.html(response.content);
                    console.log('Contenu rechargé avec succès');
                } else {
                    dynamicDiv.html(`<div class="text-red-500 p-4">Erreur: ${response.error}</div>`);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', error);
                dynamicDiv.html(`<div class="text-red-500 p-4">Erreur lors du rechargement: ${error}</div>`);
            }
        });
    };
}
