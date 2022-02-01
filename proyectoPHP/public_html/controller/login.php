<?php

/**
 * @author Rafael Espejo Angulo
 */

//CONTROLLER
$return = $db->login($_POST['email'], $_POST['password']);

//VIEW
if ($return != 0) echo 1;
else echo 0;
?>

