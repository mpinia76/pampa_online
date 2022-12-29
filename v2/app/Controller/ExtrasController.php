<?php
class ExtrasController extends AppController {
    public $scaffold;

    public function index(){
        $this->layout = 'index';
        $this->setLogUsuario('Extras');

        $this->set('extras', $this->Extra->find('all'));


    }

    public function getSubrubros(){
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->disableCache();
           
            //lista de rubros
           $this->set('extra_subrubros', $this->Extra->ExtraSubrubro->find('list',array('conditions' =>array('extra_rubro_id =' => $this->request->query['rubro_id']))));
        }

    }
	public function getSubrubrosInforme(){
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->disableCache();
           
            //lista de rubros
           $this->set('extra_subrubros', $this->Extra->ExtraSubrubro->find('list',array('conditions' =>array('extra_rubro_id =' => $this->request->query['rubro_id']))));
        }

    }
    public function getSubrubrosPrecio(){
        $this->layout = 'ajax';
        $this->disableCache();
        
        //lista by rubro id
        $this->set('subrubro_precio', $this->Extra->findAllByExtraRubroIdAndActivo($this->request->query['rubro_id'], 1));

    }
    public function add(){
        $this->layout = 'form';
        
        //lista de rubros
        $this->set('extra_rubros', $this->Extra->ExtraRubro->find('list'));
    }
    
    public function edit($id){
        $this->layout = 'form';

        $this->Extra->id = $id;
        $extra = $this->Extra->read();
        $this->set('extra_rubros', $this->Extra->ExtraRubro->find('list'));
        $this->set('extra_subrubros',$this->Extra->ExtraSubrubro->find('list',array('conditions' => array('extra_rubro_id =' =>$extra['Extra']['extra_rubro_id']))));

        $this->set('extra',$extra);
        $this->request->data = $extra;
    }
    
    public function guardar(){
        $this->layout = 'json';
        
        if(!empty($this->request->data)) {
            $extra = $this->request->data['Extra'];
            $this->Extra->set($extra);
            if($this->Extra->validates()){
                $this->Extra->save();
            }else{
                $errores['Extra'] = $this->Extra->validationErrors;
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
