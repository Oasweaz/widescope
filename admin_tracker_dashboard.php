<?php
session_start();
require_once 'functions.php';

// Redirect to login if not logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Handle admin login
    if (isset($_POST['admin_login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = "Invalid admin credentials";
        }
    }
    
    // Show login form if not logged in
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login - Freight Tracking System</title>
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
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                }
                
                .login-container {
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
                    width: 100%;
                    max-width: 400px;
                }
                
                h2 {
                    color: #2c3e50;
                    margin-bottom: 20px;
                    text-align: center;
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
                
                input {
                    width: 100%;
                    padding: 12px;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    font-size: 16px;
                }
                
                input:focus {
                    border-color: #3498db;
                    outline: none;
                }
                
                .btn {
                    display: block;
                    width: 100%;
                    background: #3498db;
                    color: white;
                    padding: 12px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 16px;
                }
                
                .btn:hover {
                    background: #2980b9;
                }
                
                .error {
                    color: red;
                    margin-bottom: 15px;
                    padding: 10px;
                    background: #ffeeee;
                    border-radius: 4px;
                }
                
                .demo-credentials {
                    margin-top: 20px;
                    font-size: 14px;
                    color: #7f8c8d;
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <h2>Admin Login</h2>
                <?php if (isset($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <button type="submit" name="admin_login" class="btn">Login</button>
                </form>
                <div class="demo-credentials">
                    <strong>Demo Credentials:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin123</code>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Handle admin logout
if (isset($_GET['admin_logout'])) {
    unset($_SESSION['admin_logged_in']);
    header("Location: user_tracker.php");
    exit();
}

// Handle inventory actions
if (isset($_POST['add_item'])) {
    $data = [
        'tracking_number' => $_POST['tracking_number'],
        'consignee' => $_POST['consignee'],
        'origin' => $_POST['origin'],
        'destination' => $_POST['destination'],
        'contents' => $_POST['contents'],
        'weight' => $_POST['weight'],
        'status' => $_POST['status'],
        'estimated_arrival' => $_POST['estimated_arrival'],
        'carrier' => $_POST['carrier']
    ];
    addItem($data);
    header("Location: admin_tracker_dashboard.php");
    exit();
}

if (isset($_POST['update_item'])) {
    $id = $_POST['id'];
    $data = [
        'tracking_number' => $_POST['tracking_number'],
        'consignee' => $_POST['consignee'],
        'origin' => $_POST['origin'],
        'destination' => $_POST['destination'],
        'contents' => $_POST['contents'],
        'weight' => $_POST['weight'],
        'status' => $_POST['status'],
        'estimated_arrival' => $_POST['estimated_arrival'],
        'carrier' => $_POST['carrier']
    ];
    updateItem($id, $data);
    header("Location: admin_tracker_dashboard.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    deleteItem($id);
    header("Location: admin_tracker_dashboard.php");
    exit();
}

// Handle edit request
$edit_item = null;
if (isset($_GET['edit'])) {
    $edit_item = getItem($_GET['edit']);
}

// Handle admin search
$admin_search_results = [];
$admin_search_term = '';
if (isset($_POST['admin_search']) && !empty($_POST['search_term'])) {
    $admin_search_term = $_POST['search_term'];
    $admin_search_results = searchInventory($admin_search_term);
} else {
    $admin_search_results = getInventory();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Freight Tracking System</title>
    <!-- Load jsPDF from CDN with correct version -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
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
            max-width: 1400px;
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
        
        .btn-warning {
            background: #2e49e2ff;
        }
        
        .btn-warning:hover {
            background: #e67e22;
        }
        
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            padding: 25px;
            margin-bottom: 25px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        table, th, td {
            border: 1px solid #ddd;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        tr:nth-child(even) {
            background-color: #f9f9f9;
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
        
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        .search-form {
            display: flex;
            margin-bottom: 20px;
        }
        
        .search-form input {
            border-radius: 4px 0 0 4px;
            margin: 0;
        }
        
        .search-form button {
            border-radius: 0 4px 4px 0;
            white-space: nowrap;
        }
        
        .admin-only {
            border-left: 4px solid #e74c3c;
            padding-left: 15px;
        }
        
        @media (max-width: 768px) {
            table {
                display: block;
                overflow-x: auto;
            }

            h1, h2, h3 {
                font-size: 15px;
                margin-bottom: 0px;
                margin-left: 8px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-form input {
                border-radius: 4px;
                margin-bottom: 10px;
            }
            
            .search-form button {
                border-radius: 4px;
            }
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
    </style>
</head>
<body>
    <div class="container">
        <header>
            <img src="./assets/logo/ws-brand-logo.png" alt="widescope"><h1>Freight Tracking System - Admin Panel</h1>
            <div>
                <a href="user_tracker.php" class="btn btn-secondary">Back to Tracker</a>
                <a href="?admin_logout=1" class="btn btn-danger">Logout</a>
            </div>
        </header>

        <!-- Search Form -->
        <div class="card">
            <h2>Search Inventory</h2>
            <form method="POST" class="search-form">
                <input type="text" name="search_term" placeholder="Search by tracking number, consignee, origin, or destination" value="<?php echo htmlspecialchars($admin_search_term); ?>">
                <button type="submit" name="admin_search" class="btn btn-primary">Search</button>
            </form>
        </div>

        <!-- Add/Edit Form -->
        <div class="card">
            <h2><?php echo $edit_item ? 'Edit' : 'Add New'; ?> Freight Item</h2>
            <form method="POST">
                <?php if ($edit_item): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_item['id']; ?>">
                <?php endif; ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                    <div class="form-group">
                        <label for="tracking_number">Tracking Number</label>
                        <input type="text" id="tracking_number" name="tracking_number" value="<?php echo $edit_item ? $edit_item['tracking_number'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="consignee">Consignee</label>
                        <input type="text" id="consignee" name="consignee" value="<?php echo $edit_item ? $edit_item['consignee'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="origin">Origin</label>
                        <input type="text" id="origin" name="origin" value="<?php echo $edit_item ? $edit_item['origin'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="destination">Destination</label>
                        <input type="text" id="destination" name="destination" value="<?php echo $edit_item ? $edit_item['destination'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contents">Contents</label>
                        <input type="text" id="contents" name="contents" value="<?php echo $edit_item ? $edit_item['contents'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="weight">Weight (kg)</label>
                        <input type="number" step="0.01" id="weight" name="weight" value="<?php echo $edit_item ? $edit_item['weight'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="Pending" <?php echo ($edit_item && $edit_item['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Transit" <?php echo ($edit_item && $edit_item['status'] == 'In Transit') ? 'selected' : ''; ?>>In Transit</option>
                            <option value="Out for Delivery" <?php echo ($edit_item && $edit_item['status'] == 'Out for Delivery') ? 'selected' : ''; ?>>Out for Delivery</option>
                            <option value="Delivered" <?php echo ($edit_item && $edit_item['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                            <option value="Delayed" <?php echo ($edit_item && $edit_item['status'] == 'Delayed') ? 'selected' : ''; ?>>Delayed</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="estimated_arrival">Estimated Arrival</label>
                        <input type="date" id="estimated_arrival" name="estimated_arrival" value="<?php echo $edit_item ? $edit_item['estimated_arrival'] : ''; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="carrier">Carrier</label>
                        <input type="text" id="carrier" name="carrier" value="<?php echo $edit_item ? $edit_item['carrier'] : ''; ?>" required>
                    </div>
                </div>
                <?php if ($edit_item): ?>
                    <button type="submit" name="update_item" class="btn btn-success">Update Item</button>
                    <a href="admin_tracker_dashboard.php" class="btn btn-secondary">Cancel</a>
                <?php else: ?>
                    <button type="submit" name="add_item" class="btn btn-success">Add Item</button>
                <?php endif; ?>
            </form>
        </div>

        <!-- Inventory List -->
        <div class="card">
            <h2>Freight Inventory</h2>
            <table>
                <thead>
                    <tr>
                        <th>Tracking #</th>
                        <th>Consignee</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Contents</th>
                        <th>Weight</th>
                        <th>Date Created</th>
                        <th>Status</th>
                        <th>Est. Arrival</th>
                        <th>Carrier</th>
                        <th>Date Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admin_search_results as $item): ?>
                        <tr>
                            <td><?php echo $item['tracking_number']; ?></td>
                            <td><?php echo $item['consignee']; ?></td>
                            <td><?php echo $item['origin']; ?></td>
                            <td><?php echo $item['destination']; ?></td>
                            <td><?php echo $item['contents']; ?></td>
                            <td><?php echo $item['weight']; ?> kg</td>
                            <td><?php echo $item['created_at']; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $item['status'])); ?>">
                                    <?php echo $item['status']; ?>
                                </span>
                            </td>
                            <td><?php echo $item['estimated_arrival']; ?></td>
                            <td><?php echo $item['carrier']; ?></td>
                            <td><?php echo $item['updated_at']; ?></td>
                            <td class="action-buttons">
                                <a href="?edit=<?php echo $item['id']; ?>" class="btn btn-warning">Edit</a>
                                <button onclick="generatePDF(<?php echo $item['id']; ?>)" class="btn btn-warning">PDF</button>
                                <a href="?delete=<?php echo $item['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
        
        // Function to generate PDF using jsPDF
        function generatePDF(itemId) {
            // Show loading indicator
            document.getElementById('pdfLoading').style.display = 'flex';
            
            // Wait for jsPDF to be available
            waitForJSPDF(function() {
                try {
                    // Get the row containing the item data
                    const buttons = document.querySelectorAll(`button[onclick="generatePDF(${itemId})"]`);
                    if (buttons.length === 0) {
                        hideLoading();
                        return;
                    }
                    
                    const button = buttons[0];
                    const row = button.closest('tr');
                    const cells = row.querySelectorAll('td');
                    
                    // Extract data from the table row
                    const trackingNumber = cells[0].textContent;
                    const consignee = cells[1].textContent;
                    const origin = cells[2].textContent;
                    const destination = cells[3].textContent;
                    const contents = cells[4].textContent;
                    const weight = cells[5].textContent;
                    const createdAt = cells[6].textContent;
                    const status = cells[7].querySelector('.status-badge').textContent.trim();
                    const estimatedArrival = cells[8].textContent;
                    const carrier = cells[9].textContent;
                    const updatedAt = cells[10].textContent;
                    
                    // Use jsPDF to create the PDF
                    const { jsPDF } = window.jspdf;
                    const doc = new jsPDF();
                    
                    // Set document properties
                    doc.setProperties({
                        title: `Freight Details - ${trackingNumber}`,
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
                    doc.text('Admin Copy-Freight Tracking Details', 105, 30, { align: 'center' });
                    
                    doc.setFontSize(12);
                    doc.setTextColor(100, 100, 100);
                    doc.text(`Tracking Number: ${trackingNumber}`, 105, 38, { align: 'center' });
                    
                    // Add a line separator
                    doc.setDrawColor(200, 200, 200);
                    doc.line(20, 40, 190, 40);
                    
                    // Add shipment information
                    doc.setFontSize(14);
                    doc.setTextColor(30, 30, 30);
                    doc.text('Shipment Information', 20, 50);
                    
                    doc.setFontSize(11);
                    doc.text(`Consignee: ${consignee}`, 20, 60);
                    doc.text(`Origin: ${origin}`, 20, 70);
                    doc.text(`Destination: ${destination}`, 20, 80);
                    doc.text(`Contents: ${contents}`, 20, 90);
                    doc.text(`Weight: ${weight}`, 20, 100);
                    doc.text(`Date/Time Created: ${createdAt}`, 20, 110);
                    
                    // Add status information
                    doc.setFontSize(14);
                    doc.text('Status Information', 20, 150);
                    
                    doc.setFontSize(11);
                    doc.text(`Status: ${status}`, 20, 160);
                    doc.text(`Estimated Arrival: ${estimatedArrival}`, 20, 170);
                    doc.text(`Carrier: ${carrier}`, 20, 180);
                    doc.text(`Date/Time Updated: ${updatedAt}`, 20, 190);
                    
                    // Add generated date
                    const generatedDate = new Date().toLocaleString();
                    doc.setFontSize(10);
                    doc.setTextColor(150, 150, 150);
                    doc.text(`Generated on: ${generatedDate}`, 20, 280);
                    
                    // Add page border
                    doc.setDrawColor(200, 200, 200);
                    doc.rect(15, 15, 180, 270);
                    
                    // Save the PDF
                    doc.save(`freight_${trackingNumber}.pdf`);
                    
                    // Hide loading indicator
                    hideLoading();
                    
                } catch (error) {
                    console.error('Error generating PDF:', error);
                    alert('Error generating PDF. Please check the console for details.');
                    hideLoading();
                }
            });
        }
        
        function hideLoading() {
            document.getElementById('pdfLoading').style.display = 'none';
        }
        
        // Fallback in case jsPDF doesn't load
        setTimeout(function() {
            if (typeof window.jspdf === 'undefined') {
                console.error('jsPDF library failed to load');
                // Replace PDF buttons with a message
                const pdfButtons = document.querySelectorAll('button[onclick^="generatePDF"]');
                pdfButtons.forEach(button => {
                    button.onclick = function() {
                        alert('PDF library failed to load. Please refresh the page and try again.');
                    };
                });
            }
        }, 5000);
    </script>
</body>
</html>