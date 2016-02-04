<?php
function _log($msg){
$nombreFile = date('Ymd') . '_log';
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