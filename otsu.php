<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['image_path'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Requête invalide.']);
    exit;
}

// Sécurisation du chemin
$relativeInputPath = str_replace('..', '', $_POST['image_path']);
$inputFullPath    = realpath(__DIR__ . '/../uploads/' . basename($relativeInputPath));
if (!$inputFullPath || !file_exists($inputFullPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Fichier non trouvé.']);
    exit;
}

// Prépare le dossier de sortie
$outputDir = __DIR__ . '/images/processed';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$outputFile = $outputDir . '/segmented_' . time() . '.png';

// Construire la commande Python
$cmd = [
    'python', 
    escapeshellarg(__DIR__ . '/otsu.py'),
    escapeshellarg($inputFullPath),
    escapeshellarg($outputFile)
];

// Exécution
$command = implode(' ', $cmd) . ' 2>&1';
exec($command, $output_lines, $return_var);

// Debug (optionnel)
// error_log("CMD: $command");
// error_log("OUT: " . implode("\n", $output_lines));

if ($return_var !== 0 || !file_exists($outputFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l’analyse.']);
    exit;
}

// Le dernier echo du script Python est le seuil utilisé
$threshold_line = trim(end($output_lines));
$threshold_used = is_numeric($threshold_line) ? $threshold_line : null;

// Réponse JSON
echo json_encode([
    'image'     => "images/processed/" . basename($outputFile),
    'threshold' => $threshold_used
]);