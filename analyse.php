<?php include 'upload_handler.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <title>Scanalytix - Analyse</title>
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
            echo "<div class='img-thumb' onclick=\"showImage('uploads/$basename')\">
                    <img src='uploads/$basename' alt='' />
                  </div>";
          }
        ?>
      </div>
    </aside>
    
    <main class="image-area" id="display-area">
      <div id="zoom-container" style="overflow: hidden; position: relative; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center;">
        <p style="color: #000;">Sélectionnez une image à gauche</p>
      </div>
    </main>

    <aside class="sidebar-right">

      <button onclick="analyserImage()">Extraire infos</button>
      <canvas id="analyzer-canvas" style="display: none;"></canvas>
      <p id="image-info"></p>

      <label for="zoom-range">Zoom</label>
      <input type="range" id="zoom-range" min="0" max="1" step="0.00001" value="0">

    </aside>
  </div>

  <script src="/ESANTE2/scripts/display.js"></script>
  <script src="/ESANTE2/scripts/imageInfo.js"></script>
  <script src="/ESANTE2/scripts/zoom2.js"></script>

</body>
</html>
