<?php 
include 'db.php'; 
session_start();

// Default user name
$user_name = isset($_SESSION['user']) ? $_SESSION['user'] : 'Sales Manager';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Showroom Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }

        /* --- SIDEBAR --- */
        .sidebar {
            width: 250px; height: 100vh; background: #2c3e50; color: white;
            position: fixed; left: 0; top: 0; padding-top: 20px;
        }
        .sidebar h2 { text-align: center; margin-bottom: 30px; font-size: 22px; border-bottom: 1px solid #34495e; padding-bottom: 20px; }
        .sidebar a {
            display: block; color: white; padding: 15px 25px; text-decoration: none;
            font-size: 16px; border-left: 4px solid transparent; transition: 0.3s;
        }
        .sidebar a:hover { background: #34495e; border-left: 4px solid #3498db; }
        .sidebar a.active { background: #34495e; border-left: 4px solid #3498db; }
        .logout-btn { color: #e74c3c !important; margin-top: 40px; border-top: 1px solid #34495e; }

        /* --- CONTENT --- */
        .content { margin-left: 250px; padding: 30px; }

        .header-section {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 30px; background: white; padding: 20px;
            border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .header-section h1 { margin: 0; font-size: 24px; color: #333; }
        .date-badge { background: #ecf0f1; color: #7f8c8d; padding: 8px 15px; border-radius: 20px; font-size: 14px; font-weight: bold; }

        /* --- CARDS --- */
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

        /* Colors */
        .card-purple { border-color: #8e44ad; }
        .card-purple .icon-box { background: #f4ecf7; color: #8e44ad; }
        
        .card-green { border-color: #27ae60; }
        .card-green .icon-box { background: #d5f5e3; color: #27ae60; }
        
        .card-blue { border-color: #2980b9; }
        .card-blue .icon-box { background: #d6eaf8; color: #2980b9; }

        /* --- TABLE --- */
        .table-container { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table-header { margin-bottom: 20px; font-size: 18px; font-weight: bold; color: #333; border-bottom: 2px solid #f4f6f9; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 12px 15px; background: #f8f9fa; color: #555; font-size: 13px; text-transform: uppercase; }
        td { padding: 12px 15px; border-bottom: 1px solid #eee; color: #333; }
        
        .price-text { color: #27ae60; font-weight: bold; }
        .action-btn { background: #3498db; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 12px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>🏍️ Showroom Panel</h2>
    <a href="dashboard.php">🏠 Main Menu</a>
    
    <a href="showroom_dashboard.php" class="active">📊 Dashboard</a>
    <a href="showroom.php">➕ Manage Stock (Add)</a>
    <a href="available_stock.php">📦 Available Stock</a>
    <a href="sold_bikes.php">📜 Sales History</a>
    
    <a href="index.php" class="logout-btn">🚪 Logout</a>
</div>

<div class="content">
    
    <div class="header-section">
        <div>
            <h1>Showroom Overview</h1>
            <p style="margin:5px 0 0; color:#7f8c8d;">Track inventory, sales, and revenue.</p>
        </div>
        <div class="date-badge">📅 <?php echo date("l, d M Y"); ?></div>
    </div>

    <div class="card-grid">
        
        <div class="card card-purple">
            <div class="card-info">
                <?php 
                $stock_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM showroom_stock WHERE status='Available'");
                $stock = mysqli_fetch_assoc($stock_q);
                ?>
                <h3><?php echo $stock['count']; ?></h3>
                <p>Bikes Available</p>
            </div>
            <div class="icon-box">🏍️</div>
        </div>

        <div class="card card-blue">
            <div class="card-info">
                <?php 
                $sold_q = mysqli_query($conn, "SELECT COUNT(*) as count FROM bike_sales");
                $sold = mysqli_fetch_assoc($sold_q);
                ?>
                <h3><?php echo $sold['count']; ?></h3>
                <p>Total Bikes Sold</p>
            </div>
            <div class="icon-box">🤝</div>
        </div>

        <div class="card card-green">
            <div class="card-info">
                <?php 
                $rev_q = mysqli_query($conn, "SELECT SUM(final_price) as total FROM bike_sales");
                $rev = mysqli_fetch_assoc($rev_q);
                $total_rev = $rev['total'] ? $rev['total'] : 0;
                ?>
                <h3>₹<?php echo number_format($total_rev); ?></h3>
                <p>Total Revenue</p>
            </div>
            <div class="icon-box">💰</div>
        </div>

    </div>

    <div class="table-container">
        <div class="table-header">📉 Recent Sales Activity</div>
        
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Model Sold</th>
                    <th>Chassis No</th>
                    <th>Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch Last 5 Sales
                $sql = "SELECT s.id, s.sale_date, s.final_price, c.name, st.model_name, st.chassis_no 
                        FROM bike_sales s 
                        JOIN customers c ON s.customer_id = c.id 
                        JOIN showroom_stock st ON s.stock_id = st.id 
                        ORDER BY s.id DESC LIMIT 5";
                
                $res = mysqli_query($conn, $sql);

                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $date = date("d M Y", strtotime($row['sale_date']));
                        echo "<tr>
                                <td>{$date}</td>
                                <td><b>{$row['name']}</b></td>
                                <td>{$row['model_name']}</td>
                                <td>{$row['chassis_no']}</td>
                                <td class='price-text'>₹" . number_format($row['final_price']) . "</td>
                                <td><a href='print_bill.php?id={$row['id']}' class='action-btn'>View Bill</a></td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding:20px; color:#999;'>No sales recorded yet.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>