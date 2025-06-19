<?php
// Upload normal
if (isset($_FILES['image'])) {
    $dir = __DIR__ . '/uploads/';
    if (!file_exists($dir)) mkdir($dir, 0777, true);
    $file = $_FILES['image'];
    $name = pathinfo($file["name"], PATHINFO_BASENAME);
    $target = $dir . $name;
    if (getimagesize($file["tmp_name"]) && move_uploaded_file($file["tmp_name"], $target)) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

// Vérifier si une image avec les mêmes paramètres existe déjà
if (isset($_POST['action']) && $_POST['action'] === 'check_existing_image') {
    header('Content-Type: application/json');
    
    try {
        if (!isset($_POST['filter_type']) || !isset($_POST['iterations'])) {
            throw new Exception('Paramètres manquants');
        }
        
        $filterType = $_POST['filter_type'];
        $iterations = $_POST['iterations'];
        $originalImage = isset($_POST['original_image']) ? $_POST['original_image'] : '';
        
        $dir = __DIR__ . '/uploads/';
        $filename = $filterType . '_' . $iterations . 'iter.png';
        $filepath = $dir . $filename;
        
        if (file_exists($filepath)) {
            echo json_encode([
                'exists' => true,
                'filepath' => 'uploads/' . $filename,
                'filename' => $filename
            ]);
        } else {
            echo json_encode([
                'exists' => false
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'exists' => false,
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

// Image traitée (Otsu, autres traitements - crée une nouvelle image)
if (isset($_POST['action']) && $_POST['action'] === 'save_processed_image') {
    header('Content-Type: application/json');
   
    try {
        if (!isset($_FILES['file'])) {
            throw new Exception('Aucun fichier reçu');
        }
        
        $file = $_FILES['file'];
        $dir = __DIR__ . '/uploads/';
        
        // Créer le dossier s'il n'existe pas
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        
        // Nettoyer le nom de fichier
        $name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
        $target = $dir . $name;
        
        // Vérifier si le fichier existe déjà
        if (file_exists($target)) {
            echo json_encode([
                'success' => true, 
                'filepath' => 'uploads/' . $name,
                'filename' => $name,
                'already_exists' => true
            ]);
            exit;
        }
        
        // Vérifier que c'est bien une image
        $imageInfo = getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            throw new Exception('Le fichier n\'est pas une image valide');
        }
        
        if (move_uploaded_file($file['tmp_name'], $target)) {
            echo json_encode([
                'success' => true, 
                'filepath' => 'uploads/' . $name,
                'filename' => $name,
                'already_exists' => false
            ]);
        } else {
            throw new Exception('Erreur lors du déplacement du fichier');
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

//Sauvegarde d'image depuis canvas (pour Chan-Vese)
if (isset($_POST['action']) && $_POST['action'] === 'save_canvas_image') {
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
    exit;
}
?>