<?php
class SubrubrosController extends AppController {


    public function combo($rubro_id){
        $this->layout = 'ajax';
        $this->set('subrubros',$this->Subrubro->find('list',array("conditions" => "rubro_id = $rubro_id")));
    }
}
?>
