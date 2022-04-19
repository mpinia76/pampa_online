<?php
class CobroTarjetaCuotasController extends AppController {
    public $scaffold;
    
    public function add(){
        $this->layout = 'form';
        
        $this->loadModel('CobroTarjetaPosnet');
        $this->set('posnets',$this->CobroTarjetaPosnet->find('list',array('order' => 'posnet asc')));
    }
    
    public function index(){
        $this->layout = 'index';
          $this->setLogUsuario('Cuotas y Coheficientes');
        $this->loadModel('CobroTarjetaTipo');
        $this->set('tarjeta_tipos', $this->CobroTarjetaTipo->find('all'));
        
        $this->loadModel('CobroTarjetaPosnet');
        $this->set('locaciones',$this->CobroTarjetaPosnet->find('list'));
    }
    public function getCuotas($tarjeta_id){
        $this->layout = 'ajax';
        
        $this->set('cuotas', $this->CobroTarjetaCuota->find('list',array('fields' => 'cuota,cuota', 'order' => 'cuota asc', 'conditions' =>array('cobro_tarjeta_tipo_id =' => $tarjeta_id))));
        $this->set('cuotas_interes', $this->CobroTarjetaCuota->find('list',array('fields' => 'cuota,interes', 'order' => 'cuota asc', 'conditions' =>array('cobro_tarjeta_tipo_id =' => $tarjeta_id))));
    }
    
    public function edit($id){
        $this->layout = 'form';
        
        $this->loadModel('CobroTarjetaPosnet');
        $this->set('posnets',$this->CobroTarjetaPosnet->find('list',array('order' => 'posnet asc')));
        
        $this->CobroTarjetaCuota->id = $id;
        $cobro_tarjeta_cuota = $this->CobroTarjetaCuota->read(); 

        $this->loadModel('CobroTarjetaTipo');
        $this->set('marcas', $this->CobroTarjetaTipo->find('list',array('conditions' =>array('cobro_tarjeta_posnet_id =' => $cobro_tarjeta_cuota['CobroTarjetaTipo']['cobro_tarjeta_posnet_id']))));

        $this->set('cobro_tarjeta_cuota',$cobro_tarjeta_cuota);
        $this->request->data = $cobro_tarjeta_cuota;
    }
    
    public function guardar(){
        $this->layout = 'json';
        
        if(!empty($this->request->data)) {
            $tarjeta_cuota = $this->request->data['CobroTarjetaCuota'];
            $this->CobroTarjetaCuota->set($tarjeta_cuota);
            if($this->CobroTarjetaCuota->validates()){
                $this->CobroTarjetaCuota->save();
            }else{
                $errores['CobroTarjetaCuota'] = $this->CobroTarjetaCuota->validationErrors;
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
    }
}
?>
