<?php 
include 'db.php'; 

$msg = "";
$msg_type = ""; 

// LOGIC: ADD NEW SUPPLIER
if(isset($_POST['add_supplier'])) {
    $name = mysqli_real_escape_string($conn, $_POST['sup_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['sup_contact']);
    
    $sql = "INSERT INTO suppliers (name, contact) VALUES ('$name', '$phone')";
    if(mysqli_query($conn, $sql)) {
        $msg = "✅ Supplier '{$name}' Added Successfully!";
        $msg_type = "success";
    } else {
        $msg = "❌ Error Adding Supplier.";
        $msg_type = "error";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Supplier Management</title>
    <style>
        .msg-box { padding: 15px; margin-bottom: 20px; border-radius: 5px; font-weight: bold; }
        .success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .form-box { background: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; border-top: 3px solid #8e44ad; margin-bottom: 30px; }
        .table-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>📦 Inventory System</h2>
    <a href="service_dashboard.php">⬅ Back to Service Panel</a>
    <a href="inventory.php">📦 Manage Stock</a>
    <a href="suppliers.php" style="background:#34495e;">🚚 Suppliers & Log</a>
</div>

<div class="content">
    <h2>🚚 Supplier Management & Purchase Log</h2>

    <?php if($msg != ""): ?> 
        <div class="msg-box <?php echo $msg_type; ?>"><?php echo $msg; ?></div> 
    <?php endif; ?>

    <div class="form-box">
        <h3 style="margin-top:0; color: #8e44ad;">➕ Register New Supplier</h3>
        <p style="font-size:12px; color:gray;"><i>Register new vendors here so they appear in the stock entry dropdown.</i></p>
        <form method="post" style="display:flex; gap:10px;">
            <input type="text" name="sup_name" placeholder="Supplier Name (e.g. Rahul Spares)" required style="flex:2;">
            <input type="text" name="sup_contact" placeholder="Phone Number" required style="flex:1;">
            <button type="submit" name="add_supplier" style="background: #8e44ad; flex:1;">Save Supplier</button>
        </form>
    </div>

    <div class="table-container">
        <h3>📜 Complete Purchase History (Batch Log)</h3>
        <p style="font-size:12px; margin-bottom:15px;"><i>This log tracks every specific batch of items added to the inventory.</i></p>
        
        <table>
            <tr style="background:#2c3e50; color:white;">
                <th>Date Added</th>
                <th>Supplier Name</th>
                <th>Supplier Phone</th>
                <th>Part Name</th>
                <th>Qty Added</th>
                <th>Price (At Purchase)</th>
            </tr>
            <?php
            // Query the purchase log (stock_entries)
            $query = "SELECT se.*, s.name as supplier_name, s.contact 
                      FROM stock_entries se 
                      JOIN suppliers s ON se.supplier_id = s.id 
                      ORDER BY se.entry_date DESC";
            $res = mysqli_query($conn, $query);
            
            if(mysqli_num_rows($res) > 0) {
                while($row = mysqli_fetch_array($res)) {
                    // Format date nicely
                    $date = date("d-M-Y h:i A", strtotime($row['entry_date']));
                    
                    echo "<tr>
                            <td>{$date}</td>
                            <td style='font-weight:bold; color:#2980b9;'>{$row['supplier_name']}</td>
                            <td>{$row['contact']}</td>
                            <td>{$row['part_name']}</td>
                            <td style='background:#e8f8f5; font-weight:bold;'>+ {$row['quantity']}</td>
                            <td>₹{$row['price']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center'>No purchase history yet.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>