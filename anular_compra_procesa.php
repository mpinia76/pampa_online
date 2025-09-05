<?php
include_once("config/db.php");
include_once("functions/util.php");


	$compra_id = $_GET['dataid'];



$sql = "SELECT * FROM compra WHERE id = ".$compra_id;

if(mysqli_num_rows(mysqli_query($conn,$sql)) != 0){
	$rsCompra = mysqli_fetch_array(mysqli_query($conn,$sql));
	if ($rsCompra['estado']==1) {


		$ok=1;
		$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCompra['id']." AND operacion_tipo = 'compra' and forma_pago = 'tarjeta'";

		$rsTemp = mysqli_query($conn,$sql);
		while($rs = mysqli_fetch_array($rsTemp)){
			$sql = "SELECT id, tarjeta_id FROM tarjeta_consumo WHERE id = ".$rs['forma_pago_id'];
			$rsTempTarjeta = mysqli_query($conn,$sql);
			if($rsTarjeta = mysqli_fetch_array($rsTempTarjeta)){
				$sql = "SELECT fecha FROM tarjeta_consumo_cuota WHERE tarjeta_consumo_id = ".$rsTarjeta['id'];
				$rsTempTarjetaCuota = mysqli_query($conn,$sql);
				while($rsTarjetaCuota = mysqli_fetch_array($rsTempTarjetaCuota)){

					$part=explode("-",$rsTarjetaCuota['fecha']);
					$sql = "SELECT id FROM tarjeta_resumen WHERE estado = 1 AND CONCAT(ano,mes) = '".$part[0].intval($part[1])."' AND tarjeta_id = ".$rsTarjeta['tarjeta_id'];
					//echo $sql;
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
			$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCompra['id']." AND operacion_tipo = 'compra' and forma_pago = 'efectivo'";

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
			//vemos si esta en cuentas a pagar
			$sql = "DELETE FROM cuenta_a_pagar WHERE operacion_id = ".$rsCompra['id'];
			//echo $sql;
			mysqli_query($conn,$sql);
			if(mysql_affected_rows() > 0){
				echo "<br>Forma de pago: cuentas a pagar";
				echo "<br>&nbsp; &nbsp; Eliminando";
			}
			$sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCompra['id']." AND operacion_tipo = 'compra'";

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
                        $sql = "SELECT * FROM cheque_consumo WHERE id = ".$rs['forma_pago_id'];

                        $rsTempCheque = mysqli_query($conn,$sql);
                        while($rsCheque = mysqli_fetch_array($rsTempCheque)){
                            $numero = str_pad($rsCheque['numero'], 8,'0',STR_PAD_LEFT);
                            $sql = "UPDATE chequera_cheques INNER JOIN chequeras ON chequera_cheques.chequera_id = chequeras.id SET chequera_cheques.estado = 0 WHERE chequeras.cuenta_id = '".$rsCheque['cuenta_id']."' AND chequera_cheques.numero = '".$numero."'";
                            mysqli_query($conn,$sql);
							_LogCheques('Update '.$sql);
                            if(mysql_affected_rows() > 0){

                                $sql = "SELECT chequeras.id FROM chequera_cheques INNER JOIN chequeras ON chequera_cheques.chequera_id = chequeras.id WHERE chequeras.cuenta_id = '".$rsCheque['cuenta_id']."' AND chequera_cheques.numero = '".$numero."'";

                                $rsTempChequera = mysqli_query($conn,$sql);
                                if($rsChequera = mysqli_fetch_array($rsTempChequera)){
									_LogCheques('El cheque '.str_pad($numero, 8,'0',STR_PAD_LEFT).' se pasó a 0 en la chequera '.$rsChequera['id']);
                                    $sql = "SELECT chequera_cheques.chequera_id FROM chequera_cheques  WHERE chequera_cheques.chequera_id = '".$rsChequera['id']."' AND chequera_cheques.estado = '0'";

                                    mysqli_query($conn,$sql);
                                    $estadoChequera = (mysql_affected_rows() > 0)?'1':'3';
                                    $sql = "UPDATE chequeras SET estado = ".$estadoChequera." WHERE id = '".$rsChequera['id']."'";
                                    //echo $sql;
                                    mysqli_query($conn,$sql);
                                }

                                echo "<br>&nbsp; &nbsp; Actualizando a disponible cheque en chequera";
                            }
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
				$sql = "DELETE FROM rel_pago_operacion WHERE operacion_id = ".$rsCompra['id']." AND operacion_tipo = 'compra'";

				mysqli_query($conn,$sql);


			}
			$sql = "UPDATE compra SET
									estado=0
								WHERE id=".$compra_id;


				mysqli_query($conn,$sql);
				echo "<br>Actualizando a NO pagada";
				echo "<br>Listo!";
		}



	}else{
		echo "La compra debe estar en estado procesada";
	}



}else{
	echo "No hay compra";
}

?>
