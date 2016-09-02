<?php
class InformesController extends AppController {
    
    function index_ventas_economico(){
        $this->layout = 'informe';
    }
    function index_ventas_financiero(){
        $this->layout = 'informe';
    }
    
    function ventas_economico($ano){
        ini_set( "memory_limit", "-1" );
        ini_set('max_execution_time', "-1");
        //error_reporting(0);
        
        $this->layout = 'ajax';
        
        $this->loadModel('Reserva');
        $this->loadModel('CobroTarjeta');
        $this->loadModel('CobroTarjetaPosnet');
        $this->loadModel('CobroTarjetaLote');
        $this->loadModel('Caja');
        $this->loadModel('Cuenta');
        $this->loadModel('CobroTransferencia');
        $this->loadModel('CobroCheque');
        $this->loadModel('Apartamento');
        $this->loadModel('ExtraRubro');
        
        $meses = array(1=>'Enero', 2=> 'Febrero', 3=> 'Marzo', 4=> 'Abril', 5=> 'Mayo', 6=> 'Junio', 7=> 'Julio', 8=> 'Agosto', 9=> 'Septiembre', 10=>'Octubre', 11=> 'Noviembre', 12=>'Diciembre');
        $this->set('meses',$meses);
        
        $reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano), 'recursive' => 2));
        //$reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'MONTH(check_out)' => 3), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('check_out <=' => '2014-02-28', 'check_out >=' => '2014-02-01'), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('check_out' => '2014-03-31'), 'recursive' => 2));
		
        for($i=1; $i<=12; $i++){
            $alojamientos[$i] = 0;
            $adelantadas[$i] = 0;
            $no_adelantadas[$i] = 0;
            $descuentos[$i] = 0;
            $intereses[$i] = 0;
            $devoluciones[$i] = 0;
            $descuentos_tarjetas[$i] = 0;
            $capacidad_ocupada[$i] = 0;
            $ventas_netas[$i] = 0;
            $cobrado[$i] = 0;
            $pendiente_cobro[$i] = 0;
        }
        
        $extra_rubros = $this->ExtraRubro->find('list');
        $this->set('extra_rubros',$extra_rubros);
        foreach($extra_rubros as $id => $rubro){
            for($i=1; $i<=12; $i++){
                $adelantadas_rubro[$id][$i] = 0;
                $no_adelantadas_rubro[$id][$i] = 0;
            }
        }
        
        $cobro_posnets = $this->CobroTarjetaPosnet->find('list');
        $this->set('posnets',$cobro_posnets);
        foreach($cobro_posnets as $id => $posnet){
            for($i=1; $i<=12; $i++){
                $cobro_posnet[$id][$i] = 0;
            }
        } 
        
        $cuentas = $this->Cuenta->find('list',array('conditions' => array('visible_en_informe' => 1)));
        $this->set('cuentas',$cuentas);
        foreach($cuentas as $id => $nombre){
            for($i=1; $i<=12; $i++){
                $cobro_cuenta[$id][$i] = 0;
            }
        }

        $cajas = $this->Caja->find('list',array('conditions' => array('visible_en_informe' => 1)));
        $this->set('cajas',$cajas);
        foreach($cajas as $id => $nombre){
            for($i=1; $i<=12; $i++){
                $cobro_caja[$id][$i] = 0;
            }
        }
        
        for($i=1; $i<=12; $i++){
            $cobro_cheque['COMUN'][$i] = 0;
            $cobro_cheque['DIFERIDO'][$i] = 0;
        }
        
        for($i=1; $i<=12; $i++){
            $devoluciones_pago['EFECTIVO'][$i] = 0;
            $devoluciones_pago['CHEQUE'][$i] = 0;
            $devoluciones_pago['TRANSFERENCIA'][$i] = 0;
        }
        
        if(count($reservas) > 0){

            foreach($reservas as $reserva){
                
                //verifico que la reserva no este cancelada
                if($reserva['Reserva']['estado'] != 2){
                    $alojamientos[$reserva['Reserva']['mes']] += $reserva['Reserva']['total_estadia'];
                    $ventas_netas[$reserva['Reserva']['mes']] += $reserva['Reserva']['total_estadia'];
					
                    if(count($reserva['ReservaExtra']>0)){ 
                        foreach($reserva['ReservaExtra'] as $extra){
                            if($extra['adelantada'] == 1){
                                if($extra['extra_id']){
                                    $adelantadas_rubro[$extra['Extra']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }elseif($extra['extra_variable_id']){
                                    $adelantadas_rubro[$extra['ExtraVariable']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }
                                $adelantadas[$reserva['Reserva']['mes']]  += $extra['cantidad'] * $extra['precio'];
                                $ventas_netas[$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                            }else{
                                if($extra['extra_id']){
                                    $no_adelantadas_rubro[$extra['Extra']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }elseif($extra['extra_variable_id']){
                                    $no_adelantadas_rubro[$extra['ExtraVariable']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }
                                $no_adelantadas[$reserva['Reserva']['mes']]  += $extra['cantidad'] * $extra['precio'];
                                $ventas_netas[$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                            }
                        }
                    }
                }
                $cobroCancelado = 0;
                if(count($reserva['ReservaCobro'])>0){
                	
                    foreach($reserva['ReservaCobro'] as $cobro){
                        if($cobro['tipo'] == 'DESCUENTO' and $reserva['Reserva']['estado'] != 2){ //no listamos los descuentos comerciales si se cancelo
                            $descuentos[$reserva['Reserva']['mes']] += $cobro['monto_neto'];
                            $ventas_netas[$reserva['Reserva']['mes']] -= $cobro['monto_neto'];
                        }else{
                        	if($reserva['Reserva']['estado'] != 2){
	                        	/*$INT = $cobro['monto_cobrado'] - $cobro['monto_neto'];
	                        	echo $reserva['Reserva']['numero']." - ".$INT."<br>";*/
	                            $intereses[$reserva['Reserva']['mes']] += $cobro['monto_cobrado'] - $cobro['monto_neto'];
	                            
	                            //si se cancelo la reserva y existieron pagos se transoforma en venta neta el total de los cobros
	                            /*if($reserva['Reserva']['estado'] == 2){
	                                $ventas_netas[$reserva['Reserva']['mes']] += $cobro['monto_neto'];
	                            }*/
	                            
	                            $ventas_netas[$reserva['Reserva']['mes']] += $cobro['monto_cobrado'] - $cobro['monto_neto'];
                        	}
                        }
                        switch($cobro['tipo']){
                            case 'TARJETA':
                                $cobro_tarjeta = $this->CobroTarjeta->findById($cobro['CobroTarjeta']['id']);
                                if($cobro_tarjeta['CobroTarjetaLote']['acreditado_por'] != 0){
                                    $cobrado[$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['total'];
                                    $cobroCancelado += $cobro_tarjeta['CobroTarjeta']['total'];
                                    if($reserva['Reserva']['estado'] != 2){
                                    	$ventas_netas[$reserva['Reserva']['mes']] -= $cobro_tarjeta['CobroTarjeta']['descuento_lote'];
                                    }
                                    $descuentos_tarjetas[$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['descuento_lote'];
                                    $cobro_posnet[$cobro_tarjeta['CobroTarjetaTipo']['cobro_tarjeta_posnet_id']][$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['total'];
                                    //$intereses[$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['interes'];
                                }else{
                                    $pendiente_cobro[$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['total'];
                                }
                                break;
                                
                            case 'EFECTIVO':
                                $cobro_caja[$cobro['CobroEfectivo']['caja_id']][$reserva['Reserva']['mes']] += $cobro['CobroEfectivo']['monto_neto'];
                                $cobrado[$reserva['Reserva']['mes']] += $cobro['CobroEfectivo']['monto_neto'];
                                $cobroCancelado += $cobro['CobroEfectivo']['monto_neto'];
                                break;
                            
                            case 'TRANSFERENCIA':
                                if($cobro['CobroTransferencia']['acreditado']){
                                    $cobro_cuenta[$cobro['CobroTransferencia']['cuenta_id']][$reserva['Reserva']['mes']] += $cobro['CobroTransferencia']['total'];
                                    $cobrado[$reserva['Reserva']['mes']] += $cobro['CobroTransferencia']['total'];
                                    $cobroCancelado += $cobro['CobroTransferencia']['total'];
                                    //$intereses[$reserva['Reserva']['mes']] += $cobro['CobroTransferencia']['interes'];
                                }else{
                                    $pendiente_cobro[$reserva['Reserva']['mes']] += $cobro['CobroTransferencia']['total'];
                                }
                                break;
                            
                            case 'CHEQUE':
                                if($cobro['CobroCheque']['acreditado'] or $cobro['CobroCheque']['asociado_a_pagos']){
                                    $cobro_cheque[$cobro['CobroCheque']['tipo']][$reserva['Reserva']['mes']] += $cobro['CobroCheque']['monto_neto'];
                                    $cobrado[$reserva['Reserva']['mes']] += $cobro['CobroCheque']['total'];
                                    $cobroCancelado += $cobro['CobroCheque']['total'];
                                    //$intereses[$reserva['Reserva']['mes']] += $cobro['CobroCheque']['interes'];
                                }else{
                                    $pendiente_cobro[$reserva['Reserva']['mes']] += $cobro['CobroCheque']['total'];
                                }
                                break;
                        }
                    }
                }
                
                if(count($reserva['ReservaDevolucion'])>0){
                    foreach($reserva['ReservaDevolucion'] as $devolucion){
                        $devoluciones[$reserva['Reserva']['mes']] += $devolucion['monto'];
                        $devoluciones_pago[$devolucion['forma_pago']][$reserva['Reserva']['mes']] -= $devolucion['monto'];
                        $cobroCancelado -= $devolucion['monto'];
                        //$ventas_netas[$reserva['Reserva']['mes']] -= $devolucion['monto'];
                    }
                }
                
                $capacidad_ocupada[$reserva['Reserva']['mes']]  += ($reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'] + $reserva['Reserva']['pax_bebes']) * $reserva['Reserva']['noches'];
				
            	if($reserva['Reserva']['estado'] == 2){
            		$alojamientos[$reserva['Reserva']['mes']] += $cobroCancelado;
	        		$ventas_netas[$reserva['Reserva']['mes']] += $cobroCancelado;
	             }
	             //echo $reserva['Reserva']['mes']." - ".$ventas_netas[$reserva['Reserva']['mes']]."<br>";
            }// foreach reservas
        }// if count reservas > 0
        
//        $lotes = $this->CobroTarjetaLote->find('all',array('conditions' => array('YEAR(fecha_cierre)' => $ano, 'acreditado_por !=' => 0)));
//        foreach($lotes as $lote){
//            $descuentos_tarjetas[$lote['CobroTarjetaLote']['mes_cierre']] += $lote['CobroTarjetaLote']['descuentos'];
//            $descuentos_tarjeta_posnets[$lote['CobroTarjetaTipo']['cobro_tarjeta_posnet_id']][$lote['CobroTarjetaLote']['mes_cierre']] += $lote['CobroTarjetaLote']['descuentos']; print_r($descuentos_tarjeta_posnets);
//            $cobrado[$lote['CobroTarjetaLote']['mes_cierre']] -= $lote['CobroTarjetaLote']['descuentos'];
//            $ventas_netas[$reserva['Reserva']['mes']] -= $lote['CobroTarjetaLote']['descuentos'];
//        }
        
        $apartamentos = $this->Apartamento->find('all');
        $q_apartamentos = count($apartamentos);
        $capacidad_total = 0;
        foreach($apartamentos as $apartamento){
            $capacidad_total += $apartamento['Apartamento']['capacidad'] * 30;
        }
        //$capacidad_total = $capacidad_total * 30;
        
        $this->set(array(
            'ano' => $ano,
            'alojamientos' => $alojamientos,
            'no_adelantadas' => $no_adelantadas,
            'adelantadas' => $adelantadas,
            'descuentos' => $descuentos,
            'intereses' => $intereses,
            'devoluciones' => $devoluciones,
            'descuentos_tarjetas' => $descuentos_tarjetas,
            'descuentos_tarjeta_posnets' => $descuentos_tarjeta_posnets,
            'capacidad_total' => $capacidad_total,
            'capacidad_ocupada' => $capacidad_ocupada,
            'q_apartamentos' => $q_apartamentos,
            'ventas_netas' => $ventas_netas,
            'cobrado' => $cobrado,
            'pendiente_cobro' => $pendiente_cobro,
            'cobro_caja' => $cobro_caja,
            'cobro_cuenta' => $cobro_cuenta,
            'cobro_posnet' => $cobro_posnet,
            'cobro_cheque' => $cobro_cheque,
            'adelantadas_rubro' => $adelantadas_rubro,
            'no_adelantadas_rubro' => $no_adelantadas_rubro,
            'devoluciones_pago' => $devoluciones_pago
        ));

    }
    
    function ventas_financiero($ano){
        //error_reporting(0);  
        $this->layout = 'ajax';
        
        $this->loadModel('Caja');
        $this->loadModel('Cuenta');
        $this->loadModel('CobroTarjetaPosnet');
        $this->loadModel('CobroEfectivo');
        $this->loadModel('CobroCheque');
        $this->loadModel('CobroTransferencia');
        $this->loadModel('CobroTarjetaLote');
        $this->loadModel('ReservaDevolucion');
        
        $meses = array(1=>'Enero', 2=> 'Febrero', 3=> 'Marzo', 4=> 'Abril', 5=> 'Mayo', 6=> 'Junio', 7=> 'Julio', 8=> 'Agosto', 9=> 'Septiembre', 10=>'Octubre', 11=> 'Noviembre', 12=>'Diciembre');
        $this->set('meses',$meses);
        
        for($i=1; $i<=12; $i++){
            $cobro_neto[$i] = 0;
        }
        
        //busoc los cobros en efectivo del ano
        $cajas = $this->Caja->find('list',array('conditions' => array('visible_en_informe' => 1)));
        $this->set('cajas',$cajas);
        foreach($cajas as $id => $nombre){
            for($i=1; $i<=12; $i++){
                $cobro_caja[$id][$i] = 0;
            }
        }
        
        $cobro_efectivos = $this->CobroEfectivo->find('all',array('conditions' => array('YEAR(ReservaCobro.fecha)' => $ano)));
        if(count($cobro_efectivos)>0){
            foreach($cobro_efectivos as $ce){
                $cobro_neto[$ce['ReservaCobro']['mes']] += $ce['ReservaCobro']['monto_cobrado'];
                $cobro_caja[$ce['CobroEfectivo']['caja_id']][$ce['ReservaCobro']['mes']] += $ce['CobroEfectivo']['monto_neto'];
            }
        }
        
        //busco los cobros con cheques del ano
        for($i=1; $i<=12; $i++){
            $cobro_cheque['COMUN'][$i] = 0;
            $cobro_cheque['DIFERIDO'][$i] = 0;
        }
        
        $cobro_cheques = $this->CobroCheque->find('all',array('conditions' => array('OR' => array('YEAR(fecha_acreditado)' => $ano, 'YEAR(asociado_a_pagos_fecha)' => $ano))));
        if(count($cobro_cheques) >0){
            foreach($cobro_cheques as $cc){
                if($cc['CobroCheque']['asociado_a_pagos']){
                    $cobro_neto[$cc['CobroCheque']['mes_asociado_a_pagos']] += $cc['CobroCheque']['total'];
                    $cobro_cheque[$cc['CobroCheque']['tipo']][$cc['CobroCheque']['mes_asociado_a_pagos']] += $cc['CobroCheque']['total'];
                }else{
                    $cobro_neto[$cc['CobroCheque']['mes_acreditado']] += $cc['CobroCheque']['total'];
                    $cobro_cheque[$cc['CobroCheque']['tipo']][$cc['CobroCheque']['mes_acreditado']] += $cc['CobroCheque']['total'];
                }
                
            }
        }
        
        //busco los cobros con transferencias del ano
        $cuentas = $this->Cuenta->find('list',array('conditions' => array('visible_en_informe' => 1)));
        $this->set('cuentas',$cuentas);
        foreach($cuentas as $id => $nombre){
            for($i=1; $i<=12; $i++){
                $cobro_cuenta[$id][$i] = 0;
            }
        }
        
        $cobro_transferencias = $this->CobroTransferencia->find('all',array('conditions' => array('YEAR(fecha_acreditado)' => $ano)));
        if(count($cobro_transferencias) > 0){
            foreach($cobro_transferencias as $ct){
                $cobro_neto[$ct['CobroTransferencia']['mes_acreditado']] += $ct['CobroTransferencia']['total'];
                $cobro_cuenta[$ct['CobroTransferencia']['cuenta_id']][$ct['CobroTransferencia']['mes_acreditado']] += $ct['CobroTransferencia']['total'];
            }
        }
        
        //busco los cobros con tarjeta
        $cobro_posnets = $this->CobroTarjetaPosnet->find('list');
        $this->set('posnets',$cobro_posnets);
        foreach($cobro_posnets as $id => $posnet){
            for($i=1; $i<=12; $i++){
                $cobro_posnet[$id][$i] = 0;
            }
        }
        
        $cobro_tarjeta_lotes = $this->CobroTarjetaLote->find('all',array('conditions' => array('YEAR(fecha_acreditacion)' => $ano)));
        if(count($cobro_tarjeta_lotes) >0){
            foreach($cobro_tarjeta_lotes as $ctl){
                $cobro_neto[$ctl['CobroTarjetaLote']['mes_acreditacion']] += $ctl['CobroTarjetaLote']['monto_total'] - $ctl['CobroTarjetaLote']['descuentos'];
                $cobro_posnet[$ctl['CobroTarjetaTipo']['cobro_tarjeta_posnet_id']][$ctl['CobroTarjetaLote']['mes_acreditacion']] += $ctl['CobroTarjetaLote']['monto_total'] - $ctl['CobroTarjetaLote']['descuentos'];
            }
        } 
        
        //agrego las devoluciones
        for($i=1; $i<=12; $i++){
            $devoluciones_pago['EFECTIVO'][$i] = 0;
            $devoluciones_pago['CHEQUE'][$i] = 0;
            $devoluciones_pago['TRANSFERENCIA'][$i] = 0;
        }
        
        $devoluciones = $this->ReservaDevolucion->find('all',array('conditions' => array('YEAR(fecha)' => $ano)));
        if(count($devoluciones) >0){
            foreach($devoluciones as $devolucion){
                $cobro_neto[$devolucion['ReservaDevolucion']['mes']] -= $devolucion['ReservaDevolucion']['monto'];
                $devoluciones_pago[$devolucion['ReservaDevolucion']['forma_pago']][$devolucion['ReservaDevolucion']['mes']]  -= $devolucion['ReservaDevolucion']['monto'];
            }
        }
        
        $this->set(array(
            'ano' => $ano,
            'cobro_neto' => $cobro_neto,
            'cobro_caja' => $cobro_caja,
            'cobro_cuenta' => $cobro_cuenta,
            'cobro_posnet' => $cobro_posnet,
            'cobro_cheque' => $cobro_cheque,
            'devoluciones_pago' => $devoluciones_pago
        ));
    }

    function ventas_economico_financiero($mes,$ano){
        error_reporting(0);
        $this->layout = 'ajax';
        
        $this->loadModel('Reserva');
        $this->loadModel('CobroTarjeta');
        
        $cobrado = array();
        $pendiente_cobro = 0;
        $ventas_netas = 0;
        
        $meses = array('01'=>'Enero', '02'=> 'Febrero','03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', 10=>'Octubre', 11=> 'Noviembre', 12=>'Diciembre');
        $this->set('meses',$meses);
        
        $reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'MONTH(check_out)' => $mes), 'recursive' => 2));
        //$reservas = $this->Reserva->find('all',array('conditions' => array('check_out <=' => '2014-03-31', 'check_out >=' => '2014-03-01'), 'recursive' => 2));
        //$reservas = $this->Reserva->find('all',array('conditions' => array('check_out' => '2014-03-31'), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('numero' => '394'), 'recursive' => 2));
        if(count($reservas) > 0){
            
            foreach($reservas as $reserva){
            	$veAux = 0;
                if($reserva['Reserva']['estado'] != 2){
                    $ventas_netas += $reserva['Reserva']['total_estadia'];
					$veAux += $reserva['Reserva']['total_estadia'];
                    if(count($reserva['ReservaExtra']>0)){ 
                        foreach($reserva['ReservaExtra'] as $extra){
                            $ventas_netas += $extra['cantidad'] * $extra['precio'];
                            $veAux += $extra['cantidad'] * $extra['precio'];
                        }
                    }
                }
                $cobroCancelado = 0;
                if(count($reserva['ReservaCobro']) > 0){
                	
                    foreach($reserva['ReservaCobro'] as $cobro){
                    	
                    	if($reserva['Reserva']['estado'] != 2){
	                        if($cobro['tipo'] == 'DESCUENTO'){
	                            $ventas_netas -= $cobro['monto_neto'];
	                            $veAux -= $cobro['monto_neto'];
	                        }else{
	                        	
	                            /*if($reserva['Reserva']['estado'] == 2){
	                                $ventas_netas += $cobro['monto_neto'];
	                            }*/
	                            
	                            $ventas_netas += $cobro['monto_cobrado'] - $cobro['monto_neto'];
	                            $veAux += $cobro['monto_cobrado'] - $cobro['monto_neto'];
	                        	
	                        }
                    	}
                        switch($cobro['tipo']){
                            case 'TARJETA':
                                $cobro_tarjeta = $this->CobroTarjeta->findById($cobro['CobroTarjeta']['id']);
                                //echo $cobro_tarjeta['CobroTarjeta']['total']."<br>";
                                if($cobro_tarjeta['CobroTarjetaLote']['acreditado_por'] != 0){
                                	if($reserva['Reserva']['estado'] != 2){
                                		$ventas_netas -= $cobro_tarjeta['CobroTarjeta']['descuento_lote'];
                                		$veAux -= $cobro_tarjeta['CobroTarjeta']['descuento_lote'];
                                	}
                                    $cobrado[$cobro_tarjeta['CobroTarjetaLote']['ano_mes_acreditado']] += $cobro_tarjeta['CobroTarjeta']['total'];
                                    $cobroCancelado += $cobro_tarjeta['CobroTarjeta']['total'];
                                    //$ventas_netas += $cobro_tarjeta['CobroTarjeta']['interes'];
                                }else{
                                    $pendiente_cobro += $cobro_tarjeta['CobroTarjeta']['total'];
                                }
                                break;
                                
                            case 'EFECTIVO':
                                $cobrado[$cobro['ano_mes']] += $cobro['CobroEfectivo']['monto_neto'];
                                $cobroCancelado += $cobro['CobroEfectivo']['monto_neto'];
                                break;
                            
                            case 'TRANSFERENCIA':
                                if($cobro['CobroTransferencia']['acreditado']){
                                    $cobrado[$cobro['CobroTransferencia']['ano_mes_acreditado']] += $cobro['CobroTransferencia']['total'];
                                    $cobroCancelado += $cobro['CobroTransferencia']['total'];
                                    //$ventas_netas += $cobro['CobroTransferencia']['interes'];
                                }else{
                                    $pendiente_cobro += $cobro['CobroTransferencia']['total'];
                                }
                                break;
                            
                            case 'CHEQUE':
                                if($cobro['CobroCheque']['acreditado'] ){
                                    $cobrado[$cobro['CobroCheque']['ano_mes_acreditado']] += $cobro['CobroCheque']['total'];
                                    $cobroCancelado += $cobro['CobroCheque']['total'];
                                    //$ventas_netas += $cobro['CobroCheque']['interes'];
                                }elseif($cobro['CobroCheque']['asociado_a_pagos']){
                                    $cobrado[$cobro['CobroCheque']['ano_mes_asociado_a_pagos']] += $cobro['CobroCheque']['total'];
                                    $cobroCancelado += $cobro['CobroCheque']['total'];
                                    //$ventas_netas += $cobro['CobroCheque']['interes'];
                                }else{
                                    $pendiente_cobro += $cobro['CobroCheque']['total'];
                                }
                                break;
                        }
                    }
                }
                
                if(count($reserva['ReservaDevolucion'])>0){
                    foreach($reserva['ReservaDevolucion'] as $devolucion){
                        //$ventas_netas -= $devolucion['monto'];
                        $cobrado[$devolucion['ano_mes']] -= $devolucion['monto'];
                        $cobroCancelado -= $devolucion['monto'];
                    }
                }
            	if($reserva['Reserva']['estado'] == 2){
                	$ventas_netas += $cobroCancelado;
                	$veAux += $cobroCancelado;
                	//echo $reserva['Reserva']['numero'].' - '.$cobroCancelado."<br>";
                }
            $total = $veAux - $cobroCancelado;   
			//echo $reserva['Reserva']['numero'].' - '.$veAux.'-'.$cobroCancelado.'='.$total."<br>";
            }// foreach reservas
        }// if count reservas > 0
        
        ksort($cobrado);
        
        $this->set(array(
            'ano' => $ano,
            'mes' => $mes,
            'ventas_netas' => $ventas_netas,
            'cobrado' => $cobrado,
            'pendiente_cobro' => $pendiente_cobro
        ));
    }
}
?>
