function initZoom() {
  const slider = document.getElementById('zoom-range'); 
  const image = document.getElementById('selected-image'); 
  const container = document.getElementById('zoom-container'); 

  // Si l'un des éléments n'existe pas, arrêter la fonction
  if (!slider || !image || !container) return;

  // Réinitialise le slider à 0 (pas de zoom)
  slider.value = 0;
  // Applique un zoom de 1 (normal) et garde la rotation actuelle
  applyTransform(image, 1, getCurrentRotation());

  // Écoute les changements de valeur du slider
  slider.addEventListener('input', () => {
    const sliderValue = parseFloat(slider.value);
    const scale = 1 + sliderValue; // Conversion du slider (0–2) en facteur de zoom (1–3)

    if (scale > 1) {
      // Si zoom > 1, créer un wrapper si pas déjà présent pour permettre le scroll
      if (!container.querySelector('.zoom-wrapper')) {
        const wrapper = document.createElement('div');
        wrapper.className = 'zoom-wrapper';

        // Déplace l'image dans le wrapper
        const img = container.removeChild(image);
        wrapper.appendChild(img);
        container.appendChild(wrapper);

        // Préparer le conteneur pour afficher des barres de mouvement
        container.style.overflow = 'auto';
        container.style.display = 'block';
        container.style.textAlign = 'left';
        container.style.padding = '0';
      }

      // Obtenir le wrapper et les dimensions utiles
      const wrapper = container.querySelector('.zoom-wrapper');
      const containerWidth = container.clientWidth;
      const containerHeight = container.clientHeight;

      const rotation = getCurrentRotation();
      const dimensions = getRotatedDimensions(image.naturalWidth, image.naturalHeight, rotation);

      // Dimensions de l'image après application du zoom et de la rotation
      const scaledWidth = dimensions.width * scale;
      const scaledHeight = dimensions.height * scale;

      // Ajuste la taille du wrapper pour contenir l'image agrandie
      wrapper.style.width = Math.max(scaledWidth, containerWidth) + 'px';
      wrapper.style.height = Math.max(scaledHeight, containerHeight) + 'px';
      wrapper.style.display = 'inline-block';
      wrapper.style.position = 'relative';

      // Calcul du décalage pour centrer l'image dans le wrapper
      const offsetX = Math.max(0, (scaledWidth - image.naturalWidth * scale) / 2);
      const offsetY = Math.max(0, (scaledHeight - image.naturalHeight * scale) / 2);

      // Centrer horizontalement si nécessaire
      if (scaledWidth < containerWidth) {
        wrapper.style.marginLeft = ((containerWidth - scaledWidth) / 2) + 'px';
      } else {
        wrapper.style.marginLeft = '0';
      }

      // Centrer verticalement si nécessaire
      if (scaledHeight < containerHeight) {
        wrapper.style.marginTop = ((containerHeight - scaledHeight) / 2) + 'px';
      } else {
        wrapper.style.marginTop = '0';
      }

      // Position absolue de l'image à l’intérieur du wrapper
      image.style.position = 'absolute';
      image.style.left = offsetX + 'px';
      image.style.top = offsetY + 'px';

      // Applique le zoom et conserve la rotation
      applyTransform(image, scale, getCurrentRotation());
      image.style.transformOrigin = 'center';

    } else {
      // Si zoom <= 1 : retour au mode affichage centré "normal"
      const wrapper = container.querySelector('.zoom-wrapper');
      if (wrapper) {
        // Déplace l'image hors du wrapper et supprime le wrapper
        const img = wrapper.removeChild(image);
        container.removeChild(wrapper);
        container.appendChild(img);

        // Restaure l'affichage centré
        container.style.overflow = 'hidden';
        container.style.display = 'flex';
        container.style.justifyContent = 'center';
        container.style.alignItems = 'center';
        container.style.textAlign = 'initial';
        container.style.padding = '0';

        // Réinitialise le positionnement de l'image
        image.style.position = 'static';
        image.style.left = 'auto';
        image.style.top = 'auto';
      }

      // Applique un zoom normal (1) et conserve la rotation
      applyTransform(image, 1, getCurrentRotation());
      image.style.transformOrigin = 'center';
    }
  });
}
