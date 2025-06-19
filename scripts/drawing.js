let drawingMode = false;
let canvas, ctx;
let points = [];
let currentImage = null;

function toggleDrawingMode() {
  drawingMode = !drawingMode;
  points = [];
  const resultElement = document.getElementById("area-result");
  if (resultElement) {
    resultElement.textContent = drawingMode ? "Mode dessin activé - Cliquez pour tracer" : "Mode dessin désactivé";
  }
  
  if (drawingMode && currentImage) {
    showDrawingCanvas();
  } else {
    hideDrawingCanvas();
  }
  
  if (!drawingMode && points.length > 0) {
    clearDrawing();
  }
}

function initDrawingCanvas(imgElement) {
  if (!imgElement) return;
  
  currentImage = imgElement;
  
  // Utilise le canvas déjà créé par display.js
  canvas = document.getElementById("drawing-canvas");
  
  if (!canvas) {
    console.error("Canvas de dessin non trouvé !");
    return;
  }

  // Configure la taille du canvas pour qu'elle corresponde à l'image
  const container = imgElement.parentElement;
  const rect = imgElement.getBoundingClientRect();
  
  // Le canvas doit avoir les mêmes dimensions que l'image affichée
  canvas.width = imgElement.naturalWidth;
  canvas.height = imgElement.naturalHeight;
  canvas.style.width = imgElement.clientWidth + 'px';
  canvas.style.height = imgElement.clientHeight + 'px';
  
  // Positionne le canvas exactement au-dessus de l'image
  canvas.style.position = "absolute";
  canvas.style.top = "0";
  canvas.style.left = "0";
  canvas.style.pointerEvents = "none";
  canvas.style.zIndex = "10";
  canvas.style.display = "none"; // Caché par défaut
  
  ctx = canvas.getContext("2d");
  
  // Efface le canvas et réinitialise
  clearDrawing();
  
  console.log("Canvas initialisé:", {
    canvasWidth: canvas.width,
    canvasHeight: canvas.height,
    styleWidth: canvas.style.width,
    styleHeight: canvas.style.height,
    imageWidth: imgElement.naturalWidth,
    imageHeight: imgElement.naturalHeight
  });
}

function updateCanvasEvents() {
  if (!canvas) return;
  
  // Supprime tous les anciens event listeners
  const newCanvas = canvas.cloneNode(true);
  canvas.parentNode.replaceChild(newCanvas, canvas);
  canvas = newCanvas;
  ctx = canvas.getContext("2d");
  
  canvas.style.pointerEvents = drawingMode ? "auto" : "none";
  
  if (drawingMode) {
    canvas.addEventListener("click", handleCanvasClick);
    canvas.addEventListener("dblclick", closePolygon);
    console.log("Event listeners ajoutés au canvas");
  }
}

function handleCanvasClick(e) {
  e.preventDefault();
  e.stopPropagation();
  
  if (!drawingMode || !ctx || !currentImage) return;

  const rect = canvas.getBoundingClientRect();
  
  // Calcul des coordonnées en tenant compte de l'échelle
  const scaleX = canvas.width / canvas.clientWidth;
  const scaleY = canvas.height / canvas.clientHeight;
  
  const x = (e.clientX - rect.left) * scaleX;
  const y = (e.clientY - rect.top) * scaleY;

  points.push({ x, y });
  console.log("Point ajouté:", { x, y }, "Total points:", points.length);
  console.log("Coordonnées écran:", e.clientX - rect.left, e.clientY - rect.top);
  console.log("Échelle:", scaleX, scaleY);
  
  redraw();
}

function closePolygon(e) {
  e.preventDefault();
  e.stopPropagation();
  
  if (points.length >= 3) {
    const dpi = getImageDPI();
    const areaPixels = calculatePolygonArea(points);
    const areaCm2 = areaPixels / (dpi * dpi) * (2.54 * 2.54);
    const dimensions = calculatePolygonDimensions(points);
    const widthCm = pixelsToCm(dimensions.width, dpi);
    const heightCm = pixelsToCm(dimensions.height, dpi);
    
    document.getElementById("area-result").innerHTML = `
      <strong>Polygone fermé!</strong><br>
      <strong>Dimensions:</strong><br>
      Largeur: ${dimensions.width.toFixed(0)} px (${widthCm.toFixed(2)} cm)<br>
      Hauteur: ${dimensions.height.toFixed(0)} px (${heightCm.toFixed(2)} cm)<br>
      <strong>Surface:</strong><br>
      ${areaPixels.toFixed(0)} px² = ${areaCm2.toFixed(2)} cm²<br>
      <small>(${points.length} points, ${dpi} DPI)</small>
    `;
    console.log("Polygone fermé - Surface:", areaCm2.toFixed(2), "cm²");
  }
}

function showDrawingCanvas() {
  if (canvas) {
    canvas.style.display = "block";
    updateCanvasEvents();
    console.log("Canvas de dessin affiché");
  }
}

function hideDrawingCanvas() {
  if (canvas) {
    canvas.style.display = "none";
    canvas.style.pointerEvents = "none";
    console.log("Canvas de dessin caché");
  }
}

function redraw() {
  if (!ctx || !canvas) {
    console.log("Pas de contexte pour redraw");
    return;
  }

  // Efface tout
  ctx.clearRect(0, 0, canvas.width, canvas.height);
  
  if (points.length === 0) return;

  console.log("Redraw avec", points.length, "points");

  // Style pour les points
  ctx.fillStyle = "red";
  ctx.strokeStyle = "white";
  ctx.lineWidth = 1;

  // Dessine les points
  points.forEach((point, index) => {
    // Cercle rouge
    ctx.beginPath();
    ctx.arc(point.x, point.y, 5, 0, 2 * Math.PI);
    ctx.fill();
    ctx.stroke();
    
    // Numéro du point en blanc
    ctx.fillStyle = "white";
    ctx.font = "bold 14px Arial";
    ctx.textAlign = "center";
    ctx.fillText((index + 1).toString(), point.x, point.y + 4);
    ctx.fillStyle = "red";
  });

  // Dessine les lignes
  if (points.length > 1) {
    ctx.strokeStyle = "red";
    ctx.lineWidth = 3;
    ctx.beginPath();
    ctx.moveTo(points[0].x, points[0].y);
    
    for (let i = 1; i < points.length; i++) {
      ctx.lineTo(points[i].x, points[i].y);
    }
    
    // Ferme le polygone si on a au moins 3 points
    if (points.length >= 3) {
      ctx.lineTo(points[0].x, points[0].y);
    }
    
    ctx.stroke();
  }

  // Calcule et affiche la surface et les dimensions
  if (points.length >= 3) {
    const dpi = getImageDPI();
    
    // Surface en pixels et cm²
    const areaPixels = calculatePolygonArea(points);
    const areaCm2 = pixelsToCm(Math.sqrt(areaPixels), dpi) * pixelsToCm(Math.sqrt(areaPixels), dpi);
    // Conversion plus précise pour la surface
    const areaCm2Precise = areaPixels / (dpi * dpi) * (2.54 * 2.54);
    
    // Dimensions en pixels et cm
    const dimensions = calculatePolygonDimensions(points);
    const widthCm = pixelsToCm(dimensions.width, dpi);
    const heightCm = pixelsToCm(dimensions.height, dpi);
    
    const resultElement = document.getElementById("area-result");
    if (resultElement) {
      resultElement.innerHTML = `
        <strong>Dimensions:</strong><br>
        Largeur: ${dimensions.width.toFixed(0)} px (${widthCm.toFixed(2)} cm)<br>
        Hauteur: ${dimensions.height.toFixed(0)} px (${heightCm.toFixed(2)} cm)<br>
        <strong>Surface:</strong><br>
        ${areaPixels.toFixed(0)} px² = ${areaCm2Precise.toFixed(2)} cm²<br>
        <small>(${points.length} points, ${dpi} DPI)</small>
      `;
    }
  }
}

function calculatePolygonArea(pts) {
  if (pts.length < 3) return 0;
  
  let area = 0;
  const n = pts.length;
  
  for (let i = 0; i < n; i++) {
    const j = (i + 1) % n;
    area += pts[i].x * pts[j].y;
    area -= pts[j].x * pts[i].y;
  }
  
  return Math.abs(area) / 2;
}

// Fonction pour convertir les pixels en centimètres
function pixelsToCm(pixels, dpi = 96) {
  // 1 pouce = 2.54 cm
  return (pixels / dpi) * 2.54;
}

// Fonction pour calculer les dimensions du polygone
function calculatePolygonDimensions(pts) {
  if (pts.length < 2) return { width: 0, height: 0 };
  
  let minX = pts[0].x, maxX = pts[0].x;
  let minY = pts[0].y, maxY = pts[0].y;
  
  for (let i = 1; i < pts.length; i++) {
    if (pts[i].x < minX) minX = pts[i].x;
    if (pts[i].x > maxX) maxX = pts[i].x;
    if (pts[i].y < minY) minY = pts[i].y;
    if (pts[i].y > maxY) maxY = pts[i].y;
  }
  
  return {
    width: maxX - minX,
    height: maxY - minY
  };
}

// Fonction pour obtenir la résolution DPI de l'image (avec fallback)
function getImageDPI() {
  // Essaie de récupérer les métadonnées de l'image si disponibles
  // Sinon utilise une valeur par défaut de 96 DPI (standard écran)
  // Pour des images médicales, souvent 300 DPI
  
  // Vous pouvez ajuster cette valeur selon vos besoins :
  // - 96 DPI : standard écran
  // - 150 DPI : impression standard
  // - 300 DPI : impression haute qualité / images médicales
  
  return 96; // Valeur par défaut, à ajuster selon vos images
}

// Fonction pour réinitialiser le dessin
function clearDrawing() {
  points = [];
  if (ctx && canvas) {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
  }
  const resultElement = document.getElementById("area-result");
  if (resultElement) {
    resultElement.textContent = drawingMode ? "Mode dessin activé - Cliquez pour tracer" : "";
  }
  console.log("Dessin effacé");
}