<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'], $_POST['sigma'])) {
    $imageName = basename($_POST['image']);
    $sigma = floatval($_POST['sigma']);
    $inputPath = __DIR__ . '/uploads/' . $imageName;
    $outputName = 'gaussian_' . $imageName;
    $outputPath = __DIR__ . '/uploads/' . $outputName;

    $scriptPath = __DIR__ . '/gaussian_blur.py';

    $command = "python \"$scriptPath\" \"$inputPath\" \"$outputPath\" $sigma 2>&1";
    exec($command, $output, $returnCode);

    if ($returnCode === 0) {
        header("Location: analyse.php?image=" . urlencode($outputName));
        exit();
    } else {
        echo "<pre>Erreur lors du flou gaussien :\n";
        print_r($output);
        echo "</pre>";
    }
} else {
    echo "Paramètres manquants.";
}
?>

