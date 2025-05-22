import pydicom
import cv2
import numpy as np
import sys

def convert_dicom_to_png(dicom_path, output_path):
    ds = pydicom.dcmread(dicom_path)
    pixel_array = ds.pixel_array.astype(float)
    normalized = (np.maximum(pixel_array, 0) / pixel_array.max()) * 255.0
    image = np.uint8(normalized)
    cv2.imwrite(output_path, image)

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python convert_dicom.py input.dcm output.png")
    else:
        convert_dicom_to_png(sys.argv[1], sys.argv[2])
