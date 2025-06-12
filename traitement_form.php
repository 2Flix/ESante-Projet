<?php
header('Content-Type: application/json; charset=utf-8');

// Définir le fuseau horaire français
date_default_timezone_set('Europe/Paris');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = htmlspecialchars($_POST["prenom"] ?? '');
    $nom = htmlspecialchars($_POST["nom"] ?? '');
    $age = htmlspecialchars($_POST["age"] ?? '');
    $sexe = htmlspecialchars($_POST["sexe"] ?? '');
    $taille = htmlspecialchars($_POST["taille"] ?? '');
    $selectedImage = htmlspecialchars($_POST["selectedImage"] ?? '');

    // Validation des champs
    if (empty($prenom) || empty($nom) || empty($age) || empty($sexe) || empty($taille)) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur : Tous les champs sont obligatoires.'
        ]);
        exit;
    }

    if (empty($selectedImage)) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur : Veuillez sélectionner une image.'
        ]);
        exit;
    }

    // Préparer les données à sauvegarder
    $formData = [
        'prenom' => $prenom,
        'nom' => $nom,
        'age' => $age,
        'sexe' => $sexe,
        'taille' => $taille,
        'timestamp' => date('Y-m-d H:i:s')
    ];

    // Sauvegarder dans un fichier JSON - CORRECTION ICI
    $dataDir = __DIR__ . '/data/';  // Changé de '/../data/' vers '/data/'
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0777, true);
    }

    $dataFile = $dataDir . 'image_data.json';
    $allData = [];
    
    // Charger les données existantes
    if (file_exists($dataFile)) {
        $json = file_get_contents($dataFile);
        $allData = json_decode($json, true) ?: [];
    }

    // Ajouter/mettre à jour les données pour cette image
    $allData[$selectedImage] = $formData;

    // Sauvegarder
    if (file_put_contents($dataFile, json_encode($allData, JSON_PRETTY_PRINT))) {
        echo json_encode([
            'success' => true,
            'message' => 'Données sauvegardées avec succès pour l\'image ' . $selectedImage,
            'data' => $formData
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors de la sauvegarde des données.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Accès non autorisé : Ce fichier doit être appelé via une soumission de formulaire POST.'
    ]);
}
?>
