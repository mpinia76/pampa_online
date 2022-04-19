<?php
session_start();


include_once("config/db.php");
include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Resumen de Tarjetas Corporativas','".getRealIP()."')";
mysql_query($sql);
$tabla 	= "tarjeta_resumen"; //tabla
$label 	= "resumen de tarjeta"; //nombre para el editar y agregar
$file 	= "tarjeta_resumen.php"; //archivo
$json	= "tarjeta_resumen.json.php"; //json
$abm 	= "tarjeta_resumen.am.php"; //agregar o modificar

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
    mygrid.setHeader("Periodo,Tarjeta,Vencimiento,Estado"); 		//nombre de las columnas
    mygrid.setInitWidths("100,*,100,100"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,left,left");			//alineacion de las columnas
	mygrid.setColSorting("str,str,str,str");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro");				//editable o no 
    mygrid.setSkin("dhx_skyblue");	
	mygrid.load("<?php echo $json?>","json");	//ruta al json con datos
	mygrid.init();
}

function edit(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_edit','Editar <?php echo $label?>','<?php echo $abm?>?dataid='+dataid,'600','400'); //nombre de los divs
	}
}
function eliminar(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm('¿Seguro desea eliminar el registro?'))window.location.href='<?php echo $file?>?delete=on&dataid='+dataid; //ruta
	}
}
function add_movimiento(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_detalle','Agregar movimiento','tarjeta_movimiento.am.php?resumen_id='+dataid,'610','220'); //nombre de los divs
	}
}
function add(){
	createWindow('w_subrubros_add','Agregar <?php echo $label?>','<?php echo $abm?>','600','400'); //botones
}
function ver_detalle(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_detalle','Detalle del resumen','tarjeta_resumen_detalle.php?resumen_id='+dataid,'610','500'); //nombre de los divs
	}
}

</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<li onclick="add()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Agregar</li>
	<li onclick="edit()" class="item"><img src="images/bt_edit.png" align="absmiddle" />  Editar</li>
	<li onclick="ver_detalle()" class="item"><img src="images/bt_view.png" align="absmiddle" />  Detalle</li>
</ul>
<div id="mygrid_container" style="width:100%;height:280px;"></div>
</body>
</html>
