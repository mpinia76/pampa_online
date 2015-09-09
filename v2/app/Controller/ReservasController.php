<?php ini_set('memory_limit', '-1');
session_start();
class ReservasController extends AppController {
    
	public $scaffold;
    public $components = array('Mpdf'); 
   
		
    public function index(){
	    $this->layout = 'index';
	  
	    if(isset($this->data['year'])){
		  $_SESSION['year'] = $this->data['year'];
		  $_SESSION['month'] = $this->data['month'];
		}else{
		  $_SESSION['year'] = '2015';
		  $_SESSION['month'] = 'Todos';
		}
	 	
    }
    
    public function index_restringido(){
	   $this->layout = 'index';
	   $_SESSION['restricted'] = 'true'; 
	   if(isset($this->data['year'])){
		  $_SESSION['year'] = $this->data['year'];
		  $_SESSION['month'] = $this->data['month'];
		}else{
		  $_SESSION['year'] = '2014';
		  $_SESSION['month'] = '01';
		}
    }
    
     public function get_reservas_restringidas($year, $month) {
        $from = $year .'-'. $month .'-01 00:00:00';
	$to = $year .'-'. $month .'-31 00:00:00';
	$result = Cache::read('get_reservas_restringidas', 'long');
        if (!$result) {
	 $result = $this->Reserva->find('all',array('order' => 'Reserva.id desc', 'conditions' => array('Reserva.check_out between ? and ?' => array($from, $to))));
	 Cache::write('get_reservas_restringidas', $result, 'long');
	}
        return $result;
    }
	 
	 
      public function get_reservas($year, $month) {
	   	 
		if($month == 'Todos'){
		    $from = $year .'-01-01';
	              $to = $year .'-12-31';
		}else{
		    $from = $year .'-'. $month .'-01';
		    $to = $year .'-'. $month .'-31';
		}
		
		$result = Cache::read('get_reservas', 'long');
                if (!$result) {
	         $result = $this->Reserva->find('all',array('order' => 'Reserva.id desc', 'conditions' => array('Reserva.check_in between ? and ?' => array($from, $to))));
	       Cache::write('get_reservas', $result, 'long');
	       }
          return $result;
    }
	
    public function dataTable($columnas_extras = 'todas', $order = 'Reserva.id desc'){ 
           
            $rows = array();
	 	   
	    $year = $_SESSION['year'];
	    $month = $_SESSION['month'];
	    $restricted = $_SESSION['restricted'];
		
	    if ($restricted == 'true') {
	       $reservas = $this->get_reservas_restringidas($year, $month);
	    } else { 
               $reservas = $this->get_reservas($year, $month);
	    } 
        
		 
				
        foreach($reservas as $reserva){
            $adelantadas = 0;
            $no_adelantadas = 0;
            $pagado = 0;
            $fiscal = 0;
            $descontado = 0;
            if(count($reserva['ReservaCobro'])>0){
                foreach($reserva['ReservaCobro'] as $cobro){
                    if($cobro['tipo'] == 'DESCUENTO'){
                        $descontado += $cobro['monto_neto'];
                    }else{
                        if($cobro['tipo'] == 'TARJETA' or $cobro['tipo'] == 'TRANSFERENCIA'){
                            $fiscal += $cobro['monto_cobrado'];
                        }
                        $pagado += $cobro['monto_neto'];
                    }
                }
            } 
            if(count($reserva['ReservaExtra']>0)){
                foreach($reserva['ReservaExtra'] as $extra){
                    if($extra['adelantada'] == 1){
                        $adelantadas = $adelantadas + $extra['cantidad'] * $extra['precio'];
                    }else{
                        $no_adelantadas = $no_adelantadas + $extra['cantidad'] * $extra['precio'];
                    }
                }
            }
            
            $devoluciones = 0;
            if(count($reserva['ReservaDevolucion']) > 0){
                foreach($reserva['ReservaDevolucion'] as $devolucion){
                    $devoluciones += $devolucion['monto'];
                }
            }
            
            $facturado = 0;
            if(count($reserva['ReservaFactura']) > 0){
                foreach($reserva['ReservaFactura'] as $factura){
                    $facturado += $factura['monto'];
                }
            }
            $pendiente = $reserva['Reserva']['total'] + $no_adelantadas - $descontado - $pagado + $devoluciones;
            $total = $reserva['Reserva']['total'] + $no_adelantadas - $descontado;
            
            switch($reserva['Reserva']['estado']){
                case 0:
                    if($pagado == 0){
                        $estado_text = "Cobro pendiente";
                        $estado_num = 0;
                    }elseif($pendiente > 0){
                        $estado_text = "Cobro parcial";
                        $estado_num = 0;
                    }elseif($pendiente == 0){
                        $estado_text = "Cobrado: pendiente de cierre";
                        $estado_num = "FINALIZA";
                    }else{
                        $estado_text = "Revisar";
                        $estado_num = '666';
                    }
                    break;
                case 1:
                    if($facturado >= $fiscal){
                        $estado_text = "Cobrado: facturado";
                        $estado_num = 1;
                    }elseif($facturado < $fiscal){
                        $estado_text = "Cobrado: discrepancia";
                        $estado_num = 1;
                    }else{
                        $estado_text = "Revisar";
                        $estado_num = '666';
                    }
                    break;
                case 2:
                    if($devoluciones > 0){
                        $estado_text = "Cancelada: devolucion parcial";
                    }else{
                        $estado_text = "Cancelada";
                    }
                    $estado_num = 2;
            }
            $row_data = array(
                $reserva['Reserva']['id'],
                $reserva['Reserva']['numero'],
                $reserva['Reserva']['creado'],
                $reserva['Cliente']['nombre_apellido'],
                $reserva['Apartamento']['apartamento'],
                $reserva['Reserva']['check_in'],
                $reserva['Reserva']['check_out']." ".$reserva['Reserva']['late_check_out']
            );
            if($columnas_extras == 'todas'){
                array_push($row_data,
                '$'.$reserva['Reserva']['total_estadia'],
                '$'.$adelantadas,
                '$'.$no_adelantadas,
                '$'.$reserva['Reserva']['total'],
                '$'.$descontado,
                '$'.$total,
                '$'.$pendiente,
                $estado_text,
                $estado_num);
            }else{
                array_push($row_data,
                '$'.$no_adelantadas,
                '$'.$pendiente,
                $estado_text,
                $estado_num);
            }
            $rows[] = $row_data;
        }
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    
    public function cancelar(){
        $this->layout = 'ajax';

        $reserva = $this->Reserva->read(null,$this->request->data['reserva_id']);
        $pagado = 0;
        $descontado = 0;
        if(count($reserva['ReservaCobro'])>0){
            foreach($reserva['ReservaCobro'] as $cobro){
                if($cobro['tipo'] == 'DESCUENTO'){
                    $descontado = $descontado + $cobro['monto_cobrado'];
                }else{
                    $pagado = $pagado + $cobro['monto_cobrado'];
                }
            }
        }
        if($pagado > 0 and count($reserva['ReservaDevolucion']) > 0){
            $this->Reserva->set('estado',2);
            $this->Reserva->save();
            $resultado = 'OK';
            $mensaje = '';
        }elseif($pagado == 0){
            $this->Reserva->set('estado',2);
            $this->Reserva->save();
            $resultado = 'OK';
            $mensaje = '';
        }elseif($pagado > 0 and count($reserva['ReservaDevolucion']) == 0){
            $resultado = 'ERROR';
            $mensaje = 'No se puede cancelar sin realizar alguna devolucion, consulte con el administrador';
        }
        
        $this->set('resultado',$resultado);
        $this->set('mensaje',$mensaje);
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje'
        ));
    }
    
    public function finalizar(){
        $this->layout = 'ajax';

        $reserva = $this->Reserva->read(null,$this->request->data['reserva_id']);

        $fiscal = 0;
        if(count($reserva['ReservaCobro'])>0){
            foreach($reserva['ReservaCobro'] as $cobro){
                if($cobro['tipo'] == 'TARJETA' or $cobro['tipo'] == 'TRANSFERENCIA'){
                    $fiscal += $cobro['monto_cobrado'];
                }
            }
        }
        
        $facturado = 0;
        if(count($reserva['ReservaFactura']) > 0){
            foreach($reseva['ReservaFactura'] as $factura){
                $facturado += $factura['monto'];
            }
        }
        
        if($facturado >= $fiscal){
            $resultado = 'OK';
            $mensaje = '';
        }else{
            $resultado = 'ERROR';
            $mensaje = 'El monto de la/las facturas realizada es incorrecto, por favor consulte al administrador';
        }
        $this->Reserva->set('estado',1);
        $this->Reserva->save();
        
        $this->set('resultado',$resultado);
        $this->set('mensaje',$mensaje);
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje'
        ));
    }
    
    public function crear(){
        $this->layout = 'form';
        
        //ultimo numero de reserva
        $ultima_reserva = $this->Reserva->find('first',array('order' => array('Reserva.id' => 'desc')));
        $ultimo_nro = $ultima_reserva['Reserva']['numero'] + 1;
        $this->set('ultimo_nro',$ultimo_nro);
        
        //lista de empleados de reservas, tengo que ir a buscar por sector de trabajo 
        $this->loadModel('EmpleadoTrabajo');
        $sectores = $this->EmpleadoTrabajo->find('all',array('order' => array('EmpleadoTrabajo.id ASC'),'conditions' => array('EmpleadoTrabajo.sector_1_id' => 1, 'Empleado.estado' => 1)));
        foreach($sectores as $sector){
            $empleados[$sector['Empleado']['id']] = $sector['Empleado']['nombre']." ".$sector['Empleado']['apellido'];
        }
        $this->set('empleados',$empleados);
        
        //lista de apartamentos
        $this->set('apartamentos', $this->Reserva->Apartamento->find('list'));
        
        //iva
        $this->set('iva_ops', array('Responsable Inscripto' => 'Responsable Inscripto', 'Excento' => 'Excento', 'Consumidor Final' => 'Consumidor Final', 'Monotributo' => 'Monotributo'));

        //lista de extra rubros
        $this->loadModel('ExtraRubro');
        $this->set('extra_rubros',$this->ExtraRubro->find('list', array('conditions' => array('extra_variables' => 0))));

    }
    
    public function editar($id = null){
        $this->layout = 'form';
        
        $this->loadModel('ExtraRubro');
        $this->set('extra_rubros',$this->ExtraRubro->find('list', array('conditions' => array('extra_variables' => 0))));
        
        $extras = $this->Reserva->ReservaExtra->find('all',array('conditions' => array('reserva_id' => $id, 'adelantada' => 1, 'extra_id !=' => 0),'recursive' => 2));
        $this->set('extras',$extras);
        
        //lista de apartamentos
        $this->set('apartamentos', $this->Reserva->Apartamento->find('list'));

        //lista de empleados de reservas, tengo que ir a buscar por sector de trabajo 
        $this->loadModel('EmpleadoTrabajo');
        $sectores = $this->EmpleadoTrabajo->find('all',array('order' => array('EmpleadoTrabajo.id ASC'),'conditions' => array('EmpleadoTrabajo.sector_1_id' => 1, 'Empleado.estado' => 1)));
        foreach($sectores as $sector){
            $empleados[$sector['Empleado']['id']] = $sector['Empleado']['nombre']." ".$sector['Empleado']['apellido'];
        }
        $this->set('empleados',$empleados);
        
        //iva
        $this->set('iva_ops', array('Responsable Inscripto' => 'Responsable Inscripto', 'Excento' => 'Excento', 'Consumidor Final' => 'Consumidor Final', 'Monotributo' => 'Monotributo'));

        $this->Reserva->id = $id;
        $this->request->data = $this->Reserva->read();
        $this->set('reserva', $this->Reserva->read());
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
                        $this->ReservaExtra->deleteAll(array('reserva_id' => $this->Reserva->id, 'adelantada' => 1), false);
                        $i=0;
                        foreach($reservaextras as $extra){
                            $this->ReservaExtra->create();
                            $this->ReservaExtra->set('extra_id',$extra);
                            $this->ReservaExtra->set('cantidad',$this->request->data['ReservaExtraCantidad'][$i]);
                            $this->ReservaExtra->set('precio',$this->request->data['ReservaExtraPrecio'][$i]);
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
    
    public function plantilla($reserva_id){
        $this->layout = 'plantilla';
        
        $this->loadModel('ExtraRubro');
        $this->set('extra_rubros',$this->ExtraRubro->find('list'));
        
        $this->loadModel('ExtraSubrubro');
        $this->set('extra_subrubros',$this->ExtraSubrubro->find('list'));
        
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
        $this->set('reserva',$reserva);
        
        $pagado = 0;
        $descontado = 0;
        if(count($reserva['ReservaCobro'])>0){
            foreach($reserva['ReservaCobro'] as $cobro){
                if($cobro['tipo'] == 'DESCUENTO'){
                    $descontado = $descontado + $cobro['monto_neto'];
                }else{
                    $pagado = $pagado + $cobro['monto_neto'];
                }
            }
        }
        $this->set('pagado',$pagado);
        $this->set('pendiente',$reserva['Reserva']['total'] - $descontado - $pagado);
        $this->set('total',$reserva['Reserva']['total'] - $descontado);
        
        //genero el pdf
        $this->Mpdf->init(); 
        $this->Mpdf->setFilename('reserva('.$reserva['Reserva']['numero'].')_'.$reserva['Cliente']['nombre_apellido'].'_plantilla_'.date('d_m_Y').'.pdf'); 
        $this->Mpdf->setOutput('D'); 
    }
    

    //protege el controlador solo para usuarios
    public function beforeFilter(){
        if(isset($_COOKIE['userid'])){
            $this->loadModel('Usuario');
            $this->set('usuario',$this->Usuario->findById($_COOKIE['userid']));
        }else{
            $this->redirect('/index');
        }
    }
}
?>
