function showImage(path) {
  const area = document.getElementById('display-area');

  area.innerHTML = `
    <div id="zoom-container" style="overflow: hidden; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; background: #f5f5f5;">
      <img id="selected-image" src="${path}" alt="Image sélectionnée" style="transform: scale(1); transform-origin: center center; user-select: none;">
    </div>
  `;

  const img = document.getElementById('selected-image');
  img.onload = () => {
    if (typeof initZoom === 'function') {
      initZoom(); // Initialiser le zoom apres le chargement
    }
    if (typeof resetRotationState === 'function') {
      resetRotationState(); // Reinitialiser l'etat de rotation
    }
    if (typeof initRotation === 'function') {
      initRotation(); // Initialiser la rotation
    }
  };
}

function selectImage(path, basename) {
  const area = document.getElementById('display-area');
  
  // Mettre à jour l'affichage avec la nouvelle image
  area.innerHTML = `
    <div id="zoom-container" style="overflow: hidden; position: relative; width: 100%; height: 100%;">
      <img id="selected-image" src="${path}" alt="Image sélectionnée" style="transform-origin: center center; cursor: grab;">
    </div>
  `;

  // Mettre à jour les indicateurs visuels
  document.querySelectorAll('.image-indicator').forEach(indicator => {
    indicator.style.display = 'none';
  });
  
  const currentIndicator = document.getElementById(`indicator-${basename}`);
  if (currentIndicator) {
    currentIndicator.style.display = 'block';
  }

  // Mettre à jour les champs caches des formulaires
  const laplacienImageField = document.getElementById('laplacien-image');
  const gaussianImageField = document.getElementById('gaussian-image');
  
  if (laplacienImageField) {
    laplacienImageField.value = basename;
  }
  if (gaussianImageField) {
    gaussianImageField.value = basename;
  }

  // Initialiser le zoom apres le chargement de l'image
  const img = document.getElementById('selected-image');
  img.onload = () => {
    if (typeof initZoom === 'function') {
      initZoom();
    }
    if (typeof resetRotationState === 'function') {
      resetRotationState(); // Reinitialiser l'etat de rotation pour la nouvelle image
    }
    if (typeof initRotation === 'function') {
      initRotation(); // Initialiser la rotation
    }
  };
  
  // Si l'image est deja en cache et chargee
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
  }
}