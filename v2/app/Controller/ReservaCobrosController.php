<?php
session_start();
class ReservaCobrosController extends AppController {
    
	public function dateFormatSQL($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }
	
	
    public $components = array('Mpdf'); 
    
    public function agregar($reserva_id, $grilla=null){
        $this->layout = 'form';
        $this->set('grilla',$grilla);
        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
       
       
        $adelantadas = 0;
        $no_adelantadas = 0;
        if(count($reserva['ReservaExtra']>0)){
            foreach($reserva['ReservaExtra'] as $extra){
                if($extra['adelantada'] == 1){
                    $adelantadas = $adelantadas + $extra['cantidad'] * $extra['precio'];
                }else{
                    $no_adelantadas = $no_adelantadas + $extra['cantidad'] * $extra['precio'];
                }
            }
        }
    	if(count($reserva['ReservaDevolucion']>0)){
    		$this->loadModel('ReservaDevolucion');
    		$i=0;
            foreach($reserva['ReservaDevolucion'] as $devolucion){
                switch ($devolucion['forma_pago']) {
                	case 'EFECTIVO':
                		$dev = $this->ReservaDevolucion->find('first',array('joins' => array(
	       
					        array(
					            'table' => 'rel_pago_operacion',
					            'alias' => 'RelPagoOperacion',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'ReservaDevolucion.id = RelPagoOperacion.operacion_id and RelPagoOperacion.operacion_tipo = "reserva_devolucion" '
					            )
					        ),
					        array(
					            'table' => 'efectivo_consumo',
					            'alias' => 'EfectivoConsumo',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'EfectivoConsumo.id = RelPagoOperacion.forma_pago_id'
					            )
					        ),
					        array(
					            'table' => 'caja',
					            'alias' => 'Caja',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'EfectivoConsumo.caja_id = Caja.id'
					            )
					        ),
					        array(
					            'table' => 'usuario',
					            'alias' => 'Usuario',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'ReservaDevolucion.usuario_id= Usuario.id'
					            )
					        )
					        
					    ),'fields'=>array('Caja.caja','EfectivoConsumo.interes','Usuario.nombre','Usuario.apellido'), 'conditions' => array('ReservaDevolucion.id =' => $devolucion['id']), 'recursive' => -1));
					   if ($dev) {
					   		$devolucion['detalle']='desde caja '.$dev['Caja']['caja'];
					   		$devolucion['interes']=$dev['EfectivoConsumo']['interes'];
					   		$devolucion['usuario']=$dev['Usuario']['nombre'].' '.$dev['Usuario']['apellido'];
					   		$reserva['ReservaDevolucion'][$i]=$devolucion;
					   }  
                	break;
                	
                	case 'TRANSFERENCIA':
		                $dev = $this->ReservaDevolucion->find('first',array('joins' => array(
	       
					        array(
					            'table' => 'rel_pago_operacion',
					            'alias' => 'RelPagoOperacion',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'ReservaDevolucion.id = RelPagoOperacion.operacion_id and RelPagoOperacion.operacion_tipo = "reserva_devolucion" '
					            )
					        ),
					        array(
					            'table' => 'transferencia_consumo',
					            'alias' => 'TransferenciaConsumo',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'TransferenciaConsumo.id = RelPagoOperacion.forma_pago_id'
					            )
					        ),
					        array(
					            'table' => 'cuenta',
					            'alias' => 'Cuenta',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'TransferenciaConsumo.cuenta_id = Cuenta.id'
					            )
					        ),
					        array(
					            'table' => 'usuario',
					            'alias' => 'Usuario',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'ReservaDevolucion.usuario_id= Usuario.id'
					            )
					        )
					        
					    ),'fields'=>array('Cuenta.nombre','TransferenciaConsumo.interes','Usuario.nombre','Usuario.apellido'), 'conditions' => array('ReservaDevolucion.id =' => $devolucion['id']), 'recursive' => -1));
					   if ($dev) {
					   		$devolucion['detalle']='desde cuenta '.$dev['Cuenta']['nombre'];
					   		$devolucion['interes']=$dev['TransferenciaConsumo']['interes'];
					   		$devolucion['usuario']=$dev['Usuario']['nombre'].' '.$dev['Usuario']['apellido'];
					   		$reserva['ReservaDevolucion'][$i]=$devolucion;
					   		
					   } 
		                break;
		            case 'CHEQUE':
		                $dev = $this->ReservaDevolucion->find('first',array('joins' => array(
	       
					        array(
					            'table' => 'rel_pago_operacion',
					            'alias' => 'RelPagoOperacion',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'ReservaDevolucion.id = RelPagoOperacion.operacion_id and RelPagoOperacion.operacion_tipo = "reserva_devolucion" '
					            )
					        ),
					        array(
					            'table' => 'cheque_consumo',
					            'alias' => 'ChequeConsumo',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'ChequeConsumo.id = RelPagoOperacion.forma_pago_id'
					            )
					        ),
					        array(
					            'table' => 'usuario',
					            'alias' => 'Usuario',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'ReservaDevolucion.usuario_id= Usuario.id'
					            )
					        )
					        
					    ),'fields'=>array('ChequeConsumo.numero','ChequeConsumo.interes','Usuario.nombre','Usuario.apellido'), 'conditions' => array('ReservaDevolucion.id =' => $devolucion['id']), 'recursive' => -1));
					   if ($dev) {
					   		$devolucion['detalle']='cheque numero '.$dev['ChequeConsumo']['numero'];
					   		$devolucion['interes']=$dev['ChequeConsumo']['interes'];
					   		$devolucion['usuario']=$dev['Usuario']['nombre'].' '.$dev['Usuario']['apellido'];
					   		$reserva['ReservaDevolucion'][$i]=$devolucion;
					   		
					   } 
		                break;
                } 
            	
			   $i++;
		        //print_r($dev);
            }
        }
        //print_r($reserva['ReservaDevolucion']);
       $this->set('reserva',$reserva);
        
        $this->set('adelantadas',$adelantadas);
        $this->set('no_adelantadas',$no_adelantadas);
        
        $this->loadModel('CobroTarjetaTipo');
        $this->set('tarjetas_tipo',$this->CobroTarjetaTipo->find('list'));
        
        $this->loadModel('Moneda');
        $this->set('monedas',$this->Moneda->find('list'));

        $this->loadModel('ConceptoFacturacion');
        $this->set('concepto_facturacions',$this->ConceptoFacturacion->find('list',array('fields' => 'id,nombre','conditions' =>array('activo =' => 1))));
        
        $this->loadModel('Caja');
        $this->set('cajas',$this->Caja->find('list'));
        
        $this->loadModel('Cuenta');
        $all_cuentas = $this->Cuenta->find('threaded');
        foreach($all_cuentas as $cuenta){
            $cuentas[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$cuentas);
        
        $this->set('reserva_descuentos',$this->ReservaCobro->find('all',array('conditions' => array('reserva_id =' => $reserva_id, 'ReservaCobro.tipo =' => 'DESCUENTO'), 'order' => 'fecha asc')));
        $this->set('reserva_cobros',$this->ReservaCobro->find('all',array('conditions' => array('reserva_id =' => $reserva_id, 'ReservaCobro.tipo !=' => 'DESCUENTO'), 'order' => 'fecha asc')));
        $this->loadModel('ConceptoFacturacion');


        $this->set('concepto_facturacions',$this->ConceptoFacturacion->find('list',array('fields' => 'id,nombre','conditions' =>array('activo =' => 1))));
        
    }
    public function finalizar($reserva_id, $restringido=0){
        $this->layout = 'form';
        
        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
        //print_r($reserva);
        $this->set('reserva',$reserva);
        
        $facturas = $this->Reserva->ReservaFactura->find('all',array('conditions' => array('reserva_id' => $reserva_id),'recursive' => 2));
        $this->set('facturas',$facturas);
        //print_r($facturas);
        
        $extras = $this->Reserva->ReservaExtra->find('all',array('conditions' => array('reserva_id' => $reserva_id, 'adelantada' => 0),'recursive' => 2));
        $this->set('extras',$extras);
        //'print_r($extras);
        $this->loadModel('ExtraRubro');//
        $this->set('extra_rubros',$this->ExtraRubro->find('list'));
        
        $pagado = 0;
        $descontado = 0;
        $adelantadas = 0;
        $no_adelantadas = 0;
        if(count($reserva['ReservaCobro'])>0){
            foreach($reserva['ReservaCobro'] as $cobro){
                if($cobro['finalizado'] == 0){
                    if($cobro['tipo'] == 'DESCUENTO'){
                        $descontado = $descontado + $cobro['monto_neto'];
                    }else{
                        $pagado = $pagado + $cobro['monto_neto'];
                    }
                }
            }
        }
        if(count($reserva['ReservaExtra']>0)){
            foreach($reserva['ReservaExtra'] as $extra){
                if($extra['adelantada'] == 1){
                    $adelantadas = $adelantadas + $extra['cantidad'] * $extra['precio'];
                }else{
                    $no_adelantadas = $no_adelantadas + $extra['cantidad'] * $extra['precio'];
                }
            }
        }
        
        $devoluciones = 0;
        if(count($reserva['ReservaDevolucion']) > 0){
            foreach($reserva['ReservaDevolucion'] as $devolucion){
                $devoluciones += $devolucion['monto'];
            }
        }
        
        $this->set('descontado',$descontado);
        $this->set('pendiente_previo',$reserva['Reserva']['total'] - $pagado + $devoluciones);
        $this->set('no_adelantadas',$no_adelantadas);
        
        $this->loadModel('CobroTarjetaTipo');
        $this->set('tarjetas_tipo',$this->CobroTarjetaTipo->find('list'));
        
        $this->loadModel('Moneda');
        $this->set('monedas',$this->Moneda->find('list'));

        $this->loadModel('ConceptoFacturacion');
        $this->set('concepto_facturacions',$this->ConceptoFacturacion->find('list',array('fields' => 'id,nombre','conditions' =>array('activo =' => 1))));
        
        $this->loadModel('Caja');
        $this->set('cajas',$this->Caja->find('list'));
        
        $this->loadModel('Cuenta');
        $all_cuentas = $this->Cuenta->find('threaded');
        foreach($all_cuentas as $cuenta){
            $cuentas[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$cuentas);
        
        $this->set('reserva_cobros',$this->ReservaCobro->find('all',array('conditions' => array('reserva_id =' => $reserva_id, 'ReservaCobro.tipo !=' => 'DESCUENTO', 'ReservaCobro.finalizado' => 1), 'order' => 'fecha asc')));
        
        //$this->set('facturas_tipo',array('Controlador Fiscal' => 'Controlador Fiscal', 'Manual' => 'Manual'));
        $this->set('facturas_tipo',array('A' => 'A', 'B' => 'B'));
        $this->set('tipos_doc',array('1' => 'Factura', '2' => 'Nota de credito'));
        
        $this->loadModel('PuntoVenta');
        $this->set('puntos_venta',$this->PuntoVenta->find('list'));
        
        $this->loadModel('Usuario');
        $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));
        
        $permisoEditar=1;
		$permisoDescuento=1;
        $permisoCobro=1;
        $permisoFactura=1;
        if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $permisoEditar=0;
	        $permisoDescuento=0;
            $permisoCobro=0;
            $permisoFactura=0;
	    	foreach($permisos as $permiso){
               if ($permiso['UsuarioPermiso']['permiso_id']==117) {
               		$permisoEditar=1;
               		//continue;
               }
	    		if ($permiso['UsuarioPermiso']['permiso_id']==132) {
               		$permisoDescuento=1;
               		//continue;
               }
                if ($permiso['UsuarioPermiso']['permiso_id']==156) {
                    $permisoCobro=1;
                    //continue;
                }
                if ($permiso['UsuarioPermiso']['permiso_id']==157) {
                    $permisoFactura=1;
                    //continue;
                }
	        }
        }
        $this->set('permisoEditar',$permisoEditar);
        $this->set('permisoDescuento',$permisoDescuento);
        $this->set('permisoCobro',$permisoCobro);
        $this->set('permisoFactura',$permisoFactura);
        $this->set('restringido',$restringido);
        //if ($restringido) {
        	 $this->set('reserva_descuentos',$this->ReservaCobro->find('all',array('conditions' => array('reserva_id =' => $reserva_id, 'ReservaCobro.tipo =' => 'DESCUENTO'), 'order' => 'fecha asc')));
        //}
       
        
    }
    public function recibo($cobro_id){
        $this->layout = 'recibo';
         
        $this->ReservaCobro->id = $cobro_id;
        $cobro = $this->ReservaCobro->read();
        $this->set('cobro',$cobro);
        
        $this->loadModel('Voucher');
        $this->set('voucher',$this->Voucher->find('first'));
        
        $this->loadModel('Cliente');
        $this->Cliente->id = $cobro['Reserva']['cliente_id'];
        $cliente = $this->Cliente->read();
        $this->set('cliente',$cliente);
        
        $this->loadModel('Reserva');
        $this->Reserva->id = $cobro['Reserva']['id'];
        $reserva = $this->Reserva->read(); 
        
        $descontado = 0;
        if(count($reserva['ReservaCobro'])>0){
            foreach($reserva['ReservaCobro'] as $cobro){
                if($cobro['tipo'] == 'DESCUENTO'){
                    $descontado = $descontado + $cobro['monto_neto'];
                }
            }
        }
        $this->set('acordado',$reserva['Reserva']['total'] - $descontado);
       
        //genero el pdf
        $this->Mpdf->init(); 
        $this->Mpdf->setFilename('reserva('.$reserva['Reserva']['numero'].')_'.$cliente['Cliente']['nombre_apellido'].'_cobro_recibo_'.$cobro_id.'_'.date('d_m_Y').'.pdf'); 
        $this->Mpdf->setOutput('D'); 
    }
    
    public function detalle($cobro_id){
        $this->layout = 'form';
        $this->ReservaCobro->id = $cobro_id;
        $cobro = $this->ReservaCobro->read();
        $this->set('cobro',$cobro);
        switch($cobro['ReservaCobro']['tipo']){
            case 'TARJETA':
                $this->loadModel('CobroTarjetaTipo');
                $this->CobroTarjetaTipo->id = $cobro['CobroTarjeta']['cobro_tarjeta_tipo_id'];
                $this->set('tarjeta_tipo',$this->CobroTarjetaTipo->read());
                break;
            case 'CHEQUE':
                $this->set('tipos',array('COMUN' => 'Comun','DIFERIDO' => 'Diferido'));
                break;
            case 'TRANSFERENCIA':
                $this->loadModel('Usuario');
                $this->Usuario->id = $_COOKIE['userid'];
                $usuario = $this->Usuario->read();

                $this->loadModel('Banco');
                $bancos = $this->Banco->find('list');

                $cuentas = array();
                foreach($usuario['Cuentas'] as $cuenta){
                    $cuentas[$cuenta['id']] = $bancos[$cuenta['banco_id']]." ".$cuenta['nombre'];
                }
                $this->set('cuentas',$cuentas);
                break;
        }
    }
    
    public function validar(){
        $reservaCobro = $this->request->data['ReservaCobro'];
        $this->ReservaCobro->set($reservaCobro);
        if(!$this->ReservaCobro->validates()){
            $this->set('error',$this->ReservaCobro->validationErrors);
            $this->set('_serialize', array('error'));
        }else{
            $this->set('error','');
            $this->set('_serialize', array('error'));
        }
    }
    
    public function guardar(){
        //print_r($this->request->data);

        $reservaCobro = $this->request->data['ReservaCobro'];
        switch ($reservaCobro['tipo']){
            case 'TARJETA':
                $model = 'CobroTarjeta';
                break;
            
            case 'CHEQUE':
                $model = 'CobroCheque';
                break;
            
            case 'EFECTIVO':
                $model = 'CobroEfectivo';
                break;
            
            case 'TRANSFERENCIA':
                $model = 'CobroTransferencia';
                break;
        }
        
        $this->loadModel($model);
        $data = $this->request->data[$model];
        
        $this->$model->set($data);
        if(!$this->$model->validates()){
            $errores[$model] = $this->$model->validationErrors;
        }else{
            $this->ReservaCobro->set($reservaCobro);
            $this->ReservaCobro->set('monto_cobrado',$data['monto_neto'] + $data['interes']);
            $this->ReservaCobro->set('monto_pendiente',$this->request->data['ReservaCobro']['pendiente'] - $data['monto_neto']);
            $this->ReservaCobro->save();
            
            $this->$model->set('reserva_cobro_id',$this->ReservaCobro->id);
           
            $this->$model->save();
        }

        if(isset($errores) and count($errores) > 0){
            $this->set('resultado','ERROR');
            $this->set('mensaje','No se pudo guardar');
            $this->set('detalle',$errores);
         }else{
            $this->set('resultado','OK');
            $this->set('mensaje','Datos guardados');
            $this->set('detalle','');
         }
         
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
    public function eliminar(){
        $this->ReservaCobro->delete($this->request->data['cobro_id'],true);
        
        $this->set('resultado','OK');
        $this->set('mensaje','Descuento eliminado');
        $this->set('detalle','');
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
	public function eliminarCobro(){
		$user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));
       	$tienePermiso=1;	
       	$tienePermisoSincro=1;	
        if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $tienePermiso=0;
	        $tienePermisoSincro=0;
	    	foreach($permisos as $permiso){
	    			if ($permiso['UsuarioPermiso']['permiso_id']==134){
	    				$tienePermisoSincro=1;
	    			}
	               if (($permiso['UsuarioPermiso']['permiso_id']==101)||($permiso['UsuarioPermiso']['permiso_id']==102)) {
	               		$tienePermiso=1;
	               		//continue;
	               }
	        }
        }
        if ($tienePermiso) {
        	$descubierto=1;
        	$sincronizada=1;
        	$this->ReservaCobro->id = $this->request->data['cobro_id'];
	        $cobro = $this->ReservaCobro->read();
	        switch($cobro['ReservaCobro']['tipo']){
	        	case 'EFECTIVO':
	               //print_r($cobro);
	          		$this->loadModel('CajaSincronizada');
	        		$fechaSincronizada = $this->CajaSincronizada->find('first',array('fields'=>'MAX(CajaSincronizada.fecha) as fecha','conditions' => array('CajaSincronizada.caja_id =' => $cobro['CobroEfectivo']['caja_id'])));
	        		
	        		 $this->loadModel('CajaMovimiento');
	        		$movimientos = $this->CajaMovimiento->find('all',array('conditions' => array('origen' => 'reservacobro_'.$cobro['CobroEfectivo']['id'])));
	        		 
	        		foreach ($movimientos as $movimiento) {
	        			//print_r($movimiento);
	        			
		        		if ($fechaSincronizada[0]['fecha']>=$movimiento['CajaMovimiento']['fecha']) {
		        			if (!$tienePermisoSincro) {
		        				$sincronizada=0;
		        			}
		        			
		        		}
	        		}
	        		
	          		
	                $this->loadModel('Caja');
	                $caja=$this->Caja->findById($cobro['CobroEfectivo']['caja_id']);
	                 
	               
	                
	                if (!$caja['Caja']['descubierto']) {
	                	$monto = $this->CajaMovimiento->find('first',array('fields'=>'SUM(CajaMovimiento.monto) as total','conditions' => array('CajaMovimiento.caja_id =' => $cobro['CobroEfectivo']['caja_id'])));
	                	
	                	$montoTotal =$monto[0]['total']-$cobro['CobroEfectivo']['monto_neto'];
						//echo $montoTotal;
	                	if ($montoTotal<0) {
	                		$descubierto=0;
	                	}
	                }
	                if($descubierto&&$sincronizada){
	                	$this->CajaMovimiento->deleteAll(array('origen' => 'reservacobro_'.$cobro['CobroEfectivo']['id']), false);
	                }
	           		
					
	                break;
	            case 'CHEQUE':
	                //print_r($cobro['CobroCheque']);       	
	               	$this->loadModel('CuentaMovimiento');
	               	$this->CuentaMovimiento->deleteAll(array('origen' => 'reservacheque_'.$cobro['CobroCheque']['id']), false);
	           		//$this->CuentaMovimiento->deleteAll(array('cuenta_id' => $cobro['CobroCheque']['cuenta_id'],'origen' => 'reservacheque_'.$cobro['CobroCheque']['id']), false);
	               					
	                break;
	           
	            case 'TRANSFERENCIA':
	            	//print_r($cobro['CobroTransferencia']);
	               $this->loadModel('CuentaMovimiento');
	           		$this->CuentaMovimiento->deleteAll(array('cuenta_id' => $cobro['CobroTransferencia']['cuenta_id'],'origen' => 'reservatransferencia_'.$cobro['CobroTransferencia']['id']), false);
	                break;
	        }
        	//
        	if (!$descubierto) {
	        	$this->set('resultado','ERROR');
		        $this->set('mensaje','Cobro no eliminado, ');
		        $this->set('detalle','genera saldo negativo en caja no autorizada');
	        }
	        elseif(!$sincronizada) {
	        	$this->set('resultado','ERROR');
		        $this->set('mensaje','Cobro no eliminado, ');
		        $this->set('detalle','la caja se encuentra conciliada y sincronizada para la fecha del movimiento que intenta realizar. Contactar administrador');
	        }
        	else{
        		$this->ReservaCobro->delete($this->request->data['cobro_id'],true);
        
		        $this->set('resultado','OK');
		        $this->set('mensaje','Cobro eliminado');
		        $this->set('detalle','');
        	}
	        
        }
        else{
        	$this->set('resultado','ERROR');
	        $this->set('mensaje','Cobro no eliminado');
	        $this->set('detalle','No tiene permiso');
        }
        
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
    //protege el controlador solo para usuarios
    public function beforeFilter(){
        if(isset($_COOKIE['userid'])){
            $this->loadModel('Usuario');
            $this->set('usuario',$this->Usuario->findById($_COOKIE['userid']));
        }else{
            $this->redirect('/index');
        }
    }

    public function guardarConcepto(){

        //print_r($this->request->data);

        $this->ReservaCobro->id = $this->request->data['cobro_id'];


        $reservaCobro=$this->ReservaCobro->read();
        $reservaCobro['ReservaCobro']['concepto_facturacion_id']=$this->request->data['concepto_facturacion_id'];



        $this->ReservaCobro->save($reservaCobro, false);


        if(isset($errores) and count($errores) > 0){
            $this->set('resultado','ERROR');
            $this->set('mensaje','No se pudo guardar');
            $this->set('detalle',$errores);
        }else{
            $this->set('resultado','OK');
            $this->set('mensaje','Datos guardados');
            $this->set('detalle','');
        }

        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle'
        ));
    }
}
?>
