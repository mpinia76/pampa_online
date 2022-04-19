<?php

$file 	= "cuenta_detalle.php"; //archivo
$json	= "cuenta_detalle.json.php"; //json
$abm 	= "cuenta_detalle.am.php"; //agregar o modificar

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
<script src="library/dhtml/js/dhtmlxgrid_export.js"></script>
<script>
var dhxWins = parent.dhxWins;

var position = dhxWins.window('w_<?php echo $tabla?>').getPosition(); //id de la ventana

var xpos = position[0];
var ypos = position[1];

var mygrid;

var dataid;
var timeoutHnd;	
function doInitGrid(){
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Fecha, Detalle, Orden/Reserva, Usuario, Credito, Debito, Saldo"); 		//nombre de las columnas
    mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,&nbsp;");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,&nbsp;,${#stat_total},${#stat_total},&nbsp;");
	mygrid.enablePaging(true,10,10,"pagingArea",true,"recinfoArea");
	mygrid.setInitWidths("60,*,60,110,80,80,90"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,right,left,right,right,right");			//alineacion de las columnas
	mygrid.setColSorting("str,str,int,str,int,int,int");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro,price,price,price");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false,false,false);
    mygrid.setSkin("dhx_skyblue");	
    var desde_mask = document.getElementById("fecha_desde").value;
	var hasta_mask = document.getElementById("fecha_hasta").value;	
	mygrid.load("<?php echo $json?>?cuenta_id=<?php echo $_GET['cuenta_id']?>&desde_mask="+desde_mask+"&hasta_mask="+hasta_mask,"json");	//ruta al json con datos
	mygrid.init();
}

function doSearch(ev){
	
	var elem = ev.target||ev.srcElement;
	if(timeoutHnd)
		clearTimeout(timeoutHnd);
	timeoutHnd = setTimeout(reloadGrid,500)
}
function reloadGrid(){
	var desde_mask = document.getElementById("fecha_desde").value;
	var hasta_mask = document.getElementById("fecha_hasta").value;
	//showLoading(true);
	mygrid.clearAndLoad("<?php echo $json?>?cuenta_id=<?php echo $_GET['cuenta_id']?>&desde_mask="+desde_mask+"&hasta_mask="+hasta_mask,"json");
	if (window.a_direction)
		myGrid.setSortImgState(true,window.s_col,window.a_direction);
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
function add(){
	createWindow('w_subrubros_add','Agregar <?php echo $label?>','<?php echo $abm?>','600','400'); //botones
}

function exportarExcel(){
	var desde = $('#fecha_desde').val();
	var hasta = $('#fecha_hasta').val();
	//alert('library/grid-excel-php/generate.php?cuenta=1&nombre=administracion&desde='+desde+'hasta='+hasta);
	mygrid.toExcel('library/grid-excel-php/generate.php?cuenta=1&nombre=<?php echo $_GET['cuenta_id']?>&desde='+desde+'&hasta='+hasta);
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
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<li class="item">Ver desde: </li>
	<li class="item"><input id="fecha_desde" type="text" class="fecha" value="<?php /*echo date('d/m/Y',strtotime('-30 day'));*/  ?>"> </li>
	<li class="item">hasta: </li>
	<li class="item"><input id="fecha_hasta" type="text" class="fecha" value="<?php /*echo date('d/m/Y');*/  ?>"> </li>
	<li class="item"><img style="cursor:pointer;" onclick="reloadGrid();" src="images/bt_view.png" align="absmiddle" /> </li>
	<li class="item"><img style="cursor:pointer;" onclick="exportarExcel()" src="images/bt_excel.png" align="absmiddle" /> </li>
</ul>

<div id="mygrid_container" style="width:100%;height:300px;"></div>
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>
</body>
</html>
