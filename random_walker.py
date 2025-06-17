import os
import sys
import numpy as np
import matplotlib.pyplot as plt
from skimage.segmentation import random_walker
from skimage.exposure import rescale_intensity
from skimage.io import imread
from skimage.util import img_as_float
import pydicom

# Fonction pour lire l'image (prise en charge PNG/JPEG/DICOM)
def read_image(path):
    ext = os.path.splitext(path)[-1].lower()
    if ext in ['.jpg', '.jpeg', '.png']:
        image = imread(path, as_gray=True)
    elif ext in ['.dcm', '.dicom']:
        ds = pydicom.dcmread(path)
        image = ds.pixel_array.astype(np.float32)
        image -= image.min()
        image /= image.max()
    else:
        raise ValueError(f"Format non supporté : {ext}")
    return img_as_float(image)

# Vérification des arguments
if len(sys.argv) != 4:
    print("Usage: python random_walker.py input_image output_image threshold")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]
try:
    threshold = int(sys.argv[3])
except ValueError:
    print("Le seuil doit être un entier entre 0 et 255.")
    sys.exit(1)

# Lecture de l'image
image = read_image(input_path)

# Ajout de bruit (comme dans l'exemple)
rng = np.random.default_rng()
sigma = 0.35
noisy_image = image + rng.normal(loc=0, scale=sigma, size=image.shape)
noisy_image = rescale_intensity(noisy_image, in_range=(-sigma, 1 + sigma), out_range=(-1, 1))

# Création des marqueurs
markers = np.zeros(noisy_image.shape, dtype=np.uint8)
markers[noisy_image < -0.95] = 1
markers[noisy_image > 0.95] = 2

# Application de l’algorithme Random Walker
labels = random_walker(noisy_image, markers, beta=10, mode='cg')

# Sauvegarde uniquement de l’image segmentée
plt.imsave(output_path, labels, cmap='gray')

# Impression du seuil pour PHP
print(threshold)
