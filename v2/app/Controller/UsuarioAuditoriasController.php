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

        echo "Tiempo de vida mÃ¡ximo de sesiÃ³n: $gc_maxlifetime segundos\n";
        echo "Tiempo de vida de la cookie de sesiÃ³n: $cookie_lifetime segundos\n";*/
		$this->setLogUsuario('Auditoria de Usuarios - logueo');

	}



	public function dataTable(){
		//print_r($_GET);
		$desde = $_SESSION['desdeA'];
		$hasta = $_SESSION['hastaA'];
		$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'asc';
		switch ($_GET['iSortCol_0']) {

			case 1:
				$order='Usuario.apellido '.$orderType;
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
				$order='Usuario.apellido '.$orderType;
				break;
		}



		$rows = array();



		$condicionSearch1 = ($_GET['sSearch_1']) ? array('OR' => array(
			'Usuario.nombre LIKE ' => '%' . ($_GET['sSearch_1']) . '%',
			'Usuario.apellido LIKE ' => '%' . ($_GET['sSearch_1']) . '%'
		)) : array();
		/*$condicionSearch2 = ($_GET['sSearch_2'])?array('UsuarioAuditoria.fecha LIKE '=>'%'.$_GET['sSearch_2'].'%'):array();
        $condicionSearch3 = ($_GET['sSearch_3'])?array('UsuarioAuditoria.interaccion LIKE '=>'%'.$_GET['sSearch_3'].'%'):array();
        $condicionSearch4 = ($_GET['sSearch_4'])?array('UsuarioAuditoria.ip LIKE '=>'%'.$_GET['sSearch_4'].'%'):array();
        $condicionSearch5=array();*/
		$condicionSearch5=array();
		if (($desde!='')&&($hasta!='')) {
			$condicionSearch5=array('UsuarioAuditoria.fecha between ? and ?' => array($desde, $hasta));
		}
		//$condicion=array($condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5);



		/*$UsuarioAuditorias = $this->UsuarioAuditoria->find('all',array('conditions'=>$condicion,
                                                     'order' => $order, 'limit'=>$_GET['iDisplayLength'], 'offset'=>$_GET['iDisplayStart']));*/

		$this->loadModel('Usuario');

		// Obtener todos los usuarios
		$usuarios = $this->Usuario->find('all', array(
			'conditions' => $condicionSearch1,
			'order' => $order,
			'limit' => $_GET['iDisplayLength'],
			'offset' => $_GET['iDisplayStart']
		));


		// Paso 2: Agregar las tuplas de auditorÃ­a para la fecha especÃ­fica
		foreach ($usuarios as &$usuario) {
			//print_r($usuario);
			$usuarioId = $usuario['Usuario']['id'];

			// Obtener las tuplas de auditorÃ­a para el usuario actual y la fecha especÃ­fica
			$tuplasAuditoria = $this->UsuarioAuditoria->find('all', array(
				'conditions' => array_merge(array('usuario_id' => $usuarioId), $condicionSearch5)
			));

			//print_r($tuplasAuditoria);
			// Agregar las tuplas de auditorÃ­a al usuario actual
			$usuario['UsuarioAuditoria'] = $tuplasAuditoria;
		}

// Contar el total de usuarios sin aplicar condiciones adicionales
		$iTotal = $this->Usuario->find('count', array('conditions' => $condicionSearch1));


// Ahora, $usuarios contiene todos los usuarios y sus tuplas de auditorÃ­a para la fecha especÃ­fica
		$rows = array();

		foreach ($usuarios as $usuario) {
			$apellidoNombre = $usuario['Usuario']['apellido'] . ', ' . $usuario['Usuario']['nombre'];

			// Verificar si el usuario tiene tuplas de auditorÃ­a
			if (!empty($usuario['UsuarioAuditoria'])) {
				// Iterar sobre las tuplas de auditorÃ­a para el usuario actual
				foreach ($usuario['UsuarioAuditoria'] as $auditoria) {
					$segundosTotales = $auditoria['UsuarioAuditoria']['segundos'];

// Calcular las horas, minutos y segundos
					$horas = floor($segundosTotales / 3600);
					$minutos = floor(($segundosTotales % 3600) / 60);
					$segundos = $segundosTotales % 60;

// Formatear los resultados segÃºn tus necesidades
					$tiempoFormateado = sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);
					//print_r($auditoria);
					$row = array(
						$auditoria['UsuarioAuditoria']['id'],
						$apellidoNombre,
						$auditoria['UsuarioAuditoria']['fecha'],
						$auditoria['UsuarioAuditoria']['logueo'],
						$tiempoFormateado,
						$auditoria['UsuarioAuditoria']['interaccion'],
						$auditoria['UsuarioAuditoria']['ip']
					);
					$rows[] = $row;
				}
			} else {
				// Si el usuario no tiene tuplas de auditorÃ­a, agregar una fila vacÃ­a
				$rows[] = array(
					null,
					$apellidoNombre,
					null,
					null,
					null,
					null,
					null
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
