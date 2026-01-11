<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Service Entry</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* SEARCH BOX STYLES */
        .search-wrapper { position: relative; width: 100%; margin-bottom: 15px; }
        .search-box { width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; font-size: 16px; }
        
        /* DROPDOWN LIST STYLES */
        #bikeList {
            position: absolute; width: 100%; background: white; border: 1px solid #ddd;
            border-top: none; z-index: 1000; display: none; max-height: 200px; overflow-y: auto;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 0 0 6px 6px;
        }
        .search-list { list-style: none; padding: 0; margin: 0; }
        .search-list li { padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; color: #333; }
        .search-list li:hover { background: #f1f1f1; }
        .search-list small { color: #888; font-size: 12px; }
    </style>

    <script>
        // 1. LIVE SEARCH LOGIC
        $(document).ready(function(){
            $('#reg_input').keyup(function(){
                var query = $(this).val();
                if(query != '') {
                    $.ajax({
                        url: "ajax_search.php",
                        method: "POST",
                        data: {query: query},
                        success: function(data){
                            $('#bikeList').fadeIn();
                            $('#bikeList').html(data);
                        }
                    });
                } else {
                    $('#bikeList').fadeOut();
                }
            });

            // Hide list if clicked outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.search-wrapper').length) {
                    $('#bikeList').fadeOut();
                }
            });
        });

        // 2. SELECT BIKE FUNCTION (Fills the input & hidden ID)
        function selectBike(id, reg) {
            $('#reg_input').val(reg);       // Show Reg No to user
            $('#selected_bike_id').val(id); // Save ID for PHP form
            $('#bikeList').fadeOut();       // Hide list
        }

        // 3. ADD PARTS ROW LOGIC
        function addPartRow() {
            var container = document.getElementById("parts-container");
            var newDiv = document.createElement("div");
            newDiv.style.display = "flex";
            newDiv.style.gap = "10px";
            newDiv.style.marginBottom = "10px";
            newDiv.innerHTML = document.getElementById("part-template").innerHTML;
            container.appendChild(newDiv);
        }
    </script>
</head>
<body>

<div class="sidebar">
    <h2>🔧 Service Dept</h2>
    <a href="service_dashboard.php">⬅ Back to Dashboard</a>
</div>

<div class="content">
    
    <h2>Create Job Card</h2>
    <form method="post">
        
        <label>Search Bike (Registration No):</label>
        <div class="search-wrapper">
            <input type="text" id="reg_input" class="search-box" placeholder="Type Reg No (e.g. WB-01...)" autocomplete="off" required>
            
            <input type="hidden" name="bike_id" id="selected_bike_id" required>
            
            <div id="bikeList"></div>
        </div>

        <label>Issue Description:</label>
        <textarea name="details" placeholder="Service Issues (e.g. Engine noise, General Service)" required style="width:100%; height:80px; padding:10px; margin-bottom:15px; border-radius:6px; border:1px solid #ccc;"></textarea>
        
        <hr>
        
        <h3>Parts Used</h3>
        <div id="parts-container">
            <div style="display:flex; gap:10px; margin-bottom:10px;">
                <select name="part_id[]" style="flex:3; padding:10px; border-radius:6px; border:1px solid #ccc;">
                    <option value="">-- Select Part --</option>
                    <?php
                    // Fetch parts once
                    $parts_query = mysqli_query($conn, "SELECT * FROM parts WHERE stock > 0");
                    $parts_options = "";
                    while($p = mysqli_fetch_array($parts_query)) {
                        $parts_options .= "<option value='{$p['id']}'>{$p['part_name']} (₹{$p['price']})</option>";
                    }
                    echo $parts_options;
                    ?>
                </select>
                <input type="number" name="qty[]" placeholder="Qty" value="1" min="1" style="flex:1; padding:10px; border-radius:6px; border:1px solid #ccc;">
            </div>
        </div>

        <button type="button" onclick="addPartRow()" style="background:#f39c12; border:none; color:white; padding:8px 15px; margin-bottom:20px; font-size:13px; cursor:pointer; border-radius:4px;">➕ Add Another Part</button>
        
        <div id="part-template" style="display:none;">
            <select name="part_id[]" style="flex:3; padding:10px; border-radius:6px; border:1px solid #ccc;">
                <option value="">-- Select Part --</option>
                <?php echo $parts_options; ?>
            </select>
            <input type="number" name="qty[]" placeholder="Qty" value="1" min="1" style="flex:1; padding:10px; border-radius:6px; border:1px solid #ccc;">
        </div>

        <hr>
        
        <input type="number" name="labor_cost" placeholder="Labor/Service Charge (₹)" required style="width:100%; padding:12px; margin-bottom:15px; border-radius:6px; border:1px solid #ccc;">
        <button type="submit" name="save_service" style="background:#27ae60; color:white; padding:15px 30px; border:none; cursor:pointer; font-size:16px; border-radius:6px; width:100%;">💾 Generate Job Card</button>
    </form>

    <?php
    if(isset($_POST['save_service'])) {
        $bike_id = $_POST['bike_id']; // Got from Hidden Input
        $details = mysqli_real_escape_string($conn, $_POST['details']);
        $labor = $_POST['labor_cost'];
        
        // 1. Calculate Total Parts Cost
        $parts_total_cost = 0;
        
        // Safe Array Handling
        $part_ids = isset($_POST['part_id']) ? $_POST['part_id'] : [];
        $qtys = isset($_POST['qty']) ? $_POST['qty'] : [];

        for($i=0; $i < count($part_ids); $i++) {
            if(!empty($part_ids[$i])) {
                $pid = $part_ids[$i];
                $pqty = $qtys[$i];
                
                $price_res = mysqli_query($conn, "SELECT price FROM parts WHERE id='$pid'");
                $pr = mysqli_fetch_assoc($price_res);
                $parts_total_cost += ($pr['price'] * $pqty);
            }
        }

        // 2. Calculate Grand Total
        $total_pre_tax = $labor + $parts_total_cost;
        $gst = $total_pre_tax * 0.18;
        $grand_total = $total_pre_tax + $gst;

        // 3. Create Service Record
        $sql = "INSERT INTO services (bike_id, service_date, details, total_cost, gst_amount, status) 
                VALUES ('$bike_id', NOW(), '$details', '$grand_total', '$gst', 'Pending')";
        
        if(mysqli_query($conn, $sql)) {
            $service_id = mysqli_insert_id($conn); 
            
            // 4. Save Parts to Link Table (WITH ERROR CHECKING)
            for($i=0; $i < count($part_ids); $i++) {
                if(!empty($part_ids[$i])) {
                    $pid = $part_ids[$i];
                    $pqty = $qtys[$i];
                    
                    // Insert into Link Table
                    $insert_part = "INSERT INTO service_parts (service_id, part_id, quantity) VALUES ('$service_id', '$pid', '$pqty')";
                    if(!mysqli_query($conn, $insert_part)) {
                         die("❌ ERROR SAVING PART: " . mysqli_error($conn)); 
                    }

                    // Deduct Stock
                    mysqli_query($conn, "UPDATE parts SET stock = stock - $pqty WHERE id='$pid'");
                }
            }
            echo "<div class='success-box'>✅ Job Card Created Successfully! Total: ₹$grand_total</div>";
        } else {
            echo "<div class='msg-box error'>Error: " . mysqli_error($conn) . "</div>";
        }
    }
    ?>
    
    <hr style="margin: 40px 0;">
    
    <h3>🔧 Active Jobs (Mechanic's Panel)</h3>
    <p><i>Mechanics use this section to mark work as finished.</i></p>
    
    <table>
        <thead>
            <tr>
                <th>Job ID</th>
                <th>Bike</th>
                <th>Issue</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $res = mysqli_query($conn, "SELECT s.*, b.reg_number FROM services s JOIN bikes b ON s.bike_id = b.id WHERE status != 'Paid' ORDER BY s.id DESC");
        
        if(mysqli_num_rows($res) > 0) {
            while($row = mysqli_fetch_array($res)) {
                
                if($row['status'] == 'Pending') {
                    $status_label = "<span class='badge-working'>In Progress</span>";
                    $action_btn = "<form method='post' style='margin:0; padding:0; box-shadow:none; background:none;'>
                                    <input type='hidden' name='job_id' value='{$row['id']}'>
                                    <button type='submit' name='mark_work_done' class='action-btn' style='padding:6px 10px; font-size:12px;'>✅ Finish Work</button>
                                   </form>";
                } 
                elseif($row['status'] == 'Work Done') {
                    $status_label = "<span class='badge-done'>Ready for Bill</span>";
                    $action_btn = "<span style='color:#7f8c8d; font-size:12px;'>Waiting for Payment...</span>";
                }
                else {
                    $status_label = "<span>{$row['status']}</span>";
                    $action_btn = "-";
                }
                
                echo "<tr>
                        <td>#{$row['id']}</td>
                        <td><b>{$row['reg_number']}</b></td>
                        <td>{$row['details']}</td>
                        <td>{$status_label}</td>
                        <td>{$action_btn}</td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center; padding:20px; color:gray;'>No active jobs.</td></tr>";
        }
        ?>
        </tbody>
    </table>

    <?php
    if(isset($_POST['mark_work_done'])) {
        $id = $_POST['job_id'];
        mysqli_query($conn, "UPDATE services SET status='Work Done' WHERE id='$id'");
        echo "<script>window.location.href='service.php';</script>";
    }
    ?>
</div>
</body>
</html>