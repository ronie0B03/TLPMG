<?php
if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("location: login.php");
}

include('dbh.php');
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$getURI = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['getURI'] = $getURI;

$from_date = date_default_timezone_set('Asia/Manila');
$from_date = date('Y-m-d');
$to_date = $from_date;
if(isset($_GET['from_date'])){
    $from_date = $_GET['from_date'];
    $to_date = $_GET['to_date'];
}

$getTransactions = mysqli_query($mysqli, "SELECT i.item_name, i.item_code, i.market_original_price, i.item_description, SUM(tl.qty) AS qty, SUM(tl.subtotal) AS subtotal
FROM transaction_lists tl
LEFT JOIN inventory i
ON i.id = tl.item_id
WHERE tl.transaction_date BETWEEN '$from_date' AND '$to_date'
GROUP by i.id ");

?>
<!DOCTYPE html>
<html lang="en">
<title>Daily Sales Report with Income <?php echo $from_date.' to '.$to_date; ?></title>
<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
          rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link rel="icon" href="img/icon.ico" type="image/gif" sizes="16x16">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">


    <!-- required libraries -->
    <script src="libs/js/bootstrap.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <link href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css" rel="stylesheet">
    <!-- For Exporting -->
    <style>
        html{
            font-size: 0.9rem;
            scroll-behavior: smooth !important;
        }
        .bg-gradient-primary {
            background-color: #1e1e1f !important;
            background-image: none !important;
            background-image: none !important;
            background-size: cover !important;
        }
        ::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
            opacity: 0.7 !important; /* Firefox */
        }
    </style>

    <!-- For Exporting -->
    <script src="js/file_export/jquery-3.5.1.js"></script>
    <script src="js/file_export/jquery.dataTables.min.js"></script>
    <script src="js/file_export/dataTables.buttons.min.js"></script>
    <script src="js/file_export/buttons.flash.min.js"></script>
    <script src="js/file_export/jszip.min.js"></script>
    <script src="js/file_export/pdfmake.min.js"></script>
    <script src="js/file_export/vfs_fonts.js"></script>
    <script src="js/file_export/buttons.html5.min.js"></script>
    <script src="js/file_export/buttons.print.min.js"></script>

    <link href="js/file_export/buttons.dataTables.min.css" rel="stylesheet">
    <!-- For Exporting -->
</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

        <!-- Sidebar - Brand -->
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php">
            C&P Store
        </a>

        <!-- Divider -->
        <hr class="sidebar-divider my-0">

        <!-- Nav Item - Dashboard -->
        <li class="nav-item active">
            <a class="nav-link" href="index.php">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>Dashboard</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            Menu
        </div>

        <!-- Nav Item - Devices -->
        <li class="nav-item">
            <a class="nav-link" href="inventory.php">
                <i class="fas fa-laptop"></i>
                <span>Inventory</span></a>
        </li>

        <!-- Nav Item - Charts -->
        <li class="nav-item">
            <a class="nav-link" href="transactions.php">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Transactions</span></a>
        </li>

        <!-- Nav Item - Charts -->
        <li class="nav-item">
            <a class="nav-link" href="report.php">
                <i class="fas fa-fw fa-table"></i>
                <span>Daily Sales Report</span></a>
        </li>

        <!-- Nav Item - Charts -->
        <li class="nav-item">
            <a class="nav-link" href="report_earnings.php">
                <i class="fas fa-fw fa-table"></i>
                <span>DSR with Earnings</span></a>
        </li>

        <!-- Nav Item - Accounts -->
        <li class="nav-item" style="display: none;">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-table"></i>
                <span>Accounts</span></a>
        </li>

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

    </ul>
    <!-- End of Sidebar -->

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
                <h1 class="h3 mb-0 text-gray-800">Daily Sales Report</h1>
            </div>

            <!-- Student Employees -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Select Date</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form action="proces_report.php" method="post">
                            <table class="table" width="100%">
                                <thead>
                                <tr>
                                    <th width="40%">Select From Date</th>
                                    <th width="40%">Select To Date</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td><input type="date" class="form-control" name="from_date" value="<?php echo $from_date;?>" required></td>
                                    <td><input type="date" class="form-control" name="to_date" value="<?php echo $to_date;?>" required></td>
                                    <td><button type="submit" class="btn btn-sm btn-success float-left" name="get_date"><i class="fa fa-paper-plane" aria-hidden="true"></i> Send</button></td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Student Employees -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Report (<?php echo $from_date.' - '.$to_date; ?>)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered display" id="example">
                            <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Description</th>
                                <th>QTY</th>
                                <th>Amount</th>
                                <th>Earnings / Income</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while($newTransactions=$getTransactions->fetch_assoc()){
                                //$balance = $newTransactions['total_amount'] - $newTransactions['amount_paid'];
                                $market_price = $newTransactions['market_original_price'];
                                $total_released_capital = $market_price * $newTransactions['qty'];
                                $earnings = $newTransactions['subtotal'] - $total_released_capital;
                                ?>
                                <tr>
                                    <td><?php echo strtoupper($newTransactions['item_code']); ?></td>
                                    <td><?php echo strtoupper($newTransactions['item_name']); ?></td>
                                    <td><?php echo ucwords($newTransactions['item_description']); ?></td>
                                    <td><?php echo number_format($newTransactions['qty']); ?></td>
                                    <td>₱<?php echo number_format($newTransactions['subtotal'],2); ?></td>
                                    <td>₱<?php echo number_format($earnings,2); ?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Student Employees -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->
    <!-- JS here -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#example').DataTable( {
                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5',
                    'pdfHtml5',
                    'print'
                ]
            } );
        } );
    </script>
    <?php
    //include('footer.php');
    ?>

    <!-- Footer -->
    <footer class="sticky-footer bg-white">
        <div class="container my-auto">
            <div class="copyright text-center my-auto">
                <span>Copyright &copy; Celine and Peter Store <?php echo date("Y"); ?></span>
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

<!-- Page level plugins -->
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->

</body>

</html>


