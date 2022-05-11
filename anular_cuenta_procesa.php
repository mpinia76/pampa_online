<?php
include_once("config/db.php");
include_once("functions/util.php");


	$cuenta_a_pagar_id = $_GET['dataid'];



$sql = "SELECT * FROM cuenta_a_pagar WHERE id = ".$cuenta_a_pagar_id;

if(mysql_num_rows(mysql_query($sql)) != 0){
	$rsCuentaAPagar = mysql_fetch_array(mysql_query($sql));
	if ($rsCuentaAPagar['estado']==1) {
        if (empty($rsCuentaAPagar['plan_id'])){
            $ok=1;
            $sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCuentaAPagar['operacion_id']." AND operacion_tipo = '".$rsCuentaAPagar['operacion_tipo']."' and forma_pago = 'tarjeta'";

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
                        //echo "<br>".$sql;
                        $rsTempTarjetaResumen = mysql_query($sql);
                        if(mysql_fetch_array($rsTempTarjetaResumen)){
                            $ok=0;
                            echo "<br>No es posible borrar este pago. Se encuentra incluido en un resumen de tarjeta de cr&eacute;dito que ya se encuentra ABONADO";
                            break;
                        }
                    }
                }
            }
            include_once("config/user.php");
            if(!ACCION_134){
                $sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCuentaAPagar['operacion_id']." AND operacion_tipo = '".$rsCuentaAPagar['operacion_tipo']."' and forma_pago = 'efectivo'";

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
                $sql = "SELECT * FROM rel_pago_operacion WHERE operacion_id = ".$rsCuentaAPagar['operacion_id']." AND operacion_tipo = '".$rsCuentaAPagar['operacion_tipo']."'";

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
                            $sql = "SELECT * FROM cheque_consumo WHERE id = ".$rs['forma_pago_id'];

                            $rsTempCheque = mysql_query($sql);
                            while($rsCheque = mysql_fetch_array($rsTempCheque)){
                                $numero = str_pad($rsCheque['numero'], 8,'0',STR_PAD_LEFT);
                                $sql = "UPDATE chequera_cheques INNER JOIN chequeras ON chequera_cheques.chequera_id = chequeras.id SET chequera_cheques.estado = 0 WHERE chequeras.cuenta_id = '".$rsCheque['cuenta_id']."' AND chequera_cheques.numero = '".$numero."'";
                                mysql_query($sql);
								_LogCheques('Update '.$sql);
                                if(mysql_affected_rows() > 0){

                                    $sql = "SELECT chequeras.id FROM chequera_cheques INNER JOIN chequeras ON chequera_cheques.chequera_id = chequeras.id WHERE chequeras.cuenta_id = '".$rsCheque['cuenta_id']."' AND chequera_cheques.numero = '".$numero."'";

                                    $rsTempChequera = mysql_query($sql);
                                    if($rsChequera = mysql_fetch_array($rsTempChequera)){
										_LogCheques('El cheque '.str_pad($rsCheque['numero'], 8,'0',STR_PAD_LEFT).' se pasó a 0 en la chequera '.$rsChequera['id']);
                                        $sql = "SELECT chequera_cheques.chequera_id FROM chequera_cheques  WHERE chequera_cheques.chequera_id = '".$rsChequera['id']."' AND chequera_cheques.estado = '0'";

                                        mysql_query($sql);
                                        $estadoChequera = (mysql_affected_rows() > 0)?'1':'3';
                                        $sql = "UPDATE chequeras SET estado = ".$estadoChequera." WHERE id = '".$rsChequera['id']."'";
                                        //echo $sql;
                                        mysql_query($sql);
                                    }

                                    echo "<br>&nbsp; &nbsp; Actualizando a disponible cheque en chequera";
                                }
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
                    $sql = "DELETE FROM rel_pago_operacion WHERE operacion_id = ".$rsCuentaAPagar['operacion_id']." AND operacion_tipo = '".$rsCuentaAPagar['operacion_tipo']."'";

                    mysql_query($sql);


                    $sql = "UPDATE cuenta_a_pagar SET
                                            estado=0,fecha_pago=null
                                        WHERE id=".$cuenta_a_pagar_id;

                    //echo $sql;
                    mysql_query($sql);
                    echo "<br>Actualizando a NO pagada";
                    echo "<br>Listo!";
                }
            }
        }else{
            echo "La cuenta se encuentra incluida en un plan de pagos";
        }


	}else{
		echo "La cuenta debe estar en estado pagada";
	}



}else{
	echo "No hay registro";
}

?>
