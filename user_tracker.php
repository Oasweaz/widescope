<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freight Tracking System</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .container img {
            width: 60px;
            height: 63px;
        }
        
        header {
            background: linear-gradient(135deg, #2c3e50, #ABDD20);
            color: white;
            padding: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 8px;
            margin-bottom: 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        h1, h2, h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .btn {
            display: inline-block;
            background: #3498db;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            margin: 5px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #2980b9;
        }
        
        .btn-primary {
            background: #3498db;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .btn-danger {
            background: #e74c3c;
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-success {
            background: #2ecc71;
        }
        
        .btn-success:hover {
            background: #27ae60;
        }
        
        .btn-secondary {
            background: #7f8c8d;
            color: #ABDD20;
        }
        
        .btn-secondary:hover {
            background: #636e70;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .tracking-form {
            text-align: center;
            padding: 30px;
            margin: 30px 0;
        }
        
        .tracking-input {
            display: flex;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .tracking-input input {
            border-radius: 4px 0 0 4px;
            margin: 0;
        }
        
        .tracking-input button {
            border-radius: 0 4px 4px 0;
            white-space: nowrap;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
        }
        
        .status-pending {
            background-color: #f39c12;
            color: white;
        }
        
        .status-in-transit {
            background-color: #3498db;
            color: white;
        }
        
        .status-out-for-delivery {
            background-color: #9b59b6;
            color: white;
        }
        
        .status-delivered {
            background-color: #2ecc71;
            color: white;
        }
        
        .status-delayed {
            background-color: #e74c3c;
            color: white;
        }
        
        .tracking-result {
            padding: 20px;
            background: #e8f4fc;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .tracking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }
        
        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .detail-item strong {
            display: block;
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        @media (max-width: 768px) {
            .tracking-input {
                flex-direction: column;
            }

            h1, h2, h3 {
                font-size: 15px;
                margin-bottom: 0px;
                margin-left: 8px;
            }
            
            .tracking-input input {
                border-radius: 4px;
                margin-bottom: 10px;
            }
            
            .tracking-input button {
                border-radius: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="./assets/logo/ws-brand-logo.png" alt="widescope"><h1>Freight Tracking System</h1>
            <a href="admin_tracker_dashboard.php" class="btn btn-secondary">Admin Login</a>
        </header>

        <!-- Tracking Form -->
        <div class="card tracking-form">
            <h2>Track Your Shipment</h2>
            <p>Enter your tracking number below to check the status of your freight</p>
            <form method="POST" action="track_result.php">
                <div class="tracking-input">
                    <input type="text" name="tracking_number" placeholder="Enter tracking number (e.g. TRK123456)" required>
                    <button type="submit" name="track" class="btn btn-primary">Track Shipment</button>
                </div>
            </form>
        </div>

        <!-- Sample Tracking Numbers -->
        <div class="card">
            <h3>Important Information</h3>
            <p>kindly note that your Freight progress can be monitored here and the Operations Manager will update your tracker as soon as there is a new development:</p>
            <ul style="list-style-type: none; padding: 10px 0;">
                <li style="padding: 5px 0;"><strong>NOTE I:</strong> - Make sure to enter in the right tracking numbers</li>
                <li style="padding: 5px 0;"><strong>NOTE II:</strong> - Keep an eye for the status of your package: PENDING, IN-TRANSIT, OUT-FOR-DELIVERY, DELIVERED, DELAYED</li>
                <li style="padding: 5px 0;"><strong>NOTE III:</strong> - You can download 'PDF' transcript of your package anytime at the bottom of the page</li>
                <li style="padding: 5px 0;"><strong>NOTE IV:</strong> - Ensure to bring with you the printed version(hard-copy) of the 'PDF' you have downloaded for proper clearance</li>
            </ul>
        </div>
    </div>
</body>
</html>