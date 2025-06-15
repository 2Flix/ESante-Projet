<?php
header('Content-Type: application/json; charset=utf-8');

// Verifier que la requete est bien de type POST
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['image'])) {
    $imageName = $_GET['image'];
    
    // Chemin vers le fichier de données
    $dataFile = __DIR__ . '/data/image_data.json';
    
    // Verifier si le fichier existe
    if (file_exists($dataFile)) {
        $json = file_get_contents($dataFile);
        $allData = json_decode($json, true);
        
        // Verifier si des donnees existent pour cette image
        if ($allData && isset($allData[$imageName])) {
            echo json_encode([
                'success' => true,
                'data' => $allData[$imageName]
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Aucune donnée trouvée pour cette image.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Fichier de données non trouvé.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Paramètre image manquant.'
    ]);
}
?>