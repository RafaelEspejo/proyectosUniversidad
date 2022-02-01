<div id="registeruser">
    <form method="POST" action="index.php">
        <input type="hidden" value="modify" name="action">
        <p></p>
        <label>Nombre:</label>
        <input type="text" name="name" value="<?php echo $_SESSION['nombre']; ?>" id="registername" class="input" pattern="[A-Za-z\s]+" required title="Porfavor, compruebe que contenga únicamente letras y espacios"> <!-- Este termino "\s]+" haze que se permitan espacios -->
        <p></p>
        <label>Contraseña:</label>
        <input type="password" name="password" value="<?php echo $_SESSION['password']; ?>" id="registerpassword" class="input" pattern="[a-zA-Z0-9-]+" required title="Porfavor, compruebe que contenga únicamente letras y numeros">
        <p></p>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo $_SESSION['email']; ?>" id="registeremail" class="input" required title="Porfavor, compruebe que su correo sea válido">
        <p></p>
        <label>Telefono:</label>
        <input type="tel"  name="phone" value="<?php echo $_SESSION['telefono']; ?>" id="registerphone" class="input" required title="Porfavor, compruebe que el formato sea el correcto">
        <p></p>
        <label>Dirección:</label>
        <input type="text" pattern="[a-zA-Z0-9-\s]+" name="address" value="<?php echo $_SESSION['direccion']; ?>" id="registeraddress" maxlength="30" class="input" required title="Porfavor, compruebe que contenga solo letras, espacios y numeros">
        <p></p>
        <label>Población:</label>
        <input type="text" pattern="[A-Za-z\s]+" name="city" value="<?php echo $_SESSION['ciudad']; ?>" id="registercity" maxlength="30" class="input" required title="Porfavor, compruebe que contenga únicamente letras y espacios">
        <p></p>
        <label>Codigo Postal:</label>
        <input type="text" pattern=".{4,}[0-9-]+" name="cp" value="<?php echo $_SESSION['cp']; ?>" id="registercp" maxlength="5" class="input" required title="Porfavor, compruebe que contenga 5 digitos y solo numeros"> <!-- El termino ".{4,}" indica que como MINIMO tiene que haver 4 + 1 cracteres -->
        <p></p>
        <input type="submit" id="modifyusersubmit" value="Modificar">
    </form>
</div>