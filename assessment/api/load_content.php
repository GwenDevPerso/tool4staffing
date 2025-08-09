<?php
// api/load_content.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Récupérer les paramètres
    $client = $_GET['client'] ?? '';
    $module = $_GET['module'] ?? '';
    $script = $_GET['script'] ?? '';

    // Validation des paramètres
    if (empty($client) || empty($module) || empty($script)) {
        throw new Exception('Paramètres manquants');
    }

    // Validation de sécurité : s'assurer que les paramètres ne contiennent pas de caractères dangereux
    if (
        !preg_match('/^[a-zA-Z0-9_-]+$/', $client) ||
        !preg_match('/^[a-zA-Z0-9_-]+$/', $module) ||
        !preg_match('/^[a-zA-Z0-9_-]+$/', $script)
    ) {
        throw new Exception('Paramètres invalides');
    }

    // Construire le chemin du fichier
    $filePath = __DIR__ . "/../customs/{$client}/modules/{$module}/{$script}.php";

    // Vérifier que le fichier existe
    if (!file_exists($filePath)) {
        throw new Exception("Fichier non trouvé: {$filePath}");
    }

    // Capturer le contenu du fichier
    ob_start();
    include $filePath;
    $content = ob_get_clean();

    // Retourner le contenu en JSON
    echo json_encode([
        'success' => true,
        'content' => $content,
        'client' => $client,
        'module' => $module,
        'script' => $script
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
