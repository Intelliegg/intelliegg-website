# AWS Design, Planning, and Cost Report - Egg Fertility Detection System

## 1. Architecture Overview

### 1.1 Architecture Diagram

```mermaid
graph LR
    subgraph "Standardized Architecture per Conveyor Belt"
    CB[Conveyor Belt] -- Captures images --> ED[Edge Device]
    ED -- Sends images --> APIG[API Gateway]
    APIG -- Routes requests --> EC2[EC2 Instance<br>YOLOv8n Model]
    EC2 -- Processes images --> EC2
    EC2 -- Stores results --> S3R[S3 Results Storage]
    EC2 -- Returns results --> APIG
    APIG -- Sends responses --> ED
    ED -- Controls --> CB
    CW[CloudWatch] -- Monitors --> EC2
    CW -- Monitors --> APIG
    CW -- Monitors --> S3R
    S3R -- Extracts metrics --> LF[Lambda Function]
    LF -- Stores metrics file --> S3MF[S3 Metrics Files]
    LF -- Deletes data --> S3R
    end
    classDef aws fill:#FF9900,stroke:#232F3E,stroke-width:2px,color:#232F3E;
    class APIG,EC2,S3R,CW,LF,S3MF aws;
    classDef factory fill:#4CAF50,stroke:#45A049,stroke-width:2px,color:#fff;
    class CB,ED factory;
```

### 1.2 Components

1. **Edge Device**
   - One per conveyor belt
   - Captures images of eggs on the conveyor belt
   - Sends images to AWS for processing
   - Receives and acts on results

2. **API Gateway**
   - Provides secure API endpoint for the edge device

3. **EC2 Instance**
   - Runs the YOLOv8n model for egg fertility detection
   - Processes images from the conveyor belt
   - Stores results temporarily in S3

4. **S3 Buckets**
   - Results Storage: For temporary storage of processing results and logs
   - Metrics Files: For storing daily metrics files before data deletion

5. **CloudWatch**
   - Monitors system performance and health for the conveyor belt

6. **Lambda Function**
   - Extracts daily metrics from the Results Storage
   - Stores the metrics in a file in the Metrics Files S3 bucket
   - Deletes the raw data from the Results Storage after metrics extraction

### 1.3 Data Flow

1. Edge device captures images of eggs on the conveyor belt
2. Images are sent to API Gateway
3. API Gateway routes requests to the EC2 instance
4. EC2 instance processes the images:
   - Runs YOLOv8n model (packaged with the instance)
   - Performs fertility detection
   - Stores results temporarily in S3
   - Returns results to API Gateway
5. Results are sent back to the edge device
6. Edge device controls the conveyor belt based on results
7. Lambda function runs daily:
   - Extracts metrics from the Results Storage
   - Stores metrics in a file in the Metrics Files S3 bucket
   - Deletes raw data from the Results Storage

### 1.4 Factory Setup

The factory has the following setup:

- 5 Conveyor Belts
- Each conveyor belt operates 16 hours per day, 6 days per week
- The standardized architecture will be replicated for each of the 5 conveyor belts

This setup will be used as the basis for the cost estimation and scaling considerations.

## 2. Implementation Plan

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

## 3. Cost Estimation

### 3.1 Usage-Based Cost Breakdown (Per Conveyor Belt)

#### EC2 Instance (g4dn.xlarge)
- On-Demand: $0.526 per hour
- 1-year Reserved Instance (No Upfront): $0.332 per hour
- 3-year Reserved Instance (All Upfront): $0.196 per hour

#### API Gateway
- Cost per 1 million API calls: $3.50

#### S3
- Storage: $0.023 per GB per month
- PUT/COPY/POST/LIST requests: $0.005 per 1,000 requests
- GET/SELECT requests: $0.0004 per 1,000 requests

#### Lambda
- First 1 million requests per month are free
- $0.20 per 1 million requests thereafter
- 400,000 GB-seconds of compute time per month free
- $0.0000166667 for every GB-second used thereafter

### 3.2 Factory Scenario

The factory setup is as follows:

- Number of conveyor belts: 5
- Operating hours: 16 hours per day, 6 days per week
- Conveyor belt speed: 50,000 eggs per hour
- Weekly production per belt: 16 * 6 * 50,000 = 4,800,000 eggs
- Monthly production per belt (4 weeks): 19,200,000 eggs
- Total monthly production: 5 * 19,200,000 = 96,000,000 eggs

#### Cost Calculation (Per Conveyor Belt, Per Month)

1. **EC2 Instance**
   - Hours: 16 * 6 * 4 = 384 hours per month
   - On-Demand Cost: 384 * $0.526 = $201.98
   - 1-year RI Cost: 384 * $0.332 = $127.49
   - 3-year RI Cost: 384 * $0.196 = $75.26

2. **API Gateway**
   - API calls: 19,200,000
   - Cost: 19.2 * $3.50 = $67.20

3. **S3 Storage** (with automatic deletion after 24 hours)
   - Assuming 5 KB per egg for results and logs
   - Daily storage: (19,200,000 / 30) * 5 KB = 3.2 GB
   - Storage cost: 3.2 GB * $0.023 = $0.07

4. **S3 Requests**
   - PUT requests (1 per egg): 19,200 * $0.005 = $0.10
   - GET requests (assuming 10% retrieval): 1,920 * $0.0004 = $0.00077

5. **Lambda**
   - Assumed 1 minute runtime per day
   - Monthly compute time: 1 * 30 = 30 minutes = 1,800 seconds
   - Monthly compute cost: 1,800 * $0.0000166667 = $0.03

#### Total Monthly Cost Per Conveyor Belt
- Using On-Demand EC2: $201.98 + $67.20 + $0.07 + $0.10 + $0.00077 + $0.03 ≈ $269.38
- Using 1-year RI EC2: $127.49 + $67.20 + $0.07 + $0.10 + $0.00077 + $0.03 ≈ $194.89
- Using 3-year RI EC2: $75.26 + $67.20 + $0.07 + $0.10 + $0.00077 + $0.03 ≈ $142.66

#### Total Monthly Cost For Factory (5 Conveyor Belts)
- Using On-Demand EC2: 5 * $269.38 ≈ $1,346.90
- Using 1-year RI EC2: 5 * $194.89 ≈ $974.45
- Using 3-year RI EC2: 5 * $142.66 ≈ $713.30

### 3.3 Additional Considerations

1. **Data Transfer Costs**: This estimation assumes minimal data transfer out of AWS. If significant data needs to be transferred out of AWS, additional costs may apply ($0.09 per GB for the first 10 TB).

2. **CloudWatch Costs**: Basic monitoring is included free. If detailed monitoring is required, additional costs of $2.10 per instance per month would apply.

3. **Model Updates**: With the model packaged in the EC2 instances, updating the model requires updating the instance AMI. This can be done through a rolling update to minimize downtime.

4. **Edge Computing**: This estimation assumes all processing occurs in AWS. Implementing edge computing could potentially reduce AWS costs but would increase on-premises infrastructure costs.

### 3.4 Cost Optimization Strategies

1. Use Reserved Instances for EC2 to significantly reduce compute costs.
2. Implement automatic deletion of S3 data after 24 hours to minimize storage costs.
3. Regularly review and optimize EC2 instance types based on actual processing needs.
4. Use AWS Cost Explorer to identify further cost-saving opportunities.
5. Implement a system to periodically extract and store essential metrics before data deletion.

## 4. Scaling Considerations

- EC2: One instance per conveyor belt, sized to handle the load, with the YOLOv8n model packaged
- API Gateway: Configure to handle the known maximum throughput from each edge device
- S3: Sized to accommodate 24-hour storage needs based on each conveyor belt's production volume
- Edge Devices: One per conveyor belt, focus on reliability and maintenance
- Lambda: Designed to handle daily metrics extraction and data deletion for each conveyor belt

## 5. Monitoring and Maintenance Plan

1. Set up CloudWatch alarms for each conveyor belt:
   - EC2 instance CPU utilization, network I/O, and status checks
   - API Gateway 4xx and 5xx errors
   - S3 bucket size and request count
   - Edge device connectivity
   - Lambda function errors and timeouts

2. Implement logging for each conveyor belt:
   - EC2 instance logs to CloudWatch Logs
   - S3 access logs (retained for compliance purposes)
   - Edge device logs
   - Lambda function logs

3. Regular maintenance tasks:
   - Review and analyze logs daily
   - Update EC2 instance AMIs and patches monthly
   - Conduct system performance review weekly
   - Calibrate edge devices and cameras as needed
   - Verify successful execution of Lambda function for metrics extraction and data deletion

## 6. Risks and Mitigation Strategies

1. **System Downtime**
   - Risk: AWS service disruptions affecting production
   - Mitigation: Implement local fallback system for critical operations, use multi-AZ deployments

2. **Data Accuracy**
   - Risk: Incorrect fertility detection leading to economic losses
   - Mitigation: Regular model retraining and validation, implement human oversight

3. **Network Connectivity**
   - Risk: Poor connectivity between edge devices and AWS
   - Mitigation: Implement local caching and retry mechanisms, ensure robust factory network

4. **Data Loss**
   - Risk: Important data deleted before analysis
   - Mitigation: Implement robust metric extraction and storage process before daily deletion

5. **Regulatory Compliance**
   - Risk: Failure to meet food industry standards
   - Mitigation: Regular audits, stay updated on regulatory requirements, implement strict data handling and security measures

## 7. Future Enhancements

1. Implement machine learning pipeline for continuous model improvement using extracted metrics
2. Develop a centralized dashboard for monitoring all conveyor belts simultaneously
3. Implement predictive maintenance for both edge devices and conveyor belts
4. Explore potential for local edge computing to reduce latency and AWS dependency
5. Integrate system with broader factory management software for comprehensive oversight
6. Optimize data retention and analysis processes to balance cost-efficiency with valuable insights
7. Analyze metrics files to gain insights into system performance and egg fertility trends over time

