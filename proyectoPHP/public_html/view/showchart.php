<?php if ($chart != FALSE){?>
<table>
<?php foreach($chart as $product){?>
<tr>
    <td class="productimg"><img src="img/<?php echo $product['imagen']; ?>" /></td>
    <td class="description">
        <h1><a value="<?php echo $product['id']; ?>" class="productlink"><b><?php echo $product['quantity']; ?>x</b> <?php echo $product['nombre']; ?></a></h1>
        <?php echo $product['descripcion']; ?>
        <p><input class="deleteProductButton" type="button" value="Eliminar" onclick="deleteProduct('<?php echo $product['id'] ?>')"/></p>
    </td> 
</tr>
<?php } ?>
<tr>
    <td class="productimg"></td>
    <td class="description">
        <?php if(isset($_SESSION['id'])) { ?> <input type="button" id="buybutton" value="Comprar" /> <?php }else{ ?>
        <input type="button" value="Para Comprar, registrate o haz login" disabled/>  <?php }?>
        <input type="button" id="emptyChart" value="Vaciar Carrito" onclick="emptyChart()" />
    </td> 
</tr>
</table>
<?php } ?>
