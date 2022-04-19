<?php
session_start();
class CobroTarjetasController extends AppController {
    public $components = array('ExportXls');
	public function dateFormatSQL($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }
    
    public function index(){
        $this->layout = 'index';
        $this->setLogUsuario('Transacciones con tarjeta');
    	
        
    	if (isset($this->data['desde'])) {
			$_SESSION['cobroTarjetadesde'] = $this->data['desde'];
		}
		else{
			$_SESSION['cobroTarjetadesde'] = '01/01/'.date('Y');
		}
    	if (isset($this->data['hasta'])) {
			$_SESSION['cobroTarjetahasta'] = $this->data['hasta'];
		}
    	else{
			$_SESSION['cobroTarjetahasta'] = '31/12/'.date('Y');
		}
    	if ((isset($this->data['limpiar']))&&($this->data['limpiar']==1)) {
			$_SESSION["cobroTarjetaLocacionSearch"]='';
			$_SESSION["cobroTarjetaMarcaSearch"]='';
			$_SESSION["cobroTarjetaComercioSearch"]='';
			$_SESSION["cobroTarjetaCuponSearch"]='';
			$_SESSION["cobroTarjetaAutorizacionSearch"]='';
			$_SESSION["cobroTarjetaLoteSearch"]='';
			$_SESSION["cobroTarjetaLiquidacionSearch"]='';
			$_SESSION["cobroTarjetaTitularSearch"]='';
			$_SESSION["cobroTarjetaConceptoSearch"]='';
			$_SESSION["cobroTarjetaMontoSearch"]='';
			$_SESSION["cobroTarjetaCuotasSearch"]='';
		}
      
    }
    
	public function index2($cobro_tarjeta_tipo_id,$lote){
        $this->layout = 'index';
        
   		$this->set('nro_lote',$lote);
		$this->set('cobro_tarjeta_tipo_id',$cobro_tarjeta_tipo_id);
      
    }
    
	public function get_transacciones_detalle($cobro_tarjeta_tipo_id,$lote) {
        $result = Cache::read('get_transacciones_detalle', 'long');
            if (!$result) {
                 $transacciones = $this->CobroTarjeta->find('all',array('conditions' => array('CobroTarjeta.cobro_tarjeta_tipo_id' => $cobro_tarjeta_tipo_id,'CobroTarjeta.lote' => $lote), 'order' => 'ReservaCobro.fecha asc','recursive' => 2));
                Cache::write('get_transacciones_detalle', $transacciones, 'long');
            }
            /*App::uses('ConnectionManager', 'Model');
        	$dbo = ConnectionManager::getDatasource('default');
		    $logs = $dbo->getLog();
		    $lastLog = $logs['log'][0];
		    echo $lastLog['query'];*/
            return $transacciones;
    }

    public function dataTable2($cobro_tarjeta_tipo_id,$lote){
        $this->layout = 'ajax';
       	
        $rows = array();
        $transacciones = $this->get_transacciones_detalle($cobro_tarjeta_tipo_id,$lote);
        foreach($transacciones as $transaccion){
            
            $monto = $transaccion['CobroTarjeta']['monto_neto'] + $transaccion['CobroTarjeta']['interes'];
            $rows[] = array(
                
                $transaccion['ReservaCobro']['fecha'],
               	$transaccion['CobroTarjeta']['lote_nuevo'],
                $transaccion['CobroTarjeta']['lote'],
               
                '$'.$monto
            );
        }
        $output = array(
        	"sEcho" => intval($_GET['sEcho']),
        	"iTotalRecords" => count($rows),
	        "iTotalDisplayRecords" => count($rows),
	        "aaData" => array()
	    );
        
        $output['aaData'] = $rows;
        $this->set('aoData',$output);
        //print_r($output);
        $this->set('_serialize', 'aoData');
    }
    

public function get_transacciones($desde, $hasta, $offset, $limit, $orderField, $orderType, $search, $search_2, $search_3, $search_4, $search_5, $search_6, $search_7, $search_8, $search_9, $search_10, $search_11, $search_12) {
    	
    	$estado = explode('_', $search);
		
    	$search = (($estado[1])&&($estado[0]=='EST'))?'':$search;
		
		
		
		if(($estado[1])&&($estado[0]=='EST')){
			
			switch ($estado[1]) {
				case 1:
				 $filtroEstado='1=1';
				 $_SESSION["cobroTarjetaSearchEstado"]='EST_1';
				break;
				case 2:
				 $filtroEstado=array('CobroTarjetaLote.id' => null);
				 $_SESSION["cobroTarjetaSearchEstado"]='EST_2';
				break;
				case 3:
				 $filtroEstado=array('OR' => array(array('CobroTarjetaLote.id' => null),array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion' => '')));
				 $_SESSION["cobroTarjetaSearchEstado"]='EST_3';
				break;
				case 4:
				 $filtroEstado=array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion <>' => '');
				 $_SESSION["cobroTarjetaSearchEstado"]='EST_4';
				break;
				
				
			}
		}
		elseif(!$estado[1]&&!$_SESSION["cobroTarjetaSearchEstado"]){
			$filtroEstado=array('OR' => array(array('CobroTarjetaLote.id' => null),array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion' => '')));
			$_SESSION["cobroTarjetaSearchEstado"]='EST_3';
			
		}
    	elseif(!$_SESSION["cobroTarjetaSearchEstado"]){
			$filtroEstado=array('OR' => array(array('CobroTarjetaLote.id' => null),array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion' => '')));
			$_SESSION["cobroTarjetaSearchEstado"]='EST_3';
		}
    	else{
    		switch ($_SESSION["cobroTarjetaSearchEstado"]) {
				case 'EST_1':
				 $filtroEstado='1=1';
				 
				break;
				case 'EST_2':
				 $filtroEstado=array('CobroTarjetaLote.id' => null);
				
				break;
				case 'EST_3':
				 $filtroEstado=array('OR' => array(array('CobroTarjetaLote.id' => null),array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion' => '')));
				 
				break;
				case 'EST_4':
				 $filtroEstado=array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion <>' => '');
				 
				break;
				
				
			}
			
		}
		
		if ($search!='') {
			if ($search==':-:-@') {
				$search='';
			}
			$_SESSION["cobroTarjetaSearch"]=$search;
		}
    	if ($search_2!='') {
			if ($search_2==':-:-@') {
				$search_2='';
			}
			elseif ($search_2==':-:-@@@') {
				$search_2='';
				
			}
			$_SESSION["cobroTarjetaLocacionSearch"]=$search_2;
		}
     	if ($search_3!='') {
			if ($search_3==':-:-@') {
				$search_3='';
			}
			elseif ($search_3==':-:-@@@') {
				$search_3='';
				
			}
			$_SESSION["cobroTarjetaMarcaSearch"]=$search_3;
		}
     	if ($search_4!='') {
			if ($search_4==':-:-@') {
				$search_4='';
			}
			elseif ($search_4==':-:-@@@') {
				$search_4='';
				
			}
			$_SESSION["cobroTarjetaComercioSearch"]=$search_4;
		}
    	if ($search_5!='') {
			if ($search_5==':-:-@') {
				$search_5='';
			}
			elseif ($search_5==':-:-@@@') {
				$search_5='';
				
			}
			$_SESSION["cobroTarjetaCuponSearch"]=$search_5;
		}
   		 if ($search_6!='') {
			if ($search_6==':-:-@') {
				$search_6='';
			}
			elseif ($search_6==':-:-@@@') {
				$search_6='';
				
			}
			$_SESSION["cobroTarjetaAutorizacionSearch"]=$search_6;
		}
    	if ($search_7!='') {
			if ($search_7==':-:-@') {
				$search_7='';
			}
			elseif ($search_7==':-:-@@@') {
				$search_7='';
				
			}
			$_SESSION["cobroTarjetaLoteSearch"]=$search_7;
		}
    	if ($search_8!='') {
			if ($search_8==':-:-@') {
				$search_8='';
			}
			elseif ($search_8==':-:-@@@') {
				$search_8='';
				
			}
			$_SESSION["cobroTarjetaLiquidacionSearch"]=$search_8;
		}
    	if ($search_9!='') {
			if ($search_9==':-:-@') {
				$search_9='';
			}
			elseif ($search_9==':-:-@@@') {
				$search_9='';
				
			}
			$_SESSION["cobroTarjetaTitularSearch"]=$search_9;
		}
    	if ($search_10!='') {
			if ($search_10==':-:-@') {
				$search_10='';
			}
			elseif ($search_10==':-:-@@@') {
				$search_10='';
				
			}
			$_SESSION["cobroTarjetaConceptoSearch"]=$search_10;
		}
    	if ($search_11!='') {
			if ($search_11==':-:-@') {
				$search_11='';
			}
			elseif ($search_11==':-:-@@@') {
				$search_11='';
				
			}
			$_SESSION["cobroTarjetaMontoSearch"]=$search_11;
		}
    	if ($search_12!='') {
			if ($search_12==':-:-@') {
				$search_12='';
			}
			elseif ($search_12==':-:-@@@') {
				$search_12='';
				
			}
			$_SESSION["cobroTarjetaCuotasSearch"]=$search_12;
		}
		
		
		$filtroLocacion=($_SESSION["cobroTarjetaLocacionSearch"])?array('CobroTarjetaPosnet.posnet LIKE '=>'%'.$_SESSION["cobroTarjetaLocacionSearch"].'%'):array(1=>1);
		$filtroMarca=($_SESSION["cobroTarjetaMarcaSearch"])?array('CobroTarjetaTipo.marca LIKE '=>'%'.$_SESSION["cobroTarjetaMarcaSearch"].'%'):array(1=>1);
		$filtroComercio=($_SESSION["cobroTarjetaComercioSearch"])?array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.$_SESSION["cobroTarjetaComercioSearch"].'%'):array(1=>1);
		$filtroCupon=($_SESSION["cobroTarjetaCuponSearch"])?array('CobroTarjeta.cupon LIKE '=>'%'.$_SESSION["cobroTarjetaCuponSearch"].'%'):array(1=>1);
		$filtroAutorizacion=($_SESSION["cobroTarjetaAutorizacionSearch"])?array('CobroTarjeta.autorizacion LIKE '=>'%'.$_SESSION["cobroTarjetaAutorizacionSearch"].'%'):array(1=>1);
		$filtroLote=($_SESSION["cobroTarjetaLoteSearch"])?array('CobroTarjeta.lote_nuevo LIKE '=>'%'.$_SESSION["cobroTarjetaLoteSearch"].'%'):array(1=>1);
		$filtroLiquidacion=($_SESSION["cobroTarjetaLiquidacionSearch"])?array('CobroTarjeta.lote LIKE '=>'%'.$_SESSION["cobroTarjetaLiquidacionSearch"].'%'):array(1=>1);
		$filtroTitular=($_SESSION["cobroTarjetaTitularSearch"])?array('CobroTarjeta.titular LIKE '=>'%'.$_SESSION["cobroTarjetaTitularSearch"].'%'):array(1=>1);
		$filtroConcepto=($_SESSION["cobroTarjetaConceptoSearch"])?array('Reserva.numero LIKE '=>'%'.$_SESSION["cobroTarjetaConceptoSearch"].'%'):array(1=>1);
		$filtroMonto=($_SESSION["cobroTarjetaMontoSearch"])?'CobroTarjeta.monto_neto+CobroTarjeta.interes LIKE \'%'.$_SESSION["cobroTarjetaMontoSearch"].'%\'':array(1=>1);
		$filtroCuotas=($_SESSION["cobroTarjetaCuotasSearch"])?array('CobroTarjeta.cuotas LIKE '=>'%'.$_SESSION["cobroTarjetaCuotasSearch"].'%'):array(1=>1);
		if (($desde!='')&&($hasta!='')) {
			
			
			
			
			$condicion=array($filtroEstado,'ReservaCobro.fecha between ? and ?' => array($this->dateFormatSQL($desde), $this->dateFormatSQL($hasta)),
			    $filtroLocacion,$filtroMarca,$filtroComercio,$filtroCupon,$filtroAutorizacion,$filtroLote,$filtroLiquidacion,$filtroTitular,$filtroConcepto 
			    ,$filtroMonto ,$filtroCuotas );
		}
		else
		$condicion=array($filtroEstado,$filtroLocacion,$filtroMarca,$filtroComercio,$filtroCupon,$filtroAutorizacion,$filtroLote,$filtroLiquidacion,$filtroTitular,$filtroConcepto 
			    ,$filtroMonto ,$filtroCuotas);
        $result = Cache::read('get_transacciones', 'long');
    	if (!$result) {
        	$order = $orderField.' '.$orderType;
        	//echo $order;
	        
	        	
	        	//$result = $this->CobroTarjeta->find('all',array('order' => $order, 'conditions' => $condicion, 'limit'=>$limit, 'offset'=>$offset,'recursive' => 2));
	        
        	$result = $this->CobroTarjeta->find('all',array('joins' => array(
        array(
            'table' => 'cobro_tarjeta_tipos',
            'alias' => 'CobroTarjetaTipo',
            'type' => 'LEFT',
            'conditions' => array(
                'CobroTarjetaTipo.id = CobroTarjeta.cobro_tarjeta_tipo_id'
            )
        ),
        array(
            'table' => 'reserva_cobros',
            'alias' => 'ReservaCobro',
            'type' => 'LEFT',
            'conditions' => array(
                'ReservaCobro.id = CobroTarjeta.reserva_cobro_id'
            )
        ),
        array(
            'table' => 'reservas',
            'alias' => 'Reserva',
            'type' => 'LEFT',
            'conditions' => array(
                'Reserva.id = ReservaCobro.reserva_id'
            )
        ),
        array(
            'table' => 'cobro_tarjeta_lotes',
            'alias' => 'CobroTarjetaLote',
            'type' => 'LEFT',
            'conditions' => array(
                'CobroTarjetaLote.id = CobroTarjeta.cobro_tarjeta_lote_id'
            )
        ),
        array(
            'table' => 'cobro_tarjeta_posnets',
            'alias' => 'CobroTarjetaPosnet',
            'type' => 'LEFT',
            'conditions' => array(
                'CobroTarjetaPosnet.id = CobroTarjetaTipo.cobro_tarjeta_posnet_id'
            )
        )
        
        
    ),'fields' => array('ReservaCobro.fecha',
'CobroTarjetaPosnet.posnet','CobroTarjetaLote.id','CobroTarjetaLote.fecha_cierre','CobroTarjetaLote.fecha_acreditacion','CobroTarjeta.id','ReservaCobro.id'
    ,'CobroTarjetaTipo.marca','CobroTarjetaTipo.nro_comercio','CobroTarjeta.cupon','CobroTarjeta.autorizacion',
    'CobroTarjeta.lote','CobroTarjeta.lote_nuevo','CobroTarjeta.titular','CobroTarjeta.monto_neto','CobroTarjeta.interes','CobroTarjeta.cuotas','Reserva.numero'),'order' => $order, 'conditions' => $condicion, 'limit'=>$limit, 'offset'=>$offset,'recursive' => -1));
        	
        	
        	/*App::uses('ConnectionManager', 'Model');
        	$dbo = ConnectionManager::getDatasource('default');
		    $logs = $dbo->getLog();
		    $lastLog = $logs['log'][0];
		    echo $lastLog['query'];*/
	       Cache::write('get_transacciones', $result, 'long');
	    }
           
            return $result;
    }
    
	public function get_transaccionescount($desde, $hasta, $search) {
		
		switch ($_SESSION["cobroTarjetaSearchEstado"]) {
				case 'EST_1':
				 $filtroEstado='1=1';
				 
				break;
				case 'EST_2':
				 $filtroEstado=array('CobroTarjetaLote.id' => null);
				
				break;
				case 'EST_3':
				 $filtroEstado=array('OR' => array(array('CobroTarjetaLote.id' => null),array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion' => '')));
				 
				break;
				case 'EST_4':
				 $filtroEstado=array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion <>' => '');
				 
				break;
				
				
			}
		$filtroLocacion=($_SESSION["cobroTarjetaLocacionSearch"])?array('CobroTarjetaPosnet.posnet LIKE '=>'%'.$_SESSION["cobroTarjetaLocacionSearch"].'%'):array(1=>1);
		$filtroMarca=($_SESSION["cobroTarjetaMarcaSearch"])?array('CobroTarjetaTipo.marca LIKE '=>'%'.$_SESSION["cobroTarjetaMarcaSearch"].'%'):array(1=>1);
		$filtroComercio=($_SESSION["cobroTarjetaComercioSearch"])?array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.$_SESSION["cobroTarjetaComercioSearch"].'%'):array(1=>1);
		$filtroCupon=($_SESSION["cobroTarjetaCuponSearch"])?array('CobroTarjeta.cupon LIKE '=>'%'.$_SESSION["cobroTarjetaCuponSearch"].'%'):array(1=>1);
		$filtroAutorizacion=($_SESSION["cobroTarjetaAutorizacionSearch"])?array('CobroTarjeta.autorizacion LIKE '=>'%'.$_SESSION["cobroTarjetaAutorizacionSearch"].'%'):array(1=>1);
		$filtroLote=($_SESSION["cobroTarjetaLoteSearch"])?array('CobroTarjeta.lote_nuevo LIKE '=>'%'.$_SESSION["cobroTarjetaLoteSearch"].'%'):array(1=>1);
		$filtroLiquidacion=($_SESSION["cobroTarjetaLiquidacionSearch"])?array('CobroTarjeta.lote LIKE '=>'%'.$_SESSION["cobroTarjetaLiquidacionSearch"].'%'):array(1=>1);
		$filtroTitular=($_SESSION["cobroTarjetaTitularSearch"])?array('CobroTarjeta.titular LIKE '=>'%'.$_SESSION["cobroTarjetaTitularSearch"].'%'):array(1=>1);
		$filtroConcepto=($_SESSION["cobroTarjetaConceptoSearch"])?array('Reserva.numero LIKE '=>'%'.$_SESSION["cobroTarjetaConceptoSearch"].'%'):array(1=>1);
		$filtroMonto=($_SESSION["cobroTarjetaMontoSearch"])?'CobroTarjeta.monto_neto+CobroTarjeta.interes LIKE \'%'.$_SESSION["cobroTarjetaMontoSearch"].'%\'':array(1=>1);
		$filtroCuotas=($_SESSION["cobroTarjetaCuotasSearch"])?array('CobroTarjeta.cuotas LIKE '=>'%'.$_SESSION["cobroTarjetaCuotasSearch"].'%'):array(1=>1);
		if (($desde!='')&&($hasta!='')) {
		$condicion=array($filtroEstado,'ReservaCobro.fecha between ? and ?' => array($this->dateFormatSQL($desde), $this->dateFormatSQL($hasta)),
			    $filtroLocacion,$filtroMarca,$filtroComercio,$filtroCupon,$filtroAutorizacion,$filtroLote,$filtroLiquidacion,$filtroTitular,$filtroConcepto 
			    ,$filtroMonto ,$filtroCuotas);
		}
		else
		$condicion=array($filtroEstado,$filtroLocacion,$filtroMarca,$filtroComercio,$filtroCupon,$filtroAutorizacion,$filtroLote,$filtroLiquidacion,$filtroTitular,$filtroConcepto 
			    ,$filtroMonto ,$filtroCuotas);
        
    	$result = Cache::read('get_transaccionescount', 'long');
        if (!$result) {
	         $result = $this->CobroTarjeta->find('count',array('joins' => array(
        array(
            'table' => 'cobro_tarjeta_tipos',
            'alias' => 'CobroTarjetaTipo',
            'type' => 'LEFT',
            'conditions' => array(
                'CobroTarjetaTipo.id = CobroTarjeta.cobro_tarjeta_tipo_id'
            )
        ),
        array(
            'table' => 'reserva_cobros',
            'alias' => 'ReservaCobro',
            'type' => 'LEFT',
            'conditions' => array(
                'ReservaCobro.id = CobroTarjeta.reserva_cobro_id'
            )
        ),
        array(
            'table' => 'reservas',
            'alias' => 'Reserva',
            'type' => 'LEFT',
            'conditions' => array(
                'Reserva.id = ReservaCobro.reserva_id'
            )
        ),
        array(
            'table' => 'cobro_tarjeta_lotes',
            'alias' => 'CobroTarjetaLote',
            'type' => 'LEFT',
            'conditions' => array(
                'CobroTarjetaLote.id = CobroTarjeta.cobro_tarjeta_lote_id'
            )
        ),
        array(
            'table' => 'cobro_tarjeta_posnets',
            'alias' => 'CobroTarjetaPosnet',
            'type' => 'LEFT',
            'conditions' => array(
                'CobroTarjetaPosnet.id = CobroTarjetaTipo.cobro_tarjeta_posnet_id'
            )
        )
        
        
    ),'conditions' => $condicion,'recursive' => -1));
	       Cache::write('get_transaccionescount', $result, 'long');
	       }
          return $result;
    }

    public function dataTable(){
        //$this->layout = 'ajax';
        switch ($_GET['iSortCol_0']) {
			case 2:
			$orderField='ReservaCobro.fecha';
			break;
			case 3:
			$orderField='CobroTarjetaPosnet.posnet';
			break;
			case 4:
			$orderField='CobroTarjetaTipo.marca';
			break;
			case 5:
			$orderField='CobroTarjetaTipo.nro_comercio';
			break;
			case 6:
			$orderField='CobroTarjeta.cupon';
			break;
			case 7:
			$orderField='CobroTarjeta.autorizacion';
			break;
			case 8:
			$orderField='CobroTarjeta.lote_nuevo';
			break;
			case 9:
			$orderField='CobroTarjeta.lote';
			break;
			case 10:
			$orderField='CobroTarjeta.titular';
			break;
			case 11:
			$orderField='Reserva.numero';
			break;
			case 13:
			$orderField='CobroTarjeta.cuotas';
			break;
			default:
			$orderField='CobroTarjeta.id';
			break;
		}
		
		$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'desc';
        
		
	   
	    
	    $desde = $_SESSION['cobroTarjetadesde'];
	    $hasta = $_SESSION['cobroTarjetahasta'];
		
        $rows = array();
        
        $transacciones = $this->get_transacciones($desde, $hasta,$_GET['iDisplayStart'], $_GET['iDisplayLength'], $orderField, $orderType, $_GET['sSearch'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8'], $_GET['sSearch_9'], $_GET['sSearch_10'], $_GET['sSearch_11'], $_GET['sSearch_12'], $_GET['sSearch_13']);
        $iTotal = $this->get_transaccionescount( $desde, $hasta,$_GET['sSearch']);
	    

		$estado = explode('_', $_GET['sSearch']);
        foreach($transacciones as $transaccion){
        	//print_r($transaccion);
            if($transaccion['CobroTarjetaLote']['id'] == ''){
                //$estado = 'Pendiente de cierre';
                $estado = 'Pendiente de acreditacion';
            }elseif($transaccion['CobroTarjetaLote']['fecha_cierre'] != '' and $transaccion['CobroTarjetaLote']['fecha_acreditacion'] == ''){
                $estado = 'Pendiente de acreditacion';
            }elseif($transaccion['CobroTarjetaLote']['fecha_cierre'] != '' and $transaccion['CobroTarjetaLote']['fecha_acreditacion'] != ''){
                $estado = 'Acreditado';
            }else{
                $estado = 'Revisar';
            }
     
            $monto = $transaccion['CobroTarjeta']['monto_neto'] + $transaccion['CobroTarjeta']['interes'];
            $rows[] = array(
                $transaccion['CobroTarjeta']['id'],
                $transaccion['ReservaCobro']['id'],
                $transaccion['ReservaCobro']['fecha'],
                $transaccion['CobroTarjetaPosnet']['posnet'],
                $transaccion['CobroTarjetaTipo']['marca'],
                $transaccion['CobroTarjetaTipo']['nro_comercio'],
                $transaccion['CobroTarjeta']['cupon'],
                $transaccion['CobroTarjeta']['autorizacion'],
                $transaccion['CobroTarjeta']['lote_nuevo'],
                $transaccion['CobroTarjeta']['lote'],
                $transaccion['CobroTarjeta']['titular'],
                'Reserva nro: '.$transaccion['Reserva']['numero'],
                '$'.$monto,
                $transaccion['CobroTarjeta']['cuotas'],
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
        $this->set('_serialize', 'aoData');
    }
    
    public  function agregar(){
        $this->layout = 'ajax';
        
        $this->loadModel('CobroTarjetaPosnet');
        $this->set('posnets',$this->CobroTarjetaPosnet->find('list',array('order' => 'posnet asc')));
        
        $this->set('reserva_cobro',$this->request->data['ReservaCobro']);
        $this->set('cobro_tarjeta_tipos', $this->CobroTarjeta->CobroTarjetaTipo->find('list'));
    }
    
	public function guardar(){
        $data = $this->request->data['CobroTarjeta']; 
       	if (!$data['fecha_pago']) {
       		 $errores['CobroTarjeta']['fecha_pago'][]='Ingrese una fecha valida';
       		 $this->set('resultado','ERROR');
             $this->set('mensaje','No se pudo guardar');
             $this->set('detalle',$errores);
       	}
       	else{
	        //actualizo si es un registro existente
	        if(isset($data['id'])){
	            $this->CobroTarjeta->id = $data['id'];
	            $this->CobroTarjeta->read();
	        }
	        $this->CobroTarjeta->set($data);
	        
	        if(!$this->CobroTarjeta->validates()){
	            $errores['CobroTarjeta'] = $this->CobroTarjeta->validationErrors;
	            $this->set('resultado','ERROR');
	            $this->set('mensaje','No se pudo guardar');
	            $this->set('detalle',$errores);
	        }else{
	            $this->CobroTarjeta->save();
	            $this->set('resultado','OK');
	            $this->set('mensaje','Datos guardados');
	            $this->set('detalle','');
	        }
       	}
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
	public function guardarMultiple(){
		$data = $this->request->data['CobroTarjeta']; 
        $ids =$data['ids']; 
        $ids = explode(",",$ids);
        foreach ($ids as $id) {
	        if (!$data['fecha_pago']) {
	       		 $errores['CobroTarjeta']['fecha_pago'][]='Ingrese una fecha valida';
	       		 $this->set('resultado','ERROR');
	             $this->set('mensaje','No se pudo guardar');
	             $this->set('detalle',$errores);
	       	}
	       	else{
		        //actualizo si es un registro existente
		        if(isset($id)){
		            $this->CobroTarjeta->id = $id;
		            $cobro_tarjeta = $this->CobroTarjeta->read();
		            $this->loadModel('CobroTarjetaLote');
			        $this->CobroTarjetaLote->id = $cobro_tarjeta['CobroTarjeta']['cobro_tarjeta_lote_id'];
			        $lote = $this->CobroTarjetaLote->read();
			        
			        if (!$lote['CobroTarjetaLote']['fecha_acreditacion']) {
		        
				        $this->CobroTarjeta->set($data);
				        
				        if(!$this->CobroTarjeta->validates()){
				            $errores['CobroTarjeta'] = $this->CobroTarjeta->validationErrors;
				            $this->set('resultado','ERROR');
				            $this->set('mensaje','No se pudo guardar');
				            $this->set('detalle',$errores);
				        }else{
				            $this->CobroTarjeta->save();
				            $this->set('resultado','OK');
				            $this->set('mensaje','Datos guardados');
				            $this->set('detalle','');
				        }
			        }
			        else{
			        	$this->set('resultado','ERROR');
				        $this->set('mensaje','Una de las transacciones ya fue acreditada');
				        $this->set('detalle','');   
				        break; 
			        }
			    }
	       	}
        }
       	
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
	public function controlarAcreditacion(){
		$id = $this->request->data['cobro_tarjeta_id'];
        $this->CobroTarjeta->id = $id;
        $cobro_tarjeta = $this->CobroTarjeta->read();
        
        $this->loadModel('CobroTarjetaLote');
        $this->CobroTarjetaLote->id = $cobro_tarjeta['CobroTarjeta']['cobro_tarjeta_lote_id'];
        $lote = $this->CobroTarjetaLote->read();
        
        if (!$lote['CobroTarjetaLote']['fecha_acreditacion']) {
	        
	        $this->set('resultado','OK');
	        $this->set('mensaje','');
	        $this->set('detalle','');
        }    
        else{   
        
	        $this->set('resultado','ERROR');
	        $this->set('mensaje','Transaccion acreditada - ');
	        $this->set('detalle','No se pueden realizar cambios');    
       }
       
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
	public function detalle($ids){
        $this->layout = 'form';
        $this->set('ids',$ids);
        
        
    }
    
	public function importar(){
        $this->layout = 'form';
        
        $data = $_FILES; 
        if (!$data) {
        	$errores['CobroTarjeta']['archivoCSV'][]='Debe seleccionar un archivo';
        }
        else{
        	$explode_name = explode('.', $data['CobroTarjetaArchivoCSV']['name']);
            //Se valida así y no con el mime type porque este no funciona para algunos programas
            $pos_ext = count($explode_name) - 1;
            if (!in_array(strtolower($explode_name[$pos_ext]), explode(",","csv,CSV"))) {
            	$errores['CobroTarjeta']['archivoCSV'][]='El archivo a subir debe ser CSV';
            }
        }
        if ($errores) {
        	$this->set('resultado','ERROR');
	        $this->set('mensaje','No se pudo procesar');
	        $this->set('detalle',$errores);
        }
        else{
        	
			//$headerRow = array("Nro. Liquidacion", "Comercio", "Lote","Fecha compra","Fecha pago", "Importe");

        	$fp = fopen ($data['CobroTarjetaArchivoCSV']['tmp_name'],"r");
        	$this->loadModel('CobroTarjetaImportacion');
        	$this->CobroTarjetaImportacion->create();
        	$this->CobroTarjetaImportacion->set('fecha',date('Y-m-d H:i:s'));
        	
            $this->CobroTarjetaImportacion->save();
            $this->loadModel('CobroTarjetaImportacionItem');
			
			while ($data = fgetcsv ($fp, 1000, ";")) {
					//echo 'Liquidacion: '.$data[3].'Comercio: '.$data[9].' - Lote: '.$data[12].' - Fecha: '.$data[14].' - Fecha Pago: '.$data[15].' - Monto: '.$data[17].'<br>';
				
				$condicion=array(array('or' => 
									array(
									array('ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4)),
									array('ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4)),
									array('CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2)),		
									array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17],'ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2)),
	        						array('(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17],'ReservaCobro.fecha = '=>$this->dateFormatSQL($data[14]),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4)),
	        						array('(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17],'CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4))
			    )));
				$result = $this->CobroTarjeta->find('first',array('joins' => array(
			        array(
			            'table' => 'cobro_tarjeta_tipos',
			            'alias' => 'CobroTarjetaTipo',
			            'type' => 'LEFT',
			            'conditions' => array(
			                'CobroTarjetaTipo.id = CobroTarjeta.cobro_tarjeta_tipo_id'
			            )
			        ),
			        array(
			            'table' => 'reserva_cobros',
			            'alias' => 'ReservaCobro',
			            'type' => 'LEFT',
			            'conditions' => array(
			                'ReservaCobro.id = CobroTarjeta.reserva_cobro_id'
			             )
			        ),
			        array(
			            'table' => 'cobro_tarjeta_lotes',
			            'alias' => 'CobroTarjetaLote',
			            'type' => 'LEFT',
			            'conditions' => array(
			                'CobroTarjetaLote.id = CobroTarjeta.cobro_tarjeta_lote_id'
			            )
			        )
			        
			        
			        
			        
			    ),'fields' => array('ReservaCobro.fecha','CobroTarjetaLote.id','CobroTarjetaLote.fecha_cierre','CobroTarjetaLote.fecha_acreditacion','CobroTarjetaTipo.nro_comercio',
    'CobroTarjeta.lote','CobroTarjeta.id','CobroTarjeta.fecha_pago','CobroTarjeta.lote_nuevo','CobroTarjeta.monto_neto','CobroTarjeta.interes','CobroTarjeta.cuotas'),'conditions' => $condicion,'recursive' => -1));
			    
			    /*App::uses('ConnectionManager', 'Model');
	        	$dbo = ConnectionManager::getDatasource('default');
			    $logs = $dbo->getLog();
			    $lastLog = $logs['log'][0];
			    echo $lastLog['query']; */
			    $this->CobroTarjetaImportacionItem->create();
          		$this->CobroTarjetaImportacionItem->set('cobro_tarjeta_importacion_id',$this->CobroTarjetaImportacion->id);
  				$this->CobroTarjetaImportacionItem->set('nro_liquidacion',$data[3]);
  				$this->CobroTarjetaImportacionItem->set('nro_comercio',$data[9]);
  				$this->CobroTarjetaImportacionItem->set('lote',$data[12]);
				$this->CobroTarjetaImportacionItem->set('fecha_compra',$data[14]);
				$this->CobroTarjetaImportacionItem->set('fecha_pago',$data[15]);
				$this->CobroTarjetaImportacionItem->set('importe',$data[17]);
			    if (!$result) {
			    	$condicion=array(array('or' => 
									array(
									array('CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4)),
									array('CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'CobroTarjeta.lote_nuevo LIKE '=>'%'.substr($data[12],-2),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjeta.tarjeta_numero LIKE '=>'%'.substr($data[13],-4),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjeta.tarjeta_numero LIKE '=>'%'.substr($data[13],-4),'CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4)),
									array('CobroTarjeta.tarjeta_numero LIKE '=>'%'.substr($data[13],-4),'CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'CobroTarjeta.tarjeta_numero LIKE '=>'%'.substr($data[13],-4)),		
									array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4),'CobroTarjeta.tarjeta_numero LIKE '=>'%'.substr($data[13],-4),'(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17]),
									array('(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17],'CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'CobroTarjeta.tarjeta_numero LIKE '=>'%'.substr($data[13],-4)),
	        						array('(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17],'CobroTarjeta.cupon LIKE '=>'%'.substr($data[16],-2),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4)),
	        						array('(CobroTarjeta.interes + CobroTarjeta.monto_neto) = '=>$data[17],'CobroTarjeta.tarjeta_numero LIKE '=>'%'.substr($data[13],-4),'CobroTarjetaTipo.nro_comercio LIKE '=>'%'.substr($data[9],-4))
					    )));
						$result1 = $this->CobroTarjeta->find('first',array('joins' => array(
					        array(
					            'table' => 'cobro_tarjeta_tipos',
					            'alias' => 'CobroTarjetaTipo',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'CobroTarjetaTipo.id = CobroTarjeta.cobro_tarjeta_tipo_id'
					            )
					        ),
					        array(
					            'table' => 'reserva_cobros',
					            'alias' => 'ReservaCobro',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'ReservaCobro.id = CobroTarjeta.reserva_cobro_id'
					             )
					        ),
					        array(
					            'table' => 'cobro_tarjeta_lotes',
					            'alias' => 'CobroTarjetaLote',
					            'type' => 'LEFT',
					            'conditions' => array(
					                'CobroTarjetaLote.id = CobroTarjeta.cobro_tarjeta_lote_id'
					            )
					        )
					        
					        
					        
					        
					    ),'fields' => array('ReservaCobro.fecha','CobroTarjetaLote.id','CobroTarjetaLote.fecha_cierre','CobroTarjetaLote.fecha_acreditacion','CobroTarjetaTipo.nro_comercio',
		    'CobroTarjeta.lote','CobroTarjeta.id','CobroTarjeta.fecha_pago','CobroTarjeta.lote_nuevo','CobroTarjeta.monto_neto','CobroTarjeta.interes','CobroTarjeta.cuotas'),'conditions' => $condicion,'recursive' => -1));
					    /*App::uses('ConnectionManager', 'Model');
			        	$dbo = ConnectionManager::getDatasource('default');
					    $logs = $dbo->getLog();
					    $lastLog = $logs['log'][0];
					    echo $lastLog['query'];*/
					    if (!$result1) {    
					    	$this->CobroTarjetaImportacionItem->set('exito',0);
							$this->CobroTarjetaImportacionItem->set('observaciones','No se encontro la transaccion');
					    }
					    else{
					    	
					    	if($result1['CobroTarjetaLote']['fecha_cierre'] != '' and $result1['CobroTarjetaLote']['fecha_acreditacion'] != ''){
					    		$this->CobroTarjetaImportacionItem->set('exito',0);
								$this->CobroTarjetaImportacionItem->set('observaciones','La transaccion ya esta acreditada');
					    	}
					    	elseif($result1['CobroTarjeta']['fecha_pago'] != '' or $result1['CobroTarjeta']['lote'] != ''){
					    		$this->CobroTarjetaImportacionItem->set('exito',0);
								$this->CobroTarjetaImportacionItem->set('observaciones','La transaccion ya tiene cargada una fecha de pago y/o nro de liquidacion');
					    	}
					    	else{
					    		$this->CobroTarjeta->updateAll(
				                    array("CobroTarjeta.lote" => $data[9],"CobroTarjeta.fecha_pago" => "'".$this->dateFormatSQL($data[15])."'"),
				                    array("CobroTarjeta.id" => $result['CobroTarjeta']['id'])
				                );
					    		
					    		$this->CobroTarjetaImportacionItem->set('exito',1);
								$this->CobroTarjetaImportacionItem->set('observaciones','Transaccion actualizada');
					    	}
					    	
					    }
			    }
			    else{
			    	/*echo 'Fecha cierre: '.$result['CobroTarjetaLote']['fecha_cierre'];
			    	echo 'Fecha acreditacion: '.$result['CobroTarjetaLote']['fecha_acreditacion'];
			    	echo 'Fecha pago: '.$result['CobroTarjeta']['fecha_pago'];
			    	echo 'Lote: '.$result['CobroTarjeta']['lote'];
			    	echo 'Id : '.$result['CobroTarjeta']['id'];*/
			    	if($result['CobroTarjetaLote']['fecha_cierre'] != '' and $result['CobroTarjetaLote']['fecha_acreditacion'] != ''){
			    		$this->CobroTarjetaImportacionItem->set('exito',0);
						$this->CobroTarjetaImportacionItem->set('observaciones','La transaccion ya esta acreditada');
			    	}
			    	elseif($result['CobroTarjeta']['fecha_pago'] != '' or $result['CobroTarjeta']['lote'] != ''){
			    		$this->CobroTarjetaImportacionItem->set('exito',0);
						$this->CobroTarjetaImportacionItem->set('observaciones','La transaccion ya tiene cargada una fecha de pago y/o nro de liquidacion');
			    	}
			    	else{
			    		$this->CobroTarjeta->updateAll(
		                    array("CobroTarjeta.lote" => $data[9],"CobroTarjeta.fecha_pago" => "'".$this->dateFormatSQL($data[15])."'"),
		                    array("CobroTarjeta.id" => $result['CobroTarjeta']['id'])
		                );
			    		/*$this->CobroTarjeta->id = $result['CobroTarjeta']['id'];
        				$this->CobroTarjeta->read();
			    		$this->CobroTarjeta->set('lote',$data[9]);
			    		$this->CobroTarjeta->set('fecha_pago',$data[15]);
			    	 	if(!$this->CobroTarjeta->validates()){
				            $errores['CobroTarjeta'] = $this->CobroTarjeta->validationErrors;
				            print_r($errores['CobroTarjeta']);
				            $this->CobroTarjetaImportacionItem->set('exito',0);
							$this->CobroTarjetaImportacionItem->set('observaciones','errrrrrr');
				        }else{
				            $this->CobroTarjeta->save();
				            $this->CobroTarjetaImportacionItem->set('exito',1);
							$this->CobroTarjetaImportacionItem->set('observaciones','Transaccion actualizada');
				        }*/
			    	
			    		
                		/*$this->CobroTarjeta->saveField('lote', $data[9]);
                		$this->CobroTarjeta->saveField('fecha_pago', $data[15]);*/
			    		$this->CobroTarjetaImportacionItem->set('exito',1);
						$this->CobroTarjetaImportacionItem->set('observaciones','Transaccion actualizada');
			    	}
			    	
			    }
			    
				
            	$this->CobroTarjetaImportacionItem->save();
			}
			$this->set('resultado','OK');
	        $this->set('mensaje','Procesado');
	        $this->set('detalle',$this->CobroTarjetaImportacion->id);
        }
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
    }
    
public function exportar($cobro_tarjeta_importacion_id){
    	$this->loadModel('CobroTarjetaImportacion');
    	$CobroTarjetaImportacion = $this->CobroTarjetaImportacion->read(null,$cobro_tarjeta_importacion_id);
    	
    	
    	
    	$this->loadModel('CobroTarjetaImportacionItem');
    	$items = $this->CobroTarjetaImportacionItem->find('all',array('conditions' => array('cobro_tarjeta_importacion_id =' =>$cobro_tarjeta_importacion_id)));
    	$this->autoRender = false;
  		$this->layout = false;
  		
  		$fecha_parts = explode(' ',$CobroTarjetaImportacion['CobroTarjetaImportacion']['fecha']);
		$hora_parts = explode(' ',$fecha_parts[1]);
		$fileName = "Importacion_".$fecha_parts[0].'_'.$hora_parts[0].'_'.$hora_parts[1].'_'.$hora_parts[2].".xls";
		//$fileName = "bookreport_".date("d-m-y:h:s").".csv";
		$headerRow = array("Estado","Motivo", "Nro liquidacion", "Nro Comercio","Lote", "Fecha compra", "Fecha pago","Importe");
		
		$data = array();
		foreach ($items as $item) {
			$estado=($item['CobroTarjetaImportacionItem']['exito'])?'Exito':'Fracaso';
			$data[] = array($estado, $item['CobroTarjetaImportacionItem']['observaciones'],$item['CobroTarjetaImportacionItem']['nro_liquidacion'],$item['CobroTarjetaImportacionItem']['nro_comercio'], $item['CobroTarjetaImportacionItem']['lote'],$item['CobroTarjetaImportacionItem']['fecha_compra'], $item['CobroTarjetaImportacionItem']['fecha_pago'],$item['CobroTarjetaImportacionItem']['importe']);
		}
		           
  		$this->ExportXls->export($fileName, $headerRow, $data);
    }
}
?>
