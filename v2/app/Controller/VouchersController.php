<?php
class VouchersController extends AppController {
    public $scaffold;
    public $components = array('Mpdf'); 
    
    public function actualizar($reserva_id){
        $this->layout = 'form';
        $this->set('voucher',$this->Voucher->find('first'));
        $this->set('reserva_id',$reserva_id);
    }
    
    public function guardar(){
        $this->Voucher->set($this->request->data['Voucher']);
        $this->Voucher->save();
        
        $this->set('resultado','OK');
        $this->set('mensaje','Datos guardados');
        $this->set('detalle','');
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
    public function ver($reserva_id){
        $this->layout = 'voucher';
        
        $this->set('voucher',$this->Voucher->find('first'));
        
        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
        $this->set('reserva',$reserva);
        
        $pagado = 0;
        $descontado = 0;
        if(count($reserva['ReservaCobro'])>0){
            foreach($reserva['ReservaCobro'] as $cobro){
                if($cobro['tipo'] == 'DESCUENTO'){
                    $descontado = $descontado + $cobro['monto_neto'];
                }else{
                    $pagado = $pagado + $cobro['monto_neto'];
                }
            }
        }
        $this->set('pagado',$pagado);
        $this->set('pendiente',$reserva['Reserva']['total'] - $descontado - $pagado);
        $this->set('total',$reserva['Reserva']['total'] - $descontado);
        
        //genero el pdf
        $this->Mpdf->init(); 
       $this->Mpdf->setFilename('reserva('.$reserva['Reserva']['numero'].')_'.$reserva['Cliente']['nombre_apellido'].'_voucher_'.date('d_m_Y').'.pdf'); 
        $this->Mpdf->setOutput('D'); 
    }
    
}
?>
