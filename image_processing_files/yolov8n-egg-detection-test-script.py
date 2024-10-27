import os
from ultralytics import YOLO
from collections import Counter

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
        class_names = results[0].names
        class_counts = {}
        for result in results:
            for box in result.boxes:
                class_id = int(box.cls)
                class_name = class_names[class_id]
                if class_name not in class_counts:
                    class_counts[class_name] = 1
                else:
                    class_counts[class_name] += 1

        simplified_output = []
        for class_name, count in class_counts.items():
            if count >= 1:
                simplified_output.append(class_name[0])

        return ', '.join(simplified_output)

    except Exception as e:
        print(f"Error predicting image {image_path}: {str(e)}")
        return None

def read_test_predictions(csv_path):
    predictions = {}
    with open(csv_path, 'r') as csv_file:
        next(csv_file)  # Skip header row
        for line in csv_file:
            filename, label = line.strip().split(',')
            predictions[filename] = label
    return predictions

def evaluate_model(model, image_dir, test_predictions):
    true_positives = 0
    false_positives = 0
    false_negatives = 0

    for filename in os.listdir(image_dir):
        if filename.lower().endswith(('.png', '.jpg', '.jpeg')):
            image_path = os.path.join(image_dir, filename)
            predicted_output = predict_image(model, image_path)
            if predicted_output is not None:
                ground_truth = test_predictions[filename]
                print(f"{image_path}: {predicted_output}")
                if ground_truth == predicted_output:
                    true_positives += 1
                else:
                    false_positives += 1
                    false_negatives += 1

    total_predictions = true_positives + false_positives + false_negatives
    accuracy = true_positives / total_predictions if total_predictions > 0 else 0
    precision = true_positives / (true_positives + false_positives) if (true_positives + false_positives) > 0 else 0
    recall = true_positives / (true_positives + false_negatives) if (true_positives + false_negatives) > 0 else 0
    f1_score = 2 * (precision * recall) / (precision + recall) if (precision + recall) > 0 else 0

    print(f"\nEvaluation Results:")
    print(f"Accuracy: {accuracy:.2f}")
    print(f"Precision: {precision:.2f}")
    print(f"Recall: {recall:.2f}")
    print(f"F1-score: {f1_score:.2f}")

def main():
    model_path = "C:/xampp/htdocs/Thesis-IntelliEgg/image_processing_files/MODEL.md"
    image_dir = "C:/xampp/htdocs/Thesis-IntelliEgg/image_processing_files/archive-eggs/test/images/"
    csv_path = "C:/xampp/htdocs/Thesis-IntelliEgg/image_processing_files/archive-eggs/test/labels/test_predictions.csv"

    model = load_model(model_path)
    if model is None:
        return

    test_predictions = read_test_predictions(csv_path)
    evaluate_model(model, image_dir, test_predictions)

if __name__ == "__main__":
    main()