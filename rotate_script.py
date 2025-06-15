import sys
from scipy import ndimage
import imageio.v3 as iio
import numpy as np
import os
import datetime

def rotate_image(image_path, angle=90):
    # Lire l'image
    img_array = iio.imread(image_path)
    angle_for_scipy = angle

    rotated = ndimage.rotate(img_array, angle_for_scipy, reshape=True)

    # Obtenir le repertoire, le nom de base du fichier et son extension
    directory, filename = os.path.split(image_path)
    base_name, extension = os.path.splitext(filename)

    # Generer un timestamp unique
    timestamp = datetime.datetime.now().strftime("_%Y%m%d_%H%M%S")

    # Construire le nouveau nom de fichier
    new_filename = f"{base_name}{timestamp}{extension}"
    new_image_path = os.path.join(directory, new_filename)

    # Enregistrer la nouvelle image rotate
    iio.imwrite(new_image_path, rotated)

    # Retourner le chemin de la nouvelle image
    print(new_image_path) # Afficher sur stdout pour que PHP puisse le recuperer

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Usage: python rotate_script.py <image_path> [angle]", file=sys.stderr)
        sys.exit(1)

    image_path = sys.argv[1]
    # L'angle est maintenant toujours passé en argument 2
    angle = float(sys.argv[2]) if len(sys.argv) > 2 else 90 # 90 par defaut si non fourni

    if os.path.exists(image_path):
        rotate_image(image_path, angle)
    else:
        print(f"Image non trouvée : {image_path}", file=sys.stderr)
        sys.exit(1)
