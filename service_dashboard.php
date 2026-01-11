<?php 
include 'db.php'; 
session_start(); 

// Default user if session is not set
$user_name = isset($_SESSION['user']) ? $_SESSION['user'] : 'Mechanic';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Service Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }

        /* --- SIDEBAR STYLING --- */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #2c3e50;
            color: white;
            position: fixed;
            padding-top: 20px;
            left: 0; top: 0;
            overflow-y: auto;
        }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 22px; border-bottom: 1px solid #34495e; padding-bottom: 20px; }
        .sidebar a {
            display: block; color: white; padding: 15px 25px; text-decoration: none;
            font-size: 16px; border-left: 4px solid transparent; transition: 0.3s;
        }
        .sidebar a:hover { background: #34495e; border-left: 4px solid #3498db; }
        .sidebar a.active { background: #34495e; border-left: 4px solid #3498db; }
        .logout-btn { color: #e74c3c !important; margin-top: 40px; border-top: 1px solid #34495e; }
        .logout-btn:hover { background: #c0392b !important; color: white !important; }

        /* --- CONTENT AREA --- */
        .content { margin-left: 250px; padding: 30px; }
        
        .header-section {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; background: white; padding: 20px;
            border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .header-section h1 { margin: 0; font-size: 24px; color: #333; }
        .date-badge { background: #ecf0f1; color: #7f8c8d; padding: 8px 15px; border-radius: 20px; font-size: 14px; font-weight: bold; }

        /* --- DASHBOARD CARDS --- */
        .card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; margin-bottom: 30px; }
        
        .card {
            background: white; padding: 25px; border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex; align-items: center; justify-content: space-between;
            transition: transform 0.2s; border-left: 5px solid;
        }
        .card:hover { transform: translateY(-5px); }
        
        .card-info h3 { margin: 0; font-size: 36px; color: #2c3e50; }
        .card-info p { margin: 5px 0 0; color: #7f8c8d; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; }
        
        .icon-box { font-size: 30px; padding: 15px; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; }

        /* Card Colors */
        .card-orange { border-color: #f39c12; }
        .card-orange .icon-box { background: #fdebd0; color: #f39c12; }
        
        .card-red { border-color: #e74c3c; }
        .card-red .icon-box { background: #fadbd8; color: #e74c3c; }
        
        .card-blue { border-color: #2980b9; }
        .card-blue .icon-box { background: #d6eaf8; color: #2980b9; }

        /* --- ACTIVE TABLE --- */
        .table-container { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table-header { margin-bottom: 20px; font-size: 18px; font-weight: bold; color: #333; border-bottom: 2px solid #f4f6f9; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; background: #f8f9fa; color: #555; font-size: 13px; text-transform: uppercase; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; color: #333; }
        tr:hover { background-color: #f9f9f9; }
        
        .status-badge { padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; }
        .status-pending { background: #fff3cd; color: #856404; }
        
        .action-btn { background: #3498db; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; }
        .action-btn:hover { background: #2980b9; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🔧 Service Center</h2>
    <a href="dashboard.php">🏠 Main Menu</a>
    <a href="service_dashboard.php" class="active">📊 Dashboard</a>
    <a href="bikes.php">🔍 Find / Add Bike</a>
    <a href="service.php">🔧 Service</a>
    <a href="inventory.php">📦 Spare Parts</a>
    <a href="suppliers.php">🚚 Suppliers Log</a>
    <a href="billing.php">💰 Billing</a>
    <a href="index.php" class="logout-btn">🚪 Logout</a>
</div>

<div class="content">
    
    <div class="header-section">
        <div>
            <h1>Hello, <?php echo htmlspecialchars($user_name); ?> 👋</h1>
            <p style="margin:5px 0 0; color:#7f8c8d;">Here's what's happening in the workshop today.</p>
        </div>
        <div class="date-badge">📅 <?php echo date("l, d M Y"); ?></div>
    </div>

    <div class="card-grid">
        
        <div class="card card-orange">
            <div class="card-info">
                <?php $pending = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM services WHERE status='Pending'")); ?>
                <h3><?php echo $pending; ?></h3>
                <p>Pending Jobs</p>
            </div>
            <div class="icon-box">🔧</div>
        </div>

        <div class="card card-red">
            <div class="card-info">
                <?php $low_stock = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM parts WHERE stock < min_alert")); ?>
                <h3><?php echo $low_stock; ?></h3>
                <p>Low Stock Alerts</p>
            </div>
            <div class="icon-box">⚠️</div>
        </div>

        <div class="card card-blue">
            <div class="card-info">
                <?php $total_bikes = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM bikes")); ?>
                <h3><?php echo $total_bikes; ?></h3>
                <p>Registered Bikes</p>
            </div>
            <div class="icon-box">🏍️</div>
        </div>

    </div>

    <div class="table-container">
        <div class="table-header">📋 Current Active Jobs (Pending)</div>
        
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Bike Reg No</th>
                    <th>Issue Reported</th>
                    <th>Est. Cost</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch latest 5 pending services to populate the dashboard table
                $sql = "SELECT s.*, b.reg_number, b.model_name 
                        FROM services s 
                        JOIN bikes b ON s.bike_id = b.id 
                        WHERE s.status = 'Pending' 
                        ORDER BY s.service_date DESC LIMIT 5";
                
                $res = mysqli_query($conn, $sql);

                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        echo "<tr>
                                <td>{$row['service_date']}</td>
                                <td><b>{$row['reg_number']}</b> <span style='color:#777; font-size:12px;'>({$row['model_name']})</span></td>
                                <td>{$row['details']}</td>
                                <td>₹{$row['total_cost']}</td>
                                <td><span class='status-badge status-pending'>Pending</span></td>
                                <td><a href='service_dashboard.php' class='action-btn'>View</a></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:20px; color:#777;'>✅ No pending jobs. All caught up!</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>