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

$outputDir = __DIR__ . '/images/processed';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0777, true);
}
$outputFile = $outputDir . '/chanvese_' . time() . '.png';

$cmd = [
    'python',
    escapeshellarg(__DIR__ . '/chan_vese.py'),
    escapeshellarg($inputFullPath),
    escapeshellarg($outputFile)
];

if (isset($_POST['threshold']) && $_POST['threshold'] !== '') {
    $t = intval($_POST['threshold']);
    if ($t < 0 || $t > 255) {
        http_response_code(400);
        echo json_encode(['error' => 'Seuil doit être entre 0 et 255.']);
        exit;
    }
    $cmd[] = escapeshellarg($t);
}

$command = implode(' ', $cmd) . ' 2>&1';
exec($command, $output_lines, $return_var);

if ($return_var !== 0 || !file_exists($outputFile)) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l’analyse Chan-Vese.']);
    exit;
}

$threshold_line = trim(end($output_lines));
$threshold_used = is_numeric($threshold_line) ? $threshold_line : null;

echo json_encode([
    'image' => "images/processed/" . basename($outputFile),
    'threshold' => $threshold_used
]);
?>
