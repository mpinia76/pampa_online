<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
function _log($msg){
$nombreFile = date('Ymd') . '_log';
         $dt = date('Y-m-d G:i:s');

         $_Log = fopen("logs/" . $nombreFile . ".log", "a+") or die("Operation Failed!");

         fputs($_Log, $dt . " --> " . $msg . "\n");

         fclose($_Log);
}

function _logCheques($msg){
    $nombreFile = 'cheques_'.date('Y-m-d');
    $dt = date('Y-m-d G:i:s');

    $_Log = fopen("logs/" . $nombreFile . ".log", "a+") or die("Operation Failed!");

    fputs($_Log, $dt . " --> " . $msg . "\n");

    fclose($_Log);
}

	function Format_toMoney( $pNum ){

		return( trim( '$'. Format_toDecimal($pNum) ) );

	}

	function Format_toDecimal( $pNum ){
		if ( is_null($pNum) ) {
			return( '0,00' );
		}else{
			return( trim( number_format($pNum, 2, ',', '.') ) );
		}
	}
	
	function getRealIP() {
		 $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        elseif(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        elseif(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        elseif(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        elseif(getenv('HTTP_FORWARDED'))
           $ipaddress = getenv('HTTP_FORWARDED');
        elseif(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
	}

    function auditarUsuarios($accion){
        $sql = "INSERT INTO usuario_log (usuario_id,nombre,accion,ip)
			VALUES ('".$_SESSION['userid']."','".$_SESSION['usernombre']."','".$accion."','".getRealIP()."')";
        mysql_query($sql);
        $date = date('Y-m-d');
        $sqlAuditoria ="SELECT * FROM usuario_auditoria WHERE usuario_id = ".$_SESSION['userid']." AND fecha='".$date."'";
        $rsTempAuditoria = mysql_query($sqlAuditoria);
        $totalAuditoria = mysql_num_rows($rsTempAuditoria);
        //_log($sqlAuditoria);
        if($totalAuditoria == 1) {
            $rsAuditoria = mysql_fetch_array($rsTempAuditoria);
            $last_interaction = strtotime($rsAuditoria['last']);
            //_log(date('Y-m-d H:i:s'));
            // Calcula los segundos entre la última interacción y el tiempo actual
            $elapsed_time_seconds = time() - $last_interaction;
            //$elapsed_time_minutes = round($elapsed_time_seconds / 60);

            // Actualiza la hora de última interacción y segundos conectados
            $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='".$accion."', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
            //_log($sql_update);
            mysql_query($sql_update);

        }
        else{
            $sqlInsertAuditoria = "INSERT INTO usuario_auditoria (usuario_id,fecha,logueo,last,segundos,interaccion,ip)
			VALUES ('".$_SESSION['userid']."','".date('Y-m-d')."','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."',0,'".$accion."','".getRealIP()."')";
            //_log($sqlInsertAuditoria);
            mysql_query($sqlInsertAuditoria);
        }
    }