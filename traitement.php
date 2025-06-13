<?php include 'upload_handler.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Scanalytix - Traitement</title>
  <link rel="stylesheet" href="/ESANTE/styles.css" />
  <link rel="icon" href="/ESANTE/favicon.png" type="image/png" />
</head>
<body>
  <?php include 'header.php'; ?>
  <div class="container">
    <aside class="sidebar-left">
      <h3>Mes Images</h3>
      <div class="image-list" id="image-list-container">
        <?php
          $images = glob("../uploads/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
          foreach ($images as $img) {
            $basename = basename($img);
            // On s'assure d'envoyer le chemin web correct à showImage
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
      <button>Filtrer</button>
      <button>Améliorer</button>
      <button onclick="rotateSelectedImage(-90)">Rotation Droite (90° Horaire)</button>
      <button onclick="rotateSelectedImage(90)">Rotation Gauche (90° Anti-horaire)</button>
      <button onclick="rotateSelectedImage(180)">Rotation À l'Envers (180°)</button>
      <button onclick="resetImageOrientation()">Remettre à Zéro</button> <button>Sauvegarder</button>
    </aside>
  </div>
  <script>
let currentImagePath = null;
let originalSelectedImagePath = null;

// Fonction pour afficher une image dans la zone principale
function showImage(path) {
  const updatedPath = path + '?t=' + new Date().getTime();
  currentImagePath = path; // sans timestamp
  if (!originalSelectedImagePath) {
    originalSelectedImagePath = path;
  }
  const area = document.getElementById('display-area');
  area.innerHTML = `<img src="${updatedPath}" alt="Image sélectionnée" style="max-width: 90%; max-height: 90%;">`;
}


// Fonction pour recharger la liste des miniatures (sidebar-left)
function reloadImageList() {
    fetch('get_image_list.php')
        .then(response => response.text())
        .then(html => {
            document.getElementById('image-list-container').innerHTML = html;
        })
        .catch(error => {
            console.error('Erreur lors du rechargement de la liste d\'images :', error);
        });
}

// Rotation de l'image avec un angle spécifique
function rotateSelectedImage(angle) {
  if (!currentImagePath) {
    alert("Veuillez d'abord sélectionner une image.");
    return;
  }

  console.log(`Tentative de rotation de l'image : ${currentImagePath} avec un angle de ${angle} degrés.`);

  fetch('/ESANTE/rotate.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    // On envoie l'angle en plus du chemin de l'image
    body: `imagePath=${encodeURIComponent(currentImagePath)}&angle=${encodeURIComponent(angle)}`
  })
  .then(response => response.json())
  .then(data => {
    if (data.status === 'success') {
      console.log('Rotation réussie ! Nouvelle image créée à :', data.newImagePath);
      currentImagePath = data.newImagePath;
      showImage(data.newImagePath);  
      // Recharger la liste des miniatures pour inclure la nouvelle image
      reloadImageList();
    } else {
      console.error('Erreur de rotation :', data.message);
      alert('Erreur : ' + data.message);
    }
  })
  .catch(error => {
    console.error('Erreur réseau ou script :', error);
    alert('Une erreur inattendue est survenue lors de la communication avec le serveur.');
  });
}

// fonction pour remettre l'image à son orientation originale
function resetImageOrientation() {
    if (!originalSelectedImagePath) {
        alert("Aucune image originale sélectionnée pour réinitialiser.");
        return;
    }
    // Afficher l'image originale
    showImage(originalSelectedImagePath);

}


document.addEventListener('DOMContentLoaded', () => {
    // Si aucune image n'est affichée au chargement, réinitialise
    if (!document.querySelector('#display-area img')) {
        currentImagePath = null;
        originalSelectedImagePath = null;
    }
});
  </script>
</body>
</html>