import sys
import cv2
import os
import datetime

def apply_median_filter(image_path, kernel_size):
    if not os.path.isfile(image_path):
        print(f"Erreur : image non trouvée : {image_path}", file=sys.stderr)
        sys.exit(1)

    directory, filename = os.path.split(image_path)
    base_name, extension = os.path.splitext(filename)
    
    # Générer le nom du fichier de sortie avec la taille du noyau
    new_filename = f"{base_name}_median_k{kernel_size}{extension}"
    new_image_path = os.path.join(directory, new_filename)
    
    # Vérifier si le fichier existe déjà
    if os.path.exists(new_image_path):
        print(f"Le fichier {new_image_path} existe déjà, réutilisation du cache.", file=sys.stderr)
        print(new_image_path)
        return

    # Si le fichier n'existe pas, le créer
    image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
    if image is None:
        print("Erreur : impossible de charger l'image.", file=sys.stderr)
        sys.exit(1)

    print(f"Application du filtre médian (noyau {kernel_size}) sur {filename}...", file=sys.stderr)
    filtered = cv2.medianBlur(image, kernel_size)

    cv2.imwrite(new_image_path, filtered)
    print(f"Nouvelle image créée : {new_filename}", file=sys.stderr)
    print(new_image_path)

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage : python median_filter.py <image_path> <kernel_size>", file=sys.stderr)
        sys.exit(1)

    image_path = sys.argv[1]
    kernel_size = int(sys.argv[2])

    if kernel_size < 3 or kernel_size % 2 == 0:
        print("Erreur : le noyau doit être impair et ≥ 3.", file=sys.stderr)
        sys.exit(1)

    apply_median_filter(image_path, kernel_size)