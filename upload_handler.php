<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $targetDir = __DIR__ . '/uploads/';
    if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);

    $file = $_FILES['image'];
    $filename = pathinfo($file["name"], PATHINFO_BASENAME);
    $targetFile = $targetDir . $filename;

    $check = getimagesize($file["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($file["tmp_name"], $targetFile)) {
            header("Location: /ESANTE/index.php");
            exit();
        }
    }
}
?>
