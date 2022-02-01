<?php


session_start();

include __DIR__."/ini.php";
include __DIR__."/model/DB.php";
$db=DB::getInstance();

if(isset($_GET['action'])) $action = $_GET['action'];
else if(isset($_POST['action'])) $action = $_POST['action'];
else $action = "";
    
switch ($action)
{
    case 'login':
        include __DIR__.'/controller/login.php';
        break;
    case 'registerform':
        include __DIR__.'/controller/registerform.php';
        break;
    case 'register':
        include __DIR__.'/controller/registersubmit.php';
        break;
    case 'modify':
        include __DIR__.'/controller/modifyuser.php';
        break;
    case 'listcategories':
        include __DIR__.'/controller/category.php';
        break;
    case 'product':
        include __DIR__.'/controller/product.php';
        break;
    case 'listproduct':
        include __DIR__.'/controller/listproduct.php';
        break;
    case 'addchart':
        include __DIR__.'/controller/addchart.php';
        break;
    case 'showchart':
        include __DIR__.'/controller/showchart.php';
        break;
    case 'logout':
        include __DIR__.'/controller/logout.php';
        break;
    case 'userinfo':
        include __DIR__.'/controller/userinfo.php';
        break;
    case 'buyproducts':
        include __DIR__.'/controller/buyproducts.php';
        break;
    case 'deleteproduct':
        include __DIR__.'/controller/deleteproduct.php';
        break;
    case 'emptychart':
        include __DIR__.'/controller/emptychart.php';
        break;
    case 'modifyproduct':
        include __DIR__.'/controller/modifyproduct.php';
        break;
    case 'addproduct':
        include __DIR__.'/controller/addProduct.php';
        break;
    case 'addproductform':
        include __DIR__.'/controller/addProductForm.php';
        break;
    case 'subcategoryselector':
        include __DIR__.'/controller/subcategorySelector.php';
        break;
    case 'addcategories':
        include __DIR__.'/controller/addCategories.php';
        break;
    case 'addcategory':
        include __DIR__.'/controller/addCategory.php';
        break;
    case 'addsubcategory':
        include __DIR__.'/controller/addSubcategory.php';
        break;
    case 'search':
        include __DIR__.'/controller/search.php';
        break;
    case 'buy':
        include __DIR__.'/controller/buy.php';
        break;
    case 'pay':
        include __DIR__.'/controller/pay.php';
        break;
    case 'listorders':
        include __DIR__.'/controller/listorder.php';
        break;
    case 'listordersadmin':
        include __DIR__.'/controller/listorderadmin.php';
        break;
    case 'updatestatus':
        include __DIR__.'/controller/updatestatus.php';
        break;
    case 'listorderadmin':
        include __DIR__.'/controller/listorderadmin.php';
        break;
    default:
        include __DIR__.'/controller/portada.php';
        break;
}

?>