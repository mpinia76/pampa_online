<?php

require_once 'gridExcelGenerator.php';
require_once 'gridExcelWrapper.php';
include_once("../../config/db.php");
$debug = false;
$error_handler = set_error_handler("PDFErrorHandler");

if (get_magic_quotes_gpc()) {
	$xmlString = stripslashes($_POST['grid_xml']);
} else {
	$xmlString = $_POST['grid_xml'];
}

//print_r($xmlString);
$xmlString = urldecode($xmlString);

//print_r($xmlString);
if ($debug == true) {
	error_log($xmlString, 3, 'debug_'.date("Y_m_d__H_i_s").'.xml');
}
$tipo = ($_GET['caja'])?'Caja':'Cuenta';
$desde = ($_GET['desde']!='')?" del ".$_GET['desde']:"";
$hasta = ($_GET['hasta']!='')?" al ".$_GET['hasta']:"";
$desde = ($_GET['desde']!='')?"_".str_replace('/', '', $_GET['desde']):"";
$hasta = ($_GET['hasta']!='')?"_".str_replace('/', '', $_GET['hasta']):"";



$excel = new gridExcelGenerator();
if ($_GET['cuenta']) {
	$sql = "SELECT banco.banco,cuenta.* FROM cuenta  INNER JOIN banco ON cuenta.banco_id=banco.id WHERE cuenta.id = ".$_GET['nombre'];
	$rs = mysql_fetch_array(mysql_query($sql));
	$primerCelda = $tipo.": ".$rs['banco'].' ('.$rs['sucursal'].') '.$rs['nombre'].$desde.$hasta;
	$nombre = $tipo."_".$rs['banco'].'_('.$rs['sucursal'].')_'.$rs['nombre'].$desde.$hasta;
}
else{
	$sql = "SELECT caja.* FROM caja WHERE id = ".$_GET['nombre'];
	$rs = mysql_fetch_array(mysql_query($sql));
	$primerCelda = $tipo.": ".$rs['caja'].$desde.$hasta;
	$nombre = $tipo."_".$rs['caja'].$desde.$hasta;
}



$xmlString = str_replace("</rows>", "<row></row><row><cell></cell><cell color='red'><![CDATA[$primerCelda]]></cell></row></rows>", $xmlString);
$xml = simplexml_load_string($xmlString);
//echo $primerCelda;

//echo $nombre;
$excel->printGrid($xml, $nombre);

function PDFErrorHandler ($errno, $errstr, $errfile, $errline) {
	global $xmlString;
	if ($errno < 1024) {
		error_log($xmlString, 3, 'error_report_'.date("Y_m_d__H_i_s").'.xml');
//		exit(1);
	}

}

?>