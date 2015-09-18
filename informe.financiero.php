<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="library/flexigrid/flexigrid.css">
<script type="text/javascript" src="library/flexigrid/flexigrid.js"></script> 
<script>
function roundVal(num){
	return Math.round(num*100)/100;
}
</script>
<title>Documento sin t&iacute;tulo</title>
<style>
a{
text-decoration:underline;
color:#0000FF;
cursor:pointer;
}
</style>
</head>

<body>
<? include_once("config/db.php"); ?>
<? if(isset($_POST['ano'])) { $ano = $_POST['ano']; }else{ $ano= date('Y'); } ?>
<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
<select size="1" name="ano">
	<option <? if($ano == '2010'){?> selected="selected" <? } ?> >2010</option>
	<option <? if($ano == '2011'){?> selected="selected" <? } ?> >2011</option>
	<option <? if($ano == '2012'){?> selected="selected" <? } ?> >2012</option>
	<option <? if($ano == '2013'){?> selected="selected" <? } ?> >2013</option>
	<option <? if($ano == '2014'){?> selected="selected" <? } ?> >2014</option>
	<option <? if($ano == '2015'){?> selected="selected" <? } ?> >2015</option>
    <option <? if($ano == '2016'){?> selected="selected" <? } ?> >2016</option>
    <option <? if($ano == '2017'){?> selected="selected" <? } ?> >2017</option>
    <option <? if($ano == '2018'){?> selected="selected" <? } ?> >2018</option>
</select> 
<input type="submit" value=">" name="buscar" />
</form>

<? include("informe.financiero.tarjeta.php"); ?><br />

<? include("informe.financiero.cheque.php"); ?><br />

<? include("informe.financiero.transferencia.php"); ?><br />

<? include("informe.financiero.efectivo.php"); ?><br />

<?
$sql = "SELECT operacion_tipo,SUM(monto) as total FROM cuenta_a_pagar WHERE estado = 0 GROUP BY operacion_tipo";
$rsTemp = mysql_query($sql);
while ($rs = mysql_fetch_array($rsTemp)){ ?>
	<p>Cuentas a pagar de <strong><?=$rs['operacion_tipo']?></strong>: <?=round($rs['total'],2)?></p>
<? } ?>

<strong>Total financiado</strong>: $<span id="total_total"></span>
<script>
var total = parseFloat(tarjeta) + parseFloat(cheque) + parseFloat(transfer) + parseFloat(efectivo);
$('#total_total').html(total);
</script>

</body>
</html>
