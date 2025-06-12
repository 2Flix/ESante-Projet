<?php
// analyse.php
$image = isset($_GET['image']) ? $_GET['image'] : null;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Scanalytix – Analyse</title>
  <link rel="stylesheet" href="/ESANTE/styles.css">
  <link rel="icon" href="/ESANTE/favicon.png" type="image/png" />
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="container">
    <aside class="sidebar-left">
      <h3>Mes Images</h3>
      <div class="image-list">
        <?php
          $images = glob(__DIR__ . "/uploads/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
          foreach ($images as $img) {
              $basename = basename($img);
              echo "<div class='img-thumb' onclick=\"location.href='analyse.php?image=" . urlencode($basename) . "'\">
                      <img src='uploads/$basename' alt='miniature' />
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
      <form action="convolution.php" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($image ?? ''); ?>">
        <label for="strength">Force de filtrage :</label>
        
        <input type="number" step="0.1" min="0.1" name="strength" id="strength" value="1.0" required style="width: 60px;">
        <button type="submit">Filtrage par convolution</button>
      </form>

      <form action="gaussian_blur.php" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($image ?? ''); ?>">
        <label for="sigma">Flou gaussien (sigma):</label>
        
        <input type="number" step="0.1" min="0.1" name="sigma" id="sigma" value="1.0" required style="width: 60px;">
        <button type="submit">Flou Gaussien</button>
      </form>

      <form action="combined.php" method="POST" style="margin-top: 10px;">
        <input type="hidden" name="image" value="<?php echo htmlspecialchars($image ?? ''); ?>">
        <label for="sigma">Flou gaussien (sigma):</label>
        
        <input type="number" step="0.1" min="0.1" name="sigma" id="sigma" value="1.0" required style="width: 60px;">
        <label for="strength">Force de filtrage :</label> 
    
        <input type="number" step="0.1" min="0.1" name="strength" id="strength" value="1.0" required style="width: 60px;">
        <button type="submit">Filtrage combiné</button>
      </form>

      <button onclick="analyserImage()">Extraire infos</button>
      <canvas id="analyzer-canvas" style="display: none;"></canvas> <!-- canvas pour lire les pixels -->
      <p id="image-info"></p>
    </aside>
  </div>

  <script>
    function améliorerImage() {
      const urlParams = new URLSearchParams(window.location.search);
      const image = urlParams.get('image');
      if (!image) {
        alert("Veuillez sélectionner une image.");
        return;
      }
      window.location.href = `analyse.php?image=${encodeURIComponent(image)}&sharpen=1`;
    }

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
