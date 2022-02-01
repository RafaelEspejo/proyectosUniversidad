<table>
<?php foreach($list as $product){?>
<tr>
    <td class="productimg"><img src="img/<?php echo $product['imagen']; ?>" /></td>
    <td class="description">
        <h1><a value="<?php echo $product['id']; ?>" class="productlink"><?php echo $product['nombre']; ?></a></h1>
        <?php echo $product['descripcion']; ?>
    </td> 
</tr>
<?php } ?>
</table>

