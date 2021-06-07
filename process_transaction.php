<?php

include ('dbh.php');

$getURI = $_SESSION['getURI'];

$date = date_default_timezone_set('Asia/Manila');
$date = date('Y-m-d');

if(isset($_POST['add_item'])){
    $itemCtrl = $_POST['itemCtrl'];
    $itemCounter = ++$itemCtrl;
    while($itemCtrl!=0){
        $item = $_POST['item'.$itemCtrl];
        $getURI = $getURI.'&item'.$itemCtrl.'='.$item;

        $qty = $_POST['qty'.$itemCtrl];
        $getURI = $getURI.'&qty'.$itemCtrl.'='.$qty;

        $price = $_POST['price'.$itemCtrl];
        $getURI = $getURI.'&price'.$itemCtrl.'='.$price;

        $itemCtrl--;
    }
    $getURI = $getURI.'&itemCtrl='.$itemCounter;
    header("location: ".$getURI);
}

if(isset($_POST['add_item_barcode']))
{    
    $itemBarCodeCounter    = $_POST['itemBarCodeCtrl'];
    
    //* check if barcode exist 
    if(!empty(trim($_POST['itemBarCode'])))
    {
        $barcode = trim($_POST['itemBarCode']);
        $query   = mysqli_query($mysqli, "SELECT * FROM inventory WHERE barcode='$barcode'");
        
        if(mysqli_num_rows($query) > 0)
        {
            if(!empty(trim($_POST['qtyBarCode'])))
            {                
                
                $product = $query->fetch_assoc();
                
                $item   = $_POST['itemBarCode'];
                $getURI = $getURI.'&itemBarCode'.$itemBarCodeCounter.'='.$item;
                
                $qty    = $_POST['qtyBarCode'] ?? 1;
                $getURI = $getURI.'&qtyBarCode'.$itemBarCodeCounter.'='.$qty;
                
                $price  = $product['item_price'];
                $getURI = $getURI.'&priceBarCode'.$itemBarCodeCounter.'='.$price;
                
                $itemBarCodeCounter++; 
            }
            else 
            {
                $_SESSION['errors']  = "Quantity Item of Barcode: {$barcode} is 0";
            }
        }
        else 
        {
            $_SESSION['errors']  = "Item with Barcode: {$barcode} not found";
        }
    }
    
    $getURI = $getURI.'&itemBarCodeCtrl='.$itemBarCodeCounter;

    while($itemBarCodeCounter  !=  0)
    {
        if(isset($_POST['itemBarCode'.$itemBarCodeCounter]))
        {
            $item   = $_POST['itemBarCode'.$itemBarCodeCounter];
            $getURI = $getURI.'&itemBarCode'.$itemBarCodeCounter.'='.$item;
            
            $qty    = $_POST['qtyBarCode'.$itemBarCodeCounter];
            $getURI = $getURI.'&qtyBarCode'.$itemBarCodeCounter.'='.$qty;
    
            $price  = $_POST['priceBarCode'.$itemBarCodeCounter];
            $getURI = $getURI.'&priceBarCode'.$itemBarCodeCounter.'='.$price;
        }

        $itemBarCodeCounter--;
    } 
    
    
    //echo $getURI; 
    //echo '<br>' . $itemBarCodeCounter;
    header("location: ".$getURI); 
}

if(isset($_POST['save'])){

    
    if(isset($_POST['item_id']))
    {
        $countItems             = count($_POST['item_id']); 
        $customer_name          = mysqli_escape_string($mysqli, $_POST['full_name']);
        $customer_address       = mysqli_escape_string($mysqli, $_POST['address']);
        $customer_phone         = mysqli_escape_string($mysqli, $_POST['phone_num']);
        $customer_cash          = mysqli_escape_string($mysqli, $_POST['amount_paid']);
        $totalTransactionAmount = 0;
        $transactionID          = $_POST['transactionID'];
    
    
        for($i=0; $i<$countItems; $i++)
        {
           
            $item_id       = mysqli_escape_string($mysqli, $_POST['item_id'][$i]);
            $item_quantity = mysqli_escape_string($mysqli, $_POST['item_quantity'][$i]);
            $item_price    = mysqli_escape_string($mysqli, $_POST['item_price'][$i]);
            
            $subTotal =  $item_price *  $item_quantity;
    
            
            if($item_quantity > 0)
            {
                $mysqli->query("INSERT INTO transaction_lists 
                                    (transaction_id, item_id, qty, adjusted_price, transaction_date, subtotal) 
                                    VALUES('$transactionID', '$item_id', '$item_quantity', '$item_price','$date','$subTotal' )"
                                    ) or die(mysqli_error($mysqli)
                                );
        
                $getQtyInventory = mysqli_query($mysqli, "SELECT * FROM inventory WHERE id = '$item_id' ");
                $newQtyInventory = $getQtyInventory->fetch_array();
                $inventoryQty    = $newQtyInventory['qty'] - $item_quantity;
                $mysqli->query("UPDATE inventory SET qty='$inventoryQty' WHERE id='$item_id' ") or die(mysqli_error($mysqli));
            } 
    
            $totalTransactionAmount += $subTotal; 
        }
    
        
        $mysqli->query("INSERT INTO transaction 
                            (id, full_name, transaction_date, address, phone_num, total_amount, amount_paid) 
                            VALUES('$transactionID', '$customer_name', '$date', '$customer_address', '$customer_phone', '$totalTransactionAmount', '$customer_cash')") 
                            or die(mysqli_error($mysqli));
    
        $_SESSION['message']    = "Transaction has been saved!";
        $_SESSION['msg_type']   = "success";
        header('location: print_transaction.php?id='.$transactionID);
    }
    else 
    {
        $_SESSION['message']    = "No Items present for Transaction!";
        $_SESSION['msg_type']   = "danger";
        header('location: transactions.php'); 
    }


  /*   $itemCtrl = $_POST['itemCtrl'];
    $itemController = 1;
    $transactionID = $_POST['transactionID'];

    full_name = $_POST['full_name'];
    address = $_POST['address'];
    phone_num= $_POST['phone_num'];
    amount_paid = $_POST['amount_paid'];

    $total=0;
    while($itemCtrl!=0){
        $item = $_POST['item'.$itemCtrl];
        $qty = $_POST['qty'.$itemCtrl];
        $price = $_POST['price'.$itemCtrl];

        if($qty!=NULL){
            $subTotal = $price*$qty;
            $mysqli->query("INSERT INTO transaction_lists (transaction_id, item_id, qty, adjusted_price, transaction_date, subtotal) VALUES('$transactionID', '$item', '$qty', '$price','$date','$subTotal' )") or die($mysqli->error());
            //Update Inventory
            $getQtyInventory = mysqli_query($mysqli, "SELECT * FROM inventory WHERE id = '$item' ");
            $newQtyInventory = $getQtyInventory->fetch_array();
            $inventoryQty = $newQtyInventory['qty'] - $qty;
            $mysqli->query("UPDATE inventory SET qty='$inventoryQty' WHERE id='$item' ") or die ($mysqli->error());
        }

        echo $total += $subTotal;

        $itemController++;
        $itemCtrl--;
    }

    $mysqli->query("INSERT INTO transaction (id, full_name, transaction_date, address, phone_num, total_amount, amount_paid) VALUES('$transactionID', '$full_name', '$date', '$address', '$phone_num','$total', '$amount_paid' )") or die($mysqli->error());

    $_SESSION['message'] = "Transaction has been saved!";
    $_SESSION['msg_type'] = "success";

    header('location: transactions.php');  */
}

if(isset($_POST['update_payment'])){
    $transaction_id = $_POST['transaction_id'];
    $total_amount_paid = $_POST['total_amount_paid'];
    $pay_amount = $_POST['pay_amount'];

    $total_amount_paid = $total_amount_paid + $pay_amount;
    $mysqli->query("UPDATE transaction SET amount_paid='$total_amount_paid' WHERE id='$transaction_id' ") or die(mysqli_error($mysqli));

    $_SESSION['message'] = "Transaction has been updated!";
    $_SESSION['msg_type'] = "success";

    header('location: '.$getURI);
}

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $mysqli->query(" DELETE FROM transaction WHERE id = '$id' ") or die(mysqli_error($mysqli));
    $mysqli->query(" DELETE FROM transaction_lists WHERE transaction_id = '$id' ") or die(mysqli_error($mysqli));

    $_SESSION['message'] = "Transaction has been deleted!";
    $_SESSION['msg_type'] = "danger";

    header('location: transactions.php');
}


?>