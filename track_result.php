<?php
require_once 'functions.php';

// Handle tracking number search
$tracking_result = null;
if (isset($_POST['track'])) {
    $tracking_number = $_POST['tracking_number'];
    $tracking_result = getItemByTrackingNumber($tracking_number);
    if (!$tracking_result) {
        $tracking_error = "No shipment found with tracking number: " . htmlspecialchars($tracking_number);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Load jsPDF from CDN with correct version -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <title>Track Shipment - Freight Tracking System</title>
    <style>
        /* Same CSS as the main page */
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
        
        /* Loading indicator for PDF generation */
        .pdf-loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        
        .spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .tracking-details {
                grid-template-columns: 1fr;
            }

            h1, h2, h3 {
                font-size: 15px;
                margin-bottom: 0px;
                margin-left: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="./assets/logo/ws-brand-logo.png" alt="widescope"><h1>Freight Tracking System</h1>
            <a href="user_tracker.php" class="btn btn-secondary">Back to Home</a>
        </header>

        <!-- Tracking Results -->
        <?php if (isset($tracking_result) && $tracking_result): ?>
            <div class="card tracking-result">
                <h2>Shipment Details</h2>
                <div class="tracking-details">
                    <div class="detail-item">
                        <strong>Tracking Number</strong>
                        <?php echo $tracking_result['tracking_number']; ?>
                    </div>
                    <div class="detail-item">
                        <strong>Consignee</strong>
                        <?php echo $tracking_result['consignee']; ?>
                    </div>
                    <div class="detail-item">
                        <strong>Status</strong>
                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $tracking_result['status'])); ?>">
                            <?php echo $tracking_result['status']; ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <strong>Origin</strong>
                        <?php echo $tracking_result['origin']; ?>
                    </div>
                    <div class="detail-item">
                        <strong>Destination</strong>
                        <?php echo $tracking_result['destination']; ?>
                    </div>
                    <div class="detail-item">
                        <strong>Estimated Arrival</strong>
                        <?php echo $tracking_result['estimated_arrival']; ?>
                    </div>
                    <div class="detail-item">
                        <strong>Contents</strong>
                        <?php echo $tracking_result['contents']; ?>
                    </div>
                    <div class="detail-item">
                        <strong>Weight</strong>
                        <?php echo $tracking_result['weight']; ?> kg
                    </div>
                    <div class="detail-item">
                        <strong>Date/Time Created</strong>
                        <?php echo $tracking_result['created_at']; ?>
                    </div>
                    <div class="detail-item">
                        <strong>Carrier</strong>
                        <?php echo $tracking_result['carrier']; ?>
                    </div>
                    <div class="detail-item">
                        <strong>Date/Time Updated</strong>
                        <?php echo $tracking_result['updated_at']; ?>
                    </div>
                </div>
                <div style="margin-top: 20px; text-align: center;">
                    <button onclick="generatePDF()" class="btn btn-primary">Download as PDF</button>
                </div>
            </div>
            
            <!-- Loading indicator -->
            <div class="pdf-loading" id="pdfLoading">
                <div class="spinner"></div>
                <p>Generating PDF, please wait...</p>
            </div>
            
            <script>
                // Wait for the jsPDF library to load
                function waitForJSPDF(callback) {
                    if (typeof window.jspdf !== 'undefined') {
                        callback();
                    } else {
                        setTimeout(function() {
                            waitForJSPDF(callback);
                        }, 100);
                    }
                }
                
                function generatePDF() {
                    // Show loading indicator
                    document.getElementById('pdfLoading').style.display = 'flex';
                    
                    // Wait for jsPDF to be available
                    waitForJSPDF(function() {
                        try {
                            const { jsPDF } = window.jspdf;
                            const doc = new jsPDF();
                            
                            // Set document properties
                            doc.setProperties({
                                title: `Freight Details - <?php echo $tracking_result['tracking_number']; ?>`,
                                subject: 'Freight Tracking Information',
                                author: 'Freight Tracking System',
                                keywords: 'freight, tracking, shipment',
                                creator: 'Widescope Freight Tracking System'
                            });

                            // Add faded background image first (before any content)
                            try {
                                // Add background image with low opacity (faded effect)
                                // Center the image: x=15, y=15, width=180, height=270 (same as border)
                                doc.addImage(
                                    './assets/logo/trans-ws-brand-logo.png',  // Path to your background image
                                    'PNG', 
                                    72,                                // X position (left)
                                    80,                                // Y position (top)
                                    70,                               // Width (matches border)
                                    70,                               // Height (matches border)
                                    undefined,                         // Alias
                                    'NONE',                            // Compression
                                    0,                                 // Rotation
                                    0.01                                // Opacity (10% - adjust as needed)
                                );
                            } catch (bgError) {
                                console.log('Could not load background image:', bgError);
                                // Optional: Add a fallback background if image fails to load
                                doc.setFillColor(245, 245, 245);
                                doc.rect(15, 15, 180, 270, 'F');
                            }
                            
                            // Add logo/title
                            doc.addImage('./assets/logo/ws-brand-logo.png', 'PNG', 100, 15, 8, 8);
                            doc.setFontSize(20);
                            doc.setTextColor(40, 40, 40);
                            doc.text('Widescope Logistics Freight Tracking Details', 105, 30, { align: 'center' });
                            
                            doc.setFontSize(12);
                            doc.setTextColor(100, 100, 100);
                            doc.text(`Tracking Number: <?php echo $tracking_result['tracking_number']; ?>`, 105, 38, { align: 'center' });
                            
                            // Add a line separator
                            doc.setDrawColor(200, 200, 200);
                            doc.line(20, 40, 190, 40);
                            
                            // Add shipment information
                            doc.setFontSize(14);
                            doc.setTextColor(30, 30, 30);
                            doc.text('Shipment Information', 20, 50);
                            
                            doc.setFontSize(11);
                            doc.text(`Consignee: <?php echo $tracking_result['consignee']; ?>`, 20, 60);
                            doc.text(`Origin: <?php echo $tracking_result['origin']; ?>`, 20, 70);
                            doc.text(`Destination: <?php echo $tracking_result['destination']; ?>`, 20, 80);
                            doc.text(`Contents: <?php echo $tracking_result['contents']; ?>`, 20, 90);
                            doc.text(`Weight: <?php echo $tracking_result['weight']; ?> kg`, 20, 100);
                            doc.text(`Date/Time Created: <?php echo $tracking_result['created_at']; ?>`, 20, 110);
                            
                            
                            // Add status information
                            doc.setFontSize(14);
                            doc.text('Status Information', 20, 150);
                            
                            doc.setFontSize(11);
                            doc.text(`Status: <?php echo $tracking_result['status']; ?>`, 20, 160);
                            doc.text(`Estimated Arrival: <?php echo $tracking_result['estimated_arrival']; ?>`, 20, 170);
                            doc.text(`Carrier: <?php echo $tracking_result['carrier']; ?>`, 20, 180);
                            doc.text(`Date/Time Updated: <?php echo $tracking_result['updated_at']; ?>`, 20, 190);
                            
                            // Add generated date
                            const generatedDate = new Date().toLocaleString();
                            doc.setFontSize(10);
                            doc.setTextColor(150, 150, 150);
                            doc.text(`Generated on: ${generatedDate}`, 20, 280);
                            
                            // Add page border
                            doc.setDrawColor(200, 200, 200);
                            doc.rect(15, 15, 180, 270);
                            
                            // Save the PDF
                            doc.save(`freight_<?php echo $tracking_result['tracking_number']; ?>.pdf`);
                            
                            // Hide loading indicator
                            document.getElementById('pdfLoading').style.display = 'none';
                            
                        } catch (error) {
                            console.error('Error generating PDF:', error);
                            alert('Error generating PDF. Please check the console for details.');
                            document.getElementById('pdfLoading').style.display = 'none';
                        }
                    });
                }
                
                // Fallback in case jsPDF doesn't load
                setTimeout(function() {
                    if (typeof window.jspdf === 'undefined') {
                        console.error('jsPDF library failed to load');
                        // Replace PDF button with a message
                        const pdfButton = document.querySelector('button[onclick="generatePDF()"]');
                        if (pdfButton) {
                            pdfButton.onclick = function() {
                                alert('PDF library failed to load. Please refresh the page and try again.');
                            };
                        }
                    }
                }, 5000);
            </script>
        <?php elseif (isset($tracking_error)): ?>
            <div class="card" style="text-align: center; color: #e74c3c;">
                <h3>Shipment Not Found</h3>
                <p><?php echo $tracking_error; ?></p>
                <p>Please check your tracking number and try again.</p>
                <a href="user_tracker.php" class="btn btn-primary">Try Again</a>
            </div>
        <?php else: ?>
            <div class="card" style="text-align: center;">
                <h3>No Tracking Information</h3>
                <p>Please enter a tracking number on the home page.</p>
                <a href="user_tracker.php" class="btn btn-primary">Back to Home</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>