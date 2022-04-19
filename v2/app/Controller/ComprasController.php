<?php 
ini_set('memory_limit', '-1');
session_start();
class ComprasController extends AppController {
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
        $this->set('rubros',$this->Rubro->find('list',array('conditions'=>array('impuestos'=>1,'activo'=>1),'order' => 'Rubro.rubro asc')));
        
        $this->loadModel('Subrubro');
        $this->set('subrubros',$this->Subrubro->find('list',array('conditions'=>array('activo'=>1),'order' => 'Subrubro.subrubro asc')));

        $this->loadModel('Usuario');
        $this->set('usuario',$this->Usuario->find('list',array('order' => 'Usuario.nombre asc')));
         $this->setLogUsuario('Impuestos, tasas y cargas sociales');
   	
    }
    
    public function dataTable(){
    	//print_r($_GET);
    	$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'desc';
    	switch ($_GET['iSortCol_0']) {
			case 1:
			$order='Compra.nro_orden '.$orderType;
			break;
			/*case 2:
			$order='Compra.orden_pago '.$orderType;
			break;*/
			case 2:
			$order='Compra.created '.$orderType;
			break;
			case 3:
			$order='Compra.fecha '.$orderType;
			break;
			case 4:
			$order='Compr.fecha_vencimiento '.$orderType;
			break;
			case 5:
			$order='Rubro.rubro '.$orderType;
			break;
			case 6:
			$order='Subrubro.subrubro '.$orderType;
			break;
			case 7:
			$order='Proveedor.nombre '.$orderType.', Compra.proveedor '.$orderType;
			break;
			case 8:
			$order='Compra.factura_tipo '.$orderType.', Compra.factura_nro '.$orderType.', Compra.recibo_nro '.$orderType;
			break;
			case 9:
			$order='Compra.monto '.$orderType;
			break;
			case 11:
			$order='Usuario.nombre '.$orderType.', Usuario.apellido '.$orderType;
			break;
			default:
			$order='Compra.created '.$orderType;
			break;
		}
		
		
	
	    $rows = array();

        $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));
        $espacioTrabajo = $user['EspacioTrabajo']['id'];        
		
        if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $tienePermiso=0;
	    	foreach($permisos as $permiso){
	               if ($permiso['UsuarioPermiso']['permiso_id']==121) {
	               		$tienePermiso=1;
	               		continue;
	               }
	        }
        }
        if (($user['Usuario']['admin'] == '1')||($tienePermiso)){
            $compras = $this->get_compras($_GET['iDisplayStart'], $_GET['iDisplayLength'], $order, $_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8'], $_GET['sSearch_10'], $_GET['sSearch_11']);
            $iTotal = $this->get_comprascount($_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8'], $_GET['sSearch_10'], $_GET['sSearch_11']);
            $iMonto = $this->get_comprassum($_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8'], $_GET['sSearch_10'], $_GET['sSearch_11']);
        }else{
        	$condicionSearch1 = ($_GET['sSearch_1'])?array('Compra.nro_orden LIKE '=>'%'.$_GET['sSearch_1'].'%'):array();
        	//$condicionSearch2 = ($_GET['sSearch_2'])?array('Compra.orden_pago LIKE '=>'%'.$_GET['sSearch_2'].'%'):array();
        	$condicionSearch2 = ($_GET['sSearch_2'])?array('Compra.created LIKE '=> '%'.$this->dateFormatSQL($_GET['sSearch_2']).'%'):array();
        	$condicionSearch3 = ($_GET['sSearch_3'])?array('Compra.fecha LIKE '=> '%'.$this->dateFormatSQL($_GET['sSearch_3']).'%'):array();
        	$condicionSearch4 = ($_GET['sSearch_4'])?array('Compra.fecha_vencimiento LIKE '=> '%'.$this->dateFormatSQL($_GET['sSearch_4']).'%'):array();
        	$condicionSearch5 = ($_GET['sSearch_5'])?array('Rubro.rubro = '=>$_GET['sSearch_5']):array();
        	$condicionSearch6 = ($_GET['sSearch_6'])?array('Subrubro.surubro = '=>$_GET['sSearch_6']):array();
        	$condicionSearch7 = ($_GET['sSearch_7'])?array('or' => array('Proveedor.nombre LIKE '=>'%'.$_GET['sSearch_7'].'%', 'Compra.proveedor LIKE '=>'%'.$_GET['sSearch_7'].'%')):array();
        	$condicionSearch8 = ($_GET['sSearch_8'])?array('or' => array('Compra.factura_tipo LIKE '=>'%'.$_GET['sSearch_8'].'%', 'Compra.factura_nro LIKE '=>'%'.$_GET['sSearch_8'].'%', 'Compra.recibo_nro LIKE '=>'%'.$_GET['sSearch_8'].'%')):array();
        	switch ($_GET['sSearch_10']) {
        		case 'Esperando nro. orden':
        		$condicionSearch10 = array('Compra.estado = '=>0, 'Compra.nro_orden = '=>0);
        		break;
        		case 'Falta abonar':
        		$condicionSearch10 = array('Compra.estado = '=>0, 'Compra.nro_orden != '=>0, 'Compra.plan_id '=>null);
        		break;
        		case 'Falta factura':
        		$condicionSearch10 = array('Compra.estado = '=>1, 'Compra.nro_orden != '=>0, 'Compra.factura_nro = '=>'', 'Compra.recibo_nro = '=>'');
        		break;
        		case 'Plan de pagos':
        		$condicionSearch10 = array('Compra.plan_id != '=>0);
        		break;
        		case 'Procesado':
        		$condicionSearch10 = array('Compra.estado = '=>1, 'Compra.nro_orden != '=>0, array('or' => array('Compra.factura_nro != '=>'','Compra.recibo_nro != '=>'')));
        		break;
        		case 'Desaprobado':
        		$condicionSearch10 = array('Compra.estado = '=>2);
        		break;
        		default:
        			$condicionSearch10 = array();
        		break;
        	}
        	$condicionSearch11 = ($_GET['sSearch_11'])?array('or' => array('Usuario.nombre LIKE '=>'%'.$_GET['sSearch_11'].'%', 'Usuario.apellido LIKE '=>'%'.$_GET['sSearch_11'].'%')):array();
        	$condicion=array('Usuario.espacio_trabajo_id'=>$espacioTrabajo,
                                                                         'Usuario.admin' => 0,$condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch12,
			    'or' => 
	        	  array('Compra.nro_orden LIKE '=>'%'.$_GET['sSearch'].'%', 'Compra.created LIKE '=>'%'.$this->dateFormatSQL($_GET['sSearch']).'%', 'Compra.fecha LIKE '=>'%'.$this->dateFormatSQL($_GET['sSearch']).'%', 'Compra.monto LIKE '=>'%'.$_GET['sSearch'].'%'  	  	  
			    ));
			$compras = $this->Compra->find('all',array('conditions'=>$condicion,
                                                         'order' => $order, 'limit'=>$_GET['iDisplayLength'], 'offset'=>$_GET['iDisplayStart']));
			
			/*App::uses('ConnectionManager', 'Model');
			$connected = ConnectionManager::getDataSource('default');
    		$logs = $connected->getLog();
    		$lastLog = end($logs['log']);
    		echo $lastLog;*/
			$iTotal = $this->Compra->find('count',array('conditions'=> $condicion));
			$iMonto = $this->Compra->find('first',array('fields'=>'SUM(Compra.monto) as total','conditions'=> $condicion));
			
			//print_r($iMonto[0]['total']);
			
            /*$query = "SELECT * FROM compra as Compra
                    inner join usuario as Usuario on Compra.user_id = Usuario.id 
                    inner join rubro  as Rubro on Compra.rubro_id = Rubro.id
                    inner join subrubro as Subrubro on Compra.subrubro_id = Subrubro.id
                    left join proveedor as Proveedor on Compra.proveedor = Proveedor.id
                    where Usuario.espacio_trabajo_id = '$espacioTrabajo' and Usuario.admin = 0
                    order by Compra.created desc";

            $compras = $this->Compra->query($query);
            */
            
        }       
 

        foreach($compras as $compra){
        	//print_r($compra);
            //estado y nro de orden
            if($compra['Compra']['estado'] == 0 and $compra['Compra']['nro_orden'] == 0){
                $nro_orden	= 'Pendiente';
                $estado = 'Esperando nro. orden';
            }elseif($compra['Compra']['plan_id'] != 0){
                $nro_orden = $compra['Compra']['nro_orden'];
                $estado = 'Plan de pagos';
            }elseif($compra['Compra']['estado'] == 0 and $compra['Compra']['nro_orden'] != 0){
                $nro_orden = $compra['Compra']['nro_orden'];
                $estado = 'Falta abonar';
            }elseif($compra['Compra']['estado'] == 1 and $compra['Compra']['nro_orden'] != 0 and $compra['Compra']['factura_nro'] == '' and $compra['Compra']['recibo_nro'] == ''){
                $nro_orden 	= $compra['Compra']['nro_orden'];
                $estado = 'Falta factura';
            }elseif($compra['Compra']['estado'] == 1 and $compra['Compra']['nro_orden'] != 0 and ($compra['Compra']['factura_nro'] != '' OR $compra['Compra']['recibo_nro'] != '')){
                $nro_orden 	= $compra['Compra']['nro_orden'];
                $estado = 'Procesado';
            }elseif($compra['Compra']['estado'] == 2){
                $nro_orden 	= '';
                $estado = 'Desaprobado';
            }
            //proveedor
            if(isset($compra['Proveedor']['id'])){
                $proveedor = $compra['Proveedor']['nombre'];
            }else{
                $proveedor = $compra['Compra']['proveedor'];
            }
            $comprobante = ($compra['Compra']['factura_nro'])?$compra['Compra']['factura_tipo']." ".$compra['Compra']['factura_nro']:$compra['Compra']['recibo_nro'];
            $rows[] = array(
                $compra['Compra']['id'],
                $nro_orden,
                //$compra['Compra']['orden_pago'],
                $compra['Compra']['created'],
                $compra['Compra']['fecha'],
                $compra['Compra']['fecha_vencimiento'],
                $compra['Rubro']['rubro'],
                $compra['Subrubro']['subrubro'],
                $proveedor,
                $comprobante,
                $compra['Compra']['monto'],
                $estado,
                $compra['Usuario']['nombre'].','.$compra['Usuario']['apellido'],
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
    
	public function eliminar(){
		$compra = $this->Compra->find('first',array('conditions' => array('Compra.id' => $this->request->data['compra_id'], 'Compra.estado' => 0)));
		
		if ($compra) {
        	$this->Compra->delete($this->request->data['compra_id'],true);
        
        	$this->set('resultado','OK');
        	$this->set('mensaje','Eliminado');
        	$this->set('detalle','');
		}
		else{
        	$this->set('resultado','ERROR');
	        $this->set('mensaje','No eliminado');
	        $this->set('detalle','Verifique el estado');
        }
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
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
     public function get_compras($offset, $limit, $order, $search, $search1,$search2, $search3, $search4, $search5, $search6, $search7, $search8, $search10, $search11) {
       	$result = Cache::read('get_compras', 'long');
        if (!$result) {
        	$condicionSearch1 = ($search1)?array('Compra.nro_orden LIKE '=>'%'.$search1.'%'):array();
        	//$condicionSearch2 = ($search2)?array('Compra.orden_pago LIKE '=>'%'.$search2.'%'):array();
        	$condicionSearch2 = ($search2)?array('Compra.created LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
        	$condicionSearch3 = ($search3)?array('Compra.fecha LIKE '=> '%'.$this->dateFormatSQL($search3).'%'):array();
        	$condicionSearch4 = ($search4)?array('Compra.fecha_vencimiento LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();
        	$condicionSearch5 = ($search5)?array('Rubro.rubro = '=>$search5):array();
        	$condicionSearch6 = ($search6)?array('Subrubro.surubro = '=>$search6):array();
        	$condicionSearch7 = ($search7)?array('or' => array('Proveedor.nombre LIKE '=>'%'.$search7.'%', 'Compra.proveedor LIKE '=>'%'.$search7.'%')):array();
        	$condicionSearch8 = ($search8)?array('or' => array('Compra.factura_tipo LIKE '=>'%'.$search8.'%', 'Compra.factura_nro LIKE '=>'%'.$search8.'%', 'Compra.recibo_nro LIKE '=>'%'.$search9.'%')):array();
        	switch ($search10) {
        		case 'Esperando nro. orden':
        		$condicionSearch10 = array('Compra.estado = '=>0, 'Compra.nro_orden = '=>0);
        		break;
        		case 'Falta abonar':
        		$condicionSearch10 = array('Compra.estado = '=>0, 'Compra.nro_orden != '=>0, 'Compra.plan_id '=>null);
        		break;
        		case 'Falta factura':
        		$condicionSearch10 = array('Compra.estado = '=>1, 'Compra.nro_orden != '=>0, 'Compra.factura_nro = '=>'', 'Compra.recibo_nro = '=>'');
        		break;
        		case 'Plan de pagos':
        		$condicionSearch10 = array('Compra.plan_id != '=>0);
        		break;
        		case 'Procesado':
        		$condicionSearch10 = array('Compra.estado = '=>1, 'Compra.nro_orden != '=>0, array('or' => array('Compra.factura_nro != '=>'','Compra.recibo_nro != '=>'')));
        		break;
        		case 'Desaprobado':
        		$condicionSearch10 = array('Compra.estado = '=>2);
        		break;
        		default:
        			$condicionSearch10 = array();
        		break;
        	}
        	$condicionSearch11 = ($search11)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search11.'%', 'Usuario.apellido LIKE '=>'%'.$search11.'%')):array();
        	$condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
			    'or' => 
	        	  array('Compra.nro_orden LIKE '=>'%'.$search.'%', 'Compra.created LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Compra.fecha LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Compra.monto LIKE '=>'%'.$search.'%'  	  	  
			    ));
            $result = $this->Compra->find('all',array('order' => $order, 'conditions' => $condicion, 'limit'=>$limit, 'offset'=>$offset));
           
			Cache::write('get_compras', $result, 'long');

		}

        return $result;
    }
    
    
 	public function get_comprascount($search, $search1,$search2, $search3, $search4, $search5, $search6, $search7, $search8, $search10, $search11) {
       	$result = Cache::read('get_comprascount', 'long');
        if (!$result) {
        	$condicionSearch1 = ($search1)?array('Compra.nro_orden LIKE '=>'%'.$search1.'%'):array();
        	//$condicionSearch2 = ($search2)?array('Compra.orden_pago LIKE '=>'%'.$search2.'%'):array();
        	$condicionSearch2 = ($search2)?array('Compra.created LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
        	$condicionSearch3 = ($search3)?array('Compra.fecha LIKE '=> '%'.$this->dateFormatSQL($search3).'%'):array();
        	$condicionSearch4 = ($search4)?array('Compra.fecha_vencimiento LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();
        	$condicionSearch5 = ($search5)?array('Rubro.rubro = '=>$search5):array();
        	$condicionSearch6 = ($search6)?array('Subrubro.surubro = '=>$search6):array();
        	$condicionSearch7 = ($search7)?array('or' => array('Proveedor.nombre LIKE '=>'%'.$search7.'%', 'Compra.proveedor LIKE '=>'%'.$search7.'%')):array();
        	$condicionSearch8 = ($search8)?array('or' => array('Compra.factura_tipo LIKE '=>'%'.$search8.'%', 'Compra.factura_nro LIKE '=>'%'.$search8.'%', 'Compra.recibo_nro LIKE '=>'%'.$search9.'%')):array();
        	switch ($search10) {
        		case 'Esperando nro. orden':
        		$condicionSearch10 = array('Compra.estado = '=>0, 'Compra.nro_orden = '=>0);
        		break;
        		case 'Falta abonar':
        		$condicionSearch10 = array('Compra.estado = '=>0, 'Compra.nro_orden != '=>0, 'Compra.plan_id '=>null);
        		break;
        		case 'Falta factura':
        		$condicionSearch10 = array('Compra.estado = '=>1, 'Compra.nro_orden != '=>0, 'Compra.factura_nro = '=>'', 'Compra.recibo_nro = '=>'');
        		break;
        		case 'Plan de pagos':
        		$condicionSearch10 = array('Compra.plan_id != '=>0);
        		break;
        		case 'Procesado':
        		$condicionSearch10 = array('Compra.estado = '=>1, 'Compra.nro_orden != '=>0, array('or' => array('Compra.factura_nro != '=>'','Compra.recibo_nro != '=>'')));
        		break;
        		case 'Desaprobado':
        		$condicionSearch10 = array('Compra.estado = '=>2);
        		break;
        		default:
        			$condicionSearch10 = array();
        		break;
        	}
        	$condicionSearch11 = ($search11)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search11.'%', 'Usuario.apellido LIKE '=>'%'.$search11.'%')):array();
        	$condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
			    'or' => 
	        	  array('Compra.nro_orden LIKE '=>'%'.$search.'%', 'Compra.created LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Compra.fecha LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Compra.monto LIKE '=>'%'.$search.'%'  	  	  
			    ));
            $result = $this->Compra->find('count',array('conditions' => $condicion));
			Cache::write('get_comprascount', $result, 'long');

		}

        return $result;
    }
    
	public function get_comprassum($search, $search1,$search2, $search3, $search4, $search5, $search6, $search7, $search8, $search10, $search11) {
       	$result = Cache::read('get_comprassum', 'long');
        if (!$result) {
        	$condicionSearch1 = ($search1)?array('Compra.nro_orden LIKE '=>'%'.$search1.'%'):array();
        	//$condicionSearch2 = ($search2)?array('Compra.orden_pago LIKE '=>'%'.$search2.'%'):array();
        	$condicionSearch2 = ($search2)?array('Compra.created LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
        	$condicionSearch3 = ($search3)?array('Compra.fecha LIKE '=> '%'.$this->dateFormatSQL($search3).'%'):array();
        	$condicionSearch4 = ($search4)?array('Compra.fecha_vencimiento LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();
        	$condicionSearch5 = ($search5)?array('Rubro.rubro = '=>$search5):array();
        	$condicionSearch6 = ($search6)?array('Subrubro.surubro = '=>$search6):array();
        	$condicionSearch7 = ($search7)?array('or' => array('Proveedor.nombre LIKE '=>'%'.$search7.'%', 'Compra.proveedor LIKE '=>'%'.$search7.'%')):array();
        	$condicionSearch8 = ($search8)?array('or' => array('Compra.factura_tipo LIKE '=>'%'.$search8.'%', 'Compra.factura_nro LIKE '=>'%'.$search8.'%', 'Compra.recibo_nro LIKE '=>'%'.$search9.'%')):array();
        	switch ($search10) {
        		case 'Esperando nro. orden':
        		$condicionSearch10 = array('Compra.estado = '=>0, 'Compra.nro_orden = '=>0);
        		break;
        		case 'Falta abonar':
        		$condicionSearch10 = array('Compra.estado = '=>0, 'Compra.nro_orden != '=>0, 'Compra.plan_id '=>null);
        		break;
        		case 'Falta factura':
        		$condicionSearch10 = array('Compra.estado = '=>1, 'Compra.nro_orden != '=>0, 'Compra.factura_nro = '=>'', 'Compra.recibo_nro = '=>'');
        		break;
        		case 'Plan de pagos':
        		$condicionSearch10 = array('Compra.plan_id != '=>0);
        		break;
        		case 'Procesado':
        		$condicionSearch10 = array('Compra.estado = '=>1, 'Compra.nro_orden != '=>0, array('or' => array('Compra.factura_nro != '=>'','Compra.recibo_nro != '=>'')));
        		break;
        		case 'Desaprobado':
        		$condicionSearch10 = array('Compra.estado = '=>2);
        		break;
        		default:
        			$condicionSearch10 = array();
        		break;
        	}
        	$condicionSearch11 = ($search11)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search11.'%', 'Usuario.apellido LIKE '=>'%'.$search11.'%')):array();
        	$condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch10,$condicionSearch11,
			    'or' => 
	        	  array('Compra.nro_orden LIKE '=>'%'.$search.'%', 'Compra.created LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Compra.fecha LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'Compra.monto LIKE '=>'%'.$search.'%'  	  	  
			    ));
            $result = $this->Compra->find('first',array('fields'=>'SUM(Compra.monto) as total','conditions'=> $condicion));
			Cache::write('get_comprassum', $result, 'long');

		}

        return $result;
    }


}
?>
