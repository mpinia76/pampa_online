<?php 
ini_set('memory_limit', '-1');
date_default_timezone_set('America/Argentina/Buenos_Aires');
session_start();
class ChequerasController extends AppController {

	//public $scaffold;
   

	
	
    public function index(){
	    $this->layout = 'index';
	    
		$this->setLogUsuario('Carga y asignacion de chequeras');
		
    }

	function index_control($chequera_id){
        $this->layout = 'index';
	    
		 $this->set('chequera_id',$chequera_id);
    }

	/*public function control($chequera_id){
	    $this->layout = 'index';
	    
		 $this->set('chequera_id',$chequera_id);
		
    }*/

	public function getChequeras($cuenta_id){
        $this->layout = 'ajax';
        
        $this->set('chequeras', $this->Chequera->find('list',array('order' => array('Chequera.id ASC'), 'conditions' =>array('Chequera.cuenta_id =' => $cuenta_id, 'Chequera.estado =' => 1))));
       
    }
    
    
 	/*public function dataTable2($chequera_id = ""){

        $rows = array();
        $this->loadModel('ChequeraCheques');
       	$cheques=$this->ChequeraCheques->find('all',array('conditions' => array('chequera_id' => $chequera_id)));
       
        foreach ($cheques as $cheque) {
        	
        	//print_r($cheque);
        
        	switch ($cheque['ChequeraCheques']['estado']) {
        		case 0:
        			$estado='Disponible';
        		break;
        		case 1:
        			$estado='Utilizado';
        		break;
        		case 2:
        			$estado='Vencido';
        		break;
        		case 3:
        			$estado='Anulado';
        		break;
        		case 4:
        			$estado='Reemplazado';
        		break;
        		case 5:
        			$estado='Extraviado';
        		break;
        	}
        	$rows[] = array(
                $cheque['ChequeraCheques']['id'],
               
                $cheque['ChequeraCheques']['numero'],
               
                $estado
            );
            
        }
       
        
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));



       
    }*/
      
    
/* Cache query */
     public function get_chequeras($offset, $limit, $order, $search, $search1, $search2, $search3, $search4, $search5, $search6, $search7, $search8) {
       	
     	$result = Cache::read('get_chequeras', 'long');
        if (!$result) {
        	
        	$condicionSearch1 = ($search1)?array('or' => array('Cuenta.nombre LIKE '=>'%'.$search1.'%', 'Cuenta.nombre LIKE '=>'%'.$search1.'%')):array();
        	$condicionSearch2 = ($search2)?array('Chequera.numero LIKE '=> '%'.$search2.'%'):array();
        	$condicionSearch3 = ($search3)?array('Chequera.tipo LIKE '=> '%'.$search3.'%'):array();
        	$condicionSearch4 = ($search4)?array('Chequera.cantidad LIKE '=> '%'.$search4.'%'):array();
        	$condicionSearch5 = ($search5)?array('Chequera.inicio LIKE '=> '%'.$search5.'%'):array();
        	$condicionSearch6 = ($search6)?array('Chequera.final LIKE '=> '%'.$search6.'%'):array();
        	$condicionSearch7 = ($search7)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search7.'%', 'Usuario.apellido LIKE '=>'%'.$search7.'%')):array();
        	
        	switch ($search8) {
        		case 'Estado':
        		$condicionSearch8 = array();
        		break;
        		case 'Activas':
        		$condicionSearch8 = array('Chequera.estado = '=>1);
        		break;
        		case 'Inactivas':
        		$condicionSearch8 = array('Chequera.estado = '=>2);
        		break;
        		case 'Utilizadas':
        		$condicionSearch8 = array('Chequera.estado = '=>3);
        		break;
        		default:
        			$condicionSearch8 = array('Chequera.estado = '=>1);
        		break;
        	}
        	
        	
        	$condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,
			    'or' => 
	        	  array('Cuenta.nombre LIKE '=>'%'.$search.'%', 'Cuenta.nombre LIKE '=>'%'.$search.'%', 'Chequera.numero LIKE '=>'%'.$search.'%', 'Chequera.tipo LIKE '=>'%'.$search.'%'
	        	  , 'Chequera.cantidad LIKE '=>'%'.$search.'%', 'Chequera.inicio LIKE '=>'%'.$search.'%', 'Chequera.final LIKE '=>'%'.$search.'%', 'Usuario.nombre LIKE '=>'%'.$search.'%', 'Usuario.apellido LIKE '=>'%'.$search.'%'  	  	  
			    ));
            $result = $this->Chequera->find('all',array('order' => $order, 'conditions' => $condicion, 'limit'=>$limit, 'offset'=>$offset,'recursive' => 2));
           
			Cache::write('get_chequeras', $result, 'long');

		}

        return $result;
    }
    
    
 	public function get_chequerascount($search, $search1, $search2, $search3, $search4, $search5, $search6, $search7, $search8) {
       	$result = Cache::read('get_chequerascount', 'long');
        if (!$result) {
        	$condicionSearch1 = ($search1)?array('or' => array('Cuenta.nombre LIKE '=>'%'.$search1.'%', 'Cuenta.nombre LIKE '=>'%'.$search1.'%')):array();
        	$condicionSearch2 = ($search2)?array('Chequera.numero LIKE '=> '%'.$search2.'%'):array();
        	$condicionSearch3 = ($search3)?array('Chequera.tipo LIKE '=> '%'.$search3.'%'):array();
        	$condicionSearch4 = ($search4)?array('Chequera.cantidad LIKE '=> '%'.$search4.'%'):array();
        	$condicionSearch5 = ($search5)?array('Chequera.inicio LIKE '=> '%'.$search5.'%'):array();
        	$condicionSearch6 = ($search6)?array('Chequera.final LIKE '=> '%'.$search6.'%'):array();
        	$condicionSearch7 = ($search7)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search7.'%', 'Usuario.apellido LIKE '=>'%'.$search7.'%')):array();
        	
        	switch ($search8) {
        		case 'Todas':
        		$condicionSearch8 = array();
        		break;
        		case 'Activas':
        		$condicionSearch8 = array('Chequera.estado = '=>1);
        		break;
        		case 'Inactivas':
        		$condicionSearch8 = array('Chequera.estado = '=>2);
        		break;
        		case 'Utilizadas':
        		$condicionSearch8 = array('Chequera.estado = '=>3);
        		break;
        		default:
        			$condicionSearch8 = array('Chequera.estado = '=>1);
        		break;
        	}
        	
        	
        	$condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch3,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,
			    'or' => 
	        	  array('Cuenta.nombre LIKE '=>'%'.$search.'%', 'Cuenta.nombre LIKE '=>'%'.$search.'%', 'Chequera.numero LIKE '=>'%'.$search.'%', 'Chequera.tipo LIKE '=>'%'.$search.'%'
	        	  , 'Chequera.cantidad LIKE '=>'%'.$search.'%', 'Chequera.inicio LIKE '=>'%'.$search.'%', 'Chequera.final LIKE '=>'%'.$search.'%', 'Usuario.nombre LIKE '=>'%'.$search.'%', 'Usuario.apellido LIKE '=>'%'.$search.'%'  	  	  
			    ));
            $result = $this->Chequera->find('count',array('conditions' => $condicion,'recursive' => 2));
            /*App::uses('ConnectionManager', 'Model');
        	$dbo = ConnectionManager::getDatasource('default');
		    $logs = $dbo->getLog();
		    $lastLog = end($logs['log']);
		    
		    echo $lastLog['query'];*/
			Cache::write('get_chequerascount', $result, 'long');

		}

        return $result;
    }
    
	public function dataTable(){
    	//print_r($_GET);
    	$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'asc';
    	switch ($_GET['iSortCol_0']) {
			case 1:
			$order='Cuenta.nombre '.$orderType;
			break;
			case 2:
			$order='Chequera.numero '.$orderType;
			break;
			case 3:
			$order='Chequera.tipo '.$orderType;
			break;
			
			case 4:
			$order='Chequera.cantidad '.$orderType;
			break;
			case 5:
			$order='Chequera.inicio '.$orderType;
			break;
			
			
			case 6:
			$order='Chequera.final '.$orderType;
			break;
			
			case 8:
			$order='Usuario.nombre '.$orderType.', Usuario.apellido '.$orderType;
			break;
			default:
			$order='Chequera.id '.$orderType;
			break;
		}
		
		
	
	    $rows = array();

        
        $chequeras = $this->get_chequeras($_GET['iDisplayStart'], $_GET['iDisplayLength'], $order, $_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8']);
        $iTotal = $this->get_chequerascount($_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8']);
          
 		
        
		foreach ($chequeras as $chequera) {
        	
        	//print_r($chequera);
        	//$abre = ($feriado['Feriado']['abre'])?'SI':'NO';
        	switch ($chequera['Chequera']['estado']) {
        		case 1:
        			$estado='Activa';
        		break;
        		case 2:
        			$estado='Inactiva';
        		break;
        		case 3:
        			$estado='Utilizada';
        		break;
        	}
        	$rows[] = array(
                $chequera['Chequera']['id'],
                $chequera['Cuenta']['Banco']['banco'].'-'.$chequera['Cuenta']['nombre'],
                $chequera['Chequera']['numero'],
                $chequera['Chequera']['tipo'],
                $chequera['Chequera']['cantidad'],
                $chequera['Chequera']['inicio'],
                $chequera['Chequera']['final'],
                $chequera['Usuario']['apellido'].','.$chequera['Usuario']['nombre'],
                $estado
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
    
    
    
    

    /*public function dataTable($estado=1,$limit = ""){

        $rows = array();
        $this->loadModel('Chequera');
        if($limit == "todos"){
            $chequeras = $this->Chequera->find('all',array('conditions' => array('estado' => $estado),'order' => 'Chequera.id DESC','recursive' => 2)); 
        }else{
            $chequeras = $this->Chequera->find('all',array('conditions' => array('estado' => $estado),'limit' => $limit,'order' => 'Chequera.id DESC','recursive' => 2)); 
        }
        
        foreach ($chequeras as $chequera) {
        	
        	//print_r($chequera);
        	//$abre = ($feriado['Feriado']['abre'])?'SI':'NO';
        	switch ($chequera['Chequera']['estado']) {
        		case 1:
        			$estado='Activa';
        		break;
        		case 2:
        			$estado='Inactiva';
        		break;
        		case 3:
        			$estado='Utilizada';
        		break;
        	}
        	$rows[] = array(
                $chequera['Chequera']['id'],
                $chequera['Cuenta']['Banco']['banco'].'-'.$chequera['Cuenta']['nombre'],
                $chequera['Chequera']['numero'],
                $chequera['Chequera']['tipo'],
                $chequera['Chequera']['cantidad'],
                $chequera['Chequera']['inicio'],
                $chequera['Chequera']['final'],
                $chequera['Usuario']['apellido'].','.$chequera['Usuario']['nombre'],
                $estado
            );
            
        }
       
        
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));



       
    }*/

   public function getNumero($cuenta_id){
	//$this->layout = false;
   	$ultimo = $this->Chequera->find('first',array('conditions' => array('cuenta_id' => $cuenta_id),'order' => array('Chequera.id' => 'desc')));
		
       $ultimo_nro = $ultimo['Chequera']['ultimo'] + 1;
       $ultimo_rango = $ultimo['Chequera']['final'] + 1;
        //$this->set('ultimo_nro',$ultimo_nro);
        $result['ultimo_nro']=$ultimo_nro;
        $result['ultimo_rango']=$ultimo_rango;
       $this->set('resultado','OK');
        $this->set('mensaje','');
        $this->set('detalle',$result);
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));  
    }
    

 	public function crear(){
        $this->layout = 'form';
        
        $this->loadModel('Cuenta');
        $cuentas = $this->Cuenta->find('all',array('conditions' => array('emite_cheques' => 1),'recursive' => 1));
        foreach($cuentas as $cuenta){
            $list[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." - ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$list);
        
        
        $this->set('tipos',array('COMUN' => 'Comun','DIFERIDO' => 'Diferido','ELECTRONICO' => 'Electronico'));
        $this->set('cantidad',array('25' => '25','50' => '50'));
		$this->set('estados',array('1' => 'Activa','2' => 'Inactiva','3' => 'Utilizada'));
		 $this->loadModel('Usuario');
        
        $usuarios = $this->Usuario->find('all',array('order' => 'Usuario.nombre asc'));
        foreach($usuarios as $usuario){
	        if ($usuario['Usuario']['admin'] != '1'){
		        $this->loadModel('UsuarioPermiso');
		        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($usuario['Usuario']['id']);
		        
		    	foreach($permisos as $permiso){
		               if ($permiso['UsuarioPermiso']['permiso_id']==138) {
		               		$list2[$usuario['Usuario']['id']] = $usuario['Usuario']['apellido'].", ".$usuario['Usuario']['nombre'];
		               		
		               }
		        }
	        }
	        else {
	        	$list2[$usuario['Usuario']['id']] = $usuario['Usuario']['apellido'].", ".$usuario['Usuario']['nombre'];
	        }
            
        }
        $this->set('usuarios',$list2);
    }

	public function editar($id = null){
       $this->layout = 'form';

       $this->Chequera->id = $id;
        $this->request->data = $this->Chequera->read();
        $chequera = $this->request->data;
       
       
        $this->loadModel('Cuenta');
        $cuentas = $this->Cuenta->find('all',array('conditions' => array('emite_cheques' => 1),'recursive' => 1));
        foreach($cuentas as $cuenta){
            $list[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." - ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$list);
        
        
        $this->set('tipos',array('COMUN' => 'Comun','DIFERIDO' => 'Diferido','ELECTRONICO' => 'Electronico'));
        $this->set('cantidad',array('25' => '25','50' => '50'));
		$this->set('estados',array('1' => 'Activa','2' => 'Inactiva','3' => 'Utilizada'));
		 $this->loadModel('Usuario');
        
        $usuarios = $this->Usuario->find('all',array('order' => 'Usuario.nombre asc'));
        foreach($usuarios as $usuario){
	        if ($usuario['Usuario']['admin'] != '1'){
		        $this->loadModel('UsuarioPermiso');
		        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($usuario['Usuario']['id']);
		        
		    	foreach($permisos as $permiso){
		               if ($permiso['UsuarioPermiso']['permiso_id']==138) {
		               		$list2[$usuario['Usuario']['id']] = $usuario['Usuario']['apellido'].", ".$usuario['Usuario']['nombre'];
		               		
		               }
		        }
	        }
	        else {
	        	$list2[$usuario['Usuario']['id']] = $usuario['Usuario']['apellido'].", ".$usuario['Usuario']['nombre'];
	        }
            
        }
        $this->set('usuarios',$list2);
        $this->set('chequera', $this->Chequera->read());
    }

    public function guardar(){

     //load modules
        $this->loadModel('Chequera');
        
		$this->loadModel('ChequeraCheque');
        //print_r($this->request->data);
        if(!empty($this->request->data)) {

        	$chequera = $this->request->data['Chequera'];
            $chequera['numero']=$chequera['ultimo'];
        	
        	
           
           
            $this->Chequera->set($chequera);
            if(!$this->Chequera->validates()){
                 $errores['Chequera'] = $this->Chequera->validationErrors;
            }
        	$chequeraAntesDeModificar = $this->Chequera->find('first',array('conditions' => array('Chequera.id' => $chequera['id'])));
        	//print_r($this->request->data);
        	if ($chequeraAntesDeModificar) {
        		if (($chequeraAntesDeModificar['Chequera']['inicio']!=$chequera['inicio'])||($chequeraAntesDeModificar['Chequera']['final']!=$chequera['final'])) {
	        		$cheques=$this->ChequeraCheque->find('first',array('conditions' => array('ChequeraCheque.estado <>' => 0,'ChequeraCheque.chequera_id' => $chequera['id'])));
		        	if ($cheques) {
		            	 $errores[Chequera][inicio]='No es posible editar los datos de una chequera en uso.';
		            }
	        	}
           
        	}
        	
            $chequeraUsada = $this->Chequera->find('first',array('conditions'=>array('Chequera.id <>' => $chequera['id'],'Chequera.cuenta_id' => $chequera['cuenta_id'],'Chequera.numero' => $chequera['numero'])));
            
            if ($chequeraUsada) {
            	 $errores[Chequera][numero]='El numero de chequera para el banco y la cuenta seleccionados ya existe.';
            }
            
            $chequeraUsada = $this->Chequera->find('first',array('conditions' => array('Chequera.id <>' => $chequera['id'],
			    'Chequera.cuenta_id' => $chequera['cuenta_id'],
	        	'AND' => array('or' => array(
	        		'Chequera.inicio between ? and ?' => array($chequera['inicio'], $chequera['final']),
			      'Chequera.final between ? and ?' => array($chequera['inicio'], $chequera['final'])
	        	)))));
         
			  
			  
			  
        	if ($chequeraUsada) {
            	 $errores[Chequera][inicio]='El intervalo de cheques para el banco y la cuenta seleccionados se encuentra incluido en la chequera Nro '.$chequeraUsada['Chequera']['numero'];
            }
          
           /*App::uses('ConnectionManager', 'Model');
        	$dbo = ConnectionManager::getDatasource('default');
		    $logs = $dbo->getLog();
		    print_r($logs);
		    $lastLog = end($logs['log']);
		    
		    echo $lastLog['query'];*/
           
            //print_r($errores);
            
            //muestro resultado
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
	            try {
				    $this->Chequera->save();
			
				    
				    $chequeraUsada = $this->ChequeraCheque->find('first',array('conditions'=>array('ChequeraCheque.chequera_id'=>$this->Chequera->id,'ChequeraCheque.estado <>'=>0)));
				    
				    if (!$chequeraUsada) {
					     //guardo reserva extras
		                $this->ChequeraCheque->deleteAll(array('chequera_id' => $this->Chequera->id), false);
		                
		                $inicio = intval($chequera['inicio']);
		                $final = intval($chequera['final']);
		                for ($i = $inicio; $i < $final+1; $i++) {
		                	
		               
	                            $this->ChequeraCheque->create();
	                            $this->ChequeraCheque->set('numero',str_pad($i, 8,'0',STR_PAD_LEFT)  );
	                            $this->ChequeraCheque->set('estado',0);
	                            $this->ChequeraCheque->set('chequera_id',$this->Chequera->id);
	                            $this->ChequeraCheque->save();
	                            
	                        }
				    }
	               
	                    
	                
	
	                $this->set('resultado','OK');
	                $this->set('mensaje','Datos guardados');
	                $this->set('detalle','');
				} catch (PDOException $e) {
				  //print_r($e);
				   $this->set('resultado','ERROR');
                	$this->set('mensaje','No se pudo guardar');
                	$this->set('detalle',$e->errorInfo[1]);
				}
               
            }
            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }

        
       
    }

	public function eliminar($id = null){
        if(!empty($this->request->data)) {

         	$this->loadModel('Feriados');
         	$this->loadModel('FeriadoHorario');
         	$this->FeriadoHorario->deleteAll(array('feriado_id' => $this->request->data['id']), false);
            
            $this->Feriados->delete($this->request->data['id'],true);   
		
        
	        $this->set('resultado','OK');
	        $this->set('mensaje','Feriado eliminado');
	        $this->set('detalle','');
	        
	        $this->set('_serialize', array(
	            'resultado',
	            'mensaje' ,
	            'detalle' 
	        ));
        }
    }
    
	
    
}
?>
