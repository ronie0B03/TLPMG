<!DOCTYPE html>
<html lang='en'>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="style.css">
        <title>Receipt example</title>
    </head>
<?php
    require_once 'dbh.php';

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
<body>
        <div class="ticket">
            <center>Toda La'el Pagibig Mini Grocery</center>
            <p class="centered">Official Receipt
                <!-- <br>Address line 1
                <br>Address line 2</p> -->
            <table>
                <thead>
                    <tr>
                        <th class="quantity">Q.</th>
                        <th class="description">Description</th>
                        <th class="price">₱₱</th>
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
                    <td><?php echo $newTransactionList['qty'].' pc(s)'; ?></td>
                    <td><?php echo strtoupper($itemName); ?></td>
                    <td>₱ <?php echo number_format($subTotal,2); ?></td>
                    </tr>
                    <?php
                            $total += $subTotal;
                            }
                    ?>
                </tbody>
                <tr>
                    <td colspan="3"><a style="float: right;">Total: ₱<?php echo $total; ?></a></td>
                </tr>
            </table>
            <p class="centered">Thank you for your purchase!</p>
            <br>
            <p class="centered">____________________</p>
        </div>
        <button id="btnPrint" class="hidden-print">Print</button>
        <script src="script.js"></script>
    </body>
<script>
    const $btnPrint = document.querySelector("#btnPrint");
    $btnPrint.addEventListener("click", () => {
    window.print();
    });
</script>
    <style>
    * {
    font-size: 12px;
    font-family: 'Times New Roman';
    }

    td,
    th,
    tr,
    table {
        border-top: 1px solid black;
        border-collapse: collapse;
    }

    td.description,
    th.description {
        width: 75px;
        max-width: 75px;
    }

    td.quantity,
    th.quantity {
        width: 40px;
        max-width: 40px;
        word-break: break-all;
    }

    td.price,
    th.price {
        width: 40px;
        max-width: 40px;
        word-break: break-all;
    }

    .centered {
        text-align: center;
        align-content: center;
    }

    .ticket {
        width: 155px;
        max-width: 155px;
    }

    img {
        max-width: inherit;
        width: inherit;
    }

    @media print {
        .hidden-print,
        .hidden-print * {
            display: none !important;
        }
    }
    </style>

</html>    
