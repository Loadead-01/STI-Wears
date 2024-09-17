<?php


session_start();

if (isset($_SESSION["admin"])) {
  header("Location: admin_dashboard.php");
  exit();
}

if (isset($_SESSION["user"])) {
    header("Location: dashboard.php");
    exit();
  }


require_once('connect.php'); // Ensure the database connection

if(isset($_POST["login"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    require_once ('connect.php');
    $sql = "SELECT * FROM admin_account.admin_acc WHERE username = '$username'";
    $result = $conn->query($sql);
    $admin = mysqli_fetch_array($result,  MYSQLI_ASSOC);
    if ($admin) {
        if ($password ==  $admin['password']) {
            session_start();
            $_SESSION['admin_id'] = $admin['ID'];
            $_SESSION['admin'] = $admin['name'];
            header("Location: admin_dashboard.php");
            die();
        } else {
            echo "<div class='alert alert-danger'>Username or Passsword doesn't match";
        }

    } else {
        echo "<div class='alert alert-danger'>Username or Passsword doesn't match";
    }


}







?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h1>Admin Login</h1>
    
    <form method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <input type="submit" value="login" name="login" class="btn btn-primary">
    </form>
</div>

</body>
</html>
