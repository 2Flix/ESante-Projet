<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['image_path'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Requête invalide.']);
    exit;
}

$relativeInputPath = str_replace('..', '', $_POST['image_path']);
$inputFullPath = realpath(__DIR__ . '/../uploads/' . basename($relativeInputPath));
if (!$inputFullPath || !file_exists($inputFullPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Fichier non trouvé.']);
    exit;
}

// Récupération du seuil
$threshold = isset($_POST['threshold']) ? intval($_POST['threshold']) : null;
if ($threshold === null || $threshold < 0 || $threshold > 255) {
    http_response_code(400);
    echo json_encode(['error' => 'Seuil invalide.']);
    exit;
}

// Prépare le dossier de sortie
$outputDir = __DIR__ . '/images/processed';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$outputFile = $outputDir . '/segmented_' . time() . '.png';

// Commande Python
$cmd = [
    'python',
    escapeshellarg(__DIR__ . '/random_walker.py'),
    escapeshellarg($inputFullPath),
    escapeshellarg($outputFile),
    escapeshellarg($threshold)
];

$command = implode(' ', $cmd) . ' 2>&1';
exec($command, $output_lines, $return_var);

if ($return_var !== 0 || !file_exists($outputFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l’analyse.']);
    exit;
}

echo json_encode([
    'image' => "images/processed/" . basename($outputFile),
    'threshold' => $threshold
]);
