<?php
ini_set('memory_limit', '-1');
session_start();
class ReservasController extends AppController {

	public $scaffold;
    public $components = array('Mpdf');

	public function dateFormatSQL($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }

    public function index(){

	    $this->layout = 'index';

		$_SESSION['restricted'] = 'false';
		$_SESSION['desde'] = '';
		$_SESSION['hasta'] = '';
	    if((isset($this->data['year']))&&(sizeof($this->data['year']))>0){
          $_SESSION['year'] = array_pop($this->data['year']);
		  $_SESSION['month'] = $this->data['month'];
		}else{
          $_SESSION['year'] = date('Y');
		  $_SESSION['month'] = 'Todos';
		}
		if (isset($this->data['desde'])) {
			$_SESSION['desde'] = $this->data['desde'];
		}
    	if (isset($this->data['hasta'])) {
			$_SESSION['hasta'] = $this->data['hasta'];
		}
		  $this->setLogUsuario('Ventas');

    }

    public function index_restringido(){
	   $this->layout = 'index';
	   $_SESSION['restricted'] = 'true';
	   if((isset($this->data['year']))&&(sizeof($this->data['year']))>0){
          $_SESSION['year'] = array_pop($this->data['year']);
          $_SESSION['month'] = $this->data['month'];
		}else{
		  $_SESSION['year'] = date('Y');
		  $_SESSION['month'] = '01';
		}
		  $this->setLogUsuario('Carga de extras y facturas');

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


   public function get_reservas($year, $month, $desde, $hasta, $offset, $limit, $orderField, $orderType, $search) {
		//echo 'busqueda: '.$search;
		if($month == 'Todos'){
		    $from = $year .'-01-01';
	            $to = $year .'-12-31';
		}else{
		    $from = $year .'-'. $month .'-01';
		    $to = $year .'-'. $month .'-31';
		}
		 $estado = explode('_', $search);
		$search = (($estado[1])&&($estado[0]=='EST'))?'':$search;
		$filtroEstado='1=1';
		$sinPaginado=0;
		if(($estado[1])&&($estado[0]=='EST')){
			$sinPaginado=1;
			switch ($estado[1]) {
				case 1:
				 $filtroEstado='Reserva.estado=0';
				break;
				case 2:
				 $filtroEstado='Reserva.estado=0';
				break;
				case 3:
				 $filtroEstado='Reserva.estado=0';
				break;
				case 4:
				 $filtroEstado='Reserva.estado=1';
				break;
				case 5:
				 $filtroEstado='Reserva.estado=1';
				break;
				case 6:
				 $filtroEstado='Reserva.estado=2';
				break;
				case 7:
				 $filtroEstado='Reserva.estado=2';
				break;
			}
		}
		if (($desde!='')&&($hasta!='')) {




			$condicion=array($filtroEstado,'Reserva.check_in between ? and ?' => array($from, $to),'Reserva.creado between ? and ?' => array($this->dateFormatSQL($desde), $this->dateFormatSQL($hasta)),
			    'or' =>
	        	  array('Reserva.numero LIKE '=>'%'.$search.'%', 'Cliente.nombre_apellido LIKE '=>'%'.$search.'%', 'Apartamento.apartamento LIKE '=>'%'.$search.'%', 'Reserva.total LIKE '=>'%'.$search.'%'
			    ));
		}
		else $condicion=array($filtroEstado,'Reserva.check_in between ? and ?' => array($from, $to),'or' =>
	        	  array('Reserva.numero LIKE '=>'%'.$search.'%', 'Cliente.nombre_apellido LIKE '=>'%'.$search.'%', 'Apartamento.apartamento LIKE '=>'%'.$search.'%', 'Reserva.total LIKE '=>'%'.$search.'%'
			    ));
		$result = Cache::read('get_reservas', 'long');
        if (!$result) {
        	$order = $orderField.' '.$orderType;
        	//echo $order;
	        if ($sinPaginado) {
	        	$result = $this->Reserva->find('all',array('order' => $order, 'conditions' => $condicion));
	        }
	        else {

	        	$result = $this->Reserva->find('all',array('order' => $order, 'conditions' => $condicion, 'limit'=>$limit, 'offset'=>$offset));
	        }

	       Cache::write('get_reservas', $result, 'long');
	    }
        return $result;
    }

	public function get_reservascount($year, $month, $desde, $hasta, $search) {

		if($month == 'Todos'){
		    $from = $year .'-01-01';
	            $to = $year .'-12-31';
		}else{
		    $from = $year .'-'. $month .'-01';
		    $to = $year .'-'. $month .'-31';
		}
	 	$estado = explode('_', $search);
		$search = (($estado[1])&&($estado[0]=='EST'))?'':$search;
		$filtroEstado='1=1';
		if(($estado[1])&&($estado[0]=='EST')){
			switch ($estado[1]) {
				case 1:
				 $filtroEstado='Reserva.estado=0';
				break;
				case 2:
				 $filtroEstado='Reserva.estado=0';
				break;
				case 3:
				 $filtroEstado='Reserva.estado=0';
				break;
				case 4:
				 $filtroEstado='Reserva.estado=1';
				break;
				case 5:
				 $filtroEstado='Reserva.estado=1';
				break;
				case 6:
				 $filtroEstado='Reserva.estado=2';
				break;
				case 7:
				 $filtroEstado='Reserva.estado=2';
				break;
			}
		}
		if (($desde!='')&&($hasta!='')) {
			$condicion=array($filtroEstado,'Reserva.check_in between ? and ?' => array($from, $to),'Reserva.creado between ? and ?' => array($this->dateFormatSQL($desde), $this->dateFormatSQL($hasta)),
			    'or' =>
	        	  array('Reserva.numero LIKE '=>'%'.$search.'%', 'Cliente.nombre_apellido LIKE '=>'%'.$search.'%', 'Apartamento.apartamento LIKE '=>'%'.$search.'%', 'Reserva.total LIKE '=>'%'.$search.'%'
			    ));
		}
		else $condicion=array($filtroEstado,'Reserva.check_in between ? and ?' => array($from, $to),'or' =>
	        	  array('Reserva.numero LIKE '=>'%'.$search.'%', 'Cliente.nombre_apellido LIKE '=>'%'.$search.'%', 'Apartamento.apartamento LIKE '=>'%'.$search.'%', 'Reserva.total LIKE '=>'%'.$search.'%'
			    ));
		$result = Cache::read('get_reservascount', 'long');
        if (!$result) {
	         $result = $this->Reserva->find('count',array('conditions' => $condicion));

	       Cache::write('get_reservascount', $result, 'long');
	       }
          return $result;
    }

    public function dataTable($columnas_extras = 'todas', $order = 'Reserva.id desc'){
		//print_r($_GET);
		//$orderField= ($_GET['iSortCol_0'])? $_GET['iSortCol_0']:'Reserva.id';

		switch ($_GET['iSortCol_0']) {
			case 1:
			$orderField='Reserva.numero';
			break;
			case 2:
			$orderField='Reserva.creado';
			break;
			case 3:
			$orderField='Cliente.nombre_apellido';
			break;
			case 4:
			$orderField='Apartamento.apartamento';
			break;
			case 5:
			$orderField='Reserva.check_in';
			break;
			case 6:
			$orderField='Reserva.check_out';
			break;
			case 7:
			$orderField='Reserva.total';
			break;
			default:
			$orderField='Reserva.id';
			break;
		}

		$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'desc';
            $rows = array();

	    $year = $_SESSION['year'];
	    $month = $_SESSION['month'];
	    $restricted = $_SESSION['restricted'];

	    $desde = $_SESSION['desde'];
	    $hasta = $_SESSION['hasta'];


	    if ($restricted == 'true') {
	       $reservas = $this->get_reservas_restringidas($year, $month);
	    } else {


               $reservas = $this->get_reservas($year, $month, $desde, $hasta, $_GET['iDisplayStart'], $_GET['iDisplayLength'], $orderField, $orderType, $_GET['sSearch']);
               $iTotal = $this->get_reservascount($year, $month, $desde, $hasta, $_GET['sSearch']);
	    }

		$estado = explode('_', $_GET['sSearch']);


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
            //if(count($reserva['ReservaExtra']>0)){
                foreach($reserva['ReservaExtra'] as $extra){
                    if($extra['adelantada'] == 1){
                        $adelantadas = $adelantadas + $extra['cantidad'] * $extra['precio'];
                    }else{
                        $no_adelantadas = $no_adelantadas + $extra['cantidad'] * $extra['precio'];
                    }
                }
            //}

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
            $pendiente = round(round($reserva['Reserva']['total'],2) + round($no_adelantadas,2) - round($descontado,2) - round($pagado,2) + round($devoluciones,2),2);
            $pendiente = ($pendiente==-0)?0:$pendiente;
            $total = $reserva['Reserva']['total'] + $no_adelantadas - $descontado;
			$contar = (($estado[1])&&($estado[0]=='EST'))?0:1;
			//echo 'Total: '.$reserva['Reserva']['total'].' No adelantadas: '.$no_adelantadas.' Descuento: '.$descontado.' Pagado: '.$pagado.' Devolucion: '.$devoluciones.' Pendiente: '.$pendiente;
            switch($reserva['Reserva']['estado']){
                case 0:
                    if($pagado == 0){
                        $estado_text = "Cobro pendiente";
                        if (($estado[1])&&($estado[0]=='EST')) {
                        	$contar = ($estado[1]==1)?1:0;
                        }
                        $estado_num = 0;
                    }elseif($pendiente > 0){
                        $estado_text = "Cobro parcial";
                    	if (($estado[1])&&($estado[0]=='EST')) {
                        	$contar = ($estado[1]==2)?1:0;
                        }
                        $estado_num = 0;
                    }elseif($pendiente == 0){
                        $estado_text = "Cobrado: pendiente de cierre";
                    	if (($estado[1])&&($estado[0]=='EST')) {
                        	$contar = ($estado[1]==3)?1:0;
                        }
                        $estado_num = "FINALIZA";
                    }else{
                        $estado_text = "Revisar";
                    	if (($estado[1])&&($estado[0]=='EST')) {
                        	$contar = ($estado[1]==8)?1:0;
                        }
                        $estado_num = '666';
                    }
                    break;
                case 1:
                    if($facturado >= $fiscal){
                        $estado_text = "Cobrado: facturado";
                    	if (($estado[1])&&($estado[0]=='EST')) {
                        	$contar = ($estado[1]==4)?1:0;
                        }
                        $estado_num = 1;
                    }elseif($facturado < $fiscal){
                        $estado_text = "Cobrado: discrepancia";
                    	if (($estado[1])&&($estado[0]=='EST')) {
                        	$contar = ($estado[1]==5)?1:0;
                        }
                        $estado_num = 1;
                    }else{
                        $estado_text = "Revisar";
                    	if (($estado[1])&&($estado[0]=='EST')) {
                        	$contar = ($estado[1]==8)?1:0;
                        }
                        $estado_num = '666';
                    }
                    break;
                case 2:
                    if($devoluciones > 0){
                    	if ($devoluciones==$pagado) {
                    		$estado_text = "Cancelada";
	                    	if (($estado[1])&&($estado[0]=='EST')) {
	                        	$contar = ($estado[1]==7)?1:0;
	                        }
                    	}
                    	else{
                        	$estado_text = "Cancelada: devolucion parcial";
	                    	if (($estado[1])&&($estado[0]=='EST')) {
	                        	$contar = ($estado[1]==6)?1:0;
	                        }
                    	}
                    }else{
                        $estado_text = "Cancelada";
                    	if (($estado[1])&&($estado[0]=='EST')) {
                        	$contar = ($estado[1]==7)?1:0;
                        }
                    }
                    $estado_num = 2;
                    break;
               case 3:

                    $estado_text = "Apartamento Bloqueado";

                    $estado_num = 3;
                    break;
            }
            if ($contar) {
            	$row_data = array(
	                $reserva['Reserva']['id'],
	                $reserva['Reserva']['numero'],
	                $reserva['Reserva']['creado'],
	                $reserva['Cliente']['nombre_apellido'],
	                $reserva['Apartamento']['apartamento'],
	                $reserva['Reserva']['check_in']." ".$reserva['Reserva']['hora_check_in'],
	                $reserva['Reserva']['check_out']." ".$reserva['Reserva']['late_check_out']
	            );
	            $planillaEnviada = ($reserva['Reserva']['planilla'])?'<img src="../img/ok.gif">('.$reserva['Reserva']['planilla'].')':'<img src="../img/bt_anular.png">';
	            $voucherEnviada = ($reserva['Reserva']['voucher'])?'<img src="../img/ok.gif">('.$reserva['Reserva']['voucher'].')':'<img src="../img/bt_anular.png">';
	            if($columnas_extras == 'todas'){
	                array_push($row_data,
	                '$'.$reserva['Reserva']['total_estadia'],
	                '$'.$adelantadas,
	                '$'.$no_adelantadas,
	                '$'.round($reserva['Reserva']['total']+$no_adelantadas,2),
	                '$'.$descontado,
	                '$'.$total,
	                '$'.$pendiente,
	                $estado_text,
	                $estado_num,
	                $planillaEnviada,
	                $voucherEnviada);
	            }else{
	                array_push($row_data,
	                '$'.$no_adelantadas,
	                '$'.$descontado,
	                '$'.$pendiente,
	                $estado_text,
	                $estado_num,
	                $planillaEnviada,
	                $voucherEnviada);
	            }
	            $rows[] = $row_data;
            }

        }
        $output = array(
        	"sEcho" => intval($_GET['sEcho']),
        	"iTotalRecords" => count($rows),
	        "iTotalDisplayRecords" => $iTotal,
	        "aaData" => array()
	    );

        $output['aaData'] = $rows;
        $this->set('aoData',$output);
        //print_r($output);
       	$this->set('_serialize',
            'aoData'
        );

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

    public function check_in(){
        $this->layout = 'ajax';

        $this->loadModel('Usuario');
        $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));

        $permisoCheckIn=1;

        if ($user['Usuario']['admin'] != '1'){
            $this->loadModel('UsuarioPermiso');
            $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
            $permisoCheckIn=0;

            foreach($permisos as $permiso){
                if ($permiso['UsuarioPermiso']['permiso_id']==146) {
                    $permisoCheckIn=1;
                    continue;
                }


            }
        }

        if ($permisoCheckIn){
            $reserva = $this->Reserva->read(null,$this->request->data['reserva_id']);

            $hoy = date('Y-m-d');
            //echo $hoy.' >= '.$reserva['Reserva']['check_in'];
            $date_parts = explode("/",$reserva['Reserva']['check_in']);
            $yy=$date_parts[2];
            $mm=$date_parts[1];
            $dd=$date_parts[0];
            $dateCheckInStr = $yy.'-'.$mm.'-'.$dd;
            if ($hoy>=$dateCheckInStr){
                //$checkIn = ($reserva['checkIn'])?0:1;

                $this->Reserva->set('checkIn',1);
                $this->Reserva->save();
                $resultado = 'OK';
                $mensaje = '';
            }
            else{
                $resultado = 'ERROR';
                $mensaje = 'La fecha de check in debe ser igual o anterior a hoy';
            }


        }
       else{
           $resultado = 'ERROR';
           $mensaje = 'No tiene permiso';
       }


        $this->set('resultado',$resultado);
        $this->set('mensaje',$mensaje);

        $this->set('_serialize', array(
            'resultado',
            'mensaje'
        ));
    }

    public function check_out(){
        $this->layout = 'ajax';

        $this->loadModel('Usuario');
        $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));

        $permisoCheckOut=1;

        if ($user['Usuario']['admin'] != '1'){
            $this->loadModel('UsuarioPermiso');
            $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
            $permisoCheckOut=0;

            foreach($permisos as $permiso){
                if ($permiso['UsuarioPermiso']['permiso_id']==147) {
                    $permisoCheckOut=1;
                    contOutue;
                }


            }
        }

        if ($permisoCheckOut){
            $reserva = $this->Reserva->read(null,$this->request->data['reserva_id']);

            if ($reserva['Reserva']['checkIn']){
                $this->Reserva->set('checkOut',1);
                $this->Reserva->save();
                $resultado = 'OK';
                $mensaje = '';
            }
            else{
                $resultado = 'ERROR';
                $mensaje = 'Aun no se hizo el Check In';
            }


        }
        else{
            $resultado = 'ERROR';
            $mensaje = 'No tiene permiso';
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

    public function crear($grilla=null, $apartamento_id=null, $checkIn=null, $checkOut=null){
        $this->layout = 'form';
		$this->set('grilla',$grilla);
        //ultimo numero de reserva
        $ultima_reserva = $this->Reserva->find('first',array('order' => array('Reserva.id' => 'desc')));
        $ultimo_nro = $ultima_reserva['Reserva']['numero'] + 1;
        $this->set('ultimo_nro',$ultimo_nro);

        if ($checkIn) {
        	$date = new DateTime($checkIn);
        	$checkIn = $date->format('d/m/Y');
        	$this->set('checkIn',$checkIn);
        }



         if ($checkOut) {
        	$date = new DateTime($checkOut);
        	$checkOut = $date->format('d/m/Y');
        	$this->set('checkOut',$checkOut);
        }



        //lista de empleados de reservas, tengo que ir a buscar por sector de trabajo
        $this->loadModel('EmpleadoTrabajo');
    	//$sectores = $this->EmpleadoTrabajo->find('all',array('order' => array('EmpleadoTrabajo.id ASC'),'conditions' => array('EmpleadoTrabajo.sector_1_id' => 1, 'Empleado.estado' => 1)));

        $empleadosTrabajo = $this->EmpleadoTrabajo->find('all',array('fields'=>array('max(EmpleadoTrabajo.id) as id'), 'group' => array('EmpleadoTrabajo.empleado_id'), 'conditions' => array( 'Empleado.estado ' => 1 )));

        foreach($empleadosTrabajo as $empleadoTrabajo){

        	$this->EmpleadoTrabajo->id = $empleadoTrabajo[0]['id'];
        	$sector = $this->EmpleadoTrabajo->read();

        	if ($sector['EmpleadoTrabajo']['sector_1_id']==1) {
        		$empleados[$sector['Empleado']['id']] = $sector['Empleado']['nombre']." ".$sector['Empleado']['apellido'];
        	}

        }
        $this->set('empleados',$empleados);

        $sexos = array ('1'=>'Masculino','2'=>'Femenino');


        $this->set('sexos',$sexos);


        $this->loadModel('Canal');

        //lista de lugares
        $this->set('canals', $this->Canal->find('list',array('order' => array('Canal.canal ASC'))));

        //lista de apartamentos
        $this->set('apartamentos', $this->Reserva->Apartamento->find('list'));
    	if ($apartamento_id) {
        	$this->set('defaultApartamento',$apartamento_id);
        }

        //iva
        $this->set('iva_ops', array('Responsable Inscripto' => 'Responsable Inscripto', 'Excento' => 'Excento', 'Monotributo' => 'Monotributo'));

        $this->set('tipoDocumento_ops', array('DNI' => 'DNI', 'Pasaporte' => 'Pasaporte'));
        $this->set('tipoTelefono_ops', array('Fijo' => 'Fijo', 'Celular' => 'Celular'));
        $this->set('tipoPersona_ops', array('Fisica' => 'Fisica', 'Juridica' => 'Juridica'));

        //lista de extra rubros
        $this->loadModel('ExtraRubro');
        $this->set('extra_rubros',$this->ExtraRubro->find('list', array('conditions' => array('extra_variables' => 0))));

    }

	public function getSubcanals($canal_id){
        $this->layout = 'ajax';

        $this->set('subcanals', $this->Reserva->Subcanal->find('list',array('order' => array('Subcanal.subcanal ASC'), 'conditions' =>array('Subcanal.canal_id =' => $canal_id))));

    }

    public function editar($id = null,$grilla=null){
        $this->layout = 'form';
		$this->set('grilla',$grilla);
        $this->loadModel('ExtraRubro');
        $this->set('extra_rubros',$this->ExtraRubro->find('list', array('conditions' => array('extra_variables' => 0))));

        $extras = $this->Reserva->ReservaExtra->find('all',array('conditions' => array('reserva_id' => $id, 'adelantada' => 1, 'extra_id !=' => 0),'recursive' => 2));
        $this->set('extras',$extras);

        //lista de apartamentos
        $this->set('apartamentos', $this->Reserva->Apartamento->find('list'));

        //lista de empleados de reservas, tengo que ir a buscar por sector de trabajo
        $this->loadModel('EmpleadoTrabajo');
    	//$sectores = $this->EmpleadoTrabajo->find('all',array('order' => array('EmpleadoTrabajo.id ASC'),'conditions' => array('EmpleadoTrabajo.sector_1_id' => 1, 'Empleado.estado' => 1)));

        $empleadosTrabajo = $this->EmpleadoTrabajo->find('all',array('fields'=>array('max(EmpleadoTrabajo.id) as id'), 'group' => array('EmpleadoTrabajo.empleado_id'), 'conditions' => array( 'Empleado.estado ' => 1 )));

        foreach($empleadosTrabajo as $empleadoTrabajo){

        	$this->EmpleadoTrabajo->id = $empleadoTrabajo[0]['id'];
        	$sector = $this->EmpleadoTrabajo->read();

        	if ($sector['EmpleadoTrabajo']['sector_1_id']==1) {
        		$empleados[$sector['Empleado']['id']] = $sector['Empleado']['nombre']." ".$sector['Empleado']['apellido'];
        	}

        }
        $this->set('empleados',$empleados);

         $this->loadModel('Canal');

        //lista de lugares
        $this->set('canals', $this->Canal->find('list',array('order' => array('Canal.canal ASC'))));

        $this->set('subcanals', $this->Reserva->Subcanal->find('list',array('order' => array('Subcanal.subcanal ASC'))));

        //iva
        $this->set('iva_ops', array('Responsable Inscripto' => 'Responsable Inscripto', 'Excento' => 'Excento', 'Monotributo' => 'Monotributo'));

        $this->Reserva->id = $id;
        $this->request->data = $this->Reserva->read();
        $reserva = $this->request->data;
        //print_r($reserva);
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
            $pendiente = round(round($reserva['Reserva']['total'],2) + round($no_adelantadas,2) - round($descontado,2) - round($pagado,2) + round($devoluciones,2),2);
            $pendiente = ($pendiente==-0)?0:$pendiente;


        $sexos = array ('1'=>'Masculino','2'=>'Femenino');

        $this->set('defaultSexo',$reserva['Cliente']['sexo']);
        $this->set('sexos',$sexos);

        //tipo documento

        $this->set('tipoDocumento_ops', array('DNI' => 'DNI', 'Pasaporte' => 'Pasaporte'));
        $this->set('tipoTelefono_ops', array('Fijo' => 'Fijo', 'Celular' => 'Celular'));
        $this->set('tipoPersona_ops', array('Fisica' => 'Fisica', 'Juridica' => 'Juridica'));

        $this->set('pendiente', $pendiente);
        $this->set('reserva', $this->Reserva->read());
    }

	public function modificarApartamento(){

	    	$reserva = $this->Reserva->read(null,$this->request->data['reserva_id']);





	       //print_r($errores);
            //muestro resultado
            if(isset($errores) and $errores!=''){
                $this->set('resultado','ERROR');
                $this->set('mensaje',$errores);
                $this->set('detalle','');
            }else{

            	$this->Reserva->set('apartamento_id',$this->request->data['apartamento_id']);
            	$date = new DateTime($this->request->data['checkIn']);
        		$checkIn = $date->format('d/m/Y');
            	$this->Reserva->set('check_in',$checkIn);
            	$date = new DateTime($this->request->data['checkOut']);
        		$checkOut = $date->format('d/m/Y');
            	$this->Reserva->set('check_out',$checkOut);
            	//print_r($this->Reserva);
                $this->Reserva->save();





                $this->set('resultado','OK');
                $this->set('mensaje','Reserva modificada');
                $this->set('detalle','');
            }







    	$this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
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



        	if ($cliente['dni']=='') {
            	$errores['Cliente']['dni'][] = 'Ingrese un Documento';
            }

            if ($cliente['tipoDocumento']=='DNI') {
                if (!ctype_digit($cliente['dni'])){
                    $errores['Cliente']['dni'][] = 'Ingrese solo numeros';
                }

            }
            if ($cliente['tipoDocumento']=='DNI') {
                if (strlen($cliente['dni'])!=8){
                    $errores['Cliente']['dni'][] = 'El DNI debe contener 8 digitos';
                }

            }

            if($cliente['codPais'] == '') {
                $cliente['codPais'] = $cliente['codPaisAux'];
            }

            if(($cliente['telefono'] == '') AND ($cliente['celular'] == '')){
            	$errores['Cliente']['telefono'][] = 'Ingrese un telefono o celular valido';
            	$errores['Cliente']['celular'][] = 'Ingrese un telefono o celular valido';
            }

        	if ($cliente['email']=='') {
            	$errores['Cliente']['email'][] = 'Ingrese un E-mail';
            }

        	if ($cliente['email2']=='') {
            	$errores['Cliente']['email2'][] = 'Ingrese un E-mail';
            }

        	if ($cliente['email']!=$cliente['email2']) {
            	$errores['Cliente']['email2'][] = 'Los E-mails son distintos';
            }

            if(($cliente['iva'] != '') AND ($cliente['tipoPersona'] == '')){
                $errores['Cliente']['tipoPersona'][] = 'Seleccione un Tipo de Persona';

            }
            if(($cliente['iva'] != '') AND ($cliente['razon_social'] == '')){
                $errores['Cliente']['razon_social'][] = 'Ingrese una Razon Social';

            }

            if ($cliente['iva'] == ''){


                $this->Cliente->set('razon_social','');
                $this->Cliente->set('cuit','');
                $this->Cliente->set('tipoPersona',null);
                $this->Cliente->set('iva',null);

            }


            //vaildo reserva
            $reserva = $this->request->data['Reserva'];
            $this->Reserva->set($reserva);
            if(!$this->Reserva->validates()){
                $errores['Reserva'] = $this->Reserva->validationErrors;
            }

        	if ($reserva['reservado_por']=='') {
            	$errores['Reserva']['reservado_por'][] = 'Debe seleccionar quien realizo la reserva';
            }

       		if ($reserva['subcanal_id']=='') {
            	$errores['Reserva']['subcanal_id'][] = 'Debe seleccionar un subcanal';
            }

            $checkIn = $reserva['check_in'];
	        $checkOut = $reserva['check_out'];

        	if ($checkIn) {
	        	$date_parts = explode("/",$checkIn);

	        	$hora_checkIn = date('H:i:s', strtotime($reserva['hora_check_in']) - 3600);
	        	$checkIn =  $date_parts[2]."-".$date_parts[1]."-".$date_parts[0].' '.$hora_checkIn;

	        }
			if ($checkOut) {
	        	$date_parts = explode("/",$checkOut);

	        	$hora_checkOut = date('H:i:s', strtotime($reserva['late_check_out']) + 3600);
	        	$checkOut =  $date_parts[2]."-".$date_parts[1]."-".$date_parts[0].' '.$hora_checkOut;
	        }


        	$result = $this->Reserva->find('first', array(
			  'conditions' => array(
			    'Reserva.apartamento_id' => $reserva['apartamento_id'],
	        	'Reserva.id <>' => (isset($reserva['id']))?$reserva['id']:'',
	        	'AND' => array('or' => array(
	        		'Reserva.estado <> ' => 2,
	        		'Reserva.estado ' => null,
	        	)),
			    'or' => array(
	        	  array('CONCAT(Reserva.check_in,\' \',Reserva.hora_check_in) <'=>$checkIn,
	        	  'CONCAT(Reserva.check_out,\' \',Reserva.late_check_out) >'=>$checkOut),
			      'CONCAT(Reserva.check_in,\' \',Reserva.hora_check_in) between ? and ?' => array($checkIn, $checkOut),
			      'CONCAT(Reserva.check_out,\' \',Reserva.late_check_out) between ? and ?' => array($checkIn, $checkOut),
			    )
			  )
			));

			/*App::uses('ConnectionManager', 'Model');
        	$dbo = ConnectionManager::getDatasource('default');
		    $logs = $dbo->getLog();
		    $lastLog = end($logs['log']);

		    echo $lastLog['query'];*/
        	//print_r($result);


	        if ($result) {
	        	$errores['Reserva']['apartamento_id'][]='El apartamento seleccionado se encuentra incluido en otra reserva para la fecha seleccionada: "Numero de reserva '.$result['Reserva']['numero'].'"';

	        }



        	$total=$reserva['total_estadia'];
        	if(array_key_exists('ReservaExtraId',$this->request->data)){
            	$reservaextras = $this->request->data['ReservaExtraId'];
                if($reservaextras and count($reservaextras)>0){

                	$i=0;
                    foreach($reservaextras as $extra){
                    	$total += $this->request->data['ReservaExtraPrecio'][$i]*$this->request->data['ReservaExtraCantidad'][$i];

                        $i++;
                     }
                  }
               }
            //echo $total .' '. $reserva['total'];
	        if ($total != $reserva['total']) {
	        	$errores['Reserva']['total_estadia']='No coinciden los montos';
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
                $this->Reserva->set('estado','0');
                $this->Reserva->save();

                //guardo reserva extras
                 $this->ReservaExtra->deleteAll(array('reserva_id' => $this->Reserva->id, 'adelantada' => 1), false);
                if(array_key_exists('ReservaExtraId',$this->request->data)){
                    $reservaextras = $this->request->data['ReservaExtraId'];
                    if($reservaextras and count($reservaextras)>0){

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

    public function bloquearApartamento(){

        //load modules

        //$this->loadModel('ReservaExtra');


        if(!empty($this->request->data)) {
        	$this->loadModel('Cliente');

        	//print_r($this->request->data);
            //valido cliente
            $cliente = $this->request->data['Cliente'];
            //print_r($cliente);
            $this->Cliente->set($cliente);

            if (($reserva['reservado_por']!='')or($reserva['subcanal_id']!='')or($cliente['dni']!='')or($cliente['telefono']!='')or($cliente['celular']!='')or($cliente['direccion']!='')
            or($cliente['localidad']!='')or($cliente['email']!='')) {
            	$this->guardar();
            }
       		else{

	        	if ($cliente['nombre_apellido']=='') {
	            	$errores['Cliente']['nombre_apellido'] = 'Ingrese un nombre y apellido valido';
	            }



	            //vaildo reserva
	            $reserva = $this->request->data['Reserva'];
	            //print_r($reserva);
	            $this->Reserva->set($reserva);
	            if (($reserva['reservado_por']!='')or($reserva['pax_adultos']!='0')or($reserva['pax_menores']!='0')
	            or($reserva['pax_bebes']!='0')or($reserva['total_estadia']!='0') ){
	            	$this->guardar();
	            }
	       		else{
       		    	if(!$this->Reserva->validates()){
		                $errores['Reserva'] = $this->Reserva->validationErrors;
		            }


		        	$checkIn = $reserva['check_in'];
			        $checkOut = $reserva['check_out'];

		        	if ($checkIn) {
			        	$date_parts = explode("/",$checkIn);

			        	$hora_checkIn = date('H:i:s', strtotime($reserva['hora_check_in']) - 3600);
			        	$checkIn =  $date_parts[2]."-".$date_parts[1]."-".$date_parts[0].' '.$hora_checkIn;

			        }
					if ($checkOut) {
			        	$date_parts = explode("/",$checkOut);

			        	$hora_checkOut = date('H:i:s', strtotime($reserva['late_check_out']) + 3600);
			        	$checkOut =  $date_parts[2]."-".$date_parts[1]."-".$date_parts[0].' '.$hora_checkOut;
			        }


		        	$result = $this->Reserva->find('first', array(
					  'conditions' => array(
					    'Reserva.apartamento_id' => $reserva['apartamento_id'],
			        	'Reserva.id <>' => (isset($reserva['id']))?$reserva['id']:'',
			        	'AND' => array('or' => array(
			        		'Reserva.estado <> ' => 2,
			        		'Reserva.estado ' => null,
			        	)),
					    'or' => array(
			        	  array('CONCAT(Reserva.check_in,\' \',Reserva.hora_check_in) <'=>$checkIn,
			        	  'CONCAT(Reserva.check_out,\' \',Reserva.late_check_out) >'=>$checkOut),
					      'CONCAT(Reserva.check_in,\' \',Reserva.hora_check_in) between ? and ?' => array($checkIn, $checkOut),
					      'CONCAT(Reserva.check_out,\' \',Reserva.late_check_out) between ? and ?' => array($checkIn, $checkOut),
					    )
					  )
					));

					/*App::uses('ConnectionManager', 'Model');
		        	$dbo = ConnectionManager::getDatasource('default');
				    $logs = $dbo->getLog();
				    $lastLog = end($logs['log']);

				    echo $lastLog['query'];*/
		        	//print_r($result);


			        if ($result) {
			        	$errores['Reserva']['apartamento_id'][]='El apartamento seleccionado se encuentra incluido en otra reserva para la fecha seleccionada: "Numero de reserva '.$result['Reserva']['numero'].'"';

			        }



			        /*$this->loadModel('Unidad');
					$this->Unidad->id = $reserva['unidad_id'];
					$unidad = $this->Unidad->read();*/
					//print_r($unidad);

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
		                $this->Reserva->set('estado','3');
		                $this->Reserva->save();



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
    }


    public function plantilla($reserva_id, $pdf=0, $output='D'){
        $this->layout = 'plantilla';

        $this->loadModel('ExtraRubro');
        $this->set('extra_rubros',$this->ExtraRubro->find('list'));

        $this->set('pdf',$pdf);


        $this->loadModel('ExtraSubrubro');
        $this->set('extra_subrubros',$this->ExtraSubrubro->find('list'));

        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();

        $telefono ='';
        if ($reserva['Cliente']['tipoTelefono']=='Fijo'){
            $telefono = $reserva['Cliente']['codPais'].' '.$reserva['Cliente']['codArea'].' '.$reserva['Cliente']['telefono'];
        }
        $celular ='';
        if ($reserva['Cliente']['tipoTelefono']=='Celular'){
            $celular = $reserva['Cliente']['codPais'].' '.$reserva['Cliente']['codArea'].' '.$reserva['Cliente']['telefono'];
        }
        $this->set('telefono',$telefono);
        $this->set('celular',$celular);

        $this->set('reserva',$reserva);

        $extras = $this->Reserva->ReservaExtra->find('all',array('conditions' => array('reserva_id' => $reserva_id),'recursive' => 2));
        $this->set('extras',$extras);


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
    	$devoluciones = 0;
            if(count($reserva['ReservaDevolucion']) > 0){
                foreach($reserva['ReservaDevolucion'] as $devolucion){
                    $devoluciones += $devolucion['monto'];
                }
            }

        $this->set('pagado',round($pagado,2));

        $this->set('pendiente',round($reserva['Reserva']['total'] - $descontado - $pagado + $devoluciones,2));
        $this->set('total',round($reserva['Reserva']['total'] - $descontado + $devoluciones,2));



        //genero el pdf
        if ($pdf) {
        	$this->Mpdf->init();

        	$fileName = ($output=='F')?'files/reserva('.$reserva['Reserva']['numero'].')_'.$reserva['Cliente']['nombre_apellido'].'_plantilla_'.date('d_m_Y').'.pdf':'reserva('.$reserva['Reserva']['numero'].')_'.$reserva['Cliente']['nombre_apellido'].'_plantilla_'.date('d_m_Y').'.pdf';

        	$this->Mpdf->setFilename($fileName);
        	$this->Mpdf->setOutput($output);
        }


    }


	public function formMailPlanilla($reserva_id){
        $this->layout = 'form';



        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_id;
        $reserva = $this->Reserva->read();
        $this->set('reserva',$reserva);
        //print_r($reserva);

    }




 	public function enviarPlanilla(){
 		$this->layout = 'json';

        if(!empty($this->request->data)) {

        	$errores=array();
        	$mails=$this->request->data['Reserva']['mails'];
        	$mailsArray=explode(",",$mails);
        	$this->loadModel('EmailValidate');

        	foreach ($mailsArray as $mail) {
        		$this->EmailValidate->set('email',trim($mail));

        		//print_r($this->EmailValidate);
	        	 if(!$this->EmailValidate->validates()){


	                $errores['Reserva']['mails'][] = 'Error en el/los mail/s';
	            }

        	}
        	$mails=$this->request->data['Reserva']['mailsCC'];
        	$mailsArray=explode(",",$mails);
        	$this->loadModel('EmailValidate');

        	foreach ($mailsArray as $mail) {
        		$this->EmailValidate->set('email',trim($mail));

        		//print_r($this->EmailValidate);
	        	 if(!$this->EmailValidate->validates()){


	                $errores['Reserva']['mailsCC'][] = 'Error en el/los mail/s';
	            }

        	}
        	$mails=$this->request->data['Reserva']['mailsCCO'];
        	$mailsArray=explode(",",$mails);
        	$this->loadModel('EmailValidate');

        	foreach ($mailsArray as $mail) {
        		$this->EmailValidate->set('email',trim($mail));

        		//print_r($this->EmailValidate);
	        	 if(!$this->EmailValidate->validates()){


	                $errores['Reserva']['mailsCCO'][] = 'Error en el/los mail/s';
	            }

        	}

            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo enviar');
                $this->set('detalle',$errores);
            }else{
            	$this->loadModel('Reserva');
		        $this->Reserva->id = $this->request->data['Reserva']['reserva_id'];
		        $reserva = $this->Reserva->read();
            	$fileName = 'reserva('.$reserva['Reserva']['numero'].')_'.$reserva['Cliente']['nombre_apellido'].'_plantilla_'.date('d_m_Y').'.pdf';
            	$file ='files/'.$fileName;

            	if(is_file($file)){

				        $fp =    @fopen($file,"rb");
				        $data =  @fread($fp,filesize($file));

				        @fclose($fp);


						$attachment = chunk_split(base64_encode($data));
            	}

				$mail.=$persona;



		$textMessage = '';
		$asunto = 'IMPORTANTE DAR DE ALTA E IMPRIMIR PLANILLA '.$reserva['Cliente']['nombre_apellido'].' '.$reserva['Apartamento']['apartamento'].' '.$reserva['Reserva']['check_in'];

	    $separator = md5(time());
		// carriage return type (we use a PHP end of line constant)
		$eol = PHP_EOL;
		// attachment name


		// main header (multipart mandatory)
		$headers  = "From: Village de las Pampas Apart Hotel Boutique <info@villagedelaspampas.com.ar> ".$eol;

	    $headers .= "Return-path: Village de las Pampas Apart Hotel Boutique <info@villagedelaspampas.com.ar> ".$eol;
	   //$headers .= "CC: ".$this->request->data['Reserva']['mailsCC']." \r\n";
	    $headers .= "BCC: ".$this->request->data['Reserva']['mailsCCO']." \r\n";
		$headers .= "MIME-Version: 1.0".$eol;
		$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"".$eol.$eol;
		$headers .= "Content-Transfer-Encoding: 7bit".$eol;
		$headers .= "This is a MIME encoded message.".$eol.$eol;
		// message
		$headers .= "--".$separator.$eol;
		$headers .= "Content-Type: text/html; charset=\"utf8\"".$eol;
		$headers .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
		$headers .= $textMessage.$eol.$eol;
		// attachment
		$headers .= "--".$separator.$eol;
		$headers .= "Content-Type: application/octet-stream; name=\"".$fileName."\"".$eol;
		$headers .= "Content-Transfer-Encoding: base64".$eol;
		$headers .= "Content-Disposition: attachment".$eol.$eol;
		$headers .= $attachment.$eol.$eol;
		$headers .= "--".$separator."--";

		$headers .= "X-Priority: 1 ".$eol;
	    $headers .= "X-MSMail-Priority: High ".$eol;
	    $headers .= "X-Mailer: PHP/".phpversion()." ".$eol;






		if (mail($this->request->data['Reserva']['mails'], $asunto, $textMessage, $headers, "-finfo@villagedelaspampas.com.ar")){

			$enviada = $reserva['Reserva']['planilla'] + 1;
			$this->Reserva->set('planilla',$enviada);
		    $this->Reserva->save();

			$this->set('resultado','OK');
                $this->set('mensaje','Planilla enviada');
                $this->set('detalle','');
		}
		else{
		 	$this->set('resultado','ERROR');
                $this->set('mensaje','Error al enviar');
                //$this->set('detalle');
            }


          unlink($file);



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
        if(isset($_COOKIE['userid'])){
            $this->loadModel('Usuario');
            $this->set('usuario',$this->Usuario->findById($_COOKIE['userid']));
        }else{
            $this->redirect('/index');
        }
    }
}
?>
