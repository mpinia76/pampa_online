<?php
session_start();
class InformesController extends AppController {
    public $components = array('ExportXls');
	function array_sort_by(&$arrIni, $col, $order = SORT_ASC)
	{
	    $arrAux = array();
	    foreach ($arrIni as $key=> $row)
	    {
	        $arrAux[$key] = is_object($row) ? $arrAux[$key] = $row->$col : $row[$col];
	        $arrAux[$key] = strtolower($arrAux[$key]);
	    }
	    array_multisort($arrAux, $order, $arrIni);
	}
    function index_ventas_economico(){
        $this->layout = 'informe';
         $this->loadModel('Categoria');
		$categorias = $this->Categoria->find('all',array( 'order' => 'categoria ASC'));


        $this->set(array(

        	'categorias' => $categorias

        ));
          $this->setLogUsuario('Informe economico - ventas');
    }

	function index_ventas_extras(){
        $this->layout = 'informe';
        /*if (!isset($_SESSION['paginaOperaciones'])) {

        	$_SESSION['paginaOperaciones']=1;
        }*/
         $this->setLogUsuario('Informe de extras');
    }

	function index_ventas_extras_economico(){
       $this->layout = 'informeDefault';
        //$_SESSION['paginaOperaciones']=1;

    }
	function index_ventas_extras_listado(){
       $this->layout = 'informeDefault';
       $this->loadModel('ExtraRubro');
        $this->set('extra_rubros',$this->ExtraRubro->find('list'));
         $this->loadModel('Usuario');
		 $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));

		$permisoAdelantada=1;
		$permisoNoAdelantada=1;
		//print_r($user);
        if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $permisoAdelantada=0;
			$permisoNoAdelantada=0;
	    	foreach($permisos as $permiso){
               if ($permiso['UsuarioPermiso']['permiso_id']==123) {
               		$permisoAdelantada=1;
               		continue;
               }
    			if ($permiso['UsuarioPermiso']['permiso_id']==124) {
               		$permisoNoAdelantada=1;
               		continue;
               }

	        }
        }

      $this->set('permisoAdelantada',$permisoAdelantada);
       $this->set('permisoNoAdelantada',$permisoNoAdelantada);
        //$_SESSION['paginaOperaciones']=1;

    }
 	function index_ventas_ocupacion(){
        $this->layout = 'informe';
        $this->setLogUsuario('Informe de ocupacion');
    }
    function index_ventas_financiero(){
        $this->layout = 'informe';
        $this->setLogUsuario('Informe financiero - ventas');
    }

	function index_iva_compras(){
        $this->layout = 'informe';
        $this->setLogUsuario('Libro IVA Compras');
    }

    function index_base_datos(){
        $this->layout = 'informe';
        $this->setLogUsuario('Base de datos');
    }

	function index_iva_ventas(){
        $this->layout = 'informe';
        $this->setLogUsuario('Libro IVA Ventas');

        $this->loadModel('PuntoVenta');


        $puntos_venta = $this->PuntoVenta->find('all',array('conditions' => array('ivaVentas =' =>1), 'order' => 'numero ASC'));


        $this->set(array(

        	'puntos_venta' => $puntos_venta

        ));
    }

	function index_ventas_grilla(){
        $this->layout = 'informeGrilla';
        ini_set( "memory_limit", "-1" );
        ini_set('max_execution_time', "-1");

         $this->loadModel('Apartamento');

        $unidads = $this->Apartamento->find('all', array('order' => 'orden ASC'));

         $this->setLogUsuario('Grilla de reservas');


		$this->loadModel('GrillaFeriado');

        $feriados = $this->GrillaFeriado->find('all', array('order' => 'desde ASC'));





        $this->set(array(
           'unidads' => $unidads
        ));

        $this->set(array(
            'feriados' => $feriados
        ));
    }

	function ventas_grilla($desde){
       $this->layout = false;
    	$this->autoRender = false;
        //$this->layout = 'json';
        ini_set( "memory_limit", "-1" );
        ini_set('max_execution_time', "-1");


		$this->loadModel('Usuario');
        $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));


        $permisoOcultarDatos=0;
        //print_r($user);
        if ($user['Usuario']['admin'] != '1'){
            $this->loadModel('UsuarioPermiso');
            $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
            $permisoOcultarDatos=0;
            foreach($permisos as $permiso){
                if ($permiso['UsuarioPermiso']['permiso_id']==145) {
                    $permisoOcultarDatos=1;
                    continue;
                }


            }
        }


       $hasta= date('Y-m-d', strtotime($desde. ' + 32 days'));

        $this->loadModel('Reserva');


        $reservas = $this->Reserva->find('all',array('conditions' => array('or'=>array(array('check_in >=' => $desde,'check_in <=' => $hasta),array('check_out >=' => $desde,'check_out <=' => $hasta),array('check_in <' => $desde,'check_out >' => $hasta))), 'recursive' => 1));

       /* App::uses('ConnectionManager', 'Model');
	        	$dbo = ConnectionManager::getDatasource('default');
			    $logs = $dbo->getLog();
			    $lastLog = end($logs['log']);

			    echo $lastLog['query'];*/
        //print_r($reservas);
        $reservasMostrar = array();
        if(count($reservas) > 0){

            foreach($reservas as $reserva){

                //verifico que la reserva no este cancelada
                if($reserva['Reserva']['estado'] != 2){
                	$adelantadas = 0;
		            $no_adelantadas = 0;
		            $pagado = 0;
		            $fiscal = 0;
		            $descontado = 0;
		            if(count($reserva['ReservaCobro'])>0){
		                foreach($reserva['ReservaCobro'] as $cobro){
		                    if($cobro['tipo'] == 'DESCUENTO'){
		                        $descontado += $cobro['monto_neto'];
		                    }else{
		                        if($cobro['tipo'] == 'TARJETA' or $cobro['tipo'] == 'TRANSFERENCIA'){
		                            $fiscal += $cobro['monto_cobrado'];
		                        }
		                        $pagado += $cobro['monto_neto'];
		                    }
		                }
		            }

	                //if(count($reserva['ReservaExtra']>0)){
		                foreach($reserva['ReservaExtra'] as $extra){
		                    if($extra['adelantada'] == 1){
		                        $adelantadas = $adelantadas + $extra['cantidad'] * $extra['precio'];
		                    }else{
		                        $no_adelantadas = $no_adelantadas + $extra['cantidad'] * $extra['precio'];
		                    }
		                }
		            //}

		            $devoluciones = 0;
		            if(count($reserva['ReservaDevolucion']) > 0){
		                foreach($reserva['ReservaDevolucion'] as $devolucion){
		                    $devoluciones += $devolucion['monto'];
		                }
		            }


		            $facturado = 0;
		            if(count($reserva['ReservaFactura']) > 0){
		                foreach($reserva['ReservaFactura'] as $factura){
		                    $facturado += $factura['monto'];
		                }
		            }
		            $total = round(round($reserva['Reserva']['total'],2) + round($no_adelantadas,2) - round($descontado,2),2);
		            $pago = round(round($pagado,2) + round($devoluciones,2),2);
		            $pendiente = round(round($reserva['Reserva']['total'],2) + round($no_adelantadas,2) - round($descontado,2) - round($pagado,2) + round($devoluciones,2),2);
		            $faltaPagar = $pendiente;
		            $pendiente = ($pendiente==-0)?0:$pendiente;


		            switch($reserva['Reserva']['estado']){
		                case 0:
		                    if($pagado == 0){
		                        $color='#ff0033';

				            }
				            elseif($pendiente > 0){
		                        $color='#f44611';
	                    	}
	                    	else{
		                        $color='#579d1c';
		                    }
		                    break;
		                case 1:
		                    $color='#579d1c';
		                    break;
		                case 3:
		                    $color='#f1fa52';
		                    break;

		            }

					$event_type = ($reserva['Reserva']['checkIn'])?'checkin_event':'';
                    $event_type = ($reserva['Reserva']['checkOut'])?'checkout_event':$event_type;
					$date_parts = explode("/",$reserva['Reserva']['check_in']);
        			$yy=$date_parts[2];
        			$mm=$date_parts[1];
        			$dd=$date_parts[0];
        			$dateRetiroStr = $yy.'-'.$mm.'-'.$dd;
        			$hrIn = explode(":", $reserva['Reserva']['hora_check_in']);

					$date_parts = explode("/",$reserva['Reserva']['check_out']);
        			$yy=$date_parts[2];
        			$mm=$date_parts[1];
        			$dd=$date_parts[0];
        			$dateDevolucionStr = $yy.'-'.$mm.'-'.$dd;
					$hr = explode(":", $reserva['Reserva']['late_check_out']);
					//$animado = ($hr[0]>=12)?'<i class="fa fa-star fa-lg fa-spin"></i>':'';
					$animado = ($hr[0]>=12)?'<span class="blink">LCO</span>':'';
                    //$reservasMostrar[]=array('unidad_id'=>$reserva['Apartamento']['id'],'reserva_id'=>$reserva['Reserva']['id'],'cliente_id'=>$reserva['Cliente']['id'],'numero'=>$reserva['Reserva']['numero'],'nombre'=>$reserva['Cliente']['nombre_apellido'],'retiro'=>$dateRetiroStr.' 10:00:00','devolucion'=>$dateDevolucionStr.' 10:00:00','fecha_retiro'=>$reserva['Reserva']['check_in'],'fecha_devolucion'=>$reserva['Reserva']['check_out'],'color'=>$color,'comentarios'=>"",'total'=>number_format($total,2),'pagado'=>number_format($pago,2),'pendiente'=>number_format($faltaPagar,2));

                    $mostrarMontos=($permisoOcultarDatos)?'':'<br />  <b>Total:</b>  '.number_format($total,2).'<b> Cobrado:</b>  '.number_format($pago,2).'<b> A Cobrar:</b>  '.number_format($faltaPagar,2);

                   $reservasMostrar[] = array('start_date'=>$dateRetiroStr.' '.$reserva['Reserva']['hora_check_in'], 'end_date'=>$dateDevolucionStr.' '.$reserva['Reserva']['late_check_out'], 'text' =>$animado.'<b>Titular:</b>'.$reserva['Cliente']['nombre_apellido'].'<br /> <b>Reserva Nro.:</b> '.$reserva['Reserva']['numero'].'<br /><b>Check IN:</b> '.$reserva['Reserva']['check_in'].' '.$hrIn[0].':'.$hrIn[1].'<br /><b>Check OUT:</b>'.$reserva['Reserva']['check_out'].' '.$hr[0].':'.$hr[1].$mostrarMontos.'<br /><b> Comentarios:</b>'.utf8_encode(str_replace("\r\n", "<br />",$reserva['Reserva']['comentarios'])), 'idReserva'=>$reserva['Reserva']['id'], 'section_id'=>$reserva['Apartamento']['id'], 'color'=>$color, 'type'=>$event_type, 'checkIn'=>$reserva['Reserva']['checkIn'], 'checkOut'=>$reserva['Reserva']['checkOut'], 'idCliente'=>$reserva['Cliente']['id'], 'readonly'=>false);
                }

	             //echo $reserva['Reserva']['mes']." - ".$ventas_netas[$reserva['Reserva']['mes']]."<br>";
            }// foreach reservas
        }// if count reservas > 0



		//print_r($reservasMostrar);





         return json_encode(array(
		        'data' => $reservasMostrar
		    ));
    }

    function ventas_economico($ano,$desde,$hasta, $categoria_id, $apartamento_id){
        ini_set( "memory_limit", "-1" );
        ini_set('max_execution_time', "-1");
        //error_reporting(0);

        $this->layout = 'ajax';

        $this->loadModel('Reserva');
        $this->loadModel('CobroEfectivo');
		$this->loadModel('CobroTransferencia');
		$this->loadModel('CobroCheque');
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
        $condicionApartamento = array();
        $condicionCategoria = array();
    	if ($apartamento_id!='null') {
    		$arrayApartamento = explode(",", $apartamento_id);
        	$condicionApartamento = array('Apartamento.id ' => $arrayApartamento);
        }
        elseif ($categoria_id!='Seleccionar...'){
        	$condicionCategoria=array('Apartamento.categoria_id =' => $categoria_id);
        }

    	if (($desde!='undefined-undefined-')&&($hasta!='undefined-undefined-')) {
				$condicionYear = array('YEAR(check_out)' => $ano, 'creado >=' => $desde, 'creado <=' => $hasta);

			}
			else{
				//$condicionYear = array('YEAR(check_out)' => $ano);
				$condicionYear =array('OR' =>array(array('YEAR(check_out)' => $ano),array('YEAR(check_in)' => $ano,'YEAR(check_out)' => intval($ano+1))));
			}



        //$reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'MONTH(check_out)' => 3), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('check_out <=' => '2014-02-28', 'check_out >=' => '2014-02-01'), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('check_out' => '2014-03-31'), 'recursive' => 2));

		$condicion=array($condicionApartamento,$condicionCategoria,$condicionYear);
		$reservas = $this->Reserva->find('all',array('conditions' => $condicion, 'recursive' => 1));

        if(count($reservas) > 0){
			 $this->loadModel('Extra');
        	$this->loadModel('ExtraVariable');
            foreach($reservas as $reserva){
                $date_parts = explode("/",$reserva['Reserva']['check_out']);
                $ano1=$date_parts[2];

                if($ano1==$ano) {
                    //verifico que la reserva no este cancelada
                    if(($reserva['Reserva']['estado'] != 2)&&($reserva['Reserva']['estado'] != 3)){
                        $alojamientos[$reserva['Reserva']['mes']] += $reserva['Reserva']['total_estadia'];
                        $ventas_netas[$reserva['Reserva']['mes']] += $reserva['Reserva']['total_estadia'];

                        if(count($reserva['ReservaExtra']>0)){
                            foreach($reserva['ReservaExtra'] as $extra){
                                $extraRubro=$this->Extra->findById($extra['extra_id']);
                                $extraVariable=$this->ExtraVariable->findById($extra['extra_variable_id']);
                                if($extra['adelantada'] == 1){
                                    if($extra['extra_id']){
                                        $adelantadas_rubro[$extraRubro['Extra']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                    }elseif($extra['extra_variable_id']){
                                        $adelantadas_rubro[$extraVariable['ExtraVariable']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                    }
                                    $adelantadas[$reserva['Reserva']['mes']]  += $extra['cantidad'] * $extra['precio'];
                                    $ventas_netas[$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }else{
                                    if($extra['extra_id']){
                                        $no_adelantadas_rubro[$extraRubro['Extra']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                    }elseif($extra['extra_variable_id']){
                                        $no_adelantadas_rubro[$extraVariable['ExtraVariable']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
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
                                    $cobro_tarjeta = $this->CobroTarjeta->find('first',array('conditions'=>array('reserva_cobro_id'=>$cobro['id'])));
                                    if($cobro_tarjeta['CobroTarjetaLote']['acreditado_por'] != 0){
                                        $cobrado[$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['total'];
                                        $cobroCancelado += $cobro_tarjeta['CobroTarjeta']['total'];
                                        if($reserva['Reserva']['estado'] != 2){
                                            $ventas_netas[$reserva['Reserva']['mes']] -= $cobro_tarjeta['CobroTarjeta']['descuento_lote'];
                                        }
                                        if($cobro_tarjeta['CobroTarjeta']['descuento_lote']<0){
                                            echo $reserva['Reserva']['numero'].' - '.$reserva['Reserva']['check_out'].' - '.$reserva['Reserva']['noches'].' - '.$date_parts[1]."<br>";
                                        }
                                        $descuentos_tarjetas[$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['descuento_lote'];
                                        $cobro_posnet[$cobro_tarjeta['CobroTarjetaTipo']['cobro_tarjeta_posnet_id']][$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['total'];
                                        //$intereses[$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['interes'];
                                    }else{
                                        $pendiente_cobro[$reserva['Reserva']['mes']] += $cobro_tarjeta['CobroTarjeta']['total'];
                                    }
                                    break;

                                case 'EFECTIVO':
                                    $cobro_efectivo = $this->CobroEfectivo->find('first',array('conditions'=>array('reserva_cobro_id'=>$cobro['id'])));
                                    $caja_visible=$this->Caja->findById($cobro_efectivo['CobroEfectivo']['caja_id']);
                                    if ($caja_visible['Caja']['visible_en_informe']) {

                                        $cobro_caja[$cobro_efectivo['CobroEfectivo']['caja_id']][$reserva['Reserva']['mes']] += $cobro_efectivo['CobroEfectivo']['monto_neto'];
                                        $cobrado[$reserva['Reserva']['mes']] += $cobro_efectivo['CobroEfectivo']['monto_neto'];
                                        $cobroCancelado += $cobro_efectivo['CobroEfectivo']['monto_neto'];
                                    }

                                    break;

                                case 'TRANSFERENCIA':
                                    $cobro_transferencia = $this->CobroTransferencia->find('first',array('conditions'=>array('reserva_cobro_id'=>$cobro['id'])));
                                    if($cobro_transferencia['CobroTransferencia']['acreditado']){
                                        $cuenta_visible=$this->Cuenta->findById($cobro_transferencia['CobroTransferencia']['cuenta_id']);
                                        if ($cuenta_visible['Cuenta']['visible_en_informe']) {
                                                $cobro_cuenta[$cobro_transferencia['CobroTransferencia']['cuenta_id']][$reserva['Reserva']['mes']] += $cobro_transferencia['CobroTransferencia']['total'];
                                                $cobrado[$reserva['Reserva']['mes']] += $cobro_transferencia['CobroTransferencia']['total'];
                                                $cobroCancelado += $cobro_transferencia['CobroTransferencia']['total'];
                                                //$intereses[$reserva['Reserva']['mes']] += $cobro_transferencia['CobroTransferencia']['interes'];
                                        }
                                    }else{
                                        $pendiente_cobro[$reserva['Reserva']['mes']] += $cobro_transferencia['CobroTransferencia']['total'];
                                    }
                                    break;

                                case 'CHEQUE':
                                            $cobro_cheque = $this->CobroCheque->find('first',array('conditions'=>array('reserva_cobro_id'=>$cobro['id'])));
                                            if($cobro_cheque['CobroCheque']['acreditado'] or $cobro_cheque['CobroCheque']['asociado_a_pagos']){
                                                $cobro_cheque[$cobro_cheque['CobroCheque']['tipo']][$reserva['Reserva']['mes']] += $cobro_cheque['CobroCheque']['monto_neto'];
                                                $cobrado[$reserva['Reserva']['mes']] += $cobro_cheque['CobroCheque']['total'];
                                                $cobroCancelado += $cobro_cheque['CobroCheque']['total'];
                                                //$intereses[$reserva['Reserva']['mes']] += $cobro_cheque['CobroCheque']['interes'];
                                            }else{
                                                $pendiente_cobro[$reserva['Reserva']['mes']] += $cobro_cheque['CobroCheque']['total'];
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
                }
                if(($reserva['Reserva']['estado'] != 2)&&($reserva['Reserva']['estado'] != 3)){
                	$date_parts = explode("/",$reserva['Reserva']['check_out']);

	                if ($date_parts[2]==$ano) {

	                	//$capacidad_ocupada_depto[intval($date_parts[1])]++  ;
	                	//$capacidad_ocupada[intval($date_parts[1])]  += ($reserva['Apartamento']['excluir']==0)?($reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'] + $reserva['Reserva']['pax_bebes']):0;
	                	$capacidad_ocupada[intval($date_parts[1])]  += ($reserva['Apartamento']['excluir']==0)?1:0;
	                }

	        		$yy=$date_parts[2];
	        		$mm=$date_parts[1];
	        		$dd=ltrim($date_parts[0], '0');
	        		$nuevafecha = $yy.'-'.$mm.'-'.$dd;
                	$j++;

                	for ($i = 1; $i < $reserva['Reserva']['noches']; $i++) {
							$nuevafecha = strtotime ( '-1 day' , strtotime ( $nuevafecha ) ) ;
							$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
							$j++;
							$date_parts = explode("-",$nuevafecha);
							//echo $reserva['Reserva']['numero'].' - '.$reserva['Reserva']['check_out'].' - '.$reserva['Reserva']['noches'].' - '.$date_parts[1]."<br>";
		                	if ($date_parts[0]==$ano) {
			                	//$capacidad_ocupada_depto[intval($date_parts[1])]++  ;
			                	//$capacidad_ocupada[intval($date_parts[1])]  += ($reserva['Apartamento']['excluir']==0)?($reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'] + $reserva['Reserva']['pax_bebes']):0;
			                	$capacidad_ocupada[intval($date_parts[1])]  += ($reserva['Apartamento']['excluir']==0)?1:0;
			                }
                	}
                	//$capacidad_ocupada[$reserva['Reserva']['mes']]  += ($reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'] + $reserva['Reserva']['pax_bebes']) * $reserva['Reserva']['noches'];
                }
                if($ano1==$ano) {
                    if ($reserva['Reserva']['estado'] == 2) {
                        $alojamientos[$reserva['Reserva']['mes']] += $cobroCancelado;
                        $ventas_netas[$reserva['Reserva']['mes']] += $cobroCancelado;
                    }
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

        /*$apartamentos = $this->Apartamento->find('all');
        $q_apartamentos = count($apartamentos);
        $capacidad_total = 0;
        foreach($apartamentos as $apartamento){
            $capacidad_total += ($apartamento['Apartamento']['excluir']==0)? 30:0;
        }
        //$capacidad_total = $capacidad_total * 30;*/
        $condicion=array($condicionApartamento,$condicionCategoria,array('excluir'=>0));


        $apartamentos = $this->Apartamento->find('all',array('conditions'=>$condicion));
        $q_apartamentos = count($apartamentos);

        $capacidad_total[1] = ($q_apartamentos) * 31;
        $capacidad_total[2] = ($q_apartamentos) * 28;
        $capacidad_total[3] = ($q_apartamentos) * 31;
        $capacidad_total[4] = ($q_apartamentos) * 30;
        $capacidad_total[5] = ($q_apartamentos) * 31;
        $capacidad_total[6] = ($q_apartamentos) * 30;
        $capacidad_total[7] = ($q_apartamentos) * 31;
        $capacidad_total[8] = ($q_apartamentos) * 31;
        $capacidad_total[9] = ($q_apartamentos) * 30;
        $capacidad_total[10] = ($q_apartamentos) * 31;
        $capacidad_total[11] = ($q_apartamentos) * 30;
        $capacidad_total[12] = ($q_apartamentos) * 31;


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

	function ventas_extras($ano,$desde,$hasta){
        ini_set( "memory_limit", "-1" );
        ini_set('max_execution_time', "-1");
        //error_reporting(0);

        $this->layout = 'ajax';

        $this->loadModel('Reserva');

        $this->loadModel('ExtraRubro');

        $meses = array(1=>'Enero', 2=> 'Febrero', 3=> 'Marzo', 4=> 'Abril', 5=> 'Mayo', 6=> 'Junio', 7=> 'Julio', 8=> 'Agosto', 9=> 'Septiembre', 10=>'Octubre', 11=> 'Noviembre', 12=>'Diciembre');
        $this->set('meses',$meses);

        if (($desde!='undefined-undefined-')&&($hasta!='undefined-undefined-')) {
        	$reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'creado >=' => $desde, 'creado <=' => $hasta), 'recursive' => 1));
        }
        else
        	$reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano), 'recursive' => 1));
        //$reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'MONTH(check_out)' => 3), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('check_out <=' => '2014-02-28', 'check_out >=' => '2014-02-01'), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('check_out' => '2014-03-31'), 'recursive' => 2));

        for($i=1; $i<=12; $i++){

            $adelantadas[$i] = 0;
            $no_adelantadas[$i] = 0;

        }

        $extra_rubros = $this->ExtraRubro->find('list');
        $this->set('extra_rubros',$extra_rubros);
        foreach($extra_rubros as $id => $rubro){
            for($i=1; $i<=12; $i++){
                $adelantadas_rubro[$id][$i] = 0;
                $no_adelantadas_rubro[$id][$i] = 0;
            }
        }





      $this->loadModel('Extra');
        $this->loadModel('ExtraVariable');


        if(count($reservas) > 0){

            foreach($reservas as $reserva){

                //verifico que la reserva no este cancelada
                if(($reserva['Reserva']['estado'] != 2)&&($reserva['Reserva']['estado'] != 3)){


                    if(count($reserva['ReservaExtra']>0)){
                        foreach($reserva['ReservaExtra'] as $extra){

                        	$extraRubro=$this->Extra->findById($extra['extra_id']);
                        	$extraVariable=$this->ExtraVariable->findById($extra['extra_variable_id']);
                            if($extra['adelantada'] == 1){
                                if($extra['extra_id']){
                                    $adelantadas_rubro[$extraRubro['Extra']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }elseif($extra['extra_variable_id']){
                                    $adelantadas_rubro[$extraVariable['ExtraVariable']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }
                                $adelantadas[$reserva['Reserva']['mes']]  += $extra['cantidad'] * $extra['precio'];

                            }else{
                                if($extra['extra_id']){
                                    $no_adelantadas_rubro[$extraRubro['Extra']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }elseif($extra['extra_variable_id']){
                                    $no_adelantadas_rubro[$extraVariable['ExtraVariable']['extra_rubro_id']][$reserva['Reserva']['mes']] += $extra['cantidad'] * $extra['precio'];
                                }
                                $no_adelantadas[$reserva['Reserva']['mes']]  += $extra['cantidad'] * $extra['precio'];

                            }
                        }
                    }
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


        //$capacidad_total = $capacidad_total * 30;

        $this->loadModel('Usuario');
		 $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));

		$permisoAdelantada=1;
		$permisoNoAdelantada=1;
		//print_r($user);
        if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $permisoAdelantada=0;
			$permisoNoAdelantada=0;
	    	foreach($permisos as $permiso){
               if ($permiso['UsuarioPermiso']['permiso_id']==123) {
               		$permisoAdelantada=1;
               		continue;
               }
    			if ($permiso['UsuarioPermiso']['permiso_id']==124) {
               		$permisoNoAdelantada=1;
               		continue;
               }

	        }
        }

      $this->set('permisoAdelantada',$permisoAdelantada);
       $this->set('permisoNoAdelantada',$permisoNoAdelantada);

        $this->set(array(

            'no_adelantadas' => $no_adelantadas,
            'adelantadas' => $adelantadas,

            'adelantadas_rubro' => $adelantadas_rubro,
            'no_adelantadas_rubro' => $no_adelantadas_rubro,

        ));

    }

	function ventas_extras_listado($mes,$ano,$tipo,$extra_rubro,$extra_subrubro,$mes_carga){
        ini_set( "memory_limit", "-1" );
        ini_set('max_execution_time', "-1");
        //error_reporting(0);

        $this->layout = 'ajax';

        $this->loadModel('Reserva');

       	//$reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'MONTH(check_out)' => $mes), 'order' => 'check_out asc'));
		
		if ($mes!='Seleccionar...'){
            $reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'MONTH(check_out)' => $mes), 'order' => 'check_out asc'));
        }
        else{
			
            /*$reservas = $this->Reserva->find('all',array('joins' => array(
                array(
                    'table' => 'reserva_extras',
                    'alias' => 'ReservaExtras',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Reserva.id = ReservaExtras.reserva_id'
                    )
                )



            ),'order' => 'ReservaExtras.agregada asc', 'conditions' => array('YEAR(ReservaExtras.agregada)' => $ano, 'MONTH(ReservaExtras.agregada)' => $mes_carga)));*/
			$reservas = $this->Reserva->find('all', array(
				'fields' => array('DISTINCT Reserva.id', 'Reserva.*','Cliente.*','Apartamento.*'), // Trae solo reservas únicas
				'joins' => array(
					array(
						'table' => 'reserva_extras',
						'alias' => 'ReservaExtras',
						'type' => 'LEFT',
						'conditions' => array(
							'Reserva.id = ReservaExtras.reserva_id'
						)
					)
				),
				'order' => 'ReservaExtras.agregada asc',
				'conditions' => array(
					'YEAR(ReservaExtras.agregada)' => $ano,
					'MONTH(ReservaExtras.agregada)' => $mes_carga
				)
			));

			/*App::uses('ConnectionManager', 'Model');
                 $dbo = ConnectionManager::getDatasource('default');
                 $logs = $dbo->getLog();
                 $lastLog = $logs['log'][0];
                 echo $lastLog['query'];*/
        }

        $this->loadModel('Usuario');
		 $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));

		$permisoAdelantada=1;
		$permisoNoAdelantada=1;
		//print_r($user);
        if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $permisoAdelantada=0;
			$permisoNoAdelantada=0;
	    	foreach($permisos as $permiso){
               if ($permiso['UsuarioPermiso']['permiso_id']==123) {
               		$permisoAdelantada=1;
               		continue;
               }
    			if ($permiso['UsuarioPermiso']['permiso_id']==124) {
               		$permisoNoAdelantada=1;
               		continue;
               }

	        }
        }

      $this->set('permisoAdelantada',$permisoAdelantada);
       $this->set('permisoNoAdelantada',$permisoNoAdelantada);


        $reservasMostrar = array();





        $this->loadModel('ExtraRubro');
        $this->loadModel('ExtraSubrubro');
        $this->loadModel('Extra');
        $this->loadModel('ExtraVariable');
        if(count($reservas) > 0){

            foreach($reservas as $reserva){

                //verifico que la reserva no este cancelada
                if(($reserva['Reserva']['estado'] != 2)&&($reserva['Reserva']['estado'] != 3)){


                    if(count($reserva['ReservaExtra']>0)){

                        foreach($reserva['ReservaExtra'] as $extra){
                        	/*print_r($extra);
	                        	echo "<br>";*/
                        	$mostrar=0;
                        	switch ($tipo) {
                        		case 1:
                        			if($extra['adelantada'] == 1){
                        				$mostrar=1;
                        			}
                        		break;
                        		case 2:
                        			if($extra['adelantada'] == 0){
                        				$mostrar=1;
                        			}
                        		break;
                        		default:
                        			$mostrar=1;
                        		break;
                        	}
                        	if(($extra['adelantada'] == 1)&&(!$permisoAdelantada)){
                        		$mostrar=0;
                        	}
                        	if(($extra['adelantada'] == 0)&&(!$permisoNoAdelantada)){
                        		$mostrar=0;
                        	}
                        	if ($mostrar) {
	                        	$filtroRubro = ($extra_rubro!='Seleccionar...')?array('Extra.extra_rubro_id'=>$extra_rubro):array(1=>1);
	                        	$filtroSubRubro = ($extra_subrubro!='Seleccionar...')?array('Extra.extra_subrubro_id'=>$extra_subrubro):array(1=>1);
	                        	if ($extra['extra_id']) {
	                        		$condicion=array(array('Extra.id'=>$extra['extra_id']),$filtroRubro,$filtroSubRubro);
	                        		$ex = $this->Extra->find('first',array('conditions' => $condicion));
	                        	}
	                        	else{
	                        		$filtroRubro = ($extra_rubro!='Seleccionar...')?array('ExtraVariable.extra_rubro_id'=>$extra_rubro):array(1=>1);
	                        		$condicion=array(array('ExtraVariable.id'=>$extra['extra_variable_id']),$filtroRubro);
	                        		$ex = $this->ExtraVariable->find('first',array('conditions' => $condicion));
	                        	}


	                        	/*print_r($ex);
	                        	echo "<br>";*/
	                        	if ($ex) {
	                        		$rubro = $this->ExtraRubro->findById($ex['Extra']['extra_rubro_id']);
		                        	$subrubro = $this->ExtraSubrubro->findById($ex['Extra']['extra_subrubro_id']);
		                        	$detalle=$ex['Extra']['detalle'];
		                        	if ($ex['ExtraVariable']) {
		                        		$rubro = $this->ExtraRubro->findById($ex['ExtraVariable']['extra_rubro_id']);
		                        		$detalle=$ex['ExtraVariable']['detalle'];
		                        	}

		                            $reservasMostrar[]=array('check_out'=>$reserva['Reserva']['check_out'],'nro_reserva'=>$reserva['Reserva']['numero'],'titular'=>$reserva['Cliente']['nombre_apellido'],'apartamento'=>$reserva['Apartamento']['apartamento'],'agregada'=>date('d/m/Y',strtotime($extra['agregada'])),'adelantada'=>($extra['adelantada'])?'SI':'NO','cantidad'=>$extra['cantidad'],'rubro'=>$rubro['ExtraRubro']['rubro'],'subrubro'=>$subrubro['ExtraSubrubro']['subrubro'],'detalle'=>$detalle,'monto'=>$extra['cantidad']*$extra['precio']);
	                        	}

                        	}

                        }
                    }
                }





	             //echo $reserva['Reserva']['mes']." - ".$ventas_netas[$reserva['Reserva']['mes']]."<br>";
            }// foreach reservas
        }// if count reservas > 0



        $this->set(array(
            'reservas' => $reservasMostrar
        ));

    }

	function ventas_ocupacion($ano,$desde,$hasta){
        ini_set( "memory_limit", "-1" );
        ini_set('max_execution_time', "-1");
        //error_reporting(0);

        $this->layout = 'ajax';

        $this->loadModel('Reserva');

        $this->loadModel('Apartamento');


        $meses = array(1=>'Enero', 2=> 'Febrero', 3=> 'Marzo', 4=> 'Abril', 5=> 'Mayo', 6=> 'Junio', 7=> 'Julio', 8=> 'Agosto', 9=> 'Septiembre', 10=>'Octubre', 11=> 'Noviembre', 12=>'Diciembre');
        $this->set('meses',$meses);

        if (($desde!='undefined-undefined-')&&($hasta!='undefined-undefined-')) {
        	$reservas = $this->Reserva->find('all',array('conditions' => array('OR' =>array(array('YEAR(check_out)' => $ano),array('YEAR(check_in)' => $ano,'YEAR(check_out)' => intval($ano+1))),'creado >=' => $desde, 'creado <=' => $hasta)));

        }
        else
        	$reservas = $this->Reserva->find('all',array('conditions' => array('OR' =>array(array('YEAR(check_out)' => $ano),array('YEAR(check_in)' => $ano,'YEAR(check_out)' => intval($ano+1))))));
        //$reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'MONTH(check_out)' => 3), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('check_out <=' => '2014-02-28', 'check_out >=' => '2014-02-01'), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('check_out' => '2014-03-31'), 'recursive' => 2));



        if(count($reservas) > 0){

            foreach($reservas as $reserva){
            	$j=0;
            	if(($reserva['Reserva']['estado'] != 2)&&($reserva['Reserva']['estado'] != 3)){
	                $date_parts = explode("/",$reserva['Reserva']['check_out']);

	                if ($date_parts[2]==$ano) {
	                	if($reserva['Apartamento']['excluir']==0){
	                		$capacidad_ocupada_depto[intval($date_parts[1])]++  ;
	                	}
	                	$capacidad_ocupada[intval($date_parts[1])]  += ($reserva['Apartamento']['excluir']==0)?($reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'] + $reserva['Reserva']['pax_bebes']):0;
	                }

	        		$yy=$date_parts[2];
	        		$mm=$date_parts[1];
	        		$dd=ltrim($date_parts[0], '0');
	        		$nuevafecha = $yy.'-'.$mm.'-'.$dd;
                	$j++;
                	$capacidad_ocupada_depto_mes[$reserva['Reserva']['mes']] += ($reserva['Apartamento']['excluir']==0)?$reserva['Reserva']['noches']:0 ;
                	//$capacidad_ocupada[$reserva['Reserva']['mes']]  += ($reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'] + $reserva['Reserva']['pax_bebes']) * $reserva['Reserva']['noches'];
                	for ($i = 1; $i < $reserva['Reserva']['noches']; $i++) {
							$nuevafecha = strtotime ( '-1 day' , strtotime ( $nuevafecha ) ) ;
							$nuevafecha = date ( 'Y-m-j' , $nuevafecha );
							$j++;
							$date_parts = explode("-",$nuevafecha);
							//echo $reserva['Reserva']['mes'].' - '.$date_parts[1]."<br>";
		                	if ($date_parts[0]==$ano) {
			                	if($reserva['Apartamento']['excluir']==0){
		                			$capacidad_ocupada_depto[intval($date_parts[1])]++  ;
			                	}
			                	$capacidad_ocupada[intval($date_parts[1])]  += ($reserva['Apartamento']['excluir']==0)?($reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'] + $reserva['Reserva']['pax_bebes']):0;
			                }
                	}

                	//echo $reserva['Reserva']['numero'].' - '.$reserva['Apartamento']['id'].' - '.$reserva['Reserva']['check_in'].' - '.$reserva['Reserva']['check_out'].' - '.$reserva['Reserva']['noches'].' - '.$j.' - '.$reserva['Reserva']['mes'].' - '.$capacidad_ocupada_depto[1].' - '.$capacidad_ocupada_depto_mes[1]."<br>";

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

        $apartamentos = $this->Apartamento->find('all',array('conditions'=>array('excluir'=>0)));
        $q_apartamentos = count($apartamentos);
        //$capacidad_total = 0;
        foreach($apartamentos as $apartamento){
        	if($apartamento['Apartamento']['excluir']==0){
	            $capacidad_total[1] += $apartamento['Apartamento']['capacidad'] * 31;
	            $capacidad_total[2] += $apartamento['Apartamento']['capacidad'] * 28;
	            $capacidad_total[3] += $apartamento['Apartamento']['capacidad'] * 31;
	            $capacidad_total[4] += $apartamento['Apartamento']['capacidad'] * 30;
	            $capacidad_total[5] += $apartamento['Apartamento']['capacidad'] * 31;
	            $capacidad_total[6] += $apartamento['Apartamento']['capacidad'] * 30;
	            $capacidad_total[7] += $apartamento['Apartamento']['capacidad'] * 31;
	            $capacidad_total[8] += $apartamento['Apartamento']['capacidad'] * 31;
	            $capacidad_total[9] += $apartamento['Apartamento']['capacidad'] * 30;
	            $capacidad_total[10] += $apartamento['Apartamento']['capacidad'] * 31;
	            $capacidad_total[11] += $apartamento['Apartamento']['capacidad'] * 30;
	            $capacidad_total[12] += $apartamento['Apartamento']['capacidad'] * 31;
        	}

        }
        $capacidad_total_depto[1] = ($q_apartamentos) * 31;
        $capacidad_total_depto[2] = ($q_apartamentos) * 28;
        $capacidad_total_depto[3] = ($q_apartamentos) * 31;
        $capacidad_total_depto[4] = ($q_apartamentos) * 30;
        $capacidad_total_depto[5] = ($q_apartamentos) * 31;
        $capacidad_total_depto[6] = ($q_apartamentos) * 30;
        $capacidad_total_depto[7] = ($q_apartamentos) * 31;
        $capacidad_total_depto[8] = ($q_apartamentos) * 31;
        $capacidad_total_depto[9] = ($q_apartamentos) * 30;
        $capacidad_total_depto[10] = ($q_apartamentos) * 31;
        $capacidad_total_depto[11] = ($q_apartamentos) * 30;
        $capacidad_total_depto[12] = ($q_apartamentos) * 31;

        $this->set(array(

            'capacidad_total' => $capacidad_total,
            'capacidad_ocupada' => $capacidad_ocupada,
        	'capacidad_total_depto' => $capacidad_total_depto,
            'capacidad_ocupada_depto' => $capacidad_ocupada_depto,
            'q_apartamentos' => $q_apartamentos

        ));

    }

    function ventas_financiero($ano,$desde,$hasta){
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

        if (($desde!='undefined-undefined-')&&($hasta!='undefined-undefined-')) {
        	$date_parts = explode("-",$desde);

        	$ano=$date_parts[0];
        	$cobro_efectivos = $this->CobroEfectivo->find('all',array('conditions' => array('ReservaCobro.fecha >=' => $desde, 'ReservaCobro.fecha <=' => $hasta), 'recursive' => 2));
        }
        else
        	$cobro_efectivos = $this->CobroEfectivo->find('all',array('conditions' => array('YEAR(ReservaCobro.fecha)' => $ano)));
        if(count($cobro_efectivos)>0){
            foreach($cobro_efectivos as $ce){
            	$caja_visible=$this->Caja->findById($ce['CobroEfectivo']['caja_id']);
                if ($caja_visible['Caja']['visible_en_informe']) {
                	$cobro_neto[$ce['ReservaCobro']['mes']] += $ce['ReservaCobro']['monto_cobrado'];
                	$cobro_caja[$ce['CobroEfectivo']['caja_id']][$ce['ReservaCobro']['mes']] += $ce['CobroEfectivo']['monto_neto'];
                }
            }
        }

        //busco los cobros con cheques del ano
        for($i=1; $i<=12; $i++){
            $cobro_cheque['COMUN'][$i] = 0;
            $cobro_cheque['DIFERIDO'][$i] = 0;
        }

        if (($desde!='undefined-undefined-')&&($hasta!='undefined-undefined-')) {

        	$cobro_cheques = $this->CobroCheque->find('all',array('conditions' => array('OR' => array(array('fecha_acreditado >=' => $desde, 'fecha_acreditado <=' => $hasta), array('asociado_a_pagos_fecha >=' => $desde, 'asociado_a_pagos_fecha <=' => $hasta)))));
        }
        else
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

        if (($desde!='undefined-undefined-')&&($hasta!='undefined-undefined-')) {
        	$cobro_transferencias = $this->CobroTransferencia->find('all',array('conditions' => array('fecha_acreditado >=' => $desde, 'fecha_acreditado <=' => $hasta), 'recursive' => 2));

        }
        else
        	$cobro_transferencias = $this->CobroTransferencia->find('all',array('conditions' => array('YEAR(fecha_acreditado)' => $ano)));
        if(count($cobro_transferencias) > 0){
            foreach($cobro_transferencias as $ct){
            	$cuenta_visible=$this->Cuenta->findById($ct['CobroTransferencia']['cuenta_id']);
                if ($cuenta_visible['Cuenta']['visible_en_informe']) {
            		$cobro_neto[$ct['CobroTransferencia']['mes_acreditado']] += $ct['CobroTransferencia']['total'];
                	$cobro_cuenta[$ct['CobroTransferencia']['cuenta_id']][$ct['CobroTransferencia']['mes_acreditado']] += $ct['CobroTransferencia']['total'];
                }
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

        if (($desde!='undefined-undefined-')&&($hasta!='undefined-undefined-')) {
        	$cobro_tarjeta_lotes = $this->CobroTarjetaLote->find('all',array('conditions' => array('fecha_acreditacion >=' => $desde, 'fecha_acreditacion <=' => $hasta), 'recursive' => 2));

        }
        else
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

        if (($desde!='undefined-undefined-')&&($hasta!='undefined-undefined-')) {
        	$devoluciones = $this->ReservaDevolucion->find('all',array('conditions' => array('fecha >=' => $desde, 'fecha <=' => $hasta), 'recursive' => 2));

        }
        else
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
		ini_set( "memory_limit", "-1" );
		ini_set('max_execution_time', "-1");
        error_reporting(0);
        //echo $mes.$ano;
		$this->layout = 'ajax';

        $this->loadModel('Reserva');
        $this->loadModel('CobroTarjeta');
		
        $cobrado = array();
        $pendiente_cobro = 0;
        $ventas_netas = 0;

        $meses = array('01'=>'Enero', '02'=> 'Febrero','03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', 10=>'Octubre', 11=> 'Noviembre', 12=>'Diciembre');
        $this->set('meses',$meses);
		//print_r($meses);
        $reservas = $this->Reserva->find('all',array('conditions' => array('YEAR(check_out)' => $ano, 'MONTH(check_out)' => $mes), 'recursive' => 2));
		
        //$reservas = $this->Reserva->find('all',array('conditions' => array('check_out <=' => '2014-03-31', 'check_out >=' => '2014-03-01'), 'recursive' => 2));
        //$reservas = $this->Reserva->find('all',array('conditions' => array('check_out' => '2014-03-31'), 'recursive' => 2));
		//$reservas = $this->Reserva->find('all',array('conditions' => array('numero' => '394'), 'recursive' => 2));
		//print_r($reservas);
        if(count($reservas) > 0){

            foreach($reservas as $reserva){
            	$veAux = 0;
                if(($reserva['Reserva']['estado'] != 2)&&($reserva['Reserva']['estado'] != 3)){
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

function iva_compras($mes,$ano, $orden){
         //error_reporting(0);
        $this->layout = 'ajax';

        $this->loadModel('Gasto');
       $this->loadModel('CondicionImpositiva');
       $this->loadModel('JurisdiccionInscripcion');


        //$gastos = $this->Gasto->find('all',array('conditions' => array('YEAR(fecha_vencimiento)' => $ano, 'MONTH(fecha_vencimiento)' => $mes, factura_tipo => array('A','M')), 'order' => $orden.' asc', 'recursive' => 1));
        $gastos = $this->Gasto->find('all',array('conditions' => array('YEAR(fecha_vencimiento)' => $ano, 'MONTH(fecha_vencimiento)' => $mes, factura_orden => 'B'), 'order' => $orden.' asc', 'recursive' => 1));

         $gastosMostrar = array();
        if(count($gastos) > 0){

            foreach($gastos as $gasto){

            	$facturaOrden = ($gasto['Gasto']['factura_orden']=='B')?'0001':'0002';
             	$factura = $gasto['Gasto']['factura_tipo'].$gasto['Gasto']['factura_punto_venta'].$gasto['Gasto']['factura_nro'];
	            $condicionImpositiva='';
	            $jurisdiccionInscripcion='';
             	if(isset($gasto['Proveedor']['id'])){
	                $proveedor = $gasto['Proveedor']['nombre'];
	                $condicion=$this->CondicionImpositiva->findById($gasto['Proveedor']['condicion_impositiva_id']);
	                $condicionImpositiva=$condicion['CondicionImpositiva']['nombre'];
	                $jurisdiccion=$this->JurisdiccionInscripcion->findById($gasto['Proveedor']['jurisdiccion_inscripcion_id']);
	                $jurisdiccionInscripcion=$jurisdiccion['JurisdiccionInscripcion']['nombre'];
	                $cuit=$gasto['Proveedor']['cuit'];
	                $razon=$gasto['Proveedor']['razon'];
	            }else{
	                $proveedor = $gasto['Gasto']['proveedor'];
	                $cuit=$gasto['Gasto']['cuit'];
	                $razon=$gasto['Gasto']['razon'];
	                $this->loadModel('Proveedor');
	                $prov = $this->Proveedor->find('first',array('conditions'=>array('Proveedor.cuit'=>$cuit)));
	                $condicion=$this->CondicionImpositiva->findById($prov['Proveedor']['condicion_impositiva_id']);
	                $condicionImpositiva=$condicion['CondicionImpositiva']['nombre'];
	                $jurisdiccion=$this->JurisdiccionInscripcion->findById($prov['Proveedor']['jurisdiccion_inscripcion_id']);
	                $jurisdiccionInscripcion=$jurisdiccion['JurisdiccionInscripcion']['nombre'];

	            }

	            $date_parts = explode("/",$gasto['Gasto']['fecha_vencimiento']);

        		$yy=$date_parts[2];
        		$mm=$date_parts[1];
        		$dd=$date_parts[0];
        		$nuevafecha = $yy.$mm.$dd;
            	$gastosMostrar[]=array('fecha'=>$nuevafecha,'fechaMostrar'=>$gasto['Gasto']['fecha_vencimiento'],'origen'=>$gasto['Gasto']['origen'],'factura'=>$factura,'proveedor'=>$proveedor,'razon'=>$razon,'cuit'=>$cuit,'condicionImpositiva'=>$condicionImpositiva,'jurisdiccionInscripcion'=>$jurisdiccionInscripcion,'iva_27'=>$gasto['Gasto']['iva_27'],'iva_21'=>$gasto['Gasto']['iva_21'],'iva_10_5'=>$gasto['Gasto']['iva_10_5'],'otra_alicuota'=>$gasto['Gasto']['otra_alicuota'],'perc_iva'=>$gasto['Gasto']['perc_iva'],'perc_iibb_bsas'=>$gasto['Gasto']['perc_iibb_bsas'],'perc_iibb_caba'=>$gasto['Gasto']['perc_iibb_caba'],'exento'=>$gasto['Gasto']['exento'],'monto'=>$gasto['Gasto']['monto']);
            }
        }
        $this->array_sort_by($gastosMostrar, $orden);
        $this->set(array(
            'gastos' => $gastosMostrar
        ));
    }

	function exportarIvaCompra($mes,$ano, $orden){
         //error_reporting(0);
        $this->layout = 'ajax';

        $this->loadModel('Gasto');
        $this->loadModel('CondicionImpositiva');
       $this->loadModel('JurisdiccionInscripcion');

        //$gastos = $this->Gasto->find('all',array('conditions' => array('YEAR(fecha_vencimiento)' => $ano, 'MONTH(fecha_vencimiento)' => $mes, factura_tipo => array('A','M')), 'order' => $orden.' asc', 'recursive' => 1));
        $gastos = $this->Gasto->find('all',array('conditions' => array('YEAR(fecha_vencimiento)' => $ano, 'MONTH(fecha_vencimiento)' => $mes, factura_orden => 'B'), 'order' => $orden.' asc', 'recursive' => 1));
    	$this->autoRender = false;
  		$this->layout = false;


		$fileName = "Iva_Compras_".$mes.'_'.$ano.".xls";
		//$fileName = "bookreport_".date("d-m-y:h:s").".csv";
		$headerRow = array("Fecha comprobante","As.Tipo","Factura","Proveedor","Tercero","CUIT","Cond.","Jurisd.","Neto","IVA 10.5%","IVA 21%","IVA 27%","Otra al�cuota","Percepci�n IVA","Perc. IIBB Bs.As.","Perc. IIBB CABA","Exento","Total Factura");

		$data = array();
		$total27=0;
	    $total21=0;
	    $total10_5=0;
	    $totalOtraAlicuota=0;
	    $totalperc_iva=0;
	    $totalperc_iibb_bsas=0;
	    $totalperc_iibb_caba=0;
	    $totalexento=0;
	    $totalMonto=0;
	    $creditoFiscal = 0;
	     $gastosMostrar = array();
	    foreach($gastos as $gasto){
             $total27 +=$gasto['Gasto']['iva_27'];
             $total21 +=$gasto['Gasto']['iva_21'];
             $total10_5 +=$gasto['Gasto']['iva_10_5'];
             $totalOtraAlicuota +=$gasto['Gasto']['otra_alicuota'];
             $totalperc_iva +=$gasto['Gasto']['perc_iva'];
             $totalperc_iibb_bsas +=$gasto['Gasto']['perc_iibb_bsas'];
             $totalperc_iibb_caba +=$gasto['Gasto']['perc_iibb_caba'];
             $totalexento +=$gasto['Gasto']['exento'];
             $totalMonto +=$gasto['Gasto']['monto'];
             $facturaOrden = ($gasto['Gasto']['factura_orden']=='B')?'0001':'0002';
             $factura = $gasto['Gasto']['factura_tipo'].$gasto['Gasto']['factura_punto_venta'].$gasto['Gasto']['factura_nro'];
	         $creditoFiscal +=$gasto['Gasto']['monto']-$gasto['Gasto']['iva_27']-$gasto['Gasto']['iva_21']-$gasto['Gasto']['iva_10_5']-$gasto['Gasto']['otra_alicuota']-$gasto['Gasto']['perc_iva']-$gasto['Gasto']['perc_iibb_bsas']-$gasto['Gasto']['perc_iibb_caba']-$gasto['Gasto']['exento'];


	    	 $condicionImpositiva='';
	            $jurisdiccionInscripcion='';
             	if(isset($gasto['Proveedor']['id'])){
	                $proveedor = $gasto['Proveedor']['nombre'];
	                $condicion=$this->CondicionImpositiva->findById($gasto['Proveedor']['condicion_impositiva_id']);
	                $condicionImpositiva=$condicion['CondicionImpositiva']['nombre'];
	                $jurisdiccion=$this->JurisdiccionInscripcion->findById($gasto['Proveedor']['jurisdiccion_inscripcion_id']);
	                $jurisdiccionInscripcion=$jurisdiccion['JurisdiccionInscripcion']['nombre'];
	                $cuit=$gasto['Proveedor']['cuit'];
	                $razon=$gasto['Proveedor']['razon'];
	            }else{

                	$proveedor = $gasto['Gasto']['proveedor'];

	                $cuit=$gasto['Gasto']['cuit'];
	                $razon=$gasto['Gasto']['razon'];
	                $this->loadModel('Proveedor');
	                $prov = $this->Proveedor->find('first',array('conditions'=>array('Proveedor.cuit'=>$cuit)));
	                $condicion=$this->CondicionImpositiva->findById($prov['Proveedor']['condicion_impositiva_id']);
	                $condicionImpositiva=$condicion['CondicionImpositiva']['nombre'];
	                $jurisdiccion=$this->JurisdiccionInscripcion->findById($prov['Proveedor']['jurisdiccion_inscripcion_id']);
	                $jurisdiccionInscripcion=$jurisdiccion['JurisdiccionInscripcion']['nombre'];

            }
            $date_parts = explode("/",$gasto['Gasto']['fecha_vencimiento']);
            $yy=$date_parts[2];
        	$mm=$date_parts[1];
        	$dd=$date_parts[0];
        	$nuevafecha = $yy.$mm.$dd;
            $gastosMostrar[]=array('fecha'=>$nuevafecha,'fechaMostrar'=>$gasto['Gasto']['fecha_vencimiento'],'origen'=>$gasto['Gasto']['origen'],'factura'=>$factura,'proveedor'=>$proveedor,'razon'=>$razon,'cuit'=>$cuit,'condicionImpositiva'=>$condicionImpositiva,'jurisdiccionInscripcion'=>$jurisdiccionInscripcion,'iva_27'=>$gasto['Gasto']['iva_27'],'iva_21'=>$gasto['Gasto']['iva_21'],'iva_10_5'=>$gasto['Gasto']['iva_10_5'],'otra_alicuota'=>$gasto['Gasto']['otra_alicuota'],'perc_iva'=>$gasto['Gasto']['perc_iva'],'perc_iibb_bsas'=>$gasto['Gasto']['perc_iibb_bsas'],'perc_iibb_caba'=>$gasto['Gasto']['perc_iibb_caba'],'exento'=>$gasto['Gasto']['exento'],'monto'=>$gasto['Gasto']['monto']);

		}
		$this->array_sort_by($gastosMostrar, $orden);
		foreach($gastosMostrar as $gasto){

			$data[] = array($gasto['fechaMostrar'], $gasto['origen'], $gasto['factura'],$gasto['proveedor'],$gasto['razon'],$gasto['cuit'],$gasto['condicionImpositiva'],$gasto['jurisdiccionInscripcion'],str_replace('.',',',$gasto['monto']-$gasto['iva_10_5']-$gasto['iva_21']-$gasto['iva_27']-$gasto['otra_alicuota']-$gasto['perc_iva']-$gasto['perc_iibb_bsas']-$gasto['perc_iibb_caba']-$gasto['exento']),str_replace('.',',',$gasto['iva_10_5']), str_replace('.',',',$gasto['iva_21']),str_replace('.',',',$gasto['iva_27']), str_replace('.',',',$gasto['otra_alicuota']), str_replace('.',',',$gasto['perc_iva']), str_replace('.',',',$gasto['perc_iibb_bsas']), str_replace('.',',',$gasto['perc_iibb_caba']), str_replace('.',',',$gasto['exento']), str_replace('.',',',$gasto['monto']));
		}
		$data[] = array("","","","","","","","","","","","","","","","","","");
		$data[] = array("","","","","","","","","","","","","","","","","","");
		//$data[] = array("", "Total Cr�dito fiscal",$creditoFiscal,str_replace('.',',',$total27), str_replace('.',',',$total21),str_replace('.',',',$total10_5), str_replace('.',',',$totalOtraAlicuota),str_replace('.',',',$totalMonto));
		$data[] = array("", "", "", "", "", "", "", "",$creditoFiscal,str_replace('.',',',$total10_5), str_replace('.',',',$total21),str_replace('.',',',$total27), str_replace('.',',',$totalOtraAlicuota), str_replace('.',',',$totalperc_iva), str_replace('.',',',$totalperc_iibb_bsas), str_replace('.',',',$totalperc_iibb_caba), str_replace('.',',',$totalexento),str_replace('.',',',$totalMonto));

		$this->ExportXls->export($fileName, $headerRow, $data);
    }

    function iva_ventas($mes = null, $ano = null, $orden = null, $tipoDoc = null, $tipo = null, $puntoVenta = null, $buscar = '')
    {
        $this->layout = 'ajax';
        $this->loadModel('ReservaFactura');

        $condicionTipoDoc = array();
        if ($tipoDoc != 'Seleccionar...' && $tipoDoc != null) {
            $condicionTipoDoc = array('ReservaFactura.tipoDoc ' => $tipoDoc);
        }

        $condicionTipo = array();
        if ($tipo != 'Seleccionar...' && $tipo != null) {
            $condicionTipo = array('ReservaFactura.tipo ' => $tipo);
        }

        $condicionPuntoVenta = array();
        if ($puntoVenta != 'Seleccionar...' && $puntoVenta != null) {
            $condicionPuntoVenta = array('ReservaFactura.punto_venta_id ' => $puntoVenta);
        }

        $condicionBuscar = array();
        if ($buscar != '' && $buscar != null && $buscar != '-') {
            $condicionBuscar = array('or' => array(
                'Reserva.numero LIKE ' => '%' . $buscar . '%',
                'ReservaFactura.titular LIKE ' => '%' . $buscar . '%',
                'ReservaFactura.numero LIKE ' => '%' . $buscar . '%',
                'ReservaFactura.monto LIKE ' => '%' . $buscar . '%',
                'ReservaFactura.fecha_emision LIKE ' => '%' . $buscar . '%',
            ));
        }

        $reservas = $this->ReservaFactura->find('all', array(
            'conditions' => array(
                'YEAR(fecha_emision)' => $ano,
                'MONTH(fecha_emision)' => $mes,
                'ivaVentas' => 1,
                $condicionTipoDoc,
                $condicionTipo,
                $condicionPuntoVenta,
                $condicionBuscar
            ),
            'order' => $orden . ' asc',
            'recursive' => 1
        ));

         $reservasMostrar = array();
        if(count($reservas) > 0){

            foreach($reservas as $reserva){
            	//print_r($reserva);
            	$tipo = ($reserva['ReservaFactura']['tipoDoc']==1)?'Factura':'Nota de credito';
            	$puntoVenta = ($reserva['ReservaFactura']['punto_venta_id'])?$reserva['PuntoVenta']['numero']:'';
             	$factura = $tipo.'-'.$reserva['ReservaFactura']['tipo'].'-'.$puntoVenta.'-'.str_pad($reserva['ReservaFactura']['numero'], 6,0,STR_PAD_LEFT);


	            $date_parts = explode("/",$reserva['ReservaFactura']['fecha_emision']);

        		$yy=$date_parts[2];
        		$mm=$date_parts[1];
        		$dd=$date_parts[0];
        		$nuevafecha = $yy.$mm.$dd;
        		$iva =($reserva['PuntoVenta']['alicuota'])?$reserva['ReservaFactura']['monto']-($reserva['ReservaFactura']['monto']/(1+$reserva['PuntoVenta']['alicuota'])):0;
            	$reservasMostrar[]=array('fecha'=>$nuevafecha,'fechaMostrar'=>$reserva['ReservaFactura']['fecha_emision'],'factura'=>$factura,'titular'=>$reserva['ReservaFactura']['titular'],'nroReserva'=>$reserva['Reserva']['numero'],'iva_21'=>$iva,'monto'=>$reserva['ReservaFactura']['monto']);
            }
        }
        $this->array_sort_by($reservasMostrar, $orden);
        $this->set(array(
            'reservas' => $reservasMostrar
        ));
    }

    function exportarIvaVenta($mes = null, $ano = null, $orden = null, $tipoDoc = null, $tipo = null, $puntoVenta = null, $buscar = '')
    {
         //error_reporting(0);
        $this->layout = 'ajax';

       $this->loadModel('ReservaFactura');

         $condicionTipoDoc = array();

    	if ($tipoDoc!='Seleccionar...') {

        	$condicionTipoDoc = array('ReservaFactura.tipoDoc ' => $tipoDoc);
        }

		$condicionTipo = array();

    	if ($tipo!='Seleccionar...') {

        	$condicionTipo = array('ReservaFactura.tipo ' => $tipo);
        }

		$condicionPuntoVenta = array();

    	if ($puntoVenta!='Seleccionar...') {

        	$condicionPuntoVenta = array('ReservaFactura.punto_venta_id ' => $puntoVenta);
        }

		$condicionBuscar = array();

    	if ($buscar!='') {

        	$condicionBuscar=array('or' =>
	        	  array('Reserva.numero LIKE '=>'%'.$buscar.'%', 'ReservaFactura.titular LIKE '=>'%'.$buscar.'%', 'ReservaFactura.numero LIKE '=>'%'.$buscar.'%', 'ReservaFactura.monto LIKE '=>'%'.$buscar.'%', 'ReservaFactura.fecha_emision LIKE '=>'%'.($buscar).'%',
			    ));
        }






        $reservas = $this->ReservaFactura->find('all',array('conditions' => array('YEAR(fecha_emision)' => $ano, 'MONTH(fecha_emision)' => $mes, ivaVentas => 1,$condicionTipoDoc,$condicionTipo,$condicionPuntoVenta,$condicionBuscar), 'order' => $orden.' asc', 'recursive' => 1));
      $this->autoRender = false;
  		$this->layout = false;


		$fileName = "Iva_Ventas_".$mes.'_'.$ano.".xls";
		//$fileName = "bookreport_".date("d-m-y:h:s").".csv";
		$headerRow = array("Fecha comprobante","Factura/N. de credito","Titular","IVA","Monto bruto");

		$data = array();

	    $total21=0;

	     $gastosMostrar = array();
	    foreach($reservas as $reserva){
             $iva =($reserva['PuntoVenta']['alicuota'])?$reserva['ReservaFactura']['monto']-($reserva['ReservaFactura']['monto']/(1+$reserva['PuntoVenta']['alicuota'])):0;
             $total21 +=$iva;
             $totalMonto +=$reserva['ReservaFactura']['monto'];
            $puntoVenta = ($reserva['ReservaFactura']['punto_venta_id'])?$reserva['PuntoVenta']['numero']:'';
            $tipo = ($reserva['ReservaFactura']['tipoDoc']==1)?'Factura':'Nota de credito';
             	$factura = $tipo.'-'.$reserva['ReservaFactura']['tipo'].'-'.$puntoVenta.'-'.str_pad($reserva['ReservaFactura']['numero'], 6,0,STR_PAD_LEFT);


	            $date_parts = explode("/",$reserva['ReservaFactura']['fecha_emision']);

        		$yy=$date_parts[2];
        		$mm=$date_parts[1];
        		$dd=$date_parts[0];
        		$nuevafecha = $yy.$mm.$dd;

            $gastosMostrar[]=array('fecha'=>$nuevafecha,'fechaMostrar'=>$reserva['ReservaFactura']['fecha_emision'],'factura'=>$factura,'titular'=>$reserva['ReservaFactura']['titular'],'iva_21'=>$iva,'monto'=>$reserva['ReservaFactura']['monto']);

		}
		$this->array_sort_by($gastosMostrar, $orden);
		foreach($gastosMostrar as $gasto){

			$data[] = array($gasto['fechaMostrar'], $gasto['factura'],$gasto['titular'], str_replace('.',',',number_format($gasto['iva_21'],2)),str_replace('.',',',number_format($gasto['monto'],2)));
		}
		$data[] = array("","","","","");
		$data[] = array("","","","","");
		$data[] = array("","","", str_replace('.',',',number_format($total21,2)),str_replace('.',',',number_format($totalMonto,2)));
  		$this->ExportXls->export($fileName, $headerRow, $data);
    }

	public function getApartamentos($categoria_id, $ano){
        $this->layout = 'ajax';
        $this->loadModel('Apartamento');

        $condicion=array('Apartamento.categoria_id =' => $categoria_id);
		$apartamentos = $this->Apartamento->find('all',array('order' => array('Apartamento.orden ASC'), 'conditions' => $condicion));
		/*App::uses('ConnectionManager', 'Model');
	        	$dbo = ConnectionManager::getDatasource('default');
			    $logs = $dbo->getLog();
			    $lastLog = end($logs['log']);

			    echo $lastLog['query'];*/
		$this->set(array(
            'apartamentos' => $apartamentos
        ));

    }


    function base_datos($mes,$ano, $colNombre, $colDni, $colTelefono, $colCelular, $colDireccion, $colLocalidad, $colEmail){
        //error_reporting(0);

        //echo $colNombre;
        $this->layout = 'ajax';

        $this->loadModel('Reserva');

        if ($mes!='N'){
            $from = $ano .'-'. $mes .'-01 00:00:00';
            $to = $ano .'-'. $mes .'-31 00:00:00';
        }
        else{
            $from = $ano .'-01-01 00:00:00';
            $to = $ano .'-12-31 00:00:00';
        }



			$reservas = $this->Reserva->find('all',array('order' => 'Reserva.id desc', 'conditions' => array('Reserva.check_out between ? and ?' => array($from, $to))));

        //print_r($reservas);

        $clientesMostrar = array();
        if(count($reservas) > 0){

            foreach($reservas as $reserva){


                $clientesMostrar[]=array('nombre_apellido'=>$reserva['Cliente']['nombre_apellido'],'dni'=>$reserva['Cliente']['dni'],'telefono'=>$reserva['Cliente']['telefono'],'celular'=>$reserva['Cliente']['celular'],'direccion'=>$reserva['Cliente']['direccion'],'localidad'=>$reserva['Cliente']['localidad'],'email'=>$reserva['Cliente']['email']);
            }
        }
        //$this->array_sort_by($gastosMostrar, $orden);
        $this->set(array(
            'clientes' => $clientesMostrar,
            'colNombre' => $colNombre,
            'colDni' => $colDni,
            'colTelefono' => $colTelefono,
            'colCelular' => $colCelular,
            'colDireccion' => $colDireccion,
            'colLocalidad' => $colLocalidad,
            'colEmail' => $colEmail,
        ));
    }

    function exportarBaseDatos($mes, $ano, $colNombre, $colDni, $colTelefono, $colCelular, $colDireccion, $colLocalidad, $colEmail){
        //error_reporting(0);
        $this->layout = 'ajax';

        $this->loadModel('Reserva');


        if ($mes!='N'){
            $from = $ano .'-'. $mes .'-01 00:00:00';
            $to = $ano .'-'. $mes .'-31 00:00:00';
        }
        else{
            $from = $ano .'-01-01 00:00:00';
            $to = $ano .'-12-31 00:00:00';
        }

        $reservas = $this->Reserva->find('all',array('order' => 'Reserva.id desc', 'conditions' => array('Reserva.check_out between ? and ?' => array($from, $to))));
        $this->autoRender = false;
        $this->layout = false;


        $fileName = "Base_Datos_".$mes.'_'.$ano.".xls";

        $headerRow = array();
        if ($colNombre){
            $headerRow[]="Nombre Apellido";
        }
        if ($colDni){
            $headerRow[]="DNI";
        }
        if ($colTelefono){
            $headerRow[]="Telefono";
        }
        if ($colCelular){
            $headerRow[]="Celular";
        }
        if ($colDireccion){
            $headerRow[]="Direccion";
        }
        if ($colLocalidad){
            $headerRow[]="Localidad";
        }
        if ($colEmail){
            $headerRow[]="E-mail";
        }

        //$headerRow = array("Nombre Apellido","DNI","Telefono","Celular","Direccion","Localidad","E-mail");

        $data = array();

        $clientesMostrar = array();
        if(count($reservas) > 0){

            foreach($reservas as $reserva){




                $clientesMostrar[]=array('nombre_apellido'=>$reserva['Cliente']['nombre_apellido'],'dni'=>$reserva['Cliente']['dni'],'telefono'=>$reserva['Cliente']['telefono'],'celular'=>$reserva['Cliente']['celular'],'direccion'=>$reserva['Cliente']['direccion'],'localidad'=>$reserva['Cliente']['localidad'],'email'=>$reserva['Cliente']['email']);
            }
        }
       // $this->array_sort_by($gastosMostrar, $orden);
         foreach($clientesMostrar as $cliente){

            $row = array();
            if ($colNombre){
                $row[]=utf8_decode(trim($cliente['nombre_apellido']));
            }
            if ($colDni){
                $row[]=utf8_decode(trim($cliente['dni']));
            }
            if ($colTelefono){
                $row[]=utf8_decode(trim($cliente['telefono']));
            }
            if ($colCelular){
                $row[]=utf8_decode(trim($cliente['celular']));
            }
            if ($colDireccion){
                $row[]=utf8_decode(trim($cliente['direccion']));
            }
            if ($colLocalidad){
                $row[]=utf8_decode(trim($cliente['localidad']));
            }
            if ($colEmail){
                $row[]=utf8_decode(trim($cliente['email']));
            }
            $data[] = $row;
        }


        $this->ExportXls->export($fileName, $headerRow, $data);
    }
}
?>
