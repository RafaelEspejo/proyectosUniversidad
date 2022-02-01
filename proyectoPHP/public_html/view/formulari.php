<?php
/**
 * @author Rafael Espejo Angulo
 */
include (__DIR__."/include/head.php"); 
include (__DIR__."/include/header.php");
?>
 <div id="registeruser">
    <form method="POST" action="index.php?action=registrar">
        <p></p>
        <label>Nombre:</label>
        <input type="text" name="name" class="input" pattern="[A-Za-z\s]+" required title="Porfavor, compruebe que contenga únicamente letras y espacios"> <!-- Este termino "\s]+" haze que se permitan espacios -->
        <p></p>
        <label>Contraseña:</label>
        <input type="password" name="password" class="input" pattern="[a-zA-Z0-9-]+" required title="Porfavor, compruebe que contenga únicamente letras y numeros">
        <p></p>
        <label>Email:</label>
        <input type="email" name="email" class="input" required title="Porfavor, compruebe que su correo sea válido">
        <p></p>
        <label>Telefono:</label>
        <input type="tel"  name="phone" class="input" required title="Porfavor, compruebe que el formato sea el correcto">
        <p></p>
        <label>Dirección:</label>
        <input type="text" pattern="[a-zA-Z0-9-\s]+" name="address" maxlength="30" class="input" required title="Porfavor, compruebe que contenga solo letras, espacios y numeros">
        <p></p>
        <label>Población:</label>
        <input type="text" pattern="[A-Za-z\s]+" name="city" maxlength="30" class="input" required title="Porfavor, compruebe que contenga únicamente letras y espacios">
        <p></p>
        <label>Codigo Postal:</label>
        <input type="text" pattern=".{4,}[0-9-]+" name="cp" maxlength="5" class="input" required title="Porfavor, compruebe que contenga 5 digitos y solo numeros"> <!-- El termino ".{4,}" indica que como MINIMO tiene que haver 4 + 1 cracteres -->
        <p></p>
        <input type="submit">
    </form>
</div>
<?php
include (__DIR__."/include/footer.php");
?>

