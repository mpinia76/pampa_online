<?
session_start();
$user_id = $_SESSION['userid'];

$tabla 	= "caja"; //tabla
$label 	= "caja"; //nombre para el editar y agregar
$file 	= "cajas.php"; //archivo
$json	= "cajas.json.php"; //json
$abm 	= "cajas.am.php"; //agregar o modificar

include_once("config/db.php");
include_once("config/user.php");

if(isset($_GET['caja_id']) and $_GET['sincronizar']=='si'){
	$caja_id = $_GET['caja_id'];
	$fecha = date("Y-m-d H:i:s");
	//obtengo el saldo de la caja
	$saldo_sql = "SELECT SUM(monto) as saldo FROM caja_movimiento WHERE caja_id=$caja_id";
	$saldo_rs = mysql_fetch_array(mysql_query($saldo_sql));
	$saldo = $saldo_rs['saldo'];
	
	$insert = "INSERT INTO caja_sincronizada (caja_id,usuario_id,fecha,monto) VALUES ($caja_id,$user_id,'$fecha','$saldo')";
	mysql_query($insert);
	echo mysql_error();
}

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

var position = dhxWins.window('w_<?=$tabla?>').getPosition(); //id de la ventana

var xpos = position[0];
var ypos = position[1];

var mygrid;

var dataid;
	
function doInitGrid(){
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Caja,Actualizada por,En la fecha, Por el monto,Saldo actual"); 		//nombre de las columnas
    mygrid.setInitWidths("150,*,120,100,100"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,left,right,right");			//alineacion de las columnas
	mygrid.setColSorting("str,str,str,float,float");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,price,price");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?=$json?>","json");	//ruta al json con datos
	mygrid.init();
}

function ver_detalle(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?=$tabla?>_detalle','Detalle de movimientos','cajas_detalle.php?caja_id='+dataid,'610','500'); //nombre de los divs
	}
}
function eliminar(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm('¿Seguro desea eliminar el registro?'))window.location.href='<?=$file?>?delete=on&dataid='+dataid; //ruta
	}
}
function add(){
	createWindow('w_<?=$tabla?>_add','Agregar <?=$label?>','<?=$abm?>','600','400'); //botones
}
function edit(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?=$tabla?>_edit','Editar <?=$label?>','<?=$abm?>?dataid='+dataid,'600','400'); //nombre de los divs
	}
}
function add_movimiento(){
	createWindow('w_<?=$tabla?>_detalle_add','Agregar movimiento','cajas_detalle.am.php','600','400'); //botones
}
function add_transferencia(){
	createWindow('w_<?=$tabla?>_transferencia_add','Agregar transferencia','cajas_transferencia.am.php','600','400'); //botones
}
function add_deposito(){
	createWindow('w_<?=$tabla?>_deposito_add','Hacer deposito','caja_extraccion.am.php','600','400'); //botones
}
function sincronizar(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm("Confirma que ha sincronizado el saldo de caja?")){
			window.location.href='<?=$file?>?sincronizar=si&caja_id='+dataid;
		}
	}
}

</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<? if(ACCION_44){ ?><li onclick="add()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Agregar</li><? } ?>
                    <? if(ACCION_44){ ?><li onclick="edit()" class="item"><img src="images/bt_edit.png" align="absmiddle" />  Editar</li><? } ?>
	<li onclick="ver_detalle()" class="item"><img src="images/bt_view.png" align="absmiddle" />  Detalles</li>
	<? if(ACCION_55){ ?><li onclick="add_movimiento()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Agregar movimiento</li><? } ?>
	<? if(ACCION_45){ ?><li onclick="add_transferencia()" class="item"><img src="images/bt_transfer.png" align="absmiddle" />  Transferencias</li><? } ?>
    <? if(ACCION_46){ ?><li onclick="add_deposito()" class="item"><img src="images/bt_deposit.png" align="absmiddle" />  Depositar en cuenta</li><? } ?>
    <? if(ACCION_47){ ?> <li onclick="sincronizar()" class="item"><img src="images/bt_sincronize.png" align="absmiddle" /> Sincronizar caja</li><? } ?>
</ul>
<div id="mygrid_container" style="width:100%;height:280px;"></div>
</body>
</html>
