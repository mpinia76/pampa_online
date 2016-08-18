<?php
session_start();
class ReservaCobrosController extends AppController {
    
    public $components = array('Mpdf'); 
    
    public function agregar($reserva_id){
        $this->layout = 'form';
        
        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
        $this->set('reserva',$reserva);
       
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
        $this->set('adelantadas',$adelantadas);
        $this->set('no_adelantadas',$no_adelantadas);
        
        $this->loadModel('CobroTarjetaTipo');
        $this->set('tarjetas_tipo',$this->CobroTarjetaTipo->find('list'));
        
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
        
    }
    public function finalizar($reserva_id){
        $this->layout = 'form';
        
        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
        $this->set('reserva',$reserva);
        
        $extras = $this->Reserva->ReservaExtra->find('all',array('conditions' => array('reserva_id' => $reserva_id, 'adelantada' => 0),'recursive' => 2));
        $this->set('extras',$extras);
        
        $this->loadModel('ExtraRubro');
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
        
        $this->set('pendiente_previo',$reserva['Reserva']['total'] - $descontado - $pagado + $devoluciones);
        $this->set('no_adelantadas',$no_adelantadas);
        
        $this->loadModel('CobroTarjetaTipo');
        $this->set('tarjetas_tipo',$this->CobroTarjetaTipo->find('list'));
        
        $this->loadModel('Caja');
        $this->set('cajas',$this->Caja->find('list'));
        
        $this->loadModel('Cuenta');
        $all_cuentas = $this->Cuenta->find('threaded');
        foreach($all_cuentas as $cuenta){
            $cuentas[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$cuentas);
        
        $this->set('reserva_cobros',$this->ReservaCobro->find('all',array('conditions' => array('reserva_id =' => $reserva_id, 'ReservaCobro.tipo !=' => 'DESCUENTO', 'ReservaCobro.finalizado' => 1), 'order' => 'fecha asc')));
        
        $this->set('facturas_tipo',array('Controlador Fiscal' => 'Controlador Fiscal', 'Manual' => 'Manual'));
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
        if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $tienePermiso=0;
	    	foreach($permisos as $permiso){
	               if ($permiso['UsuarioPermiso']['permiso_id']==101) {
	               		$tienePermiso=1;
	               		continue;
	               }
	        }
        }
        if ($tienePermiso) {
        	$this->ReservaCobro->id = $this->request->data['cobro_id'];
	        $cobro = $this->ReservaCobro->read();
	        switch($cobro['ReservaCobro']['tipo']){
	        	case 'EFECTIVO':
	                //print_r($cobro['CobroEfectivo']);
	                $this->loadModel('CajaMovimiento');
	           		$this->CajaMovimiento->deleteAll(array('origen' => 'reservacobro_'.$cobro['CobroEfectivo']['id']), false);
					
	                break;
	            
	           
	            case 'TRANSFERENCIA':
	            	//print_r($cobro['CobroTransferencia']);
	               $this->loadModel('CuentaMovimiento');
	           		$this->CuentaMovimiento->deleteAll(array('cuenta_id' => $cobro['CobroTransferencia']['cuenta_id'],'origen' => 'reservatransferencia_'.$cobro['CobroTransferencia']['id']), false);
	                break;
	        }
        	$this->ReservaCobro->delete($this->request->data['cobro_id'],true);
        
	        $this->set('resultado','OK');
	        $this->set('mensaje','Cobro eliminado');
	        $this->set('detalle','');
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
}
?>
