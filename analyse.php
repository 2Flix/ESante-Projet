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
          // Trier par date de modification (plus récent en premier)
          usort($images, function($a, $b) {
              return filemtime($b) - filemtime($a);
          });
          
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

      <p id="threshold-display" style="font-weight: bold; color: #333; margin-bottom: 10px; margin-top: 0px;">Seuil utilisé pour la segmentation Otsu : -</p>

      <label for="manual-threshold" style="text-decoration: underline; margin-bottom : 20px; margin-top : 10px">Seuil manuel Otsu :</label>
      <input type="number" id="manual-threshold" min="0" max="255" step="1" value="128" style="width: 40px;">
      <h5 style="margin-top: 15px; font-style: italic; color: blue; margin-bottom: 20px"> Plage : Entre 0 et 255 </h5>
      <button onclick="applyManualThreshold()"> Segmentation Otsu manuel</button>
      <button onclick="applyOtsu()">Segmentation Otsu automatique</button>

      <div id="chanvese-iteration-display" style="font-weight: bold; color: #333; margin-bottom: 10px; margin-top : 20px">
          Itération utilisée pour la segmentation Chan-Vese : -
      </div>
      <label for="chanvese-iterations" style="text-decoration: underline; margin-bottom: 10px; margin-top: 20px;">Itérations manuel Chan-Vese :</label>
      <input type="number" id="chanvese-iterations" min="10" max="200" step="10" value="50" style="width: 60px;">
      <h5 style="margin-top: 15px; font-style: italic; color: blue; margin-bottom: 5px">Plage recommandée : Entre 10 et 200</h5>
      <h5 style="margin-top: 15px; font-style: italic; color: blue; margin-bottom: 20px">Attention : Plus le nombre est élevé, plus l'analyse demandera de temps.</h5>
      <button onclick="applyChanVeseWithIterations()">Chan-Vese manuel</button>
      <button onclick="applyChanVeseAutomatic()">Chan-Vese automatique</button>


      <label for="brightness-input" style="text-decoration: underline; margin-bottom : 20px; margin-top : 20px"> Luminosité (%)</label>
      <input type="number" id="brightness-input" min="0" max="300" step="1" value="100" style="width: 40px;">
      <h5 style="margin-top: 15px; font-style: italic; color: blue; margin-bottom: 20px"> Plage recommandée : Entre 30 et 400 % </h5>
      <button onclick="applyBrightness()">Appliquer</button>
      

      <button onclick="inverserCouleurs()">Inverser couleurs</button>
      

      <button onclick="analyserImage()">Extraire infos</button>
      <canvas id="analyzer-canvas" style="display: none;"></canvas>
      <p id="image-info"></p>


      <button onclick="toggleDrawingMode()">Chercher la taille/surface</button>
      <p id="area-result"></p>
      
      <label style="text-decoration: underline; margin-bottom : 10px; margin-top : 10px">Dessiner sur l'image (formes) : </label>
      <div class="hollow-shape-controls">
        <div class="shape-buttons">
          <button class="shape-btn active" data-shape="circle" onclick="setShapeType('circle')">Cercle</button>
          <button class="shape-btn" data-shape="rectangle" onclick="setShapeType('rectangle')">Rectangle</button>
          <button class="shape-btn" data-shape="line" onclick="setShapeType('line')">Ligne</button>
        </div>
        
        <div class="color-stroke-controls">
          <label for="stroke-color" style="font-style: italic; font-size : 13px">Couleur :</label>
          <input type="color" id="stroke-color" value="#ff0000" onchange="setStrokeColor(this.value)">
          <br>
          <label for="stroke-width" style="font-style: italic; font-size : 13px">Épaisseur :</label>
          <input type="range" id="stroke-width" min="1" max="10" value="2" onchange="setStrokeWidth(this.value)">
          <span id="stroke-width-display">2px</span> </br>
        </div>
        
        <div class="hollow-shape-actions">
          <button id="toggle-hollow-shape-btn" onclick="toggleHollowShapeMode()">Activer les formes</button>
          <button id="clear-shapes-btn" onclick="clearAllShapes()">Effacer</button>
          <button id="save-shapes-server-btn" onclick="saveImageWithShapesToServer()">Sauvergarder l'image</button>
        </div>
        <h5 style="margin-top: 15px; font-style: italic; color: blue; margin-bottom: 20px">Assurez-vous d'activer les formes avant de dessiner en appuyant sur le bouton. 
          Si vous souhaitez faire un zoom sur l'image, assurez-vous de l'enregistrer avant!</h5>
      </div>

      <label for="zoom-range" style="margin-top : 10px;">Zoom</label>
      <input type="range" id="zoom-range" min="0" max="1" step="0.00001" value="0">

    </aside>
  </div>

  <script src="/ESANTE2/scripts/display.js"></script>
  <script src="/ESANTE2/scripts/imageInfo.js"></script>
  <script src="/ESANTE2/scripts/zoom2.js"></script>
  <script src="/ESANTE2/scripts/inverser.js"></script>
  <script src="/ESANTE2/scripts/brightness.js"></script>
  <script src="/ESANTE2/scripts/drawing.js"></script>
  <script src="/ESANTE2/scripts/otsu.js"></script>
  <script src="/ESANTE2/scripts/chanvese.js"></script>
  <script src="/ESANTE2/scripts/hollow-shapes.js"></script>

  <script>
    // Initialiser les contrôles de formes creuses
    document.getElementById('stroke-width').addEventListener('input', function() {
      document.getElementById('stroke-width-display').textContent = this.value + 'px';
      setStrokeWidth(this.value);
    });
  </script>

</body>

</html>