import os
import sys
import matplotlib.pyplot as plt
from skimage import io, color, filters, morphology
import numpy as np
import pydicom

def read_image(path):
    ext = os.path.splitext(path)[-1].lower()
    if ext in ['.jpg', '.jpeg', '.png']:
        image = io.imread(path)
        if image.ndim == 3:
            image = color.rgb2gray(image)
    elif ext in ['.dcm', '.dicom']:
        ds = pydicom.dcmread(path)
        image = ds.pixel_array.astype(np.float32)
        image -= image.min()
        image /= image.max()
    else:
        raise ValueError(f"Format non supporté : {ext}")
    
    # Convertir en niveau de gris sur 0-255
    image = (image * 255).astype(np.uint8)
    return image

# Vérification des arguments
if len(sys.argv) < 3:
    print("Usage: python segmentation1.py input_image output_image [threshold]")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]
threshold_arg = None

if len(sys.argv) == 4:
    try:
        threshold_arg = int(sys.argv[3])  # seuil entre 0 et 255
    except ValueError:
        print("Le seuil doit être un entier entre 0 et 255.")
        sys.exit(1)

# Lire l'image
image_gray = read_image(input_path)

# Calcul du seuil
if threshold_arg is None:
    threshold = filters.threshold_otsu(image_gray)
else:
    threshold = threshold_arg

# ✅ Garde les zones sombres (poumons)
binary_mask = image_gray < threshold

# Nettoyage du masque
cleaned_mask = morphology.remove_small_objects(binary_mask, min_size=500)
cleaned_mask = morphology.binary_closing(cleaned_mask)

# Appliquer le masque
segmented_image = np.zeros_like(image_gray)
segmented_image[cleaned_mask] = image_gray[cleaned_mask]

# Sauvegarde
plt.imsave(output_path, segmented_image, cmap='gray')

# Affiche le seuil utilisé pour PHP
print(threshold)