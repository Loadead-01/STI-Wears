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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous" />

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'poppins', sans-serif;
    }

    ::-webkit-scrollbar {
      width: 0;
    }
  </style>

<body>
  <nav class="navbar navbar-expand-sm bg-light sticky-top border-bottom">
    <div class="container-xxl">
      <a id="logo" class="navbar-brand mx-4 px-2" style="background-color: #FFE10F !important; color: #0040b0 !important; font-weight: bold !important;" href="#">STI Wears</a>
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
      <div class="collapse navbar-collapse mt-3" id="navbarNav">
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

  <section id="about" class="bg-light m-0 p-3 border">
    <h4 class="text-center">features</h4>
    <div class="container-fluid">
      <div class="row justify-content-center m-2 gy-4 gx-5">
        <div class="col-sm-3 width">
          <div class="card">
            <img src="assets/order_at_ease.png" class="card-img-top img-fluid border" alt="..." />
            <div class="card-body">
              <h5 class="card-title text-center testing">Order at ease</h5>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="card">
            <img src="assets/stock_transparency.png" class="card-img-top img-fluid border" alt="..." />
            <div class="card-body">
              <h5 class="card-title text-center">stock transparency</h5>
            </div>
          </div>
        </div>
        <div class="col-sm-3">
          <div class="card">
            <img src="assets/order_form.png" class="card-img-top img-fluid border" alt="..." />
            <div class="card-body">
              <h5 class="card-title text-center">E-order form</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section id="guide" class="p-4">
    <h4 class="text-center">How to order</h4>
    <div class="container  p-3">
      <div class="row gy-3 m-2 text-center">
        <div class="col-12 my-4 bg-light p-2">
          <p class="m-0">step 1</p>
          <p class="m-0 text-black-50">Select products you want to order</p>

        </div>
        <div class="col-12 my-4 bg-light p-2">
          <p class="m-0">step 2</p>
          <p class="m-0 text-black-50">Choose payment method</p>
        </div>
        <div class="col-12 my-4 bg-light p-2">
          <p class="m-0">step 3</p>
          <p class="m-0 text-black-50">Proceed to cashier and provide your order details</p>
        </div>
        <div class="col-12 my-4 bg-light p-2 ">
          <p class="m-0">step 4</p>

          <p class="m-0 text-black-50">Claim your stuff at the proware</p>
        </div>
      </div>
    </div>
  </section>

  <section id="inventory" class="bg-dark p-5 m-0">
    <div class="container bg-light p-3 ">
      <div class="row justify-content-center my-3">
        <div class="col-sm-6 p-3 m-0">
          <h2 class="lead fw-bold border text-center bg-white shadow-sm p-3">Available</h2>
          <table class="table table-striped table-responsive shadow-sm">
            <tr>
              <th>Product Name</th>
              <th>Size</th>
            </tr>
            <?php
            require_once 'connect.php';
            $query = "SELECT i.product_name, id.size 
             FROM item i 
             JOIN item_details id ON i.product_id = id.item_id 
             WHERE id.stock > 0";
            $result = mysqli_query($conn2, $query);

            $products = array();
            while ($row = mysqli_fetch_assoc($result)) {
              if (!isset($products[$row['product_name']])) {
                $products[$row['product_name']] = array();
              }
              $products[$row['product_name']][] = $row['size'];
            }

            foreach ($products as $productName => $sizes) {
              echo "<tr>
            <td> $productName </td>
            <td> " . implode(', ', $sizes) . " </td>
            </tr>";
            }
            ?>
          </table>
        </div>

        <div class="col-sm-6 p-3 m-0">
          <h2 class="lead fw-bold border text-center bg-white shadow-sm p-3">Not Available</h2>
          <table class="table table table-striped table-responsive shadow-sm">
            <tr>
              <th>Product Name</th>
              <th>Size</th>
            </tr>
            <?php
            $query = "SELECT i.product_name, id.size 
             FROM item i 
             JOIN item_details id ON i.product_id = id.item_id 
             WHERE id.stock = 0";
            $result = mysqli_query($conn2, $query);

            $products = array();
            while ($row = mysqli_fetch_assoc($result)) {
              if (!isset($products[$row['product_name']])) {
                $products[$row['product_name']] = array();
              }
              $products[$row['product_name']][] = $row['size'];
            }
            foreach ($products as $productName => $sizes) {
              echo "<tr>
            <td> $productName </td>
            <td> " . implode(', ', $sizes) . " </td>
            </tr>";
            }
            ?>
          </table>
        </div>
      </div>
    </div>
  </section>
  <?php include 'footer.php' ?>

  <script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>