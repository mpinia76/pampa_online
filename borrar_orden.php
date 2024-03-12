<?php 
session_start();


include_once("config/db.php");
include_once("functions/util.php");
$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Borrado de ordenes','".getRealIP()."')";
mysql_query($sql);
$date = date('Y-m-d');
$sqlAuditoria ="SELECT * FROM usuario_auditoria WHERE usuario_id = ".$_SESSION['userid']." AND fecha='".$date."'";
$rsTempAuditoria = mysql_query($sqlAuditoria);
$totalAuditoria = mysql_num_rows($rsTempAuditoria);

if($totalAuditoria == 1) {
    $rsAuditoria = mysql_fetch_array($rsTempAuditoria);
    $last_interaction = strtotime($rsAuditoria['last']);

    // Calcula los minutos entre la última interacción y el tiempo actual
    $elapsed_time_seconds = time() - $last_interaction;
    //$elapsed_time_minutes = round($elapsed_time_seconds / 60);

    // Actualiza la hora de última interacción y minutos conectados
    $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='Borrado de ordenes', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
    mysql_query($sql_update);

}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Documento sin título</title>
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<script>
function deleteOrden(){
	if($('#tabla').val() == '' || $('#nro_orden').val() == ''){
		alert("Debe completar con un numero de orden y selecionar la operacion");
	}else{
		if(confirm("Esta seguro que desea eliminar la orden "+$('#nro_orden').val()+" de "+$('#tabla').val()+"s ?")){
			$.ajax({
				beforeSend: function(){
					$('#loading').show();
					$('#result').html('');
				},
				type : 'POST',
				data: {'nro_orden' : $('#nro_orden').val(), 't' : $('#tabla').val() },
				url: 'borrar_orden_procesa.php',
				success: function(data){
					$('#loading').hide();
					if(data.logs){
						for(var x = 0; x < data.logs.length; x++){
							$('#result').append(data.logs[x]+'<br />');
						}
					}else{
						$('#result').html(data);
					}
				}
			});
		}
	}
}
</script>
</head>

<body style="background:#FFF; font-family:Arial, Helvetica, sans-serif; font-size:12px;">
<p>Nro. de Orden: <input type="text" name="nro_orden" id="nro_orden" /> 
<select id="tabla">
	<option value="">Tipo ... </option>
    <option value="gasto">Gastos y compras</option>
    <option value="compra">Impuestos,tasas y Cargas sociales</option>
</select>
<input type="button" value="Borrar" onclick="deleteOrden();" /> <img src="images/loading.gif" id="loading" style="display:none;" /> </p>
<div id="result"></div>

</body>
</html>