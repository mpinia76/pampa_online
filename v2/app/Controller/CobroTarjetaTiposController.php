<?php
class CobroTarjetaTiposController extends AppController {
    public $scaffold;
    
    public function getMarcas($posnet_id){
        $this->layout = 'ajax';
        //model es porque se llama desde distintas views este combo
        $this->set('model',$this->request->query['model']);
        $this->CobroTarjetaTipo->virtualFields = array(
		    'marca' =>
		    "CONCAT(marca, ' - ', nro_comercio)"
		    
		    
		);
        $this->set('marcas', $this->CobroTarjetaTipo->find('list',array('order' => 'marca asc', 'conditions' =>array('cobro_tarjeta_posnet_id =' => $posnet_id, 'mostrar=1'))));
    }
    
    public function add(){
        $this->layout = 'form';
        
        $this->loadModel('CobroTarjetaPosnet');
        $this->set('posnets',$this->CobroTarjetaPosnet->find('list'));
        
        $this->loadModel('Cuenta');
        $cuentas = $this->Cuenta->find('all',array('recursive' => 1));
        foreach($cuentas as $cuenta){
            $list[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." - ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$list);
        
        $this->render('form');
    }

    public function edit($id){
        $this->layout = 'form';
        
        $this->CobroTarjetaTipo->id = $id;
        $this->request->data = $this->CobroTarjetaTipo->read();
        
        $this->loadModel('CobroTarjetaPosnet');
        $this->set('posnets',$this->CobroTarjetaPosnet->find('list'));
        
        $this->loadModel('Cuenta');
        $cuentas = $this->Cuenta->find('all',array('recursive' => 1));
        foreach($cuentas as $cuenta){
            $list[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." - ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$list);
        $valor = ($this->request->data['CobroTarjetaTipo']['mostrar']=='SI')?1:0;
        $chequeado = ($this->request->data['CobroTarjetaTipo']['mostrar']=='SI')?'checked':null;
        $this->set('valor',$valor);
        $this->set('chequeado',$chequeado);
        $this->render('form');
    }
    
    public function guardar(){
        $this->layout = 'json';
        
        if(!empty($this->request->data)) {
            $tarjeta_tipo = $this->request->data['CobroTarjetaTipo'];
            $this->CobroTarjetaTipo->set($tarjeta_tipo);
            if($this->CobroTarjetaTipo->validates()){
                $this->CobroTarjetaTipo->save();
            }else{
                $errores['CobroTarjetaTipo'] = $this->CobroTarjetaTipo->validationErrors;
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
