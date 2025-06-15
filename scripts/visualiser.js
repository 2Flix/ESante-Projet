// Variables globales
let currentSelectedImage = ''; // Nom de l'image selectionnee


// Fonction pour charger les donnees d'une image
function loadImageData(imageName) {
  fetch(`get_image_data.php?image=${encodeURIComponent(imageName)}`) // Requete vers le serveur pour obtenir les donnees de l'image
    .then(response => response.json())
    .then(data => {
      if (data.success) { // Si des donnees existent, on les affiche formatees
        showFormattedData(data.data, imageName);

        // Affichage de l'indicateur visuel associe a l'image
        const indicator = document.getElementById(`indicator-${imageName}`);
        if (indicator) {
          indicator.style.display = 'block';
        }
      } else { // Si aucune donnee, affichage du formulaire vierge
        showForm();
        document.getElementById('formulaire').reset();
        document.getElementById('selectedImageInput').value = imageName;
        document.getElementById('formInitialMessage').textContent = 'Aucune donnée pour cette image. Remplissez le formulaire.';
        document.getElementById('formInitialMessage').style.color = 'orange';
      }
    })
    .catch(error => { // Gestion des erreurs reseau
      console.error('Erreur lors du chargement des données:', error);
      showForm();
    });
}


// Fonction pour afficher les donnees formatees
function showFormattedData(formData, imageName) {
  document.getElementById('formInitialMessage').style.display = 'none'; // Masquer les messages et formulaire
  document.getElementById('formulaire').style.display = 'none';

  // Affichage des donnees sauvegardees
  const outputContainer = document.getElementById('outputContainer');
  outputContainer.innerHTML = `
      <h3>Informations sauvegardées :</h3>
      <p><strong>Image :</strong> ${imageName}</p>
      <p><strong>Prénom :</strong> ${formData.prenom}</p>
      <p><strong>Nom :</strong> ${formData.nom}</p>
      <p><strong>Age :</strong> ${formData.age} ans</p>
      <p><strong>Sexe :</strong> ${formData.sexe}</p>
      <p><strong>Taille :</strong> ${formData.taille} cm</p>
      <p><small>Sauvegardé le : ${formData.timestamp}</small></p>
  `;
  outputContainer.style.display = 'block';

  // Bouton pour modifier les informations existantes
  const editButton = document.createElement('button');
  editButton.textContent = 'Modifier les informations';
  editButton.onclick = function() { // Remplissage du formulaire avec les données existantes
      document.getElementById('prenom').value = formData.prenom;
      document.getElementById('nom').value = formData.nom;
      document.getElementById('age').value = formData.age;
      document.getElementById('taille').value = formData.taille;

      // Pre-selection du sexe
      if (formData.sexe === 'Homme') {
        document.getElementById('homme').checked = true;
      } else if (formData.sexe === 'Femme') {
        document.getElementById('femme').checked = true;
      }

      // Associer l’image au formulaire
      document.getElementById('selectedImageInput').value = imageName;

      // Afficher le formulaire pour modification
      showForm();
      document.getElementById('formInitialMessage').textContent = 'Modification des données existantes.';
      document.getElementById('formInitialMessage').style.color = 'blue';
  };
  outputContainer.appendChild(editButton);
}


// Fonction pour afficher le formulaire
function showForm() {
  document.getElementById('formInitialMessage').style.display = 'block';
  document.getElementById('formulaire').style.display = 'block';
  document.getElementById('outputContainer').style.display = 'none';
}

// Extension de la fonction selectImage de display.js pour ajouter des comportements personnalises
function extendSelectImage() {
  const originalSelectImage = window.selectImage; // Sauvegarde de la fonction originale

  // Redefinition de la fonction selectImage
  window.selectImage = function(path, basename) {
    if (originalSelectImage) {
      originalSelectImage(path, basename); // Appel de la fonction d'origine
    }

    // Mise a jour de l’image selectionnee
    currentSelectedImage = basename;
    document.getElementById('selectedImageInput').value = basename;

    // Charger les donnees liees a cette image
    loadImageData(basename);
    showForm();

    // Reinitialiser tous les indicateurs visuels
    document.querySelectorAll('.image-indicator').forEach(indicator => {
      indicator.style.display = 'none';
    });

    // Afficher l'indicateur de l'image selectionnee
    const currentIndicator = document.getElementById(`indicator-${basename}`);
    if (currentIndicator) {
      currentIndicator.style.display = 'block';
    }
  };
}


// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
  extendSelectImage(); // Etendre la fonction de selection d'image

  // Gestionnaire de soumission du formulaire
  document.getElementById('formulaire').addEventListener('submit', function(event) {
      event.preventDefault(); // Empecher le rechargement de la page

      // Verification qu'une image est selectionnee
      if (!currentSelectedImage) {
        alert('Veuillez d\'abord sélectionner une image.');
        return;
      }

      // Recuperation des valeurs du formulaire
      const prenom = document.getElementById('prenom').value;
      const nom = document.getElementById('nom').value;
      const age = document.getElementById('age').value;
      const taille = document.getElementById('taille').value;
      const sexe = document.querySelector('input[name="sexe"]:checked') ? document.querySelector('input[name="sexe"]:checked').value : '';

      // Construction de l'objet FormData pour l'envoi
      const formData = new FormData();
      formData.append('prenom', prenom);
      formData.append('nom', nom);
      formData.append('age', age);
      formData.append('sexe', sexe);
      formData.append('taille', taille);
      formData.append('selectedImage', currentSelectedImage);
      formData.append('InscriptionEnvoyer', 'Sign Up');

      // Envoi des donnees au serveur via POST
      fetch('traitement_form.php', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) { // Affichage des donnees sauvegardees apres succes
              document.getElementById('formInitialMessage').style.display = 'none';
              document.getElementById('formulaire').style.display = 'none';

              const outputContainer = document.getElementById('outputContainer');
              outputContainer.innerHTML = `
                  <h3>Informations sauvegardées :</h3>
                  <p><strong>Image :</strong> ${currentSelectedImage}</p>
                  <p><strong>Prénom :</strong> ${data.data.prenom}</p>
                  <p><strong>Nom :</strong> ${data.data.nom}</p>
                  <p><strong>Age :</strong> ${data.data.age} ans</p>
                  <p><strong>Sexe :</strong> ${data.data.sexe}</p>
                  <p><strong>Taille :</strong> ${data.data.taille} cm</p>
                  <p><small>Sauvegardé le : ${data.data.timestamp}</small></p>
              `;
              outputContainer.style.display = 'block';

              // Afficher l’indicateur pour l’image concernee
              const indicator = document.getElementById(`indicator-${currentSelectedImage}`);
              if (indicator) {
                indicator.style.display = 'block';
              }

              // Bouton pour permettre une modification ulterieure
              const resetButton = document.createElement('button');
              resetButton.textContent = 'Continuer à modifier';
              resetButton.onclick = function() {
                  showForm(); // Reafficher le formulaire
              };
              outputContainer.appendChild(resetButton);

          } else { // Afficher un message d'erreur si la sauvegarde echoue
              alert(data.message);
          }
      })
      .catch(error => {
          // Gestion des erreurs reseau
          console.error('Erreur:', error);
          alert('Une erreur est survenue lors de l\'envoi des données.');
      });
  });
});
