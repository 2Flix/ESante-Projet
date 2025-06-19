function inverserCouleurs() {
  const img = document.querySelector('#zoom-container img');
  if (!img) return;

  const src = img.src;
  const filename = src.split('/').pop();
  
  // Si déjà inversée, revenir à l'originale
  if (filename.includes('_inverted')) {
    const originalName = filename.replace('_inverted', '');
    const originalImg = document.querySelector(`img[src*="${originalName}"]`);
    if (originalImg) {
      showImage(`uploads/${originalName}`);
      return;
    }
  }
  
  // Chercher si l'image inversée existe déjà
  const nameWithoutExt = filename.substring(0, filename.lastIndexOf('.'));
  const ext = filename.substring(filename.lastIndexOf('.'));
  const invertedName = `${nameWithoutExt}_inverted${ext}`;
  
  const existingInverted = document.querySelector(`img[src*="${invertedName}"]`);
  if (existingInverted) {
    showImage(`uploads/${invertedName}`);
    return;
  }

  // Créer l'image inversée
  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');
  
  canvas.width = img.naturalWidth;
  canvas.height = img.naturalHeight;
  ctx.drawImage(img, 0, 0);
  
  const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
  const data = imageData.data;
  
  // Inverser les couleurs
  for (let i = 0; i < data.length; i += 4) {
    data[i] = 255 - data[i];
    data[i + 1] = 255 - data[i + 1];
    data[i + 2] = 255 - data[i + 2];
  }
  
  ctx.putImageData(imageData, 0, 0);
  
  canvas.toBlob((blob) => {
    const formData = new FormData();
    formData.append('file', blob, invertedName);
    formData.append('action', 'save_processed_image');
    
    fetch('upload_handler.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        addImageToSidebar(data.filepath, invertedName);
        showImage(data.filepath);
      }
    });
  }, 'image/png');
}

function addImageToSidebar(filepath, filename) {
  const imageList = document.querySelector('.image-list');
  if (imageList) {
    const thumb = document.createElement('div');
    thumb.className = 'img-thumb';
    thumb.onclick = () => showImage(filepath);
    
    const img = document.createElement('img');
    img.src = filepath;
    img.alt = '';
    
    thumb.appendChild(img);
    imageList.appendChild(thumb);
  }
}