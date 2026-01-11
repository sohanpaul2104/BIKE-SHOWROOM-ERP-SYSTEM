<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        *{ margin:0; padding:0; box-sizing: border-box; }
        body { background-color: #f0f2f5; font-family: sans-serif; }
        .main-container { 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 60vh; 
            gap: 50px; 
        }
        .content1 {
            margin-left: 220px; /* Adjust based on sidebar width */
            padding: 20px;
            gap: 40px;
            display: flex;
            flex-direction: column;
        }
        .big-card {
            width: 300px;
            height: 300px;
            padding: 50px;
            text-align: center;
            background: white;
            border-radius: 20px;
            text-decoration: none;
            color: #333;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 8px solid #ccc;
        }
        .big-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        
        /* Specific Colors */
        .card-sales { border-top-color: #8e44ad; } /* Purple */
        .card-service { border-top-color: #2980b9; } /* Blue */

        .icon { font-size: 80px; display: block; margin-bottom: 20px; }
        .title { font-size: 28px; font-weight: bold; display: block; margin-bottom: 10px; }
        .desc { color: #7f8c8d; font-size: 16px; }
    </style>
</head>
<body>
<div class="content1">
    <div style="text-align:center; margin-top:30px;">
    <h1 style="text-align:center; color:#2c3e50; margin-top:50px;">Bike Showroom Management System</h1>
    </div>
    <div class="main-container">

        <a href="showroom_dashboard.php" class="big-card card-sales">
            <span class="icon">🏍️</span>
            <span class="title">New Bikes</span>
            <span class="desc">Showroom, Sales & Stock</span>
        </a>

        <a href="service_dashboard.php" class="big-card card-service">
            <span class="icon">🔧</span>
            <span class="title">Servicing</span>
            <span class="desc">Workshop, Repairs & Parts</span>
        </a>

    </div>
</div>

</body>
</html>