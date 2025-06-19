<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageName = $_POST['image'] ?? '';

    if (empty($imageName)) {
        echo json_encode(['status' => 'error', 'message' => 'Aucune image spécifiée.']);
        exit;
    }

    $baseDir = realpath(__DIR__ . '/uploads/');
    $imagePath = $baseDir . '/' . basename($imageName);

    if (!file_exists($imagePath)) {
        echo json_encode(['status' => 'error', 'message' => 'Image non trouvée.']);
        exit;
    }

    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

    if (!in_array($extension, $allowedExtensions)) {
        echo json_encode(['status' => 'error', 'message' => 'Format de fichier non supporté.']);
        exit;
    }

    try {
        $script = escapeshellarg(__DIR__ . '/median_filter.py');
        $escapedPath = escapeshellarg($imagePath);
        $kernel = isset($_POST['kernel']) ? intval($_POST['kernel']) : 5;

        if ($kernel < 3 || $kernel % 2 === 0) {
            echo json_encode(['status' => 'error', 'message' => 'La taille du noyau doit être un entier impair ≥ 3.']);
            exit;
        }

        $escapedKernel = escapeshellarg($kernel);
        $command = "python $script $escapedPath $escapedKernel 2>&1";
        $output = shell_exec($command);

        if ($output === null) {
            throw new Exception('Erreur lors de l\'exécution du script Python');
        }

        $lines = array_filter(array_map('trim', explode("\n", $output)));
        $newImagePath = end($lines);

        if (!file_exists($newImagePath)) {
            throw new Exception('Le fichier filtré n\'a pas été généré. Sortie : ' . $output);
        }

       // Tout est OK
        echo json_encode([
            'status' => 'success',
            'newImage' => basename($newImagePath)
        ]);

    } catch (Exception $e) {
        error_log("Erreur de filtre médian : " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Erreur lors du traitement : ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
}