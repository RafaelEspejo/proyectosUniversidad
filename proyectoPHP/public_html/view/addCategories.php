<form action="index.php" method="POST">
    <table id="addcatgeories">
        <tr>
            <td>Añadir Categoria: </td>
            <td><input type="text" name="category" style="width:100%"/></td> 
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Añadir Categoria" /></td> 
        </tr>
    </table>
    <input type="hidden" name="action" value="addcategory" />
</form>
<form action="index.php" method="POST">
    <table id="addsubcatgeories">
        <tr>
            <td>Categoria: </td>
            <td>
                <select name="categoryselector" id="categoryselector">
                    <?php foreach($categories as $category => $subcategory){ ?>
                        <option value="<?php echo $ids[$i]; ?>"><?php echo $category; ?></option>
                        <?php $i++; ?>
                    <?php } ?>
                </select>
            </td> 
        </tr>
        <tr>
            <td>Añadir Subcategoria </td>
            <td><input type="text" name="subcategory" style="width:100%"/></td> 
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Añadir Subcategoria" /></td> 
        </tr>
    </table>
    <input type="hidden" name="action" value="addsubcategory" />
</form>

