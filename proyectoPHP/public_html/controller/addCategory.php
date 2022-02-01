<?php

$return = $db->addCategory($_POST['category']);

if($return) setcookie("alert", "Se ha añadido la categoria correctamente!");
else setcookie("alert", "No se ha podido añadir la categoria! Porfavor contacte con el administrador!");

header("Location: ".URL);
