<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Service Billing & History</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .status-badge { padding: 5px 10px; border-radius: 15px; font-weight: bold; font-size: 12px; }
        .badge-ready { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .badge-paid { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        
        .btn-pay { background: #27ae60; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 13px; font-weight: bold; }
        .btn-pay:hover { background: #219150; }
        
        .btn-view { background: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 13px; font-weight: bold; }
        .btn-view:hover { background: #2980b9; }

        /* Search Bar Style */
        .search-box { padding: 10px; width: 300px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>💰 Billing Counter</h2>
    <a href="service_dashboard.php">⬅ Back to Dashboard</a>
    <a href="billing.php" class="active">📜 Billing History</a>
</div>

<div class="content">
    
    <div class="header-section">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h1 style="margin:0;">📜 Service History & Invoices</h1>
                <p style="margin:5px 0 0; color:gray;">Manage payments and print past invoices.</p>
            </div>
            
            <form method="get" style="margin:0; padding:0; background:none; box-shadow:none;">
                <input type="text" name="search" class="search-box" placeholder="🔍 Search Job ID or Bike No..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>
    </div>

    <?php
    if(isset($_POST['confirm_payment'])) {
        $pay_id = $_POST['job_id'];
        mysqli_query($conn, "UPDATE services SET status='Paid' WHERE id='$pay_id'");
        echo "<div class='success-box'>✅ Payment Collected! Invoice #$pay_id is now Paid.</div>";
    }
    ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Job ID</th>
                    <th>Date</th>
                    <th>Bike Reg No</th>
                    <th>Customer Name</th>
                    <th>Service Details</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Filter Logic
                $filter = "";
                if(isset($_GET['search']) && !empty($_GET['search'])) {
                    $s = mysqli_real_escape_string($conn, $_GET['search']);
                    $filter = "AND (b.reg_number LIKE '%$s%' OR s.id = '$s' OR c.name LIKE '%$s%')";
                }

                // Query: Fetch 'Work Done' (Ready for pay) AND 'Paid' (History)
                $sql = "SELECT s.*, b.reg_number, b.model_name, c.name as customer_name 
                        FROM services s 
                        JOIN bikes b ON s.bike_id = b.id 
                        JOIN customers c ON b.customer_id = c.id 
                        WHERE s.status IN ('Work Done', 'Paid') $filter
                        ORDER BY 
                            CASE WHEN s.status = 'Work Done' THEN 0 ELSE 1 END, /* Show Pending Payments First */
                            s.id DESC";
                
                $res = mysqli_query($conn, $sql);

                if(mysqli_num_rows($res) > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $date = date("d M Y", strtotime($row['service_date']));
                        
                        // Dynamic Status & Buttons
                        if($row['status'] == 'Work Done') {
                            $status_badge = "<span class='status-badge badge-ready'>⚠️ Pending Payment</span>";
                            $action = "<form method='post' style='margin:0; padding:0; background:none; box-shadow:none;'>
                                         <input type='hidden' name='job_id' value='{$row['id']}'>
                                         <button type='submit' name='confirm_payment' class='btn-pay'>💰 Collect ₹{$row['total_cost']}</button>
                                       </form>";
                        } else {
                            $status_badge = "<span class='status-badge badge-paid'>✅ Paid</span>";
                            // Links to the separate print page we created
                            $action = "<a href='print_service_bill.php?id={$row['id']}' class='btn-view'>🖨️ View Bill</a>";
                        }
                        
                        echo "<tr>
                                <td>#{$row['id']}</td>
                                <td>{$date}</td>
                                <td style='font-weight:bold; color:#2c3e50;'>{$row['reg_number']}</td>
                                <td>{$row['customer_name']} <br><small style='color:#7f8c8d;'>{$row['model_name']}</small></td>
                                <td>{$row['details']}</td>
                                <td style='font-weight:bold;'>₹" . number_format($row['total_cost']) . "</td>
                                <td>{$status_badge}</td>
                                <td>{$action}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align:center; padding:30px; color:#7f8c8d;'>No billing records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>