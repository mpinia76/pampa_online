<?php
include_once("config/db.php");

$nro_orden = $_POST['nro_orden'];
$tabla = $_POST['t'];

//selecciono los ids de la orden
$sql = "SELECT id FROM $tabla WHERE nro_orden = $nro_orden";
$rsTemp = mysql_query($sql);
while ($rs = @mysql_fetch_array($rsTemp)){
	$ids[] = $rs['id'];
}
if(is_array($ids) and count($ids) > 0){
	$list = implode(",",$ids);
	$log[] = "Lista de ids: $list";

	$ok=1;

    $sql = "SELECT * FROM $tabla WHERE nro_orden = $nro_orden AND plan_id is not null";

    mysqli_query($conn,$sql);
    if(mysqli_affected_rows($conn) > 0){
        $ok=0;
        $log[] = "No es posible borrar esta orden. Se encuentra incluida en un plan de pago";

    }

	$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id IN ($list) AND operacion_tipo = '$tabla' and forma_pago = 'tarjeta'";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){
		$sql = "SELECT id, tarjeta_id FROM tarjeta_consumo WHERE id = ".$rs['forma_pago_id'];
		//echo "<br>".$sql;
		$rsTempTarjeta = mysql_query($sql);
		if($rsTarjeta = mysql_fetch_array($rsTempTarjeta)){
			$sql = "SELECT fecha FROM tarjeta_consumo_cuota WHERE tarjeta_consumo_id = ".$rsTarjeta['id'];
			//echo "<br>".$sql;
			$rsTempTarjetaCuota = mysql_query($sql);
			while($rsTarjetaCuota = mysql_fetch_array($rsTempTarjetaCuota)){
				$part=explode("-",$rsTarjetaCuota['fecha']);
				$sql = "SELECT id FROM tarjeta_resumen WHERE estado = 1 AND CONCAT(ano,mes) = '".$part[0].intval($part[1])."' AND tarjeta_id = ".$rsTarjeta['tarjeta_id'];
				$rsTempTarjetaResumen = mysql_query($sql);
				if(mysql_fetch_array($rsTempTarjetaResumen)){
					$ok=0;
					$log[] = "No es posible borrar esta orden. Se encuentra incluida en un resumen de tarjeta de cr&eacute;dito que ya se encuentra pago";
					break;
				}
			}
		}
	}
	include_once("config/user.php");
	if(!ACCION_134){
		$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id IN ($list) AND operacion_tipo = '$tabla' and forma_pago = 'efectivo'";

		$rsTemp = mysql_query($sql);
		while($rs = mysql_fetch_array($rsTemp)){
			$sql = "SELECT id, caja_id, fecha FROM caja_movimiento WHERE origen = 'efectivo_consumo' AND registro_id = ".$rs['forma_pago_id'];
			$rsTempCajaMovimiento = mysql_query($sql);
			if($rsCajaMovimiento = mysql_fetch_array($rsTempCajaMovimiento)){
				$sql = "SELECT MAX(fecha) AS fecha
				FROM caja_sincronizada
				WHERE caja_id = ".$rsCajaMovimiento['caja_id'];

				$rsCajaSincronizada = mysql_fetch_array(mysql_query($sql));
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
		$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id IN ($list) AND operacion_tipo = '$tabla'";
		$rsTemp = mysql_query($sql);
		while($rs = mysql_fetch_array($rsTemp)){
			switch($rs['forma_pago']){
				case 'cheque':
				$sql = "DELETE FROM cuenta_movimiento WHERE origen = 'cheque' AND registro_id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "Forma de pago: cheque";
					$log[] = "&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}

				$sql = "DELETE FROM cheque_consumo WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "&nbsp; &nbsp; Eliminando el cheque emitido";
				}
				break;

				case 'debito':
				$sql = "DELETE FROM cuenta_movimiento WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "Forma de pago: debito de cuenta";
					$log[] = "&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}
				break;

				case 'efectivo':
				$sql = "DELETE FROM caja_movimiento WHERE origen = 'efectivo_consumo' AND registro_id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "Forma de pago: efectivo";
					$log[] = "&nbsp; &nbsp; Eliminando el movimiento de la caja";
				}

				$sql = "DELETE FROM efectivo_consumo WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "&nbsp; &nbsp; Eliminando el movimiento de efectivo";
				}

				break;

				case 'tarjeta':
				$sql = "DELETE FROM tarjeta_consumo_cuota WHERE tarjeta_consumo_id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "Forma de pago: tarjeta";
					$log[] = "&nbsp; &nbsp; Eliminando las cuotas";
				}

				$sql = "DELETE FROM tarjeta_consumo WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "&nbsp; &nbsp; Eliminando el movimiento de tarjeta";
				}

				break;

				case 'transferencia':
				$sql = "DELETE FROM cuenta_movimiento WHERE origen = 'transferencia' AND registro_id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "Forma de pago: transferencia";
					$log[] = "&nbsp; &nbsp; Eliminando el movimiento de cuenta";
				}

				$sql = "DELETE FROM transferencia_consumo WHERE id = ".$rs['forma_pago_id'];
				mysql_query($sql);
				if(mysql_affected_rows() > 0){
					$log[] = "&nbsp; &nbsp; Eliminando el consumo de transferencia";
				}
				break;
			}
		}
		$sql = "DELETE FROM rel_pago_operacion WHERE operacion_id IN ($list) AND operacion_tipo = '$tabla'";
		mysql_query($sql);
		if(mysql_affected_rows() > 0){
			$log[] = "Eliminando datos de tabla relacional";
		}

		$sql = "DELETE FROM $tabla WHERE nro_orden = $nro_orden";
		mysql_query($sql);
		if(mysql_affected_rows() > 0){
			$log[] = "Eliminando datos de tabla maestra";
		}

		//vemos si esta en cuentas a pagar
		$sql = "DELETE FROM cuenta_a_pagar WHERE operacion_id IN ($list) AND operacion_tipo = '$tabla'";
		mysql_query($sql);
		if(mysql_affected_rows() > 0){
			$log[] = "Eliminado de cuentas a pagar";
		}

		$log[] = "Listo!";
	}

	header('Content-type: application/json');
	echo json_encode(array("logs" => $log));

}else{
	echo "No se ha encontrado una orden con dicho numero";
}
?>
