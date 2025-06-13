<?php
// get_image_list.php
// Ce script renvoie le HTML de la liste des miniatures d'images.
$images = glob("../uploads/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
foreach ($images as $img) {
  $basename = basename($img);
  echo "<div class='img-thumb' onclick=\"showImage('../uploads/$basename')\">
          <img src='../uploads/$basename' alt='' />
        </div>";
}
?>