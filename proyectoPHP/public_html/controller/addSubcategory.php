<?php

$return = $db->addSubcategory($_POST['categoryselector'], $_POST['subcategory']);

if($return) setcookie("alert", "Se ha añadido la subcategoria correctamente!");
else setcookie("alert", "No se ha podido añadir la subcategoria! Porfavor contacte con el administrador!");

header("Location: ".URL);

