<?php
require_once 'process_student.php';

include('sidebar.php');
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$getURI = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['getURI'] = $getURI;

if(isset($_GET['id'])){
    $applicationID = $_GET['id'];
    $classification = $_GET['classification'];
    if($classification=='student'){
        $getApplication = $mysqli->query("SELECT s.id, s.student_id, s.full_name, s.months, s.level, s.application_date, d.brand, d.model, d.price
        FROM student s
        JOIN devices d
        ON s.device_id = d.id
        WHERE s.id = '$applicationID' ") or die ($mysqli->error);
        $newApplication = $getApplication->fetch_array();
    }
    else{
        $getApplication = $mysqli->query("SELECT e.id, e.employee_id, e.full_name, e.months, e.application_date, d.brand, d.model, d.price
        FROM employee e
        JOIN devices d
        ON e.device_id = d.id
        WHERE e.id = '$applicationID' ") or die ($mysqli->error);
        $newApplication = $getApplication->fetch_array();
    }
    //Get Total Paid
    $getTotalPaid = $mysqli->query(" SELECT SUM(payment) as total_payment FROM payment WHERE class = '$classification' AND application_id = '$applicationID'  ") or die ($mysqli->error);
    $newTotalPaid = $getTotalPaid->fetch_array();
    $total_payment = $newTotalPaid['total_payment'];
    //Get total Months
    $getTotalMonthsPaid = $mysqli->query(" SELECT count(application_id) as total_months FROM payment WHERE class = '$classification' AND application_id = '$applicationID'  ") or die ($mysqli->error);
    $newTotalMonths = $getTotalMonthsPaid->fetch_array();
    $total_months = $newTotalMonths['total_months'];
    $months = $newApplication['months'];
    $remainining_months = $months - $total_months;

    //reamining balance
    $remainingprice = $newApplication['price'] - $total_payment;
    //amount due
    $amountdue = $remainingprice / $remainining_months;


    //Get Ledger Payments
    $getPayment = $mysqli->query(" SELECT * FROM payment WHERE class = '$classification' AND application_id = '$applicationID'  ") or die ($mysqli->error);
}

if(!isset($_GET['id'])){
?>
<meta http-equiv = "refresh" content = "0; url = index.php" />
<?php
}


?>
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
                <h1 class="h3 mb-0 text-gray-800"><?php echo strtoupper($classification); ?></h1>
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

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo $newApplication['full_name']; ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <form action="process_payment.php" method="post">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Device</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Remaining Balance</th>
                                        <th>Amount Due</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $newApplication['model']; ?></td>
                                        <td>₱ <?php echo  number_format($newApplication['price'],2);?></td>
                                        <td class="text-success">₱ <?php echo number_format($total_payment,2);?></td>
                                        <td class="text-danger">₱ <?php echo number_format($remainingprice,2);?></td>
                                        <td class="text-danger"><b>₱<?php echo number_format($amountdue, 2); ?></b></td>
                                        <td><input type="number" name="amount" min="0" max="<?php echo $remainingprice; ?>" min="0" placeholder="ex: <?php echo number_format($remainingprice); ?>" class="form-control" required></td>
                                    </tr>
                                </tbody>
                                <input type="text" name="id" value="<?php echo $applicationID; ?>" style="visibility: hidden;" readonly>
                                <input type="text" name="class" value="<?php echo $classification; ?>" style="visibility: hidden;" readonly>
                            </table>
                            <button type="submit" name="save" class="btn btn-sm btn-success mb-1 float-right">Add Payment</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account Ledger -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ledger Account - <?php echo $newApplication['full_name']; ?></h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="paymentTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                while($newPayment=$getPayment->fetch_assoc()){ ?>
                                <tr>
                                    <td><?php echo $newPayment['payment_date']; ?></td>
                                    <td>₱ <?php echo number_format($newPayment['payment'],2); ?></td>
                                </tr>
                                <?php
                                    $total = $total + $newPayment['payment'];
                                 } ?>
                                <tfoot>
                                <tr>
                                    <td><b class="float-right">TOTAL:</b></td>
                                    <td><b class="text-success">₱<?php echo number_format($total, 2); ?></b></td>
                                </tr>
                                </tfoot>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- End Student Lists -->

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- JS here -->
    <script type="text/javascript">
        $(document).ready(function() {
            $('#paymentTable').DataTable( {
                "pageLength": 25
            } );
        } );
    </script>
    <?php
    include('footer.php');
    ?>
    <style type="text/css">
        /*
        Max width before this PARTICULAR table gets nasty. This query will take effect for any screen smaller than 760px and also iPads specifically.
        */
        @media
        only screen
        and (max-width: 760px), (min-device-width: 768px)
        and (max-device-width: 1024px)  {

            /* Force table to not be like tables anymore */
            table, thead, tbody, th, td, tr {
                display: block;
            }

            thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                margin: 0 0 1rem 0;
            }

            tr:nth-child(odd) {
                padding: 1%;
                width: 100%;
                border-bottom: 2px solid grey;
                border-top: 2px solid grey;
                background: #fcfce3;
            }

            td {
                border-bottom: 1px solid #eee;
                position: relative;
            }

            td:before {
                top: 0;
                width: 45%;
                padding-right: 5%;
                white-space: nowrap;
            }

            /*
            Label the data
            You could also use a data-* attribute and content for this. That way "bloats" the HTML, this way means you need to keep HTML and CSS in sync.
            */
            td:nth-of-type(1):before { content: "ID:"; font-weight: bold;}
            td:nth-of-type(2):before { content: "Full Name:"; font-weight: bold; }
            td:nth-of-type(3):before { content: "Building Name:"; font-weight: bold; }
            td:nth-of-type(4):before { content: "Building Description:"; font-weight: bold; }
            td:nth-of-type(4):before { content: "Building Description:"; font-weight: bold; }

        }
    </style>
