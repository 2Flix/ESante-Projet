// Variable globale pour stocker le nom de l'image sélectionnée
let currentImageName = '';

// Fonction appelée lorsqu'une image est sélectionnée
function selectImage(path, basename) {
  // Mise à jour du nom de l'image sélectionnée
  currentImageName = basename;

  // Si une fonction showImage est définie, l'utiliser pour afficher l'image
  if (typeof showImage === 'function') {
    showImage(path);
  } else { // Sinon, afficher l'image manuellement dans la zone prévue à cet effet
    const area = document.querySelector('.image-area'); // Sélection de la zone d'affichage
    const img = new Image(); // Création d'un élément image
    img.id = "selected-image"; // Attribution d'un ID
    img.src = path; // Chemin de l'image
    img.alt = "Image sélectionnée"; // Texte alternatif
    img.style.maxWidth = "90%"; // Taille max en largeur
    img.style.maxHeight = "90%"; // Taille max en hauteur

    // Lorsque l'image est chargée, elle est affichée dans la zone prévue
    img.onload = () => {
      area.innerHTML = ''; // On vide d'abord le contenu actuel
      area.appendChild(img); // Puis on y ajoute la nouvelle image
    };
  }

  // Mise à jour des champs cachés des formulaires pour transmettre le nom de l'image sélectionnée
  document.getElementById('laplacien-image').value = basename;
  document.getElementById('gaussian-image').value = basename;

  // Mise à jour de l'URL du navigateur sans recharger la page 
  const url = new URL(window.location);
  url.searchParams.set('image', basename); // Ajoute ou modifie le paramètre 'image'
  window.history.pushState({}, '', url); // Met à jour l'URL affichée
}

// Exécution une fois que le DOM est complètement chargé
window.addEventListener('DOMContentLoaded', () => {
  // Récupération des paramètres de l'URL 
  const urlParams = new URLSearchParams(window.location.search);
  const imageFromUrl = urlParams.get('image');

  if (imageFromUrl) {
    currentImageName = imageFromUrl;
    document.getElementById('laplacien-image').value = imageFromUrl;
    document.getElementById('gaussian-image').value = imageFromUrl;
  }

  // Validation du formulaire Laplacien avant soumission
  document.getElementById('laplacien-form').addEventListener('submit', function(e) {
    // Si aucune image n'est sélectionnée, empecher la soumission et alerter l'utilisateur
    if (!currentImageName && !document.getElementById('laplacien-image').value) {
      e.preventDefault();
      alert('Veuillez sélectionner une image avant d\'appliquer le filtre Laplacien.');
    }
  });

  // Validation du formulaire Gaussien avant soumission
  document.getElementById('gaussian-form').addEventListener('submit', function(e) {
    // Si aucune image n'est sélectionnée, empecher la soumission et message d'alerte
    if (!currentImageName && !document.getElementById('gaussian-image').value) {
      e.preventDefault();
      alert('Veuillez sélectionner une image avant d\'appliquer le filtre.');
    }
  });
});
