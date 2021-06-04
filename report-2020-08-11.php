<?php
include('sidebar.php');
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

$getTransactions = mysqli_query($mysqli, "SELECT * FROM transaction WHERE transaction_date BETWEEN '$from_date' AND '$to_date' ");

?>
<title>Dashboard - Celine & Peter Store</title>
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
                        <table class="table table-bordered" id="dailyTransactoinTable">
                            <thead>
                            <tr>
                                <th>Control ID</th>
                                <th>Full Name</th>
                                <th>Total Amount</th>
                                <th>Amount Paid</th>
                                <th>Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php while($newTransactions=$getTransactions->fetch_assoc()){
                                $balance = $newTransactions['total_amount'] - $newTransactions['amount_paid'];
                                ?>
                                <tr>
                                    <td><a href="view_transaction.php?id=<?php echo $newTransactions['id']; ?>"><?php echo $newTransactions['id']; ?></a></td>
                                    <td><a href="view_transaction.php?id=<?php echo $newTransactions['id']; ?>"><?php echo $newTransactions['full_name']; ?></a></td>
                                    <td>₱ <?php echo number_format($newTransactions['total_amount'],2); ?></td>
                                    <td>₱<?php echo number_format($newTransactions['amount_paid'],2); ?></td>
                                    <td class="font-weight-bold" style="color: <?php if($balance<0){echo 'red';}else{echo 'green';} ?>">₱ <?php echo number_format($balance, 2); ?></td>
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
            $('#indexTransactionTable').DataTable( {
                "pageLength": 25
            } );
        } );
        $(document).ready(function() {
            $('#dailyTransactoinTable').DataTable( {
                "pageLength": 100
            } );
        } );
    </script>
    <?php
    include('footer.php');
    ?>

