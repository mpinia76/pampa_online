<?php
class CobroEfectivosController extends AppController {
    
    public  function agregar($user_id){
        $this->layout = 'ajax';
        
        $this->loadModel('Usuario');
        $this->Usuario->id = $user_id;
        $usuario = $this->Usuario->read();
        $cajas = array();
        foreach($usuario['Cajas'] as $caja){
            $cajas[$caja['id']] = $caja['caja'];
        }
        $this->set('cajas',$cajas);
        $this->set('reserva_cobro',$this->request->data['ReservaCobro']);
    }
}
?>
