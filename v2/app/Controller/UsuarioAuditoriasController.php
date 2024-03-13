<?php
ini_set('memory_limit', '-1');
session_start();
class UsuarioAuditoriasController extends AppController {
    public $scaffold;
    
	public function dateFormatSQL($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }

	public function dateFormatView($dateString) {

		$date_parts = explode("-",$dateString);
		return $date_parts[2]."/".$date_parts[1]."/".$date_parts[0];
	}



	public function index($desde,$hasta){
		$_SESSION['desdeA'] = '';
		$_SESSION['hastaA'] = '';

		$this->layout = 'index';

		if (isset($desde)&&($desde!='')) {
			$_SESSION['desdeA'] = $desde;
			$this->set('desde',$this->dateFormatView($_SESSION['desdeA']));
		}
		if (isset($hasta)&&($hasta!='')) {
			$_SESSION['hastaA'] = $hasta;
			$this->set('hasta',$this->dateFormatView($_SESSION['hastaA']));
		}
		/*$gc_maxlifetime = ini_get('session.gc_maxlifetime');
        $cookie_lifetime = ini_get('session.cookie_lifetime');

        echo "Tiempo de vida máximo de sesión: $gc_maxlifetime segundos\n";
        echo "Tiempo de vida de la cookie de sesión: $cookie_lifetime segundos\n";*/
		$this->setLogUsuario('Auditoria de Usuarios - logueo');

	}


    
    public function dataTable(){
    	//print_r($_GET);
    	$desde = $_SESSION['desdeA'];
	    $hasta = $_SESSION['hastaA'];
    	$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'desc';
    	switch ($_GET['iSortCol_0']) {
			
			case 1:
			$order='Usuario.nombre '.$orderType;
			break;
			case 2:
			$order='UsuarioAuditoria.fecha '.$orderType;
			break;
			case 3:
			$order='UsuarioAuditoria.interaccion '.$orderType;
			break;
			case 4:
			$order='UsuarioAuditoria.ip '.$orderType;
			break;
			default:
			$order='Usuario.nombre '.$orderType;
			break;
		}
		
		
	
	    $rows = array();

    	
        	
        	$condicionSearch1 = ($_GET['sSearch_1'])?array('Usuario.nombre LIKE '=> '%'.($_GET['sSearch_1']).'%'):array();
        	$condicionSearch2 = ($_GET['sSearch_2'])?array('UsuarioAuditoria.fecha LIKE '=>'%'.$_GET['sSearch_2'].'%'):array();
        	$condicionSearch3 = ($_GET['sSearch_3'])?array('UsuarioAuditoria.interaccion LIKE '=>'%'.$_GET['sSearch_3'].'%'):array();
        	$condicionSearch4 = ($_GET['sSearch_4'])?array('UsuarioAuditoria.ip LIKE '=>'%'.$_GET['sSearch_4'].'%'):array();
        	$condicionSearch5=array();
    		if (($desde!='')&&($hasta!='')) {
				$condicionSearch5=array('UsuarioAuditoria.fecha between ? and ?' => array($desde, $hasta));
			}
        	$condicion=array($condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5);
        	
		    
        	
			/*$UsuarioAuditorias = $this->UsuarioAuditoria->find('all',array('conditions'=>$condicion,
                                                         'order' => $order, 'limit'=>$_GET['iDisplayLength'], 'offset'=>$_GET['iDisplayStart']));*/

		$this->loadModel('Usuario');

		// Obtener todos los usuarios
		$usuarios = $this->Usuario->find('all', [
			'conditions' => $condicionSearch1,
			'limit' => $_GET['iDisplayLength'],
			'offset' => $_GET['iDisplayStart']
		]);

		// Paso 2: Agregar las tuplas de auditoría para la fecha específica
		foreach ($usuarios as &$usuario) {
			$usuarioId = $usuario['id'];

			// Obtener las tuplas de auditoría para el usuario actual y la fecha específica
			$tuplasAuditoria = $this->Usuario->UsuarioAuditoria->find('all', [
				'conditions' => [
					'UsuarioAuditoria.usuario_id' => $usuarioId,
					$condicion
				]

			]);

			// Agregar las tuplas de auditoría al usuario actual
			$usuario['UsuarioAuditoria'] = $tuplasAuditoria->toArray();
		}

// Contar el total de usuarios sin aplicar condiciones adicionales
		$iTotal = $this->Usuario->find('count', ['conditions' => $condicionSearch1]);

// Ahora, $usuarios contiene todos los usuarios y sus tuplas de auditoría para la fecha específica
		$rows = array();

		foreach ($usuarios as $usuario) {
			$apellidoNombre = $usuario['apellido'] . ', ' . $usuario['nombre'];

			// Iterar sobre las tuplas de auditoría para el usuario actual
			foreach ($usuario['UsuarioAuditoria'] as $auditoria) {
				$rows[] = array(
					$auditoria['id'],
					$apellidoNombre,
					$auditoria['fecha'],
					$auditoria['interaccion'],
					$auditoria['ip']
				);
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
}
?>
