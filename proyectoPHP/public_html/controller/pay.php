<?php

$return = $db->buyproducts($_POST['shipping'], $_POST['shippingcost']);

$db->deleteFullChart();

