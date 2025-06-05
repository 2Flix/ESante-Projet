import os
import matplotlib.pyplot as plt
from skimage import io, color, filters
import numpy as np
import pydicom

# === Paramètres ===
image_path = "image2.dicom"  # Remplace par ton chemin d’image
mode = "soft"  # Options : "soft", "color", "overlay"

# === Fonction : lire une image (jpg, png, dcm) ===
def read_image(path):
    ext = os.path.splitext(path)[-1].lower()
    if ext in ['.jpg', '.jpeg', '.png']:
        image = io.imread(path)
        if image.ndim == 3:
            image = color.rgb2gray(image)
        return image
    elif ext in ['.dcm', '.dicom']:
        ds = pydicom.dcmread(path)
        image = ds.pixel_array.astype(np.float32)
        image -= image.min()
        image /= image.max()
        return image
    else:
        raise ValueError(f"Format non supporté : {ext}")

# === Lecture de l’image ===
image_gray = read_image(image_path)

# === Seuillage d’Otsu ===
threshold = filters.threshold_otsu(image_gray)
binary_mask = image_gray > threshold

# === Affichage selon le mode ===
fig, axs = plt.subplots(1, 2, figsize=(12, 6))

# Image originale
axs[0].imshow(image_gray, cmap='gray')
axs[0].set_title("Image originale (niveaux de gris)")
axs[0].axis('off')

# Image segmentée
if mode == "soft":
    segmented = binary_mask.astype(np.float32)
    segmented[segmented == 1] = 0.85
    segmented[segmented == 0] = 0.15
    axs[1].imshow(segmented, cmap='gray')
    axs[1].set_title("Segmentée (niveaux de gris doux)")

elif mode == "color":
    axs[1].imshow(binary_mask, cmap='plasma')
    axs[1].set_title("Segmentée (en couleur)")

elif mode == "overlay":
    axs[1].imshow(image_gray, cmap='gray')
    axs[1].imshow(binary_mask, cmap='Reds', alpha=0.4)
    axs[1].set_title("Image + Segmentation (overlay)")

else:
    raise ValueError("Mode invalide. Utilise 'soft', 'color' ou 'overlay'.")

axs[1].axis('off')
plt.tight_layout()
plt.show()
