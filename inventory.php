<?php 
include 'db.php'; 

$msg = "";
$msg_type = ""; 

// ====================================================
//  PHP LOGIC
// ====================================================

// 1. ADD/UPDATE STOCK
if(isset($_POST['save_part'])) {
    $name = mysqli_real_escape_string($conn, $_POST['p_name']);
    $buying_price = $_POST['p_price']; // Renamed variable to avoid confusion
    $qty = $_POST['p_stock'];
    $sup_id = $_POST['supplier_id']; 

    // A. Update Master Inventory
    $check_query = mysqli_query($conn, "SELECT * FROM parts WHERE part_name = '$name'");
    
    if(mysqli_num_rows($check_query) > 0) {
        // --- CASE 1: ITEM EXISTS ---
        $row = mysqli_fetch_assoc($check_query);
        $new_stock = $row['stock'] + $qty;
        $id = $row['id'];
        
        // CRITICAL CHANGE: We only update STOCK. We do NOT update PRICE.
        // Your Selling Price remains whatever you set it to before.
        mysqli_query($conn, "UPDATE parts SET stock='$new_stock' WHERE id='$id'");
        
        $msg = "🔄 Stock Updated! Master Selling Price remains ₹{$row['price']}.";
    } else {
        // --- CASE 2: NEW ITEM ---
        // Since it's new, we set the Selling Price = Buying Price initially.
        // You can edit it later to add your profit margin.
        mysqli_query($conn, "INSERT INTO parts (part_name, price, stock) VALUES ('$name', '$buying_price', '$qty')");
        $msg = "✅ New Item Added! Initial Selling Price set to ₹$buying_price (Edit to change).";
    }
    $msg_type = "success";

    // B. Log to Purchase History
    // We record the BUYING PRICE here so you know what you paid Sohan/Rohan.
    mysqli_query($conn, "INSERT INTO stock_entries (part_name, supplier_id, quantity, price) VALUES ('$name', '$sup_id', '$qty', '$buying_price')");
}

// 2. UPDATE SELLING PRICE (Edit Mode)
if(isset($_POST['update_item'])) {
    $id = $_POST['u_id'];
    $name = $_POST['u_name'];
    $selling_price = $_POST['u_price']; // This is the actual SELLING price
    $alert = $_POST['u_alert'];
    
    // This is the ONLY place where the Master Price is updated
    mysqli_query($conn, "UPDATE parts SET part_name='$name', price='$selling_price', min_alert='$alert' WHERE id='$id'");
    
    header("Location: inventory.php?msg=updated");
    exit();
}

if(isset($_GET['msg'])) { $msg = "✏️ Selling Price & Details Updated!"; $msg_type = "success"; }
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Inventory Management</title>
    <style>
        .msg-box { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .form-box { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; border-top: 3px solid #27ae60; margin-bottom: 30px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>📦 Inventory System</h2>
    <a href="service_dashboard.php">⬅ Back to Service Panel</a>
    <a href="inventory.php" style="background:#34495e;">📦 Manage Stock</a>
    <a href="suppliers.php">🚚 Suppliers & Log</a>
</div>

<div class="content">
    <h2>📦 Master Inventory Stock</h2>

    <?php if($msg != ""): ?> <div class="msg-box <?php echo $msg_type; ?>"><?php echo $msg; ?></div> <?php endif; ?>

    <?php
    if(isset($_GET['edit_id'])) {
        $id = $_GET['edit_id'];
        $edit_res = mysqli_query($conn, "SELECT * FROM parts WHERE id='$id'");
        $edit_row = mysqli_fetch_assoc($edit_res);
    ?>
        <form method="post" style="background:#fff3cd; padding:20px; border:1px solid #e67e22; margin-bottom:20px; border-left: 5px solid #e67e22;">
            <h3 style="margin-top:0; color:#d35400;">✏️ Update Selling Price</h3>
            <p style="font-size:12px; color:#d35400;"><i>Set the price you charge customers here.</i></p>
            
            <input type="hidden" name="u_id" value="<?php echo $edit_row['id']; ?>">
            
            <label>Part Name:</label> 
            <input type="text" name="u_name" value="<?php echo $edit_row['part_name']; ?>" required>
            
            <div style="display:flex; gap:20px;">
                <div style="flex:1;">
                    <label>Selling Price (₹):</label>
                    <input type="number" name="u_price" value="<?php echo $edit_row['price']; ?>" required>
                </div>
                <div style="flex:1;">
                    <label>Min Alert Level:</label>
                    <input type="number" name="u_alert" value="<?php echo $edit_row['min_alert']; ?>" required>
                </div>
            </div>
            
            <button type="submit" name="update_item" style="background:#e67e22; margin-top:10px;">Update Selling Price</button>
            <a href="inventory.php" style="background:#7f8c8d; text-decoration:none; padding:10px 20px; color:white; font-size:14px; border-radius:4px; margin-left:10px;">Cancel</a>
        </form>
    <?php } else { ?>

        <div class="form-box">
            <h3 style="margin-top:0; color: #27ae60;">➕ Add New Stock</h3>
            <p style="font-size:12px; color:gray;"><i>Enter the <b>Buying Price</b>. This will NOT change your Selling Price.</i></p>
            <form method="post">
                <input type="text" id="p_name" name="p_name" list="part_list" placeholder="Search Part Name..." autocomplete="off" required>
                <datalist id="part_list">
                    <?php
                    $list_res = mysqli_query($conn, "SELECT part_name FROM parts");
                    while($row = mysqli_fetch_array($list_res)) {
                        echo "<option value='{$row['part_name']}'>";
                    }
                    ?>
                </datalist>

                <select name="supplier_id" required style="background: #e8f8f5;">
                    <option value="">-- Select Source Supplier --</option>
                    <?php
                    $sup_res = mysqli_query($conn, "SELECT * FROM suppliers");
                    if(mysqli_num_rows($sup_res) > 0) {
                        while($s = mysqli_fetch_array($sup_res)) { echo "<option value='{$s['id']}'>{$s['name']}</option>"; }
                    } else {
                        echo "<option value='' disabled>No Suppliers! Go to 'Suppliers & Log' page to add one.</option>";
                    }
                    ?>
                </select>

                <div style="display:flex; gap:10px;">
                    <div style="flex:1;">
                        <input type="number" name="p_price" placeholder="Buying Cost (₹)" required style="width:100%;">
                    </div>
                    <div style="flex:1;">
                        <input type="number" name="p_stock" placeholder="Qty" required style="width:100%;">
                    </div>
                </div>
                
                <button type="submit" name="save_part" style="background: #27ae60;">Add to Inventory</button>
            </form>
        </div>

        <h3>Current Master Stock (Selling Prices)</h3>
        <table>
            <tr>
                <th>Part Name</th>
                <th>Selling Price (₹)</th>
                <th>Total Stock</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php
            $res = mysqli_query($conn, "SELECT * FROM parts ORDER BY id DESC");
            while($row = mysqli_fetch_array($res)) {
                $status = ($row['stock'] < $row['min_alert']) ? "<span style='color:red; font-weight:bold;'>Low Stock</span>" : "<span style='color:green;'>In Stock</span>";
                
                echo "<tr>
                        <td>{$row['part_name']}</td>
                        <td>₹{$row['price']}</td>
                        <td>{$row['stock']}</td>
                        <td>{$status}</td>
                        <td>
                            <a href='inventory.php?edit_id={$row['id']}'>
                                <button style='background:#f39c12; padding:4px 8px; border:none; cursor:pointer; color:white; border-radius:3px;'>✏️ Set Price</button>
                            </a>
                        </td>
                      </tr>";
            }
            ?>
        </table>

    <?php } ?>

</div>
</body>
</html>