# MODEL.md - Egg Fertility Detection Model Documentation

## Model Architecture

The egg fertility detection model is based on YOLOv8n, optimized for high-speed, single-column conveyor belt egg processing.

- Base model: YOLOv8n
- Task: Object Detection (Binary Classification: Fertile/Infertile)
- Output: 2 classes (Fertile 'f', Infertile 'i')

## Training Process

### Dataset
- Total samples: 179 images (139 training, 40 validation)
- Features: Preprocessed egg images from single-column conveyor belt setup
- Classes: 2 (Fertile, Infertile)

### Training Configuration
- Framework: PyTorch (via Ultralytics YOLO implementation)
- Image size: 640x640
- Batch size: 16
- Maximum epochs: 300
- Early stopping patience: 15
- Learning rate strategy:
  - Initial learning rate (lr0): 0.01
  - Final learning rate factor (lrf): 0.001
- Optimizer: SGD (Stochastic Gradient Descent)
- Data augmentation:
  - Close mosaic: 10
  - Auto augment: randaugment
- Mixed precision training: Enabled (amp=True)

## Model Performance

### Test Dataset
- Number of test images: 20
- Test environment: Single-column conveyor belt setup

### Test Results
Based on the evaluation of the 20-image test dataset:

- Accuracy: 1.00 (100%)
- Precision: 1.00 (100%)
- Recall: 1.00 (100%)
- F1-score: 1.00 (100%)

These metrics indicate perfect performance on the test dataset, suggesting that the model has learned to classify egg fertility with high accuracy in the specific conveyor belt environment.

### Speed Performance
- Preprocess: 3.0ms per image
- Inference: 10.0ms per image
- Postprocess: 2.0ms per image
- Total processing time: 15.0ms per image

The model demonstrates fast processing times, suitable for high-speed conveyor belt operations.

## Multiple Detection Handling

The model occasionally detects a single egg as two objects. This is a known trade-off for achieving higher processing speed and lower computational costs. Important points:

1. The conveyor belt processes eggs in a single column, ensuring only one egg is present in each image.
2. Correction code is implemented in the testing scripts to handle multiple detections:
   - The highest confidence detection is used for classification.
   - Multiple detections are merged or filtered out.
3. The system maintains high accuracy in egg type classification (fertile vs. infertile) despite occasional multiple detections.

This approach allows for:
- Faster processing speeds
- Lower computational requirements
- Reduced AWS service costs
- Maintenance of high classification accuracy

## Future Work

1. Expand the test dataset to include a larger and more diverse set of egg images
2. Fine-tune the model to further reduce multiple detections while maintaining speed
3. Conduct long-term performance monitoring in production environment
4. Explore potential for model quantization to further optimize inference speed
5. Investigate adaptive thresholding techniques to improve robustness across varying lighting conditions
