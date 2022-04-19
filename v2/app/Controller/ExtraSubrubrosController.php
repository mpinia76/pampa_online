<?php
class ExtraSubrubrosController extends AppController {
    public $scaffold;
    public function json(){
        $this->response->type('application/json');
        $this->layout = 'json';
        $this->set('subrubros',$this->Subrubro->find('all'));
    }
    
    public function create(){
        $this->layout = 'json';
        if(empty($this->request->data)) {
            
        }else{
            $this->loadModel('Rubro');
            $rubro = $this->request->data['Rubro'];
            $this->Rubro->create();
            $this->Rubro->set($rubro);
            if($this->Rubro->validates()){
                $this->Rubro->save();
                
                $subrubro = $this->request->data['Subrubro'];
                $this->Subrubro->create();
                $this->Subrubro->set('rubro_id',$this->Rubro->id);
                $this->Subrubro->set($subrubro);
                
                if($this->Subrubro->validates()){
                    $this->Subrubro->save();
                }
            }
            

        }
        
    }
}
?>
