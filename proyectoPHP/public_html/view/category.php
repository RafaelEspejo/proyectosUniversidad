<?php foreach($categoria as $clave => $aux){ ?>
    <li><?php echo $clave; ?> &#x25BE
    <ul>
    <?php foreach($aux as $subcategoria){ ?>
        <li subcategory="<?php echo $subcategoria['id'] ?>"><?php echo $subcategoria['subcategoria']; ?></li>
    <?php } ?>
    </ul>
    </li>
<?php } ?>