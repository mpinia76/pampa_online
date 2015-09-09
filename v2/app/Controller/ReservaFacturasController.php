<?php
class ReservaFacturasController extends AppController {
    
    function guardar(){
        $factura = $this->request->data['ReservaFactura'];
        $this->ReservaFactura->set($factura);
        $reserva = $this->request->data['ReservaCobro'];
        
        $this->ReservaFactura->set(array(
            'reserva_id' => $reserva['reserva_id'],
            'agregada_por' => $reserva['usuario_id']
        ));
        
        if($this->ReservaFactura->validates()){
            $this->ReservaFactura->save();
        }else{
            $errores['ReservaFactura'] = $this->ReservaFactura->validationErrors;
        }
        
        if(isset($errores) and count($errores) > 0){
            $this->set('resultado','ERROR');
            $this->set('mensaje','No se pudo agregar la factura');
            $this->set('detalle',$errores);
        }else{
            $this->set('resultado','OK');
            $this->set('mensaje','Factura agregada');
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
