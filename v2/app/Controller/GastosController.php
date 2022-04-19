<?php
ini_set('memory_limit', '-1');
session_start();
class GastosController extends AppController {
    public $scaffold;

	public function dateFormatSQL($dateString) {
		//echo $dateString."<br>";
        $date_parts = explode("/",$dateString);
        switch (count($date_parts)) {
        	case 1:
        	$result = $date_parts[0];
        	break;
        	case 2:
        	$result = $date_parts[1]."-".$date_parts[0];
        	break;
        	default:
        	$result = $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
        	break;
        }
        return $result;
    }

   public function index(){


	    $this->layout = 'index';


		$this->loadModel('Rubro');
        $this->set('rubros',$this->Rubro->find('list',array('conditions'=>array('gastos'=>1,'activo'=>1),'order' => 'Rubro.rubro asc')));

        $this->loadModel('Subrubro');
        $this->set('subrubros',$this->Subrubro->find('list',array('conditions'=>array('activo'=>1),'order' => 'Subrubro.subrubro asc')));

        $this->loadModel('Usuario');
        $this->set('usuario',$this->Usuario->find('list',array('order' => 'Usuario.nombre asc')));

         $this->setLogUsuario('Gastos y compras');

    }

    public function dataTable(){
    	//print_r($_GET);
    	$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'desc';
    	switch ($_GET['iSortCol_0']) {
			case 1:
			$order='Gasto.nro_orden '.$orderType;
			break;
			/*case 2:
			$order='Gasto.orden_pago '.$orderType;
			break;*/
			case 2:
			$order='Gasto.created '.$orderType;
			break;
			case 3:
			$order='Gasto.fecha '.$orderType;
			break;
			case 4:
			$order='Gasto.fecha_vencimiento '.$orderType;
			break;
			case 5:
			$order='Rubro.rubro '.$orderType;
			break;
			case 6:
			$order='Subrubro.subrubro '.$orderType;
			break;
			case 7:
			$order='Proveedor.nombre '.$orderType.', Gasto.proveedor '.$orderType;
			break;
			case 8:
			$order='Gasto.factura_tipo '.$orderType.', Gasto.factura_punto_venta '.$orderType.', Gasto.factura_nro '.$orderType;
			break;
			case 9:
			$order='Gasto.monto '.$orderType;
			break;
			case 11:
			$order='Usuario.nombre '.$orderType.', Usuario.apellido '.$orderType;
			break;
			default:
			$order='Gasto.created '.$orderType;
			break;
		}



	    $rows = array();

        $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$user_id)));
        $espacioTrabajo = $user['EspacioTrabajo']['id'];

        if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $tienePermiso=0;
	    	foreach($permisos as $permiso){
	               if ($permiso['UsuarioPermiso']['permiso_id']==100) {
	               		$tienePermiso=1;
	               		continue;
	               }
	        }
        }
        if (($user['Usuario']['admin'] == '1')||($tienePermiso)){
            $gastos = $this->get_gastos($_GET['iDisplayStart'], $_GET['iDisplayLength'], $order, $_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8'], $_GET['sSearch_10'], $_GET['sSearch_11'], $_GET['sSearch_12']);
            $iTotal = $this->get_gastoscount($_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8'], $_GET['sSearch_10'], $_GET['sSearch_11'], $_GET['sSearch_12']);
            $iMonto = $this->get_gastossum($_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8'], $_GET['sSearch_10'], $_GET['sSearch_11'], $_GET['sSearch_12']);
        }else{
        	$condicionSearch1 = ($_GET['sSearch_1'])?array('Gasto.nro_orden LIKE '=>'%'.$_GET['sSearch_1'].'%'):array();
        	//$condicionSearch2 = ($_GET['sSearch_2'])?array('Gasto.orden_pago LIKE '=>'%'.$_GET['sSearch_2'].'%'):array();
        	$condicionSearch2 = ($_GET['sSearch_2'])?array('Gasto.created LIKE '=> '%'.$this->dateFormatSQL($_GET['sSearch_2']).'%'):array();
        	$condicionSearch3 = ($_GET['sSearch_3'])?array('Gasto.fecha LIKE '=> '%'.$this->dateFormatSQL($_GET['sSearch_3']).'%'):array();
        	$condicionSearch4 = ($_GET['sSearch_4'])?array('Gasto.fecha_vencimiento LIKE '=> '%'.$this->dateFormatSQL($_GET['sSearch_4']).'%'):array();
        	$condicionSearch5 = ($_GET['sSearch_5'])?array('Rubro.rubro = '=>$_GET['sSearch_5']):array();
        	$condicionSearch6 = ($_GET['sSearch_6'])?array('Subrubro.subrubro = '=>$_GET['sSearch_6']):array();
        	$condicionSearch7 = ($_GET['sSearch_7'])?array('or' => array('Proveedor.nombre LIKE '=>'%'.$_GET['sSearch_7'].'%', 'Gasto.proveedor LIKE '=>'%'.$_GET['sSearch_7'].'%')):array();
        	$condicionSearch8 = ($_GET['sSearch_8'])?array('or' => array('Gasto.factura_tipo LIKE '=>'%'.$_GET['sSearch_8'].'%', 'Gasto.factura_punto_venta LIKE '=>'%'.$_GET['sSearch_8'].'%', 'Gasto.factura_nro LIKE '=>'%'.$_GET['sSearch_8'].'%')):array();
        	switch ($_GET['sSearch_10']) {
        		case 'Esperando nro. orden':
        		$condicionSearch10 = array('Gasto.estado = '=>0, 'Gasto.nro_orden = '=>0);
        		break;
        		case 'Falta abonar':
        		$condicionSearch10 = array('Gasto.estado = '=>0, 'Gasto.nro_orden != '=>0, 'Gasto.plan_id '=>null);
        		break;
        		case 'Falta factura':
        		$condicionSearch10 = array('Gasto.estado = '=>1, 'Gasto.nro_orden != '=>0, 'Gasto.factura_nro = '=>'');
        		break;
        		case 'Plan de pagos':
        		$condicionSearch10 = array('Gasto.plan_id != '=>0);
        		break;
        		case 'Procesado':
        		$condicionSearch10 = array('Gasto.estado = '=>1, 'Gasto.nro_orden != '=>0, 'Gasto.factura_nro != '=>'');
        		break;
        		case 'Desaprobado':
        		$condicionSearch10 = array('Gasto.estado = '=>2);
        		break;
        		default:
        			$condicionSearch10 = array();
        		break;
        	}
        	$condicionSearch11 = ($_GET['sSearch_11'])?array('or' => array('Usuario.nombre LIKE '=>'%'.$_GET['sSearch_11'].'%', 'Usuario.apellido LIKE '=>'%'.$_GET['sSearch_11'].'%')):array();
        	if ($_GET['sSearch_12']=='on'){
                $condicion=array('Usuario.espacio_trabajo_id'=>$espacioTrabajo,
                    'Usuario.admin' => 0,$condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
                    'or' =>
                        array('Gasto.descripcion LIKE '=>'%'.$_GET['sSearch'].'%'
                        ));
                $gastos = $this->Gasto->find('all',array('conditions'=>$condicion,
                    'order' => $order, 'limit'=>$_GET['iDisplayLength'], 'offset'=>$_GET['iDisplayStart']));
            }
        	else{
                $condicion=array('Usuario.espacio_trabajo_id'=>$espacioTrabajo,
                    'Usuario.admin' => 0,$condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
                    'or' =>
                        array('Gasto.nro_orden LIKE '=>'%'.$_GET['sSearch'].'%', 'Gasto.created LIKE '=>'%'.$this->dateFormatSQL($_GET['sSearch']).'%', 'Gasto.fecha LIKE '=>'%'.$this->dateFormatSQL($_GET['sSearch']).'%', 'Gasto.monto LIKE '=>'%'.$_GET['sSearch'].'%'
                        ));
                $gastos = $this->Gasto->find('all',array('conditions'=>$condicion,
                    'order' => $order, 'limit'=>$_GET['iDisplayLength'], 'offset'=>$_GET['iDisplayStart']));
            }


			/*App::uses('ConnectionManager', 'Model');
			$connected = ConnectionManager::getDataSource('default');
    		$logs = $connected->getLog();
    		$lastLog = end($logs['log']);
    		echo $lastLog;*/
			$iTotal = $this->Gasto->find('count',array('conditions'=> $condicion));
			$iMonto = $this->Gasto->find('first',array('fields'=>'SUM(Gasto.monto) as total','conditions'=> $condicion));

			//print_r($iMonto[0]['total']);

            /*$query = "SELECT * FROM gasto as Gasto
                    inner join usuario as Usuario on Gasto.user_id = Usuario.id
                    inner join rubro  as Rubro on Gasto.rubro_id = Rubro.id
                    inner join subrubro as Subrubro on Gasto.subrubro_id = Subrubro.id
                    left join proveedor as Proveedor on Gasto.proveedor = Proveedor.id
                    where Usuario.espacio_trabajo_id = '$espacioTrabajo' and Usuario.admin = 0
                    order by Gasto.created desc";

            $gastos = $this->Gasto->query($query);
            */

        }


        foreach($gastos as $gasto){
        	//print_r($gasto);
            //estado y nro de orden
            if($gasto['Gasto']['estado'] == 0 and $gasto['Gasto']['nro_orden'] == 0){
                $nro_orden	= 'Pendiente';
                $estado = 'Esperando nro. orden';
            }elseif($gasto['Gasto']['plan_id'] != 0){

                $nro_orden = $gasto['Gasto']['nro_orden'];
                $estado = 'Plan de pagos';
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
            $nro_orden = ($gasto['Gasto']['quitar_egresos'])?'<span style="color:red">'.$nro_orden.'</span>':$nro_orden;
            $rows[] = array(
                $gasto['Gasto']['id'],
                $nro_orden,

                $gasto['Gasto']['created'],
                $gasto['Gasto']['fecha'],
                $gasto['Gasto']['fecha_vencimiento'],
                $gasto['Rubro']['rubro'],
                $gasto['Subrubro']['subrubro'],
                $proveedor,
                $gasto['Gasto']['factura_tipo']." ".$gasto['Gasto']['factura_punto_venta'].$gasto['Gasto']['factura_nro'],
                $gasto['Gasto']['monto'],
                $estado,
                $gasto['Usuario']['nombre'].','.$gasto['Usuario']['apellido'],
				round($iMonto[0]['total'],2)
            );
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


    /* Cache query */
     public function get_gastos($offset, $limit, $order, $search, $search1, $search2, $search3, $search4, $search5, $search6, $search7, $search8, $search10, $search11, $search12) {
       	$result = Cache::read('get_gastos', 'long');
        if (!$result) {
        	$condicionSearch1 = ($search1)?array('Gasto.nro_orden LIKE '=>'%'.$search1.'%'):array();
        	//$condicionSearch2 = ($search2)?array('Gasto.orden_pago LIKE '=>'%'.$search2.'%'):array();
        	$condicionSearch2 = ($search2)?array('Gasto.created LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
        	$condicionSearch3 = ($search3)?array('Gasto.fecha LIKE '=> '%'.$this->dateFormatSQL($search3).'%'):array();
        	$condicionSearch4 = ($search4)?array('Gasto.fecha_vencimiento LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();
        	$condicionSearch5 = ($search5)?array('Rubro.rubro = '=>$search5):array();
        	$condicionSearch6 = ($search6)?array('Subrubro.subrubro = '=>$search6):array();
        	$condicionSearch7 = ($search7)?array('or' => array('Proveedor.nombre LIKE '=>'%'.$search7.'%', 'Gasto.proveedor LIKE '=>'%'.$search7.'%')):array();
        	$condicionSearch8 = ($search8)?array('or' => array('Gasto.factura_tipo LIKE '=>'%'.$search8.'%', 'Gasto.factura_nro LIKE '=>'%'.$search8.'%')):array();
        	switch ($search10) {
        		case 'Esperando nro. orden':
        		$condicionSearch10 = array('Gasto.estado = '=>0, 'Gasto.nro_orden = '=>0);
        		break;
        		case 'Falta abonar':
        		$condicionSearch10 = array('Gasto.estado = '=>0, 'Gasto.nro_orden != '=>0, 'Gasto.plan_id '=>null);
        		break;
        		case 'Falta factura':
        		$condicionSearch10 = array('Gasto.estado = '=>1, 'Gasto.nro_orden != '=>0, 'Gasto.factura_nro = '=>'');
        		break;
        		case 'Plan de pagos':
        		$condicionSearch10 = array('Gasto.plan_id != '=>0);
        		break;
        		case 'Procesado':
        		$condicionSearch10 = array('Gasto.estado = '=>1, 'Gasto.nro_orden != '=>0, 'Gasto.factura_nro != '=>'');
        		break;
        		case 'Desaprobado':
        		$condicionSearch10 = array('Gasto.estado = '=>2);
        		break;
        		default:
        			$condicionSearch10 = array();
        		break;
        	}
        	$condicionSearch11 = ($search11)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search11.'%', 'Usuario.apellido LIKE '=>'%'.$search11.'%')):array();
        	if ($search12=='on'){
                $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
                    'or' =>
                        array('Gasto.descripcion LIKE '=>'%'.$search.'%'
                        ));



            }
        	else{
                $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
                    'or' =>
                        array('Gasto.nro_orden LIKE '=>'%'.$search.'%', 'Gasto.created LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Gasto.fecha LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Gasto.monto LIKE '=>'%'.$search.'%'
                        ));
            }

            $result = $this->Gasto->find('all',array('order' => $order, 'conditions' => $condicion, 'limit'=>$limit, 'offset'=>$offset));

			Cache::write('get_gastos', $result, 'long');

		}

        return $result;
    }


 	public function get_gastoscount($search, $search1, $search2, $search3, $search4, $search5, $search6, $search7, $search8, $search10, $search11, $search12) {
       	$result = Cache::read('get_gastoscount', 'long');
        if (!$result) {
        	$condicionSearch1 = ($search1)?array('Gasto.nro_orden LIKE '=>'%'.$search1.'%'):array();
        	//$condicionSearch2 = ($search2)?array('Gasto.orden_pago LIKE '=>'%'.$search2.'%'):array();
        	$condicionSearch2 = ($search2)?array('Gasto.created LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
        	$condicionSearch3 = ($search3)?array('Gasto.fecha LIKE '=> '%'.$this->dateFormatSQL($search3).'%'):array();
        	$condicionSearch4 = ($search4)?array('Gasto.fecha_vencimiento LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();
        	$condicionSearch5 = ($search5)?array('Rubro.rubro = '=>$search5):array();
        	$condicionSearch6 = ($search6)?array('Subrubro.subrubro = '=>$search6):array();
        	$condicionSearch7 = ($search7)?array('or' => array('Proveedor.nombre LIKE '=>'%'.$search7.'%', 'Gasto.proveedor LIKE '=>'%'.$search7.'%')):array();
        	$condicionSearch8 = ($search8)?array('or' => array('Gasto.factura_tipo LIKE '=>'%'.$search8.'%', 'Gasto.factura_nro LIKE '=>'%'.$search8.'%')):array();
        	switch ($search10) {
        		case 'Esperando nro. orden':
        		$condicionSearch10 = array('Gasto.estado = '=>0, 'Gasto.nro_orden = '=>0);
        		break;
        		case 'Falta abonar':
        		$condicionSearch10 = array('Gasto.estado = '=>0, 'Gasto.nro_orden != '=>0, 'Gasto.plan_id '=>null);
        		break;
        		case 'Falta factura':
        		$condicionSearch10 = array('Gasto.estado = '=>1, 'Gasto.nro_orden != '=>0, 'Gasto.factura_nro = '=>'');
        		break;
        		case 'Plan de pagos':
        		$condicionSearch10 = array('Gasto.plan_id != '=>0);
        		break;
        		case 'Procesado':
        		$condicionSearch10 = array('Gasto.estado = '=>1, 'Gasto.nro_orden != '=>0, 'Gasto.factura_nro != '=>'');
        		break;
        		case 'Desaprobado':
        		$condicionSearch10 = array('Gasto.estado = '=>2);
        		break;
        		default:
        			$condicionSearch10 = array();
        		break;
        	}
        	$condicionSearch11 = ($search11)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search11.'%', 'Usuario.apellido LIKE '=>'%'.$search11.'%')):array();
        	if ($search12=='on'){
                $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
                    'or' =>
                        array('Gasto.descripcion LIKE '=>'%'.$search.'%'
                        ));
            }
        	else{
                $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
                    'or' =>
                        array('Gasto.nro_orden LIKE '=>'%'.$search.'%', 'Gasto.created LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Gasto.fecha LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Gasto.monto LIKE '=>'%'.$search.'%'
                        ));
            }

            $result = $this->Gasto->find('count',array('conditions' => $condicion));
			Cache::write('get_gastoscount', $result, 'long');

		}

        return $result;
    }

	public function get_gastossum($search, $search1, $search2, $search3, $search4, $search5, $search6, $search7, $search8, $search10, $search11, $search12) {
       	$result = Cache::read('get_gastossum', 'long');
        if (!$result) {
        	$condicionSearch1 = ($search1)?array('Gasto.nro_orden LIKE '=>'%'.$search1.'%'):array();
        	//$condicionSearch2 = ($search2)?array('Gasto.orden_pago LIKE '=>'%'.$search2.'%'):array();
        	$condicionSearch2 = ($search2)?array('Gasto.created LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
        	$condicionSearch3 = ($search3)?array('Gasto.fecha LIKE '=> '%'.$this->dateFormatSQL($search3).'%'):array();
        	$condicionSearch4 = ($search4)?array('Gasto.fecha_vencimiento LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();
        	$condicionSearch5 = ($search5)?array('Rubro.rubro = '=>$search5):array();
        	$condicionSearch6 = ($search6)?array('Subrubro.subrubro = '=>$search6):array();
        	$condicionSearch7 = ($search7)?array('or' => array('Proveedor.nombre LIKE '=>'%'.$search7.'%', 'Gasto.proveedor LIKE '=>'%'.$search7.'%')):array();
        	$condicionSearch8 = ($search8)?array('or' => array('Gasto.factura_tipo LIKE '=>'%'.$search8.'%', 'Gasto.factura_nro LIKE '=>'%'.$search8.'%')):array();
        	switch ($search10) {
        		case 'Esperando nro. orden':
        		$condicionSearch10 = array('Gasto.estado = '=>0, 'Gasto.nro_orden = '=>0);
        		break;
        		case 'Falta abonar':
        		$condicionSearch10 = array('Gasto.estado = '=>0, 'Gasto.nro_orden != '=>0, 'Gasto.plan_id '=>null);
        		break;
        		case 'Falta factura':
        		$condicionSearch10 = array('Gasto.estado = '=>1, 'Gasto.nro_orden != '=>0, 'Gasto.factura_nro = '=>'');
        		break;
        		case 'Plan de pagos':
        		$condicionSearch10 = array('Gasto.plan_id != '=>0);
        		break;
        		case 'Procesado':
        		$condicionSearch10 = array('Gasto.estado = '=>1, 'Gasto.nro_orden != '=>0, 'Gasto.factura_nro != '=>'');
        		break;
        		case 'Desaprobado':
        		$condicionSearch10 = array('Gasto.estado = '=>2);
        		break;
        		default:
        			$condicionSearch10 = array();
        		break;
        	}
        	$condicionSearch11 = ($search11)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search11.'%', 'Usuario.apellido LIKE '=>'%'.$search11.'%')):array();
        	if ($search12=='on'){
                $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
                    'or' =>
                        array('Gasto.descripcion LIKE '=>'%'.$search.'%'
                        ));
            }
        	else{
                $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
                    'or' =>
                        array('Gasto.nro_orden LIKE '=>'%'.$search.'%', 'Gasto.created LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Gasto.fecha LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Gasto.monto LIKE '=>'%'.$search.'%'
                        ));
            }

            $result = $this->Gasto->find('first',array('fields'=>'SUM(Gasto.monto) as total','conditions'=> $condicion));
			Cache::write('get_gastossum', $result, 'long');

		}

        return $result;
    }


}
?>
