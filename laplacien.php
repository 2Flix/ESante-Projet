<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'], $_POST['strength'])) {
    $imageName = basename($_POST['image']);
    $strength = floatval($_POST['strength']);
    $inputPath = __DIR__ . '/uploads/' . $imageName;
    $outputName = 'laplacien_' . $imageName;
    $outputPath = __DIR__ . '/uploads/' . $outputName;

    $scriptPath = __DIR__ . '/laplacien.py';

    $command = "python \"$scriptPath\" \"$inputPath\" \"$outputPath\" $strength 2>&1";
    exec($command, $output, $returnCode);

    if ($returnCode === 0) {
        header("Location: traitement.php?image=" . urlencode($outputName));
        exit();
    } else {
        echo "<pre>Erreur lors du laplacien :\n";
        print_r($output);
        echo "</pre>";
    }
} else {
    echo "Paramètres manquants.";
}
?>
