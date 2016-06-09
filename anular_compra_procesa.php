<?php
include_once("config/db.php");
include_once("functions/util.php");


	$compra_id = $_GET['dataid'];



$sql = "SELECT * FROM compra WHERE id = ".$compra_id;

if(mysql_num_rows(mysql_query($sql)) != 0){
	$rsCompra = mysql_fetch_array(mysql_query($sql));
	if ($rsCompra['estado']==1) {
		
		
		$ok=1;
		$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCompra['id']." AND operacion_tipo = 'compra' and forma_pago = 'tarjeta'";
		
		$rsTemp = mysql_query($sql);
		while($rs = mysql_fetch_array($rsTemp)){
			$sql = "SELECT id, tarjeta_id FROM tarjeta_consumo WHERE id = ".$rs['forma_pago_id'];
			$rsTempTarjeta = mysql_query($sql);
			if($rsTarjeta = mysql_fetch_array($rsTempTarjeta)){
				$sql = "SELECT fecha FROM tarjeta_consumo_cuota WHERE tarjeta_consumo_id = ".$rsTarjeta['id'];
				$rsTempTarjetaCuota = mysql_query($sql);
				while($rsTarjetaCuota = mysql_fetch_array($rsTempTarjetaCuota)){
					$part=explode("-",$rsTarjetaCuota['fecha']);
					$sql = "SELECT id FROM tarjeta_resumen WHERE estado = 1 AND CONCAT(ano,mes) >= '".$part[0].intval($part[1])."' AND tarjeta_id = ".$rsTarjeta['tarjeta_id'];
					//echo $sql;
					$rsTempTarjetaResumen = mysql_query($sql);
					if(mysql_fetch_array($rsTempTarjetaResumen)){
						$ok=0;
						echo "<br>No es posible borrar este pago. Se encuentra incluido en un resumen de tarjeta de cr&eacute;dito que ya se encuentra pago";
						break;
					}
				}
			}
		}
		if ($ok) {
			
			$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCompra['id']." AND operacion_tipo = 'compra'";
			
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
				$sql = "DELETE FROM rel_pago_operacion WHERE operacion_id = ".$rsCompra['id']." AND operacion_tipo = 'compra'";
				
				mysql_query($sql); 	
				
				$sql = "UPDATE compra SET 
									estado=0
								WHERE id=".$compra_id;
						
			
				mysql_query($sql);
				echo "<br>Actualizando a NO pagada";
				echo "<br>Listo!";
			}
		}
		
		
		
	}else{
		echo "La compra debe estar en estado procesada";
	}
	
	
	
}else{
	echo "No hay compra";
}

?>