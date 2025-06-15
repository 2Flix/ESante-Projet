import sys
import cv2
import numpy as np

# Verifie que le nombre d'arguments passes est bien 4 (nom du script + 3 arguments)
if len(sys.argv) != 4:
    print("Usage: laplacien.py input_path output_path strength")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]
strength = float(sys.argv[3])

image = cv2.imread(input_path, cv2.IMREAD_GRAYSCALE)
if image is None:
    print(f"Erreur lecture image : {input_path}")
    sys.exit(1)

# Applique le filtre Laplacien sur l'image, qui detecte les contours
laplacian = cv2.Laplacian(image, cv2.CV_64F)

# Multiplie le resultat par la force specifiee
laplacian *= strength

# Convertit le resultat en valeurs absolues 8 bits, pour pouvoir l'enregistrer comme image
laplacian = cv2.convertScaleAbs(laplacian)

# Enregistre l'image 
cv2.imwrite(output_path, laplacian)
