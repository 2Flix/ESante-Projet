<?php
header('Content-Type: application/json');

try {
    if (!isset($_POST['image_data']) || !isset($_POST['filename'])) {
        throw new Exception('Données manquantes');
    }
    
    $imageData = $_POST['image_data'];
    $filename = $_POST['filename'];
    
    $dir = __DIR__ . '/uploads/';
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
    
    // Nettoyer le nom de fichier
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
    $target = $dir . $filename;
    
    // Vérifier si le fichier existe déjà
    if (file_exists($target)) {
        echo json_encode([
            'success' => true,
            'filepath' => 'uploads/' . $filename,
            'filename' => $filename,
            'already_exists' => true
        ]);
        exit;
    }
    
    // Décoder les données base64
    if (strpos($imageData, 'data:image/') === 0) {
        $imageData = substr($imageData, strpos($imageData, ',') + 1);
    }
    
    $decodedData = base64_decode($imageData);
    if ($decodedData === false) {
        throw new Exception('Erreur lors du décodage des données image');
    }
    
    if (file_put_contents($target, $decodedData) !== false) {
        echo json_encode([
            'success' => true,
            'filepath' => 'uploads/' . $filename,
            'filename' => $filename,
            'already_exists' => false
        ]);
    } else {
        throw new Exception('Erreur lors de l\'écriture du fichier');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>