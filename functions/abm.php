<?php
function mysql_insert($tabla,$campos){
$query='INSERT INTO `'.$tabla.'` (';
$count=0;
foreach($campos as $campo => $valor){
	$campo = str_replace("\'","`",$campo);
		if($count==0)
		{
		$query.=$campo;
		}else{
		$query.=','.$campo;
		
		}
$count++;
}
$query.=') VALUES (';
	$count=0;
foreach($campos as $campo => $valor){
if (strcmp ($valor,'NOW()') == 0){
$valor="".$valor."";
}else{
$valor="'".$valor."'";}
		if($count==0)
		{
		$query.=$valor;
		}else{
		$query.=",".$valor;
		
		}
$count++;
}
$query.=')';
//echo $query;
mysql_query($query);
$result=mysql_affected_rows();
if($result=="-1"){
$msg="No se pudieron cargar los datos: ".mysql_error();}
else{
$msg=1;}
return $msg;
}

function mysql_update($tabla,$campos,$id) {
$query='UPDATE `'.$tabla.'` SET ';
$count=0;
foreach($campos as $campo => $valor)
{
	$campo = str_replace("\'","`",$campo);
	if($count==0)
		{
	$query.=''.$campo.'=\''.$valor.'\'';
		}else{
	$query.=','.$campo.'=\''.$valor.'\'';	
		}
$count++;
}
$query.=' WHERE id = \''.$id.'\'';
//echo $query;

mysql_query($query);
$result=mysql_affected_rows();
if($result=="-1"){
$msg="No se pudieron actualizar los datos: ".mysql_error();}
else{
$msg=2;}
return $msg;
}

?>