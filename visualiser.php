<?php include 'upload_handler.php';?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Scanalytix - Visualiser Image</title>
  <link rel="stylesheet" href="/ESANTE2/styles.css" />
  <link rel="icon" href="/ESANTE2/favicon.png" type="image/png" />
</head>

<body>
  <?php include 'header.php'; ?>
  <div class="container">
    <aside class="sidebar-left">
      <h3>Mes Images</h3>
      <div class="image-list">
        <?php
          $images = glob("uploads/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
          foreach ($images as $img) {
            $basename = basename($img);
            echo "<div class='img-thumb' onclick=\"selectImage('uploads/$basename', '$basename')\">
                    <img src='uploads/$basename' alt='' />
                    <div class='image-indicator' id='indicator-$basename'></div>
                  </div>";
          }
        ?>
      </div>
    </aside>

    <main class="image-area" id="display-area">
      <p>Sélectionnez une image à gauche</p>
    </main>

    <aside class="sidebar-right">
      <div id="formContainer">
        <p id="formInitialMessage" style="color: red; margin-bottom: 1rem;">Sélectionnez une image et remplissez le formulaire.</p>
        <form id="formulaire" method="post" action="traitement_form.php" enctype="multipart/form-data">
            
            <input type="hidden" name="selectedImage" id="selectedImageInput" value="" />
            
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" id="prenom" placeholder="Prenom" required pattern="^[A-Za-z ']+$" maxlength="40"/>
            </div>

            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" placeholder="Nom" required pattern="^[A-Za-z ']+$" maxlength="40"/>
            </div>

            <div class="form-group">
                <label for="age">Age :</label>
                <input type="number" name="age" id="age" placeholder="Age" required min="0" max="120"/>
            </div>

            <div class="form-group">
                <label>Sexe :</label>
                <div class="radio-group">
                    <div class="radio-option">
                        <input type="radio" id="homme" name="sexe" value="Homme" required />
                        <label for="homme">Homme</label>
                    </div>
                    <div class="radio-option">
                        <input type="radio" id="femme" name="sexe" value="Femme" required />
                        <label for="femme">Femme</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="taille">Taille (cm) :</label>
                <input type="number" name="taille" id="taille" placeholder="Taille en cm" min="30" max="300" required />
            </div>
            
            <input class="sendButton" type="submit" name="InscriptionEnvoyer" id="btn_send" value="Sign Up"/>

        </form>
          
        <div id="outputContainer" class="output-display" style="display: none;">
        </div>
      </div>
    </aside>
  </div>

  <script src="/ESANTE2/scripts/display.js"></script>
  <script src="/ESANTE2/scripts/visualiser.js"></script>

</body>
</html>