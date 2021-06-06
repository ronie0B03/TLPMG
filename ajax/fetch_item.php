<?php
require_once('../dbh.php');


if(isset($_GET['getBarcode']))
{
    $data = [
        'success' => true, 
        'item'  => "", 
    ]; 

    if(!empty(trim($_GET['barcode'])))
    {
        $barcode = mysqli_real_escape_string($mysqli, trim($_GET['barcode']));
        $query   = mysqli_query($mysqli, "SELECT * FROM inventory WHERE barcode='$barcode'");
        
        if(mysqli_num_rows($query) > 0)
        {
            $item = $query->fetch_assoc(); 
            $data['item'] = $item;            
        }
        else 
        {
            $data['success'] = false; 
        }
    }
    else 
    {        
        $data['success'] = false; 
    }

    echo json_encode($data);
}