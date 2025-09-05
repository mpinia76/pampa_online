<?php
session_start();
$user_id = $_SESSION['userid'];
if($user_id == '') { header("Location: index.php"); }
include_once("config/db.php");
include_once("config/user.php");

include_once("functions/util.php");
include_once("functions/fechasql.php");
include_once("library/fpdf17/fpdfhtml.php");
$id = $_GET['id'];
if (!$_GET['copia']) {
	$sql = "SELECT max(nro) as nro FROM recibos";
	if(mysqli_num_rows(mysqli_query($conn,$sql)) != 0){
		$rs = mysqli_fetch_array(mysqli_query($conn,$sql));
		$reciboNro = $rs['nro']+1;
	}
	else{
		$reciboNro = 1;
	}
	$sql = "INSERT INTO recibos (nro) VALUES (".$reciboNro.")";
	mysqli_query($conn,$sql); 
	$tablaUpadate = ($_GET['adelanto'])?'empleado_adelanto':'empleado_pago';
	$sql = "UPDATE $tablaUpadate SET recibo = '".$reciboNro."' WHERE id = $id";
	mysqli_query($conn,$sql); 
}

if ($_GET['adelanto']) {
	$sql = "SELECT empleado_adelanto.recibo, empleado_adelanto.creado as abonado, empleado_adelanto.monto, empleado_adelanto.mes, empleado_adelanto.ano, usuario.espacio_trabajo_id, CONCAT(empleado.apellido,', ',empleado.nombre) as empleado FROM empleado_adelanto LEFT JOIN usuario ON empleado_adelanto.creado_por = usuario.id LEFT JOIN empleado ON empleado_adelanto.empleado_id = empleado.id WHERE empleado_adelanto.id = $id";
}
else{
	$sql = "SELECT empleado_pago.recibo, empleado_pago.abonado, empleado_pago.monto, empleado_pago.mes, empleado_pago.ano, usuario.espacio_trabajo_id, CONCAT(empleado.apellido,', ',empleado.nombre) as empleado FROM empleado_pago LEFT JOIN usuario ON empleado_pago.abonado_por = usuario.id LEFT JOIN empleado ON empleado_pago.empleado_id = empleado.id WHERE empleado_pago.id = $id";
}

$rsSueldo = mysqli_fetch_array(mysqli_query($conn,$sql));
class PDF_Recibo extends fpdfhtmlHelper {
	function Header() {
		global $rsSueldo;
		$this->Image('images/logo.jpg',150);
		$nroRecibo = str_pad($rsSueldo['recibo'], 8, "0", STR_PAD_LEFT);
		$this->SetFont ( 'Arial', '', 12 );
		$this->Cell ( 185, 6, 'N� 0001-'.$nroRecibo.'-RECIBO-', '',0,'L');
		$this->ln(8);
		$texto = ($rsSueldo['espacio_trabajo_id']==1)?'Mar de las Pampas':'Buenos Aires';
		$this->Cell ( 185, 6, $texto.' '.fechavista($rsSueldo['abonado']), '',0,'L');
		$this->ln(8);
		$this->Cell ( 188, 6, '', 'T',0,'L');
	}
	
	function Footer() {
			
		$this->SetY(-60);
		$this->Cell ( 185, 6, 'Firma:', '',0,'L');
		$this->ln(16);
		$this->Cell ( 185, 6, 'Aclaraci�n:', '',0,'L');
		$this->ln(16);
		$this->Cell ( 185, 6, 'D.N.I.:', '',0,'L');
		$this->ln(16);
		$this->Cell ( 185, 6, 'Apreciamos mucho su trabajo, Muchas Gracias.', '',0,'C');
		
	}
}

$oPdf = new PDF_Recibo();
$oPdf->AddPage();
$oPdf->ln(8);
$meses = array('','enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre');
$haberes = ($_GET['adelanto'])?' adelanto de haberes ':' haberes ';
$oPdf->MultiCell( 185, 6, 'Recib� de Village de las Pampas, la suma de '.Format_toMoney($rsSueldo['monto']).' correspondiente a'.$haberes.'del mes de '.$meses[$rsSueldo['mes']]. ' de '.$rsSueldo['ano'], '','L');
$oPdf->Output();
?>