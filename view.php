<?php
$image = $_GET['img'];
$input_path = "images/" . $image;
$output_path = "images/processed/" . $image;

if (!file_exists('images/processed')) {
    mkdir('images/processed', 0777, true);
}

$python = 'C:\Users\bartj\AppData\Local\Programs\Python\Python313\python.exe'; // Modifier si nécessaire
$command = escapeshellcmd("$python anomaly_detection.py $input_path $output_path");
exec($command, $output, $return_var);

if ($return_var !== 0) {
    $output_path = $input_path;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Visualisation</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <h1>Analyse de l'image</h1>
    <img src="<?= htmlspecialchars($output_path) ?>" alt="Image" style="max-width:90%; border:1px solid black;" />
</body>
</html>
