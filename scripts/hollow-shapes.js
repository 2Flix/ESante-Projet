// hollow-shapes.js - Système de dessin de formes creuses

let isHollowShapeMode = false;
let currentShapeType = 'circle'; // 'circle', 'rectangle', 'line'
let isDrawingShape = false;
let startPoint = { x: 0, y: 0 };
let currentStrokeColor = '#ff0000';
let currentStrokeWidth = 2;
let hollowShapeCanvas = null;
let hollowShapeCtx = null;
let drawnShapes = []; // Stocker toutes les formes dessinées

// Initialiser le mode formes creuses
function initHollowShapeMode() {
    const img = document.getElementById('selected-image');
    if (!img) {
        alert('Veuillez d\'abord sélectionner une image');
        return;
    }

    // Créer ou réinitialiser le canvas pour les formes creuses
    createHollowShapeCanvas();
    
    // Activer le mode
    isHollowShapeMode = true;
    
    // Mettre à jour l'interface
    updateHollowShapeUI();
    
    console.log('Mode formes creuses activé');
}

// Créer le canvas pour les formes creuses
function createHollowShapeCanvas() {
    const img = document.getElementById('selected-image');
    const container = document.getElementById('zoom-container');
    
    if (!img || !container) return;
    
    // Supprimer l'ancien canvas s'il existe
    const existingCanvas = document.getElementById('hollow-shape-canvas');
    if (existingCanvas) {
        existingCanvas.remove();
    }
    
    // Créer le nouveau canvas
    hollowShapeCanvas = document.createElement('canvas');
    hollowShapeCanvas.id = 'hollow-shape-canvas';
    hollowShapeCanvas.style.position = 'absolute';
    hollowShapeCanvas.style.top = '0';
    hollowShapeCanvas.style.left = '0';
    hollowShapeCanvas.style.zIndex = '15';
    hollowShapeCanvas.style.cursor = 'crosshair';
    hollowShapeCanvas.style.pointerEvents = 'auto';
    
    // CORRECTION: Synchroniser parfaitement le canvas avec l'image
    function updateCanvasSize() {
        const imgRect = img.getBoundingClientRect();
        const containerRect = container.getBoundingClientRect();
        
        // Calculer la position et taille réelles de l'image affichée
        const imgDisplayWidth = img.offsetWidth;
        const imgDisplayHeight = img.offsetHeight;
        const imgLeft = img.offsetLeft;
        const imgTop = img.offsetTop;
        
        // Positionner le canvas exactement sur l'image
        hollowShapeCanvas.style.left = imgLeft + 'px';
        hollowShapeCanvas.style.top = imgTop + 'px';
        hollowShapeCanvas.style.width = imgDisplayWidth + 'px';
        hollowShapeCanvas.style.height = imgDisplayHeight + 'px';
        
        // Définir la résolution du canvas basée sur l'image originale
        hollowShapeCanvas.width = img.naturalWidth || img.width;
        hollowShapeCanvas.height = img.naturalHeight || img.height;
    }
    
    // Mettre à jour la taille immédiatement
    updateCanvasSize();
    
    // Mettre à jour la taille si l'image change (zoom, etc.)
    const resizeObserver = new ResizeObserver(updateCanvasSize);
    resizeObserver.observe(img);
    
    container.appendChild(hollowShapeCanvas);
    
    hollowShapeCtx = hollowShapeCanvas.getContext('2d');
    
    // Redessiner les formes existantes si il y en a
    redrawAllShapes();
    
    // Ajouter les événements de souris
    addHollowShapeEventListeners();
}

// Ajouter les événements pour le dessin de formes
function addHollowShapeEventListeners() {
    if (!hollowShapeCanvas) return;
    
    hollowShapeCanvas.addEventListener('mousedown', startDrawingShape);
    hollowShapeCanvas.addEventListener('mousemove', drawingShape);
    hollowShapeCanvas.addEventListener('mouseup', endDrawingShape);
    hollowShapeCanvas.addEventListener('mouseleave', endDrawingShape);
}

// Fonction pour convertir les coordonnées de l'écran vers le canvas
function getCanvasCoordinates(e) {
    const rect = hollowShapeCanvas.getBoundingClientRect();
    const scaleX = hollowShapeCanvas.width / rect.width;
    const scaleY = hollowShapeCanvas.height / rect.height;
    
    return {
        x: (e.clientX - rect.left) * scaleX,
        y: (e.clientY - rect.top) * scaleY
    };
}

// Commencer à dessiner une forme
function startDrawingShape(e) {
    if (!isHollowShapeMode) return;
    
    isDrawingShape = true;
    startPoint = getCanvasCoordinates(e);
}

// Dessiner la forme en cours
function drawingShape(e) {
    if (!isDrawingShape || !isHollowShapeMode) return;
    
    const currentPoint = getCanvasCoordinates(e);
    
    // Effacer le canvas et redessiner toutes les formes + aperçu
    redrawAllShapes();
    drawPreviewShape(startPoint, currentPoint);
}

// Terminer le dessin de la forme
function endDrawingShape(e) {
    if (!isDrawingShape || !isHollowShapeMode) return;
    
    isDrawingShape = false;
    
    const endPoint = getCanvasCoordinates(e);
    
    // Créer l'objet forme et l'ajouter à la liste
    const shape = createShapeObject(startPoint, endPoint);
    if (shape) {
        drawnShapes.push(shape);
        redrawAllShapes();
    }
}

// Créer un objet forme
function createShapeObject(start, end) {
    const shape = {
        type: currentShapeType,
        start: start,
        end: end,
        strokeColor: currentStrokeColor,
        strokeWidth: currentStrokeWidth
    };
    
    // Vérifier que la forme est assez grande
    switch (currentShapeType) {
        case 'circle':
            const radius = Math.sqrt(Math.pow(end.x - start.x, 2) + Math.pow(end.y - start.y, 2));
            if (radius < 5) return null;
            shape.radius = radius;
            break;
            
        case 'rectangle':
            const width = end.x - start.x;
            const height = end.y - start.y;
            if (Math.abs(width) < 5 || Math.abs(height) < 5) return null;
            shape.width = width;
            shape.height = height;
            break;
            
        case 'line':
            const distance = Math.sqrt(Math.pow(end.x - start.x, 2) + Math.pow(end.y - start.y, 2));
            if (distance < 5) return null;
            break;
    }
    
    return shape;
}

// Dessiner l'aperçu de la forme
function drawPreviewShape(start, end) {
    if (!hollowShapeCtx) return;
    
    hollowShapeCtx.save();
    hollowShapeCtx.strokeStyle = currentStrokeColor;
    hollowShapeCtx.lineWidth = currentStrokeWidth;
    hollowShapeCtx.setLineDash([5, 5]); // Ligne pointillée pour l'aperçu
    
    drawShape(start, end, currentShapeType);
    
    hollowShapeCtx.restore();
}

// Fonction générique pour dessiner une forme
function drawShape(start, end, type, strokeColor = null, strokeWidth = null) {
    if (!hollowShapeCtx) return;
    
    if (strokeColor) hollowShapeCtx.strokeStyle = strokeColor;
    if (strokeWidth) hollowShapeCtx.lineWidth = strokeWidth;
    
    switch (type) {
        case 'circle':
            const radius = Math.sqrt(Math.pow(end.x - start.x, 2) + Math.pow(end.y - start.y, 2));
            hollowShapeCtx.beginPath();
            hollowShapeCtx.arc(start.x, start.y, radius, 0, 2 * Math.PI);
            hollowShapeCtx.stroke();
            break;
            
        case 'rectangle':
            const width = end.x - start.x;
            const height = end.y - start.y;
            hollowShapeCtx.beginPath();
            hollowShapeCtx.rect(start.x, start.y, width, height);
            hollowShapeCtx.stroke();
            break;
            
        case 'line':
            hollowShapeCtx.beginPath();
            hollowShapeCtx.moveTo(start.x, start.y);
            hollowShapeCtx.lineTo(end.x, end.y);
            hollowShapeCtx.stroke();
            break;
    }
}

// Redessiner toutes les formes stockées
function redrawAllShapes() {
    if (!hollowShapeCtx) return;
    
    // Effacer le canvas
    hollowShapeCtx.clearRect(0, 0, hollowShapeCanvas.width, hollowShapeCanvas.height);
    
    // Redessiner toutes les formes stockées
    drawnShapes.forEach(shape => {
        hollowShapeCtx.save();
        hollowShapeCtx.strokeStyle = shape.strokeColor;
        hollowShapeCtx.lineWidth = shape.strokeWidth;
        hollowShapeCtx.setLineDash([]); // Ligne continue
        
        drawShape(shape.start, shape.end, shape.type);
        
        hollowShapeCtx.restore();
    });
}

// Changer le type de forme
function setShapeType(type) {
    currentShapeType = type;
    updateHollowShapeUI();
}

// Changer la couleur du contour
function setStrokeColor(color) {
    currentStrokeColor = color;
}

// Changer l'épaisseur du contour
function setStrokeWidth(width) {
    currentStrokeWidth = parseInt(width);
}

// Effacer toutes les formes
function clearAllShapes() {
    drawnShapes = [];
    if (hollowShapeCtx) {
        hollowShapeCtx.clearRect(0, 0, hollowShapeCanvas.width, hollowShapeCanvas.height);
    }
}

// Désactiver le mode formes creuses
function disableHollowShapeMode() {
    isHollowShapeMode = false;
    isDrawingShape = false;
    
    if (hollowShapeCanvas) {
        hollowShapeCanvas.style.pointerEvents = 'none';
        hollowShapeCanvas.style.cursor = 'default';
    }
    
    updateHollowShapeUI();
    console.log('Mode formes creuses désactivé');
}

// Fonction de téléchargement supprimée - seule la sauvegarde serveur est disponible

// Sauvegarder l'image avec les formes sur le serveur
function saveImageWithShapesToServer() {
    const img = document.getElementById('selected-image');
    if (!img || !hollowShapeCanvas) {
        alert('Aucune image ou formes à sauvegarder');
        return;
    }
    
    // Créer un canvas temporaire avec les bonnes dimensions
    const tempCanvas = document.createElement('canvas');
    const tempCtx = tempCanvas.getContext('2d');
    
    //Utiliser les dimensions naturelles de l'image
    const naturalWidth = img.naturalWidth || img.width;
    const naturalHeight = img.naturalHeight || img.height;
    
    tempCanvas.width = naturalWidth;
    tempCanvas.height = naturalHeight;
    
    // Dessiner l'image de base à sa taille naturelle
    tempCtx.drawImage(img, 0, 0, naturalWidth, naturalHeight);
    
    // Dessiner les formes directement
    tempCtx.drawImage(hollowShapeCanvas, 0, 0);
    
    // Convertir en base64
    const imageData = tempCanvas.toDataURL('image/png');
    
    // Générer un nom de fichier unique
    const originalName = window.currentImagePath ? 
        window.currentImagePath.split('/').pop().split('.')[0] : 'image';
    const filename = originalName + '_avec_formes_' + Date.now() + '.png';
    
    // Envoyer au serveur
    const formData = new FormData();
    formData.append('action', 'save_canvas_image');
    formData.append('image_data', imageData);
    formData.append('filename', filename);
    
    fetch('upload_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recharger la liste des images
            location.reload();
        } else {
            alert('Erreur lors de la sauvegarde : ' + (data.error || 'Erreur inconnue'));
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la sauvegarde');
    });
}

// Mettre à jour l'interface utilisateur
function updateHollowShapeUI() {
    const toggleButton = document.getElementById('toggle-hollow-shape-btn');
    if (toggleButton) {
        toggleButton.textContent = isHollowShapeMode ? 'Désactiver Formes' : 'Activer Formes';
        toggleButton.style.backgroundColor = isHollowShapeMode ? '#dc3545' : '#007bff';
    }
    
    // Mettre à jour les boutons de formes
    document.querySelectorAll('.shape-btn').forEach(btn => {
        btn.style.backgroundColor = btn.dataset.shape === currentShapeType ? '#28a745' : '#6c757d';
    });
}

// Fonction principale pour activer/désactiver le mode
function toggleHollowShapeMode() {
    if (isHollowShapeMode) {
        disableHollowShapeMode();
    } else {
        initHollowShapeMode();
    }
}