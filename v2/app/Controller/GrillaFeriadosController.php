<?php
class GrillaFeriadosController extends AppController {
    public $scaffold;



    public function add(){
        $this->layout = 'form';


    }

    public function edit($id){
        $this->layout = 'form';

        $this->GrillaFeriado->id = $id;
        $grillaFeriado = $this->GrillaFeriado->read();

        $this->set('grillaFeriado',$grillaFeriado);
        $this->request->data = $grillaFeriado;
    }


    public function guardar(){
        $this->layout = 'json';

        if(!empty($this->request->data)) {
            $GrillaFeriado = $this->request->data['GrillaFeriado'];
            $this->GrillaFeriado->set($GrillaFeriado);
            if($this->GrillaFeriado->validates()){
                $this->GrillaFeriado->save();
            }else{
                $errores['GrillaFeriado'] = $this->GrillaFeriado->validationErrors;
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

    public function eliminar($id = null){
        if(!empty($this->request->data)) {




            $this->GrillaFeriado->delete($this->request->data['id'],true);


            $this->set('resultado','OK');
            $this->set('mensaje','Eliminado');
            $this->set('detalle','');

            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }
    }


}
?>
