<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tool4cars</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js">
    </script>

</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
    <header class="border-b bg-white">
        <div class="mx-auto max-w-4xl p-4">
            <h1 class="text-xl font-semibold">Tool4cars</h1>
            <p class="text-sm text-gray-600">
                Démo: chargement dynamique selon le client (cookie)
            </p>
        </div>
    </header>
    <main class="mx-auto max-w-4xl p-4">
        <div class="mb-4 flex items-center gap-3">
            <label for="clientSwitcher" class="text-sm font-medium" aria-label="Sélecteur de client">Client</label>
            <select
                id="clientSwitcher"
                name="clientSwitcher"
                class="w-48 rounded-md border border-gray-300 bg-white p-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                tabindex="0"
                aria-label="Changer de client">
                <option value="clienta">Client A</option>
                <option value="clientb">Client B</option>
                <option value="clientc">Client C</option>
            </select>
            <span id="currentClientLabel" class="text-sm text-gray-600" aria-live="polite"></span>
            <div id="status" role="status" aria-live="polite" class="sr-only"></div>
        </div>
        <div
            class="dynamic-div rounded-md border bg-white p-4"
            data-module="cars"
            data-script="ajax"></div>
    </main>
    <script>
        $(document).ready(function() {

            function setCookie(name, value, days = 30) {
                const expires = new Date();
                expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
                document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
                console.log(`Cookie mis à jour: ${name}=${value}`);
            }

            function getCookie(name) {
                const nameEQ = name + "=";
                const ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                }
                console.log(`Cookie non trouvé: ${name}`);
                return null;
            }

            // Fonction pour charger le contenu dynamique
            function loadDynamicContent() {
                const dynamicDiv = $('.dynamic-div');
                const clientId = getCookie('currentClient') || 'clienta';
                const module = dynamicDiv.data('module');
                const script = dynamicDiv.data('script');

                console.log(`Chargement: client=${clientId}, module=${module}, script=${script}`);

                // Afficher un indicateur de chargement
                dynamicDiv.html('<div class="text-center p-4"><span class="text-gray-500">Chargement...</span></div>');

                $.ajax({
                    url: 'api/load_content.php',
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
                            console.log('Contenu chargé avec succès');
                        } else {
                            dynamicDiv.html(`<div class="text-red-500 p-4">Erreur: ${response.error}</div>`);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX:', error);
                        dynamicDiv.html(`<div class="text-red-500 p-4">Erreur lors du chargement du contenu: ${error}</div>`);
                    }
                });
            }

            // Gestionnaire de changement de client
            $("#clientSwitcher").on("change", function() {
                const selectedClient = $(this).val();

                // Mettre à jour le cookie
                setCookie('currentClient', selectedClient);

                // Mettre à jour l'affichage
                $("#currentClientLabel").text(`Client actuel: ${selectedClient}`);
                $("#status").text(`Client changé vers ${selectedClient}`);

                console.log(`Cookie mis à jour: ${selectedClient}`);

                // Recharger le contenu dynamique
                loadDynamicContent();
            });

            // Initialisation au chargement de la page
            function initializePage() {
                // Récupérer le client depuis le cookie ou utiliser clienta par défaut
                const currentClient = getCookie('currentClient') || 'clienta';

                // Mettre à jour le sélecteur
                $("#clientSwitcher").val(currentClient);

                // Mettre à jour l'affichage
                $("#currentClientLabel").text(`Client actuel: ${currentClient}`);

                console.log(`Initialisation avec client: ${currentClient}`);

                // Charger le contenu initial
                loadDynamicContent();
            }

            // Lancer l'initialisation
            initializePage();
        });
    </script>
</body>

</html>