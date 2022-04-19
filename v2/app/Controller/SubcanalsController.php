<?php
class SubcanalsController extends AppController {
    public $scaffold;
    public $components = array('Mpdf');
    
    public function index(){
    	$this->layout = 'index';
    	 $this->setLogUsuario('Subcanales de venta');
    }
    
	public function dataTable($limit = ""){
        $rows = array();
        $this->loadModel('Subcanal');
        if($limit == "todos"){
            $subcanales = $this->Subcanal->find('all'); 
        }else{
            $subcanales = $this->Subcanal->find('all',array('limit' => $limit)); 
        }
        foreach ($subcanales as $subcanal) {
        	
        	$rows[] = array(
                $subcanal['Subcanal']['id'],
                $subcanal['Canal']['canal'],
                $subcanal['Subcanal']['subcanal']
                
            );
            
        }
       
        
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    
	public function crear(){
        $this->layout = 'form';
		//lista de subcanales
        $this->set('canals', $this->Subcanal->Canal->find('list'));
        
    }
    
    public function editar($id = null){
        $this->layout = 'form';
        $this->set('canals', $this->Subcanal->Canal->find('list'));
		$this->Subcanal->id = $id;
        $this->request->data = $this->Subcanal->read();
        $subcanal = $this->request->data;
		
        $this->set('subcanal', $this->Subcanal->read());
    }
    
public function guardar(){

       
        
        //print_r($this->request->data);
        if(!empty($this->request->data)) {

         	//vaildo reserva
            $subcanal = $this->request->data['Subcanal'];
            $this->Subcanal->set($subcanal);
            if(!$this->Subcanal->validates()){
                $errores['Subcanal'] = $this->Subcanal->validationErrors;
            }

            //muestro resultado
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
                
                $this->Subcanal->save();

                

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
