<?php
session_start();
if(empty($_SESSION['userid']) or $_SESSION['userid']==''){
	header("Location: index.php");
}
include_once("config/db.php");
include_once("config/user.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Pampa Online!</title>
<link href="styles/structure.css" rel="stylesheet" type="text/css" />
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
    <script src="js/jqsimplemenu.js" type="text/javascript"></script>

    <!--dhtmlWindows-->
<link rel="stylesheet" type="text/css" href="library/dhtml/styles/dhtmlxwindows.css">
<link rel="stylesheet" type="text/css" href="library/dhtml/styles/dhtmlxwindows_dhx_skyblue.css">
<script src="library/dhtml/js/dhtmlxcommon.js"></script>
<script src="library/dhtml/js/dhtmlxcontainer.js"></script>
<script src="library/dhtml/js/dhtmlxwindows.js"></script>

<script>
function GetWidth()
{
        var x = 0;
        if (self.innerHeight)
        {
                x = self.innerWidth;
        }
        else if (document.documentElement && document.documentElement.clientHeight)
        {
                x = document.documentElement.clientWidth;
        }
        else if (document.body)
        {
                x = document.body.clientWidth;
        }
        return x;
}
 
function GetHeight()
{
        var y = 0;
        if (self.innerHeight)
        {
                y = self.innerHeight;
        }
        else if (document.documentElement && document.documentElement.clientHeight)
        {
                y = document.documentElement.clientHeight;
        }
        else if (document.body)
        {
                y = document.body.clientHeight;
        }
        return y;
}
$(document).ready(function() {
    $('.jq-menu').jqsimplemenu();
});

</script>

</head>

<body onload="doOnLoad(); $('#desktop').height(GetHeight()-40);">

<?php include_once("top-bar.php"); ?>
<?php include_once("menu.php"); ?>

<div id="desktop">
<img class="logo" src="images/logo_gr.png" />
</div>
<script>

 //posicion inicial de la primer ventana
var xpos = 50;
var ypos = 5; 

var dhxWins,w1;

function doOnLoad() {

    dhxWins = new dhtmlXWindows();
    dhxWins.enableAutoViewport(false);
    dhxWins.attachViewportTo("desktop");
    dhxWins.setImagePath("library/dhtml/imgs/");

}
</script>
<script src="js/createWindow.js"></script>

</body>
</html>
