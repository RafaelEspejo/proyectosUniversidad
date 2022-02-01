<?php

//CONTROLLER

$return = $db->modifyUserInfo($_POST['name'], $_POST['password'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['city'], $_POST['cp']);

switch($return)
{
    case 1:
        setcookie("alert", "Los datos se han modificado correctamente!");
        break;
    case 2:
        setcookie("alert", "Error al modificar los datos! Porfavor contacta con el administrador.");
        break;
    case 3:
        setcookie("alert", "Los datos introducidos no son validos. Porfavor, comprueba que tengan el formato correcto!");
        break;
}

header("location: ".URL);


