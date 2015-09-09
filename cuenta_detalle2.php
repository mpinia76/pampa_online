<?

$file 	= "cuenta_detalle.php"; //archivo
$json	= "cuenta_detalle.json2.php"; //json
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

var dataid;
	
function doInitGrid(){
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Fecha, Detalle, Nro. Orden, Monto,Balance"); 		//nombre de las columnas
	mygrid.attachHeader("#text_filter,#text_filter,#text_filter,#text_filter,&nbsp;");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,${#stat_total},&nbsp;");
	mygrid.enablePaging(true,10,10,"pagingArea",true,"recinfoArea");
    mygrid.setInitWidths("80,*,100,90,90"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,right,right,right");			//alineacion de las columnas
	mygrid.setColSorting("str,str,int,int,int");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,price,price");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?=$json?>?cuenta_id=<?=$_GET['cuenta_id']?>","json");	//ruta al json con datos
	mygrid.init();
}

function edit(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?=$tabla?>_edit','Editar <?=$label?>','<?=$abm?>?dataid='+dataid,'600','400'); //nombre de los divs
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
}

</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
</ul>
<div id="mygrid_container" style="width:100%;height:300px;"></div>
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>
</body>
</html>
