<?php
// api/load_content.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    $client = $_GET['client'] ?? '';
    $module = $_GET['module'] ?? '';
    $script = $_GET['script'] ?? '';

    if (empty($client) || empty($module) || empty($script)) {
        throw new Exception('Paramètres manquants');
    }

    if (
        !preg_match('/^[a-zA-Z0-9_-]+$/', $client) ||
        !preg_match('/^[a-zA-Z0-9_-]+$/', $module) ||
        !preg_match('/^[a-zA-Z0-9_-]+$/', $script)
    ) {
        throw new Exception('Paramètres invalides');
    }

    $filePath = __DIR__ . "/../customs/{$client}/modules/{$module}/{$script}.php";

    if (!file_exists($filePath)) {
        throw new Exception("Fichier non trouvé: {$filePath}");
    }

    ob_start();
    include $filePath;
    $content = ob_get_clean();

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
