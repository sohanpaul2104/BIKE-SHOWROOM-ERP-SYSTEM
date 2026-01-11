<?php include 'db.php'; 

// 1. GET MODEL & COLOR FROM URL (Passed from showroom.php)
if(isset($_GET['model']) && isset($_GET['color'])) {
    $model = urldecode($_GET['model']);
    $color = urldecode($_GET['color']);
} else {
    // If accessed directly without clicking "Sell", go back
    header("Location: showroom.php");
    exit();
}

// VARIABLES TO TRACK STATUS
$sale_successful = false;
$new_sale_id = 0;
$sold_chassis = "";
$sold_customer = "";
$error_message = "";

// HANDLE FORM SUBMISSION
if(isset($_POST['confirm_sale'])) {
    
    // --- STEP 1: GATHER INPUTS ---
    $stock_id = $_POST['stock_id'];
    $c_name = mysqli_real_escape_string($conn, $_POST['c_name']);
    $c_phone = mysqli_real_escape_string($conn, $_POST['c_phone']);
    $c_addr = mysqli_real_escape_string($conn, $_POST['c_address']);
    
    // Costs
    $rto = (float)$_POST['rto_cost'];
    $ins = (float)$_POST['ins_cost'];

    // Payment Method (NEW UPDATE)
    $pay_method = mysqli_real_escape_string($conn, $_POST['pay_method']);

    // --- STEP 2: SAFETY CHECK (IDENTITY VALIDATION) ---
    $can_proceed = true;
    $cust_id = 0;

    $check_cust = mysqli_query($conn, "SELECT * FROM customers WHERE phone='$c_phone'");
    
    if(mysqli_num_rows($check_cust) > 0) {
        // Customer Exists: Check if Name Matches
        $row = mysqli_fetch_assoc($check_cust);
        
        $existing_name = strtolower(trim($row['name'])); 
        $input_name = strtolower(trim($c_name));

        if($existing_name != $input_name) {
            // ❌ STOP! Names are different.
            $can_proceed = false;
            $error_message = "⚠️ <b>Identity Mismatch!</b><br> 
                              Phone Number <b>$c_phone</b> is already registered to <b>{$row['name']}</b>.<br>
                              You entered <b>$c_name</b>.<br><br>
                              <i>Solution: Correct the name to '{$row['name']}' OR update the customer details in the Service Center.</i>";
        } else {
            // ✅ MATCH! Update address
            $cust_id = $row['id'];
            mysqli_query($conn, "UPDATE customers SET address='$c_addr' WHERE id='$cust_id'");
        }
    } else {
        // ✅ NEW CUSTOMER
        mysqli_query($conn, "INSERT INTO customers (name, phone, address) VALUES ('$c_name', '$c_phone', '$c_addr')");
        $cust_id = mysqli_insert_id($conn);
    }

    // --- STEP 3: PROCESS SALE ---
    if($can_proceed) {
        
        // Fetch Bike Details
        $bike_res = mysqli_query($conn, "SELECT * FROM showroom_stock WHERE id='$stock_id'");
        $bike = mysqli_fetch_assoc($bike_res);
        
        $final_price = $bike['showroom_price'] + $rto + $ins;
        $sold_chassis = $bike['chassis_no']; 
        $sold_customer = $c_name;

        // Register in Service DB
        $temp_reg = "NEW-" . substr($bike['chassis_no'], -5);
        $model_name = $bike['model_name'];
        mysqli_query($conn, "INSERT INTO bikes (model_name, reg_number, customer_id) VALUES ('$model_name', '$temp_reg', '$cust_id')");

        // Record Sale (UPDATED SQL to use $pay_method)
        $sale_sql = "INSERT INTO bike_sales (customer_id, stock_id, final_price, payment_method) VALUES ('$cust_id', '$stock_id', '$final_price', '$pay_method')";
        
        if(mysqli_query($conn, $sale_sql)) {
            // SUCCESS!
            $new_sale_id = mysqli_insert_id($conn);
            $sale_successful = true; 

            // Mark as Sold
            mysqli_query($conn, "UPDATE showroom_stock SET status='Sold' WHERE id='$stock_id'");
        } else {
            $error_message = "Database Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        .success-box {
            background: #d4edda; border: 2px solid #c3e6cb; color: #155724;
            padding: 40px; border-radius: 10px; text-align: center; margin-top: 20px;
        }
        .action-btn {
            display: inline-block; padding: 12px 25px; margin: 15px 10px;
            text-decoration: none; color: white; font-weight: bold;
            border-radius: 5px; font-size: 16px; border: none; cursor: pointer;
        }
        .btn-print { background: #2980b9; box-shadow: 0 4px 0 #1f618d; }
        .btn-print:hover { background: #2471a3; transform: translateY(-2px); }
        
        .btn-home { background: #7f8c8d; box-shadow: 0 4px 0 #626d6e; }
        .btn-home:hover { background: #707b7c; transform: translateY(-2px); }

        .msg-box { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; text-align: center; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>💰 Sales Counter</h2>
    <a href="showroom_dashboard.php">🏠 Back to Dashboard</a>
</div>

<div class="content">
    <h2>📝 New Bike Sale Invoice</h2>

    <?php if($sale_successful): ?>
        
        <div class="success-box">
            <h1 style="margin-top:0;">🎉 Sale Successful!</h1>
            <p style="font-size:18px;">
                Bike Chassis <b><?php echo $sold_chassis; ?></b><br>
                Sold to <b><?php echo $sold_customer; ?></b>
            </p>
            <br>
            <a href="print_bill.php?id=<?php echo $new_sale_id; ?>" class="action-btn btn-print">🖨️ View & Print Bill</a>
            <a href="showroom_dashboard.php" class="action-btn btn-home">🏠 Back to Dashboard</a>
        </div>

    <?php else: ?>

        <?php if($error_message): ?>
            <div class="msg-box error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div style="display:flex; gap:20px;">
            <div style="flex:1; background:#fff; padding:30px; border:1px solid #ddd; border-radius:8px;">
                <form method="post">
                    
                    <h3 style="margin-top:0;">1. Select Vehicle Unit</h3>
                    <div style="background:#e8f8f5; padding:20px; border-radius:8px; border:1px solid #2ecc71; margin-bottom:25px;">
                        <p style="margin:0 0 10px 0; font-size:16px;">
                            Selling Model: <b><?php echo $model; ?></b> <br>
                            Color: <b><?php echo $color; ?></b>
                        </p>
                        
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Select Chassis Number:</label>
                        <select name="stock_id" required style="width:100%; padding:10px; border:2px solid #27ae60; border-radius:4px; font-size:14px; background:white;">
                            <option value="">-- Click here to Choose Chassis --</option>
                            <?php
                            $stock_query = "SELECT * FROM showroom_stock WHERE model_name='$model' AND color='$color' AND status='Available'";
                            $stock_res = mysqli_query($conn, $stock_query);
                            
                            if(mysqli_num_rows($stock_res) > 0) {
                                while($s = mysqli_fetch_assoc($stock_res)) {
                                    echo "<option value='{$s['id']}'>Chassis: {$s['chassis_no']} (Engine: {$s['engine_no']}) - ₹{$s['showroom_price']}</option>";
                                }
                            } else {
                                echo "<option value='' disabled>❌ No Stock Available!</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <h3>2. Customer Details</h3>
                    <div style="margin-bottom:15px;">
                        <input type="text" name="c_name" value="<?php echo isset($_POST['c_name']) ? $_POST['c_name'] : ''; ?>" placeholder="Customer Name" required style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ccc; border-radius:4px;">
                        <input type="text" name="c_phone" value="<?php echo isset($_POST['c_phone']) ? $_POST['c_phone'] : ''; ?>" placeholder="Phone Number" required style="width:100%; padding:10px; margin-bottom:10px; border:1px solid #ccc; border-radius:4px;">
                        <textarea name="c_address" placeholder="Full Address" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px; height:60px;"><?php echo isset($_POST['c_address']) ? $_POST['c_address'] : ''; ?></textarea>
                    </div>

                    <h3>3. Payment & Costs</h3>
                    <div style="display:flex; gap:10px; margin-bottom:15px;">
                        <input type="number" name="rto_cost" value="<?php echo isset($_POST['rto_cost']) ? $_POST['rto_cost'] : ''; ?>" placeholder="RTO Charges (₹)" required style="flex:1; padding:10px; border:1px solid #ccc; border-radius:4px;">
                        <input type="number" name="ins_cost" value="<?php echo isset($_POST['ins_cost']) ? $_POST['ins_cost'] : ''; ?>" placeholder="Insurance Cost (₹)" required style="flex:1; padding:10px; border:1px solid #ccc; border-radius:4px;">
                    </div>

                    <label style="display:block; margin-bottom:5px; font-weight:bold;">Payment Method:</label>
                    <select name="pay_method" style="width:100%; padding:10px; margin-bottom:25px; border:1px solid #ccc; border-radius:4px; background:white;">
                        <option value="Cash">Cash</option>
                        <option value="Online">Online (UPI/Card)</option>
                        <option value="Finance">Finance / Loan</option>
                    </select>

                    <button type="submit" name="confirm_sale" style="background:#27ae60; width:100%; font-size:18px; padding:15px; color:white; border:none; border-radius:5px; cursor:pointer;">✅ Confirm Sale</button>
                </form>
            </div>
        </div>

    <?php endif; ?>

</div>
</body>
</html>