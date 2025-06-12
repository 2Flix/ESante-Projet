<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'], $_POST['sigma'], $_POST['strength'])) {
    $imageName = basename($_POST['image']);
    $sigma = floatval($_POST['sigma']);
    $strength = floatval($_POST['strength']);
    $inputPath = __DIR__ . '/uploads/' . $imageName;
    $outputName = 'combined_' . $imageName;
    $outputPath = __DIR__ . '/uploads/' . $outputName;

    $scriptPath = __DIR__ . '/combined.py';

    $command = "python \"$scriptPath\" \"$inputPath\" \"$outputPath\" $sigma $strength 2>&1";
    exec($command, $output, $returnCode);

    if ($returnCode === 0) {
        header("Location: analyse.php?image=" . urlencode($outputName));
        exit();
    } else {
        echo "<pre>Erreur lors du traitement combiné :\n";
        print_r($output);
        echo "</pre>";
    }
} else {
    echo "Paramètres manquants.";
}
?>
