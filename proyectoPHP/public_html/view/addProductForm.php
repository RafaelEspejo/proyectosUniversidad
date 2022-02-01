<form action="index.php" method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <td>Categoria: </td>
            <td>
                <select name="category" id="categoryselector" onchange="subcategorySelector()">
                    <?php foreach($categories as $category => $subcategory){ ?>
                        <option value="<?php echo $ids[$i]; ?>"><?php echo $category; ?></option>
                        <?php $i++; ?>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Subcategoria: </td>
            <td><select name="subcategory" id="subcategoryselector"></select></td>
        </tr>
        <tr>
            <td>Nombre: </td>
            <td><input type="text" name="nombre" style="width:100%"/></td>
        </tr>
        <tr>
            <td>Cantidad: </td>
            <td><input type="text" name="cantidad" width="30px" /></td>
        </tr>
        <tr>
            <td>Precio: </td>
            <td><input type="text" name="precio" /></td>
        </tr>
        <tr>
            <td>Precio recomendado: </td>
            <td><input type="text" name="precio_recomendado" width="30px" /></td>
        </tr>
        <tr>
            <td>Descripcion: </td>
            <td><textarea rows="10" cols="100" name="descripcion"></textarea></td>
        </tr>
        <tr>
            <td>Imagen: </td>
            <td><input type="file" name="image" id="imageupload"></td>
        </tr>
        <tr>
            <td> </td>
            <td><input type="submit" value="Guardar" id="productsavebutton" /></td>
        </tr>
    </table>
    <input type="hidden" name="action" value="addproduct" />
</form>
