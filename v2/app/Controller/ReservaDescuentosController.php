<?php
class ReservaDescuentosController extends AppController {
    
    public function agregar(){
        $this->layout = 'ajax';
    }
    
    public function guardar(){
        $descuento = $this->request->data['ReservaDescuento'];
        $this->ReservaDescuento->set($descuento);
        
        $this->loadModel('ReservaCobro');
        $reservaCobro = $this->request->data['ReservaCobro'];
        $this->ReservaCobro->set($reservaCobro);
        $this->ReservaCobro->set('tipo','DESCUENTO');
        $this->ReservaCobro->set('fecha',$descuento['fecha']); 
        $this->ReservaCobro->set('monto_cobrado','0'); //este valor no se guarda pero es para pasar la regla de validacion del modelo  
        $this->ReservaCobro->set('monto_neto',$descuento['monto']);  
        
        if($this->ReservaDescuento->validates()){
            $this->ReservaCobro->save();
            $this->ReservaDescuento->set('reserva_cobro_id',$this->ReservaCobro->id);
            $this->ReservaDescuento->save();
        }else{
            $errores['ReservaDescuento'] = $this->ReservaDescuento->validationErrors;
        }
        if(isset($errores) and count($errores) > 0){
            $this->set('resultado','ERROR');
            $this->set('mensaje','No se pudo agregar el descuento');
            $this->set('detalle',$errores);
        }else{
            $this->set('resultado','OK');
            $this->set('mensaje','Descuento agregado');
            $this->set('detalle','');
        }
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
	public function eliminar(){
        $this->ReservaDescuento->delete($this->request->data['cobro_id'],true);
        
        $this->set('resultado','OK');
        $this->set('mensaje','Cobro eliminado');
        $this->set('detalle','');
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
}
?>