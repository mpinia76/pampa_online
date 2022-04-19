<?php
ini_set('memory_limit', '-1');
session_start();
class UsuarioLogsController extends AppController {
    public $scaffold;
    
	public function dateFormatSQL($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }
    
   public function index(){
   		$_SESSION['desde'] = '';
		$_SESSION['hasta'] = '';
	
	    $this->layout = 'index';

		$_SESSION['desde'] = '';
		$_SESSION['hasta'] = '';
   		if (isset($this->data['desde'])) {
			$_SESSION['desde'] = $this->data['desde'];
		}
    	if (isset($this->data['hasta'])) {
			$_SESSION['hasta'] = $this->data['hasta'];
		}
         $this->setLogUsuario('Auditoria de Usuarios');
   	
    }
    
    public function dataTable(){
    	//print_r($_GET);
    	$desde = $_SESSION['desde'];
	    $hasta = $_SESSION['hasta'];
    	$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'desc';
    	switch ($_GET['iSortCol_0']) {
			
			case 1:
			$order='UsuarioLog.created '.$orderType;
			break;
			case 2:
			$order='UsuarioLog.nombre '.$orderType;
			break;
			case 3:
			$order='UsuarioLog.accion '.$orderType;
			break;
			case 4:
			$order='UsuarioLog.ip '.$orderType;
			break;
			default:
			$order='UsuarioLog.created '.$orderType;
			break;
		}
		
		
	
	    $rows = array();

    	
        	
        	$condicionSearch1 = ($_GET['sSearch_1'])?array('UsuarioLog.created LIKE '=> '%'.($_GET['sSearch_1']).'%'):array();
        	$condicionSearch2 = ($_GET['sSearch_2'])?array('UsuarioLog.nombre LIKE '=>'%'.$_GET['sSearch_2'].'%'):array();
        	$condicionSearch3 = ($_GET['sSearch_3'])?array('UsuarioLog.accion LIKE '=>'%'.$_GET['sSearch_3'].'%'):array();
        	$condicionSearch4 = ($_GET['sSearch_4'])?array('UsuarioLog.ip LIKE '=>'%'.$_GET['sSearch_4'].'%'):array();
        	$condicionSearch5=array();
    		if (($desde!='')&&($hasta!='')) {
				$condicionSearch5=array('UsuarioLog.created between ? and ?' => array($this->dateFormatSQL($desde), $this->dateFormatSQL($hasta)));
			}
        	$condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5);
        	
		    
        	
			$usuarioLogs = $this->UsuarioLog->find('all',array('conditions'=>$condicion,
                                                         'order' => $order, 'limit'=>$_GET['iDisplayLength'], 'offset'=>$_GET['iDisplayStart']));
			
			/*App::uses('ConnectionManager', 'Model');
			$connected = ConnectionManager::getDataSource('default');
    		$logs = $connected->getLog();
    		$lastLog = end($logs['log']);
    		echo $lastLog;*/
			$iTotal = $this->UsuarioLog->find('count',array('conditions'=> $condicion));
			
			
			
			
           
            
               
 

        foreach($usuarioLogs as $usuarioLog){
        	//print_r($usuarioLog);
            //estado y nro de orden
            
            
            $rows[] = array(
                $usuarioLog['UsuarioLog']['id'],
                
                $usuarioLog['UsuarioLog']['created'],
                $usuarioLog['UsuarioLog']['nombre'],
                $usuarioLog['UsuarioLog']['accion'],
                $usuarioLog['UsuarioLog']['ip']
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
}
?>
