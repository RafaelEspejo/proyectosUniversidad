<footer>
    <div class="left">
        <p>Quieres subscrivirte a nuestro boletin?</p>
        <p>Escribe tu correo electronico y disfruta de nuestras ofertas</p>
        <input type="text" value="email" />
        <input type="submit" value="Subscribirse" />
    </div>
    <div class="center">
        <p>Te gustan nuestros productos? Pues compartelos con tus amigos para que ellos puedan disfrutarlos!</p>
        <p>Estamos en la mayoria de redes soliales como</p>
        <ul>
            <li><a href="#" class="fa fa-facebook"></a></li>
            <li><a href="#" class="fa fa-twitter"></a></li>
            <li><a href="#" class="fa fa-linkedin"></a></li>
            <li><a href="#" class="fa fa-youtube"></a></li>
        </ul>
    </div>
    <div class="right">
        <p> Nos encontramos en: <br/>
            &nbsp;&nbsp;&nbsp;&nbsp; Campus de la UAB, Plaça Cívica, s/n<br />
            &nbsp;&nbsp;&nbsp;&nbsp; 08193 Bellaterra<br />
            &nbsp;&nbsp;&nbsp;&nbsp; Barcelona<br />
        </p>
    </div>
    <div class="copyright">
        &copy; 2017 Todos los derechos reservados TiendaOnline.com
    </div>
</footer>
<div id="loginuser">
    <form method="POST" action="login.php" >
        <input type="hidden" name="action" value="login">
        <div id="logintitle"><a href="#" id="loginclose">X</a></div>
        <p></p>
        <label>Usuario:</label>
        <input type="email" name="email" class="input" id="loginemail">
        <p></p>
        <label>Contraseña:</label>
        <input type="password" name="password" class="input" pattern="[a-zA-Z0-9-]+" id="loginpassword">
        <p></p>
        <input type="button" value="Entrar" id="loginsubmit">
    </form>
</div>
</body>
</html>