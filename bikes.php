<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-top: 20px; }
        .badge-working { background: #f39c12; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; } 
        .badge-done { background: #2980b9; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; } 
        .badge-unpaid { background: #e74c3c; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; } 
        .badge-paid { background: #27ae60; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px; } 
        
        /* Message Boxes */
        .msg-box { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; text-align: center; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Edit Boxes (Hidden by default) */
        .edit-box { background: #fff3cd; padding: 15px; margin-top: 10px; border: 1px solid #ffeeba; border-radius: 5px; display: none; }
        .cust-edit-box { background: #d1ecf1; padding: 15px; margin-top: 10px; border: 1px solid #bee5eb; border-radius: 5px; display: none; }
        
        .sm-btn { padding: 4px 10px; font-size: 12px; border: none; border-radius: 4px; cursor: pointer; color: white; margin-left: 10px; font-weight: bold; }
    </style>
    <script>
        function toggleEdit(id) {
            var x = document.getElementById("edit-reg-form-" + id);
            x.style.display = (x.style.display === "none") ? "block" : "none";
        }
        function toggleCustEdit(id) {
            var x = document.getElementById("edit-cust-form-" + id);
            x.style.display = (x.style.display === "none") ? "block" : "none";
        }
    </script>
</head>
<body>
<div class="sidebar">
    <h2>🔧 Service Center</h2>
    <a href="service_dashboard.php">⬅ Back to Service Panel</a>
    <a href="bikes.php" style="background:#34495e;">🔧 Find / Add Bike</a>
</div>

<div class="content">

    <?php
    if(isset($_POST['update_reg'])) {
        $bike_id = $_POST['bike_id'];
        $new_reg = mysqli_real_escape_string($conn, $_POST['new_reg_no']);
        
        // Safety Check: Is this new number already taken?
        $check = mysqli_query($conn, "SELECT * FROM bikes WHERE reg_number='$new_reg'");
        if(mysqli_num_rows($check) > 0) {
            echo "<div class='msg-box error'>❌ Error: Registration Number <b>$new_reg</b> is already assigned to another bike!</div>";
        } else {
            mysqli_query($conn, "UPDATE bikes SET reg_number='$new_reg' WHERE id='$bike_id'");
            echo "<div class='msg-box success'>✅ Success: Registration Updated to <b>$new_reg</b>!</div>";
        }
    }
    ?>

    <?php
    if(isset($_POST['update_customer'])) {
        $cust_id = $_POST['cust_id'];
        $new_name = mysqli_real_escape_string($conn, $_POST['new_name']);
        $new_addr = mysqli_real_escape_string($conn, $_POST['new_addr']);
        
        mysqli_query($conn, "UPDATE customers SET name='$new_name', address='$new_addr' WHERE id='$cust_id'");
        echo "<div class='msg-box success'>✅ Success: Customer details updated to <b>$new_name</b>!</div>";
    }
    ?>
    
    <?php
    if(isset($_POST['add_bike'])) {
        $c_name = mysqli_real_escape_string($conn, $_POST['c_name']);
        $c_phone = mysqli_real_escape_string($conn, $_POST['c_phone']);
        $c_address = mysqli_real_escape_string($conn, $_POST['c_address']);
        $b_model = mysqli_real_escape_string($conn, $_POST['b_model']);
        $b_reg = mysqli_real_escape_string($conn, $_POST['b_reg']);

        // --- SAFETY LOCK 1: CHECK IF BIKE REGISTRATION EXISTS ---
        // We join with customers table so we can tell you WHO owns it
        $check_bike = mysqli_query($conn, "SELECT b.*, c.name as owner_name 
                                           FROM bikes b 
                                           JOIN customers c ON b.customer_id = c.id 
                                           WHERE b.reg_number = '$b_reg'");
        
        if(mysqli_num_rows($check_bike) > 0) {
            $existing = mysqli_fetch_assoc($check_bike);
            echo "<div class='msg-box error'>
                    ⚠️ <b>Registration Exists!</b><br>
                    Bike Number <b>$b_reg</b> is already registered to <b>{$existing['owner_name']}</b>.<br>
                    Please check the number or search for it below.
                  </div>";
        } else {
            // --- SAFETY LOCK 2: CHECK CUSTOMER IDENTITY ---
            $check_cust = mysqli_query($conn, "SELECT id, name FROM customers WHERE phone = '$c_phone'");
            $cust_id = 0; 
            $can_proceed = false;
            $msg_extra = "";

            if(mysqli_num_rows($check_cust) > 0) {
                // Customer Exists: CHECK NAME MATCH
                $row = mysqli_fetch_assoc($check_cust);
                
                $existing_name = strtolower(trim($row['name']));
                $input_name = strtolower(trim($c_name));

                if($existing_name != $input_name) {
                    // ❌ STOP! Names are different.
                    echo "<div class='msg-box error'>
                            ⚠️ <b>Identity Mismatch!</b><br>
                            Phone <b>$c_phone</b> belongs to <b>{$row['name']}</b>.<br>
                            You entered <b>$c_name</b>.<br>
                            Please correct the name OR search and edit the customer first.
                          </div>";
                } else {
                    // ✅ MATCH! Proceed.
                    $cust_id = $row['id'];
                    $can_proceed = true;
                    $msg_extra = " (Linked to existing: {$row['name']})";
                    if(!empty($c_address)) { mysqli_query($conn, "UPDATE customers SET address='$c_address' WHERE id='$cust_id'"); }
                }

            } else {
                // New Customer -> Create
                mysqli_query($conn, "INSERT INTO customers (name, phone, address) VALUES ('$c_name', '$c_phone', '$c_address')");
                $cust_id = mysqli_insert_id($conn);
                $can_proceed = true;
                $msg_extra = " (New Customer Created)";
            }

            // --- FINAL STEP: INSERT BIKE ---
            if($can_proceed) {
                if(mysqli_query($conn, "INSERT INTO bikes (model_name, reg_number, customer_id) VALUES ('$b_model', '$b_reg', '$cust_id')")) {
                     echo "<div class='msg-box success'>✅ Success: Bike Registered! $msg_extra</div>";
                } else {
                     echo "<div class='msg-box error'>Error: ".mysqli_error($conn)."</div>";
                }
            }
        }
    }
    ?>

    <div style="background: #e8f6f3; padding: 20px; border-radius: 8px; margin-bottom: 30px; border: 1px solid #1abc9c;">
        <h2 style="margin-top:0;">🔍 Find Bike / Customer</h2>
        <p style="font-size:12px; color:gray;">Search by <b>Reg Number</b> (e.g. WB-02..) OR <b>Phone Number</b> (e.g. 98765..)</p>
        <form method="post">
            <input type="text" name="search_term" placeholder="Enter Reg No OR Phone No" required style="width: 70%; display: inline-block;">
            <button type="submit" name="search" style="width: 25%; display: inline-block;">Search</button>
        </form>
    </div>

    <?php
    if(isset($_POST['search'])) {
        $term = mysqli_real_escape_string($conn, $_POST['search_term']);
        
        $sql = "SELECT b.*, c.id as cust_id, c.name, c.phone, c.address 
                FROM bikes b 
                JOIN customers c ON b.customer_id = c.id 
                WHERE b.reg_number = '$term' OR c.phone = '$term'";
                
        $bike_query = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($bike_query) > 0) {
            while($bike = mysqli_fetch_assoc($bike_query)) {
                $bike_id = $bike['id'];
                $cust_id = $bike['cust_id'];
                ?>

                <div class="profile-card">
                    
                    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom: 2px solid #eee; padding-bottom: 10px;">
                        <h2 style="margin:0;">
                            <?php echo $bike['model_name']; ?> 
                            <span style="background:#34495e; color:white; padding:5px 10px; border-radius:5px; font-size:18px;">
                                <?php echo $bike['reg_number']; ?>
                            </span>
                        </h2>
                        <button onclick="toggleEdit(<?php echo $bike_id; ?>)" class="sm-btn" style="background:#f39c12;">✏️ Reg No</button>
                    </div>

                    <div id="edit-reg-form-<?php echo $bike_id; ?>" class="edit-box">
                        <form method="post" style="display:flex; gap:10px; align-items:center; margin:0;">
                            <b style="color:#856404;">New Plate Number:</b>
                            <input type="hidden" name="bike_id" value="<?php echo $bike['id']; ?>">
                            <input type="text" name="new_reg_no" placeholder="e.g. WB-20-XY-9999" required style="flex:1;">
                            <button type="submit" name="update_reg" style="background:#27ae60; width:auto; border:none; padding:5px 10px; color:white; border-radius:4px; cursor:pointer;">Save</button>
                        </form>
                    </div>

                    <div style="display: flex; gap: 30px; margin-top:10px; align-items: center; background:#f9f9f9; padding:10px; border-radius:5px;">
                        <div>
                            <strong>Owner:</strong> <?php echo $bike['name']; ?> 
                            <button onclick="toggleCustEdit(<?php echo $bike_id; ?>)" class="sm-btn" style="background:#17a2b8;">✏️ Edit Name</button>
                        </div>
                        <div><strong>Phone:</strong> <?php echo $bike['phone']; ?></div>
                    </div>

                    <div id="edit-cust-form-<?php echo $bike_id; ?>" class="cust-edit-box">
                        <form method="post">
                            <b style="color:#0c5460;">Update Customer Details:</b><br>
                            <input type="hidden" name="cust_id" value="<?php echo $cust_id; ?>">
                            <div style="display:flex; gap:10px; margin-top:5px;">
                                <input type="text" name="new_name" value="<?php echo $bike['name']; ?>" required style="flex:1;">
                                <input type="text" name="new_addr" value="<?php echo $bike['address']; ?>" placeholder="Address" style="flex:2;">
                                <button type="submit" name="update_customer" style="background:#17a2b8; color:white; border:none; padding:5px 15px; border-radius:3px; cursor:pointer;">Update</button>
                            </div>
                        </form>
                    </div>

                    <h3 style="margin-top: 20px; font-size:16px;">Service History</h3>
                    <table style="font-size:14px; width:100%; border-collapse:collapse; margin-top:10px;">
                        <tr style="background:#eee;">
                            <th style="padding:8px; border:1px solid #ddd;">Date</th>
                            <th style="padding:8px; border:1px solid #ddd;">Issue</th>
                            <th style="padding:8px; border:1px solid #ddd;">Cost</th>
                            <th style="padding:8px; border:1px solid #ddd;"> Work Status</th>
                            <th style="padding:8px; border:1px solid #ddd;">Payment Status</th>
                        </tr>
                        <?php
                        $history = mysqli_query($conn, "SELECT * FROM services WHERE bike_id = '$bike_id' ORDER BY id DESC");
                        if(mysqli_num_rows($history) > 0) {
                            while($h = mysqli_fetch_assoc($history)) {
                                $work = ($h['status'] == 'Pending') ? "<span class='badge-working'>In Progress</span>" : "<span class='badge-done'>Finished</span>";
                                $pay = ($h['status'] == 'Paid') ? "<span class='badge-paid'>Paid</span>" : "<span class='badge-unpaid'>Unpaid</span>";
                                echo "<tr>
                                        <td style='padding:8px; border:1px solid #ddd;'>{$h['service_date']}</td>
                                        <td style='padding:8px; border:1px solid #ddd;'>{$h['details']}</td>
                                        <td style='padding:8px; border:1px solid #ddd;'>₹{$h['total_cost']}</td>
                                        <td style='padding:8px; border:1px solid #ddd;'>{$work}</td>
                                        <td style='padding:8px; border:1px solid #ddd;'>{$pay}</td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding:10px;'>No history yet.</td></tr>";
                        }
                        ?>
                    </table>
                </div>

                <?php
            }
        } else {
            echo "<div class='msg-box error'>❌ No records found for '$term'</div>";
        }
    }
    ?>

    <hr style="margin: 40px 0;">

    <h3>➕ Register Manual Bike (Old / Outside)</h3>
    <form method="post" style="background:#fff; padding:20px; border-radius:8px; border:1px solid #ddd;">
        <div style="display:flex; gap:10px; margin-bottom:10px;">
            <input type="text" name="c_name" value="<?php echo isset($_POST['c_name']) ? $_POST['c_name'] : ''; ?>" placeholder="Customer Name" required style="flex:1;">
            <input type="text" name="c_phone" value="<?php echo isset($_POST['c_phone']) ? $_POST['c_phone'] : ''; ?>" placeholder="Phone Number" required style="flex:1;">
        </div>
        <div style="margin-bottom:10px;">
             <input type="text" name="c_address" value="<?php echo isset($_POST['c_address']) ? $_POST['c_address'] : ''; ?>" placeholder="Customer Address" style="width: 100%;">
        </div>
        <div style="display:flex; gap:10px;">
            <input type="text" name="b_model" value="<?php echo isset($_POST['b_model']) ? $_POST['b_model'] : ''; ?>" placeholder="Bike Model (e.g. Hero Splendor)" required style="flex:1;">
            <input type="text" name="b_reg" value="<?php echo isset($_POST['b_reg']) ? $_POST['b_reg'] : ''; ?>" placeholder="Registration No (e.g. WB-01-1234)" required style="flex:1;">
        </div>
        <button type="submit" name="add_bike" style="background:#34495e; margin-top:10px; width:100%;">Register Data</button>
    </form>

</div>
</body>
</html>