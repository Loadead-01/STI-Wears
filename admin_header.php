<nav class="navbar navbar-expand-sm color-blue sticky-top col-12" style=" background-color: #2198f4 !important;  color: #F0F0F0 !important; font-weight: bold !important; ">
        <div class="container-xxl align-items-center ">
            <a id="logo" class="navbar-brand mx-4 border text-dark bg-light" href="index.php">STI Wears</a>
            <div class="d-flex col-6 justify-content-end align-items-center">
                <p class=" col-auto m-0 h-100 align-items-center d-none d-sm-block me-3"> <?php echo $_SESSION['admin']; ?> | <?php echo "admin"; ?> </p>
                
                <button class="btn btn-light col-3 px-1 me-2" style="min-width: 75px; max-width:75px; max-height: 50px;" type="offcanvas" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"  aria-controls="offcanvasExample"> Menu</button>
                <a href="function/logout_func.php" class="btn btn-warning col-3 px-1"  style="min-width: 75px; max-width:75px; max-height: 50px;">Logout</a>

            </div>
        </div>
    </nav>


<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasExampleLabel">Admin Panel Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body">
    <hr>
    <div  class="row gy-2 px-2">
    <a href="admin_dashboard.php" class="btn btn-primary" > Dashboard </a> 

        <a href="add_item.php" class="btn btn-primary" > Add new item </a> 
        <a href="stock_management.php" class="btn btn-primary" > Stock Management </a> <!-- Restock, Remove item -->
        <a href="#" class="btn btn-primary" > Sales Record </a> <!-- PAID ORDERS! Sales for each item (deduct based from order ID and quantity, ) -->
        <a href="add_item.php" class="btn btn-primary" > Order </a> <!-- Pending orders! -->
        <!-- cancelled orders dont need to get list? ig -->



    </div>
  </div>
</div>