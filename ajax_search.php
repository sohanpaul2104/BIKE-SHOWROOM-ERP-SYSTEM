<?php
include 'db.php';

if(isset($_POST["query"])) {
    $search = mysqli_real_escape_string($conn, $_POST["query"]);
    
    // Fetch top 5 matching bikes
    $query = "SELECT id, reg_number, model_name FROM bikes WHERE reg_number LIKE '%$search%' LIMIT 5";
    $result = mysqli_query($conn, $query);
    
    if(mysqli_num_rows($result) > 0) {
        echo '<ul class="search-list">';
        while($row = mysqli_fetch_assoc($result)) {
            // PASS BOTH ID AND REG NUMBER to the Javascript function
            echo '<li onclick="selectBike(\''. $row["id"] .'\', \''. $row["reg_number"] .'\')">
                    <strong>'. $row["reg_number"] .'</strong> <br>
                    <small>'. $row["model_name"] .'</small>
                  </li>';
        }
        echo '</ul>';
    } else {
        echo '<div style="padding:10px; color:#721c24;">❌ Bike Not Found</div>';
    }
}
?>