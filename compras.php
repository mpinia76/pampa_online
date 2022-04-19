<?php 
include_once("config/db.php");
include_once("config/user.php");

$tabla 	= "compra"; //tabla
$label 	= "compra"; //nombre para el editar y agregar
$file 	= "compras.php"; //archivo
$json	= "compras.json.php"; //json
$abm 	= "compras.add.php"; //agregar o modificar

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
	
function doInitGrid(){
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Creacion,Devengado,Rubro,Subrubro,Monto,Nro. Orden,Estado"); 		//nombre de las columnas
	mygrid.attachHeader("#text_filter,#text_filter,<div id='rubro_filter'></div>,#select_filter,#text_filter,#text_filter,#select_filter");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,&nbsp;,${#stat_total},&nbsp;,<div id='desaprobadas'><a href=\"#\" onclick=\"showDesaprobadas(1);\">Ver solo desaprobadas</a></div>");
	mygrid.enablePaging(true,20,10,"pagingArea",true,"recinfoArea");
    mygrid.setInitWidths("80,80,*,*,80,80,130"); 								//ancho de las columnas
    mygrid.setColAlign("left,left,left,left,right,right,right");						//alineacion de las columnas
	mygrid.setColSorting("str,str,na,str,int,int,str");							//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro,price,ro,ro");								//editable o no 
	mygrid.enableEditEvents(false,false,false,false,false,false,false);
	mygrid.enableMultiselect(true);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?php echo $json?>","json");										//ruta al json con datos
	mygrid.init();
	addFilter();
}
function makeFilter(){
	var rubro = $('#rubro').val();
	mygrid.clearAll();
	mygrid.load("<?php echo $json?>?rubro="+rubro,"json");
}
<?php 
$sql = "SELECT * FROM rubro ORDER BY rubro ASC"; 
$rsTemp = mysql_query($sql);
?>
function addFilter(){
	$('#rubro_filter').html('<select style="font-size:11px;" id="rubro" onchange="makeFilter();"><option selected="selected" value=""> </option><?PHP while($rs = mysql_fetch_array($rsTemp)){ ?><option value="<?php echo $rs['id']?>"><?php echo $rs['rubro']?></option><?PHP } ?></select>');
}

function showDesaprobadas(desaprobadas){
	if(desaprobadas == 0){
		$('#desaprobadas').html('<a href="#" onclick="showDesaprobadas(1);">Ver solo desaprobadas</a>');
	}else{
		$('#desaprobadas').html('<a href="#" onclick="showDesaprobadas(0);">Ver todas</a>');
	}
	mygrid.clearAll();
	mygrid.load("<?php echo $json?>?desaprobadas="+desaprobadas,"json");
}

function edit(action){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_edit','Ver <?php echo $label?>','compras.view.php?dataid='+dataid+'&action='+action,'600','400'); //nombre de los divs
	}
}
function eliminar(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm('¿Seguro desea eliminar el registro?, solo se borrara aquella compra que todavia no haya sido abonada.'))window.location.href='<?php echo $file?>?delete=on&dataid='+dataid; //ruta
	}
}
function add(){
	createWindow('w_<?php echo $tabla?>_add','Agregar <?php echo $label?>','<?php echo $abm?>','600','400'); //botones
}

function anular(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm('¿Seguro desea anular el pago?'))createWindow('w_<?php echo $tabla?>_anular','Anular <?php echo $label?>','anular_compra_procesa.php?dataid='+dataid,'600','400'); //botones
	}
}

</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<?php  if(ACCION_30){ ?><li onclick="add()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Agregar</li><?php  } ?>
	<?php  if(ACCION_32){ ?><li onclick="edit('consultar')" class="item"><img src="images/bt_view.png" align="absmiddle" />  Consultar</li><?php  } ?>
	<?php  if(ACCION_35){ ?><li onclick="edit('editar')" class="item"><img src="images/bt_edit.png" align="absmiddle" />  Editar</li><?php  } ?>
	<?php  if(ACCION_31){ ?><li onclick="edit('autorizar')" class="item"><img src="images/bt_autorice.png" align="absmiddle" />  Autorizar</li><?php  } ?>
	<?php  if(ACCION_32){ ?><li onclick="edit('abonar')" class="item"><img src="images/bt_pay.png" align="absmiddle" />  Abonar</li> <?php  } ?>
	<?php  if(ACCION_29){ ?><li onclick="eliminar()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Eliminar</li> <?php  } ?>
	<?php  if(ACCION_29){ ?><li onclick="anular()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Anular pago</li> <?php  } ?>
	
</ul>
<div id="mygrid_container" style="width:100%;height:330px;"></div>
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>
</body>
</html>
