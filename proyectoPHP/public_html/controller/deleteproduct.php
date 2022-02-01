<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

//CONTROLLER
$return = $db->deleteProductFromChat($_GET['id']);

if(is_array($return))
{
    //CONTROLLER
    $chart = $db->showChart();

    //VIEW
    include __DIR__."/../view/showchart.php";
} else echo "El carrito esta vacio!";
