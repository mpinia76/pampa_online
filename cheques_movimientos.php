<?php
session_start();
$user_id = $_SESSION['userid'];

include_once("config/db.php");
include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Cheques a debitar','".getRealIP()."')";
mysqli_query($conn,$sql);
include_once("config/user.php");

if(isset($_POST['ok'])){

	$sql	= "SELECT * FROM ".$_POST['tabla']." WHERE id=".$_POST['registro'];
	$rs		= mysqli_fetch_array(mysqli_query($conn,$sql));
	
	if(strtotime($rs['fecha']) <= time()){ //la fecha de pago es menor o igual a la fecha de hoy
		$cuenta_id	= $rs['cuenta_id'];
		$origen		= 'cheque';
		$registro_id	= $_POST['registro'];
		
		$monto		= $rs['monto'];
	
		$insert = "INSERT INTO cuenta_movimiento (cuenta_id,origen,registro_id,monto,fecha) VALUES ($cuenta_id,'$origen',$registro_id,-$monto,NOW())";
		mysqli_query($conn,$insert);
		
		$update = "UPDATE ".$_POST['tabla']." SET debitado=1, fecha_debitado=NOW(), debitado_por=$user_id  WHERE id=".$rs['id'];
		mysqli_query($conn,$update);
	}else{
		$error = true;
	}
	
}
	
$tabla 	= "cheque_consumo"; //tabla
$label 	= "cheque"; //nombre para el editar y agregar
$file 	= "cheques_movimientos.php"; //archivo
$json	= "cheques_movimientos.json.php"; //json

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
    mygrid.setHeader("Fecha de pago,Fecha debitado,Mes,Banco,Cuenta,Numero,Titular,Concepto,Monto,Estado"); 		//nombre de las columnas
    mygrid.setInitWidths("80,80,80,*,*,50,*,90,70,100"); 				//ancho de las columnas
	mygrid.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,<div id='estado_filter'></div>");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;,${#stat_total},&nbsp;");
    mygrid.setColAlign("left,left,left,left,left,left,left,left,left,left");			//alineacion de las columnas
	mygrid.enablePaging(true,12,10,"pagingArea",true,"recinfoArea");
	mygrid.setColSorting("date,date,str,str,str,str,str,str,str,na");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro,ro,ro,ro,ro,ro,ro");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false,false,false,false,false,false);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?php echo $json?>?debitado=no","json");	//ruta al json con datos
	mygrid.init();
	addFilter();
	estado = "no";
	inicio = "";
	fin = "";
}


function aprobar_viejo(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		document.form.tabla.value = 'cheque_consumo';
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
			'tabla' : 'cheque_consumo'
		});
		
		$.ajax({
			beforeSend: function(){
				$('#loading').show();
			},
			data: datos,
			url: 'functions/checkDebitado.php',
			success: function(data) {
			
				if(data == 'si'){		
					if(confirm("El cheque ya fue debitado  \n \n Desea continuar para cambiar la fecha de debito del cheque?")) {
						createWindow('w_<?php echo $tabla?>_debitar','Debitar <?php echo $label?>','cheques_debitar.php?actualizar=1&dataid='+dataid,'600','200'); //nombre de los divs
					}
				}else{
					createWindow('w_<?php echo $tabla?>_debitar','Debitar <?php echo $label?>','cheques_debitar.php?dataid='+dataid,'600','200'); //nombre de los divs
					
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
			'tabla' : 'cheque_consumo'
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
							url: 'cheque_anular_debito.php',
							success: function(data) {
								mygrid.clearAll();
								mygrid.load("<?php echo $json?>?debitado="+estado,"json");
					
								
								
							}
						});
					}
				}else{
					alert('No fue debitado');
					
				}
				
				$('#loading1').hide();	
			}
		});
		
		
	}
}

function filterByFecha(){
	inicio = $('#fecha_desde').val();
	fin = $('#fecha_hasta').val();
	mygrid.clearAll();
	mygrid.load("<?php echo $json?>?debitado="+estado+"&inicio="+inicio+"&fin="+fin,"json");
}
function makeFilter(){
	estado = $('#estado').val();
	mygrid.clearAll();
	mygrid.load("<?php echo $json?>?debitado="+estado+"&inicio="+inicio+"&fin="+fin,"json");
}
function addFilter(){
	$('#estado_filter').html('<select id="estado" onchange="makeFilter();"><option selected="selected" value="no">Pendiente</option><option value="si">Debitada</option><option value="t">Todas</option></select>');
}
function edit(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_edit','Editar <?php echo $label?>','cheque_consumo.am.php?dataid='+dataid,'600','400'); //nombre de los divs
	}
}
function add(){
	createWindow('w_<?php echo $tabla?>_add','Agregar <?php echo $label?>','cheque_consumo.am.php','600','400'); //nombre de los divs
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
<script>
<?php  if($error){ ?>
	alert("La fecha de pago del cheque debe ser inferior o igual a la fecha de hoy");
<?php  } ?>
</script>
<form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<input type="hidden" name="tabla" value="" />
<input type="hidden" name="ok" value="1" />
<input type="hidden" name="registro" value="" />
</form>
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<?php  if(ACCION_37){ ?><li onclick="edit()" class="item"><img src="images/bt_edit.png" align="absmiddle" />  Editar</li><?php  } ?>
    <?php  if(ACCION_49){ ?><li onclick="add()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Agregar</li><?php  } ?>
	<?php  if(ACCION_36){ ?><li onclick="aprobar()" class="item"><img src="images/ok.gif" align="absmiddle" />  Confirmar d&eacute;bito<img id="loading" src="images/loading.gif" style="display:none" /></li><?php  } ?>
	<?php  if(ACCION_36){ ?><li onclick="anular()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Anular d&eacute;bito<img id="loading1" src="images/loading.gif" style="display:none" /></li><?php  } ?>
	<li class="item">Ver desde: </li>
	<li class="item"><input id="fecha_desde" type="text" class="fecha"> </li>
	<li class="item">hasta: </li>
	<li class="item"><input id="fecha_hasta" type="text" class="fecha"> </li>
	<li class="item"><img style="cursor:pointer;" onclick="filterByFecha();" src="images/bt_view.png" align="absmiddle" /> </li>
</ul>
<div id="mygrid_container" style="width:100%;height:320px;"></div>
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>
</body>
</html>
