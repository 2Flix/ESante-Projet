// Fonction permettant d'analyser l'image actuellement affichée
function analyserImage() {
  const img = document.getElementById('selected-image');
  const info = document.getElementById('image-info');
  // Élément <canvas> utilisé pour analyser les pixels de l'image
  const canvas = document.getElementById('analyzer-canvas');

  // Affichage dans la console 
  console.log("Image trouvée :", img);
  console.log("Chargée :", img.complete);
  console.log("Taille naturelle :", img.naturalWidth, img.naturalHeight);

  // Vérifie si l'image est présente, complètement chargée et valide
  if (!img || !img.complete || img.naturalWidth === 0) {
    info.textContent = "Aucune image sélectionnée ou chargement en cours.";
    return; // Arrête la fonction si l'image n'est pas prête
  }

  // Récupération du contexte 2D du canvas pour dessiner et lire l'image
  const context = canvas.getContext('2d');
  // Ajuste la taille du canvas à la taille de l'image
  canvas.width = img.naturalWidth;
  canvas.height = img.naturalHeight;
  // Dessine l'image dans le canvas
  context.drawImage(img, 0, 0); 

  // Récupère les données brutes des pixels de l'image (tableau RGBA)
  const imageData = context.getImageData(0, 0, canvas.width, canvas.height).data;

  // Variables pour stocker les niveaux de gris minimum et maximum
  let min = 255, max = 0;

  // Parcours des pixels (4 éléments par pixel : R,G,B,A)
  for (let i = 0; i < imageData.length; i += 4) {
    const gray = imageData[i]; // On suppose que l'image est en niveaux de gris, donc R = G = B
    if (gray < min) min = gray;
    if (gray > max) max = gray;
  }

  // Affiche les informations analysées dans la page
  info.innerHTML = `
    <strong>Dimensions :</strong> ${img.naturalWidth} x ${img.naturalHeight} px<br>
    <strong>Niveau de gris :</strong> min = ${min}, max = ${max}
  `;
}
