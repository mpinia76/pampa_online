<?php
session_start();
include_once("config/db.php");
include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Pago de haberes','".getRealIP()."')";
mysql_query($sql);
include_once("config/user.php");

$tabla	= "empleados_sueldos";
$file 	= "empleados_sueldos.php"; //archivo
$json	= "empleados_sueldos.json.php"; //json

//resta modificar los datos del grid
include_once("functions/delete.php");

if(ACCION_66 and ACCION_67){
	$espacio = 'todos';
}elseif(ACCION_66){
	$espacio = 'oficina';
}elseif(ACCION_67){
	$espacio = 'hotel';
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
	$("#input_ano").change();
	mygrid = new dhtmlXGridObject('mygrid_container');
	mygrid.setImagePath("library/dhtml/imgs/");
    mygrid.setHeader("Legajo, Nombre y Apellido, Centro de costos, Sector, Salario Acordado, Aguinaldo, Hrs. Extras, Descuentos, Total a pagar, Adelantos, Saldo a cobrar, Pagado, Estado"); 		//nombre de las columnas
   	mygrid.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#select_filter");
	mygrid.attachHeader("&nbsp;,&nbsp;,&nbsp;,&nbsp;,${#stat_total},${#stat_total},${#stat_total},${#stat_total},${#stat_total},${#stat_total},${#stat_total},${#stat_total},&nbsp;");
	mygrid.setInitWidths("80,*,*,*,60,60,60,60,60,60,60,60,80"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,left,left,right,right,right,right,right,right,right,right,left");			//alineacion de las columnas
	mygrid.setColSorting("str,str,str,str,int,int,int,int,int,int,int,int,str");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro,ro,price,price,price,price,price,price,price,price,ro");				//editable o no
	mygrid.enableEditEvents(false,false,false,false,false,false,false,false,false,false,false,false,false);
	mygrid.enableMultiselect(true);
    mygrid.setSkin("dhx_skyblue");
	//mygrid.load("<?php echo $json?>?ano=<?php echo date('Y')?>&mes=<?php echo date('n')?>&espacio=<?php echo $espacio?>","json");	//ruta al json con datos
	
	mygrid.attachEvent("onXLS",function(){
				$('#mensaje').text('Cargando...')
				document.getElementById('mensaje').style.display='block';
			});
	mygrid.attachEvent("onXLE",function(){
		document.getElementById('mensaje').style.display='none';
	});
	mygrid.init();
}
function ver(){
	var dataid = mygrid.getSelectedRowId();
	var mes = $('#input_mes').val();
	var ano = $('#input_ano').val();

	if(ano == ''){
		alert('Debe completar con un ano');
	}else if(mes == 'n'){
		alert('Debe seleccionar un mes');
	}else{
		
		$('#mygrid_container').show();
		//$('#mensaje').hide();
		mygrid.clearAll();	
		mygrid.load("<?php echo $json?>?ano="+ano+"&mes="+mes+"&espacio=<?php echo $espacio?>","json");
	}
}
function pagar(){
	var dataid = mygrid.getSelectedRowId();
	var mes = $('#input_mes').val();
	var ano = $('#input_ano').val();
	
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else if(mes == ''){
		alert('Debe completar con un ano');
	}else{
		createWindow('w_<?php echo $tabla?>_pagar','Pago de sueldo','empleado.pagar.php?empleado_id='+dataid+'&mes='+mes+'&ano='+ano,'650','550'); //nombre de los divs
	}
}
function detalle(){
	var dataid = mygrid.getSelectedRowId();
	var mes = $('#input_mes').val();
	var ano = $('#input_ano').val();
	
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else if(mes == ''){
		alert('Debe completar con un ano');
	}else{
		createWindow('w_<?php echo $tabla?>_detalle','Detalle de sueldo','empleado_sueldo.detalle.php?empleado_id='+dataid+'&mes='+mes+'&ano='+ano,'650','550'); //nombre de los divs
	}
}
function addAdelanto(){
    dataid = mygrid.getSelectedRowId();
    var mes = $('#input_mes').val();
	var ano = $('#input_ano').val();
    if(!dataid){
            alert('Debe seleccionar un registro');
    }else{
        createWindow('w_empleado_adelanto','Adelanto','empleado.adelanto.php?empleado_id='+dataid+'&mes='+mes+'&ano='+ano,'500','400');
    }
}
function addHoraExtra(){
    dataid = mygrid.getSelectedRowId();
    var mes = $('#input_mes').val();
	var ano = $('#input_ano').val();
    if(!dataid){
            alert('Debe seleccionar un registro');
    }else{
        createWindow('w_emplado_add_hora_extra','Agregar Horas extras','empleado.horas_extras.php?empleado_id='+dataid+'&mes='+mes+'&ano='+ano,'500','300');
   }
}

function borrar_pago(){
	var dataid = mygrid.getSelectedRowId();
	var mes = $('#input_mes').val();
	var ano = $('#input_ano').val();
	
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else if(mes == ''){
		alert('Debe completar con un ano');
	}else{
		if(confirm("Esta seguro que desea anular el pago ?")){
			createWindow('w_<?php echo $tabla?>_borrar_pago','Anular Pago de sueldo','borrar_pago_procesa.php?empleado_id='+dataid+'&mes='+mes+'&ano='+ano,'650','550'); //nombre de los divs
		}
	}
}

function cargarMeses(year){
	$("#input_mes").empty();
	$("#input_mes").append("<option value=\"n\">Seleccione...</option>");
	var hoy = new Date();
	
	var meses={
			  1:"Enero",
			  2:"Febrero",
			  3:"Marzo",
			  4:"Abril",
			  5:"Mayo",
			  6:"Junio",
			  7:"Julio",
			  8:"Agosto",
			  9:"Septiembre",
			  10:"Octubre",
			  11:"Noviembre",
			  12:"Diciembre",
			};
	$.each(meses, function(k,v){
		
		fecha2 = new Date(year.value,(k-1),1);
		if(hoy.getTime()>=fecha2.getTime()){
			$("#input_mes").append("<option value=\""+k+"\">"+v+"</option>");
		}
	});
	
}

</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu" style="height:28px;">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<li class="item" style="width:190px; text-align:right;"> &nbsp; <input type="test" size="5" id="input_ano" value="<?php echo date('Y');?>" onChange="cargarMeses(this)"> 
	<select id="input_mes">
    <option value="n" >Seleccione...</option>
	<option value="1" >Enero</option>
	<option value="2"  >Febrero</option>
	<option value="3"  >Marzo</option>
	<option value="4"  >Abril</option>
	<option value="5"  >Mayo</option>
	<option value="6"  >Junio</option>
	<option value="7"  >Julio</option>
	<option value="8"  >Agosto</option>
	<option value="9"  >Septiembre</option>
	<option value="10" >Octubre</option>
	<option value="11">Noviembre</option>
	<option value="12" >Diciembre</option>
	</select>
	</li>	
	<li onclick="ver()" class="item">Ver</li>
    <?php  if(ACCION_63){ ?><li onclick="pagar()" class="item">Pagar</li><?php  } ?>
    <?php  if(ACCION_63){ ?><li onclick="detalle()" class="item">Detalle</li><?php  } ?>
    <?php  if(ACCION_70){ ?><li onclick="addAdelanto()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Dar Adelanto</li><?php  } ?>
     <?php  if(ACCION_71){ ?><li onclick="addHoraExtra()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Asignar Hrs. Extras</li><?php  } ?>
    <?php  if(ACCION_63){ ?><li onclick="borrar_pago()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Borrar pago</li><?php  } ?>
</ul>
<div id="mensaje" style="text-align:center; font-family:Arial, Helvetica, sans-serif; font-size:12px;">Seleccione un mes para ver el detalle</div>
<div id="mygrid_container" style="width:100%;height:320px; display:none;"></div><!--
<ul id="menu">
<div style="float:left; margin-top:4px; width:300px;" id="pagingArea"></div>
<div style="float:right; margin-top:4px; margin-right:5px;" id="recinfoArea"></div>
</ul>-->
</body>
</html>
