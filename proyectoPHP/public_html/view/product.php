<table id="productdescription">
    <tr>
        <td><img src="img/<?php echo $product['imagen']; ?>" /></td>
        <td>
            <h1><?php echo $product['nombre']; ?></h1>
            <h2><b>Precio recomendado:</b> <?php echo $product['precio_recomendado']; ?>€</h2>
            <h3><b>Precio:</b> <?php echo $product['precio']; ?>€</h3>
            <h4><b>Ahorras:</b> -<?php echo ($product['precio_recomendado'] - $product['precio']); ?>€ (<?php echo substr(((($product['precio_recomendado'] - $product['precio']) / $product['precio_recomendado']) * 100), 0, 5); ?>%)</h4>
            <p><?php echo $product['descripcion']; ?></p>
            <?php if($product['cantidad'] > 0){ ?><p><input type="button" value="Añadir a carrito" id="addchartbutton" productid="<?php echo $product['id']; ?>" /> <?php if($product['cantidad'] <= 5) echo "Date prisa! Solo quedan ".$product['cantidad']; else echo "Quedan ".$product['cantidad']; ?></p><?php } ?>
            <?php if($product['cantidad'] == 0){ ?><p>Lo sentimos pero este producto esta agotado</p><?php } ?>
        </td> 
    </tr>
</table>
