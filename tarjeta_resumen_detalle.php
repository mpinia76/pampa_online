<?php
include_once("config/db.php");
$sql = "SELECT tarjeta_resumen.*, CONCAT(banco.banco,' ',tarjeta_marca.marca,' ',tarjeta.titular) AS tarjeta FROM tarjeta INNER JOIN tarjeta_marca ON tarjeta.tarjeta_marca_id=tarjeta_marca.id INNER JOIN banco ON tarjeta.banco_id=banco.id INNER JOIN tarjeta_resumen ON tarjeta_resumen.tarjeta_id=tarjeta.id WHERE tarjeta_resumen.id=".$_GET['resumen_id'];
$resumen = mysql_fetch_array(mysql_query($sql));

$file 	= "tarjeta_resumen_detalle.php"; //archivo
$json	= "tarjeta_resumen_detalle.json.php"; //json
$abm 	= "tarjeta_resumen_detalle.am.php"; //agregar o modificar
$tabla 	= "tarjeta_resumen_detalle";

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
var dhxWins = parent.dhxWins;

var position = dhxWins.window('w_<?php echo $tabla?>').getPosition(); //id de la ventana

var xpos = position[0];
var ypos = position[1];

var mygrid;

var dataid;
	
function doInitGrid(){
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Detalle, Nro. Orden, Cuota, Monto"); 		//nombre de las columnas
	mygrid.attachHeader("#text_filter,#text_filter,&nbsp;,#text_filter");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,${#stat_total}");
	//mygrid.enablePaging(true,10,10,"pagingArea",true,"recinfoArea");
    mygrid.setInitWidths("*,80,100,90"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,right,right");			//alineacion de las columnas
	mygrid.setColSorting("str,int,str,int");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,price");				//editable o no
	mygrid.enableEditEvents(false,false,false,false);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?php echo $json?>?resumen_id=<?php echo $_GET['resumen_id']?>","json");	//ruta al json con datos
	mygrid.init();
}

function pagar(){
	createWindow('w_<?php echo $tabla?>_pagar','Abonar resumen','tarjeta_resumen.pagar.php?resumen_id=<?php echo $_GET['resumen_id']?>','600','400'); //nombre de los divs
}
function anular(){
	
		if(confirm('¿Seguro desea anular el pago?'))
			createWindow('w_<?php echo $tabla?>_anular','Anular pago','anular_tarjeta_resumen_procesa.php?dataid=<?php echo $_GET['resumen_id']?>','600','400');
	
}
function add(){
	createWindow('w_subrubros_add','Agregar <?php echo $label?>','<?php echo $abm?>','600','400'); //botones
}
function add_movimiento(){
	createWindow('w_<?php echo $tabla?>_detalle','Agregar movimiento','tarjeta_movimiento.am.php?resumen_id=<?php echo $_GET['resumen_id']?>','610','240'); //nombre de los divs
}

</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<?php if($resumen['estado'] == 0){ ?>
	<li onclick="add_movimiento()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Agregar movimiento</li>
	<li onclick="pagar()" class="item"><img src="images/bt_pay.png" align="absmiddle" />  Abonar</li>
	<?php }else{ ?>
	<li onclick="pagar()" class="item"><img src="images/bt_pay.png" align="absmiddle" />  Consultar el pago</li>
	<li onclick="anular()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Anular pago</li>
	<?php } ?>
</ul>
<div id="mygrid_container" style="width:100%;height:370px;"></div>
<!--
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>
-->
</body>
</html>
