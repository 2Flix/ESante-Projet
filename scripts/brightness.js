// Fonction pour appliquer la luminosité en utilisant la valeur saisie
function applyBrightness() {
    const brightnessInput = document.getElementById('brightness-input');
    const imageElement = document.querySelector('#zoom-container img');
    
    if (!imageElement) {
        alert('Aucune image sélectionnée');
        return;
    }
    
    const brightnessValue = parseFloat(brightnessInput.value);
    
    // Validation de la valeur saisie
    if (isNaN(brightnessValue) || brightnessValue < 0) {
        alert('Veuillez saisir une valeur valide (0 ou plus)');
        brightnessInput.value = 100; // Valeur par défaut
        return;
    }
    
    // Appliquer le filtre de luminosité
    imageElement.style.filter = `brightness(${brightnessValue}%)`;
}

// Fonction pour ajuster la luminosité de l'image (conservée pour compatibilité)
function adjustBrightness() {
    const brightnessInput = document.getElementById('brightness-input');
    const imageElement = document.querySelector('#zoom-container img');
    
    if (imageElement) {
        const brightnessValue = brightnessInput.value;
        imageElement.style.filter = `brightness(${brightnessValue}%)`;
    }
}



// Écouteur d'événement pour permettre l'application avec la touche Entrée
document.addEventListener('DOMContentLoaded', function() {
    const brightnessInput = document.getElementById('brightness-input');
    
    if (brightnessInput) {
        // Permettre d'appliquer la luminosité avec la touche Entrée
        brightnessInput.addEventListener('keypress', function(event) {
            if (event.key === 'Enter') {
                applyBrightness();
            }
        });
    }
});

// Fonction pour réinitialiser la luminosité
function resetBrightness() {
    const brightnessInput = document.getElementById('brightness-input');
    const imageElement = document.querySelector('#zoom-container img');
    
    if (brightnessInput) {
        brightnessInput.value = 100;
    }
    
    if (imageElement) {
        imageElement.style.filter = 'brightness(100%)';
    }
}

// Fonction à appeler lors du changement d'image pour préserver les réglages
function preserveBrightnessOnImageChange() {
    const brightnessInput = document.getElementById('brightness-input');
    const imageElement = document.querySelector('#zoom-container img');
    
    if (imageElement && brightnessInput) {
        const brightnessValue = brightnessInput.value;
        imageElement.style.filter = `brightness(${brightnessValue}%)`;
    }
}