<?php

session_start();

if (isset($_SESSION["user"])) {
  header("Location: dashboard.php");
  exit();
}

if (isset($_SESSION["admin"])) {
  header("Location: admin_dashboard.php");
  exit();
}

?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>STI Wears</title>
  <link rel="stylesheet" href="style.css" />
  <!-- Bootsrap -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous" />
</head>

<body>
  <nav class="navbar navbar-expand-sm bg-light sticky-top">
    <div class="container-xxl">
      <a id="logo" class="navbar-brand mx-4" href="#">STI Wears</a>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- (Login/Signup) -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-2 ms-auto my-1 w-100 justify-content-end align-items-center">
          <li
            popovertarget="logins"
            class="col-12 col-sm-3 nav-item color-yellow rounded mb-2 mb-sm-0 d-md-none">
            <button class="nav-link btn text-dark w-100" popovertarget="logins">Login</button>
          </li>
          <li
            class="col-12 col-sm-3 nav-item color-blue rounded border button d-md-none ms-sm-4">
            <a class="nav-link btn text-light" href="signup.php">Sign up</a>
          </li>
        </ul>
      </div>
    </div>


  </nav>


  <div class="container-fluid bg-transparent border-0" popover id="logins">
    <div class="row justify-content-center bg-transparent">
      <div
        id="form-sec"
        class="col-8 col align-items-center justify-content-center p-0">
        <div>
          <form action="index.php" method="post" class="col-12 p-3 border bg-light">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required />
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required />
            <br />
            <input type="submit" value="login" name="login" class="btn color-yellow col-12">
          </form>
        </div>
      </div>
    </div>
  </div>

  <section id="intro">
    <div class="container-fluid p-0">



      <div class="row justify-content-center g-0">
        <!-- Image Section -->
        <div class="col-md-6 col-sm-12">
          <img src="assets/LP_1.png" alt="test" class="img-fluid" />
        </div>

        <!-- Content Section -->
        <div
          class="col-6 col align-items-center justify-content-center d-md-grid d-none p-0">

          <div>

            <?php include "function/login_func.php" ?>
            <form id="form" action="index.php" method="post" class="col-12 p-3 h-100  bg-light shadow">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" name="username" required />
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" name="password" required />
              <br />
              <input
                type="submit"
                value="Login"
                name="login"
                class="btn color-yellow col-12">

              <hr />
              <a href="signup.php" class="btn color-blue col-12">Sign up</a>
            </form>
            <div class="col-12 bg-light shadow bg-success mt-2 p-3">
              <a href="admin_login.php" class="btn bg-success text-light col-12">Admin Login</a>

            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="about" class="bg-light m-0 p-0 border">
    <h4 class="text-center">features</h4>
    <div class="container-fluid">
      <div class="row justify-content-center m-4 gy-4 gx-5">
        <div class="col-sm-3 width">
          <div class="card">
            <img src="assets/LP_2.png" class="card-img-top" alt="..." />
            <div class="card-body">
              <h5 class="card-title text-center testing">Order at ease</h5>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="card">
            <img src="assets/LP_2.png" class="card-img-top" alt="..." />
            <div class="card-body">
              <h5 class="card-title text-center">stock transparency</h5>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="card">
            <img src="assets/LP_1.png" class="card-img-top" alt="..." />
            <div class="card-body">
              <h5 class="card-title text-center">E-order form</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="guide" class="p-4">
    <h3 class="text-center">How to order</h3>
    <div class="container border border-danger p-3">
      <div class="row gy-3 m-4">
        <div class="col-12 border border-success">
          <p>step 1</p>
        </div>
        <div class="col-12 border border-success">
          <p>step 2</p>
        </div>
        <div class="col-12 border border-success">
          <p>step 3</p>
        </div>
        <div class="col-12 border border-success">
          <p>step 4</p>
        </div>
      </div>
    </div>
  </section>

  <section id="inventory" class="bg-dark p-5">
    <div class="container bg-light p-3">
      <div class="row justify-content-center my-3">
        <div class="col-sm-6 border p-3">
          <h2 class="lead fw-bold">Available</h2>
          <p class="text-lighten">
            lorem shshsbsbs jsbanahaka ajabaja aja a bahahaha
          </p>
        </div>
        <div class="col-sm-6 border p-3">
          <h2 class="lead fw-bold">Not Available</h2>
          <p class="text-lighten">
            lorem shshsbsbs jsbanahaka ajabaja aja a bahahaha
          </p>
        </div>
      </div>
    </div>
  </section>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>