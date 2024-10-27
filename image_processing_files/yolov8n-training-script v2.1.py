import os
import yaml
import logging
import torch
from ultralytics import YOLO

# Set up logging
logging.basicConfig(level=logging.INFO, format='%(asctime)s %(levelname)s: %(message)s', datefmt='%Y-%m-%d %H:%M:%S')

def verify_directory(path, dir_type):
    if not os.path.isdir(path):
        raise NotADirectoryError(f"The {dir_type} path is not a valid directory: {path}")
    if not os.path.exists(os.path.join(path, 'images')) or not os.path.exists(os.path.join(path, 'labels')):
        raise FileNotFoundError(f"The {dir_type} directory must contain 'images' and 'labels' subdirectories.")

def create_data_yaml(train_path, val_path, nc):
    data = {
        'train': train_path,
        'val': val_path,
        'nc': nc,
        'names': ['f', 'i']
    }
    yaml_path = 'egg_detection_data.yaml'
    with open(yaml_path, 'w') as f:
        yaml.dump(data, f)
    return yaml_path

def train():
    logging.info("Starting training process")

    # Check CUDA availability
    cuda_available = torch.cuda.is_available()
    if cuda_available:
        logging.info(f"CUDA is available. Using GPU: {torch.cuda.get_device_name(0)}")
        device = 'cuda'
    else:
        logging.warning("CUDA is not available. Using CPU. This may significantly slow down training.")
        device = 'cpu'

    try:
        # Get dataset paths from user input
        train_path = input("Enter the directory path for the train dataset: ").strip()
        val_path = input("Enter the directory path for the validation dataset: ").strip()
        
        # Verify directories
        verify_directory(train_path, "train dataset")
        verify_directory(val_path, "validation dataset")
        
        logging.info(f"Train dataset path: {train_path}")
        logging.info(f"Validation dataset path: {val_path}")

        # Create data.yaml file
        data_yaml = create_data_yaml(train_path, val_path, nc=2)
        logging.info(f"Created data YAML file: {data_yaml}")

        # Initialize YOLOv8n model
        model = YOLO('yolov8n.pt')
        logging.info("YOLOv8n model initialized")

        # Train the model
        results = model.train(
            data=data_yaml,
            epochs=300,
            imgsz=640,
            batch=16,
            patience=15,  # Enable built-in early stopping
            lr0=0.01,
            lrf=0.001,
            device=device,
            workers=8,
            project='egg_detection',
            name='yolov8n_run',
            exist_ok=True,
            pretrained=True,
            optimizer='SGD',
            close_mosaic=10,
            amp=True,
            save=True,
            save_period=-1
        )
        logging.info("Training complete!")

        # Save the final model
        model.save('egg_detection_yolov8n_final.pt')
        logging.info("Final model saved as 'egg_detection_yolov8n_final.pt'")

        # Validate the model
        val_results = model.val(data=data_yaml, device=device)
        logging.info(f"Validation results: {val_results}")

    except Exception as e:
        logging.error(f"An error occurred during training: {str(e)}")
        raise

if __name__ == '__main__':
    train()
