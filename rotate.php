<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imageWebPath = $_POST['imagePath'];
    $angle = isset($_POST['angle']) ? floatval($_POST['angle']) : 90; // Récupère l'angle, 90 par défaut si non spécifié

    // Sécurité : empêcher injections et accès hors dossier
    $baseDir = realpath(__DIR__ . '/../uploads/');
    $imageFullPath = realpath($imageWebPath);

    if ($imageFullPath && strpos($imageFullPath, $baseDir) === 0 && file_exists($imageFullPath)) {
        // Construire la commande pour appeler le script Python
        // On passe l'angle comme deuxième argument
        $command = escapeshellcmd("python rotate_script.py \"$imageFullPath\" $angle");

        // Exécuter la commande et capturer la sortie (le nouveau chemin de l'image)
        $output = shell_exec($command);

        // Nettoyer la sortie pour s'assurer que c'est bien un chemin
        $newImageFullPath = trim($output);

        // Vérifie si un nouveau chemin a été retourné et si le fichier existe
        if (!empty($newImageFullPath) && file_exists($newImageFullPath)) {
            // Convertir le chemin absolu du serveur en chemin web relatif
            $relativeNewImagePath = str_replace($baseDir, '../uploads', $newImageFullPath);
            $relativeNewImagePath = str_replace('\\', '/', $relativeNewImagePath); // Pour compatibilité Windows/Linux

            echo json_encode([
                'status' => 'success',
                'newImagePath' => $relativeNewImagePath
            ]);
        } else {
            error_log("Erreur Python ou nouveau fichier non trouvé. Output Python: " . $output);
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la rotation ou création du nouveau fichier.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Image non trouvée ou non autorisée.']);
    }
}
?>
