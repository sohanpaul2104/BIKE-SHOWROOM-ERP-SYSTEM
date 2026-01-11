<?php 
include 'db.php'; 

// 1. Validation
if(!isset($_GET['id'])) { die("Error: Missing Invoice ID"); }

$service_id = mysqli_real_escape_string($conn, $_GET['id']);

// 2. FETCH DATA
$sql = "SELECT s.*, b.reg_number, b.model_name, c.name, c.phone, c.address 
        FROM services s 
        JOIN bikes b ON s.bike_id = b.id 
        JOIN customers c ON b.customer_id = c.id 
        WHERE s.id = '$service_id'";

$res = mysqli_query($conn, $sql);
if(!$res) { die("Query Failed: " . mysqli_error($conn)); } 
if(mysqli_num_rows($res) == 0) { die("Error: Invoice not found."); }

$data = mysqli_fetch_assoc($res);

// 3. FETCH PARTS
$parts_sql = "SELECT sp.quantity, p.part_name, p.price 
              FROM service_parts sp 
              JOIN parts p ON sp.part_id = p.id 
              WHERE sp.service_id = '$service_id'";
$parts_res = mysqli_query($conn, $parts_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?php echo $service_id; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        /* --- RESET & BASICS --- */
        body {
            background-color: #555; /* Dark background so paper stands out */
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* --- FIXED ACTION BAR (THE FIX) --- */
        .action-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.85); /* Dark bar */
            padding: 15px;
            display: flex;
            justify-content: center; /* Center the buttons */
            gap: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            z-index: 1000; /* FORCE ON TOP */
        }

        .btn {
            padding: 10px 25px;
            border-radius: 30px;
            text-decoration: none;
            color: white;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
            transition: transform 0.2s, background 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn:hover { transform: scale(1.05); }
        .btn-print { background-color: #3498db; }
        .btn-print:hover { background-color: #2980b9; }
        .btn-back { background-color: #95a5a6; }
        .btn-back:hover { background-color: #7f8c8d; }

        /* --- INVOICE CONTAINER --- */
        .invoice-container {
            background: white;
            width: 210mm; /* A4 standard */
            min-height: 297mm;
            margin: 80px auto 50px auto; /* Top margin pushes it below the button bar */
            padding: 50px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            position: relative;
            overflow: hidden;
            z-index: 1; /* Lower priority than buttons */
        }

        /* --- HEADER SECTION --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 30px;
            margin-bottom: 30px;
        }

        .company-branding h1 { margin: 0; font-size: 28px; color: #2c3e50; letter-spacing: -0.5px; }
        .company-branding p { margin: 5px 0 0; color: #7f8c8d; font-size: 14px; }

        .invoice-meta { text-align: right; }
        .invoice-meta h2 { margin: 0; font-size: 32px; color: #e74c3c; text-transform: uppercase; }
        .meta-data { margin-top: 10px; font-size: 14px; color: #555; }
        .meta-data span { font-weight: 600; color: #333; }

        /* --- PAID STAMP --- */
        .stamp {
            position: absolute;
            top: 40px; right: 40%;
            color: #27ae60;
            border: 4px solid #27ae60;
            font-size: 40px; font-weight: 700;
            padding: 10px 20px;
            text-transform: uppercase;
            border-radius: 10px;
            opacity: 0.2;
            transform: rotate(-15deg);
        }

        /* --- GRID & TABLES --- */
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .details-box h3 { font-size: 12px; text-transform: uppercase; color: #95a5a6; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 10px; }
        .details-box p { margin: 0; font-size: 15px; line-height: 1.6; }
        .details-box b { color: #2c3e50; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background-color: #2c3e50; color: white; padding: 12px 15px; text-align: left; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-bottom: 1px solid #f0f0f0; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        .qty-col { text-align: center; }
        .price-col { text-align: right; }

        /* --- TOTALS --- */
        .totals-wrapper { display: flex; justify-content: flex-end; }
        .totals-table { width: 300px; border-collapse: collapse; }
        .totals-table td { padding: 10px 0; border-bottom: 1px solid #eee; }
        .totals-table .label { color: #7f8c8d; font-size: 14px; }
        .totals-table .value { text-align: right; font-weight: 600; font-size: 15px; }
        .grand-total { font-size: 20px !important; color: #2c3e50; border-top: 2px solid #2c3e50; padding-top: 15px !important; }

        /* --- FOOTER --- */
        .footer { margin-top: 60px; text-align: center; font-size: 12px; color: #95a5a6; border-top: 1px solid #f0f0f0; padding-top: 20px; }

        /* --- PRINT MEDIA QUERY --- */
        @media print {
            body { background: white; padding: 0; }
            .invoice-container { box-shadow: none; margin: 0; width: 100%; padding: 20px; }
            .action-bar { display: none !important; } /* Hide buttons on paper */
            .stamp { opacity: 0.1; }
        }
    </style>
</head>
<body>

<div class="action-bar">
    <a href="billing.php" class="btn btn-back">⬅ Back to History</a>
    <a href="#" onclick="window.print()" class="btn btn-print">🖨️ Print Invoice</a>
</div>

<div class="invoice-container">
    
    <?php if($data['status'] == 'Paid'): ?>
        <div class="stamp">PAID</div>
    <?php endif; ?>

    <div class="header">
        <div class="company-branding">
            <h1>Authorized Service Center</h1>
            <p>123 Auto Market, Main Road, New Delhi - 110001</p>
            <p>📞 +91 98765 43210 &nbsp; | &nbsp; ✉️ support@servicecenter.com</p>
        </div>
        <div class="invoice-meta">
            <h2>INVOICE</h2>
            <div class="meta-data">
                Invoice #: <span><?php echo str_pad($data['id'], 6, '0', STR_PAD_LEFT); ?></span><br>
                Date: <span><?php echo date("d M Y", strtotime($data['service_date'])); ?></span><br>
                Job Status: <span><?php echo $data['status']; ?></span>
            </div>
        </div>
    </div>

    <div class="details-grid">
        <div class="details-box">
            <h3>Bill To:</h3>
            <p>
                <b><?php echo $data['name']; ?></b><br>
                <?php echo $data['address']; ?><br>
                Phone: <?php echo $data['phone']; ?>
            </p>
        </div>
        <div class="details-box">
            <h3>Vehicle Details:</h3>
            <p>
                Model: <b><?php echo $data['model_name']; ?></b><br>
                Reg Number: <b><?php echo $data['reg_number']; ?></b><br>
            </p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50%;">Description / Part Name</th>
                <th class="qty-col">Quantity</th>
                <th class="price-col">Unit Price</th>
                <th class="price-col">Total</th>
            </tr>
        </thead>
        <tbody>
            
            <?php
            // Calculate Parts Total First
            $parts_sum = 0;
            $parts_rows = [];
            
            if(mysqli_num_rows($parts_res) > 0) {
                while($p = mysqli_fetch_assoc($parts_res)) {
                    $parts_rows[] = $p;
                    $parts_sum += ($p['price'] * $p['quantity']);
                }
            }

            // Math: Total Bill - GST - Parts Cost = Labor Cost
            $total_excl_tax = $data['total_cost'] - $data['gst_amount'];
            $labor_cost = $total_excl_tax - $parts_sum;
            if($labor_cost < 0) $labor_cost = 0; // Prevent negative display
            ?>

            <tr style="background-color: #fcfcfc;">
                <td>
                    <b>Service Labor Charges</b><br>
                    <small style="color:#7f8c8d;"><?php echo $data['details']; ?></small>
                </td>
                <td class="qty-col">-</td>
                <td class="price-col">-</td>
                <td class="price-col"><b>₹<?php echo number_format($labor_cost, 2); ?></b></td>
            </tr>

            <?php foreach($parts_rows as $row): ?>
                <tr>
                    <td><?php echo $row['part_name']; ?></td>
                    <td class="qty-col"><?php echo $row['quantity']; ?></td>
                    <td class="price-col">₹<?php echo number_format($row['price'], 2); ?></td>
                    <td class="price-col">₹<?php echo number_format($row['price'] * $row['quantity'], 2); ?></td>
                </tr>
            <?php endforeach; ?>

            <?php if(empty($parts_rows)): ?>
                <tr>
                    <td colspan="4" style="text-align:center; color:#999; padding:10px;">(No Spare Parts used in this service)</td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>

    <div class="totals-wrapper">
        <table class="totals-table">
            <tr>
                <td class="label">Sub Total</td>
                <td class="value">₹<?php echo number_format($total_excl_tax, 2); ?></td>
            </tr>
            <tr>
                <td class="label">GST (18%)</td>
                <td class="value">₹<?php echo number_format($data['gst_amount'], 2); ?></td>
            </tr>
            <tr>
                <td class="label grand-total">Grand Total</td>
                <td class="value grand-total">₹<?php echo number_format($data['total_cost'], 2); ?></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>Terms: Warranty on parts is subject to manufacturer policy. Service warranty valid for 15 days.</p>
        <p><i>This is a computer-generated invoice.</i></p>
    </div>

</div>

</body>
</html>