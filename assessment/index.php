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
        <div class="mb-4 space-y-3">
            <div class="flex items-center gap-3">
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

            <div class="flex items-center gap-3">
                <label for="moduleSwitcher" class="text-sm font-medium" aria-label="Sélecteur de module">Module</label>
                <select
                    id="moduleSwitcher"
                    name="moduleSwitcher"
                    class="w-48 rounded-md border border-gray-300 bg-white p-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                    tabindex="0"
                    aria-label="Changer de module">
                    <option value="cars">🚗 Voitures</option>
                    <option value="garages" id="garagesOption" disabled>🏢 Garages</option>
                </select>
                <span id="currentModuleLabel" class="text-sm text-gray-600" aria-live="polite"></span>
            </div>
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
            }

            function getCookie(name) {
                const nameEQ = name + "=";
                const ca = document.cookie.split(';');
                for (let i = 0; i < ca.length; i++) {
                    let c = ca[i];
                    while (c.charAt(0) === ' ') c = c.substring(1, c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
                }
                return null;
            }

            function loadDynamicContent() {
                const dynamicDiv = $('.dynamic-div');
                const clientId = getCookie('currentClient') || 'clienta';
                const module = getCookie('currentModule') || 'cars';
                const script = dynamicDiv.data('script');

                console.log(`Chargement: client=${clientId}, module=${module}, script=${script}`);

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

            $("#clientSwitcher").on("change", function() {
                const selectedClient = $(this).val();

                setCookie('currentClient', selectedClient);

                $("#currentClientLabel").text(`Client actuel: ${selectedClient}`);
                $("#status").text(`Client changé vers ${selectedClient}`);

                updateModuleAvailability();

                loadDynamicContent();
            });

            $("#moduleSwitcher").on("change", function() {
                const selectedModule = $(this).val();

                setCookie('currentModule', selectedModule);

                $("#currentModuleLabel").text(`Module actuel: ${selectedModule}`);
                $("#status").text(`Module changé vers ${selectedModule}`);

                loadDynamicContent();
            });

            function updateModuleAvailability() {
                const currentClient = getCookie('currentClient') || 'clienta';
                const garagesOption = $("#garagesOption");

                if (currentClient === 'clientb') {
                    garagesOption.prop('disabled', false);
                    garagesOption.text('🏢 Garages');
                } else {
                    garagesOption.prop('disabled', true);
                    garagesOption.text('🏢 Garages (Client B uniquement)');

                    if (getCookie('currentModule') === 'garages') {
                        setCookie('currentModule', 'cars');
                        $("#moduleSwitcher").val('cars');
                        $("#currentModuleLabel").text('Module actuel: cars');
                    }
                }
            }

            function initializePage() {
                const currentClient = getCookie('currentClient') || 'clienta';
                const currentModule = getCookie('currentModule') || 'cars';

                $("#clientSwitcher").val(currentClient);
                $("#moduleSwitcher").val(currentModule);

                $("#currentClientLabel").text(`Client actuel: ${currentClient}`);
                $("#currentModuleLabel").text(`Module actuel: ${currentModule}`);

                updateModuleAvailability();

                loadDynamicContent();
            }

            initializePage();
        });
    </script>
</body>

</html>