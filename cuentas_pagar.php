<?
$tabla 	= "cuenta_a_pagar"; //tabla
$label 	= "cuenta"; //nombre para el editar y agregar
$file 	= "cuentas_pagar.php"; //archivo
$json	= "cuentas_pagar.json.php"; //json
$abm 	= "cuentas_pagar.view.php"; //agregar o modificar

//resta modificar los datos del grid

include_once("functions/delete.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin t&iacute;tulo</title>
<link rel="STYLESHEET" type="text/css" href="styles/toolbar.css"><script type="text/javascript" src="library/jquery/jquery-1.4.2.min.js"></script> 
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

var position = dhxWins.window('w_<?=$tabla?>').getPosition(); //id de la ventana

var xpos = position[0];
var ypos = position[1];

var mygrid;
var estado;
var dataid;
	
function doInitGrid(){
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Nro. orden,Tipo de opeacion,Proveedor,Monto,Estado"); 		//nombre de las columnas
   	mygrid.attachHeader("#text_filter,#select_filter,#select_filter,#text_filter,<div id='estado_filter'></div>");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,${#stat_total},&nbsp;");
	mygrid.enablePaging(true,12,10,"pagingArea",true,"recinfoArea");
	mygrid.setInitWidths("100,*,*,100,100"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,left,right,right");			//alineacion de las columnas
	mygrid.setColSorting("int,str,str,int,na");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,price,ro");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false);
	mygrid.enableMultiselect(true);
    mygrid.setSkin("dhx_skyblue");
	mygrid.load("<?=$json?>?pagado=no","json");	//ruta al json con datos
	mygrid.init();		addFilter();
}

function edit(action){
	dataid = mygrid.getSelectedRowId();
	data = dataid.split(",");
	
	if(!dataid){
		alert('Debe seleccioanr un registro');
	}else{
		if(data.length>1 && action != 'abonar'){
			alert('Debe seleccionar un solo registro');
		}else{
			createWindow('w_<?=$tabla?>_edit','Ver <?=$label?>','<?=$abm?>?dataid='+dataid+'&action='+action,'600','400'); //nombre de los divs
		}
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
	createWindow('w_subrubros_add','Agregar <?=$label?>','<?=$abm?>','600','400'); //botones
}function addFilter(){	$('#estado_filter').html('<select id="estado" onchange="makeFilter();"><option selected="selected" value="no">Pendiente</option><option value="si">Pagada</option><option value="t">Todas</option></select>');}
function makeFilter(){	estado = $('#estado').val();	mygrid.clearAll();	mygrid.load("<?=$json?>?pagado="+estado,"json");}
</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<li onclick="edit('consultar')" class="item"><img src="images/bt_view.png" align="absmiddle" />  Consultar</li>
	<li onclick="edit('abonar')" class="item"><img src="images/bt_pay.png" align="absmiddle" />  Abonar</li>
</ul>
<div id="mygrid_container" style="width:100%;height:320px;"></div>
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>
</body>
</html>
