<?php include 'upload_handler.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Traitement</title>
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
      <button>Filtrer</button>
      <button>Améliorer</button>
      <button>Comparer</button>
      <button>Sauvegarder</button>
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
