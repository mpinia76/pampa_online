<?php
function fechasql($fecha){
	$part=explode("/",$fecha);
	$mysql=$part[2]."-".$part[1]."-".$part[0];
	return $mysql;
}
function fechavista($fecha){
	$part=explode("-",$fecha);
	$mysql=$part[2]."/".$part[1]."/".$part[0];
	return $mysql;
}
function is_date( $fecha ) 
{ 
	$part=explode("/",$fecha);
	if(count($part == 3) and @checkdate($part[1], $part[0], $part[2]) and $fecha != ''){
		return TRUE;
	}else{
		return FALSE;
	}
} 
?>