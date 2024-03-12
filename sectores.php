<?php
session_start();

include_once("config/db.php");
include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Sectores y Valorizacion de Horas Extras','".getRealIP()."')";
mysql_query($sql);
$date = date('Y-m-d');
$sqlAuditoria ="SELECT * FROM usuario_auditoria WHERE usuario_id = ".$_SESSION['userid']." AND fecha='".$date."'";
$rsTempAuditoria = mysql_query($sqlAuditoria);
$totalAuditoria = mysql_num_rows($rsTempAuditoria);

if($totalAuditoria == 1) {
    $rsAuditoria = mysql_fetch_array($rsTempAuditoria);
    $last_interaction = strtotime($rsAuditoria['last']);

    // Calcula los segundos entre la última interacción y el tiempo actual
    $elapsed_time_seconds = time() - $last_interaction;
    //$elapsed_time_minutes = round($elapsed_time_seconds / 60);

    // Actualiza la hora de última interacción y segundos conectados
    $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='Sectores y Valorizacion de Horas Extras', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
    mysql_query($sql_update);

}
$tabla 	= "sector"; //tabla
$label 	= "sector"; //nombre para el editar y agregar
$file 	= "sectores.php"; //archivo
$json	= "sectores.json.php"; //json
$abm 	= "sectores.am.php"; //agregar o modificar

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
<script src="library/dhtml/js/dhtmlxtreegrid.js"></script>
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
    mygrid.setHeader("Sector, Valor de hora extra"); 		//nombre de las columnas
    mygrid.setInitWidths("*,*"); 				//ancho de las columnas
    mygrid.setColAlign("left,left");			//alineacion de las columnas
	mygrid.setColSorting("str,int");			//tipo datos para ordenar
	mygrid.setColTypes("ro,price");				//editable o no
	mygrid.enableEditEvents(false,false);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?php echo $json?>","json");	//ruta al json con datos
	mygrid.init();
}

function edit(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_edit','Editar <?php echo $label?>','<?php echo $abm?>?dataid='+dataid,'400','200'); //nombre de los divs
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
function add(){
	createWindow('w_<?php echo $tabla?>_add','Agregar <?php echo $label?>','<?php echo $abm?>','400','200'); //botones
}
function addHoraExtra(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_horas','Horas extras','sectores_horas.am.php?sector_id='+dataid,'400','200'); //botones
	}
}

</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload();" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<li onclick="add();" class="item"><img src="images/bt_add.png" align="absmiddle" />  Agregar</li>
	<li onclick="edit();" class="item"><img src="images/bt_edit.png" align="absmiddle" />  Editar</li>
	<!--<li onclick="eliminar();" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Eliminar</li>-->
    <li onclick="addHoraExtra();" class="item"><img src="images/bt_pesos.png" align="absmiddle" />  Asignar nuevo valor de hora extra</li>
</ul>
<div id="mygrid_container" style="width:100%;height:280px;"></div>
</body>
</html>
