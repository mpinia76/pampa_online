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
        <?php
        $anio_actual = date('Y')+1;
        for ($i = $anio_actual; $i >= 2010; $i--) {
            $selected = ($i == $ano) ? 'selected' : '';
            echo "<option value='$i' $selected>$i</option>";
        }
        ?>
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
    <option <?php if($_POST['estado'] == '5'){?> selected="selected" <?php } ?> value="5">Enviada (sin procesar)</option>
    <option <?php if($_POST['estado'] == '6'){?> selected="selected" <?php } ?> value="6">Error API</option>
</select> 
<input type="hidden"  name="hTransfiere" id="hTransfiere"/>
<input type="hidden"  name="hTC" id="hTC"/>
<input type="hidden"  name="hCheques" id="hCheques"/>
<input type="submit"  name="ver" id="ver" value="ver" /><span id="cargando" style="display:none;">Cargando ...</span>

<br>
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
    $sql = "SELECT id, CONCAT(numero,' ', cuit,' ', descripcion,' ', direccion) as punto, alicuota FROM punto_ventas";
    $rsTemp = mysqli_query($conn,$sql);
    while($rs = mysqli_fetch_array($rsTemp)){
        ?>
        <option value="<?php echo $rs['id']?>" data-alicuota="<?php echo $rs['alicuota']; ?>"
            <?php if($_POST['puntos'] == $rs['id']) echo 'selected="selected"'; ?>>
            <?php echo $rs['punto']?>
        </option>
    <?php } ?>
</select>

<input type="button" name="descargar" id="descargar" value="Descargar" onClick="descargar()"/>
<input type="button" name="facturar" id="facturar" value="Facturar" onClick="abrirFacturacion()"/>
</form>
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
auditarUsuarios('Facturacion electronica');

if (isset($_POST['ver'])) {

    // Obtener todos los conceptos posibles (idealmente al inicio del archivo para no repetir)
    $conceptos = mysqli_query($conn, "SELECT * FROM concepto_facturacions WHERE activo=1 ORDER BY nombre");

    /*$sql = "SELECT R.numero,R.id, R.check_in, R.check_out, R.total, C.nombre_apellido, C.dni, R.estado, C.cuit, C.titular_factura, C.razon_social, C.iva
FROM reservas R INNER JOIN clientes C ON R.cliente_id = C.id ";*/
    $ano = mysqli_real_escape_string($conn, $_POST['ano']);
    $mes = mysqli_real_escape_string($conn, $_POST['mes']);
    $inicio = $ano . '-' . $mes . '-01';
    $fin = $ano . '-' . $mes . '-31';

    $sql = "
SELECT 
    R.id,
    MAX(R.numero) AS numero,
    MAX(R.check_in) AS check_in,
    MAX(R.check_out) AS check_out,
    MAX(R.total) AS total,
    MAX(C.nombre_apellido) AS nombre_apellido,
    MAX(C.dni) AS dni,
    MAX(R.estado) AS estado,
    MAX(C.cuit) AS cuit,
    MAX(C.titular_factura) AS titular_factura,
    MAX(C.razon_social) AS razon_social,
    MAX(C.iva) AS iva,
    MAX(RFP.id) AS procesada_id,
    MAX(RFP.procesada_api) AS procesada_api,
    MAX(RFP.error_api) AS error_api,
    MAX(RFP.error_mensaje) AS error_mensaje
FROM reservas R
INNER JOIN clientes C ON R.cliente_id = C.id
LEFT JOIN (
    SELECT rfp1.*
    FROM reserva_factura_procesada rfp1
    INNER JOIN (
        SELECT reserva_id, MAX(id) AS max_id
        FROM reserva_factura_procesada
        GROUP BY reserva_id
    ) rfp2 ON rfp1.id = rfp2.max_id
) AS RFP ON RFP.reserva_id = R.id 
         AND RFP.cliente = C.nombre_apellido 
         AND RFP.dni = C.dni
";

    if ($_POST['metodo'] == 'check_out') {
        $sql .= " WHERE R.check_out LIKE '$ano-$mes%' 
              GROUP BY R.id 
              ORDER BY R.check_out, C.nombre_apellido ASC";
    } else {
        $sql .= "
    INNER JOIN reserva_cobros RC ON R.id = RC.reserva_id 
    LEFT JOIN cobro_transferencias CT ON RC.id = CT.reserva_cobro_id 
    LEFT JOIN cobro_cheques CC ON RC.id = CC.reserva_cobro_id
    WHERE (RC.fecha LIKE '$ano-$mes%' 
           OR CT.fecha_acreditado LIKE '$ano-$mes%' 
           OR CC.fecha_acreditado LIKE '$ano-$mes%' 
           OR CC.asociado_a_pagos_fecha LIKE '$ano-$mes%')
      AND RC.tipo != 'DESCUENTO'
    GROUP BY R.id
    ORDER BY R.check_out, C.nombre_apellido ASC
    ";
    }


//echo $sql;
$rsTemp = mysqli_query($conn,$sql); 
$aFacturar=0;
$totalVentas=0;
$totalFacturado=0;
$totalMesFacturado=0;
while($rs = mysqli_fetch_array($rsTemp)){
	
	if(($rs['estado']!='2')&&($rs['estado']!='3')){
		$sql = "SELECT * FROM reserva_extras WHERE reserva_id = ".$rs['id'];
	
		$rsTempExtras = mysqli_query($conn,$sql); 
		$no_adelantadas=0;
		while($rsExtras = mysqli_fetch_array($rsTempExtras)){
			if($rsExtras['adelantada'] != 1){
	        	$no_adelantadas = $no_adelantadas + $rsExtras['cantidad'] * $rsExtras['precio'];
	        }
		}
		$sql = "SELECT * FROM reserva_cobros WHERE reserva_id = ".$rs['id'];
		
		$rsTempDescuentos = mysqli_query($conn,$sql); 
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
		while($rsDescuentos = mysqli_fetch_array($rsTempDescuentos)){
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
				$rsTempTransferencias = mysqli_query($conn,$sql); 
				
				while($rsTransferencias = mysqli_fetch_array($rsTempTransferencias)){
					
						$transferencias +=$rsTransferencias['monto_neto']+$rsTransferencias['intereses']; 
						$quienTransfiere =$rsTransferencias['quien_transfiere'];
					
					
					
					
				
				}
				$sql = "SELECT * FROM cobro_transferencias INNER JOIN cuenta ON cobro_transferencias.cuenta_id = cuenta.id  
		        WHERE reserva_cobro_id = ".$rsDescuentos['id']." AND cuenta.controla_facturacion = 1 AND cobro_transferencias.acreditado = 1";
	        	
		        //echo $sql;
				$rsTempTransferencias = mysqli_query($conn,$sql); 
				
				while($rsTransferencias = mysqli_fetch_array($rsTempTransferencias)){
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
				$rsTempTarjetas = mysqli_query($conn,$sql); 
				
				while($rsTarjetas = mysqli_fetch_array($rsTempTarjetas)){
					
						$tarjetas +=$rsTarjetas['monto_neto']+$rsTarjetas['intereses']; 
						$titular =$rsTarjetas['titular'];
					
					
				}
		        
				$sql = "SELECT * FROM cobro_tarjetas ";
	        	
		        $sql .= " WHERE reserva_cobro_id = ".$rsDescuentos['id'];
	        	
				$rsTempTarjetas = mysqli_query($conn,$sql); 
				
				while($rsTarjetas = mysqli_fetch_array($rsTempTarjetas)){
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
				$rsTempCheques = mysqli_query($conn,$sql); 
				
				while($rsCheques = mysqli_fetch_array($rsTempCheques)){
					
						$cheques +=$rsCheques['monto_neto']; 
						$libradoPor =$rsCheques['librado_por']; 
					
					
	        	}
	        	
				$sql = "SELECT * FROM cobro_cheques LEFT JOIN cuenta ON cobro_cheques.cuenta_acreditado = cuenta.id  
		        WHERE reserva_cobro_id = ".$rsDescuentos['id']." AND ((acreditado = 1 AND cuenta.controla_facturacion = 1) OR (cobro_cheques.cuenta_acreditado=0))";
				
				//echo $sql."<br>";
				$rsTempCheques = mysqli_query($conn,$sql); 
				
				while($rsCheques = mysqli_fetch_array($rsTempCheques)){
					if ($_POST['hCheques']==1) {
						$chequesEstado +=$rsCheques['monto_neto'];
					}
	        	}
	        }
		}
		$fc = $transferencias+$tarjetas+$cheques;
		
		$fcEstado = $transferenciasEstado+$tarjetasEstado+$chequesEstado;
		
		$sql = "SELECT * FROM reserva_facturas WHERE reserva_id = ".$rs['id'];
		
		$rsTempFacturas = mysqli_query($conn,$sql); 
		$facturas=0;
		$estado='';
		while($rsFacturas = mysqli_fetch_array($rsTempFacturas)){
			
			$facturas +=$rsFacturas['monto'];
		}
		
		$otrasFacturas=0;
		if ($_POST['metodo']!='check_out') {
			$sql = "SELECT * FROM reserva_facturas WHERE reserva_id = ".$rs['id'];
			
			$sql .= " AND fecha_emision NOT LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' ";
			
			$rsTempFacturas = mysqli_query($conn,$sql); 
			
			
			while($rsFacturas = mysqli_fetch_array($rsTempFacturas)){
				
				$otrasFacturas +=$rsFacturas['monto'];
			}
		}
		
		$disabled=0;
        $color = '';   // por defecto
        $detalleError ='';
        if ($rs['error_api'] == 1) {
            $estado = 'Error API';
            $color = 'ff0000';  // rojo
            $disabled = 0;      // siempre habilitada
            $detalleError = $rs['error_mensaje'];
        } else {
            if ($rs['procesada_id'] && $rs['procesada_api'] == 0) {
                $estado = 'Enviada (sin procesar)';
                $color = '6fa8dc'; // azul claro
                $disabled = 1;     // evita que se pueda seleccionar
            } else {
                if ($facturas == 0) {
                    $estado = 'Pendiente';
                    $color = 'fc3156';
                } elseif ($facturas == $fcEstado) {
                    $estado = 'Facturado';
                    $disabled = 1;
                    $color = '15d905';
                } elseif (($facturas - $fcEstado) < 0) {
                    $estado = 'Facturacion Parcial';
                    $color = 'fa9008';
                } else {
                    $estado = 'Sobre Facturacion';
                    $disabled = 1;
                    $color = '9404cd';
                }
            }
        }
		//echo $estado."-".$_POST['estado']."<br>";
		$mostrar=1;
		
		if (($_POST['signo']==1)) {
			
			$mostrar = ($fc>$_POST['monto'])?1:0;
			
		}
		if (($_POST['signo']==2)) {
			
			$mostrar = ($fc<=$_POST['monto'])?1:0;
			
		}
        $detalle = '';
        $idCobro = 0;

        $sql = "SELECT reserva_cobros.*, concepto_facturacions.nombre as concepto_facturacion FROM reserva_cobros LEFT JOIN concepto_facturacions ON reserva_cobros.concepto_facturacion_id = concepto_facturacions.id  WHERE reserva_id = ".$rs['id']." AND reserva_cobros.tipo <> 'DESCUENTO' ORDER BY reserva_cobros.id";
        if ($_POST['metodo']!='check_out') {
            $sql .= " AND fecha LIKE '".$_POST["ano"]."-".$_POST["mes"]."%'";
        }

        $rsTempCobros = mysqli_query($conn,$sql);

        while($rsCobros = mysqli_fetch_array($rsTempCobros)){
            $detalle = $rsCobros['concepto_facturacion'];
            $idCobro = $rsCobros['id'];
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
                case 5:
                    $mostrar = ($estado == 'Enviada (sin procesar)') ? 1 : 0;
                    break;
                case 6:
                    $mostrar = ($estado == 'Error API') ? 1 : 0;
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
    <?php

    echo '<td>';
    if (($estado=='Pendiente')||($estado=='Error API')||($estado=='Facturacion Parcial')) {
        echo '<select class="select-concepto" data-id="' . $idCobro . '" style="width:100px;">';
        echo '<option value=""></option>';
        mysqli_data_seek($conceptos, 0); // reiniciar puntero
        while ($c = mysqli_fetch_assoc($conceptos)) {
            // Solo mostrar conceptos del punto de venta seleccionado
            // Supongamos que en tu tabla concepto_facturacions hay un campo punto_venta_id
            if ($c['punto_venta_id'] == $_POST['puntos']) {
                $selected = ($c['nombre'] == $detalle) ? 'selected' : '';
                echo '<option value="' . $c['id'] . '" ' . $selected . '>' . $c['nombre'] . '</option>';
            }
        }
        echo '</select>';
    } else {
        echo $detalle;
    }
    echo '</td>'; ?>
<td><?php echo trim( number_format($fc, 2, '.', '') );?></td>
<td><?php echo trim( number_format($transferencias, 2, '.', '') );?></td>
<td><?php echo $quienTransfiere;?></td>
<td><?php echo trim( number_format($tarjetas, 2, '.', '') );?></td>
<td><?php echo $titular;?></td>
<td><?php echo trim( number_format($cheques, 2, '.', '') );?></td>
<td><?php echo $libradoPor;?></td>
<td><?php echo ($detalleError) ? $detalleError : $estado; ?></td>
<td><a style="cursor:pointer;" onclick="detalle(<?php echo $rs['id'];?>)">Ver</a> </td>


</tr>
<?php }}}
$sql = "SELECT reserva_facturas.monto, punto_ventas.alicuota FROM reserva_facturas LEFT JOIN punto_ventas ON reserva_facturas.punto_venta_id = punto_ventas.id WHERE fecha_emision LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' ";
		$rsTempFacturas = mysqli_query($conn,$sql); 
		$facturasMes=0;
		$facturasMesIva=0;
		while($rsFacturas = mysqli_fetch_array($rsTempFacturas)){
			
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
	
</script>'
?>


</tbody>
</table>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Esto se ejecuta cuando el navegador empieza a renderizar el HTML,
        // pero todavía NO terminó de cargar todo (imágenes, estilos, etc.)
        document.getElementById('cargando').style.display = 'block';
    });

    window.addEventListener("load", function() {
        // Esto se ejecuta SOLO cuando todo el contenido terminó de cargarse
        // (incluyendo la ejecución de tu PHP pesado)
        document.getElementById('cargando').style.display = 'none';
    });
</script>

</body>
<script>
function buscar(){
	
		$('#ver').click();
	
	
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

function abrirFacturacion() {


    var seleccionadas = [];
    $('.trSelected', $('.medios_pago')).each(function() {
        var id = $(this).attr('id');
        var disable = $(this).attr('disable');
        if (id && disable == '0') {
            seleccionadas.push(id);
        }
    });

    if (seleccionadas.length === 0) {
        alert('Debe seleccionar al menos una reserva para facturar.');
        return;
    }

    // âœ… Inicialización segura del manejador de ventanas
    if (typeof dhxWins === 'undefined' || !dhxWins) {
        dhxWins = new dhtmlXWindows();
    }

    // âœ… Cierra ventana anterior solo si existe y tiene mÃ©todo close
    if (typeof w1 !== 'undefined' && w1 && typeof w1.close === 'function') {
        try {
            w1.close();
        } catch (e) {
            console.warn('No se pudo cerrar ventana anterior:', e);
        }
    }

    var puntoVentaSelect = $('#puntos').val();

    var resultadoPV = validarPuntoVenta(seleccionadas, puntoVentaSelect);

    if (resultadoPV.errores.length > 0) {
        let mensaje = "Se excluirán estas reservas por incluir facturas con otro punto de venta:\n";
        resultadoPV.errores.forEach(function(r) {
            mensaje += "Reserva " + r.numero_reserva + " - Nro: " + r.nro + " - Punto de venta: " + r.punto_venta + "\n";
        });
        alert(mensaje);
    }

// Solo facturamos las válidas
    seleccionadas = resultadoPV.validas;

    if (seleccionadas.length === 0) {
        alert('No hay reservas válidas para facturar.');
        return;
    }



    // âœ… Crea nueva ventana
    w1 = dhxWins.createWindow("w_facturar", 200, 100, 400, 400);
    w1.setText("Facturación");
    w1.setModal(true);
    w1.button("park").hide();
    w1.centerOnScreen();



    // Total bruto y cantidad solo de reservas válidas
    var totalBruto = 0, cantidad = 0;
    $('.trSelected', $('.medios_pago')).each(function() {
        var id = $(this).attr('id');
        var disable = $(this).attr('disable');
        if (id && disable == '0' && seleccionadas.includes(id)) {
            totalBruto += parseFloat($(this).attr('monto'));
            cantidad++;
        }
    });


    // Clonar opciones del select principal incluyendo data-alicuota
    var opcionesSelect = $('#puntos option').map(function() {
        var alicuota = this.dataset.alicuota; // acceso directo al atributo data-alicuota
        return `<option value="${this.value}" data-alicuota="${alicuota}" ${this.value == puntoVentaSelect ? 'selected' : ''}>${this.text}</option>`;
    }).get().join('');


    // IVA y Neto inicial según el punto seleccionado
    var alicuota = parseFloat($('#puntos option:selected').attr('data-alicuota')) || 0;
    var montoNeto = totalBruto / (1 + alicuota);
    var iva = totalBruto - montoNeto;



    var htmlInfo = `
        <div style="padding:15px;font-family:Arial, sans-serif;line-height:1.8;">


            <label><b>Fecha facturas:</b></label><br>
            <input type="date" id="fechaFactura" style="width:95%;padding:4px;"><br>

            <b>Monto total Neto:</b> $<span id="modalNeto">${montoNeto.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span><br>
            <b>IVA:</b> $<span id="modalIva">${iva.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span><br>
            <b>Monto total Bruto:</b> $${totalBruto.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}<br>
            <b>Cantidad de facturas:</b> ${cantidad}<br><br>

            <div style="text-align:right;">
                <input type="hidden" id="idsSeleccionados" value="${seleccionadas.join(',')}">
                <button type="button" id="btnConfirmarFacturacion" onclick="confirmarFacturacion()">Confirmar</button>
                <button type="button" onclick="cerrarVentanaFacturacion()">Cerrar</button>
            </div>

        </div>
    `;

    w1.attachHTMLString(htmlInfo);



}

// Función global para cerrar ventana
function cerrarVentanaFacturacion() {
    if (typeof w1 !== 'undefined' && w1 && typeof w1.close === 'function') {
        w1.close();
    }
}

function confirmarFacturacion() {
    var fecha = $('#fechaFactura').val();
    if (!fecha) {
        alert('Debe seleccionar una fecha de factura.');
        return;
    }


    // Validación de fecha para el punto de venta
    $.ajax({
        url: 'v2/reserva_facturas/validarFechaFactura',
        type: 'POST',
        dataType: 'json',
        data: {
            punto_venta_id: 1,
            fecha: fecha
        },
        success: function(resp) {
            if (resp.error === 1) {
                alert(resp.mensaje);
                return; // bloquea la facturación si la fecha es inválida
            }

            // Aquí seguís con el envío normal a la API
            enviarFacturacion();
        },
        error: function(xhr, status, err) {
            console.error(xhr, status, err);
            alert('Error validando la fecha de la factura.');
        }
    });
}

function enviarFacturacion() {
    var fecha = $('#fechaFactura').val();

    var ids = $('#idsSeleccionados').val();
    var puntoVenta = 1;

    var ano = $('#ano').val();
    var mes = $('#mes').val();

    var columnaTransfiere = $('#columnaTransfiere').is(':checked') ? 1 : 0;
    var columnaTC = $('#columnaTC').is(':checked') ? 1 : 0;
    var columnaCheques = $('#columnaCheques').is(':checked') ? 1 : 0;

    if (!fecha || !puntoVenta) {
        alert('Debe completar la fecha y/ punto de venta.');
        return;
    }

    const fechaStr = $('#fechaFactura').val();
    const partes = fechaStr.split('-');
    const fechaFactura = new Date(partes[0], partes[1] - 1, partes[2]);

    const hoy = new Date();
    const diffDias = Math.floor((hoy - fechaFactura) / (1000 * 60 * 60 * 24));

    if (fechaFactura > hoy) {
        alert('La fecha de factura no puede ser futura.');
        return;
    }

    if (diffDias > 10) {
        alert('AFIP no permite emitir facturas de servicios con más de 10 días de antigÃ¼edad.');
        return;
    }

    // Loading seguro
    if ($('#loadingFacturacion').length === 0) {
        $('#fechaFactura').after('<div id="loadingFacturacion" style="display:none;margin-top:10px;">Procesando, por favor espere...</div>');
    }

    // Botón seguro dentro de la ventana
    var btnConfirmar = $(w1._content).find('#btnConfirmarFacturacion');
    if (btnConfirmar.length) btnConfirmar.prop('disabled', true);
    $('#loadingFacturacion').show();

    $.ajax({
        url: 'facturar_reservas_api.php',
        type: 'POST',
        dataType: 'json',
        data: {
            fecha: fecha,
            ano: ano,
            mes: mes,
            ids: ids,
            puntoVenta: puntoVenta,
            columnaTransfiere: columnaTransfiere,
            columnaTC: columnaTC,
            columnaCheques: columnaCheques
        },
        success: function(resp) {
            let mensaje = "";

            resp.results.forEach(function(r) {
                if (r.error === "N") {
                    mensaje += "✔ Reserva ID " + r.id + ": Factura emitida correctamente.\n";
                } else {
                    let detalle = "Error desconocido";

                    if (Array.isArray(r.errores) && r.errores.length > 0) {
                        detalle = r.errores.join(" | ");
                    } else if (typeof r.error_details === "string" && r.error_details.trim() !== "") {
                        detalle = r.error_details;
                    } else if (typeof r.rta === "string" && r.rta.trim() !== "") {
                        detalle = r.rta;
                    }

                    mensaje += "❌ Reserva ID " + r.id + ": " + detalle + "\n";
                }
            });

            alert(mensaje);
            w1.close();
            $('#ver').click();
        },
        error: function(xhr, status, err) {
            console.error(xhr, status, err);
            alert("Error inesperado al facturar.");
        },
        complete: function() {
            $('#loadingFacturacion').hide();
            if (btnConfirmar.length) btnConfirmar.prop('disabled', false);
        }
    });
}

$('.select-concepto').live('change', function() {
    var id = $(this).attr('data-id'); // <--- usar attr
    var conceptoId = $(this).val(); // directamente del select que cambió

    $.ajax({
        url : 'v2/reserva_cobros/guardarConcepto',
        type : 'POST',
        dataType : 'json',
        data : {
            cobro_id: id,
            concepto_facturacion_id: conceptoId
        },
        success : function(res) {

        },
        error: function(xhr, status, err){
            console.error(xhr.responseText);
            alert('Error de conexión con el servidor');
        }
    });
});

function validarPuntoVenta(seleccionadas, puntoVenta) {
    let errorReservas = [];
    let validas = [];

    seleccionadas.forEach(function(id) {
        $.ajax({
            url: 'v2/reserva_facturas/validarPuntoVenta',
            type: 'POST',
            dataType: 'json',
            async: false,
            data: { reserva_id: id, punto_venta_id: puntoVenta },
            success: function(resp) {
                if (resp.error === 1) {
                    errorReservas.push({
                        numero_reserva: resp.numero_reserva,
                        nro: resp.numero,
                        punto_venta: resp.punto_venta
                    });
                } else {
                    validas.push(id);
                }
            },
            error: function(xhr, status, err) {
                console.error('Error validando reserva ' + id, xhr, status, err);
            }
        });
    });

    return { errores: errorReservas, validas: validas };
}
$('#puntos').change(function() {
    $('#ver').click();
});
</script>
</html>
