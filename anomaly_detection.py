import cv2
import sys

def detect_dark_spots(image_path, output_path, threshold=50):
    img = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
    if img is None:
        return False
    blur = cv2.GaussianBlur(img, (5, 5), 0)
    _, thresh = cv2.threshold(blur, threshold, 255, cv2.THRESH_BINARY_INV)
    contours, _ = cv2.findContours(thresh, cv2.RETR_EXTERNAL, cv2.CHAIN_APPROX_SIMPLE)
    result = cv2.cvtColor(img, cv2.COLOR_GRAY2BGR)
    cv2.drawContours(result, contours, -1, (0, 0, 255), 2)
    cv2.imwrite(output_path, result)
    return True

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print("Usage: python anomaly_detection.py input.png output.png")
    else:
        detect_dark_spots(sys.argv[1], sys.argv[2])
