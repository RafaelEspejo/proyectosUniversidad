<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


//CONTROLLER
$list = $db->getProductForSubcategory($_GET['id']);

//print_r($list);

//VIEW
if($list == 1)
{
    echo "<p>No tenemos productos de esta subcategoria, de momento.</p>";
}
else include __DIR__."/../view/search.php";

?>
