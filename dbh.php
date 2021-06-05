<?php
if (!isset($_SESSION)) {
    session_start();
}

$production = false;  

if($production)
{
    $host = 'localhost';
    $username = 'ronie';
    $password = 'ronie';
    $database = 'tlpmg';
}
else 
{
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'tlpmg';
}

$mysqli = new mysqli($host, $username, $password, $database) or die(mysqli_error($mysqli));

?>