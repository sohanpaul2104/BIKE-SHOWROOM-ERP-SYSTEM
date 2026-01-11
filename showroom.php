<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-box { background: #fff; padding: 20px; border-radius: 8px; border-top: 4px solid #8e44ad; margin-bottom: 30px; }
        .tab-btn { padding: 10px 20px; cursor: pointer; background: #eee; border: none; font-weight: bold; border-radius: 5px 5px 0 0; margin-right: 5px; }
        .active-tab { background: #8e44ad; color: white; }
        .hidden-form { display: none; }
        
        .sell-btn { background:#e67e22; color:white; border:none; padding:6px 15px; border-radius:4px; text-decoration:none; font-size:14px; font-weight:bold; }
        .qty-badge { background: #2c3e50; color: white; padding: 4px 12px; border-radius: 15px; font-weight: bold; font-size: 14px; }
        .suggestion { font-size: 11px; background: #d1ecf1; color: #0c5460; padding: 2px 6px; border-radius: 4px; margin-left: 5px; }
        .logout-btn { color: #e74c3c !important; margin-top: 40px; border-top: 1px solid #34495e; }
        .logout-btn:hover { background: #c0392b !important; color: white !important; }
        
    </style>
    <script>
        function showTab(tabName) {
            document.getElementById('single-form').style.display = 'none';
            document.getElementById('bulk-form').style.display = 'none';
            document.getElementById(tabName).style.display = 'block';
            
            document.getElementById('btn-single').classList.remove('active-tab');
            document.getElementById('btn-bulk').classList.remove('active-tab');
            
            if(tabName === 'single-form') { document.getElementById('btn-single').classList.add('active-tab'); }
            else { document.getElementById('btn-bulk').classList.add('active-tab'); }
        }
    </script>
</head>
<body>

<div class="sidebar">
    <h2>🏍️ Showroom</h2>
    <a href="dashboard.php">🏠 Main Menu</a>
    <a href="showroom_dashboard.php">🏠 Back to Dashboard</a>
    <a href="showroom.php" style="background:#34495e;">➕ Manage Stock (Add)</a>
    
    <a href="available_stock.php">📊 Available Stock</a>
    <a href="sold_bikes.php">📜 Sales History</a>
    <a href="index.php" class="logout-btn">🚪 Logout</a>
</div>

<div class="content">
    <h2>🏍️ Manage Showroom Inventory</h2>

    <?php
    // Get the very last entry to auto-fill details
    $last_query = mysqli_query($conn, "SELECT chassis_no, engine_no, model_name, color, purchase_price, showroom_price FROM showroom_stock ORDER BY id DESC LIMIT 1");
    
    // Default Values
    $s_c_prefix = ""; $s_c_start = "";
    $s_e_prefix = ""; $s_e_start = "";
    $last_model = ""; $last_color = ""; $last_buy = ""; $last_sell = "";

    if($row = mysqli_fetch_assoc($last_query)) {
        $last_model = $row['model_name'];
        $last_color = $row['color'];
        $last_buy = $row['purchase_price'];
        $last_sell = $row['showroom_price'];

        // Split "MBLHA1011" into "MBLHA" and "1012" (Next Number)
        if(preg_match('/^([^\d]*)(\d+)$/', $row['chassis_no'], $matches)) {
            $s_c_prefix = $matches[1];
            $s_c_start = $matches[2] + 1;
        }
        if(preg_match('/^([^\d]*)(\d+)$/', $row['engine_no'], $matches)) {
            $s_e_prefix = $matches[1];
            $s_e_start = $matches[2] + 1;
        }
    }
    ?>

    <div style="margin-bottom: 0;">
        <button id="btn-single" class="tab-btn active-tab" onclick="showTab('single-form')" style="color: black;">➕ Add Single Bike</button>
        <button id="btn-bulk" class="tab-btn" onclick="showTab('bulk-form')" style="color: black;">📦 Bulk Add (Next Batch)</button>
    </div>

    <div id="single-form" class="form-box">
        <h3 style="color: black;">Add Single Vehicle</h3>
        <form method="post">
            <input type="hidden" name="action_type" value="single">
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <input type="text" name="model" value="<?php echo $last_model; ?>" placeholder="Model Name" required style="flex:2;">
                <input type="text" name="color" value="<?php echo $last_color; ?>" placeholder="Color" required style="flex:1;">
            </div>
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <input type="text" name="chassis" value="<?php echo $s_c_prefix . $s_c_start; ?>" placeholder="Chassis No" required style="flex:1;">
                <input type="text" name="engine" value="<?php echo $s_e_prefix . $s_e_start; ?>" placeholder="Engine No" required style="flex:1;">
            </div>
            <div style="display:flex; gap:10px;">
                <input type="number" name="buy_price" value="<?php echo $last_buy; ?>" placeholder="Buy Price" required style="flex:1;">
                <input type="number" name="sell_price" value="<?php echo $last_sell; ?>" placeholder="Sell Price" required style="flex:1;">
            </div>
            <button type="submit" name="save_stock" style="background: #8e44ad; margin-top:15px; width:100%;">Add to Stock</button>
        </form>
    </div>

    <div id="bulk-form" class="form-box hidden-form">
        <h3 style="color: black;">📦 Bulk Add Sequential Stock</h3>
        <form method="post">
            <input type="hidden" name="action_type" value="bulk">
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <input type="text" name="model" value="<?php echo $last_model; ?>" placeholder="Model Name" required style="flex:2;">
                <input type="text" name="color" value="<?php echo $last_color; ?>" placeholder="Color" required style="flex:1;">
            </div>

            <div style="background:#f9f9f9; padding:10px; border:1px solid #ddd; margin-bottom:10px;">
                <label><strong>Chassis Series:</strong> <span class="suggestion">Auto-detected next start</span></label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="chassis_prefix" value="<?php echo $s_c_prefix; ?>" placeholder="Prefix (e.g. MBLHA)" required style="flex:1;">
                    <input type="number" name="chassis_start" value="<?php echo $s_c_start; ?>" placeholder="Start Number" required style="flex:1;">
                </div>
            </div>

            <div style="background:#f9f9f9; padding:10px; border:1px solid #ddd; margin-bottom:10px;">
                <label><strong>Engine Series:</strong></label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="engine_prefix" value="<?php echo $s_e_prefix; ?>" placeholder="Prefix (e.g. ENG)" required style="flex:1;">
                    <input type="number" name="engine_start" value="<?php echo $s_e_start; ?>" placeholder="Start Number" required style="flex:1;">
                </div>
            </div>

            <div style="display:flex; gap:10px;">
                <input type="number" name="qty" placeholder="Quantity (How many?)" required style="flex:1;">
                <input type="number" name="buy_price" value="<?php echo $last_buy; ?>" placeholder="Buy Price" required style="flex:1;">
                <input type="number" name="sell_price" value="<?php echo $last_sell; ?>" placeholder="Sell Price" required style="flex:1;">
            </div>
            <button type="submit" name="save_stock" style="background: #27ae60; margin-top:15px; width:100%;">🚀 Generate & Add Bulk Stock</button>
        </form>
    </div>

    <?php
    if(isset($_POST['save_stock'])) {
        $type = $_POST['action_type'];
        $model = mysqli_real_escape_string($conn, $_POST['model']);
        $color = mysqli_real_escape_string($conn, $_POST['color']);
        $buy = $_POST['buy_price'];
        $sell = $_POST['sell_price'];

        if($type == 'single') {
            $chassis = $_POST['chassis'];
            $engine = $_POST['engine'];
            
            // Check existence first
            $check = mysqli_query($conn, "SELECT id FROM showroom_stock WHERE chassis_no = '$chassis'");
            if(mysqli_num_rows($check) > 0) {
                echo "<div style='background:#f8d7da; color:#721c24; padding:15px; border-radius:5px; margin-bottom:20px;'>
                        ❌ Error: Chassis <b>$chassis</b> already exists!
                      </div>";
            } else {
                try {
                    mysqli_query($conn, "INSERT INTO showroom_stock (model_name, color, chassis_no, engine_no, purchase_price, showroom_price) VALUES ('$model', '$color', '$chassis', '$engine', '$buy', '$sell')");
                    echo "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px;'>
                            ✅ Single Bike Added Successfully!
                          </div>";
                } catch(Exception $e) {
                     echo "<div style='background:#fff3cd; color:#856404; padding:15px; margin-bottom:20px;'>⚠️ Duplicate Error.</div>";
                }
            }

        } elseif($type == 'bulk') {
            $c_prefix = $_POST['chassis_prefix']; $c_start = $_POST['chassis_start'];
            $e_prefix = $_POST['engine_prefix']; $e_start = $_POST['engine_start'];
            $qty = $_POST['qty'];
            
            $added = 0; $skipped = 0;

            for($i = 0; $i < $qty; $i++) {
                $cur_c = $c_prefix . ($c_start + $i);
                $cur_e = $e_prefix . ($e_start + $i);

                try {
                    $sql = "INSERT INTO showroom_stock (model_name, color, chassis_no, engine_no, purchase_price, showroom_price) 
                            VALUES ('$model', '$color', '$cur_c', '$cur_e', '$buy', '$sell')";
                    if(mysqli_query($conn, $sql)) { $added++; }
                } catch (Exception $e) {
                    $skipped++; // Catch duplicate error and continue
                }
            }
            
            echo "<div style='background:#d4edda; color:#155724; padding:15px; border-radius:5px; margin-bottom:20px;'>
                    <b>Batch Complete!</b><br>
                    ✅ Added: <b>$added</b> bikes.<br>
                    " . ($skipped > 0 ? "⚠️ Skipped (Duplicates): $skipped" : "") . "
                  </div>";
        }
    }
    ?>

    <!-- <hr style="margin:30px 0;"> -->

    

</div>
</body>
</html>