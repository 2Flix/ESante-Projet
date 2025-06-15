<?php
header('Content-Type: application/json');

// Verifier que la requete est bien de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperer le chemin de l'image et l'angle envoye via POST
    $imageWebPath = $_POST['imagePath'] ?? '';
    $angle = isset($_POST['angle']) ? floatval($_POST['angle']) : 90;

    // Verification : l'angle doit etre un multiple de 90 degres
    if (abs($angle % 90) > 0.01) { // Tolerance pour les erreurs de precision des floats
        echo json_encode(['status' => 'error', 'message' => 'L\'angle doit etre un multiple de 90°. Recu: ' . $angle]);
        exit;
    }
    
    // Normalisation de l'angle pour qu'il soit entre 0 et 360
    $normalizedAngle = $angle % 360;
    if ($normalizedAngle < 0) $normalizedAngle += 360;

    // Si l'angle est tres proche de 0, aucune rotation n'est necessaire
    if (abs($normalizedAngle) < 0.01) {
        echo json_encode(['status' => 'error', 'message' => 'Aucune rotation à appliquer.']);
        exit;
    }

    //Construction du chemin absolu du dossier uploads
    $baseDir = realpath(__DIR__ . '/uploads/');
    
    // Nettoyage du chemin de l'image pour eviter les attaques par traversee de repertoire
    $imageWebPath = ltrim($imageWebPath, '/');
    $imageWebPath = str_replace('../', '', $imageWebPath); 

    // Construction du chemin absolu du fichier image a partir du nom de fichier nettoye
    $imageFullPath = $baseDir . '/' . basename($imageWebPath);

    // Verification que le fichier existe et qu'il s'agit bien d'un fichier (et non d'un dossier)
    if (!file_exists($imageFullPath) || !is_file($imageFullPath)) {
        echo json_encode(['status' => 'error', 'message' => 'Image non trouvée: ' . basename($imageWebPath)]);
        exit;
    }

    // Verification de l'extension du fichier (pour s'assurer que c'est bien une image)
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    $fileExtension = strtolower(pathinfo($imageFullPath, PATHINFO_EXTENSION));
    
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['status' => 'error', 'message' => 'Format de fichier non supporté.']);
        exit;
    }

    try {
        $escapedPath = escapeshellarg($imageFullPath);
        $escapedAngle = escapeshellarg($normalizedAngle);
        $pythonScript = escapeshellarg(__DIR__ . '/rotate_script.py');
        
        // Commande shell pour executer le script Python avec les arguments
        $command = "python $pythonScript $escapedPath $escapedAngle 2>&1";
        
        // Execution de la commande
        $output = shell_exec($command);
        
        if ($output === null) {
            throw new Exception('Erreur lors de l\'exécution du script Python');
        }

        // Traitement de la sortie : recuperer la derniere ligne non vide comme chemin du fichier genere
        $lines = array_filter(array_map('trim', explode("\n", $output)));
        $newImageFullPath = end($lines);

        // Verification de l'existence du nouveau fichier
        if (!file_exists($newImageFullPath)) {
            throw new Exception('Le fichier n\'a pas été créé. Sortie Python: ' . $output);
        }

    } catch (Exception $e) {
        // Gestion des erreurs internes (avec log serveur)
        error_log("Erreur de rotation: " . $e->getMessage());
        echo json_encode([
            'status' => 'error', 
            'message' => 'Erreur lors de la rotation de l\'image: ' . $e->getMessage()
        ]);
    }

} else {
    // Requete non-POST : methode non autorisee
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée']);
}
?>
