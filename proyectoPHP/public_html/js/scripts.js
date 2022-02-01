/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(function(){
    
    //CARGAMOS EL MENU
    $.get( 
        "index.php",
        { action: "listcategories" },
        function(data) 
        {
           $('nav div.down ul').html(data);
        }
     );
     
    refreshBodySize();
    
    //Ocultamos las opciones del usuario inicialmete
    $("#logoutbutton").parent().toggle(false); 
    $("#ordersbutton").parent().toggle(false);
    $("#userbutton").parent().toggle(false);
    
    $(document).on("click", "nav div.down ul li ul li", function() 
    { 
        $.get( 
            "index.php",
            { action: "listproduct", id: $(this).attr('subcategory') },
            function(data) 
            {
                $('section').html(data);
                $("nav div.down ul li").children().toggle(false); //Ocultamos los menu (si habian)
                refreshBodySize();
            });
    });
    
    $("#loginsubmit").click(function()
    {
        login();
    });
    
    $("#registerbutton").click(function()
    {
        registerForm();
    });
    
    $("#logoutbutton").click(function()
    {
        logout();
    });
    
    $("#showchartbutton").click(function()
    {
        showChart();
        refreshBodySize();
    });
    
    $("#productformbutton").click(function()
    {
        addProductForm();
        refreshBodySize();
    });
    
    $("#buybutton").click(function()
    {
        buyChart();
        refreshBodySize();
    });
    
    $("#searchbutton").click(function()
    {
        search();
        refreshBodySize();
    });
    
    $("#ordersbutton").click(function()
    {
        listorders();
    });
    
    
    $(document).on("click", "a.productlink", function()
    {    
        $.get( 
            "index.php",
            { action: "product", productid: $(this).attr("value") },
            function(data) 
            {
               $("section").html(data);
               refreshBodySize();
            }
         );
    });
    
    $(document).on("click", "#categorybutton", function()
    {    
        $.get( 
            "index.php",
            { action: "addcategories" },
            function(data) 
            {
               $("section").html(data);
               refreshBodySize();
            }
         );
    });
    
    $(document).on("click", "#ordersadminbutton", function()
    {    
        $.get( 
            "index.php",
            { action: "listorderadmin" },
            function(data) 
            {
               $("section").html(data);
               refreshBodySize();
            }
         );
    });
    
    $(document).on("click", "#buybutton", function()
    {    
        $.post( 
            "index.php",
            { action: "buy" },
            function(data) 
            {
               $("section").html(data);
               refreshBodySize();
            }
         );
    });
    
    $(document).on("click", "#paybutton", function()
    {    
        var button = confirm("Desea comprar todos estos productos?");
        
        if(button == true)
        {
            $.post( 
                "index.php",
                { action: "pay", shipping: $("#shippingselector").val(), shippingcost: $("#shippingselector option").attr("cost") },
                function(data) 
                {
                   alert("Pedido realizado correctamente! Puedes consultar el estado de tu pedido en el apartado 'Pedidos'");
                   location.reload();
                }
             );
        }
            
    });
    
    $(document).on("click", "#addchartbutton", function()
    {    
        $.get( 
            "index.php",
            { action: "addchart", productid: $(this).attr("productid") },
            function() 
            {
               alert("Añadio al carrito!");
            }
         );
    });
    
    $(document).on("click", "#usermenubutton", function()
    {    
        $("#logoutbutton").parent().toggle(); 
        $("#ordersbutton").parent().toggle();
        $("#userbutton").parent().toggle();
    });
    
    $(document).on("click", "#userbutton", function()
    {    
        $.get( 
            "index.php",
            { action: "userinfo" },
            function(data) 
            {
               $("section").html(data);
               refreshBodySize();
            }
         );
    });
    
    $(document).on("click", "#orderbutton", function()
    {    
        $.get( 
            "index.php",
            { action: "buyproducts" },
            function(data) 
            {
               $("section").html(data);
               refreshBodySize();
            }
         );
    });

    //MUESTRA SUBMENU
    $(document).on("click", "nav div.down ul li", function()
    {    
        $("nav div.down ul li").children().toggle(false); //Ocultamos los menu (si habian)
        $(this).children().toggle(true); //Mostramos el menu
    });
    
    //MUESTRA EL QUADRO DE DIALOGO DE LOGIN
    $("#loginbutton").bind("click", function(){
        $("header, section, footer").animate({
            opacity: 0.4
          }, 300, function() {
            $("#loginuser").css("background-color", "white"); //Como hemos hecho que todo tenga un poco de transparencia, entonces tenmos que poner nuestro div sin opacidad, a traves del background color
            $("#loginuser").show(); //Mostramos el menu
          });
    });
    
    //CIERRA EL QUADRO DE DIALOGO CUANDO PULSA LA X
    $("#loginclose").bind("click", function(){
        $("header, section, footer").animate({
            opacity: 1 //Vuelve a dejar a los objectos a su estado normal
        }, 200);
        $("#loginuser").hide(); //Escondemos el quadro de dialogo
    });
});

function refreshBodySize(size = 100)
{
    //Los productos no haces scroll o el footer no muestra parte de los productos.
    //Tambien, como no consigo que el footer se queda abajo del todo, cuando hago scroll, realizo estas acciones
    var sectionHeight = $("section").height();
    var headerHeight = $("header").height();
    var footerHeight = $("footer").height();
    $('body').css('height', sectionHeight + footerHeight + headerHeight + size); //Lo que hago es cambiar el tamaño de la ventana para poder realizar los scrolls sin problemas y que los productos se muestren correctamente
}

function login()
{
    $.post( 
        "index.php",
        { action: "login", 
          email: $("#loginemail").val(), 
          password: $('#loginpassword').val() },
        function(data) 
        {
           console.log("RETURN = " + data);
           if(data == 0) alert("Email o contraseña incorrecto");
           if(data == 1) location.reload();
        }
     );
}

function registerForm()
{
    $.post( 
        "index.php",
        { action: "registerform" },
        function(data) 
        {
           $("section").html(data);
           refreshBodySize();
        }
     );
}

function logout()
{
    $.get( 
        "index.php",
        { action: "logout" },
        function(data) 
        {
           location.reload();
        }
     );
}

function showChart()
{
    $.get( 
        "index.php",
        { action: "showchart" },
        function(data) 
        {
           $("section").html(data);
           refreshBodySize();
        }
     );
}

function userInfo()
{
    $.get( 
        "index.php",
        { action: "userinfo" },
        function(data) 
        {
           $("section").html(data);
           refreshBodySize();
        }
     );
}

function deleteProduct(id)
{
    $.get( 
        "index.php",
        { action: "deleteproduct", 
          id: id 
        },
        function(data) 
        { 
            $("section").html(data);
           refreshBodySize();
        }
     );
}

function emptyChart()
{
    $.get( 
        "index.php",
        { action: "emptychart"
        },
        function(data) 
        { 
           $("section").html(data);
           refreshBodySize();
        }
     );
}

function addProductForm()
{
    $.get( 
        "index.php",
        { action: "addproductform"
        },
        function(data) 
        { 
           $("section").html(data);
           refreshBodySize();
        }
     );
}

function subcategorySelector()
{
    $.get( 
        "index.php",
        { action: "subcategoryselector", id: $("#categoryselector").val()
        },
        function(data) 
        { 
           $("#subcategoryselector").html(data);
           refreshBodySize();
        }
     );
}

function buyChart()
{
    $.get( 
        "index.php",
        { action: "buyproducts"
        },
        function(data) 
        { 
           alert(data);
        }
     );
}

function search()
{
    $.get( 
        "index.php",
        { action: "search", 
          tosearch: $("#tosearch").val()
        },
        function(data) 
        { 
           $("section").html(data);
           refreshBodySize();
        }
     );
}

function listorders()
{
    $.post( 
        "index.php",
        { action: "listorders" },
        function(data) 
        { 
           $("section").html(data);
           refreshBodySize(200);
        }
     );
}

$(document).on("change", ".statusselector", function(){
    var id = $(this).attr("order_id");
    var selected_status = $(this).val();
    $.post( 
        "index.php",
        { action: "updatestatus", order_id: id, status: selected_status },
        function(data) 
        { 
            if(data == 0) alert("No se ha podido cambiar el status!");
            if(data == 1) alert("Status cambiado correctamente!");
        }
     );
});
