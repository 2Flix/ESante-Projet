import sys
import cv2
import numpy as np

if len(sys.argv) != 4:
    print("Usage: convolution.py input_path output_path strength")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]
strength = float(sys.argv[3])

image = cv2.imread(input_path)
if image is None:
    print("Could not read image")
    sys.exit(1)

# Créer le noyau de convolution personnalisé basé sur la force
kernel = np.array([
    [0, -1, 0],
    [-1, 4 + strength, -1],
    [0, -1, 0]
], dtype=np.float32)

# Appliquer la convolution
sharpened = cv2.filter2D(image, -1, kernel)

# Sauvegarder l'image
cv2.imwrite(output_path, sharpened)
