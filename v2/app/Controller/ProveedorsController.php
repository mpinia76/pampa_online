<?php
class ProveedorsController extends AppController {
    public $scaffold;
    
	public function index(){
    	$this->layout = 'index';
    	 $this->setLogUsuario('Alta de proveedores');
    }
    
	public function dataTable($limit = ""){
        $rows = array();
        $this->loadModel('Proveedor');
        if($limit == "todos"){
            $proveedores = $this->Proveedor->find('all'); 
        }else{
            $proveedores = $this->Proveedor->find('all',array('limit' => $limit)); 
        }
        
        foreach ($proveedores as $proveedor) {
        	//print_r($proveedor);
        	$rows[] = array(
                $proveedor['Proveedor']['id'],
                $proveedor['Proveedor']['nombre'],
                $proveedor['Proveedor']['razon'],
                $proveedor['Proveedor']['cuit'],
                $proveedor['CondicionImpositiva']['nombre'],
                $proveedor['JurisdiccionInscripcion']['nombre'],
                $proveedor['Rubro']['rubro']
                
            );
            
        }
       
        
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    
	public function crear(){
        $this->layout = 'form';
		
        $this->set('rubros', $this->Proveedor->Rubro->find('list'));
        $this->set('condicionImpositivas', $this->Proveedor->CondicionImpositiva->find('list'));
        $this->set('jurisdiccionInscripcions', $this->Proveedor->JurisdiccionInscripcion->find('list'));
        
    }
    
    
	public function editar($id = null){
        $this->layout = 'form';
        
        $this->Proveedor->id = $id;
        $this->request->data = $this->Proveedor->read();
        /*$subcanal = $this->request->data;*/
		
        $this->set('proveedor', $this->Proveedor->read());
        
        
        $this->set('rubros', $this->Proveedor->Rubro->find('list'));
        $this->set('condicionImpositivas', $this->Proveedor->CondicionImpositiva->find('list'));
        $this->set('jurisdiccionInscripcions', $this->Proveedor->JurisdiccionInscripcion->find('list'));
    }
    
	public function guardar(){

       
        
        //print_r($this->request->data);
        if(!empty($this->request->data)) {

         	//vaildo reserva
            $proveedor = $this->request->data['Proveedor'];
            $this->Proveedor->set($proveedor);
            if(!$this->Proveedor->validates()){
                $errores['Proveedor'] = $this->Proveedor->validationErrors;
            }

            //muestro resultado
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
                
                $this->Proveedor->save();

                

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
