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
        mysqli_query($conn,$sql);
        $date = date('Y-m-d');
        $sqlAuditoria ="SELECT * FROM usuario_auditoria WHERE usuario_id = ".$_SESSION['userid']." AND fecha='".$date."'";
        $rsTempAuditoria = mysqli_query($conn,$sqlAuditoria);
        $totalAuditoria = mysqli_num_rows($rsTempAuditoria);
        //_log($sqlAuditoria);
        if($totalAuditoria == 1) {
            $rsAuditoria = mysqli_fetch_array($rsTempAuditoria);
            $last_interaction = strtotime($rsAuditoria['last']);
            //_log(date('Y-m-d H:i:s'));
            // Calcula los segundos entre la última interacción y el tiempo actual
            $elapsed_time_seconds = time() - $last_interaction;
            $elapsed_time_seconds = ($elapsed_time_seconds>1440)?1440:$elapsed_time_seconds;
            //$elapsed_time_minutes = round($elapsed_time_seconds / 60);
			$elapsed_time_seconds = ($elapsed_time_seconds>1440)?1440:$elapsed_time_seconds;
            // Actualiza la hora de última interacción y segundos conectados
            $sql_update = "UPDATE usuario_auditoria SET last = now(), interaccion='".$accion."', segundos = segundos + $elapsed_time_seconds WHERE usuario_id = " . $_SESSION['userid'] . " AND fecha = '$date'";
            //_log($sql_update);
            mysqli_query($conn,$sql_update);

        }
        else{
            $sqlInsertAuditoria = "INSERT INTO usuario_auditoria (usuario_id,fecha,logueo,last,segundos,interaccion,ip)
			VALUES ('".$_SESSION['userid']."','".date('Y-m-d')."','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."',0,'".$accion."','".getRealIP()."')";
            //_log($sqlInsertAuditoria);
            mysqli_query($conn,$sqlInsertAuditoria);
        }
    }

/**
 * Limpia texto para enviar a la API de facturación
 * Reemplaza apostrofes, comillas, tildes y caracteres especiales
 */
function limpiarTextoApi($texto) {
    if (!$texto) return '';

    // 1️⃣ Reemplazar apostrofes y comillas
    $texto = str_replace(["'", '"'], ["´", "”"], $texto);

    // 2️⃣ Reemplazar tildes y caracteres acentuados
    $mapTildes = [
        'Á'=>'A', 'É'=>'E', 'Í'=>'I', 'Ó'=>'O', 'Ú'=>'U',
        'á'=>'a', 'é'=>'e', 'í'=>'i', 'ó'=>'o', 'ú'=>'u',
        'Ñ'=>'N', 'ñ'=>'n'
    ];
    $texto = strtr($texto, $mapTildes);

    // 3️⃣ Quitar cualquier otro carácter no alfanumérico básico
    $texto = preg_replace("/[^A-Za-z0-9 \-\.]/", "", $texto);

    return trim($texto);
}
