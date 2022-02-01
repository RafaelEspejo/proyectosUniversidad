<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$subcategories = $db->getSubategorys($_GET['id']);

include __DIR__."/../view/subcategorySelector.php";