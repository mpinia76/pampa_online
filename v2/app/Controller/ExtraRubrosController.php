<?php
class ExtraRubrosController extends AppController {
    public $scaffold;
    
    public function detalle($rubro_id){
        $this->layout = 'ajax';
        
        $this->ExtraRubro->id = $rubro_id;
        $er = $this->ExtraRubro->read();
        if($er['ExtraRubro']['extra_variables']){
            $this->render('extra_variable');
        }else{
            //listo los extras disponibles
            $this->loadModel('Extra');
            $this->set('subrubro_precio', $this->Extra->findAllByExtraRubroIdAndActivo($rubro_id, 1));
            $this->render('extra_precio');
        }
    }
}
?>
