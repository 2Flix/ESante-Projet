// Variables globales pour gerer la rotation
let currentRotation = 0;

// Rendre la rotation accessible globalement
window.currentRotation = currentRotation;

// Fonction pour faire tourner l'image visuellement
function rotateImage(angle) {
  const selectedImage = document.getElementById('selected-image');
  
  if (!selectedImage || !selectedImage.src) {
    alert('Aucune image sélectionnée');
    return;
  }

  // Mettre à jour la rotation actuelle
  currentRotation = (currentRotation + angle) % 360;
  if (currentRotation < 0) currentRotation += 360;
  
  // Mettre à jour la variable globale
  window.currentRotation = currentRotation;

  // S'assurer que l'origine de transformation est au centre pour la rotation
  selectedImage.style.transformOrigin = 'center center';

  // Appliquer la rotation ET conserver le zoom actuel
  const currentScale = getCurrentScale();
  applyTransform(selectedImage, currentScale, currentRotation);
}

// Fonction appelée quand une image est sélectionnée pour réinitialiser la rotation
function resetRotationState() {
  currentRotation = 0;
  window.currentRotation = 0;
  const selectedImage = document.getElementById('selected-image');
  if (selectedImage) {
    // S'assurer que l'origine de transformation est correcte
    selectedImage.style.transformOrigin = 'center center';
    // Réinitialiser seulement la rotation, pas le zoom
    const currentScale = getCurrentScale();
    applyTransform(selectedImage, currentScale, 0);
  }
}

// Fonction utilitaire pour appliquer les transformations combinées
function applyTransform(element, scale, rotation) {
  // S'assurer que l'origine est au centre avant d'appliquer les transformations
  element.style.transformOrigin = 'center center';
  element.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
}

// Fonction pour récupérer le zoom actuel
function getCurrentScale() {
  const slider = document.getElementById('zoom-range');
  if (slider) {
    return 1 + parseFloat(slider.value);
  }
  return 1;
}

// Fonction d'initialisation pour s'assurer que l'image est correctement configurée
function initRotation() {
  const selectedImage = document.getElementById('selected-image');
  if (selectedImage) {
    // Forcer l'origine de transformation au centre
    selectedImage.style.transformOrigin = 'center center';
    // Appliquer l'état actuel
    const currentScale = getCurrentScale();
    applyTransform(selectedImage, currentScale, currentRotation);
  }
}