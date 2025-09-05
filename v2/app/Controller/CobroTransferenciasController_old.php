<?php
class CobroTransferenciasController extends AppController {
    
    public function index(){
        $this->layout = 'index';
    }
    public function dataTable($estado = ''){
        $rows = array();
        if($estado == ''){
            $transferencias = $this->CobroTransferencia->find('all',array('order' => 'ReservaCobro.fecha asc', 'recursive' => 2));
        }elseif($estado == 'pendiente'){
            $transferencias = $this->CobroTransferencia->find('all',array('order' => 'ReservaCobro.fecha asc','conditions' => array('CobroTransferencia.acreditado' => '0'), 'recursive' => 2));
        }elseif($estado == 'acreditado'){
            $transferencias = $this->CobroTransferencia->find('all',array('order' => 'ReservaCobro.fecha asc','conditions' => array('CobroTransferencia.acreditado' => '1'), 'recursive' => 2));
        }

        foreach($transferencias as $tran){
            $monto = $tran['CobroTransferencia']['monto_neto'] + $tran['CobroTransferencia']['interes'];
            if($tran['CobroTransferencia']['acreditado'] == 0){
                $estado = 'Pendiente';
            }else{
                $estado = 'Acreditado';
            }
            $rows[] = array(
                $tran['CobroTransferencia']['id'],
                $tran['ReservaCobro']['id'],
                $tran['ReservaCobro']['fecha'],
                $tran['Cuenta']['Banco']['banco'],
                $tran['Cuenta']['nombre'],
                $tran['CobroTransferencia']['quien_transfiere'],
                $tran['CobroTransferencia']['numero_operacion'],
                'Reserva nro. '.$tran['ReservaCobro']['Reserva']['numero'],
                '$'.$monto,
                $estado
            );
        }
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    public function acreditar(){
        $cobro = $this->CobroTransferencia->read(null, $this->request->data['id']);
        
        $this->CobroTransferencia->set(array(
            'fecha_acreditado' => $this->request->data['fecha'],
            'acreditado' => 1,
            'acreditado_por' => $this->request->data['usuario']
        ));
        if(!$this->CobroTransferencia->validates(array('fieldList' => array('fecha_acreditado')))){
            $this->set('resultado','ERROR');
            $this->set('detalle',$this->CobroTransferencia->validationErrors);
        }else{
            $this->CobroTransferencia->save();
            $this->loadModel('CuentaMovimiento');
            $acreditado = $this->request->data['acreditado'];
            //elimino el movimiento anterior si ya estaba acreditado
        	if ($acreditado) {
				//mysqli_query($conn,"DELETE FROM cuenta_movimiento WHERE cuenta_id = ".$cuenta_id." AND registro_id = ".$registro_id);
				$this->CuentaMovimiento->deleteAll(array('cuenta_id' => $cobro['CobroTransferencia']['cuenta_id'], 'origen' => 'reservatransferencia_'.$this->CobroTransferencia->id), false);
			}
            //agrego el movimiento a la cuenta
            
            $this->CuentaMovimiento->set(array(
                'cuenta_id' => $cobro['CobroTransferencia']['cuenta_id'],
                'origen' => 'reservatransferencia_'.$this->CobroTransferencia->id,
                'monto' =>$cobro['CobroTransferencia']['monto_neto'] + $cobro['CobroTransferencia']['interes'],
                'fecha' => $this->request->data['fecha']
            ));
            $this->CuentaMovimiento->save();
            
            $this->set('resultado','OK');
            $this->set('detalle','');
        }
        $this->set('_serialize', array(
            'resultado',
            'detalle' 
        ));
    }
    
	public function anular(){
        $cobro = $this->CobroTransferencia->read(null, $this->request->data['id']);
        
        $this->CobroTransferencia->set(array(
            'fecha_acreditado' => '01/01/1970',
            'acreditado' => 0,
        	'acreditado_por' => 0
        ));
      
	    $this->CobroTransferencia->save();
	    $this->loadModel('CuentaMovimiento');
	           
		$this->CuentaMovimiento->deleteAll(array('cuenta_id' => $cobro['CobroTransferencia']['cuenta_id'], 'origen' => 'reservatransferencia_'.$this->CobroTransferencia->id), false);
				
	            
	    $this->set('resultado','OK');
	    $this->set('detalle','');
      
       
        $this->set('_serialize', array(
            'resultado',
            'detalle' 
        ));
    }
    
    
    public function agregar($user_id){
        $this->layout = 'ajax';
        
        $this->loadModel('Usuario');
        $this->Usuario->id = $user_id;
        $usuario = $this->Usuario->read();
        
        $this->loadModel('Banco');
        $bancos = $this->Banco->find('list');
        
        $cuentas = array();
        foreach($usuario['Cuentas'] as $cuenta){
            $cuentas[$cuenta['id']] = $bancos[$cuenta['banco_id']]." ".$cuenta['nombre'];
        }
        $this->set('cuentas',$cuentas);
        $this->set('reserva_cobro',$this->request->data['ReservaCobro']);
    }
    
    public function guardar(){
        $data = $this->request->data['CobroTransferencia'];
        $this->CobroTransferencia->set($data);
        if(!$this->CobroTransferencia->validates()){
             $errores['CobroTransferencia'] = $this->CobroTransferencia->validationErrors;
        }else{
            $this->CobroTransferencia->save();
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
