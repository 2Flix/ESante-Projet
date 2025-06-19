function applyMedian(event) {
  event.preventDefault();

  // Récupérer le nom de l'image de manière plus fiable
  let imageName = null;
  
  // 1. Essayer d'abord depuis l'image affichée (plus fiable)
  const selectedImage = document.getElementById('selected-image');
  if (selectedImage && selectedImage.src) {
    const srcParts = selectedImage.src.split('/');
    imageName = srcParts[srcParts.length - 1];
    console.log("Image récupérée depuis l'image affichée:", imageName);
  }
  
  // 2. Si pas trouvé, essayer window.selectedImageName
  if (!imageName) {
    imageName = window.selectedImageName;
    console.log("Image récupérée depuis window.selectedImageName:", imageName);
  }
  
  // 3. Si toujours pas trouvé, essayer depuis les champs cachés
  if (!imageName) {
    const medianImageField = document.getElementById('median-image');
    if (medianImageField && medianImageField.value) {
      imageName = medianImageField.value;
      console.log("Image récupérée depuis le champ caché:", imageName);
    }
  }

  const kernelInput = document.getElementById('median-kernel');
  const kernel = parseInt(kernelInput.value, 10);

  // Vérification de l'image sélectionnée
  if (!imageName) {
    alert("Veuillez sélectionner une image.");
    return;
  }

  // Vérification du noyau
  if (isNaN(kernel) || kernel < 3 || kernel % 2 === 0) {
    alert("Veuillez entrer une taille de noyau impaire supérieure ou égale à 3.");
    return;
  }

  console.log("Application du filtre médian sur:", imageName, "avec noyau:", kernel);

  fetch("median.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: `image=${encodeURIComponent(imageName)}&kernel=${kernel}`
  })
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        const newImage = data.newImage;
        console.log("Nouvelle image générée:", newImage);
        const displayArea = document.getElementById("display-area");
        
        // Afficher la nouvelle image dans la zone principale
        displayArea.innerHTML = `
          <div id="zoom-container" style="overflow: hidden; position: relative; width: 100%; height: 100%;">
            <img id="selected-image" src="uploads/${newImage}" style="transform-origin: top left; cursor: grab;">
          </div>
        `;
        
        // Mettre à jour le nom de l'image sélectionnée
        window.selectedImageName = newImage;
        
        // Ajouter la nouvelle image à la liste des images dans la barre latérale
        addImageToSidebar(newImage);
        
        // Mettre à jour les indicateurs visuels
        updateImageIndicators(newImage);
        
        // Initialiser le zoom et la rotation pour la nouvelle image
        const img = document.getElementById('selected-image');
        img.onload = () => {
          if (typeof initZoom === 'function') initZoom();
          if (typeof resetRotationState === 'function') resetRotationState();
          if (typeof initRotation === 'function') initRotation();
        };
        
        // Si l'image est déjà chargée
        if (img.complete) {
          if (typeof initZoom === 'function') initZoom();
          if (typeof resetRotationState === 'function') resetRotationState();
          if (typeof initRotation === 'function') initRotation();
        }
      } else {
        alert("Erreur lors du traitement : " + (data.message || "Erreur inconnue"));
      }
    })
    .catch(error => {
      console.error("Erreur réseau :", error);
      alert("Une erreur est survenue lors du traitement.");
    });
}

// Fonction pour ajouter une nouvelle image à la barre latérale
function addImageToSidebar(imageName) {
  const imageList = document.querySelector('.image-list');
  
  // Vérifier si l'image n'existe pas déjà dans la liste
  const existingImage = document.getElementById(`indicator-${imageName}`);
  if (existingImage) {
    return; // L'image existe déjà
  }
  
  // Créer l'élément pour la nouvelle image
  const imageThumb = document.createElement('div');
  imageThumb.className = 'img-thumb';
  imageThumb.onclick = function() {
    selectImage(`uploads/${imageName}`, imageName);
  };
  
  imageThumb.innerHTML = `
    <img src="uploads/${imageName}" alt="" />
    <div class="image-indicator" id="indicator-${imageName}"></div>
  `;
  
  // Ajouter la nouvelle image en haut de la liste
  imageList.insertBefore(imageThumb, imageList.firstChild);
}

// Fonction pour mettre à jour les indicateurs visuels
function updateImageIndicators(selectedImageName) {
  console.log("Mise à jour des indicateurs pour:", selectedImageName);
  
  // Masquer tous les indicateurs
  document.querySelectorAll('.image-indicator').forEach(indicator => {
    indicator.style.display = 'none';
  });
  
  // Afficher l'indicateur de l'image sélectionnée
  const currentIndicator = document.getElementById(`indicator-${selectedImageName}`);
  if (currentIndicator) {
    currentIndicator.style.display = 'block';
  }
  
  // Mettre à jour les champs cachés des formulaires
  const laplacienImageField = document.getElementById('laplacien-image');
  const gaussianImageField = document.getElementById('gaussian-image');
  const medianImageField = document.getElementById('median-image');
  
  if (laplacienImageField) {
    laplacienImageField.value = selectedImageName;
  }
  if (gaussianImageField) {
    gaussianImageField.value = selectedImageName;
  }
  if (medianImageField) {
    medianImageField.value = selectedImageName;
  }
  
  // Mettre à jour window.selectedImageName
  window.selectedImageName = selectedImageName;
  
  console.log("Champs cachés mis à jour avec:", selectedImageName);
}