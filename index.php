<?php
session_start();
include 'db.php';

if(isset($_POST['login'])) {
    $u = $_POST['username'];
    $p = $_POST['password'];
    $sql = "SELECT * FROM users WHERE username='$u' AND password='$p'";
    $result = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($result) > 0) {
        $_SESSION['user'] = $u;
        header("Location: dashboard.php");
    } else {
        echo "<script>alert('Invalid Username or Password');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head><link rel="stylesheet" href="style.css"></head>
<body style="display:flex; justify-content:center; align-items:center; padding-top:100px; ">
    <div style="background:white; padding:40px; border-radius:8px; width:300px; box-shadow:0 4px 10px rgba(0,0,0,0.1);">
        <h2 style="text-align:center; color:#2c3e50;">Admin Login</h2>
        <form method="post" style="box-shadow:none; padding:0;">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" style="width:100%;">Login</button>
        </form>
    </div>
</body>
</html>