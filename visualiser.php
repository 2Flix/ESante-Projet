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

  <script>
    let currentSelectedImage = '';

    function selectImage(path, basename) {
      currentSelectedImage = basename;
      
      const area = document.getElementById('display-area');
      area.innerHTML = `<img src="${path}" alt="Image sélectionnée" style="max-width: 90%; max-height: 90%;">`;
      
      document.getElementById('selectedImageInput').value = basename;
      
      loadImageData(basename);
      
      showForm();
    }

    function loadImageData(imageName) {
      fetch(`get_image_data.php?image=${encodeURIComponent(imageName)}`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showFormattedData(data.data, imageName);
            
            const indicator = document.getElementById(`indicator-${imageName}`);
            if (indicator) {
              indicator.style.display = 'block';
            }
          } else {
            showForm();
            document.getElementById('formulaire').reset();
            document.getElementById('selectedImageInput').value = imageName;
            document.getElementById('formInitialMessage').textContent = 'Aucune donnée pour cette image. Remplissez le formulaire.';
            document.getElementById('formInitialMessage').style.color = 'orange';
          }
        })
        .catch(error => {
          console.error('Erreur lors du chargement des données:', error);
          showForm();
        });
    }

    function showFormattedData(formData, imageName) {
      document.getElementById('formInitialMessage').style.display = 'none';
      document.getElementById('formulaire').style.display = 'none';

      const outputContainer = document.getElementById('outputContainer');
      outputContainer.innerHTML = `
          <h3>Informations sauvegardées :</h3>
          <p><strong>Image :</strong> ${imageName}</p>
          <p><strong>Prénom :</strong> ${formData.prenom}</p>
          <p><strong>Nom :</strong> ${formData.nom}</p>
          <p><strong>Age :</strong> ${formData.age} ans</p>
          <p><strong>Sexe :</strong> ${formData.sexe}</p>
          <p><strong>Taille :</strong> ${formData.taille} cm</p>
          <p><small>Sauvegardé le : ${formData.timestamp}</small></p>
      `;
      outputContainer.style.display = 'block';

      const editButton = document.createElement('button');
      editButton.textContent = 'Modifier les informations';
      editButton.onclick = function() {
          document.getElementById('prenom').value = formData.prenom;
          document.getElementById('nom').value = formData.nom;
          document.getElementById('age').value = formData.age;
          document.getElementById('taille').value = formData.taille;
          
          if (formData.sexe === 'Homme') {
            document.getElementById('homme').checked = true;
          } else if (formData.sexe === 'Femme') {
            document.getElementById('femme').checked = true;
          }
          
          document.getElementById('selectedImageInput').value = imageName;
          
          showForm();
          document.getElementById('formInitialMessage').textContent = 'Modification des données existantes.';
          document.getElementById('formInitialMessage').style.color = 'blue';
      };
      outputContainer.appendChild(editButton);
    }

    function showForm() {
      document.getElementById('formInitialMessage').style.display = 'block';
      document.getElementById('formulaire').style.display = 'block';
      document.getElementById('outputContainer').style.display = 'none';
    }

    document.getElementById('formulaire').addEventListener('submit', function(event) {
        event.preventDefault();

        if (!currentSelectedImage) {
          alert('Veuillez d\'abord sélectionner une image.');
          return;
        }

        const prenom = document.getElementById('prenom').value;
        const nom = document.getElementById('nom').value;
        const age = document.getElementById('age').value;
        const taille = document.getElementById('taille').value;
        const sexe = document.querySelector('input[name="sexe"]:checked') ? document.querySelector('input[name="sexe"]:checked').value : '';

        const formData = new FormData();
        formData.append('prenom', prenom);
        formData.append('nom', nom);
        formData.append('age', age);
        formData.append('sexe', sexe);
        formData.append('taille', taille);
        formData.append('selectedImage', currentSelectedImage);
        formData.append('InscriptionEnvoyer', 'Sign Up');

        fetch('traitement_form.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                
                document.getElementById('formInitialMessage').style.display = 'none';
                document.getElementById('formulaire').style.display = 'none';

                const outputContainer = document.getElementById('outputContainer');
                outputContainer.innerHTML = `
                    <h3>Informations sauvegardées :</h3>
                    <p><strong>Image :</strong> ${currentSelectedImage}</p>
                    <p><strong>Prénom :</strong> ${data.data.prenom}</p>
                    <p><strong>Nom :</strong> ${data.data.nom}</p>
                    <p><strong>Age :</strong> ${data.data.age} ans</p>
                    <p><strong>Sexe :</strong> ${data.data.sexe}</p>
                    <p><strong>Taille :</strong> ${data.data.taille} cm</p>
                    <p><small>Sauvegardé le : ${data.data.timestamp}</small></p>
                `;
                outputContainer.style.display = 'block';

                const indicator = document.getElementById(`indicator-${currentSelectedImage}`);
                if (indicator) {
                  indicator.style.display = 'block';
                }

                const resetButton = document.createElement('button');
                resetButton.textContent = 'Continuer à modifier';
                resetButton.onclick = function() {
                    showForm();
                };
                outputContainer.appendChild(resetButton);
                
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'envoi des données.');
        });
    });
  </script>

</body>
</html>