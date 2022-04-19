<?php
class PuntoVentasController extends AppController {
    public $scaffold;
    
	public function index(){
    	$this->layout = 'index';
    	$this->setLogUsuario('Puntos de ventas');
    }
    
 	
    
	public function dataTable($limit = ""){
        $rows = array();
        //$this->loadModel('PuntoVenta');
        if($limit == "todos"){
            $puntoVentas = $this->PuntoVenta->find('all'); 
        }else{
            $puntoVentas = $this->PuntoVenta->find('all',array('limit' => $limit)); 
        }
        foreach ($puntoVentas as $puntoVenta) {
        	$ivaVentas = ($puntoVenta['PuntoVenta']['ivaVentas'])?'SI':'NO';
        	
        	
        	$rows[] = array(
                $puntoVenta['PuntoVenta']['id'],
                $puntoVenta['PuntoVenta']['cuit'],
            
                $puntoVenta['PuntoVenta']['numero'],
                $puntoVenta['PuntoVenta']['alicuota'],
                $puntoVenta['PuntoVenta']['descripcion'],
                $puntoVenta['PuntoVenta']['direccion'],
                
                $ivaVentas
            );
            
        }
       
        
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    
	public function crear(){
        $this->layout = 'form';
		
        
    }
    
	public function editar($id = null){
        $this->layout = 'form';
        
		$this->PuntoVenta->id = $id;
        $this->request->data = $this->PuntoVenta->read();
        
		
        //$this->set('unidad', $this->PuntoVenta->read());
    }
    
    
	public function guardar(){

       
        
        //print_r($this->request->data);
        if(!empty($this->request->data)) {

         	//vaildo reserva
            $unidad = $this->request->data['PuntoVenta'];
            $this->PuntoVenta->set($unidad);
            if(!$this->PuntoVenta->validates()){
                $errores['PuntoVenta'] = $this->PuntoVenta->validationErrors;
            }

            //muestro resultado
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
                
                $this->PuntoVenta->save();

                

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
