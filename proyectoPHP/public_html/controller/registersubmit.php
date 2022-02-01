<?php

//CONTROLLER

$return = $db->registrar($_POST['name'], $_POST['password'], $_POST['email'], $_POST['phone'], $_POST['address'], $_POST['city'], $_POST['cp']);

//VIEW
switch($return)
{
    case 1:
        setcookie("alert", "Lo sentimos, pero el email ya existe. Porfavor, prueba con otro!");
        break;
    case 2:
        setcookie("alert", "Te has registrado correctamente. Para entrar, presiona el boton login y pon tus datos!");
        break;
    case 3:
        setcookie("alert", "Los datos introducidos no son validos. Porfavor, comprueba que tengan el formato correcto!");
        break;
    case 4:
        setcookie("alert", "Fallo al registrarte! Porfavor contacta con el administrador!");
        break;
}

header("location: index.php");
 
