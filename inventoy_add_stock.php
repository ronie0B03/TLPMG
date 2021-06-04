<?php
require_once 'process_inventory.php';

include('sidebar.php');

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$getURI = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['getURI'] = $getURI;

$getLastItem = mysqli_query($mysqli, "SELECT * FROM inventory");
$lastItemID = 0;
while ($newLastItem = mysqli_fetch_array($getLastItem)) {
    $lastItemID = $newLastItem['id'];
}


if(!isset($_GET['item_id'])){
    ?>
<meta http-equiv = "refresh" content = "0; url = inventory.php" />
<?php
    }
    else{
        $item_id = $_GET['item_id'];
        $getItems = mysqli_query($mysqli, "SELECT * FROM inventory WHERE id = '$item_id' ");
        $newItems = $getItems->fetch_assoc();
    }
?>
<title>Add Stock - Celine & Peter Store</title>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <?php
        include('topbar.php');
        ?>
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h5 mb-0 text-gray-800">
                    <a href="inventory.php"><i class="fa fa-undo" aria-hidden="true"></i> Return to Inventory</a>
                </h1>
            </div>

            <!-- Alert here -->
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    ?>
                </div>
            <?php } ?>
            <!-- End Alert here -->

            <!-- Add Stock QTY -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add Stock</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form method="post" action="process_inventory.php">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th width="5%">Item ID</th>
                                    <th width="15%">Item Code</th>
                                    <th width="20%">Item Name</th>
                                    <th width="520">Current stock</th>
                                    <th width="10%">Total stocks to be added</th>
                                    <th width="10%">(₱) Market Original Price / Item</th>
                                    <th width="20%">(₱) Total Cost</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><input type="text" name="item_id" class="form-control" value="<?php echo $newItems['id']; ?>" required readonly></td>
                                    <td><?php echo $newItems['item_code']; ?></td>
                                    <td><input type="text" name="item_name" class="form-control" value="<?php echo $newItems['item_name']; ?>" required></td>
                                    <td><input type="number" class="form-control" name="old_stock" placeholder="0" value="<?php echo $newItems['qty']; ?>" readonly></td>
                                    <td><input type="number" class="form-control" name="new_stock" placeholder="0" required></td>
                                    <td><input type="number" class="form-control" step="0.01" name="market_price" placeholder="0" required></td>
                                    <td><input type="number" class="form-control" name="cost" placeholder="0" required></td>
                                </tr>
                                </tbody>
                            </table>
                            <button class="float-right btn btn-sm btn-primary m-1" name="add_stock" type="submit"><i class="far fa-save" ></i> Add / Update Stock</button>
                            <a href="<?php echo $getURI;?>" class="btn btn-danger btn-sm m-1 float-right"><i class="fas as fa-sync"></i> Cancel</a>
                        </form>

                    </div>
                    ***Note: <b>"Market Original Price / Item"</b> is needed to accurately calculate your earnings. Thank you
                </div>
            </div>
            <!-- End Add Stock QTY -->
            <!-- End Add Stock QTY -->


        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- JS here -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#studentTable').DataTable( {
                "pageLength": 25
            } );
        } );
    </script>
    <?php
    include('footer.php');
    ?>
