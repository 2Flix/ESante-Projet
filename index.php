<?php require_once 'upload_handler.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Visionneuse d'images</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

  <?php include 'header.php'; ?>

  <div class="container">
    <!-- Liste des images -->
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

    <!-- Image centrale -->
    <main class="image-area" id="display-area">
      <p>Sélectionnez une image à gauche</p>
    </main>

    <!-- Actions -->
    <aside class="sidebar-right">
      <button>Action 1</button>
      <button>Action 2</button>
      <button>Action 3</button>
      <button>Action 4</button>
    </aside>
  </div>

  <script>
    function showImage(path) {
  const area = document.getElementById('display-area');
  area.innerHTML = `<img src="${path}" alt="Image sélectionnée" style="max-width: 90%; max-height: 90%;">`;
  }
  </script>
</body>
</html>
