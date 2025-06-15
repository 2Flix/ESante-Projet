function initZoom() {
  const slider = document.getElementById('zoom-range');
  const image = document.getElementById('selected-image');
  const container = document.getElementById('zoom-container');

  if (!slider || !image || !container) return;

  // Réinitialiser le slider à 0 (position de départ)
  slider.value = 0;
  // Ne pas écraser la rotation existante, juste appliquer le scale
  applyTransform(image, 1, getCurrentRotation());

  slider.addEventListener('input', () => {
    const sliderValue = parseFloat(slider.value);
    // Convertir la valeur du slider (0-2) en échelle (1-3)
    const scale = 1 + sliderValue;
    
    if (scale > 1) {
      // Créer un wrapper pour forcer les barres de défilement
      if (!container.querySelector('.zoom-wrapper')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'zoom-wrapper';
        
        // Déplacer l'image dans le wrapper
        const img = container.removeChild(image);
        wrapper.appendChild(img);
        container.appendChild(wrapper);
        
        // Ajuster le container pour le scroll
        container.style.overflow = 'auto';
        container.style.display = 'block';
        container.style.textAlign = 'left';
        container.style.padding = '0';
      }
      
      const wrapper = container.querySelector('.zoom-wrapper');
      const containerWidth = container.clientWidth;
      const containerHeight = container.clientHeight;
      
      // Calculer les dimensions en tenant compte de la rotation
      const rotation = getCurrentRotation();
      const dimensions = getRotatedDimensions(image.naturalWidth, image.naturalHeight, rotation);
      
      // Calculer les dimensions de l'image zoomée ET rotée
      const scaledWidth = dimensions.width * scale;
      const scaledHeight = dimensions.height * scale;
      
      // Le wrapper doit être assez grand pour contenir l'image zoomée et rotée
      wrapper.style.width = Math.max(scaledWidth, containerWidth) + 'px';
      wrapper.style.height = Math.max(scaledHeight, containerHeight) + 'px';
      wrapper.style.display = 'inline-block';
      wrapper.style.position = 'relative';
      
      // Centrer l'image dans le wrapper
      const offsetX = Math.max(0, (scaledWidth - image.naturalWidth * scale) / 2);
      const offsetY = Math.max(0, (scaledHeight - image.naturalHeight * scale) / 2);
      
      // Si l'image zoomée est plus petite que le container, la centrer
      if (scaledWidth < containerWidth) {
        wrapper.style.marginLeft = ((containerWidth - scaledWidth) / 2) + 'px';
      } else {
        wrapper.style.marginLeft = '0';
      }
      
      if (scaledHeight < containerHeight) {
        wrapper.style.marginTop = ((containerHeight - scaledHeight) / 2) + 'px';
      } else {
        wrapper.style.marginTop = '0';
      }
      
      // Positionner l'image au centre du wrapper
      image.style.position = 'absolute';
      image.style.left = offsetX + 'px';
      image.style.top = offsetY + 'px';
      
      // Appliquer le zoom ET conserver la rotation
      applyTransform(image, scale, getCurrentRotation());
      image.style.transformOrigin = 'center';
      
    } else {
      // Mode normal : revenir au mode centré simple
      const wrapper = container.querySelector('.zoom-wrapper');
      if (wrapper) {
        const img = wrapper.removeChild(image);
        container.removeChild(wrapper);
        container.appendChild(img);
        
        container.style.overflow = 'hidden';
        container.style.display = 'flex';
        container.style.justifyContent = 'center';
        container.style.alignItems = 'center';
        container.style.textAlign = 'initial';
        container.style.padding = '0';
        
        // Réinitialiser le positionnement de l'image
        image.style.position = 'static';
        image.style.left = 'auto';
        image.style.top = 'auto';
      }
      
      // Appliquer scale 1 mais conserver la rotation
      applyTransform(image, 1, getCurrentRotation());
      image.style.transformOrigin = 'center';
    }
  });
}

// Fonction pour calculer les dimensions d'une image après rotation
function getRotatedDimensions(width, height, rotation) {
  const radians = (rotation * Math.PI) / 180;
  const cos = Math.abs(Math.cos(radians));
  const sin = Math.abs(Math.sin(radians));
  
  const newWidth = width * cos + height * sin;
  const newHeight = width * sin + height * cos;
  
  return {
    width: newWidth,
    height: newHeight
  };
}

// Fonction utilitaire pour appliquer les transformations combinées
function applyTransform(element, scale, rotation) {
  element.style.transform = `scale(${scale}) rotate(${rotation}deg)`;
}

// Fonction pour récupérer la rotation actuelle
function getCurrentRotation() {
  return window.currentRotation || 0;
}

// Fonction pour récupérer le zoom actuel
function getCurrentScale() {
  const slider = document.getElementById('zoom-range');
  if (slider) {
    return 1 + parseFloat(slider.value);
  }
  return 1;
}