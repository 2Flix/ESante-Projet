<?php /*
// Vérifie que le formulaire a bien été soumis via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = htmlspecialchars($_POST["prenom"]);
    $nom = htmlspecialchars($_POST["nom"]);
    $age = htmlspecialchars($_POST["age"]);
    $sexe = htmlspecialchars($_POST["sexe"]);
    $taille = htmlspecialchars($_POST["taille"]);


        // Les champs sont remplis ?
    if (empty($prenom) || empty($nom) || empty($age) || empty($sexe) || empty($taille)) {
        echo "Tous les champs sont obligatoires.";
        exit;
    }
    ?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Vos informations ci-dessous</title>
    </head>
    <body>
        <h1>Vos Informations :</h1>
        <p><strong>Prenom :</strong> <?= $prenom ?></p>
        <p><strong>Nom :</strong> <?= $nom ?></p>
        <p><strong>Age :</strong> <?= $age ?> ans</p>
        <p><strong>Sexe :</strong> <?= $sexe ?></p>
        <p><strong>Taille :</strong> <?= $taille ?> cm</p>
    </body>
    </html>

    <?php
} else {
    echo "Formulaire non soumis.";
} */

// On veut s'assurer que le navigateur interprète la réponse comme du HTML
header('Content-Type: text/html; charset=utf-8');

// Va vérifier que le formulaire a bien été soumis via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prenom = htmlspecialchars($_POST["prenom"] ?? '');
    $nom = htmlspecialchars($_POST["nom"] ?? '');
    $age = htmlspecialchars($_POST["age"] ?? '');
    $sexe = htmlspecialchars($_POST["sexe"] ?? '');
    $taille = htmlspecialchars($_POST["taille"] ?? '');

    // Les champs sont remplis ?
    if (empty($prenom) || empty($nom) || empty($age) || empty($sexe) || empty($taille)) {
        echo '<div style="color: red; font-weight: bold; padding: 10px; border: 1px solid red; background-color: #ffe6e6; border-radius: 5px;">';
        echo 'Erreur : Tous les champs sont obligatoires.';
        echo '</div>';
        exit;
    }

    // Si toutes les validations sont passées, on peut afficher le HTML des informations soumises
    ?>
    <h1>Vos Informations :</h1>
    <p><strong>Prenom :</strong> <?= $prenom ?></p>
    <p><strong>Nom :</strong> <?= $nom ?></p>
    <p><strong>Age :</strong> <?= $age ?> ans</p>
    <p><strong>Sexe :</strong> <?= $sexe ?></p>
    <p><strong>Taille :</strong> <?= $taille ?> cm</p>
    <?php
} else {
    echo '<div style="color: orange; font-weight: bold; padding: 10px; border: 1px solid orange; background-color: #fff3e0; border-radius: 5px;">';
    echo 'Accès non autorisé : Ce fichier doit être appelé via une soumission de formulaire POST.';
    echo '</div>';
}

?>