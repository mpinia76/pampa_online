<?
$file 	= "operaciones_xpago.php"; //archivo
$json	= "operaciones_xpago.json.php"; //json

//resta modificar los datos del grid

include_once("functions/delete.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link rel="STYLESHEET" type="text/css" href="styles/toolbar.css">
<!--dhtmlGrid-->
<link rel="STYLESHEET" type="text/css" href="library/dhtml/styles/dhtmlxgrid.css">
<link rel="STYLESHEET" type="text/css" href="library/dhtml/styles/dhtmlxgrid_dhx_skyblue.css">
<script src="library/dhtml/js/dhtmlxcommon.js"></script>
<script src="library/dhtml/js/dhtmlxgrid.js"></script>
<script src="library/dhtml/js/dhtmlxgridcell.js"></script>
<script src="library/dhtml/js/dhtmlxgrid_filter.js"></script>
<script src="library/dhtml/js/dhtmlxgrid_srnd.js"></script>
<script src="library/dhtml/js/dhtmlxgrid_pgn.js"></script>
<script>

var mygrid;

var dataid;
	
function doInitGrid(){	
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Operacion,Forma de pago,Fecha,Nro. Orden,Monto"); 		//nombre de las columnas
	mygrid.attachHeader("#select_filter,#select_filter,#text_filter,#text_filter,#text_filter");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,&nbsp;,${#stat_total}");
	mygrid.enablePaging(true,10,10,"pagingArea",true,"recinfoArea");
    mygrid.setColAlign("left,left,left,right,right");			//alineacion de las columnas
	mygrid.setColSorting("str,str,str,int,int");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro,price");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?=$json?>","json");	//ruta al json con datos
	mygrid.init();
}



</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">

<div id="mygrid_container" style="width:100%;height:300px;"></div>
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>

</body>
</html>
