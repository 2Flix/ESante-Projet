<?php
$image = isset($_GET['image']) ? $_GET['image'] : null;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <title>Scanalytix – Traitement</title>
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

    <main id="display-area" class="image-area">
      <?php if ($image): ?>
        <div id="zoom-container" style="overflow: hidden; position: relative; width: 100%; height: 100%;">
          <img id="selected-image" src="uploads/<?php echo htmlspecialchars($image); ?>" alt="Image sélectionnée" style="transform-origin: top left; cursor: grab;">
        </div>
      <?php else: ?>
        <p>Sélectionnez une image à gauche</p>
      <?php endif; ?>
    </main>

    <aside class="sidebar-right">

      <form id="laplacien-form" action="laplacien.php" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="image" id="laplacien-image" value="">
        <label for="strength" style="text-decoration: underline;">Force du filtre Laplacien :</label>
        <input type="number" step="0.1" min="0" name="strength" id="strength" value="1.0" required style="width: 40px;">
        <h6 style="margin-top: 15px; font-style: italic; color: blue; margin-bottom: 0px; margin-top: 0px"> Le filtre laplacien est un outil qui détecte les contours d'une image en repérant les zones où la luminosité change brusquement. 
          Augmenter sa force fait ressortir davantage de détails fins mais peut aussi amplifier le bruit indésirable. </h6>
        <button type="submit">Appliquer Laplacien</button>
      </form>

      <form id="gaussian-form" action="gaussian_blur.php" method="POST" style="margin-top: 20px;">
        <input type="hidden" name="image" id="gaussian-image" value="">
        <label for="sigma" style="text-decoration: underline;">Degré de flou :</label>
        <input type="number" step="0.1" min="0.1" name="sigma" id="sigma" value="1.0" required style="width: 40px;">
        <h6 style="margin-top: 15px; font-style: italic; color: blue; margin-bottom: 0px; margin-top: 0px"> Le flou gaussien adoucit une image en mélangeant chaque pixel avec ses voisins de manière progressive, créant un effet de flou naturel. 
          Augmenter le degré de flou rend l'image de plus en plus floue, masquant les détails fins et les imperfections. </h6>
        <button type="submit">Appliquer le Flou Gaussien</button>
      </form>

      <form id="median-form" action="javascript:void(0);" onsubmit="applyMedian(event)" style="margin-top: 20px;">
        <input type="hidden" name="image" id="median-image" value="" >
        <label for="median-kernel" style="text-decoration: underline;">Taille du noyau :</label>
        <input type="number" id="median-kernel" name="kernel" min="3" step="2" value="5" style="width: 40px;" required>
        <h6 style="margin-top: 15px; font-style: italic; color: blue; margin-bottom: 0px; margin-top: 0px"> Le filtre médian remplace chaque pixel par la valeur médiane de ses pixels voisins, ce qui permet d'éliminer efficacement le bruit tout en préservant les contours nets. 
          Augmenter la taille du noyau élargit la zone de voisinage analysée, ce qui supprime davantage de bruit mais peut aussi lisser ou déformer les petits détails de l'image. </h6>
        <button type="submit">Appliquer Filtre Médian</button>
      </form>

      <h4 style="margin-bottom: 5px;">Rotation</h4>
      <button type="button" onclick="rotateImage(-90)">↺ Anti-horaire 90°</button>
      <button type="button" onclick="rotateImage(90)">↻ Horaire 90°</button>

      <label style="margin-top: 20px" for="zoom-range">Zoom</label>
      <input type="range" id="zoom-range" min="0" max="1" step="0.001" value="0">


    </aside>
  </div>

  <script src="/ESANTE2/scripts/display.js"></script>
  <script src="/ESANTE2/scripts/zoom2.js"></script>
  <script src="/ESANTE2/scripts/filters.js"></script>
  <script src="/ESANTE2/scripts/rotation.js"></script>

<script>
    // Pour que median.js sache quelle image est sélectionnée
    window.selectedImageName = "<?php echo $image ?? ''; ?>";
  </script>
  <script src="/ESANTE2/scripts/median.js"></script>

  <script>
  // On va initialiser le zoom si l'image est deja presente
  document.addEventListener('DOMContentLoaded', function() {
    const selectedImage = document.getElementById('selected-image');
    if (selectedImage && selectedImage.src) {
      selectedImage.onload = function() {
        if (typeof initZoom === 'function') {
          initZoom();
        }
        if (typeof resetRotationState === 'function') {
          resetRotationState();
        }
        if (typeof initRotation === 'function') {
          initRotation();
        }
        // Initialiser l'image sélectionnée (fonction maintenant dans display.js)
        if (typeof initSelectedImage === 'function') {
          initSelectedImage();
        }
      };
      
      // Si l'image est déjà chargée
      if (selectedImage.complete) {
        if (typeof initZoom === 'function') {
          initZoom();
        }
        if (typeof resetRotationState === 'function') {
          resetRotationState();
        }
        if (typeof initRotation === 'function') {
          initRotation();
        }
        // Initialiser l'image sélectionnée (fonction maintenant dans display.js)
        if (typeof initSelectedImage === 'function') {
          initSelectedImage();
        }
      }
    }
  });
  </script>

</body>
</html>