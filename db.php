<?php
$conn = mysqli_connect("localhost", "root", "", "bike_showroom",3308);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>