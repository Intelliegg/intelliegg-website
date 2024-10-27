# PROCESS.md - Egg Fertility Detection Development Process Documentation

## Project Overview

This document outlines the development process for the egg fertility detection system, designed for high-speed, single-column conveyor belt egg processing using AWS services. The system balances speed, cost-efficiency, and accuracy through strategic design choices and optimizations.

## Development Stages

### 1. Data Preparation and Model Training

#### Dataset Preparation
- Collected and organized 179 egg images (139 training, 40 validation) from single-column conveyor belt setups
- Created 'images' and 'labels' subdirectories for both train and validation sets
- Ensured proper labeling of fertile and infertile eggs

#### Model Training
- Chose YOLOv8n for its speed and efficiency in single-object scenarios
- Implemented and trained the model using PyTorch via Ultralytics YOLO
- Achieved high accuracy (mAP50-95: 0.995) on the validation set

### 2. AWS Deployment Design

#### Core Architecture

1. **Edge Devices**:
   - Capture images of single eggs on the conveyor belt
   - Send images to AWS for processing
   - Receive and act on results

2. **API Gateway**:
   - Provides secure API endpoint for edge devices

3. **EC2 Instance**:
   - Processes images using YOLOv8n model
   - Determines egg fertility
   - Implements correction code for multiple detections

4. **S3 Buckets**:
   - Results Storage: For storing processing results and logs
   - Metrics Storage: For storing daily results metrics to be used later

5. **CloudWatch**:
   - Monitors system performance and health

6. **Lambda Function**:
   - Extracts the data daily after the daily production schedule
   - Makes a file with a report of the daily results to upload into the Metrics Storage S3 Bucket
   - Deletes the data in the Results Storage S3 Bucket for cost reduction
   
#### Data Flow
1. Edge device captures image of a single egg on the conveyor belt
2. Image is sent to API Gateway
3. API Gateway inputs to EC2 Instance
4. EC2 Instance processes the image:
   - Performs fertility detection
   - Applies correction code for multiple detections
   - Stores results in S3
   - Returns results to API Gateway
5. Results are sent back to edge device
6. Edge device controls conveyor belt based on results
(after production hours)
8. Lambda function is trigered:
   - Extract Results Storage S3 Bucket data
   - Make a report and dowNloads it to the Metrics Storage S3 Bucket
   - Deletes all the data in the Results Storage S3 Bucket

## Implementation Plan

### Phase 1: AWS Infrastructure Setup (Per Conveyor Belt)
1. Create S3 buckets for results storage and metrics files
2. Set up EC2 instance with YOLOv8n model packaged
3. Configure API Gateway and create necessary endpoints
4. Set up CloudWatch monitoring for all components
5. Create Lambda function for metrics extraction and data deletion

### Phase 2: Edge Device Integration (Per Conveyor Belt)
1. Install and configure edge device for the conveyor belt
2. Develop software for image capture and communication with API Gateway
3. Test image capture and transmission

### Phase 3: System Testing and Optimization (Per Conveyor Belt)
1. Conduct end-to-end testing with the conveyor belt
2. Optimize EC2 instance type based on performance
3. Fine-tune edge device configuration
4. Test and optimize Lambda function for metrics extraction and data deletion

### Phase 4: Production Rollout (For All Conveyor Belts)
1. Train factory personnel on system operation
2. Perform final system checks
3. Start production monitoring and support
4. Continuously monitor and optimize system performance

## Challenges and Solutions

1. **Multiple Detections**
   - Challenge: Occasional detection of a single egg as two objects
   - Solution: Implemented correction code in testing scripts, integrated into EC2 Instance for production

2. **Speed vs. Accuracy Trade-off**
   - Challenge: Balancing processing speed with detection accuracy
   - Solution: Chose YOLOv8n and implemented correction code, optimizing for high-speed conveyor belt operation

3. **Cost Optimization**
   - Challenge: Minimizing AWS service costs for continuous operation
   - Solution: Optimized model and processing pipeline to reduce computation time and resource usage

4. **Real-time Processing**
   - Challenge: Ensuring low-latency processing for high-speed conveyor belts
   - Solution: Custom fit EC2 instance selection for high-speed conveyor belts, implemented efficient correction code
   DISCLAIMER: If you're using any EC2 Instance other than g4dn.xlarge go to https://github.com/DimitriVavoulisPortfolio/aws-computer-vision-industrial-egg-fertility-sorting-system/blob/main/AWS-PLAN-DESIGN-AND-COST-REPORT.md and use the calculations there as a template for adjustment 

5. **Industrial Environment Integration**
   - Challenge: Adapting the system to various lighting conditions and conveyor speeds
   - Solution: Extensive testing and calibration in the actual factory environment

6. **Reliability**
   - Challenge: Ensuring system reliability in a 24/7 operation
   - Solution: Implement robust error handling, failover mechanisms, and continuous monitoring

## Future Enhancements

1. Refine the model and correction code to further reduce multiple detections while maintaining speed
2. Develop a dashboard for real-time monitoring of egg fertility rates and system performance
3. Implement a feedback loop for continuous model improvement using production data
4. Explore edge computing solutions to further reduce latency and cloud dependency
5. Integrate predictive maintenance for conveyor belts based on image data analysis

## Conclusion

This egg fertility detection system is optimized for high-speed, single-column conveyor belt operations, balancing accuracy, speed, and cost-efficiency. The implementation of correction code for multiple detections, along with the choice of a lightweight YOLOv8n model, allows for fast and accurate egg classification while minimizing computational requirements and AWS service costs. This approach demonstrates the system's suitability for real-world industrial applications, providing a robust and efficient solution for egg fertility detection.
