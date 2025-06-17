<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['image_path'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Requête invalide.']);
    exit;
}

// Sécurisation du chemin
$relativeInputPath = str_replace('..', '', $_POST['image_path']);
$inputFullPath = realpath(__DIR__ . '/../' . $relativeInputPath);
if (!$inputFullPath || !file_exists($inputFullPath)) {
    http_response_code(404);
    echo json_encode(['error' => 'Fichier non trouvé.']);
    exit;
}

// Préparer le dossier de sortie
$outputDir = __DIR__ . '/images/processed';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$outputFile = $outputDir . '/dilated_' . time() . '.png';

// Commande Python
$cmd = [
    'python',
    escapeshellarg(__DIR__ . '/dilate.py'),
    escapeshellarg($inputFullPath),
    escapeshellarg($outputFile)
];

$command = implode(' ', $cmd) . ' 2>&1';
exec($command, $output_lines, $return_var);

if ($return_var !== 0 || !file_exists($outputFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de la dilatation.']);
    exit;
}

echo json_encode([
    'image' => 'images/processed/' . basename($outputFile),
    'operation' => 'dilation'
]);
