<?php
$image = isset($_GET['image']) ? $_GET['image'] : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Scanalytix – Analyse</title>
  <link rel="stylesheet" href="/ESANTE2/styles.css">
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

    <main class="image-area">
      <?php if ($image): ?>
        <img id="selected-image" src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Image sélectionnée" style="max-width: 90%; max-height: 90%;">
      <?php else: ?>
        <p>Sélectionnez une image à gauche</p>
      <?php endif; ?>
    </main>

    <!-- Boutons d'action -->
    <aside class="sidebar-right">
      <!-- CORRECTION : Utiliser JavaScript pour obtenir l'image sélectionnée -->
      <form id="convolution-form" action="convolution.php" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="image" id="convolution-image" value="">
        <label for="strength">Force de filtrage :</label>
        <input type="number" step="0.1" min="0.1" name="strength" id="strength" value="1.0" required style="width: 60px;">
        <button type="submit">Filtrage par convolution</button>
      </form>

      <form id="gaussian-form" action="gaussian_blur.php" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="image" id="gaussian-image" value="">
        <label for="sigma">Flou gaussien (sigma):</label>
        <input type="number" step="0.1" min="0.1" name="sigma" id="sigma" value="1.0" required style="width: 60px;">
        <button type="submit">Flou Gaussien</button>
      </form>

      <form id="combined-form" action="combined.php" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="image" id="combined-image" value="">
        <label for="sigma2">Flou gaussien (sigma):</label>
        <input type="number" step="0.1" min="0.1" name="sigma" id="sigma2" value="1.0" required style="width: 60px;">
        <label for="strength2">Force de filtrage :</label> 
        <input type="number" step="0.1" min="0.1" name="strength" id="strength2" value="1.0" required style="width: 60px;">
        <button type="submit">Filtrage combiné</button>
      </form>

      <button onclick="analyserImage()">Extraire infos</button>
      <canvas id="analyzer-canvas" style="display: none;"></canvas>
      <p id="image-info"></p>
    </aside>
  </div>

  <script>
    let currentImagePath = '';
    let currentImageName = '';

    function selectImage(path, basename) {
      currentImagePath = path;
      currentImageName = basename;
      
      // Afficher l'image dans la zone principale
      const area = document.querySelector('.image-area');
      const img = new Image();
      img.id = "selected-image";
      img.src = path;
      img.alt = "Image sélectionnée";
      img.style.maxWidth = "90%";
      img.style.maxHeight = "90%";

      img.onload = () => {
        area.innerHTML = '';
        area.appendChild(img);
      };

      // Mettre à jour les champs cachés des formulaires
      document.getElementById('convolution-image').value = basename;
      document.getElementById('gaussian-image').value = basename;
      document.getElementById('combined-image').value = basename;

      // Mettre à jour l'URL avec le paramètre image
      const url = new URL(window.location);
      url.searchParams.set('image', basename);
      window.history.pushState({}, '', url);
    }

    // Au chargement de la page, si une image est déjà sélectionnée dans l'URL
    window.addEventListener('DOMContentLoaded', function() {
      const urlParams = new URLSearchParams(window.location.search);
      const imageFromUrl = urlParams.get('image');
      if (imageFromUrl) {
        currentImageName = imageFromUrl;
        document.getElementById('convolution-image').value = imageFromUrl;
        document.getElementById('gaussian-image').value = imageFromUrl;
        document.getElementById('combined-image').value = imageFromUrl;
      }
    });

    // Vérifier qu'une image est sélectionnée avant soumission
    document.getElementById('gaussian-form').addEventListener('submit', function(e) {
      if (!currentImageName && !document.getElementById('gaussian-image').value) {
        e.preventDefault();
        alert('Veuillez sélectionner une image avant d\'appliquer le filtre.');
      }
    });

    document.getElementById('convolution-form').addEventListener('submit', function(e) {
      if (!currentImageName && !document.getElementById('convolution-image').value) {
        e.preventDefault();
        alert('Veuillez sélectionner une image avant d\'appliquer le filtre.');
      }
    });

    document.getElementById('combined-form').addEventListener('submit', function(e) {
      if (!currentImageName && !document.getElementById('combined-image').value) {
        e.preventDefault();
        alert('Veuillez sélectionner une image avant d\'appliquer le filtre.');
      }
    });

    function améliorerImage() {
      const urlParams = new URLSearchParams(window.location.search);
      const image = urlParams.get('image');
      if (!image) {
        alert("Veuillez sélectionner une image.");
        return;
      }
      window.location.href = `analyse.php?image=${encodeURIComponent(image)}&sharpen=1`;
    }

    function analyserImage() {
      const img = document.getElementById('selected-image');
      const info = document.getElementById('image-info');
      const canvas = document.getElementById('analyzer-canvas');

      if (!img || !img.complete || img.naturalWidth === 0) {
        info.textContent = "Aucune image sélectionnée ou chargement en cours.";
        return;
      }

      const context = canvas.getContext('2d');
      canvas.width = img.naturalWidth;
      canvas.height = img.naturalHeight;
      context.drawImage(img, 0, 0);

      const imageData = context.getImageData(0, 0, canvas.width, canvas.height).data;
      let min = 255, max = 0;

      for (let i = 0; i < imageData.length; i += 4) {
        const gray = imageData[i];
        if (gray < min) min = gray;
        if (gray > max) max = gray;
      }

      info.innerHTML = `
        <strong>Dimensions :</strong> ${img.naturalWidth} x ${img.naturalHeight} px<br>
        <strong>Niveau de gris :</strong> min = ${min}, max = ${max}
      `;
    }
  </script>
</body>
</html>