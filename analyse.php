<?php include 'upload_handler.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Analyse</title>
  <link rel="stylesheet" href="ESANTE/styles.css" />
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
      <button>Analyser</button>
      <button onclick="analyserImage()">Extraire infos</button>
      <button>Mesurer</button>
      <button>Exporter</button>
      <canvas id="analyzer-canvas" style="display: none;"></canvas> <!-- canvas pour lire les pixels -->
      <p id="image-info"></p>
    </aside>
  </div>
  <script>
    function showImage(path) {
       const area = document.getElementById('display-area');
  currentImagePath = path;

  // Crée dynamiquement l'image avec onload
  const img = new Image();
  img.id = "selected-image";
  img.src = path;
  img.alt = "Image sélectionnée";
  img.style.maxWidth = "90%";
  img.style.maxHeight = "90%";

  // Une fois que l’image est complètement chargée, on l’ajoute à la zone
  img.onload = () => {
    area.innerHTML = '';
    area.appendChild(img);
  };
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
      // Niveaux de gris → on suppose que R = G = B
      const gray = imageData[i]; // R
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
