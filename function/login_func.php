<?php
      if (isset($_POST["login"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        require_once "connect.php";
        $sql = "SELECT * FROM account WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
        if ($user) {
          if (password_verify($password, $user['password'])) {
            session_start();
            $_SESSION["account_id"] = $user["account_id"];
            $_SESSION["student_id"] = $user["student_id"];
            $_SESSION["student_email"] = $user["student_email"];
            $_SESSION["user"] = $user["full_name"];
            $_SESSION["section"] = $user["section"];

            header("Location: dashboard.php");

            die();
          } else {
            echo "<div class='alert alert-danger m-0'>Password doesn't match </div>";
          }
        } else {
          echo "<div class='alert alert-danger m-0'>Can't find user account </div>";
        }
      }
?>