<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Botiga Online">
    <meta name="keywords" content="botiga, online">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <!-- Social Media and FA icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="js/jquery-3.2.1.min.js"></script>
    <script type="text/javascript" src="js/scripts.js"></script>
    <script>
    <?php 
        if(@isset($_COOKIE['alert'])) echo "alert('".$_COOKIE['alert']."');";
        setcookie("alert", NULL, -1);
    ?>
    </script>
</head>
<body>
