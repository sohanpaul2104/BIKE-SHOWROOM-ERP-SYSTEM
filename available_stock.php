<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Stock</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .table-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .sell-btn { background:#e67e22; color:white; border:none; padding:6px 15px; border-radius:4px; text-decoration:none; font-size:14px; font-weight:bold; }
        .qty-badge { background: #2c3e50; color: white; padding: 4px 12px; border-radius: 15px; font-weight: bold; font-size: 14px; }
        .back-btn { background: #7f8c8d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; margin-bottom: 20px; }
        .logout-btn { color: #e74c3c !important; margin-top: 40px; border-top: 1px solid #34495e; }
        th{ background-color: #34495e; color: white; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🏍️ Showroom</h2>
    <a href="dashboard.php">🏠 Main Menu</a>
    <a href="showroom_dashboard.php">🏠 Back to Dashboard</a>
    <a href="showroom.php">➕ Manage Stock (Add)</a>
    <a href="available_stock.php" style="background:#34495e;">📊 Available Stock</a>
    <a href="sold_bikes.php">📜 Sales History</a>
    <a href="index.php" class="logout-btn">🚪 Logout</a>

</div>

<div class="content">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h2>📊 Available Models Summary</h2>
        <a href="showroom.php" class="back-btn">⬅ Back to Add Stock</a>
    </div>

    <div class="table-container">
        <table style="width:100%; border-collapse:collapse;">
            <tr style="background:#34495e; color:white; text-align:left;">
                <th style="padding:12px;">Model Name</th>
                <th>Color</th>
                <th>Price (Ex-Showroom)</th>
                <th>Stock Quantity</th>
                <th>Action</th>
            </tr>
            <?php
            // GROUP BY Logic: Counts how many bikes of same Model+Color exist
            $sql = "SELECT model_name, color, showroom_price, COUNT(*) as qty 
                    FROM showroom_stock 
                    WHERE status = 'Available' 
                    GROUP BY model_name, color";
            
            $res = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_array($res)) {
                    $u_model = urlencode($row['model_name']);
                    $u_color = urlencode($row['color']);
                    
                    echo "<tr style='border-bottom:1px solid #ddd;'>
                            <td style='padding:12px; font-weight:bold;'>{$row['model_name']}</td>
                            <td>{$row['color']}</td>
                            <td>₹{$row['showroom_price']}</td>
                            <td><span class='qty-badge'>{$row['qty']} Units</span></td>
                            <td>
                                <a href='sell_bike.php?model={$u_model}&color={$u_color}' class='sell-btn'>💰 Sell One</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:gray;'>No stock available. <a href='showroom.php'>Add some?</a></td></tr>";
            }
            ?>
        </table>
    </div>

</div>
</body>
</html>