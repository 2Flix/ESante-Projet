import sys
import cv2
import numpy as np

if len(sys.argv) != 5:
    print("Usage: combined.py input_path output_path sigma strength")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]
sigma = float(sys.argv[3])
strength = float(sys.argv[4])

image = cv2.imread(input_path)
if image is None:
    print("Could not read image")
    sys.exit(1)

# Étape 1 : Appliquer un flou gaussien
blurred = cv2.GaussianBlur(image, (0, 0), sigmaX=sigma, sigmaY=sigma)

# Étape 2 : Appliquer un filtre de netteté (convolution)
kernel = np.array([
    [0, -1, 0],
    [-1, 4 + strength, -1],
    [0, -1, 0]
], dtype=np.float32)

sharpened = cv2.filter2D(blurred, -1, kernel)

cv2.imwrite(output_path, sharpened)

