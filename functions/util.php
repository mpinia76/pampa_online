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