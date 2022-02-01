<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//CONTROLLER

$product = $db->getProductFromID($_GET['productid']);

//VIEW
if($product == 1)
{
    echo "<p>No hemos podido encontrar el producto que deseas. Porfavor contacta con el administrador.</p>";
}elseif($product != 1 && $_SESSION['administrador'] == 1)
{
    include __DIR__."/../view/productAdmin.php";
}
else
{
    include __DIR__."/../view/product.php";
}

