function showImage(path) {
  const area = document.getElementById('display-area');

  area.innerHTML = `
    <div id="zoom-container" style="position: relative; width: 100%; height: 100%; background: #f5f5f5;">
      <img id="selected-image" src="${path}" alt="Image sélectionnée" style="display: block; max-width: 100%; height: auto;">
      <canvas id="drawing-canvas" style="position: absolute; top: 0; left: 0; z-index: 10;"></canvas>
    </div>
  `;

  const img = document.getElementById('selected-image');
  img.onload = () => {
    if (typeof initZoom === 'function') initZoom();
    if (typeof resetRotationState === 'function') resetRotationState();
    if (typeof initRotation === 'function') initRotation();
    if (typeof initDrawingCanvas === 'function') initDrawingCanvas(img);
    // Initialiser l'image sélectionnée
    initSelectedImage();
  };

  if (img.complete) {
    if (typeof initZoom === 'function') initZoom();
    if (typeof resetRotationState === 'function') resetRotationState();
    if (typeof initRotation === 'function') initRotation();
    if (typeof initDrawingCanvas === 'function') initDrawingCanvas(img);
    // Initialiser l'image sélectionnée
    initSelectedImage();
  }

  window.currentImagePath = path;
}

function selectImage(path, basename) {
  console.log("Sélection de l'image:", basename, "chemin:", path);
  
  const area = document.getElementById('display-area');
  
  // Mettre à jour l'affichage avec la nouvelle image
  area.innerHTML = `
    <div id="zoom-container" style="overflow: hidden; position: relative; width: 100%; height: 100%;">
      <img id="selected-image" src="${path}" alt="Image sélectionnée" style="transform-origin: center center; cursor: grab;">
    </div>
  `;

  // IMPORTANT: Mettre à jour immédiatement window.selectedImageName
  window.selectedImageName = basename;
  console.log("window.selectedImageName mis à jour:", window.selectedImageName);

  // Mettre à jour les indicateurs visuels en utilisant la fonction centralisée
  if (typeof updateImageIndicators === 'function') {
    updateImageIndicators(basename);
  } else {
    // Fallback si la fonction n'est pas disponible
    console.log("Fonction updateImageIndicators non disponible, utilisation du fallback");
    document.querySelectorAll('.image-indicator').forEach(indicator => {
      indicator.style.display = 'none';
    });
    
    const currentIndicator = document.getElementById(`indicator-${basename}`);
    if (currentIndicator) {
      currentIndicator.style.display = 'block';
    }

    // Mettre à jour les champs cachés des formulaires
    const laplacienImageField = document.getElementById('laplacien-image');
    const gaussianImageField = document.getElementById('gaussian-image');
    const medianImageField = document.getElementById('median-image');
    
    if (laplacienImageField) {
      laplacienImageField.value = basename;
    }
    if (gaussianImageField) {
      gaussianImageField.value = basename;
    }
    if (medianImageField) {
      medianImageField.value = basename;
    }
    
    console.log("Champs cachés mis à jour (fallback):", basename);
  }

  // Mettre à jour le chemin de l'image courante
  window.currentImagePath = path;

  // Initialiser le zoom après le chargement de l'image
  const img = document.getElementById('selected-image');
  img.onload = () => {
    if (typeof initZoom === 'function') {
      initZoom();
    }
    if (typeof resetRotationState === 'function') {
      resetRotationState(); // Réinitialiser l'état de rotation pour la nouvelle image
    }
    if (typeof initRotation === 'function') {
      initRotation(); // Initialiser la rotation
    }
    // Initialiser l'image sélectionnée
    initSelectedImage();
  };
  
  // Si l'image est déjà en cache et chargée
  if (img.complete) {
    if (typeof initZoom === 'function') {
      initZoom();
    }
    if (typeof resetRotationState === 'function') {
      resetRotationState();
    }
    if (typeof initRotation === 'function') {
      initRotation();
    }
    // Initialiser l'image sélectionnée
    initSelectedImage();
  }
}

// Fonction pour initialiser l'image sélectionnée au chargement
function initSelectedImage() {
  const selectedImage = document.getElementById('selected-image');
  if (selectedImage && selectedImage.src) {
    // Extraire le nom du fichier depuis l'URL de l'image
    const srcParts = selectedImage.src.split('/');
    const imageName = srcParts[srcParts.length - 1];
    
    console.log("initSelectedImage - Image détectée:", imageName);
    
    // Mettre à jour window.selectedImageName seulement si elle n'est pas déjà définie correctement
    if (!window.selectedImageName || window.selectedImageName !== imageName) {
      window.selectedImageName = imageName;
      console.log("window.selectedImageName initialisé:", window.selectedImageName);
    }
    
    // Mettre à jour les champs cachés des formulaires si ils ne sont pas déjà remplis
    const laplacienImageField = document.getElementById('laplacien-image');
    const gaussianImageField = document.getElementById('gaussian-image');
    const medianImageField = document.getElementById('median-image');
    
    if (laplacienImageField && (!laplacienImageField.value || laplacienImageField.value !== imageName)) {
      laplacienImageField.value = imageName;
    }
    if (gaussianImageField && (!gaussianImageField.value || gaussianImageField.value !== imageName)) {
      gaussianImageField.value = imageName;
    }
    if (medianImageField && (!medianImageField.value || medianImageField.value !== imageName)) {
      medianImageField.value = imageName;
    }
    
    console.log("Champs cachés vérifiés/mis à jour avec:", imageName);
  }
}