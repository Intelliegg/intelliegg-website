import os
from ultralytics import YOLO

def load_model(model_path):
    try:
        model = YOLO(model_path)
        print(f"Model loaded successfully from {model_path}")
        return model
    except Exception as e:
        print(f"Error loading model: {str(e)}")
        return None

def predict_image(model, image_path):
    try:
        results = model(image_path)
        if len(results[0].boxes) > 0:
            predictions = [results[0].probs.argmax() == 0 for result in results[0].boxes]
            if all(predictions):
                return 'f'
            elif not any(predictions):
                return 'i'
            else:
                return 'u'  # return 'u' for 'unknown' if the predictions are mixed
        else:
            return 'u'  # return 'u' for 'unknown' if no eggs are detected
    except Exception as e:
        print(f"Error predicting image {image_path}: {str(e)}")
        return 'u'
    
def main():
    model_path = input("Enter the path to the trained model: ").strip()
    model = load_model(model_path)
    
    if model is None:
        return

    while True:
        image_path = input("\nEnter the path to the egg image file (or 'q' to quit): ").strip()
        
        if image_path.lower() == 'q':
            break

        if not os.path.exists(image_path):
            print(f"Error: File {image_path} does not exist.")
            continue

        prediction = predict_image(model, image_path)
        
        if prediction is not None:
            print(f"Prediction: {prediction}")

    print("Thank you for using the egg fertilization prediction tool.")

if __name__ == "__main__":
    main()