<?php

//CONTROLLER

$chart = $db->showChart();
$shippings = $db->getShippingMethods();

$total = 0;

//VIEW
if(isset($_SESSION['id'])) include __DIR__."/../view/buy.php";
else 
{
    setcookie("alert", "Para comprar, porfavor, realiza el login o registrate!");
    header("location: ".URL);
}

