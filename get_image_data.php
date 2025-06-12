<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['image'])) {
    $imageName = htmlspecialchars($_GET['image']);
    $dataFile = __DIR__ . '/../data/image_data.json';
    
    if (file_exists($dataFile)) {
        $json = file_get_contents($dataFile);
        $allData = json_decode($json, true) ?: [];
        
        if (isset($allData[$imageName])) {
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
            'message' => 'Fichier de données introuvable.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Paramètre image manquant.'
    ]);
}
?>