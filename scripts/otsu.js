function applyOtsu() {
  if (!window.currentImagePath) {
    alert("Veuillez d'abord sélectionner une image.");
    return;
  }

  fetch('otsu_segmentation.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ imagePath: window.currentImagePath })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Afficher le seuil calculé
      const thresholdDisplay = document.getElementById('threshold-display');
      if (thresholdDisplay) {
        thresholdDisplay.textContent = `Seuil utilisé pour la segmentation Otsu : ${data.threshold}`;
      }
      
      // Forcer le rechargement en ajoutant un timestamp
      const imagePathWithTimestamp = data.segmentedImagePath + '?t=' + new Date().getTime();
      
      // Mettre à jour l'image dans la barre de gauche
      updateImageInSidebar(data.segmentedImagePath, imagePathWithTimestamp);
      
      // Afficher l'image avec le timestamp pour éviter le cache
      showImage(imagePathWithTimestamp);
    } else {
      alert("Erreur lors de la segmentation : " + data.error);
    }
  });
}

function applyManualThreshold() {
  if (!window.currentImagePath) {
    alert("Veuillez d'abord sélectionner une image.");
    return;
  }

  const thresholdInput = document.getElementById('manual-threshold');
  const threshold = parseInt(thresholdInput.value);

  if (isNaN(threshold) || threshold < 0 || threshold > 255) {
    alert("Veuillez entrer un seuil valide entre 0 et 255.");
    return;
  }

  fetch('otsu_segmentation.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ 
      imagePath: window.currentImagePath,
      manualThreshold: threshold
    })
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Afficher le seuil utilisé
      const thresholdDisplay = document.getElementById('threshold-display');
      if (thresholdDisplay) {
        thresholdDisplay.textContent = `Seuil utilisé pour la segmentation Otsu : ${data.threshold}`;
      }
      
      // Forcer le rechargement en ajoutant un timestamp
      const imagePathWithTimestamp = data.segmentedImagePath + '?t=' + new Date().getTime();
      
      // Mettre à jour l'image dans la barre de gauche
      updateImageInSidebar(data.segmentedImagePath, imagePathWithTimestamp);
      
      // Afficher l'image avec le timestamp pour éviter le cache
      showImage(imagePathWithTimestamp);
    } else {
      alert("Erreur lors de la segmentation : " + data.error);
    }
  });
}

function updateImageInSidebar(originalPath, timestampedPath) {
  const imageList = document.querySelector('.image-list');
  const basename = originalPath.split('/').pop();
  
  // Chercher l'image existante dans la barre de gauche
  const existingImages = imageList.querySelectorAll('.img-thumb');
  let imageFound = false;
  
  existingImages.forEach(thumb => {
    const img = thumb.querySelector('img');
    if (img && img.src.includes(basename.split('?')[0])) {
      // Mettre à jour l'image existante avec le nouveau timestamp
      img.src = timestampedPath;
      thumb.onclick = function() { showImage(timestampedPath); };
      imageFound = true;
    }
  });
  
  // Si l'image n'existe pas encore, l'ajouter
  if (!imageFound) {
    const newThumb = document.createElement('div');
    newThumb.className = 'img-thumb';
    newThumb.onclick = function() { showImage(timestampedPath); };
    
    const newImg = document.createElement('img');
    newImg.src = timestampedPath;
    newImg.alt = '';
    
    newThumb.appendChild(newImg);
    imageList.appendChild(newThumb);
  }
}