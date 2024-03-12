<?php
session_start();
include_once("config/db.php");
include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Administrador de Empleados','".getRealIP()."')";
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
    $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='Administrador de Empleados', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
    mysql_query($sql_update);

}
include_once("config/user.php");

$tabla 	= "empleado"; //tabla
$label 	= "empleado"; //nombre para el editar y agregar
$file 	= "empleados.php"; //archivo
$json	= "empleados.json.php"; //json
$abm 	= "empleados.am.php"; //agregar o modificar

if(ACCION_64 and ACCION_65){
	$espacio = 'todos';
}elseif(ACCION_64){
	$espacio = 'oficina';
}elseif(ACCION_65){
	$espacio = 'hotel';
}

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
<script type="text/javascript" src="library/jquery/jquery-1.4.2.min.js"></script>
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
    mygrid.setHeader("Nombre,Apellido,Estado"); 		//nombre de las columnas
    mygrid.attachHeader("#text_filter,#text_filter,&nbsp;");
    mygrid.setInitWidths("*,*,*"); 				//ancho de las columnas
    mygrid.setColAlign("left,left,left");			//alineacion de las columnas
	mygrid.setColSorting("str,str,str");			//tipo datos para ordenar
	mygrid.setColTypes("ro,ro,ro");				//editable o no
	mygrid.enableEditEvents(false,false,false);
    mygrid.setSkin("dhx_skyblue");		
	mygrid.load("<?php echo $json?>?espacio=<?php echo $espacio?>&activo=1","json");	//ruta al json con datos
	mygrid.init();
}
/*function dar_baja(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm('¿Seguro desea dar de baja al empleado? \n El empleado no aparecera listado en niguna parte del sistema.')){
			$('#baja_loading').show();
			$.ajax({
				type : 'POST',
				data : { 'empleado_id' : dataid },
				url : 'empleado_baja.php',
				success: function(){
					$('#baja_loading').hide();
					mygrid.clearAll();
					mygrid.load("<?php echo $json?>?espacio=<?php echo $espacio?>&activo=1","json");
				}
			});
		}
	}
}*/

function dar_baja(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm('¿Seguro desea dar de baja al empleado?')){
			createWindow('w_<?php echo $tabla?>_bajar','Dar de baja <?php echo $label?>','empleado_baja.php?dataid='+dataid,'600','200'); //nombre de los divs
		}
	}
}
function dar_alta(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		if(confirm('¿Seguro desea dar de alta al empleado?')){
			createWindow('w_<?php echo $tabla?>_subir','Dar de alta <?php echo $label?>','empleado_alta.php?dataid='+dataid,'600','200'); //nombre de los divs
		}
	}
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
		if(confirm('¿Seguro desea eliminar el registro?')) window.location.href='<?php echo $file?>?delete=on&dataid='+dataid; //ruta
	}
}
function view(){
	dataid = mygrid.getSelectedRowId();
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else{
		createWindow('w_<?php echo $tabla?>_view','Consultar <?php echo $label?>','empleados.ficha.php?empleado_id='+dataid,'850','550'); //nombre de los divs
	}
}
function pagar(){
	var dataid = mygrid.getSelectedRowId();
	var mes = $('#input_mes').val();
	var ano = $('#input_ano').val();
	
	if(!dataid){
		alert('Debe seleccionar un registro');
	}else if(mes == 'null'){
		alert('Debe seleccionar un mes');
	}else{
		createWindow('w_<?php echo $tabla?>_pagar','Pago de sueldo','empleado.pagar.php?empleado_id='+dataid+'&mes='+mes+'&ano='+ano,'650','550'); //nombre de los divs
	}
}
function add(){
	createWindow('w_<?php echo $tabla?>_add','Agregar <?php echo $label?>','<?php echo $abm?>','700','400'); //botones
}
function addAdelanto(){
    dataid = mygrid.getSelectedRowId();
    if(!dataid){
            alert('Debe seleccionar un registro');
    }else{
        createWindow('w_empleado_adelanto','Adelanto','empleado.adelanto.php?empleado_id='+dataid,'500','400');
    }
}
function addHoraExtra(){
    dataid = mygrid.getSelectedRowId();
    if(!dataid){
            alert('Debe seleccionar un registro');
    }else{
        createWindow('w_emplado_add_hora_extra','Agregar Horas extras','empleado.horas_extras.php?empleado_id='+dataid,'500','300');
   }
}

function filtroActivo(select){
		
		$('#mygrid_container').show();
		$('#mensaje').hide();
		mygrid.clearAll();	
		mygrid.load("<?php echo $json?>?espacio=<?php echo $espacio?>&activo="+select.value,"json");
	
}

</script>
<script src="js/createWindow.js"></script>
</head>

<body onload="doInitGrid();">
<ul id="menu">
	<li onclick="window.location.reload()" class="item"><img src="images/bt_reload.png" align="absmiddle" /></li>
	<li class="item"> 
	<select id="selectActivo" name="selectActivo" onChange="filtroActivo(this)">
    <option value="1" >Activos</option>
	<option value="0" >Inactivos</option>
	</select>
	</li>	
	<?php  if(ACCION_13){ ?><li onclick="add()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Agregar</li><?php  } ?>
    <?php  if(ACCION_14){ ?><li onclick="edit()" class="item"><img src="images/bt_edit.png" align="absmiddle" />  Editar</li><?php  } ?>
	<?php  if(ACCION_60){ ?><li onclick="view()" class="item"><img src="images/bt_view.png" align="absmiddle" />  Consultar</li><?php  } ?>
    <?php  if(ACCION_69){ ?><li onclick="dar_baja()" class="item"><img src="images/bt_baja.png" align="absmiddle" />  Dar de baja &nbsp; 
    <li onclick="dar_alta()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Dar de alta &nbsp; 
    	<img align="absmiddle" src="images/loading.gif" height="15" style="display:none;" id="baja_loading" /> </li><?php  } ?>
    
     <?php  if(ACCION_70){ ?><li onclick="addAdelanto()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Dar Adelanto</li><?php  } ?>
     <?php  if(ACCION_71){ ?><li onclick="addHoraExtra()" class="item"><img src="images/bt_add.png" align="absmiddle" />  Asignar Hrs. Extras</li><?php  } ?>
	<!--<li onclick="eliminar()" class="item"><img src="images/bt_delete.png" align="absmiddle" />  Eliminar</li>-->
</ul>
<div id="mygrid_container" style="width:100%;height:280px;"></div>
</body>
</html>
