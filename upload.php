<?php
if ($_FILES["image"]["error"] == UPLOAD_ERR_OK) {
    $tmp = $_FILES["image"]["tmp_name"];
    $name = pathinfo($_FILES["image"]["name"], PATHINFO_FILENAME);
    $target = "images/" . $name . ".png";

    $python = 'C:\Users\bartj\AppData\Local\Programs\Python\Python313\python.exe'; // Mets ici ton chemin exact
    $cmd = escapeshellcmd("$python convert_dicom.py \"$tmp\" \"$target\"");
    exec($cmd . " 2>&1", $output, $ret);

    if ($ret !== 0) {
        echo "<h2> Erreur lors de la conversion DICOM</h2>";
        echo "<pre>" . htmlspecialchars(implode("\n", $output)) . "</pre>";
        exit;
    }
}
header("Location: index.html");
?>

