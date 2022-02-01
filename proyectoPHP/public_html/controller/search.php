<?php

//CONTROLLER
$list = $db->search($_GET['tosearch']);

//VIEW
if($list == 0)
{
    echo "<p>Ningun resultado. Prueba con otras busqudas</p>";
}
else include __DIR__."/../view/search.php"; 
    

