<?php
session_start();
include_once("config/db.php");
include_once("functions/util.php");
auditarUsuarios('Transferencias a debitar');

if(isset($_POST['ok'])){

	//include_once("config/db.php");

	$sql	= "SELECT * FROM ".$_POST['tabla']." WHERE id=".$_POST['registro'];
	$rs		= mysqli_fetch_array(mysqli_query($conn,$sql));

	$cuenta_id	= $rs['cuenta_id'];
	$origen		= 'transferencia';
	$registro_id	= $_POST['registro'];

	$monto		= $rs['monto'] + $rs['interes'] - $rs['descuento'];

	$insert = "INSERT INTO cuenta_movimiento (cuenta_id,origen,registro_id,monto,fecha) VALUES ($cuenta_id,'$origen',$registro_id,-$monto,NOW())";
	mysqli_query($conn,$insert);

	$update = "UPDATE ".$_POST['tabla']." SET debitado=1, fecha_debitada=NOW() WHERE id=$registro_id";
	mysqli_query($conn,$update);

}

$file 	= "transferencias_movimientos.php"; //archivo
$json	= "transferencias_movimientos.json.php"; //json
$tabla = "transferencia_consumo";
$lable = "transferencia";

//resta modificar los datos del grid

include_once("functions/delete.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link rel="STYLESHEET" type="text/css" href="styles/toolbar.css">
<script type="text/javascript" src="library/jquery/jquery-1.4.2.min.js"></script>
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

var estado;
var inicio;
var fin;

function doInitGrid(){
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Fecha,Mes,Banco,Cuenta,Cuenta Destino,Concepto,Monto,Estado"); 		//nombre de las columnas
	mygrid.attachHeader("#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,<div id='estado_filter'></div>");
    mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;,${#stat_total},&nbsp;");
	mygrid.enablePaging(true,12,10,"pagingArea",true,"recinfoArea");
	mygrid.setInitWidths("80,80,*,*,*,200,100,100"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,left,left,left,left,left,left");			//alineacion de las columnas
	mygrid.setColSorting("str,str,str,str,str,str,str,na");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false,false,false,false);
    mygrid.setSkin("dhx_skyblue");
	mygrid.load("<?php echo $json?>?debitado=no","json");	//ruta al json con datos
	mygrid.init();
	addFilter();
	estado = "no";
	inicio = "";
	fin = "";
}
function makeFilter(){
	estado = $('#estado').val();
	mygrid.clearAll();
	mygrid.load("<?php echo $json?>?debitado="+estado+"&inicio="+inicio+"&fin="+fin,"json");
}
function filterByFecha(){
	inicio = $('#fecha_desde').val();
	fin = $('#fecha_hasta').val();
	mygrid.clearAll();
	mygrid.load("<?php echo $json?>?debitado="+estado+"&inicio="+inicio+"&fin="+fin,"json");
}
function addFilter(){
	$('#estado_filter').html('<select id="estado" onchange="makeFilter();"><option selected="selected" value="no">Pendiente</option><option value="si">Debitada</option><option value="t">Todas</option></select>');
}
function aprobar_viejo(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		document.form.tabla.value = 'transferencia_consumo';
		document.form.registro.value = dataid;
		document.form.submit();
	}
}
function aprobar(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		var datos = ({
			'id' : dataid,
			'tabla' : 'transferencia_consumo'
		});

		$.ajax({
			beforeSend: function(){
				$('#loading').show();
			},
			data: datos,
			url: 'functions/checkDebitado.php',
			success: function(data) {

				if(data == 'si'){
					if(confirm("La transferencia ya fue debitada  \n \n Desea continuar para cambiar la fecha de debito de la transferencia?")) {
						createWindow('w_<?php echo $tabla?>_debitar','Debitar <?php echo $label?>','transferencias_debitar.php?actualizar=1&dataid='+dataid,'600','200'); //nombre de los divs
					}
				}else{
					createWindow('w_<?php echo $tabla?>_debitar','Debitar <?php echo $label?>','transferencias_debitar.php?dataid='+dataid,'600','200'); //nombre de los divs

				}
				$('#loading').hide();

			}
		});

	}
}

function anular(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		var datos = ({
			'id' : dataid,
			'tabla' : 'transferencia_consumo'
		});

		$.ajax({
			beforeSend: function(){
				$('#loading1').show();
			},
			data: datos,
			url: 'functions/checkDebitado.php',
			success: function(data) {

				if(data == 'si'){
					if(confirm("Anular el debito?")) {

						$.ajax({
							data: datos,
							url: 'transferencia_anular_debito.php',
							success: function(data) {
								mygrid.clearAll();
								mygrid.load("<?php echo $json?>?debitado="+estado,"json");



							}
						});
					}
				}else{
					alert('No fue debitada');

				}

				$('#loading1').hide();

			}
		});

	}
}
function edit(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_edit','Editar <?php echo $label?>','transferencias_movimientos.am.php?dataid='+dataid,'600','400'); //nombre de los divs
	}
}

</script>
<!--JQuery Date Picker-->
<script type="text/javascript" src="library/datepicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="library/datepicker/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="library/datepicker/jquery.datePicker.min-2.1.2.js"></script>
<link href="library/datepicker/datePicker.css" rel="stylesheet" type="text/css" />
<style>
a.dp-choose-date {
	float: left;
	width: 16px;
	height: 16px;
	padding: 0;
	margin: 5px 3px 0;
	display: block;
	text-indent: -2000px;
	overflow: hidden;
	background: url(images/calendar.png) no-repeat;
}
a.dp-choose-date.dp-disabled {
	background-position: 0 -20px;
	cursor: default;
}
/* makes the input field shorter once the date picker code
 * has run (to allow space for the calendar icon
 */
input.dp-applied {
	width: 80px;
	float: left;
}
</style>
<!--/JQuery Date Picker-->
<script src="js/createWindow.js"></script>
</head>
<body onload="doInitGrid(); $('.fecha').datePicker({startDate:'01/01/2010'});">
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<input type="hidden" name="tabla" value="" />
<input type="hidden" name="ok" value="1" />
<input type="hidden" name="registro" value="" />
</form>
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<?php if(ACCION_59){ ?><li onclick="edit()" class="item"><img src="images/bt_edit.png" align="absmiddle" /> Editar</li><?php } ?>
	<?php if(ACCION_38){ ?><li onclick="aprobar()" class="item"><img src="images/ok.gif" align="absmiddle" />  Confirmar d&eacute;bito<img id="loading" src="images/loading.gif" style="display:none" /></li><?php } ?>
	<?php  if(ACCION_38){ ?><li onclick="anular()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Anular d&eacute;bito<img id="loading1" src="images/loading.gif" style="display:none" /></li><?php  } ?>
	<li class="item">Ver desde: </li>
	<li class="item"><input id="fecha_desde" type="text" class="fecha"> </li>
	<li class="item">hasta: </li>
	<li class="item"><input id="fecha_hasta" type="text" class="fecha"> </li>
	<li class="item"><img style="cursor:pointer;" onclick="filterByFecha();" src="images/bt_view.png" align="absmiddle" /> </li>
</ul>
<div id="mygrid_container" style="width:100%;height:330px;"></div>
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>
</body>
</html>
