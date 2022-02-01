<form action="index.php" method="post" enctype="multipart/form-data">
    <table id="productdescription">
            <tr>
                <td><img src="img/<?php echo $product['imagen']; ?>" /></td>
                <td>
                    <h1><input type="text" name="nombre" value="<?php echo $product['nombre']; ?>" style="width:100%"/></h1>
                    <h5><b>Cantidad:</b><input type="text" name="cantidad" value="<?php echo $product['cantidad']; ?>" width="30px" /></h5>
                    <h2><b>Precio recomendado:</b><input type="text" name="precio_recomendado" value="<?php echo $product['precio_recomendado']; ?>" width="30px" />€</h2>
                    <h3><b>Precio:</b><input type="text" name="precio" value=" <?php echo $product['precio']; ?>" />€</h3>
                    <h4><b>Ahorras:</b> -<?php echo ($product['precio_recomendado'] - $product['precio']); ?>€ (<?php echo substr(((($product['precio_recomendado'] - $product['precio']) / $product['precio_recomendado']) * 100), 0, 5); ?>%)</h4>
                    <p><textarea rows="10" cols="100" name="descripcion"><?php echo $product['descripcion']; ?></textarea></p>
                    <p>Cambiar imagen: <input type="file" name="image" id="imageupload"></p>
                    <p><input type="submit" value="Guardar" id="productsavebutton" productid="<?php echo $product['id']; ?>" /> </p>
                    <input type="hidden" name="action" value="modifyproduct" />
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                </td> 
            </tr>
    </table>
</form>
