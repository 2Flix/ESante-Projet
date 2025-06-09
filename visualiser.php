<?php include 'upload_handler.php';?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Visualiser Image</title>
  <link rel="stylesheet" href="/ESANTE/styles.css" />
</head>
<body>
  <?php include 'header.php'; ?>
  <div class="container">
    <aside class="sidebar-left">
      <h3>Mes Images</h3>
      <div class="image-list">
        <?php
          $images = glob("../uploads/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
          foreach ($images as $img) {
            $basename = basename($img);
            echo "<div class='img-thumb' onclick=\"showImage('../uploads/$basename')\">
                    <img src='../uploads/$basename' alt='' />
                  </div>";
          }
        ?>
      </div>
    </aside>
    <main class="image-area" id="display-area">
      <p>Sélectionnez une image à gauche</p>
    </main>
    <aside class="sidebar-right">
      <!-- Aucun bouton ici -->
      <div id="formContainer">
        <p id="formInitialMessage" style="color: red; margin-bottom: 1rem;">Formulaire non soumis.</p>
          <form id="formulaire" method="post" action="traitement_form.php" enctype="multipart/form-data">
                
                    
                    <label for="prenom">Prénom :</label>
                    <input type="text" name="prenom" id="prenom" placeholder="Prenom" required pattern="^[A-Za-z ']+$" maxlength="40"/>

                    <label for="nom">Nom :</label>
                    <input type="text" name="nom" id="nom" placeholder="Nom" required pattern="^[A-Za-z ']+$" maxlength="40"/>

                    <label for="age">Age :</label>
                    <input type="number" name="age" id="age" placeholder="Age" required min ="0" max ="120"/>

                    <div class="radio-option">
                    <label>Sexe :</label><br /><input type="radio" id="homme" name="sexe" value="Homme" required />
                    <label for="homme">Homme</label><br />
                    </div>

                    <div class="radio-option">
                    <input type="radio" id="femme" name="sexe" value="Femme" required />
                    <label for="femme">Femme</label>
                    </div>

                    <label for="taille">Taille (cm) :</label>
                    <input type="number" name="taille" id="taille" placeholder="Taille en cm" min="30" max="300" required />
                    
                    <input class="sendButton" type="submit" name="InscriptionEnvoyer" id="btn_send" value="Sign Up"/>

          </form>
          <div id="outputContainer" class="output-display" style="display: none;">
            </div>
        </div>
    </aside>
  </div>
  <script>
    function showImage(path) {
      const area = document.getElementById('display-area');
      area.innerHTML = `<img src="${path}" alt="Image sélectionnée" style="max-width: 90%; max-height: 90%;">`;
    }

        document.getElementById('formulaire').addEventListener('submit', function(event) {
            event.preventDefault(); // Va empêcher la soumission par défaut du formulaire

            // 1. On récupère les données du formulaire
            const prenom = document.getElementById('prenom').value;
            const nom = document.getElementById('nom').value;
            const age = document.getElementById('age').value;
            const taille = document.getElementById('taille').value;
            const sexe = document.querySelector('input[name="sexe"]:checked') ? document.querySelector('input[name="sexe"]:checked').value : ''; // Vide si non sélectionné, ou 'Non spécifié'

            // On crée un objet FormData pour envoyer les données comme un formulaire HTML de base
            const formData = new FormData();
            formData.append('prenom', prenom);
            formData.append('nom', nom);
            formData.append('age', age);
            formData.append('sexe', sexe);
            formData.append('taille', taille);
            formData.append('InscriptionEnvoyer', 'Sign Up');

            // 2. On envoie les données au fichier traitement_form.php
            fetch('traitement_form.php', {
                method: 'POST',
                body: formData // on utilise FormData pour envoyer les données comme un formulaire
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erreur réseau ou réponse serveur non OK');
                }
                return response.text(); // on récupère la réponse du serveur sous forme de texte (le HTML)
            })
            .then(data => {
                // 'data' contient maintenant le HTML généré par traitement_form.php
                
                // 3. Va remplacer le formulaire par le nouveau contenu
                document.getElementById('formInitialMessage').style.display = 'none'; // On cache le message initial
                document.getElementById('formulaire').style.display = 'none'; // Va cacher le formulaire pour le remplacement

                const outputContainer = document.getElementById('outputContainer');
                outputContainer.innerHTML = data; // On insère le HTML reçu du serveur
                outputContainer.style.display = 'block'; // on affiche le conteneur des informations

                // On ajoute un bouton pour recharger la page si l'utilisateur veut revenir au formulaire
                const resetButton = document.createElement('button');
                resetButton.textContent = 'Réinitialiser le formulaire';
                resetButton.onclick = function() {
                    location.reload();
                };
                outputContainer.appendChild(resetButton); // on ajoute le bouton après les informations
            })
            .catch(error => {
                console.error('Erreur lors de l\'envoi des données ou de la récupération de la réponse:', error);
                alert('Une erreur est survenue lors de l\'envoi des données ou de la récupération de la réponse. Veuillez réessayer.');
            });
          });
  </script>

</body>
</html>
