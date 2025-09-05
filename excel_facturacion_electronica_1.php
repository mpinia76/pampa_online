<?php
include_once("config/db.php");
include_once("functions/date.php");
include_once("functions/fechasql.php");
include_once("functions/util.php");
header("Pragma: public");
header("Expires: 0");

$filename = "facturacion_electronica_".$_GET["ano"]."_".$_GET["mes"].".xls";
header("Content-type: application/x-msdownload");
header("Content-Disposition: attachment; filename=$filename");
header("Pragma: no-cache");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
$meses = array('01'=>'ENERO', '02'=> 'FEBRERO', '03'=> 'MARZO', '04'=> 'ABRIL', '05'=> 'MAYO', '06'=> 'JUNIO', '07'=> 'JULIO', '08'=> 'AGOSTO', '09'=> 'SEPTIEMBRE', '10'=>'OCTUBRE', '11'=> 'NOVIEMBRE', '12'=>'DICIEMBRE');
?>
<table style="border-collapse: collapse;">
<tbody>

<tr>
<th colspan="15" style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">Datos del Cliente</th>
<th colspan="29" style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">Detalles del Comprobante</th>
<th style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">Cantidad De Conceptos</th>
<th colspan="9" style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">Concepto</th>
<th colspan="5" style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">Tributo</th>
</tr>


<tr>


<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">docTipo</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">docNro</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">email</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">emailAlternativo</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">tipoPersona</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">nombre</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">apellido</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">razonSocial</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">condicion</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">direccion</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">ciudad</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">provincia</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">cp</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">telefono</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">celular</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">origen</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">cbteNro</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">cbteFch</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">cae</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">caeFchVto</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">id</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">cbteTipo</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">concepto</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impTotal</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impOpEx</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impTotConc</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impNeto</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impTrib</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">baseImp3</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impIVA3</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">baseImp4</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impIVA4</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">baseImp5</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impIVA5</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">baseImp6</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impIVA6</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">fchServDesde</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">fchServHasta</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">monId</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">monCotiz</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">puntoVenta</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">condicionVta</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">remito</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">observaciones</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">cantConceptos</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">cantidad</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">unidad</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">codigo</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">detalle</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">importe</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">bonificacion</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">alicuota</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impIVA</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">impTotalConcepto</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">idTipoTributo</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">desc</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">baseImp</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">alic</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center; font-weight: bold;border: 0.1pt solid black;vertical-align: middle;">importeTributo</td>

</tr>
<?php 

$ids = substr( $_GET['ids'], 0, strlen($_GET['ids'])-1); //se le quita la �ltima , (coma)
//print_r($_GET['ids']);
$sql = "SELECT R.id, R.check_in, R.check_out, R.total, C.nombre_apellido, C.dni, R.estado, C.nombre_apellido, C.cuit, C.dni, C.sexo, C.tipoDocumento, C.tipoPersona, C.titular_factura, C.razon_social, C.iva
FROM reservas R INNER JOIN clientes C ON R.cliente_id = C.id 

WHERE R.id IN (".$ids.") ORDER BY check_out, C.nombre_apellido ASC";

$rsTemp = mysqli_query($conn,$sql); 
$totalGral=0;
while($rs = mysqli_fetch_array($rsTemp)){
	
	if(($rs['estado']!='2')&&($rs['estado']!='3')){

        $docTipo = ($rs['tipoDocumento']=='DNI')?'96':'94';
        $documento = preg_replace("/[^0-9]/", "", $rs['dni']);

        $tipoPersona = ($rs['tipoPersona']=='Juridica')?'0':'1';
        $razonSocial = ($rs['titular_factura']=='0')?$rs['razon_social']:'0';
        $nombre = $rs['nombre_apellido'];
        switch ($rs['iva']) {

            case 'Responsable Inscripto':
                $condicion='0';
                $docTipo = '80';
                $documento = str_replace('-','',$rs['cuit']);
                $nombre = ($rs['razon_social']&&($rs['tipoPersona']!='Juridica'))?$rs['razon_social']:$rs['nombre_apellido'];
                break;
            case 'Excento':
                $condicion='2';
                break;
            case 'Monotributo':
                $condicion='3';
                break;
            default:
                $condicion='1';

                break;

        }
        $cvTipo = ($rs['iva']=='Responsable Inscripto')?'1':'6';
        /*$sql = "SELECT * FROM reserva_extras WHERE reserva_id = ".$rs['id'];

        $rsTempExtras = mysqli_query($conn,$sql);
        $no_adelantadas=0;
        while($rsExtras = mysqli_fetch_array($rsTempExtras)){
            if($rsExtras['adelantada'] != 1){
                $no_adelantadas = $no_adelantadas + $rsExtras['cantidad'] * $rsExtras['precio'];
            }
        }*/
        $sql = "SELECT reserva_cobros.*, concepto_facturacions.nombre as concepto_facturacion FROM reserva_cobros LEFT JOIN concepto_facturacions ON reserva_cobros.concepto_facturacion_id = concepto_facturacions.id  WHERE fecha LIKE '".$_GET["ano"]."-".$_GET["mes"]."%' AND reserva_id = ".$rs['id']." AND reserva_cobros.tipo <> 'DESCUENTO' ORDER BY reserva_cobros.id";

        $rsTempCobros = mysqli_query($conn,$sql);
        $detalle = '';
        while($rsCobros = mysqli_fetch_array($rsTempCobros)){
            $detalle = $rsCobros['concepto_facturacion'];
        }


		$sql = "SELECT * FROM reserva_cobros WHERE reserva_id = ".$rs['id'];
		
		$rsTempDescuentos = mysqli_query($conn,$sql); 
		$descontado=0;
		$transferencias=0;
		$tarjetas=0;
		$cheques=0;
		while($rsDescuentos = mysqli_fetch_array($rsTempDescuentos)){
			if($rsDescuentos['tipo'] == "DESCUENTO"){
				
	        	$descontado += $rsDescuentos['monto_neto'];
	        }
			if ($_GET['columnaTransfiere']==1) {
				$sql = "SELECT * FROM cobro_transferencias INNER JOIN cuenta ON cobro_transferencias.cuenta_id = cuenta.id  
		        WHERE reserva_cobro_id = ".$rsDescuentos['id']." AND cuenta.controla_facturacion = 1 AND cobro_transferencias.acreditado = 1";
				if ($_GET['metodo']!='check_out') {
		        	$sql .= " AND cobro_transferencias.fecha_acreditado LIKE '".$_GET["ano"]."-".$_GET["mes"]."%'";
		        }
				$rsTempTransferencias = mysqli_query($conn,$sql); 
				$descontado=0;
				while($rsTransferencias = mysqli_fetch_array($rsTempTransferencias)){
					$transferencias +=$rsTransferencias['monto_neto']+$rsTransferencias['intereses']; 
					$quienTransfiere =$rsTransferencias['quien_transfiere'];
				}
			}
			if ($_GET['columnaTC']==1) {
				$sql = "SELECT * FROM cobro_tarjetas ";
	        	if ($_POST['metodo']!='check_out') {
					$sql .= " INNER JOIN reserva_cobros ON reserva_cobros.id = cobro_tarjetas.reserva_cobro_id ";
				}
		        $sql .= " WHERE reserva_cobro_id = ".$rsDescuentos['id'];
	        	if ($_GET['metodo']!='check_out') {
					$sql .= " AND (reserva_cobros.fecha LIKE '".$_GET["ano"]."-".$_GET["mes"]."%')";
				}
			
				$rsTempTarjetas = mysqli_query($conn,$sql); 
				$descontado=0;
				while($rsTarjetas = mysqli_fetch_array($rsTempTarjetas)){
					$tarjetas +=$rsTarjetas['monto_neto']+$rsTarjetas['intereses']; 
					$titular =$rsTarjetas['titular'];
				}
			}
			if ($_GET['columnaCheques']==1) {
				$sql = "SELECT * FROM cobro_cheques LEFT JOIN cuenta ON cobro_cheques.cuenta_acreditado = cuenta.id  
		        WHERE reserva_cobro_id = ".$rsDescuentos['id']." AND ((acreditado = 1 AND cuenta.controla_facturacion = 1) OR (cobro_cheques.cuenta_acreditado=0))";
				if ($_GET['metodo']!='check_out') {
					$sql .= " AND (fecha_acreditado LIKE '".$_GET["ano"]."-".$_GET["mes"]."%' OR asociado_a_pagos_fecha LIKE '".$_GET["ano"]."-".$_GET["mes"]."%')";
				}
				$rsTempCheques = mysqli_query($conn,$sql); 
				$descontado=0;
				while($rsCheques = mysqli_fetch_array($rsTempCheques)){
					$cheques +=$rsCheques['monto_neto']; 
					$libradoPor =$rsCheques['librado_por']; 
				}
			}
		}
		$sql = "SELECT * FROM reserva_facturas WHERE reserva_id = ".$rs['id'];
		
		$rsTempFacturas = mysqli_query($conn,$sql); 
		$facturas=0;
		$estado='';
		while($rsFacturas = mysqli_fetch_array($rsTempFacturas)){
			
			$facturas +=$rsFacturas['monto'];
		}
		$otrasFacturas=0;
		if ($_GET['metodo']!='check_out') {
			$sql = "SELECT * FROM reserva_facturas WHERE reserva_id = ".$rs['id'];
			
			$sql .= " AND fecha_emision NOT LIKE '".$_GET["ano"]."-".$_GET["mes"]."%' ";
			
			$rsTempFacturas = mysqli_query($conn,$sql); 
			
			
			while($rsFacturas = mysqli_fetch_array($rsTempFacturas)){
				
				$otrasFacturas +=$rsFacturas['monto'];
			}
		}
		$total = $transferencias+$tarjetas+$cheques-$facturas+$otrasFacturas;
		//$total =$rs['total']+ $no_adelantadas-$descontado;
		$ivaCoeficiente=1;
		$sql = "SELECT * FROM punto_ventas WHERE id = ".$_GET["puntoVenta"];
		
		$rsTempPuntos = mysqli_query($conn,$sql);
		if($rsPuntos = mysqli_fetch_array($rsTempPuntos)){
				
				$ivaCoeficiente = ($rsPuntos['alicuota'])?1+$rsPuntos['alicuota']:1;
				
			} 
		$neto = $total/$ivaCoeficiente;
		$diferencia = $total-$neto;
		mysqli_query($conn,"DELETE FROM reserva_factura_procesada WHERE reserva_id = ".$rs['id']." AND cliente = '".$rs['nombre_apellido']."' AND dni = '".$rs['dni']." AND total = '".$total."'");
		
		$insert = "INSERT INTO reserva_factura_procesada (reserva_id,fecha,cliente,dni,total,neto,diferencia) VALUES (".$rs['id'].",'".date('Y-m-d H:i:s')."','".$rs['nombre_apellido']."','".trim($rs['dni'])."','".$total."','".$neto."','".$diferencia."')";
		mysqli_query($conn,$insert);
		$total = trim( number_format($total, 2, '.', '') );
		$neto = trim( number_format($neto, 2, '.', '') );
		$diferencia = trim( number_format($diferencia, 2, '.', '') );
		/*$impuesto = $total*(1/100);
		$totalGral += $impuesto;*/
		/*$buscar = 'https://soa.afip.gob.ar/sr-padron/v2/personas/'.trim($rs['dni']);
		$respuesta = file_get_contents($buscar);
	
		$respuesta = json_decode($respuesta);
	
		if(($respuesta->success)&&(!$respuesta->data[1])){
			$cuit= $respuesta->data[0];
			$pre_cuit = substr ( $cuit , 0 , 2);
			$dni= substr ( $cuit , 2 , 8);
			$post_cuit = substr($cuit, -1);
			$cuit = $pre_cuit."-".$dni."-".$post_cuit;
		}
		else{
			$cuit=$rs['dni'];
		}*/
		/*if ($rs['sexo']) {
			$dni = str_pad($rs['dni'], 8, "0", STR_PAD_LEFT);
			$cuit=Format_toCuil( $dni, $rs['sexo']);
		}
		else{
			$cuit=$rs['dni'];
		}*/
		
		
		

?>
<tr>
    <td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $docTipo; ?></td>
    <td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $documento ?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">administracion@villagedelaspampas.com.ar</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"></td>
    <td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $tipoPersona; ?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo utf8_decode($nombre);?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
    <td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo utf8_decode($razonSocial);?></td>
    <td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $condicion; ?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>

    <td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $cvTipo;?></td>
    <td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">2</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $total;?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $neto;?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $neto;?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $diferencia;?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo str_replace('-', '', $rs['check_in']);?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo str_replace('-', '', $rs['check_out']);?></td></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">PES</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">1</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">10</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">1</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">1</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">7</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">1</td>
    <td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo ($detalle)?utf8_decode($detalle):'Alquiler de Departamento';?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $neto;?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">0</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;">5</td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $diferencia;?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"><?php echo $total;?></td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>
<td style="font-family: Arial; font-size: 10pt; text-align:center;border: 0.1pt solid black;vertical-align: middle;"> </td>

</tr>
<?php }}?>

</tbody>
</table>
