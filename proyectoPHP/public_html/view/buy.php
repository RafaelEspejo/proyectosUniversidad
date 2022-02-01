<?php if ($chart != FALSE){?>
<table>
<?php foreach($chart as $product){?>
<tr>
    <td class="productimg"><img src="img/<?php echo $product['imagen']; ?>" /></td>
    <td class="description">
        <h1><a value="<?php echo $product['id']; ?>" class="productlink"><b><?php echo $product['quantity']; ?>x</b> <?php echo $product['nombre']; ?></a></h1>
        <?php echo $product['descripcion']; ?>
        <?php $total += $product['precio'] * intval($product['quantity']); ?>
    </td> 
</tr>
<?php } ?>
<tr>
    <td class="productimg"></td>
    <td class="description">
        Envio: 
        <select name="shippingselector" id="shippingselector">
            <?php foreach($shippings as $shipping){ ?>
                <option value="<?php echo $shipping['id']; ?>" cost="<?php echo $shipping['cost']; ?>"><?php echo $shipping['method']; ?> (<?php echo $shipping['time']; ?>) - <?php echo $shipping['cost']; ?>€</option>
            <?php } ?>
        </select>
    </td> 
</tr>
<tr>
    <td class="productimg"></td>
    <td class="description">
        Total: <?php echo $total; ?>€ - <input type="button" id="paybutton" value="Pagar" />
    </td> 
</tr>
<tr>
    <td class="productimg"></td>
    <td class="description">
        
    </td> 
</tr>
</table>
<?php } ?>
