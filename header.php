
  <nav class="navbar navbar-expand-sm color-blue sticky-top" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important; font-weight: bold !important; ">
        <div class="container-xxl align-items-center ">
            <a id="logo" class="navbar-brand mx-4 border text-dark bg-light" href="index.php">STI Wears</a>
            <div class="d-flex col-6 justify-content-end align-items-center">
                <p class=" col-auto m-0 h-100 align-items-center d-none d-sm-block me-3"> <?php echo $_SESSION['user']; ?> | <?php echo $_SESSION['section']; ?> </p>
                <a href="cart.php" class="btn btn-light col-3 px-1 mx-1 text-dark" style="min-width: 75px; max-width:75px; max-height: 50px;">Cart</a>
                <a href="function/logout_func.php" class="btn btn-warning col-3 px-1" style="min-width: 75px; max-width:75px; max-height: 50px;">Logout</a>

            </div>
        </div>
    </nav>