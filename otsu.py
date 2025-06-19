import math
import numpy as np
from PIL import Image
import sys

threshold_values = {}
h = [1]

def Hist(img):
    row, col = img.shape 
    y = np.zeros(256)
    for i in range(row):
        for j in range(col):
            y[img[i, j]] += 1
    return y

def regenerate_img(img, threshold):
    row, col = img.shape 
    y = np.zeros((row, col))
    for i in range(row):
        for j in range(col):
            y[i, j] = 255 if img[i, j] >= threshold else 0
    return y

def countPixel(h):
    return np.sum(h[h > 0])

def weight(s, e):
    return np.sum(h[s:e])

def mean(s, e):
    w = weight(s, e)
    return np.sum([h[i] * i for i in range(s, e)]) / float(w) if w != 0 else 0

def variance(s, e):
    m = mean(s, e)
    w = weight(s, e)
    return np.sum([(i - m)**2 * h[i] for i in range(s, e)]) / w if w != 0 else 0

def threshold_calc(h):
    cnt = countPixel(h)
    for i in range(1, len(h)):
        vb = variance(0, i)
        wb = weight(0, i) / float(cnt)
        mb = mean(0, i)

        vf = variance(i, len(h))
        wf = weight(i, len(h)) / float(cnt)
        mf = mean(i, len(h))

        V2w = wb * vb + wf * vf

        if not math.isnan(V2w):
            threshold_values[i] = V2w

def get_optimal_threshold():
    min_V2w = min(threshold_values.values())
    optimal_threshold = [k for k, v in threshold_values.items() if v == min_V2w]
    return optimal_threshold[0]

# === MAIN ===
if len(sys.argv) < 3:
    print("Usage: python otsu.py <input_path> <output_path> [manual_threshold]")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]
manual_threshold = None

# Vérifier si un seuil manuel est fourni
if len(sys.argv) > 3:
    try:
        manual_threshold = int(sys.argv[3])
        if manual_threshold < 0 or manual_threshold > 255:
            raise ValueError("Le seuil doit être entre 0 et 255")
    except ValueError as e:
        print(f"Erreur: {e}", file=sys.stderr)
        sys.exit(1)

try:
    image = Image.open(input_path).convert("L")
    img = np.asarray(image)
except Exception as e:
    print(f"Erreur lors de l'ouverture de l'image: {e}", file=sys.stderr)
    sys.exit(1)

if manual_threshold is not None:
    # Utiliser le seuil manuel
    threshold_used = manual_threshold
    print(f"{threshold_used} (Otsu manuel)")
else:
    # Calculer le seuil Otsu
    h = Hist(img)
    threshold_calc(h)
    threshold_used = get_optimal_threshold()
    print(f"{threshold_used} (Otsu automatique)")

try:
    res = regenerate_img(img, threshold_used)
    Image.fromarray(res.astype(np.uint8)).save(output_path)
except Exception as e:
    print(f"Erreur lors de la sauvegarde: {e}", file=sys.stderr)
    sys.exit(1)