<?php
$student_ID = $student_email = $full_name = $section = $username = $password = $confirm_password = "";

session_start();

if (isset($_SESSION["user"])) {
    header("Location: index.php");
    exit();
}

if (isset($_POST["submit"])) {
    $student_ID = $_POST["student_ID"];
    $student_email = $_POST["student_email"];
    $full_name = $_POST["full_name"];
    $section = $_POST["section"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $errors = array();
    
    

    // Check for empty fields
    if (empty($student_ID) || empty($student_email) || empty($full_name) || empty($section) || empty($username) || empty($password) || empty($confirm_password)) {
        array_push($errors, "All fields are required");
    }

    // Validate email
    if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, "Email is not valid");
    }

    // Validate password length
    if (strlen($password) < 8) {
        array_push($errors, "Password must be at least 8 characters long");
    }

    // Validate student ID length
    if (strlen($student_ID) == 10) {
        array_push($errors, "Student ID must be 11 characters long");
    }

    // Validate password match
    if ($password !== $confirm_password) {
        array_push($errors, "Password doesn't match");
    }

    require_once "connect.php";
    $sql = "SELECT * FROM account WHERE username = '$username'"; //query
    $result = mysqli_query($conn, $sql); 
    $rowCount = mysqli_num_rows($result); // 
    if ($rowCount > 0) {
        array_push($errors, "User already exist!");
    }

    $sql1 = "SELECT * FROM account WHERE  student_ID = '$student_ID'"; //query
    $result1 = mysqli_query($conn, $sql1);
    $rowCount1 = mysqli_num_rows($result1); //

    if ($rowCount1 > 0) {
        array_push($errors, "Student ID already exist! Message us for assistance");

    }

    if (count($errors) > 0) {
        foreach ($errors as $error) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
    } else {
        require_once "connect.php";
        $sql = "INSERT INTO account (student_id, username, password, full_name, section, student_email) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        $prepareStmt = mysqli_stmt_prepare($stmt, $sql);
        if ($prepareStmt) {
            mysqli_stmt_bind_param($stmt, "ssssss", $student_ID, $username, $password_hash, $full_name, $section, $student_email);
            mysqli_stmt_execute($stmt);
            echo "<div class='alert alert-success'> account created successfully</div>";
        } else {
            die("something wnet wrong");
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign up</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous" />
</head>

<body>
    
  <nav class="navbar navbar-expand-sm color-blue sticky-top" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important; font-weight: bold !important; ">
        <div class="container-xxl align-items-center ">
            <a id="logo" class="navbar-brand mx-4 border text-dark bg-light" href="index.php">STI Wears</a>
        </div>
    </nav>



    <div class="container" style="margin-top: 20px">

        <div class="row justify-content-center">

            <form action="signup.php" method="POST" class="col-7 border p-5 bg-light-subtle shadow">
                <label class="form-label">Student ID (02000363495)</label>
                <input type="text" class="form-control" id="stud_ID" name="student_ID" value="<?php echo $student_ID ?>"/>
                <label class="form-label">Student Email (juan.123456@cubao.sti.edu.ph)</label>
                <input type="text" class="form-control" id="email" name="student_email" value="<?php echo $student_email ?>"/>
                <label class="form-label">Full name (Juan Luna)</label>
                <input type="text" class="form-control" id="name" name="full_name" value="<?php echo $full_name ?>"/>
                <label class="form-label">Strand / Course (ITM 101 / BSIT 101)</label>
                <input type="text" class="form-control" id="section" name="section" value="<?php echo $section ?>" />
                <label class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo $username ?>"/>
                <label class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" />
                <label class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" />
                <hr>
                <input type="submit" value="register" name="submit" class="bg-success radius-sm text-light border-1 col-12 my-3 p-3">
            </form>
        </div>
    </div>

    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>