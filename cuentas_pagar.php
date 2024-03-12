<?php
session_start();


$tabla 	= "cuenta_a_pagar"; //tabla
$label 	= "cuenta"; //nombre para el editar y agregar
$file 	= "cuentas_pagar.php"; //archivo
$json	= "cuentas_pagar.json.php"; //json
$abm 	= "cuentas_pagar.view.php"; //agregar o modificar

//resta modificar los datos del grid

include_once("functions/delete.php");
include_once("config/db.php");
include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Cuentas a pagar','".getRealIP()."')";

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
    $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='Cuentas a pagar', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
    mysql_query($sql_update);

}
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

var position = dhxWins.window('w_<?php echo $tabla?>').getPosition(); //id de la ventana

var xpos = position[0];
var ypos = position[1];

var mygrid;
var estado;
var dataid;
	
function doInitGrid(){
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Nro. orden,Tipo de operacion,Proveedor,Rubro,Subrubro,Monto,Estado,Fecha de devengado,Cantidad de dias, Numero de Factura, Responsable"); 		//nombre de las columnas
   	mygrid.attachHeader("#text_filter,#select_filter,#select_filter,#select_filter,#select_filter,#text_filter,<div id='estado_filter'></div>,#text_filter,#text_filter,#text_filter,#text_filter");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;,${#stat_total},&nbsp;,&nbsp;,&nbsp;,&nbsp;,&nbsp;");
	mygrid.enablePaging(true,12,10,"pagingArea",true,"recinfoArea",true,true,true,true);
	mygrid.setInitWidths("100,*,*,*,*,100,100,*,*,*,*"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,left,left,left,right,right,right,right,right,right");			//alineacion de las columnas
	mygrid.setColSorting("int,str,str,str,str,int,na,str,int,str,str");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro,ro,price,ro,ro,ro,ro,ro");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false,false,false,false,false,false,false);
	mygrid.enableMultiselect(true);
    mygrid.setSkin("dhx_skyblue");
	mygrid.load("<?php echo $json?>?pagado=no","json");	//ruta al json con datos
	mygrid.init();		
	addFilter();
	
}
function borrarPlan(){
	dataid = mygrid.getSelectedRowId();
	data = dataid.split(",");
	
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
	if(data.length>1){
		alert('Debe seleccionar un solo registro');
	}else{
        
		if(confirm('¿Seguro desea anular el plan?')){
    		$.ajax({
	        url : 'v2/plans/borrar',
	        data: {'data' : {'id' : dataid},'tipo':'cuentas'},
	        type: 'POST',
	        dataType: 'json',
	        success: function(data){
	            $('#loading_save').hide();
	            if(data.error!=''){
	                
	                    
	                    alert(data.error);
	                
	            }else{
	                document.location.reload();
	            }
	        }
	    	})
    	}
	}
	}
}

function generarPlan(){
	
	dataid = mygrid.getSelectedRowId();
	//alert(dataid);
	data = dataid.split(",");
	
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
    
	    
	    
		var datos = ({
			'items' : dataid
		});
		
		$.ajax({
			beforeSend: function(){
				$('#loading').show();
			},
			data: datos,
			url: 'functions/controlarItems.php',
			dataType:"json",
			success: function(data) {
				$('#loading').hide();
				if(data.tipos==0){
					alert('Los items deben ser del mismo tipo de operacion');
					}
				else{
					if(data.pendientes==0){
						alert('Para pagos conjuntos seleccione �rdenes en estado "Pendiente de pago"');
					}
					else{
						if(data.iguales==0){
							alert('Los items deben ser del mismo rubro, subrubro y proveedor');
						}
						else{
							//alert(data.ids);
							createWindow('w_generar_plan','Generar plan de pagos','v2/plans/crear/'+dataid+'/c'+data.tipo,'430','450');
						}
					}
					
				}
				
			}
		});
	   
	    
	}
    

}

function edit(action){
	dataid = mygrid.getSelectedRowId();
	data = dataid.split(",");
	
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(data.length>1 && action != 'abonar'){
			alert('Debe seleccionar un solo registro');
		}else{
			createWindow('w_<?php echo $tabla?>_edit','Ver <?php echo $label?>','<?php echo $abm?>?dataid='+dataid+'&action='+action,'600','400'); //nombre de los divs
		}
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

function anular(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm('¿Seguro desea anular el pago?'))createWindow('w_<?php echo $tabla?>_anular','Anular <?php echo $label?>','anular_cuenta_procesa.php?dataid='+dataid,'600','400'); //botones
	}
}

function add(){
	createWindow('w_subrubros_add','Agregar <?php echo $label?>','<?php echo $abm?>','600','400'); //botones
}
function addFilter(){	$('#estado_filter').html('<select id="estado" onchange="makeFilter();"><option selected="selected" value="no">Pendiente</option><option value="si">Pagada</option><option value="p">Plan de pago</option><option value="t">Todas</option></select>');}
function makeFilter(){	estado = $('#estado').val();	mygrid.clearAll();	mygrid.load("<?php echo $json?>?pagado="+estado,"json");}
</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<li onclick="edit('consultar')" class="item"><img src="images/bt_view.png" align="absmiddle" />  Consultar</li>
	<li onclick="edit('abonar')" class="item"><img src="images/bt_pay.png" align="absmiddle" />  Abonar</li>
	<li onclick="anular()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Anular pago</li>
	<li onclick="generarPlan();"  class="item"><img src="images/bt_pay.png" align="absmiddle" />Generar plan de pagos</li>
	<li onclick="borrarPlan()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Anular plan de pagos</li>
</ul>
<div id="mygrid_container" style="width:100%;height:320px;"></div>
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>
</body>
</html>
