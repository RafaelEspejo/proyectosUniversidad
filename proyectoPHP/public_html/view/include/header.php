<header>
    <nav>
        <div class="left">ComputerOnline </div>
        <input type="text" id="tosearch" /><input type="button" value="Buscar" id="searchbutton" />
        <div class="right">
            <ul>
                <?php if(!isset($_SESSION['id'])) { ?>
                <li><a href="#" id="loginbutton">Login</a></li>
                <li><a href="#" id="registerbutton">Registarse</a></li>
                <li><a href="#" id="showchartbutton">Carrito</a></li>
                <li>Soporte</li>
                <?php } elseif (isset($_SESSION['id']) && $_SESSION['administrador'] == 0) { ?>
                <li><a href="#" id="logoutbutton">Cerrar Session </a></li>
                <li><a href="#" id="ordersbutton">Pedidos </a></li>
                <li><a href="#" id="userbutton">Mi cuenta </a></li>
                <li><a href="#" id="usermenubutton"><?php $name = explode(' ', trim($_SESSION['nombre'])); echo $name[0]." ".$name[1]; ?> </a></li>
                <li><a href="#" id="showchartbutton">Carrito</a></li>
                <li>Soporte</li>
                <?php } elseif (isset($_SESSION['id']) && $_SESSION['administrador'] == 1) { ?>
                <li><a href="#" id="logoutbutton">Cerrar Session </a></li>
                <li><a href="#" id="ordersadminbutton">Pedidos </a></li>
                <li><a href="#" id="productformbutton">Productos </a></li>
                <li><a href="#" id="categorybutton">Categorias </a></li>
                <li><a href="#" id="userbutton">Mi cuenta </a></li>
                <li><a href="#" id="usermenubutton"><?php $name = explode(' ', trim($_SESSION['nombre'])); echo $name[0]." ".$name[1]; ?> </a></li>
                <?php } ?>
                
            </ul>
        </div>
        <div class="down">
            <ul>
                
            </ul>
        </div>
    </nav>
</header>