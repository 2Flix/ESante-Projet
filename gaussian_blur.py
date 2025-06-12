import sys
import cv2

if len(sys.argv) != 4:
    print("Usage: gaussian_blur.py input_path output_path sigma")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]
sigma = float(sys.argv[3])

# Lire l'image
image = cv2.imread(input_path)
if image is None:
    print("Could not read image")
    sys.exit(1)

# Taille du kernel : calculée à partir de sigma (doit être impair et > 0)
k = int(6 * sigma + 1)
if k % 2 == 0:
    k += 1

# Appliquer le flou Gaussien
blurred = cv2.GaussianBlur(image, (k, k), sigma)

# Sauvegarder le résultat
cv2.imwrite(output_path, blurred)
