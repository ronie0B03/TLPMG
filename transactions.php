<?php
require_once 'process_inventory.php';

include('sidebar.php');

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$getURI = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['getURI'] = $getURI.'?';

$getLastTransaction = mysqli_query($mysqli, "SELECT * FROM transaction");
$getTransaction = mysqli_query($mysqli, "SELECT * FROM transaction");

$lastTransactionID = 0;
while ($newLastTransaction = mysqli_fetch_array($getLastTransaction)) {
    $lastTransactionID = $newLastTransaction['id'];
}

if(!isset($_GET['itemCtrl'])){
    $itemCtrl = 1;
}
else{
    $itemCtrl = $_GET['itemCtrl'];
}


if(!isset($_GET['itemBarCodeCtrl']))
{
    $itemBarCodeCtrl = 0;
}
else{
    $itemBarCodeCtrl = $_GET['itemBarCodeCtrl'];
}

?>
<title>Transactions - Celine & Peter Store</title>

<link href="css/bootstrap.min.css" rel="stylesheet" />
<link href="css/bootstrap-select.min.css" rel="stylesheet" />
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
                <h1 class="h3 mb-0 text-gray-800">Transaction</h1>
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
            <?php if (isset($_SESSION['errors'])): ?>
                <div class="alert alert-danger alert-dismissible">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                    <?php
                    echo $_SESSION['errors'];
                    unset($_SESSION['errors']);
                    ?>
                </div>
            <?php endif ?>
            <!-- End Alert here -->

            <!-- Add Transaction -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Add Transaction</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form method="post" action="process_transaction.php">
                            <table id="table_items_barcode" class="table">
                                <thead>
                                    <tr>
                                        <th width="10%"></th>
                                        <th width="35%">Barcode</th>
                                        <th width="25%">Quantity</th>
                                        <th width="15%">Price</th>
                                        <th width="15%">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <input type="hidden" class='_input_id' value="">
                                            <div class='d-flex justify-content-center align-items-center border-none'>
                                                <button type="button" class="btn btn-success btn-sm _btn_add_item" disabled>Add Item</button>
                                            </div>
                                        </td>
                                        <td>
                                            <input  list="item_barcodes" class="form-control _input_barcode"  value="" placeholder="Barcode">
                                            <datalist id="item_barcodes">
                                                <?php
                                                    $items = mysqli_query($mysqli, "SELECT barcode FROM inventory");
                                                ?>
                                                <?php while($item=$items->fetch_assoc()): ?> 
                                                    <option value="<?=$item['barcode']?>">
                                                        <?=$item['barcode']?>
                                                    </option>
                                                <?php endwhile ?>
                                            </datalist>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control _input_quantity" value="0" placeholder="0" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control _input_price"    value="0" step="0.0001" placeholder="0.00" readonly >
                                        </td>
                                        <td><input class="form-control _input_subtotal"  value="0" readonly></td>
                                    </tr>
                                </tfoot>
                            </table>
                            <span class="float-right"><b>TOTAL: ₱ <span id="total_amount_barcode">0.00</span></b></span>
                                <br>
                                <br>
                                <br>
                                <br>
                                <table id="table_items" class="table">
                                    <thead>
                                        <tr>
                                            <th width="10%"></th>
                                            <th width="35%">Item</th>
                                            <th width="25%">Quantity</th>
                                            <th width="15%">Price</th>
                                            <th width="15%">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                                <input type="hidden" class='_input_id' value="">
                                                <div class='d-flex justify-content-center align-items-center border-none'>
                                                    <button type="button" class="btn btn-success btn-sm _btn_add_item" disabled>Add Item</button>
                                                </div>
                                            </td>
                                            <td>
                                                <select class="form-control  select-picker _select_item" data-live-search="true">
                                                    <option selected disabled>
                                                        Select Item
                                                    </option>
                                                    <?php
                                                        $getItemForAdding = mysqli_query($mysqli, "SELECT * FROM inventory");
                                                    ?>
                                                    <?php while($newItemsForAdding=$getItemForAdding->fetch_assoc()): ?> 
                                                            <option data-tokens="<?php echo strtoupper($newItemsForAdding['item_name']); ?>" class="" value="<?php echo $newItemsForAdding['id']; ?>">
                                                                <?php echo strtoupper($newItemsForAdding['item_code'].' - '.$newItemsForAdding['item_name']); ?>
                                                            </option>
                                                    <?php endwhile ?>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control _input_quantity"  value="0" placeholder="0" disabled>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control _input_price"  value="0" step="0.0001" placeholder="0.00" readonly>
                                            </td>
                                            <td><input class="form-control _input_subtotal" name="subTotal" value="0" readonly></td>
                                        </tr>
                                    </tfoot>
                                </table>
                                <span class="float-right"><b>TOTAL: ₱ <span id="total_amount">0.00</span></b></span>
                                <br>
                                <br>
                                <br>
                                <br>
                                <table class="table" width="100%" cellspacing="0">
                                    <thead>
                                    <tr>
                                        <th width="10%;">Control ID</th>
                                        <th width="">Full Name</th>
                                        <th width="">Address</th>
                                        <th width="">Phone Number</th>
                                        <th width="">Amount Paid</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" class="form-control" name="transactionID" value="<?php echo ++$lastTransactionID; ?>" readonly></td>
                                            <td><input type="text" class="form-control" name="full_name" placeholder="ex: Juan Crus" value="Juan Cruz"></td>
                                            <td>
                                                <textarea class="form-control" name="address" style="min-height: 100px;">Angeles City</textarea>
                                            </td>
                                            <td><input type="text" class="form-control" name="phone_num" placeholder="ex: 04876494843" value="09090912098"></td>
                                            <td><input type="number" step="0.01" class="form-control" name="amount_paid"  required></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <br/>
                                <center>
                                    <button class="btn btn-sm btn-primary m-1" name="save" type="submit"><i class="far fa-save" ></i> Save</button>
                                    <a href="transactions.php" class="btn btn-danger btn-sm m-1"><i class="fas as fa-sync"></i> Cancel</a>
                                </center>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Add Transaction -->

            <!-- List of Transactions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">List of Transactions</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="transactionTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Control ID</th>
                                    <th>Full Name</th>
                                    <th>Phone Num</th>
                                    <th>Total Amount</th>
                                    <th>Total Paid</th>
                                    <th>Total Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php while($newTransaction = $getTransaction->fetch_assoc()){
                                $balance = $newTransaction['amount_paid'] - $newTransaction['total_amount'];
                                ?>
                                <tr>
                                    <td><?php echo $newTransaction['transaction_date']; ?></td>
                                    <td><a href="view_transaction.php?id=<?php echo $newTransaction['id']; ?>" target="_blank"><?php echo $newTransaction['id']; ?></a></td>
                                    <td><a href="view_transaction.php?id=<?php echo $newTransaction['id']; ?>" target="_blank"><?php echo $newTransaction['full_name']; ?></a></td>
                                    <td><?php echo $newTransaction['phone_num']; ?></td>
                                    <td><?php echo '₱'.number_format($newTransaction['total_amount'],2); ?></td>
                                    <td><?php echo '₱'.number_format($newTransaction['amount_paid'],2); ?></td>
                                    <td style="color: <?php if($balance<0){echo 'red';}else{echo 'green';} ?>">
                                        <b><?php echo number_format($balance,2); ?></b>
                                    </td>
                                    <td>
                                        <!-- Start Drop down Delete here -->
                                        <button class="btn btn-danger btn-secondary dropdown-toggle btn-sm mb-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="far fa-trash-alt"></i> Delete
                                        </button>
                                        <div class="dropdown-menu p-1" aria-labelledby="dropdownMenuButton btn-sm">
                                            Are you sure you want to delete? You cannot undo the changes<br/>
                                            <a href="process_transaction.php?delete=<?php echo $newTransaction['id']; ?>" class='btn btn-danger btn-sm'>
                                                <i class="far fa-trash-alt"></i> Confirm Delete
                                            </a>
                                            <a href="#" class='btn btn-success btn-sm'><i class="far fa-window-close"></i> Cancel</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <!-- End Item Transactions -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- JS here -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#transactionTable').DataTable( {
                "pageLength": 25
            } );
        } );
    </script>

    <!-- JS here -->
    <script type="text/javascript">
        $(function() {
            $('.selectpicker').selectpicker();
        });
    </script>




<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Toda La'el Pagibig Mini Grocery <?php echo date("Y"); ?></span>
            <br>
            <br>
            <img src="img/logo.png" style="width: 50px;">
        </div>
    </div>
</footer>
<!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Scroll to Top Button-->
<a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
</a>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
            <div class="modal-footer">
                <button class="btn btn-sm btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                <a class="btn btn-sm btn-danger" href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Page Behaviour -->
<script src="./transaction_page.js"></script>

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>

<!-- Selector with search -->
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>



    <style type="text/css">
        .dropdown-menu{
            /*padding: 10px !important;*/
        }
        .topbar {
            /*height: 3rem !important; */
        }
        html{
            font-family: 'Roboto Condensed', sans-serif !important;
            font-size: 14px;
            scroll-behavior: smooth !important;
        }
        input.date{
            width: 10px;
        }

        #dataTable_wrapper,#fixtureTable_wrapper, #airconTable_wrapper, #forRepairTable_wrapper {
            width: 100% !important;
        }

        .bg-gradient-primary {
            background-color: #0f1e5d !important;
            background-image: none !important;
            background-image: none !important;
            background-size: cover !important;
        }
        .page-item.active .page-link {
            z-index: 1;
            color: #fff;
            background-color: #0f1e5d !important;
            border-color: #0f1e5d !important;
        }
        .container-fluid{
            background-color: white;
            /*padding-left: 5% !important;
            padding-right: 5% !important;*/
        }
        #content-wrapper{
            background-color: white !important;
        }
        ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            opacity: 0.7 !important; /* Firefox */
        }
        nav ul{
            position: sticky !important;
            top: 0;
            z-index: 99;
            white-space: normal;
        }
        nav ul li a{
            white-space: normal !important;
        }
    </style>
</body>

</html>

