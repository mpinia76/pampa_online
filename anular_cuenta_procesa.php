<?php
include_once("config/db.php");
include_once("functions/util.php");


	$cuenta_a_pagar_id = $_GET['dataid'];



$sql = "SELECT * FROM cuenta_a_pagar WHERE id = ".$cuenta_a_pagar_id;

if(mysql_num_rows(mysql_query($sql)) != 0){
	$rsCuentaAPagar = mysql_fetch_array(mysql_query($sql));
	if ($rsCuentaAPagar['estado']==1) {
		
		//vemos si esta en cuentas a pagar
		$sql = "UPDATE cuenta_a_pagar SET 
								estado=0,fecha_pago=null
							WHERE id=".$cuenta_a_pagar_id;
					
		//echo $sql;
		mysql_query($sql);
		echo "<br>Actualizando a NO pagada";
		
		$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCuentaAPagar['id']." AND operacion_tipo = '".$rsCuentaAPagar['operacion_tipo']."'";
		
		$rsTemp = mysql_query($sql);
		while($rs = mysql_fetch_array($rsTemp)){
			
			switch($rs['forma_pago']){
				case 'cheque':
				$sql = "DELETE FROM cuenta_movimiento WHERE origen = 'cheque' AND registro_id = ".$rs['forma_pago_id']; 
				mysql_query($sql);
				//_log($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: cheque";
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}
				
				$sql = "DELETE FROM cheque_consumo WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				//_log($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>&nbsp; &nbsp; Eliminando el cheque emitido";
				}
				break;
				
				case 'debito':
				$sql = "DELETE FROM cuenta_movimiento WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: debito de cuenta";
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}
				break;
				
				case 'efectivo':
				
				$sql = "DELETE FROM caja_movimiento WHERE origen = 'efectivo_consumo' AND registro_id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: efectivo";
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de la caja";
				}
				
				$sql = "DELETE FROM efectivo_consumo WHERE id = ".$rs['forma_pago_id']; 
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de efectivo";
				}
	
				break;
				
				case 'tarjeta':
				$sql = "DELETE FROM tarjeta_consumo_cuota WHERE tarjeta_consumo_id = ".$rs['forma_pago_id']; 
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: tarjeta";
					echo "<br>&nbsp; &nbsp; Eliminando las cuotas";
				}
				
				$sql = "DELETE FROM tarjeta_consumo WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de tarjeta";
				}
				break;
				
				case 'transferencia':
				$sql = "DELETE FROM cuenta_movimiento WHERE origen = 'transferencia' AND registro_id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: transferencia";
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}
				
				$sql = "DELETE FROM transferencia_consumo WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>&nbsp; &nbsp; Eliminando el consumo de transferencia";
				}
				break;
			}
			echo "<br>Eliminando datos de tabla relacional";
			$sql = "DELETE FROM rel_pago_operacion WHERE operacion_id = ".$rsCuentaAPagar['id']." AND operacion_tipo = '".$rsCuentaAPagar['operacion_tipo']."'";
			
			mysql_query($sql); 	
		}
		
		
		echo "<br>Listo!";
	}else{
		echo "La cuenta debe estar en estado pagada";
	}
	
	
	
}else{
	echo "No hay registro";
}

?>