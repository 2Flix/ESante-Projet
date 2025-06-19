function applyChanVeseAutomatic() {
    const defaultIterations = 50;
    console.log("Mode automatique déclenché avec 50 itérations");
    
    // Vérifier si l'image existe déjà avant de traiter
    checkExistingImageBeforeProcessing('chanvese', defaultIterations, () => {
        runChanVeseSegmentation(defaultIterations);
    });
}

function applyChanVeseWithIterations() {
    const input = document.getElementById('chanvese-iterations');
    if (!input) {
        alert("Input d'itérations introuvable !");
        return;
    }

    const iterations = parseInt(input.value);
    if (isNaN(iterations)) {
        alert("Valeur d'itérations invalide !");
        return;
    }

    if (iterations < 10 || iterations > 200) {
        alert("Le nombre d'itérations doit être entre 10 et 200");
        return;
    }

    console.log("Mode manuel déclenché avec", iterations, "itérations");
    
    // Vérifier si l'image existe déjà avant de traiter
    checkExistingImageBeforeProcessing('chanvese', iterations, () => {
        runChanVeseSegmentation(iterations);
    });
}

function checkExistingImageBeforeProcessing(filterType, iterations, processCallback) {
    const img = document.getElementById('selected-image');
    if (!img || !img.src) {
        alert("Image non chargée !");
        return;
    }

    // Générer le nom de fichier attendu
    const filename = `${filterType}_${iterations}iter.png`;
    
    console.log('Vérification de l\'existence de:', filename);
    
    // Vérifier si l'image existe déjà
    const formData = new FormData();
    formData.append('action', 'check_existing_image');
    formData.append('filter_type', filterType);
    formData.append('iterations', iterations);
    formData.append('original_image', img.src);
    
    fetch('/ESANTE2/upload_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.exists) {
            console.log('Image existante trouvée:', data.filename);
            // Charger l'image existante
            loadExistingImage(data.filepath, iterations);
        } else {
            console.log('Image non trouvée, traitement nécessaire');
            // Procéder au traitement
            processCallback();
        }
    })
    .catch(error => {
        console.error('Erreur lors de la vérification:', error);
        // En cas d'erreur, procéder quand même au traitement
        processCallback();
    });
}

function loadExistingImage(filepath, iterations) {
    const img = document.getElementById('selected-image');
    const display = document.getElementById('chanvese-iteration-display');
    
    if (display) {
        display.innerText = `Itération utilisée pour la segmentation Chan-Vese : ${iterations} (image existante)`;
    }
    
    // Charger l'image existante avec un cache buster
    img.src = filepath + '?t=' + Date.now();
    
    console.log('Image existante chargée:', filepath);
    
    // Vérifier si l'image est déjà dans la liste, sinon l'ajouter
    const filename = filepath.split('/').pop();
    addImageToListIfNotExists(filepath, filename);
}

function runChanVeseSegmentation(iterations) {
    const img = document.getElementById('selected-image');
    if (!img || !img.src) {
        alert("Image non chargée !");
        return;
    }

    const display = document.getElementById('chanvese-iteration-display');
    if (display) {
        display.innerText = `Itération utilisée pour la segmentation Chan-Vese : ${iterations}`;
    }

    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = img.naturalWidth;
    canvas.height = img.naturalHeight;
    ctx.drawImage(img, 0, 0);
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;

    const grayData = new Array(canvas.width * canvas.height);
    for (let i = 0; i < data.length; i += 4) {
        const gray = 0.299 * data[i] + 0.587 * data[i + 1] + 0.114 * data[i + 2];
        grayData[i / 4] = gray;
    }

    const segmented = chanVeseSegmentation(grayData, canvas.width, canvas.height, iterations);

    for (let i = 0; i < segmented.length; i++) {
        const pixelIndex = i * 4;
        const value = segmented[i] > 0.5 ? 255 : 0;
        data[pixelIndex] = value;
        data[pixelIndex + 1] = value;
        data[pixelIndex + 2] = value;
        data[pixelIndex + 3] = 255;
    }

    ctx.putImageData(imageData, 0, 0);
    
    // Sauvegarder l'image traitée
    saveProcessedImage(canvas, 'chanvese', iterations);
    
    // Afficher l'image traitée
    img.src = canvas.toDataURL();
}

function saveProcessedImage(canvas, filterType, iterations = null) {
    // Convertir le canvas en data URL
    const dataURL = canvas.toDataURL('image/png');
    
    // Générer un nom de fichier avec les paramètres
    let filename;
    if (iterations) {
        filename = `${filterType}_${iterations}iter.png`;
    } else {
        const timestamp = Date.now();
        filename = `${filterType}_${timestamp}.png`;
    }
    
    console.log('Sauvegarde de l\'image:', filename);
    
    // Envoyer l'image au serveur
    const formData = new FormData();
    formData.append('image_data', dataURL);
    formData.append('filename', filename);
    
    fetch('/ESANTE2/save_image.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Réponse reçue:', response.status);
        return response.text();
    })
    .then(text => {
        console.log('Réponse brute:', text);
        try {
            const data = JSON.parse(text);
            if (data.success) {
                if (data.already_exists) {
                    console.log('Image déjà existante:', data.filename);
                } else {
                    console.log('Image sauvegardée avec succès:', data.filename);
                }
                // Ajouter l'image à la liste si elle n'y est pas déjà
                addImageToListIfNotExists(data.filepath, data.filename);
            } else {
                console.error('Erreur lors de la sauvegarde:', data.error);
                alert('Erreur lors de la sauvegarde: ' + data.error);
            }
        } catch (e) {
            console.error('Erreur de parsing JSON:', e);
            console.error('Réponse reçue:', text);
            alert('Erreur de communication avec le serveur');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur de connexion lors de la sauvegarde');
    });
}

function addImageToList(filepath, filename) {
    const imageList = document.querySelector('.image-list');
    if (imageList) {
        // Créer le nouvel élément image
        const newImageDiv = document.createElement('div');
        newImageDiv.className = 'img-thumb';
        newImageDiv.onclick = function() { showImage(filepath); };
        
        const newImg = document.createElement('img');
        newImg.src = filepath + '?t=' + Date.now(); // Cache buster
        newImg.alt = filename;
        newImg.title = filename; // Tooltip avec le nom du fichier
        
        newImageDiv.appendChild(newImg);
        
        // Ajouter en première position (plus récent en premier)
        imageList.insertBefore(newImageDiv, imageList.firstChild);
        
        console.log('Image ajoutée à la liste:', filename);
    } else {
        console.error('Liste d\'images non trouvée');
    }
}

function addImageToListIfNotExists(filepath, filename) {
    const imageList = document.querySelector('.image-list');
    if (imageList) {
        // Vérifier si l'image existe déjà dans la liste
        const existingImages = imageList.querySelectorAll('img');
        let imageExists = false;
        
        existingImages.forEach(img => {
            if (img.alt === filename || img.src.includes(filename)) {
                imageExists = true;
            }
        });
        
        if (!imageExists) {
            addImageToList(filepath, filename);
        } else {
            console.log('Image déjà présente dans la liste:', filename);
        }
    }
}

function refreshImageList() {
    // Recharger la page pour mettre à jour la liste complète
    // Alternative plus douce: recharger seulement la sidebar
    const sidebar = document.querySelector('.sidebar-left');
    if (sidebar) {
        fetch(window.location.href)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newImageList = doc.querySelector('.image-list');
                if (newImageList) {
                    const currentImageList = document.querySelector('.image-list');
                    if (currentImageList) {
                        currentImageList.innerHTML = newImageList.innerHTML;
                    }
                }
            })
            .catch(error => {
                console.error('Erreur lors du rechargement des images:', error);
            });
    }
}

function chanVeseSegmentation(grayData, width, height, iterations = 50) {
    const n = width * height;
    let phi = new Array(n);

    // Initialisation avec un cercle au centre
    const centerX = width / 2;
    const centerY = height / 2;
    const radius = Math.min(width, height) / 4;

    for (let y = 0; y < height; y++) {
        for (let x = 0; x < width; x++) {
            const idx = y * width + x;
            const dist = Math.sqrt((x - centerX) ** 2 + (y - centerY) ** 2);
            phi[idx] = radius - dist;
        }
    }

    // Paramètres de l'algorithme
    const dt = 0.1;
    const mu = 0.2;
    const lambda1 = 1.0;
    const lambda2 = 1.0;

    // Boucle principale de segmentation
    for (let iter = 0; iter < iterations; iter++) {
        // Calculer les moyennes c1 et c2
        let sum1 = 0, sum2 = 0, count1 = 0, count2 = 0;

        for (let i = 0; i < n; i++) {
            if (phi[i] >= 0) {
                sum1 += grayData[i];
                count1++;
            } else {
                sum2 += grayData[i];
                count2++;
            }
        }

        const c1 = count1 > 0 ? sum1 / count1 : 0;
        const c2 = count2 > 0 ? sum2 / count2 : 0;

        const newPhi = new Array(n);

        // Mise à jour de la fonction level set
        for (let y = 1; y < height - 1; y++) {
            for (let x = 1; x < width - 1; x++) {
                const idx = y * width + x;
                
                // Gradients
                const dx = (phi[idx + 1] - phi[idx - 1]) / 2;
                const dy = (phi[idx + width] - phi[idx - width]) / 2;
                
                // Dérivées secondes
                const dxx = phi[idx + 1] - 2 * phi[idx] + phi[idx - 1];
                const dyy = phi[idx + width] - 2 * phi[idx] + phi[idx - width];

                const gradMag = Math.sqrt(dx * dx + dy * dy);
                const curvature = gradMag > 1e-8 ? (dxx + dyy) / gradMag : 0;

                // Force de segmentation
                const force = -lambda1 * (grayData[idx] - c1) ** 2 +
                              lambda2 * (grayData[idx] - c2) ** 2 +
                              mu * curvature;

                newPhi[idx] = phi[idx] + dt * force;
            }
        }

        // Conditions aux limites
        for (let x = 0; x < width; x++) {
            newPhi[x] = newPhi[width + x];
            newPhi[(height - 1) * width + x] = newPhi[(height - 2) * width + x];
        }
        for (let y = 0; y < height; y++) {
            newPhi[y * width] = newPhi[y * width + 1];
            newPhi[y * width + width - 1] = newPhi[y * width + width - 2];
        }

        phi = newPhi;
    }

    // Conversion finale en masque binaire
    const result = new Array(n);
    for (let i = 0; i < n; i++) {
        result[i] = phi[i] >= 0 ? 1 : 0;
    }

    return result;
}