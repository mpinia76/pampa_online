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



	public function index($mes=null, $year=null){

		$_SESSION['mesA'] = '';
		$_SESSION['yearA'] = '';
		$this->layout = 'index';


		if (isset($mes)&&($mes!='')) {
			$_SESSION['mesA'] = $mes;
			$this->set('mes',$_SESSION['mesA']);
		}
		else{
			$_SESSION['mesA'] = date('m');
			$this->set('mes',$_SESSION['mesA']);
		}

		if (isset($year)&&($year!='')) {
			$_SESSION['yearA'] = $year;
			$this->set('year',$_SESSION['yearA']);
		}
		else{
			$_SESSION['yearA'] = date('Y');
			$this->set('year',$_SESSION['yearA']);
		}
		/*$gc_maxlifetime = ini_get('session.gc_maxlifetime');
        $cookie_lifetime = ini_get('session.cookie_lifetime');

        echo "Tiempo de vida mÃƒÂ¡ximo de sesiÃƒÂ³n: $gc_maxlifetime segundos\n";
        echo "Tiempo de vida de la cookie de sesiÃƒÂ³n: $cookie_lifetime segundos\n";*/
		$this->setLogUsuario('Auditoria de Usuarios - logueo');

	}



	public function dataTable(){
		//print_r($_GET);
		/*$desde = $_SESSION['desdeA'];
        $hasta = $_SESSION['hastaA'];*/
		$mes = (isset($_SESSION['mesA']))?$_SESSION['mesA']:date('m');
		$year = (isset($_SESSION['yearA']))?$_SESSION['yearA']:date('Y');

		// Obtener el primer y Ãºltimo dÃ­a del mes
		$desde = date('Y-m-01', strtotime("$year-$mes-01"));
		$hasta = date('Y-m-t', strtotime("$year-$mes-01"));

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


		$rows = array();

		// Iterar sobre cada dÃ­a del mes
		for ($dia = 1; $dia <= cal_days_in_month(CAL_GREGORIAN, $mes, $year); $dia++) {
			// Obtener la fecha especÃ­fica del dÃ­a
			$fecha = date('Y-m-d', strtotime("$year-$mes-$dia"));

			/*$condicionSearch5=array();
            if (($desde!='')&&($hasta!='')) {
                $condicionSearch5=array('UsuarioAuditoria.fecha between ? and ?' => array($desde, $hasta));
            }*/
			//$condicion=array($condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5);



			/*$UsuarioAuditorias = $this->UsuarioAuditoria->find('all',array('conditions'=>$condicion,
                                                         'order' => $order, 'limit'=>$_GET['iDisplayLength'], 'offset'=>$_GET['iDisplayStart']));*/
			// Agregar la condiciÃ³n de activo = 1 a las condiciones de bÃºsqueda existentes
			$condicionSearch1['AND'] = array('Usuario.activo' => 1);

			$this->loadModel('Usuario');

			// Obtener todos los usuarios
			$usuarios = $this->Usuario->find('all', array(
				'conditions' => $condicionSearch1,
				'order' => $order,
				'limit' => $_GET['iDisplayLength'],
				'offset' => $_GET['iDisplayStart']
			));



			$nuevosUsuarios = array();
			foreach ($usuarios as $usuario) {
				$usuarioId = $usuario['Usuario']['id'];

				// Obtener las tuplas de auditorÃ­a para el usuario actual y la fecha especÃ­fica
				/*$tuplasAuditoria = $this->UsuarioAuditoria->find('all', array(
                    'conditions' => array_merge(array('usuario_id' => $usuarioId), $condicionSearch5)
                ));*/

				// Consultar si hay registros de auditorÃ­a para este usuario y este dÃ­a
				$registros = $this->UsuarioAuditoria->find('all', array(
					'conditions' => array(
						'usuario_id' => $usuarioId,
						'fecha' => $fecha
					)
				));
				$apellidoNombre = $usuario['Usuario']['apellido'] . ', ' . $usuario['Usuario']['nombre'];
				// Si no hay registros para este dÃ­a, aÃ±adir el dÃ­a a la matriz con un marcador para sombrearlo
				if (empty($registros)) {

					// Si el usuario no tiene tuplas de auditorÃƒÂ­a, agregar una fila vacÃƒÂ­a
					$rows[] = array(
						null,
						$apellidoNombre,
						$fecha,
						null,
						null,
						null,
						null,
						null
					);

				} else {
					// Si hay registros, aÃ±adirlos a la matriz de datos de la grilla
					foreach ($registros as $registro) {
						$segundosTotales = $registro['UsuarioAuditoria']['segundos'];

// Calcular las horas, minutos y segundos
						$horas = floor($segundosTotales / 3600);
						$minutos = floor(($segundosTotales % 3600) / 60);
						$segundos = $segundosTotales % 60;
						$tiempoFormateado = sprintf("%02d:%02d:%02d", $horas, $minutos, $segundos);

						$rows[] = array(
							$registro['UsuarioAuditoria']['id'],
							$apellidoNombre,
							$registro['UsuarioAuditoria']['fecha'],
							$registro['UsuarioAuditoria']['logueo'],
							$registro['UsuarioAuditoria']['last'],
							$tiempoFormateado,
							$registro['UsuarioAuditoria']['interaccion'],
							$registro['UsuarioAuditoria']['ip']
						);
					}
				}
			}
		}

		// Generar la salida en el formato esperado para la grilla
		$output = array(
			"sEcho" => intval($_GET['sEcho']),
			"iTotalRecords" => count($rows),
			"iTotalDisplayRecords" => count($rows),
			"aaData" => $rows
		);

		// Establecer la salida como datos serializados para la vista
		$this->set('aoData', $output);
		$this->set('_serialize', 'aoData');
	}
}
?>
