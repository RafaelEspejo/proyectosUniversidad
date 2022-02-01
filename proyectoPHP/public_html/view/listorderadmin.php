<?php foreach($list as $order){ ?>
<ul>Pedido del <?php echo $order['date']; ?> - <?php echo $order['total']; ?>€ | Envio: <?php echo $order['shipping']['method']; ?> (Entrega en <?php echo $order['shipping']['time']; ?>) - <?php echo $order['shipping']['shippingcost']; ?>€ | Estado: 
    <select class="statusselector" order_id="<?php echo $order['id']; ?>">
        <option value='1' <?php if($order['status'] == 1) echo 'selected="selected"'; ?>>Pendiente</option>
        <option value='2' <?php if($order['status'] == 2) echo 'selected="selected"'; ?>>Empaquetado</option>
        <option value='3' <?php if($order['status'] == 3) echo 'selected="selected"'; ?>>Listo para envio</option>
        <option value='4' <?php if($order['status'] == 4) echo 'selected="selected"'; ?>>Enviado</option>
        <option value='5' <?php if($order['status'] == 5) echo 'selected="selected"'; ?>>En reparto</option>
        <option value='6' <?php if($order['status'] == 6) echo 'selected="selected"'; ?>>Entregado</option>
    </select>
    <?php foreach($order['product'] as $product){ ?>
    <li><b><a class="productlink" value="<?php echo $product['product_id']; ?>"><?php echo $product['quantity']; ?>x</b> <u><?php echo $product['name']; ?></u></a> (<?php echo $product['price'] ?>€) - Total: <?php echo $product['total_product'] ?>€</li>
    <?php } ?>
</ul>
<?php } ?>

