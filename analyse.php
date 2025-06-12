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
      <div id="image-container">
        <p>Sélectionnez une image à gauche</p>
      </div>
      <canvas id="zoom-canvas" width="200" height="200" style="border: 1px solid #ccc; display: none; margin-top: 10px;"></canvas>
      <div id="zoom-controls" style="display: none; margin-top: 10px;">
        <button onclick="zoomIn()">+</button>
        <button onclick="zoomOut()">–</button>
      </div>
    </main>
    <aside class="sidebar-right">
      <button>Analyser</button>
      <button onclick="analyserImage()">Extraire infos</button>
      <button>Mesurer</button>
      <button>Exporter</button>
      <canvas id="analyzer-canvas" style="display: none;"></canvas>
      <p id="image-info"></p>
    </aside>
  </div>

  <script>
    let zoomCenterX = 0;
    let zoomCenterY = 0;
    let zoomSize = 50;
    let zoomFactor = 4;

    function showImage(path) {
      const area = document.getElementById('display-area');
      currentImagePath = path;

      const img = new Image();
      img.id = "selected-image";
      img.src = path;
      img.alt = "Image sélectionnée";
      img.style.maxWidth = "90%";
      img.style.maxHeight = "90%";

      img.onload = () => {
        let imgContainer = document.getElementById('image-container');
        imgContainer.innerHTML = '';  // vide juste l'image précédente
        imgContainer.appendChild(img);

        // Afficher les boutons et canvas pour le zoom
        document.getElementById('zoom-canvas').style.display = 'none'; // caché tant qu'on clique pas
        document.getElementById('zoom-controls').style.display = 'none';
        document.getElementById('image-info').textContent = ''; // reset infos
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
        const gray = imageData[i];
        if (gray < min) min = gray;
        if (gray > max) max = gray;
      }

      info.innerHTML = `
        <strong>Dimensions :</strong> ${img.naturalWidth} x ${img.naturalHeight} px<br>
        <strong>Niveau de gris :</strong> min = ${min}, max = ${max}
      `;
    }

    // Gestion du clic sur l'image pour afficher zoom + boutons
    document.addEventListener('click', function (e) {
      const img = document.getElementById('selected-image');
      const canvas = document.getElementById('zoom-canvas');
      const controls = document.getElementById('zoom-controls');

      if (!img || e.target.id !== 'selected-image') return;

      const rect = img.getBoundingClientRect();
      const x = e.clientX - rect.left;
      const y = e.clientY - rect.top;

      const scaleX = img.naturalWidth / img.width;
      const scaleY = img.naturalHeight / img.height;
      zoomCenterX = x * scaleX;
      zoomCenterY = y * scaleY;

      zoomSize = 50;
      zoomFactor = 4;

      drawZoom(img, canvas);
      canvas.style.display = 'block';
      controls.style.display = 'block';
    });

    function drawZoom(img, canvas) {
      const zoomCanvasSize = zoomSize * zoomFactor;
      canvas.width = zoomCanvasSize;
      canvas.height = zoomCanvasSize;

      const ctx = canvas.getContext('2d');
      const hiddenCanvas = document.createElement('canvas');
      hiddenCanvas.width = img.naturalWidth;
      hiddenCanvas.height = img.naturalHeight;
      const hiddenCtx = hiddenCanvas.getContext('2d');
      hiddenCtx.drawImage(img, 0, 0);

      ctx.imageSmoothingEnabled = false;
      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(
        hiddenCanvas,
        zoomCenterX - zoomSize / 2, zoomCenterY - zoomSize / 2,
        zoomSize, zoomSize,
        0, 0,
        zoomCanvasSize, zoomCanvasSize
      );
    }

    function zoomIn() {
      zoomFactor = Math.min(zoomFactor + 1, 10);
      const img = document.getElementById('selected-image');
      const canvas = document.getElementById('zoom-canvas');
      drawZoom(img, canvas);
    }

    function zoomOut() {
      zoomFactor = Math.max(zoomFactor - 1, 1);
      const img = document.getElementById('selected-image');
      const canvas = document.getElementById('zoom-canvas');
      drawZoom(img, canvas);
    }
  </script>
</body>
</html>
