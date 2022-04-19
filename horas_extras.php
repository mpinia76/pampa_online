<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }

$tabla 	= "empleado_hora_extra"; //tabla
$label 	= "hora extra"; //nombre para el editar y agregar
$file 	= "horas_extras.php"; //archivo
$json	= "horas_extras.json.php"; //json
$abm 	= "horas_extras.am.php"; //agregar o modificar

$empleado_id  = $_GET['empleado_id'];


//resta modificar los datos del grid

include_once("functions/delete.php");
include_once("config/db.php");
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
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
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
    mygrid.setHeader("Empleado,Creado,Sector,Solicitadas,Aprobadas,Mes,A&ntilde;o,Usuario,Estado"); 		//nombre de las columnas
    mygrid.setInitWidths("*,80,80,60,60,60,60,*,60"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,left,right,right,left,left,left,left");			//alineacion de las columnas
	mygrid.setColSorting("str,str,str,int,int,str,int,str,str");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false,false,false,false,false);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?php echo $json?>?empleado_id=<?php echo $empleado_id?>","json");	//ruta al json con datos
	mygrid.init();
}

function aprobar(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else if(mygrid.cellById(dataid, 8).getValue() != 'Pendiente'){
		alert('La hora extra debe estar pendiente de aprobacion');
	}else{
		createWindow('w_empleado_hora_extra_aprobar','Aprobar horas extras','horas_extras_aprobar.php?id='+dataid,'300','200');
		//window.location.href='horas_extras.php?hora_extra_id='+dataid+'&estado=1&empleado_id=<?=$empleado_id?>';
	}
}
function desaprobar(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else if(mygrid.cellById(dataid, 7).getValue() != 'Pendiente'){
		alert('La hora extra debe estar pendiente de aprobacion');
	}else{
		window.location.href='horas_extras.php?hora_extra_id='+dataid+'&estado=2&empleado_id=<?php echo $empleado_id?>';
	}
}

</script>
<script src="js/createWindow.js"></script>
</head>


<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<li onclick="aprobar()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Aprobar</li>
</ul>
<div id="mygrid_container" style="width:100%;height:370px;"></div>
</body>
</html>
