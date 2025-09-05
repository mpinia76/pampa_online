<?php
include_once("config/db.php");
include_once("functions/util.php");

$empleado_id = $_GET['empleado_id'];
$ano = $_GET['ano'];
$mes = $_GET['mes'];

$sql = "SELECT * FROM empleado_pago WHERE empleado_id = $empleado_id AND mes = $mes AND ano = $ano";

if(mysqli_num_rows(mysqli_query($conn,$sql)) != 0){
	$rsSueldo = mysqli_fetch_array(mysqli_query($conn,$sql));
	$ok=1;
	$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsSueldo['id']." AND operacion_tipo = 'sueldo_pago' and forma_pago = 'tarjeta'";
	
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){
		$sql = "SELECT id, tarjeta_id FROM tarjeta_consumo WHERE id = ".$rs['forma_pago_id'];
		//echo "<br>".$sql;
		$rsTempTarjeta = mysqli_query($conn,$sql);
		if($rsTarjeta = mysqli_fetch_array($rsTempTarjeta)){
			$sql = "SELECT fecha FROM tarjeta_consumo_cuota WHERE tarjeta_consumo_id = ".$rsTarjeta['id'];
			//echo "<br>".$sql;
			$rsTempTarjetaCuota = mysqli_query($conn,$sql);
			while($rsTarjetaCuota = mysqli_fetch_array($rsTempTarjetaCuota)){
				$part=explode("-",$rsTarjetaCuota['fecha']);
				$sql = "SELECT id FROM tarjeta_resumen WHERE estado = 1 AND CONCAT(ano,mes) = '".$part[0].intval($part[1])."' AND tarjeta_id = ".$rsTarjeta['tarjeta_id'];
				$rsTempTarjetaResumen = mysqli_query($conn,$sql);
				if(mysqli_fetch_array($rsTempTarjetaResumen)){
					$ok=0;
					echo "<br>No es posible borrar este pago. Se encuentra incluido en un resumen de tarjeta de cr&eacute;dito que ya se encuentra ABONADO";
					break;
				}
			}
		}
	}
	include_once("config/user.php");
	if(!ACCION_134){
		$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsSueldo['id']." AND operacion_tipo = 'sueldo_pago' and forma_pago = 'efectivo'";
		
		$rsTemp = mysqli_query($conn,$sql);
		while($rs = mysqli_fetch_array($rsTemp)){
			$sql = "SELECT id, caja_id, fecha FROM caja_movimiento WHERE origen = 'efectivo_consumo' AND registro_id = ".$rs['forma_pago_id'];
			$rsTempCajaMovimiento = mysqli_query($conn,$sql);
			if($rsCajaMovimiento = mysqli_fetch_array($rsTempCajaMovimiento)){
				$sql = "SELECT MAX(fecha) AS fecha  
				FROM caja_sincronizada
				WHERE caja_id = ".$rsCajaMovimiento['caja_id'];
				
				$rsCajaSincronizada = mysqli_fetch_array(mysqli_query($conn,$sql));
				$fecha_sincronizacion = $rsCajaSincronizada['fecha'];
				if ($fecha_sincronizacion>=$rsCajaMovimiento['fecha']) {
					$ok=0;
					echo "<br>No es posible borrar este pago. La caja se encuentra conciliada y sincronizada para la fecha del movimiento que intenta realizar. Contactar administrador";
						break;
				}
				
			}
		}
	}
	if ($ok) {
		$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsSueldo['id']." AND operacion_tipo = 'sueldo_pago'";
		
		$rsTemp = mysqli_query($conn,$sql);
		while($rs = mysqli_fetch_array($rsTemp)){
			
			switch($rs['forma_pago']){
				case 'cheque':
				$sql = "DELETE FROM cuenta_movimiento WHERE origen = 'cheque' AND registro_id = ".$rs['forma_pago_id']; 
				mysqli_query($conn,$sql);
				//_log($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: cheque";
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}
				
				$sql = "DELETE FROM cheque_consumo WHERE id = ".$rs['forma_pago_id'];
				mysqli_query($conn,$sql);
				//_log($sql);
				if(mysql_affected_rows() > 0){
					echo "<br>&nbsp; &nbsp; Eliminando el cheque emitido";
				}
				break;
				
				case 'debito':
				$sql = "DELETE FROM cuenta_movimiento WHERE id = ".$rs['forma_pago_id'];
				mysqli_query($conn,$sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: debito de cuenta";
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}
				break;
				
				case 'efectivo':
				
				$sql = "DELETE FROM caja_movimiento WHERE origen = 'efectivo_consumo' AND registro_id = ".$rs['forma_pago_id'];
				mysqli_query($conn,$sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: efectivo";
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de la caja";
				}
				
				$sql = "DELETE FROM efectivo_consumo WHERE id = ".$rs['forma_pago_id']; 
				mysqli_query($conn,$sql);
				if(mysql_affected_rows() > 0){
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de efectivo";
				}
	
				break;
				
				case 'tarjeta':
		
				$sql = "DELETE FROM tarjeta_consumo_cuota WHERE tarjeta_consumo_id = ".$rs['forma_pago_id']; 
				mysqli_query($conn,$sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: tarjeta";
					echo "<br>&nbsp; &nbsp; Eliminando las cuotas";
				}
				
				$sql = "DELETE FROM tarjeta_consumo WHERE id = ".$rs['forma_pago_id'];
				mysqli_query($conn,$sql);
				if(mysql_affected_rows() > 0){
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de tarjeta";
				}
					
				break;
				
				case 'transferencia':
				$sql = "DELETE FROM cuenta_movimiento WHERE origen = 'transferencia' AND registro_id = ".$rs['forma_pago_id'];
				mysqli_query($conn,$sql);
				if(mysql_affected_rows() > 0){
					echo "<br>Forma de pago: transferencia";
					echo "<br>&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}
				
				$sql = "DELETE FROM transferencia_consumo WHERE id = ".$rs['forma_pago_id'];
				mysqli_query($conn,$sql);
				if(mysql_affected_rows() > 0){
					echo "<br>&nbsp; &nbsp; Eliminando el consumo de transferencia";
				}
				break;
			}
		
			echo "<br>Eliminando datos de tabla relacional";
			$sql = "DELETE FROM rel_pago_operacion WHERE operacion_id = ".$rsSueldo['id']." AND operacion_tipo = 'sueldo_pago'";
			
			mysqli_query($conn,$sql); 	
			//vemos si esta en cuentas a pagar
			$sql = "DELETE FROM empleado_pago WHERE empleado_id = $empleado_id AND mes = $mes AND ano = $ano";
			
			mysqli_query($conn,$sql);
			echo "<br>Borrando registro en tabla empleado_pago";
			echo "<br>Listo!";
		}
	}
	
	
	
	
	
	
}else{
	echo "No hay pagos al empleado en dicho periodo";
}
?>