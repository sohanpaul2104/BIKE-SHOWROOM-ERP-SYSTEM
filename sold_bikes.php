<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; background: white; }
        th { background: #2c3e50; color: white; padding: 10px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 10px; }
        .view-btn { background: #3498db; color: white; padding: 5px 10px; text-decoration: none; border-radius: 4px; font-size: 12px; }
        .logout-btn { color: #e74c3c !important; margin-top: 40px; border-top: 1px solid #34495e; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🏍️ Showroom</h2>
    <a href="dashboard.php">🏠 Main Menu</a>
    <a href="showroom_dashboard.php">🏠 Back to Dashboard</a>
    <a href="showroom.php">➕ Manage Stock (Add)</a>
    <a href="available_stock.php">📊 Available Stock</a>
    <a href="sold_bikes.php" style="background:#34495e;">📜 Sales History</a>
    <a href="index.php" class="logout-btn">🚪 Logout</a>
    
</div>

<div class="content">
    <h2>📜 Sales History Report</h2>
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Customer</th>
                <th>Bike Model</th>
                <th>Chassis No</th>
                <th>Sold Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT s.id, s.sale_date, s.final_price, c.name, st.model_name, st.chassis_no 
                    FROM bike_sales s 
                    JOIN customers c ON s.customer_id = c.id 
                    JOIN showroom_stock st ON s.stock_id = st.id 
                    ORDER BY s.sale_date DESC";
            
            $res = mysqli_query($conn, $sql);
            
            if(mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_assoc($res)) {
                    $date = date("d M Y", strtotime($row['sale_date']));
                    echo "<tr>
                            <td>{$date}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['model_name']}</td>
                            <td>{$row['chassis_no']}</td>
                            <td style='color:#27ae60; font-weight:bold;'>₹{$row['final_price']}</td>
                            <td>
                                <a href='print_bill.php?id={$row['id']}' class='view-btn'>🖨️ View Bill</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center; padding:20px;'>No sales yet.</td></tr>";
            }
            ?>
        </tbody>
    </table>

</div>
</body>
</html>