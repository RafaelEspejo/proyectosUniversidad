<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$return = $db->editProduct($_POST['nombre'], $_POST['cantidad'], $_POST['precio_recomendado'], $_POST['precio'], $_POST['descripcion'], $_POST['product_id'], $_FILES['image']);

setcookie("alert", $return);

header("Location: ".URL);