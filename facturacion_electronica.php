<?php 
session_start();
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 0); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>

<link rel="stylesheet" type="text/css" href="library/flexigrid/flexigrid_facturacion.css">
<script type="text/javascript" src="library/flexigrid/flexigrid.js"></script> 
<link rel="stylesheet" type="text/css" href="library/dhtml/styles/dhtmlxwindows.css">
<link rel="stylesheet" type="text/css" href="library/dhtml/styles/dhtmlxwindows_dhx_skyblue.css">
<script src="library/dhtml/js/dhtmlxcommon.js"></script>
<script src="library/dhtml/js/dhtmlxcontainer.js"></script>
<script src="library/dhtml/js/dhtmlxwindows.js"></script>
<script>
function roundVal(num){
	return Math.round(num*100)/100;
}

var xpos = 50;
var ypos = 5; 

var dhxWins,w1;

function doOnLoad() {

    dhxWins = new dhtmlXWindows();
    

}
</script>

<script src="js/createWindow.js"></script>
<title>Documento sin t&iacute;tulo</title>
<style>
a{
text-decoration:underline;
color:#0000FF;
cursor:pointer;
}
</style>
</head>

<body onload="doOnLoad();">

<?php if(isset($_POST['ano'])) { $ano = $_POST['ano']; }else{ $ano= date('Y'); } 
		if(isset($_POST['mes'])) { $mes = $_POST['mes']; }else{ $mes= date('m'); }	
?>

<form method="post" id="formBuscar" action="<?php echo $_SERVER['PHP_SELF']?>" onSubmit="asignarColumnas()">
<select size="1" name="metodo" id="metodo">
	<option value="check_out" <?php if($_POST['metodo'] == 'check_out'){?> selected="selected" <?php } ?> >Por fecha de check out</option>
	<option value="fecha_cobro" <?php if($_POST['metodo'] == 'fecha_cobro'){?> selected="selected" <?php } ?> >Por fecha de cobro</option>
	
</select> 
<select size="1" name="ano" id="ano">
	<option <?php if($ano == '2010'){?> selected="selected" <?php } ?> >2010</option>
	<option <?php if($ano == '2011'){?> selected="selected" <?php } ?> >2011</option>
	<option <?php if($ano == '2012'){?> selected="selected" <?php } ?> >2012</option>
	<option <?php if($ano == '2013'){?> selected="selected" <?php } ?> >2013</option>
	<option <?php if($ano == '2014'){?> selected="selected" <?php } ?> >2014</option>
	<option <?php if($ano == '2015'){?> selected="selected" <?php } ?> >2015</option>
    <option <?php if($ano == '2016'){?> selected="selected" <?php } ?> >2016</option>
    <option <?php if($ano == '2017'){?> selected="selected" <?php } ?> >2017</option>
    <option <?php if($ano == '2018'){?> selected="selected" <?php } ?> >2018</option>
    <option <?php if($ano == '2019'){?> selected="selected" <?php } ?> >2019</option>
    <option <?php if($ano == '2020'){?> selected="selected" <?php } ?> >2020</option>
    <option <?php if($ano == '2021'){?> selected="selected" <?php } ?> >2021</option>
    <option <?php if($ano == '2022'){?> selected="selected" <?php } ?> >2022</option>
    <option <?php if($ano == '2023'){?> selected="selected" <?php } ?> >2023</option>
    <option <?php if($ano == '2024'){?> selected="selected" <?php } ?> >2024</option>
    <option <?php if($ano == '2025'){?> selected="selected" <?php } ?> >2025</option>
    <option <?php if($ano == '2026'){?> selected="selected" <?php } ?> >2026</option>
    <option <?php if($ano == '2027'){?> selected="selected" <?php } ?> >2027</option>
</select> 


<select id="mes" name="mes" >
    <option <?php if($mes == '1'){?> selected="selected" <?php } ?> value="01">Enero</option>
    <option <?php if($mes == '2'){?> selected="selected" <?php } ?> value="02">Febrero</option>
    <option <?php if($mes == '3'){?> selected="selected" <?php } ?> value="03">Marzo</option>
    <option <?php if($mes == '4'){?> selected="selected" <?php } ?> value="04">Abril</option>
    <option <?php if($mes == '5'){?> selected="selected" <?php } ?> value="05">Mayo</option>
    <option <?php if($mes == '6'){?> selected="selected" <?php } ?> value="06">Junio</option>
    <option <?php if($mes == '7'){?> selected="selected" <?php } ?> value="07">Julio</option>
    <option <?php if($mes == '8'){?> selected="selected" <?php } ?> value="08">Agosto</option>
    <option <?php if($mes == '9'){?> selected="selected" <?php } ?> value="09">Septiembre</option>
    <option <?php if($mes == '10'){?> selected="selected" <?php } ?> value="10">Octubre</option>
    <option <?php if($mes == '11'){?> selected="selected" <?php } ?> value="11">Noviembre</option>
    <option <?php if($mes == '12'){?> selected="selected" <?php } ?> value="12">Diciembre</option>
</select> 
Montos 
<select id="signo" name="signo">
	<option value="0">Seleccionar...</option>
    <option <?php if($_POST['signo'] == '1'){?> selected="selected" <?php } ?> value="1">></option>
    <option <?php if($_POST['signo'] == '2'){?> selected="selected" <?php } ?> value="2"><=</option>
    
</select> 
<input type="text"  name="monto" id="monto" value="<?php echo $_POST['monto'];?>" />
<select id="estado" name="estado">
	<option value="0">Seleccionar...</option>
    <option <?php if($_POST['estado'] == '1'){?> selected="selected" <?php } ?> value="1">Pendiente</option>
    <option <?php if($_POST['estado'] == '2'){?> selected="selected" <?php } ?> value="2">Facturado</option>
    <option <?php if($_POST['estado'] == '3'){?> selected="selected" <?php } ?> value="3">Facturacion parcial</option>
    <option <?php if($_POST['estado'] == '4'){?> selected="selected" <?php } ?> value="4">Sobre facturacion</option>
</select> 
<input type="hidden"  name="hTransfiere" id="hTransfiere"/>
<input type="hidden"  name="hTC" id="hTC"/>
<input type="hidden"  name="hCheques" id="hCheques"/>
<input type="submit"  name="ver" id="ver" value="ver" /><span id="cargando" style="display:none;">Cargando ...</span>

</form>
<?php
include_once("config/db.php");
include_once("functions/util.php");
//if ($_POST['metodo']=='check_out') {?>
<select size="1" name="descargas" id="descargas">
	<option value="descarga1" <?php if($_POST['descargas'] == '"descarga1"'){?> selected="selected" <?php } ?> >descarga1</option>
	<option value="descarga2" <?php if($_POST['descargas'] == '"descarga2"'){?> selected="selected" <?php } ?> >descarga2</option>
	
</select> 
<select size="1" name="puntos" id="puntos">

	<?php 
	$sql = "SELECT id, CONCAT(numero,' ', cuit,' ', descripcion,' ', direccion) as punto FROM punto_ventas  ";

	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){
	
	?>
	
	<option value="<?php echo $rs['id']?>" <?php if($_POST['puntos'] == $rs['id']){?> selected="selected" <?php } ?>><?php echo $rs['punto']?> </option>
	
	<?php } ?>
	
</select> 
<input type="button" name="descargar" id="descargar" value="Descargar" onClick="descargar()"/>
<table class="medios_pago">
<tbody>
	
<tr>


<th colspan="2" style="text-align:left; background-color:red; font-size:16px">Ventas: $<span id="montoVentas"></span></th>
<th colspan="2" style="text-align:left; background-color:#15d905; font-size:16px">Facturado: $<span id="montoFacturado"></span></th>
<th colspan="2" style="text-align:left; background-color:red; font-size:16px">A facturar: $<span id="aFacturar"></span></th>
<th></th>
<th colspan="3" style="text-align:left; background-color:#15d905; font-size:16px">Facturado en el mes: $<span id="montoMesFacturado"></span></th>
<th colspan="3" style="text-align:left; background-color:red; font-size:16px">Debito fiscal: $<span id="montoDebitoFiscal"></span></th>


</tr>

<tr>


<th>OUT</th>
<th>Nro de reserva</th>
<th>Titular</th>
<th>DNI/CUIT</th>
    <th>Condición impositiva</th>
    <th>Concepto de facturacion</th>
<th>Monto FC</th>
<th>transfe/deposito <input type="checkbox" <?php echo (($_POST['hTransfiere']==1)||(!isset($_POST['hTransfiere'])))?'checked="checked"':''?> id="columnaTransfiere" name="columnaTransfiere" onClick="buscar();"></input></th>
<th>quien transfiere</th>
<th>TC <input type="checkbox" <?php echo (($_POST['hTC']==1)||(!isset($_POST['hTC'])))?'checked="checked"':''?> id="columnaTC" name="columnaTC" onClick="buscar();"></input></th>
<th>Titular TC</th>
<th>Cheques<input type="checkbox" <?php echo (($_POST['hCheques']==1)||(!isset($_POST['hCheques'])))?'checked="checked"':''?> id="columnaCheques" name="columnaCheques" onClick="buscar();"></input></th>
<th>Librado por</th>
<th>Estado</th>
<th>Detalle</th>


</tr>
<?php 

$sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','Facturacion electronica','".getRealIP()."')";
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
    $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='Facturacion electronica', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
    mysql_query($sql_update);

}
if (isset($_POST['ver'])) {
	
	
	$sql = "SELECT R.numero,R.id, R.check_in, R.check_out, R.total, C.nombre_apellido, C.dni, R.estado, C.cuit, C.titular_factura, C.razon_social, C.iva
FROM reservas R INNER JOIN clientes C ON R.cliente_id = C.id ";
if ($_POST['metodo']=='check_out') {
	$sql .= "WHERE check_out LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' ORDER BY check_out, C.nombre_apellido ASC";
}
else{
	$sql .= "INNER JOIN reserva_cobros RC ON R.id = RC.reserva_id 
	LEFT JOIN cobro_transferencias ON RC.id = cobro_transferencias.reserva_cobro_id 
	LEFT JOIN cobro_cheques ON RC.id = cobro_cheques.reserva_cobro_id ";
	$sql .= "WHERE (RC.fecha LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' OR RC.fecha LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' 
	OR cobro_transferencias.fecha_acreditado LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' 
	OR cobro_cheques.fecha_acreditado LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' 
	OR cobro_cheques.asociado_a_pagos_fecha LIKE '".$_POST["ano"]."-".$_POST["mes"]."%')
	 AND RC.tipo != 'DESCUENTO' 
	GROUP BY R.id ORDER BY check_out, C.nombre_apellido ASC";
}

//echo $sql;
$rsTemp = mysql_query($sql); 
$aFacturar=0;
$totalVentas=0;
$totalFacturado=0;
$totalMesFacturado=0;
while($rs = mysql_fetch_array($rsTemp)){
	
	if(($rs['estado']!='2')&&($rs['estado']!='3')){
		$sql = "SELECT * FROM reserva_extras WHERE reserva_id = ".$rs['id'];
	
		$rsTempExtras = mysql_query($sql); 
		$no_adelantadas=0;
		while($rsExtras = mysql_fetch_array($rsTempExtras)){
			if($rsExtras['adelantada'] != 1){
	        	$no_adelantadas = $no_adelantadas + $rsExtras['cantidad'] * $rsExtras['precio'];
	        }
		}
		$sql = "SELECT * FROM reserva_cobros WHERE reserva_id = ".$rs['id'];
		
		$rsTempDescuentos = mysql_query($sql); 
		$descontado=0;
		$transferencias=0;
		$transferenciasEstado=0;
		$quienTransfiere='';
		$tarjetas=0;
		$tarjetasEstado=0;
		$titular='';
		$cheques=0;
		$chequesEstado=0;
		$libradoPor='';
		while($rsDescuentos = mysql_fetch_array($rsTempDescuentos)){
			if($rsDescuentos['tipo'] == "DESCUENTO"){
				
	        	$descontado += $rsDescuentos['monto_neto'];
	        }
	        if ($_POST['hTransfiere']==1) {
		        $sql = "SELECT * FROM cobro_transferencias INNER JOIN cuenta ON cobro_transferencias.cuenta_id = cuenta.id  
		        WHERE reserva_cobro_id = ".$rsDescuentos['id']." AND cuenta.controla_facturacion = 1 AND cobro_transferencias.acreditado = 1";
		        if ($_POST['metodo']!='check_out') {
		        	$sql .= " AND cobro_transferencias.fecha_acreditado LIKE '".$_POST["ano"]."-".$_POST["mes"]."%'";
		        }
		        //echo $sql;
				$rsTempTransferencias = mysql_query($sql); 
				
				while($rsTransferencias = mysql_fetch_array($rsTempTransferencias)){
					
						$transferencias +=$rsTransferencias['monto_neto']+$rsTransferencias['intereses']; 
						$quienTransfiere =$rsTransferencias['quien_transfiere'];
					
					
					
					
				
				}
				$sql = "SELECT * FROM cobro_transferencias INNER JOIN cuenta ON cobro_transferencias.cuenta_id = cuenta.id  
		        WHERE reserva_cobro_id = ".$rsDescuentos['id']." AND cuenta.controla_facturacion = 1 AND cobro_transferencias.acreditado = 1";
	        	
		        //echo $sql;
				$rsTempTransferencias = mysql_query($sql); 
				
				while($rsTransferencias = mysql_fetch_array($rsTempTransferencias)){
					$transferenciasEstado +=$rsTransferencias['monto_neto']+$rsTransferencias['intereses'];
					
					
					
				
				}
	        }
	        
	        if ($_POST['hTC']==1) {
				$sql = "SELECT * FROM cobro_tarjetas ";
	        	if ($_POST['metodo']!='check_out') {
					$sql .= " INNER JOIN reserva_cobros ON reserva_cobros.id = cobro_tarjetas.reserva_cobro_id ";
				}
		        $sql .= " WHERE reserva_cobro_id = ".$rsDescuentos['id'];
	        	if ($_POST['metodo']!='check_out') {
					$sql .= " AND (reserva_cobros.fecha LIKE '".$_POST["ano"]."-".$_POST["mes"]."%')";
				}
				$rsTempTarjetas = mysql_query($sql); 
				
				while($rsTarjetas = mysql_fetch_array($rsTempTarjetas)){
					
						$tarjetas +=$rsTarjetas['monto_neto']+$rsTarjetas['intereses']; 
						$titular =$rsTarjetas['titular'];
					
					
				}
		        
				$sql = "SELECT * FROM cobro_tarjetas ";
	        	
		        $sql .= " WHERE reserva_cobro_id = ".$rsDescuentos['id'];
	        	
				$rsTempTarjetas = mysql_query($sql); 
				
				while($rsTarjetas = mysql_fetch_array($rsTempTarjetas)){
					$tarjetasEstado +=$rsTarjetas['monto_neto']+$rsTarjetas['intereses'];
				}
	        }
	        if ($_POST['hCheques']==1) {
				$sql = "SELECT * FROM cobro_cheques LEFT JOIN cuenta ON cobro_cheques.cuenta_acreditado = cuenta.id  
		        WHERE reserva_cobro_id = ".$rsDescuentos['id']." AND ((acreditado = 1 AND cuenta.controla_facturacion = 1) OR (cobro_cheques.cuenta_acreditado=0))";
				if ($_POST['metodo']!='check_out') {
					$sql .= " AND (fecha_acreditado LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' OR asociado_a_pagos_fecha LIKE '".$_POST["ano"]."-".$_POST["mes"]."%')";
				}
				//echo $sql."<br>";
				$rsTempCheques = mysql_query($sql); 
				
				while($rsCheques = mysql_fetch_array($rsTempCheques)){
					
						$cheques +=$rsCheques['monto_neto']; 
						$libradoPor =$rsCheques['librado_por']; 
					
					
	        	}
	        	
				$sql = "SELECT * FROM cobro_cheques LEFT JOIN cuenta ON cobro_cheques.cuenta_acreditado = cuenta.id  
		        WHERE reserva_cobro_id = ".$rsDescuentos['id']." AND ((acreditado = 1 AND cuenta.controla_facturacion = 1) OR (cobro_cheques.cuenta_acreditado=0))";
				
				//echo $sql."<br>";
				$rsTempCheques = mysql_query($sql); 
				
				while($rsCheques = mysql_fetch_array($rsTempCheques)){
					if ($_POST['hCheques']==1) {
						$chequesEstado +=$rsCheques['monto_neto'];
					}
	        	}
	        }
		}
		$fc = $transferencias+$tarjetas+$cheques;
		
		$fcEstado = $transferenciasEstado+$tarjetasEstado+$chequesEstado;
		
		$sql = "SELECT * FROM reserva_facturas WHERE reserva_id = ".$rs['id'];
		
		$rsTempFacturas = mysql_query($sql); 
		$facturas=0;
		$estado='';
		while($rsFacturas = mysql_fetch_array($rsTempFacturas)){
			
			$facturas +=$rsFacturas['monto'];
		}
		
		$otrasFacturas=0;
		if ($_POST['metodo']!='check_out') {
			$sql = "SELECT * FROM reserva_facturas WHERE reserva_id = ".$rs['id'];
			
			$sql .= " AND fecha_emision NOT LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' ";
			
			$rsTempFacturas = mysql_query($sql); 
			
			
			while($rsFacturas = mysql_fetch_array($rsTempFacturas)){
				
				$otrasFacturas +=$rsFacturas['monto'];
			}
		}
		
		$disabled=0;
		if ($facturas==0) {
			$estado = 'Pendiente'; 
			$color='fc3156';
		}
		elseif ($facturas==$fcEstado){
			$estado = 'Facturado'; 
			$disabled=1;
			$color='15d905';
		}
		elseif (($facturas-$fcEstado)<0){
			$estado = 'Facturacion Parcial'; 
			$color='fa9008';
		}
		else{
			$estado = 'Sobre Facturacion'; 
			$disabled=1;
			$color='9404cd';
		}
		//echo $estado."-".$_POST['estado']."<br>";
		$mostrar=1;
		
		if (($_POST['signo']==1)) {
			
			$mostrar = ($fc>$_POST['monto'])?1:0;
			
		}
		if (($_POST['signo']==2)) {
			
			$mostrar = ($fc<=$_POST['monto'])?1:0;
			
		}

        $sql = "SELECT reserva_cobros.*, concepto_facturacions.nombre as concepto_facturacion FROM reserva_cobros LEFT JOIN concepto_facturacions ON reserva_cobros.concepto_facturacion_id = concepto_facturacions.id  WHERE fecha LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' AND reserva_id = ".$rs['id']." AND reserva_cobros.tipo <> 'DESCUENTO' ORDER BY reserva_cobros.id";

        $rsTempCobros = mysqli_query($conn,$sql);

        while($rsCobros = mysqli_fetch_array($rsTempCobros)){
            $detalle = $rsCobros['concepto_facturacion'];
        }
		
		if (isset($_POST['estado'])) {
			switch ($_POST['estado']) {
				case 1:
				$mostrar = ($estado=='Pendiente')?1:0;
				break;
				
				case 2:
				$mostrar = ($estado=='Facturado')?1:0;
				break;
				case 3:
				$mostrar = ($estado=='Facturacion Parcial')?1:0;
				break;
				case 4:
				$mostrar = ($estado=='Sobre Facturacion')?1:0;
				break;
			}
		}
		if ($fc==0) {
			$mostrar = 0;
		}
if ($mostrar) {
	if (($estado == 'Pendiente')||($estado == 'Facturacion Parcial')) {
		$aFacturar +=$fc-$facturas+$otrasFacturas;
	}
	$totalVentas +=$fc;
	$totalFacturado +=$facturas;


    $razonSocial = ($rs['razon_social'])?$rs['razon_social']:$rs['nombre_apellido'];
    $iva = ($rs['iva'])?$rs['iva']:'Consumidor final';

	
?>

<tr id="<?php echo $rs['id']; ?>" disable="<?php echo $disabled; ?>" color="<?php echo $color; ?>" monto="<?php echo $fc-$facturas+$otrasFacturas; ?>">
<td><?php echo $rs['check_out']; ?></td>
<td><?php echo $rs['numero']; ?></td>
<td><?php echo $razonSocial;?></td>
<td><?php echo ($rs['cuit']!='')?$rs['cuit']:$rs['dni']; ?></td>
    <td><?php echo $iva; ?></td>
    <td><?php echo $detalle; ?></td>
<td><?php echo trim( number_format($fc, 2, '.', '') );?></td>
<td><?php echo trim( number_format($transferencias, 2, '.', '') );?></td>
<td><?php echo $quienTransfiere;?></td>
<td><?php echo trim( number_format($tarjetas, 2, '.', '') );?></td>
<td><?php echo $titular;?></td>
<td><?php echo trim( number_format($cheques, 2, '.', '') );?></td>
<td><?php echo $libradoPor;?></td>
<td><?php echo $estado;?></td>
<td><a style="cursor:pointer;" onclick="detalle(<?php echo $rs['id'];?>)">Ver</a> </td>


</tr>
<?php }}}
$sql = "SELECT reserva_facturas.monto, punto_ventas.alicuota FROM reserva_facturas LEFT JOIN punto_ventas ON reserva_facturas.punto_venta_id = punto_ventas.id WHERE fecha_emision LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' ";
		$rsTempFacturas = mysql_query($sql); 
		$facturasMes=0;
		$facturasMesIva=0;
		while($rsFacturas = mysql_fetch_array($rsTempFacturas)){
			
			$facturasMes +=$rsFacturas['monto'];
			$facturasMesIva +=($rsFacturas['alicuota'])?$rsFacturas['monto']/(1+$rsFacturas['alicuota']):$rsFacturas['monto'];
		}
}
$totalMesFacturado +=$facturasMes;

echo '<script>
	
	$("#aFacturar").text("'.trim( number_format($aFacturar, 2, '.', '') ).'");
	$("#montoVentas").text("'.trim( number_format($totalVentas, 2, '.', '') ).'");
	$("#montoFacturado").text("'.trim( number_format($totalFacturado, 2, '.', '') ).'");
	$("#montoMesFacturado").text("'.trim( number_format($totalMesFacturado, 2, '.', '') ).'");
	$("#montoDebitoFiscal").text("'.trim( number_format($totalMesFacturado-$facturasMesIva, 2, '.', '') ).'");
	$("#cargando").hide();
</script>'
?>


</tbody>
</table>

</body>
<script>
function buscar(){
	
		$('#ver').click();
	
	
}
function asignarColumnas(){
	

	if($('#columnaTransfiere').is(':checked')){
		$('#hTransfiere').val(1);
	}
	if($('#columnaTC').is(':checked')){
		$('#hTC').val(1);
	}
	if($('#columnaCheques').is(':checked')){
		$('#hCheques').val(1);
	}
	
}
$('.medios_pago').flexigrid({height:'auto',striped:false});

seleccionarTodas()
function detalle(id){
	
		createWindow('w_facturas_ver','Detalle de facturas','v2/reserva_facturas/index/'+id,'600','400'); //nombre de los divs
		$('html, body').animate({scrollTop:0}, 'slow');
}
function seleccionarTodas(){
	
	
	$(' tbody tr', $('.medios_pago')).each( function(){
		var disable = $(this).attr('disable');
		var id = $(this).attr('id');
		//alert(disable);
		var color = $(this).attr('color');
		if(id){
			if(disable=='0'){
				$(this).addClass('trSelected');
				
			}
			else{
				$(this).css("background-color", "#"+color);
				$(this).css("color", "#ffffff");
			}
		}
		
        
    });

	
}

$('.medios_pago').click(function(event){
	var total=0;
    $(' tbody tr', this).each( function(){
        var id = $(this).attr('id');
        
        if(id){
        	var monto = $(this).attr('monto');
    		
        	var disable = $(this).attr('disable');
        	
	        if(disable=='1'){
	        	 //total=total+parseFloat(monto);
				var color = $(this).attr('color');
				$(this).css("color", "#ffffff");
				$(this).css("background-color", "#"+color);
			}
	        
        }
    });

    $('.trSelected', this).each( function(){
        var id = $(this).attr('id');
        if(id){
        	
	        var disable = $(this).attr('disable');
	        
	        if(disable=='1'){
	        	var color = $(this).attr('color');	
		        $(this).css("background-color", "#"+color);
				
				$(this).css("color", "#ffffff");
				$(this).removeClass('trSelected' )
			}
	        else{
	        	var monto = $(this).attr('monto');
	            total=total+parseFloat(monto);
		        }
        }
    });
    $("#aFacturar").text(formatDec(parseFloat(total),2));
});
function formatDec(valor, decimales) {
	var parts = String(valor).split(".");
	parts[1] = String(parts[1]).substring(0, decimales);
	// parts[1] = Number(parts[1]) * Math.pow(10, -(decimales - 1)); //POTENCIA
	// parts[1] = String(Math.floor(parts[1])); //REDODEA HACIA ABAJO
	return parseFloat(parts.join("."));
}
$('#ver').click(function(event){
	$('#cargando').show();
})

function descargar(){
	var ids='';
	/*searchListSelection.forEach( function(valor, indice, array) {
		if(valor!=''){
			ids = ids + valor + ',';
		}
	    
	});*/
	
	$('.trSelected', $('.medios_pago')).each( function(){
		var disable = $(this).attr('disable');
		var id = $(this).attr('id');
		//alert(disable);
		
		if(id){
			if(disable=='0'){
				ids = ids + id + ',';
			}
		}
		
        
    });
	//var serial = searchListSelection.serialize(); // lo pasamos a formato json
	var columnaTransfiere=0;
	if($('#columnaTransfiere').is(':checked')){
		columnaTransfiere=1;
	}
	var columnaTC=0;
	if($('#columnaTC').is(':checked')){
		columnaTC=1;
	}
	var columnaCheques=0;
	if($('#columnaCheques').is(':checked')){
		columnaCheques=1;
	}
	
	if($('#descargas').val()=='descarga1'){
		window.location.href='excel_facturacion_electronica_1.php?ano='+$('#ano').val()+'&mes='+$('#mes').val()+'&metodo='+$('#metodo').val()+'&ids='+ids+'&columnaTransfiere='+columnaTransfiere+'&columnaTC='+columnaTC+'&columnaCheques='+columnaCheques+'&puntoVenta='+$('#puntos').val();
	}
	else{
		window.location.href='excel_facturacion_electronica_2.php?ano='+$('#ano').val()+'&mes='+$('#mes').val()+'&metodo='+$('#metodo').val()+'&ids='+ids+'&columnaTransfiere='+columnaTransfiere+'&columnaTC='+columnaTC+'&columnaCheques='+columnaCheques;
	}
	
	

}
</script>
</html>
