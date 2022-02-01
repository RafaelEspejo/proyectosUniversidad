<?php foreach($list as $order){ ?>
<ul>Pedido del <?php echo $order['date']; ?> - <?php echo $order['total']; ?>€ | Envio: <?php echo $order['shipping']['method']; ?> (Entrega en <?php echo $order['shipping']['time']; ?>) - <?php echo $order['shipping']['shippingcost']; ?>€ | Estado: 
    
    <?php
    switch($order['status'])
    {
        case 1:
            echo "Pendiente";
            break;
        case 2:
            echo "Empaquetado";
            break;
        case 3:
            echo "Listo para envio";
            break;
        case 4: 
            echo "Enviado";
            break;
        case 5:
            echo "En reparto";
            break;
        case 6:
            echo "Entregado";
            break;
    }
    ?>
    
    <?php foreach($order['product'] as $product){ ?>
    <li><b><a class="productlink" value="<?php echo $product['product_id']; ?>"><?php echo $product['quantity']; ?>x</b> <u><?php echo $product['name']; ?></u></a> (<?php echo $product['price'] ?>€) - Total: <?php echo $product['total_product'] ?>€</li>
    <?php } ?>
</ul>
<?php } ?>

