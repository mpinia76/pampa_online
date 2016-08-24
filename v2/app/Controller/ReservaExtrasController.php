<?php
class ReservaExtrasController extends AppController {
    public $scaffold;
    public function index(){
        $this->set('rows',$this->ReservaExtra->find('all'));
        $this->set('_serialize', array(
            'rows'
        ));
    }
    public function eliminar(){
        $id = $this->request->data['reserva_extra_id'];
        $this->ReservaExtra->id = $id;
        $reserva_extra = $this->ReservaExtra->read();
        /*if($reserva_extra['ReservaExtra']['extra_id']){
            $this->ReservaExtra->delete($id);
        }else if($reserva_extra['ReservaExtra']['extra_variable_id']){
            $this->loadModel('ExtraVariable');
            $this->ExtraVariable->delete($reserva_extra['ReservaExtra']['extra_variable_id']);
            $this->ReservaExtra->delete($id);
        }
        $this->autoRender = false;*/
        $this->set('resultado','ERROR');
        $this->set('mensaje','Cobro no eliminado');
        $this->set('detalle','No tiene permiso');
    }
    
    public function getRow(){
        $this->layout = 'ajax';

        if($this->request->data){
            $this->set('cantidad',$this->request->data['cantidad']);
            $extra = $this->ReservaExtra->Extra->findById($this->request->data['extra_id']);
            $this->set('extra',$extra);
            
            //guardo la relacion automaticamente
            $this->ReservaExtra->set(array(
                'reserva_id' => $this->request->data['reserva_id'],
                'extra_id' => $this->request->data['extra_id'],
                'cantidad' => $this->request->data['cantidad'],
                'precio' => $extra['Extra']['tarifa'],
                'adelantada' => 0,
                'agregada' => date('Y-m-d')
            ));
            $this->ReservaExtra->save();
            $this->set('reserva_extra_id',$this->ReservaExtra->id);
        }else{
            $this->set('cantidad',$this->request->query['cantidad']);
            $this->set('extra',$this->ReservaExtra->Extra->findById($this->request->query['extra_id']));
        }
    }
    public function getRowVariable(){
        $this->layout = 'ajax';
        
        if($this->request->data){
            $this->set('precio',$this->request->data['precio']);
            $this->set('detalle',$this->request->data['detalle']);
            
            $this->loadModel('ExtraRubro');
            $this->set('rubro',$this->ExtraRubro->findById($this->request->data['rubro_id']));
            
            $this->loadModel('ExtraVariable');
            $this->ExtraVariable->set(array(
                'extra_rubro_id' => $this->request->data['rubro_id'],
                'detalle' => $this->request->data['detalle']
            ));
            $this->ExtraVariable->save();
            
            //guardo la relacion automaticamente
            $this->ReservaExtra->set(array(
                'reserva_id' => $this->request->data['reserva_id'],
                'extra_variable_id' => $this->ExtraVariable->id,
                'precio' => $this->request->data['precio'],
                'adelantada' => 0,
                'cantidad' => 1,
                'agregada' => date('Y-m-d')
            ));
            $this->ReservaExtra->save();
            $this->set('reserva_extra_id',$this->ReservaExtra->id);
            
        }else{
            $this->set('precio',$this->request->query['precio']);
            $this->set('detalle',$this->request->query['detalle']);
            $this->loadModel('ExtraRubro');
            $this->set('rubro',$this->ExtraRubro->findById($this->request->query['rubro_id']));
        }
    }
}
?>
