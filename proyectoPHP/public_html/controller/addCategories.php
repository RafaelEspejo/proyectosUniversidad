<?php

$categories = $db->getCategorys($ids);
$i = 0;

include __DIR__."/../view/addCategories.php";

