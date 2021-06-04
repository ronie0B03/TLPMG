<?php
require_once 'process_inventory.php';

include('sidebar.php');

$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$getURI = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$_SESSION['getURI'] = $getURI.'?';

if(isset($_GET['id'])){
    $id = $_GET['id'];
}

$getTransaction = mysqli_query($mysqli, "SELECT * FROM transaction WHERE id = '$id' ");
$newTransaction = $getTransaction->fetch_array();

$balance = $newTransaction['amount_paid'] - $newTransaction['total_amount'];

$getTransactionLists = mysqli_query($mysqli, "SELECT * FROM transaction_lists WHERE transaction_id = '$id' ");
?>
<title>View Transaction - Celine & Peter Store</title>
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
            <!-- End Alert here -->

            <!-- View Individual Transactions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">View Transaction</h6>
                </div>
                <div class="card-body">
                    <span class="float-right">Control ID: <b><?php echo $id;?></b></span>
                    Customer Name: <b><?php echo $newTransaction['full_name']; ?></b>
                    <br>
                    Address: <b><?php echo $newTransaction['address']; ?></b>
                    <br>
                    Phone Number: <b><?php echo $newTransaction['phone_num']; ?></b>
                    <br>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $total = 0;
                            while ($newTransactionList=$getTransactionLists->fetch_assoc()){
                                $item_id = $newTransactionList['item_id'];
                                $getItem = mysqli_query($mysqli, "SELECT * FROM inventory WHERE id = '$item_id' ");
                                $newItem= $getItem->fetch_array();
                                $itemName = $newItem['item_name'];

                                $subTotal = $newTransactionList['adjusted_price'] * $newTransactionList['qty'];
                                ?>
                                <tr>
                                    <td><?php echo strtoupper($itemName); ?></td>
                                    <td><?php echo $newTransactionList['qty'].' pc(s)'; ?></td>
                                    <td>₱ <?php echo number_format($newTransactionList['adjusted_price'],2); ?></td>
                                    <td>₱ <?php echo number_format($subTotal,2); ?></td>
                                </tr>
                            <?php
                            $total += $subTotal;
                            } ?>
                            <tr>
                                <td colspan="3"><span class="float-right">Total:</span></td>
                                <td><span class="font-weight-bold">₱<?php echo number_format($total,2); ?></span></td>
                            </tr>
                            </tbody>
                        </table>

                        <span class="float-right"><h6><b>Total Amount Paid: ₱<?php echo number_format($newTransaction['amount_paid'],2); ?></b></h6></span>
                        <br>
                        <br>
                        <span class="float-right"><h6><b style="display: none; color: <?php if($balance<0){ echo 'red';}else{echo 'green';} ?>">Balance: <?php echo $balance; ?></b></h6></span>
                        <br>
                        <br>
                        <div style="display: none;">
                        <span class="float-right">Customer Signature: __________________________</span>
                        <br>
                        <br>
                        <span class="float-right">Sales Agent Signature: __________________________</span>
                        </div>

                        <?php if($balance<0){ ?>
                            <form method="post" action="process_transaction.php">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Control ID</th>
                                            <th>Total Amount Paid</th>
                                            <th>Pay Remaining Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><input type="text" name="transaction_id" class="form-control" value="<?php echo $id; ?>" readonly></td>
                                            <td><input type="text" name="total_amount_paid" class="form-control" value="<?php echo $newTransaction['amount_paid']; ?>" readonly></td>
                                            <td><input type="number" step="0.01" name="pay_amount" class="form-control" value="<?php echo $balance * (-1);?>"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="submit" name="update_payment" class="btn btn-sm btn-success float-right"><i class="far fa-save" ></i> Update</button>
                            </form>
                        <?php }  ?>
                        <center>
                            <a href="print_transaction.php?id=<?php echo $id; ?>" class="text-white btn btn-sm btn-info"><i class="fa fa-print" aria-hidden="true"></i> PRINT</a>
                        </center>

                    </div>
                </div>
            </div>
            <!-- End View Individual Transactions -->

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
    <?php
    include('footer.php');
    ?>
