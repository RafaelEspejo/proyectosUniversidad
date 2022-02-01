<?php


class DB {
    
    private static $instancia = NULL;
    private $servername = NULL;
    private $user = NULL;
    private $password = NULL;
    private $datetable = NULL;
    private $conexion = NULL;

    function __construct() {
        $this->servername = SERVERNAME;
        $this->user = USER;
        $this->password = PASSWORD;
        $this->datetable = DATATABLE;
        $this->conexion = new mysqli($this->servername, $this->user, $this->password, $this->datetable);
        $this->conexion->set_charset("utf8"); 
        if ($this->conexion->connect_error) {
            die("Connection failed: " . $this->conexion->connect_error);
        }
    }
    public static function getInstance() {
        if (self::$instancia == NULL) {
            self::$instancia = new self();
        }
        return self::$instancia;
    }
    function __destruct() {
        mysqli_close($this->conexion);
    }

    private function consultas($sql) {
        return mysqli_query($this->conexion, $sql);
    }
    
    public function getCategorys(&$id_category = NULL) {
        $categoria = array();
        $subcategoria = array();
        $id_category = array();
        $aux = array();
        $sql = "SELECT name AS categoria, id FROM category";

        $result = $this->consultas($sql);

        for($i = 0; $row = mysqli_fetch_array($result); $i++) {
            $name = $row['categoria'];
            $id = $row['id'];
            $sql = "SELECT s.name AS subcategoria, s.id FROM category c, subcategory s WHERE c.id = s.category_id AND c.name = '$name'";
            $results = $this->consultas($sql);
            for ($j = 0; $s = mysqli_fetch_array($results); $j++) {
                $aux['id'] = $s['id'];
                $aux['subcategoria'] = $s['subcategoria'];
                $subcategoria[$j] = $aux;
            }
            $categoria[$name] = $subcategoria;
            $id_category[$i] = $id;
        }
        
        return $categoria;
    }
    
    public function getSubategorys($categoryID) {
        $subcategoria = array();
        $sql = "SELECT `name`, `id` FROM `subcategory` WHERE `category_id` = '$categoryID'";

        $result = $this->consultas($sql);

        while ($row = mysqli_fetch_array($result)) {
            $subcategoria[$row['name']] = $row['id'];
        }
        
        return $subcategoria;
    }

    public function registrar($name, $password, $email, $phone, $address, $city, $cp) {
        $checked = false;
        
        //Filtramos las variables para evitar ataques XSS
        $name = filter_var(htmlentities($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_STRING);
        $password = filter_var(htmlentities($password, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $phone = intval(filter_var(htmlentities($phone, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_NUMBER_INT));
        $address = filter_var(htmlentities($address, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_STRING);
        $city = filter_var(htmlentities($city, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_STRING);
        $cp = intval(filter_var(htmlentities($cp, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_NUMBER_INT));
        
        //Comprobamos que los datos sean integramente INT o EMAIL
        if(filter_var($email, FILTER_VALIDATE_EMAIL) 
            && filter_var($phone, FILTER_VALIDATE_INT) 
            && filter_var($cp, FILTER_VALIDATE_INT)) $checked = true; 

        //Si los datos son unicamene STRING, INT o EMAIL
        if($checked)
        {
            $query = "SELECT email FROM user WHERE email = '$email'";
            $results = $this->consultas($query);

            //Comprobamos que el email no exista
            if ($results->num_rows == 1)
                return 1;
            else {
                $query = "INSERT INTO user VALUES(NULL,?,?,?,?,?,?,?,0,1,1)";
                $sq = $this->conexion->stmt_init();
                $sq->prepare($query);
                $sq->bind_param("sssssss", $name, password_hash($password, PASSWORD_DEFAULT), $email, $phone, $address, $city, $cp);
                if($sq->execute()) return 2;
                else return 4;
            }
        } else return 3;
    }

    public function login($email, $password) {
        $query = "SELECT password, administrador FROM user WHERE email=?";
        
        $sq = $this->conexion->stmt_init();
        $sq->prepare($query);
        $sq->bind_param("s", $email);
        $sq->execute();
        $sq->bind_result($pass, $isAdmin);
        $sq->fetch();
        
        if (password_verify($password, $pass)) {
            $query = "SELECT id, name, phone, address, city, postal_code, status, active FROM user WHERE email=?";
            $sq->prepare($query);
            $sq->bind_param("s", $email);
            $sq->execute();
            $sq->bind_result($id, $nombre, $telefono, $direccion, $ciudad, $cp, $estado, $activo);
            $sq->fetch();
            $_SESSION['id'] = $id;
            $_SESSION['password'] = $password;
            $_SESSION['email'] = $email;
            $_SESSION['nombre'] = $nombre;
            $_SESSION['telefono'] = $telefono;
            $_SESSION['direccion'] = $direccion;
            $_SESSION['ciudad'] = $ciudad;
            $_SESSION['cp'] = $cp;
            $_SESSION['estado'] = $estado;
            $_SESSION['activo'] = $activo;
            $_SESSION['administrador'] = $isAdmin;
            return 1;
        } else {
            return 0;
        }
    }

    public function getProductForSubcategory($subcategoria) {
        $result = array();
        $dades = array();
        $aux = array();
        $query = "SELECT id, name AS nombre, price AS precio, quantity AS cantidad, img AS imagen, description AS descripcion,"
                . "recommendedprice AS precio_recomendado, active AS activo FROM product WHERE subcategory_id=?";

        $sq = $this->conexion->stmt_init();
        $sq->prepare($query);
        $sq->bind_param("s", $subcategoria);
        $sq->execute();
        $sq->bind_result($dades['id'], $dades['nombre'], $dades['precio'], $dades['cantidad'], $dades['imagen'], $dades['descripcion'], $dades['precio_recomendado'], $dades['activo']);

        for ($i = 0; $sq->fetch(); $i++) {
            $aux['id'] = $dades['id'];
            $aux['nombre'] = $dades['nombre'];
            $aux['precio'] = $dades['precio'];
            $aux['cantidad'] = $dades['cantidad'];
            $aux['imagen'] = $dades['imagen'];
            $aux['descripcion'] = $dades['descripcion'];
            $aux['precio_recomendado'] = $dades['precio_recomendado'];
            $aux['activo'] = $dades['activo'];
            $result[$i] = $aux;
        }

        if ($i > 0)
            return $result;
        else
            return 0;
    }

    public function getProductFromID($id) {
        $product = array();
        $product['id'] = $id;
        $query = "SELECT name, price, quantity, img, description, recommendedprice, active FROM product WHERE id=?";
        $sq = $this->conexion->stmt_init();
        $sq->prepare($query);
        $sq->bind_param("i", $id);
        $sq->execute();
        $sq->store_result();
        if ($sq->num_rows != 0) {
            $sq->bind_result($product['nombre'], $product['precio'], $product['cantidad'], $product['imagen'], $product['descripcion'], $product['precio_recomendado'], $product['activo']);
            $sq->fetch();
        } else
            $product = 1;

        return $product;
    }

    public function getProductForcategory($categoria) {
        $query = "SELECT id FROM subcategory WHERE category_id=?";
        $sq = $this->conexion->stmt_init();
        $sq->prepare($query);
        $sq->bind_param("s", $categoria);
        $sq->execute();
        $sq->bind_result($d[]);
        $id = "(";
        for ($i = 0; $sq->fetch(); $i++)
            $id = $id . $d[$i];
        $id = ")";
        $query = "Select p.id, p.name as nombre, p.price as precio, p.quantity as cantidad, p.img as imagen, p.description as descripcion,"
                . "p.recommendedprice as precio_recomendado, p.active as activo from product p where p.subcategory_id in ;";
        $sq = $this->conexion->stmt_init();
        $sq->prepare($query);
        $sq->bind_param("s", $categoria);
        $sq->execute();
        $dades = array();
        $sq->bind_result($dades['id'], $dades['nombre'], $dades['precio'], $dades['cantidad'], $dades['imagen'], $dades['descripcion'], $dades['precio_recomendado'], $dades['activo']);
        $result = array();
        $i = 0;
        while ($sq->fetch()) {
            $aux = array();
            $aux['id'] = $dades['id'];
            $aux['nombre'] = $dades['nombre'];
            $aux['precio'] = $dades['precio'];
            $aux['cantidad'] = $dades['cantidad'];
            $aux['imagen'] = $dades['imagen'];
            $aux['descripcion'] = $dades['descripcion'];
            $aux['precio_recomendado'] = $dades['precio_recomendado'];
            $aux['activo'] = $dades['activo'];
            $result[$i] = $aux;
            $i++;
        }
        if ($i > 0) {
            return $result;
        } else {
            return 0;
        }
    }

    public function addChart($id) {
        if (isset($_SESSION['chart'])) {
            $array = unserialize($_SESSION['chart']);
            if (isset($array[$id]))
                $array[$id] += 1;
            else
                $array[$id] = 1;
            $_SESSION['chart'] = serialize($array);
        }
        else {
            $array = array();
            $array[$id] = 1;
            $_SESSION['chart'] = serialize($array);
        }

        return $array;
    }
    
    public function deleteProductFromChat($id) {
        if (isset($_SESSION['chart'])) {
            $array = unserialize($_SESSION['chart']);
            if (isset($array[$id]))
            {
                $array[$id] -= 1;
                if($array[$id] == 0)
                {
                    unset($array[$id]);
                }
                
                $_SESSION['chart'] = serialize($array);
                $return = $array;
            }
        }
        else   
        {
            $return = -1;
        }

        return $array;
    }
    
    public function deleteFullChart() {
        $_SESSION['chart'] = NULL;
    }

    public function showChart() {
        if (isset($_SESSION['chart'])) {
            $array = unserialize($_SESSION['chart']);
            $return = array();
            foreach ($array as $id => $quantity) {
                $aux = array();
                $aux['quantity'] = $quantity;
                $aux = array_merge($aux, $this->getProductFromID($id));
                array_push($return, $aux);
            }
            return $return;
        } else {
            return false;
        }
    }

    public function modifyUserInfo($name, $password, $email, $phone, $address, $city, $pc) {
        $checked = false;
        
        //Filtramos las variables para evitar ataques XSS
        $name = filter_var(htmlentities($name, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_STRING);
        $password = filter_var(htmlentities($password, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $phone = intval(filter_var(htmlentities($phone, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_NUMBER_INT));
        $address = filter_var(htmlentities($address, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_STRING);
        $city = filter_var(htmlentities($city, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_STRING);
        $pc = intval(filter_var(htmlentities($pc, ENT_QUOTES | ENT_HTML5, 'UTF-8'), FILTER_SANITIZE_NUMBER_INT));
        
        //Comprobamos que los datos sean integramente INT o EMAIL
        if(filter_var($email, FILTER_VALIDATE_EMAIL) 
            && filter_var($phone, FILTER_VALIDATE_INT) 
            && filter_var($pc, FILTER_VALIDATE_INT)) $checked = true; 
        
        if($checked)
        {
            $query = "UPDATE `user` SET "
                . "`name` =?, "
                . "`password` =?, "
                . "`email` =?, "
                . "`phone` =?, "
                . "`address` =?, "
                . "`city` =?, "
                . "`postal_code` =? "
                . "WHERE `user`.`id` =?;";
            $sq = $this->conexion->stmt_init();
            $sq->prepare($query);
          
            $sq->bind_param("sssssssi", $name, password_hash($password, PASSWORD_DEFAULT), $email, $phone, $address, $city, $pc, $_SESSION['id']);
            if ($sq->execute()) {
                $_SESSION['password'] = $password;
                $_SESSION['email'] = $email;
                $_SESSION['nombre'] = $name;
                $_SESSION['telefono'] = $phone;
                $_SESSION['direccion'] = $address;
                $_SESSION['ciudad'] = $city;
                $_SESSION['cp'] = $pc;
                return 1;
            } else return 2;
        } else return 3;
        
    }

    public function buyproducts($shippingmethod, $shippingcost) {
        $array = unserialize($_SESSION['chart']);
        $aux = array();
        $precioT = 0;
        $user = $_SESSION['id'];
        $trackingNumber = $this->trackingNumberGenerator();
        
        foreach ($array as $id => $quantity) {
            $producto = $this->getProductFromID($id);
            $auxs['id'] = $id;
            $auxs['nombre'] = $producto['nombre'];
            $auxs['cantidad'] = $quantity;
            $auxs['precio'] = $producto['precio'];
            $auxs['precio_total'] = $quantity * $producto['precio'];
            $cantidad = ($producto ['cantidad'] - $quantity);
            if ($cantidad >= 0) {
                $sql = "UPDATE product SET quantity = $cantidad WHERE id = $id;";
                $this->consultas($sql);
                $precioT = $precioT + $auxs['precio_total'];
                $auxs['agotado'] = false;
            } else {
                $auxs['agotado'] = true;
            }
            array_push($aux, $auxs);
        }

        $sql = "INSERT INTO orden VALUES (NULL, $user, $shippingmethod, '1', '$precioT', '$trackingNumber', '" . date("Y-m-d") . "', NULL)";
        $this->consultas($sql);
        $lastid = $this->conexion->insert_id;

        for ($i = 0; $i < count($aux); $i++) {
            if ($aux[$i]['agotado'] != true) {
                $id = $aux[$i]['id'];
                $nombre = $aux[$i]['nombre'];
                $cantidad = $aux[$i]['cantidad'];
                $precio = $aux[$i]['precio'];
                $precio_total = $aux[$i]['precio_total'];
                $sql = "INSERT INTO order_has_products VALUES($lastid, $id, '$nombre', $cantidad, $precio, $precio_total);";

                $result = $this->consultas($sql);
                
            }
        }
        
        //Actualizar precio total, con el coste de envio incluido
        $total = $precioT + $shippingcost;
        $sql = "UPDATE `orden` SET `price_with_shopping_cost` = '".$total."' WHERE `orden`.`idorder` = ".$lastid;
        $this->consultas($sql);

    }
    
    public function listOrderUser()
    {
        if($_SESSION['id'] != NULL)
        {
            $return = array();
            $i = 0;
            
            $sql = "SELECT * FROM `orden` WHERE `user_id` = ".$_SESSION['id'];
            $result = $this->consultas($sql);
            
            while($row = mysqli_fetch_array($result))
            {
                $return[$i]['id'] = $row['idorder'];
                $return[$i]['shipping'] = $this->getShippingMethodByID($row['shipping_id']);
                $return[$i]['price'] = $row['price'];
                $return[$i]['status'] = $row['status'];
                $return[$i]['tracking'] = $row['tracking_number'];
                $return[$i]['date'] = $row['date'];
                $return[$i]['total'] = $row['price_with_shopping_cost'];
                
                $sql = "SELECT * FROM `order_has_products` WHERE `order_idorder` = ".$row['idorder'];
                $result2 = $this->consultas($sql);
                
                while($row2 = mysqli_fetch_array($result2))
                {
                    $aux = array();
                    $aux['product_id'] = $row2['product_id'];
                    $aux['name'] = $row2['name'];
                    $aux['quantity'] = $row2['quantity'];
                    $aux['price'] = $row2['product_price'];
                    $aux['total_product'] = $row2['total_product_price'];
                    
                    $return[$i]['product'][] = $aux;
                    
                }
                
                $i++;
            }
            
            return $return;
        }
    }
    
    public function listOrderFull()
    {
        if($_SESSION['id'] != NULL && $_SESSION['administrador'] == 1)
        {
            $return = array();
            $i = 0;
            
            $sql = "SELECT * FROM `orden`";
            $result = $this->consultas($sql);
            
            while($row = mysqli_fetch_array($result))
            {
                $return[$i]['id'] = $row['idorder'];
                $return[$i]['shipping'] = $this->getShippingMethodByID($row['shipping_id']);
                $return[$i]['price'] = $row['price'];
                $return[$i]['status'] = $row['status'];
                $return[$i]['tracking'] = $row['tracking_number'];
                $return[$i]['date'] = $row['date'];
                $return[$i]['total'] = $row['price_with_shopping_cost'];
                
                $sql = "SELECT * FROM `order_has_products` WHERE `order_idorder` = ".$row['idorder'];
                $result2 = $this->consultas($sql);
                
                while($row2 = mysqli_fetch_array($result2))
                {
                    $aux = array();
                    $aux['product_id'] = $row2['product_id'];
                    $aux['name'] = $row2['name'];
                    $aux['quantity'] = $row2['quantity'];
                    $aux['price'] = $row2['product_price'];
                    $aux['total_product'] = $row2['total_product_price'];
                    
                    $return[$i]['product'][] = $aux;
                    
                }
                
                $i++;
            }
            
            return $return;
        }
    }
    
    public function editProduct($nombre, $cantidad, $precio_recomendado, $precio, $descripcion, $product_id, $image)
    {
        $return = "";
        $no_errors = false;
        $sq = $this->conexion->stmt_init();
        
        //Si ha subido una imagen, la procesamos
        if(!($image['error'] == 4))
        {
            $file_name = basename($image['name']);
            $path = ABSOLUTE_PATH . $file_name;

            $type = explode("/", $image['type']);
            $type = $type[0];

            if($type == "image")
            {
                if(!file_exists($path))
                {
                    if(move_uploaded_file($image['tmp_name'], $path))
                    {
                        $no_errors = true;
                        
                        $return .= "- El fichero es válido y se subió con éxito.";
                        $sql = "UPDATE `product` SET "
                        . "`name` =?, "
                        . "`price` =?, "
                        . "`quantity` =?, "
                        . "`img` =?, "
                        . "`description` =?, "
                        . "`recommendedprice` =? "
                        . "WHERE `product`.`id` =?";

                        $sq->prepare($sql);
                        $sq->bind_param("ssssssi", $nombre, $precio, $cantidad, $file_name, $descripcion, $precio_recomendado, $product_id);
                    }
                    else $return .= "- Error en subir el fichero!";
                } else $return .= "- El archivo ya existe. Porfavor, cambie el nombre del archivo!";
            } else $return .= "- No es una imagen!";
        }
        
        //Si no ha subido la imagen, no actualizamos el campo img
        else
        {
            $no_errors = true;
            
            $sql = "UPDATE `product` SET "
                . "`name` =?, "
                . "`price` =?, "
                . "`quantity` =?, "
                . "`description` =?, "
                . "`recommendedprice` =? "
                . "WHERE `product`.`id` =?";

            $sq->prepare($sql);
            $sq->bind_param("sssssi", $nombre, $precio, $cantidad, $descripcion, $precio_recomendado, $product_id);
        }
        
        if($no_errors)
        {
            if ($sq->execute()) $return .= "El producto se ha modificado correctamente!";
            else $return .= "El producto NO se ha modificado correctamente!";
        }
        
        return $return;
    }
    
    public function addProduct($subcategory, $nombre, $cantidad, $precio_recomendado, $precio, $descripcion, $image)
    {
        $return = "";
        $no_errors = false;
        $sq = $this->conexion->stmt_init();
        
        //Si ha subido una imagen, la procesamos
        if(!($image['error'] == 4))
        {
            $file_name = basename($image['name']);
            $path = ABSOLUTE_PATH . $file_name;

            $type = explode("/", $image['type']);
            $type = $type[0];

            if($type == "image")
            {
                if(!file_exists($path))
                {
                    if(move_uploaded_file($image['tmp_name'], $path))
                    {
                        $no_errors = true;
                        
                        $return .= "- El fichero es válido y se subió con éxito.";
                        $sql = "INSERT INTO `product` (`id`, `subcategory_id`, `name`, `price`, `quantity`, `img`, `description`, `recommendedprice`, `active`)"
                                . " VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, '1');";

                        $sq->prepare($sql);
                        $sq->bind_param("sssssss", $subcategory, $nombre, $precio, $cantidad, $file_name, $descripcion, $precio_recomendado);
                    }
                    else $return .= "- Error en subir el fichero!";
                } else $return .= "- El archivo ya existe. Porfavor, cambie el nombre del archivo!";
            } else $return .= "- No es una imagen!";
        }
        else $return .= "- No se ha subido ningun archivo!";
        
        if($no_errors)
        {
            if ($sq->execute()) $return .= "- El producto se ha modificado correctamente!";
            else $return .= "- El producto NO se ha modificado correctamente!";
        }
        
        return $return;
    }
	
    public function addCategory($category)
    {
        $query = "INSERT INTO `category` (`id`, `name`) VALUES (NULL, ?)";
        $sq = $this->conexion->stmt_init();
        $sq->prepare($query);
        $sq->bind_param("s", $category);
        if($sq->execute()) return true;
        else return false;
    }

    public function addSubcategory($categoryid, $subcategory)
    {
        echo $categoryid." _ ".$subcategory;
        $query = "INSERT INTO `subcategory` (`id`, `category_id`, `name`) VALUES (NULL, ?, ?)";
        $sq = $this->conexion->stmt_init();
        $sq->prepare($query);
        $sq->bind_param("is", $categoryid, $subcategory);
        if($sq->execute()) return true;
        else return false;
    }

    public function search($tosearch)
    {
        $product = array();
        $tosearch = '%'.$tosearch.'%';
        
        $query = "SELECT `id`, `name`, `price`, `quantity`, `img`, `description`, `recommendedprice` FROM `product` WHERE `name` LIKE ? AND `active` = 1";
        $sq = $this->conexion->stmt_init();
        $sq->prepare($query);
        $sq->bind_param("s", $tosearch);
        $sq->execute();
        $sq->store_result();
        if ($sq->num_rows > 0) {
            $sq->bind_result($id, $nombre, $precio, $cantidad, $imagen, $descripcion, $precio_recomendado);
            while($sq->fetch())
            {
                $aux = array();
                $aux['id'] = $id;
                $aux['nombre'] = $nombre;
                $aux['precio'] = $precio;
                $aux['cantidad'] = $cantidad;
                $aux['imagen'] = $imagen;
                $aux['descripcion'] = $descripcion;
                $aux['precio_recomendado'] = $precio_recomendado;
                
                $product[] = $aux;
            }
        } else $product = 1;
        
        return $product;
    }

    public function getShippingMethods() {
        $shippings = array();
        $sql = "SELECT * FROM `shipping` WHERE `status` = 1";

        $result = $this->consultas($sql);

        for($i = 0; $row = mysqli_fetch_array($result); $i++) {
            $aux = array();
            $aux['id'] = $row['id'];
            $aux['method'] = $row['method'];
            $aux['time'] = $row['estimated_date'];
            $aux['cost'] = $row['shippingcost'];
            
            $shippings[] = $aux;
        }
        
        return $shippings;
    }
    
    public function updateStatus($order_id, $status)
    {
        if($_SESSION['administrador'] == 1)
        {
            $sql = "UPDATE `orden` SET `status` = '$status' WHERE `orden`.`idorder` = $order_id";
            $this->consultas($sql);
            return 1;
        } else return 0;
    }
    
    public function getShippingMethodByID($id)
    {
        $sql = "SELECT * FROM `shipping` WHERE `status` = 1 AND `id` = $id";

        $result = $this->consultas($sql);
        $row = mysqli_fetch_row($result);
        
        $return['method'] = $row[1];
        $return['time'] = $row[2];
        $return['shippingcost'] = $row[3];
        
        return $return;
    }
    
    function trackingNumberGenerator($length = 20) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
}
?>    

