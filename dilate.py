import os
import sys
import matplotlib.pyplot as plt
from skimage import io, color, morphology
import numpy as np
# import pydicom  # inutile ici si tu n'utilises pas de DICOM à ce stade

def read_image(path):
    ext = os.path.splitext(path)[-1].lower()
    image = io.imread(path)
    if image.ndim == 3:
        image = color.rgb2gray(image)
    image = (image * 255).astype(np.uint8)
    return image

if len(sys.argv) < 3:
    print("Usage: python dilate.py input_image output_image")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]

image_gray = read_image(input_path)

# Créer un masque binaire à partir de l’image segmentée
mask = image_gray > 0

# Appliquer la dilatation
dilated_mask = morphology.binary_dilation(mask)

# Appliquer le masque dilaté à l’image segmentée
result = np.zeros_like(image_gray)
result[dilated_mask] = image_gray[dilated_mask]

# Sauvegarde avec échelle de gris correcte
plt.imsave(output_path, result, cmap='gray', vmin=0, vmax=255)
