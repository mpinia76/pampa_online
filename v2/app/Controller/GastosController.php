<?php ini_set('memory_limit', '-1');
session_start();
class GastosController extends AppController {
    public $scaffold;
    
   public function index(){
	
	    $this->layout = 'index';
	    
		
		
		$this->loadModel('Rubro');
        $this->set('rubros',$this->Rubro->find('list',array('order' => 'Rubro.rubro asc')));
        
        $this->loadModel('Subrubro');
        $this->set('subrubros',$this->Subrubro->find('list',array('order' => 'Subrubro.subrubro asc')));
    }
    
    public function dataTable(){
	
	    $rows = array();
		$limit = $_SESSION['year'];
		
   
	     $gastos = $this->Gasto->find('all',array('order' => 'Gasto.created desc'));
       
        foreach($gastos as $gasto){
            //estado y nro de orden
            if($gasto['Gasto']['estado'] == 0 and $gasto['Gasto']['nro_orden'] == 0){
                $nro_orden	= 'Pendiente';
                $estado = 'Esperando nro. orden';
            }elseif($gasto['Gasto']['estado'] == 0 and $gasto['Gasto']['nro_orden'] != 0){
                $nro_orden = $gasto['Gasto']['nro_orden'];
                $estado = 'Falta abonar';
            }elseif($gasto['Gasto']['estado'] == 1 and $gasto['Gasto']['nro_orden'] != 0 and $gasto['Gasto']['factura_nro'] == ''){
                $nro_orden 	= $gasto['Gasto']['nro_orden'];
                $estado = 'Falta factura';
            }elseif($gasto['Gasto']['estado'] == 1 and $gasto['Gasto']['nro_orden'] != 0 and $gasto['Gasto']['factura_nro'] != ''){
                $nro_orden 	= $gasto['Gasto']['nro_orden'];
                $estado = 'Procesado';
            }elseif($gasto['Gasto']['estado'] == 2){
                $nro_orden 	= '';
                $estado = 'Desaprobado';
            }
            //proveedor
            if(isset($gasto['Proveedor']['id'])){
                $proveedor = $gasto['Proveedor']['nombre'];
            }else{
                $proveedor = $gasto['Gasto']['proveedor'];
            }
            
            $rows[] = array(
                $gasto['Gasto']['id'],
                $nro_orden,
                $gasto['Gasto']['created'],
                $gasto['Gasto']['fecha'],
                $gasto['Rubro']['rubro'],
                $gasto['Subrubro']['subrubro'],
                $proveedor,
                $gasto['Gasto']['factura_tipo']." ".$gasto['Gasto']['factura_nro'],
                $gasto['Gasto']['monto'],
                $estado
            );
        }
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    public function crear(){
        $this->layout = 'form';
        
        //ultimo numero de reserva
        $ultima_reserva = $this->Reserva->find('first',array('oder' => array('id' => 'desc')));
        $ultimo_nro = $ultima_reserva['Reserva']['numero'] + 1;
        $this->set('ultimo_nro',$ultimo_nro);
        
        //lista de apartamentos
        $this->set('apartamentos', $this->Reserva->Apartamento->find('list'));
        
        //lista de extra rubros
        $this->loadModel('ExtraRubro');
        $this->set('extra_rubros', $this->ExtraRubro->find('list'));

    }
    public function editar($id = null){
        $this->layout = 'form';
        
        $this->loadModel('ExtraRubro');
        $this->loadModel('ExtraSubrubro');
            
        //lista de apartamentos
        $this->set('apartamentos', $this->Reserva->Apartamento->find('list'));
        
        $this->Reserva->id = $id;
        if ($this->request->is("get")) {
            $this->request->data = $this->Reserva->read();
            $this->set('reserva', $this->Reserva->read());
            $this->set('extra_rubros',$this->ExtraRubro->find('list'));
            $this->set('extra_subrubros',$this->ExtraSubrubro->find('list'));
        }
    }

    public function guardar(){
        
        //load modules
        $this->loadModel('Cliente');
        $this->loadModel('ReservaExtra');
        
        //print_r($this->request->data);
        if(!empty($this->request->data)) {
            
            //valido cliente
            $cliente = $this->request->data['Cliente'];
            $this->Cliente->set($cliente);
            if(!$this->Cliente->validates()){
                 $errores['Cliente'] = $this->Cliente->validationErrors;
            }
            
            //vaildo reserva
            $reserva = $this->request->data['Reserva'];
            $this->Reserva->set($reserva);
            if(!$this->Reserva->validates()){
                $errores['Reserva'] = $this->Reserva->validationErrors;
            }

            //muestro resultado
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
                //guardo cliente
                $this->Cliente->save();
                
                //guardo reserva
                $this->Reserva->set('cliente_id',$this->Cliente->id);
                $this->Reserva->save();
                
                //guardo reserva extras
                if(array_key_exists('ReservaExtraId',$this->request->data)){
                    $reservaextras = $this->request->data['ReservaExtraId'];
                    if($reservaextras and count($reservaextras)>0){
                        $this->ReservaExtra->deleteAll(array('reserva_id' => $this->Reserva->id), false);
                        $i=0;
                        foreach($reservaextras as $extra){
                            $this->ReservaExtra->create();
                            $this->ReservaExtra->set('extra_id',$extra);
                            $this->ReservaExtra->set('cantidad',$this->request->data['ReservaExtraCantidad'][$i]);
                            $this->ReservaExtra->set('reserva_id',$this->Reserva->id);
                            $this->ReservaExtra->set('agregada',date('Y-m-d'));
                            $this->ReservaExtra->save();
                            $i++;
                        }
                    }
                }
                
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
    
    //protege el controlador solo para usuarios
    public function beforeFilter(){
        $usuario_accion = '';
        if(isset($_COOKIE['userid'])){
            $this->loadModel('Usuario');
            $this->set('usuario',$this->Usuario->findById($_COOKIE['userid']));
            
            $this->loadModel('UsuarioPermiso');
            $permisos = $this->UsuarioPermiso->findAllByUsuarioId($_COOKIE['userid']);
            $accion = array();
            foreach($permisos as $permiso){
                $usuario_accion[$permiso['UsuarioPermiso']['permiso_id']] = true;
            }
            $this->set('usuario_accion',$usuario_accion);
        }else{
            $this->redirect('/index');
        }
    }
}
?>
