<?php
$student_ID = $student_email = $full_name = $section = $username = $password = $confirm_password = "";
$errors = [
    'student_ID' => '',
    'student_email' => '',
    'full_name' => '',
    'section' => '',
    'username' => '',
    'password' => '',
    'confirm_password' => '',
];

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

    if (empty($student_ID) || empty($student_email) || empty($full_name) || empty($section) || empty($username) || empty($password) || empty($confirm_password)) {
        array_push($errors, "All fields are required");
    }

    if (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
        $errors['student_email'] = "Email is not valid";
    } elseif (!preg_match('/@cubao\.sti\.edu\.ph$/', $student_email)) {
        $errors['student_email'] = "Only email addresses from @cubao.sti.edu.ph are allowed";
    }

    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long";
    }

    if (empty($student_ID)) {
        $errors['student_ID'] = "Student ID is required";
    } elseif (!preg_match('/^[0-9]{11}$/', $student_ID)) {
        $errors['student_ID'] = "Student ID must be exactly 11 characters long and can only contain numbers";
    }

    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Password doesn't match";
    }

    if (empty($section)) {
        $errors['section'] = "Section is required";
    } elseif (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z0-9 ]{6,12}$/', $section)) {
        $errors['section'] = "Double check your section, must include letters and numbers";
    }

    if (empty($full_name)) {
        $errors['full_name'] = "Full name is required";
    } elseif (count(explode(' ', $full_name)) < 2) {
        $errors['full_name'] = "Full name must have firstname and lastname";
    } elseif (preg_match('/\d/', $full_name)) {
        $errors['full_name'] = "Full name should not contain numbers";
    }

    if (empty($username)) {
        $errors['username'] = "Username is required";
    } elseif (!preg_match('/^[a-zA-Z0-9_]{6,20}$/', $username)) {
        $errors['username'] = "Username must be between 6 and 20 characters, and can only contain letters, numbers, and underscores";
    }

    require_once "connect.php";

    $sql = "SELECT * FROM account WHERE username = '$username'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $errors['username'] = "User already exists!";
    }

    $sql1 = "SELECT * FROM account WHERE student_ID = '$student_ID'";
    $result1 = mysqli_query($conn, $sql1);
    if (mysqli_num_rows($result1) > 0) {
        $errors['student_ID'] = "Student ID already exists! Message us for assistance.";
    }

    
    if (!array_filter($errors)) {
        $sql = "INSERT INTO account (student_id, username, password, full_name, section, student_email) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssss", $student_ID, $username, $password_hash, $full_name, $section, $student_email);
            mysqli_stmt_execute($stmt);
    
            // Display modal HTML
            echo '<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Notification</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Account created successfully
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Login</button>
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>';
    
            // JavaScript to trigger modal
            echo '<script>
                    var exampleModal = new bootstrap.Modal(document.getElementById("exampleModal"));
                    exampleModal.show();
                  </script>';
        } else {
            die("Something went wrong");
        }
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
    <style>
        .text-danger {
            color: red;
            /* Red color for error messages */
        }

        .small {
            font-size: 0.8rem;
            /* Smaller font size */
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 40px;
            /* Space for the eye icon */
        }

        .password-wrapper .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            user-select: none;
            /* Prevents text selection on click */
        }

        body {
            font-family: 'poppins', sans-serif;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-sm" style="background-color: #2198f4; color: #F0F0F0; font-weight: bold;">
        <div class="container-xxl align-items-center">
            <a id="logo" class="navbar-brand mx-4 px-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;" href="index.php">STI Wears</a>

        </div>
    </nav>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <form  method="POST" class="col-11 col-md-7 border p-md-5 p-3 bg-light-subtle shadow form">
                <label class="form-label">Student ID (02000363495)</label>
                <input type="number" class="form-control" name="student_ID" value="<?php echo $student_ID; ?>" />
                <div class="text-danger small mb-3"><?php echo $errors['student_ID']; ?></div>

                <label class="form-label">Student Email (juan.123456@cubao.sti.edu.ph)</label>
                <input type="email" class="form-control" name="student_email" value="<?php echo $student_email; ?>" />
                <div class="text-danger small mb-3"><?php echo $errors['student_email']; ?></div>

                <label class="form-label">Full name (Juan Luna)</label>
                <input type="text" class="form-control" name="full_name" value="<?php echo $full_name; ?>" />
                <div class="text-danger small mb-3"><?php echo $errors['full_name']; ?></div>

                <label class="form-label">Strand / Course (ITM 101 / BSIT 101)</label>
                <input type="text" class="form-control" name="section" value="<?php echo $section; ?>" />
                <div class="text-danger small mb-3 "><?php echo $errors['section']; ?></div>

                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" value="<?php echo $username; ?>" />
                <div class="text-danger small mb-3"><?php echo $errors['username']; ?></div>

                <label class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" name="password" />
                    <span class="toggle-password" onclick="togglePassword('password')"><i class="bi bi-eye"></i></span>
                </div>
                <div class="text-danger small mb-3"><?php echo $errors['password']; ?></div>

                <label class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" class="form-control" name="confirm_password" />
                    <span class="toggle-password" onclick="togglePassword('confirm_password')"><i class="bi bi-eye"></i></span>
                </div>
                <div class="text-danger small mb-3"><?php echo $errors['confirm_password']; ?></div>

                <hr>
                <input type="hidden" name="submit">
                <button type="submit" class="bg-success radius-sm text-light border-1 col-12 my-3 p-3" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Register
                </button>


                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="false">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Notification</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Account created succesfully
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Login</button>
                                <button type="button" class="btn btn-primary">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php include 'footer.php' ?>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <script>



        function togglePassword(inputId) {
            var passwordInput = document.getElementsByName(inputId)[0];
            var eyeIcon = passwordInput.nextElementSibling;

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<i class="bi bi-eye-slash"></i>'; // Change icon when password is visible
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<i class="bi bi-eye"></i>'; // Change back to eye icon
            }
        }
    </script>
</body>

</html>