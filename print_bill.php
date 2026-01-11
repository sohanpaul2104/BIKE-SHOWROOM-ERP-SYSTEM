<?php 
include 'db.php'; 

$data = null;
$sale_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($sale_id > 0) {
    // Fetch Sale Details
    $sql = "SELECT s.*, c.name, c.phone, c.address, 
                   st.model_name, st.chassis_no, st.engine_no, st.color, st.showroom_price 
            FROM bike_sales s 
            JOIN customers c ON s.customer_id = c.id 
            JOIN showroom_stock st ON s.stock_id = st.id 
            WHERE s.id = '$sale_id'";
            
    $res = mysqli_query($conn, $sql);
    if(mysqli_num_rows($res) > 0) {
        $data = mysqli_fetch_assoc($res);
        
        // Calculate Prices
        $base_price = $data['showroom_price'];
        $total_price = $data['final_price'];
        $other_charges = $total_price - $base_price;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?php echo $sale_id; ?></title>
    <style>
        /* PAGE LAYOUT */
        body { 
            font-family: 'Helvetica', sans-serif; 
            background: #555; 
            display: flex; 
            flex-direction: column; /* Stack items vertically */
            align-items: center; 
            padding: 30px; 
            margin: 0;
        }

        /* THE PAPER BILL */
        .invoice-box { 
            background: white; 
            width: 210mm; 
            min-height: 297mm; 
            padding: 40px; 
            box-shadow: 0 0 15px rgba(0,0,0,0.3); 
        }
        
        /* BUTTON TOOLBAR (Now sits ABOVE the bill, not over it) */
        .actions { 
            width: 210mm; /* Same width as bill */
            display: flex; 
            justify-content: flex-end; /* Align to right */
            gap: 15px; 
            margin-bottom: 20px; 
        }

        .btn { 
            padding: 12px 25px; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            font-weight: bold; 
            cursor: pointer; 
            border: none; 
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }
        .btn-print { background: #2980b9; box-shadow: 0 4px 0 #1f618d; }
        .btn-print:active { transform: translateY(2px); box-shadow: 0 2px 0 #1f618d; }
        
        .btn-back { background: #7f8c8d; box-shadow: 0 4px 0 #626d6e; }
        .btn-back:active { transform: translateY(2px); box-shadow: 0 2px 0 #626d6e; }

        /* INVOICE STYLES */
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #333; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #333; }
        .shop-details { text-align: right; font-size: 14px; color: #555; }
        
        .info-section { display: flex; margin-bottom: 30px; }
        .box { flex: 1; }
        .box h3 { margin-top: 0; font-size: 16px; color: #333; border-bottom: 1px solid #ddd; padding-bottom: 5px; display: inline-block; }
        .box p { margin: 5px 0; font-size: 14px; color: #555; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #eee; text-align: left; padding: 10px; border: 1px solid #ddd; }
        td { padding: 10px; border: 1px solid #ddd; }
        .total-row td { border: none; font-weight: bold; font-size: 18px; text-align: right; padding-top: 20px; }
        
        .error-msg { text-align: center; color: #c0392b; margin-top: 100px; }

        /* PRINT MODE (Hides buttons, removes background) */
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .invoice-box { box-shadow: none; width: 100%; min-height: auto; margin: 0; padding: 0; }
            .actions { display: none !important; } /* Hide buttons */
        }
    </style>
</head>
<body>

<div class="actions">
    <a href="showroom.php" class="btn btn-back">⬅ Back to Stock</a>
    <button onclick="window.print()" class="btn btn-print">🖨️ Print Invoice</button>
</div>

<div class="invoice-box">
    
    <?php if($data): ?>
    
    <div class="header">
        <div class="logo">
            🏍️ YOUR BIKE SHOP NAME<br>
            <span style="font-size:14px; font-weight:normal;">Authorized Dealer</span>
        </div>
        <div class="shop-details">
            123 Main Street, City Name<br>
            Phone: +91 98765 43210<br>
            Email: sales@bikeshop.com
        </div>
    </div>

    <div style="text-align:center; margin-bottom:40px;">
        <h2>TAX INVOICE</h2>
        <p>Invoice No: <b>#INV-<?php echo str_pad($sale_id, 4, '0', STR_PAD_LEFT); ?></b> | Date: <?php echo date("d-M-Y", strtotime($data['sale_date'])); ?></p>
    </div>

    <div class="info-section">
        <div class="box">
            <h3>Billed To:</h3>
            <p><strong><?php echo $data['name']; ?></strong></p>
            <p><?php echo $data['address']; ?></p>
            <p>Phone: <?php echo $data['phone']; ?></p>
        </div>
        <div class="box" style="text-align:right;">
            <h3>Vehicle Details:</h3>
            <p>Model: <strong><?php echo $data['model_name']; ?></strong></p>
            <p>Color: <?php echo $data['color']; ?></p>
            <p>Chassis No: <?php echo $data['chassis_no']; ?></p>
            <p>Engine No: <?php echo $data['engine_no']; ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="width:150px; text-align:right;">Amount (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <b><?php echo $data['model_name']; ?> (<?php echo $data['color']; ?>)</b><br>
                    <small>Ex-Showroom Price</small>
                </td>
                <td style="text-align:right;"><?php echo number_format($base_price, 2); ?></td>
            </tr>
            <tr>
                <td>
                    RTO Registration & Insurance Charges<br>
                    <small>(Additional Services)</small>
                </td>
                <td style="text-align:right;"><?php echo number_format($other_charges, 2); ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td style="text-align:right;">GRAND TOTAL:</td>
                <td>₹<?php echo number_format($total_price, 2); ?></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 50px; font-size: 12px; color: #777;">
        <p><b>Terms & Conditions:</b></p>
        <ol>
            <li>Goods once sold will not be taken back.</li>
            <li>Warranty as per manufacturer terms.</li>
            <li>Subject to local jurisdiction.</li>
        </ol>
    </div>

    <div style="margin-top: 80px; display:flex; justify-content: space-between;">
        <div style="text-align:center;">
            _______________________<br>Customer Signature
        </div>
        <div style="text-align:center;">
            _______________________<br>Authorized Signatory
        </div>
    </div>

    <?php else: ?>
        <h2 class="error-msg">⚠️ Invoice Not Found!</h2>
        <p style="text-align:center; color:white;">Please check your Sales History.</p>
    <?php endif; ?>

</div>

</body>
</html>