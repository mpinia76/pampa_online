<?php
class CobroChequesController extends AppController {
    public function index(){
        $this->layout = 'index';
        
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
    }
    
    public function dataTable($estado = ''){
        $rows = array();
        if($estado == ''){
            $cheques = $this->CobroCheque->find('all',array('order' => 'ReservaCobro.fecha desc', 'recursive' => 2));
        }elseif($estado == 'pendiente'){
            $cheques = $this->CobroCheque->find('all',array('order' => 'ReservaCobro.fecha desc','conditions' => array('CobroCheque.acreditado' => '0', 'CobroCheque.asociado_a_pagos' => '0'), 'recursive' => 2));
        }elseif($estado == 'acreditado'){
            $cheques = $this->CobroCheque->find('all',array('order' => 'ReservaCobro.fecha desc','conditions' => array('CobroCheque.acreditado' => '1'), 'recursive' => 2));            
        }elseif($estado == 'asociado'){
            $cheques = $this->CobroCheque->find('all',array('order' => 'ReservaCobro.fecha desc','conditions' => array('CobroCheque.asociado_a_pagos' => '1'), 'recursive' => 2));
        }
        
        foreach($cheques as $cheque){
            $monto = $cheque['CobroCheque']['monto_neto'] + $cheque['CobroCheque']['interes'];
            $detalle = ''; //detalle cuando el cheque fue asociado a pago
            if($cheque['CobroCheque']['acreditado'] == 1){
                $estado = 'Acreditado';
            }elseif($cheque['CobroCheque']['asociado_a_pagos'] == 1){
                $estado = 'Asociado a pago';
                //dependiendo si fue a compra o gasto, puede ser que se agreguen mas operaciones que usen el cheque.. ATENTO!
                switch($cheque['RelPagoOperacion']['operacion_tipo']){
                    case 'gasto':
                        $detalle = 'Gasto '.$cheque['RelPagoOperacion']['Gasto']['nro_orden'];
                        break;
                    case 'compra':
                        $detalle = 'Compra '.$cheque['RelPagoOperacion']['Compra']['nro_orden'];
                        break;
                }
            }elseif($cheque['CobroCheque']['acreditado'] == 0){
                $estado = 'Pendiente';
            }
            
            $rows[] = array(
                $cheque['CobroCheque']['id'],
                $cheque['ReservaCobro']['id'],
                $cheque['CobroCheque']['fecha_cobro'],
                $cheque['CobroCheque']['numero'],
                $cheque['CobroCheque']['banco'],
                $cheque['CobroCheque']['tipo'],
                $cheque['CobroCheque']['librado_por'],
                $cheque['CobroCheque']['a_la_orden_de'],
                'Reserva nro. '.$cheque['ReservaCobro']['Reserva']['numero'],
                '$'.$monto,
                $estado,
                $detalle
            );
        }
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    
    public function acreditar(){
        $cobro = $this->CobroCheque->read(null, $this->request->data['id']);
        $acreditado = $this->request->data['acreditado'];
        $this->CobroCheque->set(array(
            'fecha_acreditado' => $this->request->data['fecha'],
            'cuenta_acreditado' => $this->request->data['cuenta'],
            'acreditado' => 1,
            'acreditado_por' => $this->request->data['usuario']
        ));
        if(!$this->CobroCheque->validates(array('fieldList' => array('fecha_acreditado','cuenta_acreditado')))){
            $this->set('resultado','ERROR');
            $this->set('detalle',$this->CobroCheque->validationErrors);
        }else{
            $this->CobroCheque->save();
            
            //agrego el movimiento a la cuenta
            $this->loadModel('CuentaMovimiento');
            $acreditado = $this->request->data['acreditado'];
        	//elimino el movimiento anterior si ya estaba acreditado
        	if ($acreditado) {
				//mysql_query("DELETE FROM cuenta_movimiento WHERE cuenta_id = ".$cuenta_id." AND registro_id = ".$registro_id);
				$this->CuentaMovimiento->deleteAll(array('origen' => 'reservacheque_'.$this->CobroCheque->id), false);
			}
            $this->CuentaMovimiento->set(array(
                'cuenta_id' => $this->request->data['cuenta'],
                'origen' => 'reservacheque_'.$this->CobroCheque->id,
                'monto' =>$cobro['CobroCheque']['monto_neto'] + $cobro['CobroCheque']['interes'],
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
    
    public  function agregar(){
        $this->layout = 'ajax';
        
        $this->set('tipos',array('COMUN' => 'Comun','DIFERIDO' => 'Diferido'));
        $this->set('reserva_cobro',$this->request->data['ReservaCobro']);
    }
    
    public function guardar(){
        $data = $this->request->data['CobroCheque'];
        $this->CobroCheque->set($data);
        if(!$this->CobroCheque->validates()){
             $errores['CobroCheque'] = $this->CobroCheque->validationErrors;
        }else{
            $this->CobroCheque->save();
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
