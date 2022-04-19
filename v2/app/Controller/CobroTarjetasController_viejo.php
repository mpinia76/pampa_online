<?php
class CobroTarjetasController extends AppController {
    
    public function index(){
        $this->layout = 'index';
        
        $rows = array();
        //$transacciones = $this->get_transacciones();
      
    }
    

    public function get_transacciones() {
        $result = Cache::read('get_transacciones', 'long');
            if (!$result) {
                 $transacciones = $this->CobroTarjeta->find('all',array('order' => 'ReservaCobro.fecha asc','recursive' => 2));
                Cache::write('get_transacciones', $transacciones, 'long');
            }
            return $transacciones;
    }

    public function dataTable(){
        $this->layout = 'ajax';
        
        $rows = array();
        $transacciones = $this->get_transacciones();
        foreach($transacciones as $transaccion){
            if($transaccion['CobroTarjetaLote']['id'] == ''){
                $estado = 'Pendiente de cierre';
            }elseif($transaccion['CobroTarjetaLote']['fecha_cierre'] != '' and $transaccion['CobroTarjetaLote']['fecha_acreditacion'] == ''){
                $estado = 'Pendiente de acreditacion';
            }elseif($transaccion['CobroTarjetaLote']['fecha_cierre'] != '' and $transaccion['CobroTarjetaLote']['fecha_acreditacion'] != ''){
                $estado = 'Acreditado';
            }else{
                $estado = 'Revisar';
            }
            $monto = $transaccion['CobroTarjeta']['monto_neto'] + $transaccion['CobroTarjeta']['interes'];
            $rows[] = array(
                $transaccion['CobroTarjeta']['id'],
                $transaccion['ReservaCobro']['id'],
                $transaccion['ReservaCobro']['fecha'],
                $transaccion['CobroTarjetaTipo']['CobroTarjetaPosnet']['posnet'],
                $transaccion['CobroTarjetaTipo']['marca'],
                $transaccion['CobroTarjeta']['cupon'],
                $transaccion['CobroTarjeta']['autorizacion'],
                $transaccion['CobroTarjeta']['lote'],
                $transaccion['CobroTarjeta']['titular'],
                'Reserva nro: '.$transaccion['ReservaCobro']['Reserva']['numero'],
                '$'.$monto,
                $transaccion['CobroTarjeta']['cuotas'],
                $estado
            );
        }
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    
    public  function agregar(){
        $this->layout = 'ajax';
        
        $this->loadModel('CobroTarjetaPosnet');
        $this->set('posnets',$this->CobroTarjetaPosnet->find('list',array('order' => 'posnet asc')));
        
        $this->set('reserva_cobro',$this->request->data['ReservaCobro']);
        $this->set('cobro_tarjeta_tipos', $this->CobroTarjeta->CobroTarjetaTipo->find('list'));
    }
    
    public function guardar(){
        $data = $this->request->data['CobroTarjeta']; 
        
        //actualizo si es un registro existente
        if(isset($data['id'])){
            $this->CobroTarjeta->id = $data['id'];
            $this->CobroTarjeta->read();
        }
        $this->CobroTarjeta->set($data);
        
        if(!$this->CobroTarjeta->validates()){
            $errores['CobroTarjeta'] = $this->CobroTarjeta->validationErrors;
            $this->set('resultado','ERROR');
            $this->set('mensaje','No se pudo guardar');
            $this->set('detalle',$errores);
        }else{
            $this->CobroTarjeta->save();
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
