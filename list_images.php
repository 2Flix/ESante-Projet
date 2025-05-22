<?php
header('Content-Type: application/json');
$images = glob("images/*.png");
echo json_encode(array_map('basename', $images));
?>
