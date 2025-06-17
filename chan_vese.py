import os
import sys
import matplotlib.pyplot as plt
from skimage import io, color, morphology
from skimage.segmentation import chan_vese
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
    return image

# Argument check
if len(sys.argv) < 3:
    print("Usage: python chan_vese.py input_path output_path [threshold]")
    sys.exit(1)

input_path = sys.argv[1]
output_path = sys.argv[2]

if len(sys.argv) == 4:
    try:
        threshold = int(sys.argv[3])
    except ValueError:
        print("Le seuil doit être un entier.")
        sys.exit(1)
else:
    # seuil automatique : Otsu par exemple
    from skimage.filters import threshold_otsu
    image_temp = read_image(input_path)
    threshold = int(threshold_otsu(image_temp * 255))

# Load and normalize
image_gray = read_image(input_path)
image_norm = (image_gray * 255).astype(np.uint8)
initial_mask = image_norm < threshold

# Chan-Vese segmentation
cv_result = chan_vese(image_gray, mu=0.25, lambda1=1, lambda2=1,
                      tol=1e-3, max_num_iter=200, dt=0.5,
                      init_level_set=initial_mask)

# Post-traitement
cleaned = morphology.remove_small_objects(cv_result, min_size=500)
cleaned = morphology.binary_closing(cleaned)

# Final image
segmented = np.zeros_like(image_gray)
segmented[cleaned] = image_gray[cleaned]

# Save output
plt.imsave(output_path, segmented, cmap='gray')

# Return threshold for PHP
print(threshold)
