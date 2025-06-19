<?php
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$imagePath = $data['imagePath'] ?? '';
$manualThreshold = $data['manualThreshold'] ?? null;

if (!$imagePath || !file_exists($imagePath)) {
  echo json_encode(['success' => false, 'error' => 'Image introuvable']);
  exit;
}

$segmentedPath = preg_replace('/(\.\w+)$/', '_segmented$1', $imagePath);
$escapedInput = escapeshellarg($imagePath);
$escapedOutput = escapeshellarg($segmentedPath);

// Si un seuil manuel est fourni, l'ajouter à la commande
if ($manualThreshold !== null) {
  $command = "python otsu.py $escapedInput $escapedOutput " . intval($manualThreshold);
} else {
  $command = "python otsu.py $escapedInput $escapedOutput";
}

exec($command, $output, $return_var);

if ($return_var !== 0) {
  echo json_encode([
    'success' => false,
    'error' => "Erreur Python : " . implode("\n", $output),
    'command' => $command,
    'output' => $output
  ]);
  exit;
}

// Récupérer le seuil depuis la sortie Python (première ligne)
$threshold = isset($output[0]) ? trim($output[0]) : 'N/A';

// Vérifier que l'image segmentée a bien été créée
if (!file_exists($segmentedPath)) {
  echo json_encode([
    'success' => false,
    'error' => "L'image segmentée n'a pas pu être créée"
  ]);
  exit;
}

echo json_encode([
  'success' => true,
  'segmentedImagePath' => $segmentedPath,
  'threshold' => $threshold
]);
?>