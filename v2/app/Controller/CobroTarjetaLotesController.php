<?php 

ini_set('memory_limit', '-1');
class CobroTarjetaLotesController extends AppController {
    
	public function dateFormatSQL($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }
	
    public function index(){
        $this->layout = 'index';
        
         $this->setLogUsuario('Liquidaciones y lotes');
    	
        
        $this->loadModel('Usuario');
        $this->Usuario->id = $_COOKIE['userid'];
        $usuario = $this->Usuario->read();
    }
    


     public function get_lotes_cobro_tarjetas($offset, $limit, $orderField, $orderType, $search, $search_2, $search_3, $search_4, $search_5, $search_6, $search_7, $search_8, $search_9, $search_10) {
    	$estado = explode('_', $search);
		
    	$search = (($estado[1])&&($estado[0]=='EST'))?'':$search;
		
     	if ($search!='') {
			if ($search==':-:-@') {
				$search='';
			}
			elseif ($search==':-:-@@@') {
				$search='';
				$_SESSION["cobroTarjetaLoteSearchEstado"]='EST_3';
			}
			//$_SESSION["cobroTarjetaLoteSearch"]=$search;
		}
		
     	if ($search_2!='') {
			if ($search_2==':-:-@') {
				$search_2='';
			}
			elseif ($search_2==':-:-@@@') {
				$search_2='';
				
			}
			$_SESSION["cobroTarjetaLoteLocacionSearch"]=$search_2;
		}
     	if ($search_3!='') {
			if ($search_3==':-:-@') {
				$search_3='';
			}
			elseif ($search_3==':-:-@@@') {
				$search_3='';
				
			}
			$_SESSION["cobroTarjetaLoteMarcaSearch"]=$search_3;
		}
     	if ($search_4!='') {
			if ($search_4==':-:-@') {
				$search_4='';
			}
			elseif ($search_4==':-:-@@@') {
				$search_4='';
				
			}
			$_SESSION["cobroTarjetaLoteComercioSearch"]=$search_4;
		}
     	if ($search_5!='') {
			if ($search_5==':-:-@') {
				$search_5='';
			}
			elseif ($search_5==':-:-@@@') {
				$search_5='';
				
			}
			$_SESSION["cobroTarjetaLoteCuentaSearch"]=$search_5;
		}
     	if ($search_6!='') {
			if ($search_6==':-:-@') {
				$search_6='';
			}
			elseif ($search_6==':-:-@@@') {
				$search_6='';
				
			}
			$_SESSION["cobroTarjetaLoteLiquidacionSearch"]=$search_6;
		}
     	if ($search_7!='') {
			if ($search_7==':-:-@') {
				$search_7='';
			}
			elseif ($search_7==':-:-@@@') {
				$search_7='';
				
			}
			$_SESSION["cobroTarjetaLoteMontoSearch"]=$search_7;
		}
     	if ($search_8!='') {
			if ($search_8==':-:-@') {
				$search_8='';
			}
			elseif ($search_8==':-:-@@@') {
				$search_8='';
				
			}
			$_SESSION["cobroTarjetaLoteOperacionesSearch"]=$search_8;
		}
     	if ($search_9!='') {
			if ($search_9==':-:-@') {
				$search_9='';
			}
			elseif ($search_9==':-:-@@@') {
				$search_9='';
				
			}
			$_SESSION["cobroTarjetaLoteFechaPagoSearch"]=$search_9;
		}
     	if ($search_10!='') {
			if ($search_10==':-:-@') {
				$search_10='';
			}
			elseif ($search_10==':-:-@@@') {
				$search_10='';
				
			}
			$_SESSION["cobroTarjetaLoteFechaAcreditacionSearch"]=$search_10;
		}
		if(($estado[1])&&($estado[0]=='EST')){
			
			switch ($estado[1]) {
				case 1:
				 $filtroEstado='1=1';
				 $_SESSION["cobroTarjetaLoteSearchEstado"]='EST_1';
				break;
				case 2:
				 $filtroEstado=array('CobroTarjetaLote.id' => null);
				 $_SESSION["cobroTarjetaLoteSearchEstado"]='EST_2';
				break;
				case 3:
				 $filtroEstado=array('OR' => array(array('CobroTarjetaLote.id' => null),array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion' => '')));
				 $_SESSION["cobroTarjetaLoteSearchEstado"]='EST_3';
				break;
				case 4:
				 $filtroEstado=array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion <>' => '');
				 $_SESSION["cobroTarjetaLoteSearchEstado"]='EST_4';
				break;
				
				
			}
		}
		elseif(!$estado[1]&&!$_SESSION["cobroTarjetaLoteSearchEstado"]){
			$filtroEstado=array('OR' => array(array('CobroTarjetaLote.id' => null),array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion' => '')));
			$_SESSION["cobroTarjetaLoteSearchEstado"]='EST_3';
			
		}
    	elseif(!$_SESSION["cobroTarjetaLoteSearchEstado"]){
			$filtroEstado=array('OR' => array(array('CobroTarjetaLote.id' => null),array('CobroTarjetaLote.fecha_cierre <>' => '', 'CobroTarjetaLote.fecha_acreditacion' => '')));
			$_SESSION["cobroTarjetaLoteSearchEstado"]='EST_3';
		}
    	else{
    		switch ($_SESSION["cobroTarjetaLoteSearchEstado"]) {
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
		$filtroLocacion=($_SESSION["cobroTarjetaLoteLocacionSearch"])?array('CobroTarjetaPosnet.posnet LIKE '=>'%'.$_SESSION["cobroTarjetaLoteLocacionSearch"].'%'):array(1=>1);
		$filtroMarca=($_SESSION["cobroTarjetaLoteMarcaSearch"])?array('CobroTarjetaTipo.marca LIKE '=>'%'.$_SESSION["cobroTarjetaLoteMarcaSearch"].'%'):array(1=>1);
		$filtroComercio=($_SESSION["cobroTarjetaLoteComercioSearch"])?array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.$_SESSION["cobroTarjetaLoteComercioSearch"].'%'):array(1=>1);
		$filtroCuenta=($_SESSION["cobroTarjetaLoteCuentaSearch"])?array('Cuenta.nombre LIKE '=>'%'.$_SESSION["cobroTarjetaLoteCuentaSearch"].'%'):array(1=>1);
		
		$filtroLiquidacion=($_SESSION["cobroTarjetaLoteLiquidacionSearch"])?array('CobroTarjeta.lote LIKE '=>'%'.$_SESSION["cobroTarjetaLoteLiquidacionSearch"].'%'):array(1=>1);
		$filtroMonto=($_SESSION["cobroTarjetaLoteMontoSearch"])?'sum(CobroTarjeta.interes + CobroTarjeta.monto_neto) LIKE \'%'.$_SESSION["cobroTarjetaLoteMontoSearch"].'%\'':'1=1';
		$filtroOperaciones=($_SESSION["cobroTarjetaLoteOperacionesSearch"])?'count(CobroTarjeta.id) LIKE \'%'.$_SESSION["cobroTarjetaLoteOperacionesSearch"].'%\'':'1=1';
		$date_parts = explode("/",$_SESSION["cobroTarjetaLoteFechaPagoSearch"]);
		
		switch (count($date_parts)) {
			case 1:
			$fechaPago = $date_parts[0];
			break;
			
			case 2:
			$fechaPago = $date_parts[1].'-'.$date_parts[0];
			break;
			case 3:
			$fechaPago = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
			break;
		}
		
		
		$filtroFechaPago=($_SESSION["cobroTarjetaLoteFechaPagoSearch"])?array('CobroTarjeta.fecha_pago LIKE '=>'%'.$fechaPago.'%'):array(1=>1);
		
		
		$filtroFechaAcreditacion=($_SESSION["cobroTarjetaLoteFechaAcreditacionSearch"])?array('CobroTarjetaLote.fecha_acreditacion LIKE '=>'%'.$this->dateFormatSQL($_SESSION["cobroTarjetaLoteFechaAcreditacionSearch"]).'%'):array(1=>1);
		
		$condicion=array($filtroEstado,'CobroTarjeta.lote !=' => '',$filtroLocacion,$filtroMarca, $filtroComercio,$filtroCuenta,$filtroLiquidacion,$filtroFechaPago,$filtroFechaAcreditacion	  	  
			    );
        $result = Cache::read('get_lotes_cobro_tarjetas', 'long');
            if (!$result) {
        		$order = $orderField.' '.$orderType;
                $lotes = $this->CobroTarjeta->find('all',array('joins' => array(
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
        ),
        array(
            'table' => 'cuenta',
            'alias' => 'Cuenta',
            'type' => 'LEFT',
            'conditions' => array(
                'Cuenta.id = CobroTarjetaTipo.cuenta_id'
            )
        )
        
        
    ),'fields'=>array('CobroTarjetaPosnet.*','Cuenta.*','CobroTarjeta.*', 'CobroTarjetaTipo.*','CobroTarjetaLote.*', 'sum(CobroTarjeta.interes + CobroTarjeta.monto_neto) as monto_total, 
    count(CobroTarjeta.id) as operaciones'), 'group' => array('CobroTarjeta.cobro_tarjeta_tipo_id', 'CobroTarjeta.lote HAVING '.$filtroMonto.' AND '.$filtroOperaciones ),'order' => $order, 'conditions' => $condicion, 'limit'=>$limit, 'recursive' => -1));
            /*App::uses('ConnectionManager', 'Model');
        	$dbo = ConnectionManager::getDatasource('default');
		    $logs = $dbo->getLog();
		    $lastLog = $logs['log'][0];
		    echo $lastLog['query'];*/    
    		Cache::write('get_lotes_cobro_tarjetas', $lotes, 'long');
            }
            
            return $lotes;
    }


	public function get_lotes_cobro_tarjetascount($search, $search_2, $search_3, $search_4) {
		
		switch ($_SESSION["cobroTarjetaLoteSearchEstado"]) {
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
		
		$filtroLocacion=($_SESSION["cobroTarjetaLoteLocacionSearch"])?array('CobroTarjetaPosnet.posnet LIKE '=>'%'.$_SESSION["cobroTarjetaLoteLocacionSearch"].'%'):array(1=>1);
		$filtroMarca=($_SESSION["cobroTarjetaLoteMarcaSearch"])?array('CobroTarjetaTipo.marca LIKE '=>'%'.$_SESSION["cobroTarjetaLoteMarcaSearch"].'%'):array(1=>1);
		$filtroComercio=($_SESSION["cobroTarjetaLoteComercioSearch"])?array('CobroTarjetaTipo.nro_comercio LIKE '=>'%'.$_SESSION["cobroTarjetaLoteComercioSearch"].'%'):array(1=>1);
		$filtroCuenta=($_SESSION["cobroTarjetaLoteCuentaSearch"])?array('Cuenta.nombre LIKE '=>'%'.$_SESSION["cobroTarjetaLoteCuentaSearch"].'%'):array(1=>1);
		
		$filtroLiquidacion=($_SESSION["cobroTarjetaLoteLiquidacionSearch"])?array('CobroTarjeta.lote LIKE '=>'%'.$_SESSION["cobroTarjetaLoteLiquidacionSearch"].'%'):array(1=>1);
		$filtroMonto=($_SESSION["cobroTarjetaLoteMontoSearch"])?'sum(CobroTarjeta.interes + CobroTarjeta.monto_neto) LIKE \'%'.$_SESSION["cobroTarjetaLoteMontoSearch"].'%\'':'1=1';
		$filtroOperaciones=($_SESSION["cobroTarjetaLoteOperacionesSearch"])?'count(CobroTarjeta.id) LIKE \'%'.$_SESSION["cobroTarjetaLoteOperacionesSearch"].'%\'':'1=1';
		$date_parts = explode("/",$_SESSION["cobroTarjetaLoteFechaPagoSearch"]);
		
		switch (count($date_parts)) {
			case 1:
			$fechaPago = $date_parts[0];
			break;
			
			case 2:
			$fechaPago = $date_parts[1].'-'.$date_parts[0];
			break;
			case 3:
			$fechaPago = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
			break;
		}
		
		
		$filtroFechaPago=($_SESSION["cobroTarjetaLoteFechaPagoSearch"])?array('CobroTarjeta.fecha_pago LIKE '=>'%'.$fechaPago.'%'):array(1=>1);
		
		
		$filtroFechaAcreditacion=($_SESSION["cobroTarjetaLoteFechaAcreditacionSearch"])?array('CobroTarjetaLote.fecha_acreditacion LIKE '=>'%'.$this->dateFormatSQL($_SESSION["cobroTarjetaLoteFechaAcreditacionSearch"]).'%'):array(1=>1);
		
		$condicion=array($filtroEstado,'CobroTarjeta.lote !=' => '',$filtroLocacion,$filtroMarca, $filtroComercio,$filtroCuenta,$filtroLiquidacion,$filtroFechaPago,$filtroFechaAcreditacion	  	  
		 	  	  
			    );
        
    	$result = Cache::read('get_lotes_cobro_tarjetascount', 'long');
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
        ),
        array(
            'table' => 'cuenta',
            'alias' => 'Cuenta',
            'type' => 'LEFT',
            'conditions' => array(
                'Cuenta.id = CobroTarjetaTipo.cuenta_id'
            )
        )
        
        
    ),'conditions' => $condicion, 'group' => array('CobroTarjeta.cobro_tarjeta_tipo_id', 'CobroTarjeta.lote HAVING '.$filtroMonto.' AND '.$filtroOperaciones),'recursive' => -1));
	       Cache::write('get_transaccionescount', $result, 'long');
	       }
          return $result;
    }

    public function dataTable(){
        //$this->layout = 'ajax';
        //print_r($_GET);
    	switch ($_GET['iSortCol_0']) {
			
			case 2:
			$orderField='CobroTarjetaPosnet.posnet';
			break;
			case 3:
			$orderField='CobroTarjetaTipo.marca';
			break;
			case 4:
			$orderField='CobroTarjetaTipo.nro_comercio';
			break;
			case 5:
			$orderField='Cuenta.nombre';
			break;
			case 6:
			$orderField='CobroTarjeta.lote';
			break;
			
			case 9:
			$orderField='CobroTarjeta.fecha_pago';
			break;
			case 10:
			$orderField='CobroTarjetaLotes.fecha_acreditacion';
			break;
			
			default:
			$orderField='CobroTarjeta.id';
			break;
		}
		
		$orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'desc';
        
        $rows = array();
        $this->loadModel('CobroTarjeta');

        //Se agrega cache para query
        $lotes =  $this->get_lotes_cobro_tarjetas($_GET['iDisplayStart'], $_GET['iDisplayLength'], $orderField, $orderType, $_GET['sSearch'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $_GET['sSearch_8'], $_GET['sSearch_9'], $_GET['sSearch_10']);
		$iTotal = $this->get_lotes_cobro_tarjetascount( $_GET['sSearch'], $_GET['sSearch_2'], $_GET['sSearch_3'], $_GET['sSearch_4']);
        
        foreach($lotes as $lote){
            if($lote['CobroTarjeta']['cobro_tarjeta_lote_id'] == 0){
                //$estado = 'Pendiente de cierre';
                $estado = 'Pendiente de acreditacion';
            }elseif($lote['CobroTarjetaLote']['fecha_cierre'] != '' and $lote['CobroTarjetaLote']['fecha_acreditacion'] == ''){
                $estado = 'Pendiente de acreditacion';
            }elseif($lote['CobroTarjetaLote']['fecha_cierre'] != '' and $lote['CobroTarjetaLote']['fecha_acreditacion'] != ''){
                $estado = 'Acreditado';
            }else{
                $estado = 'Revisar';
            }
            
            $rows[] = array(
            	$lote['CobroTarjeta']['cobro_tarjeta_lote_id'],
                $lote['CobroTarjetaTipo']['id'],
                $lote['CobroTarjetaPosnet']['posnet'],
                $lote['CobroTarjetaTipo']['marca'],
                $lote['CobroTarjetaTipo']['nro_comercio'],
                $lote['Cuenta']['nombre'],
                $lote['CobroTarjeta']['lote'],
                '$'.$lote['0']['monto_total'],
                $lote['0']['operaciones'],
                $lote['CobroTarjeta']['fecha_pago'],
                $lote['CobroTarjetaLote']['fecha_acreditacion'],
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
    
    public function cerrar(){
        $this->CobroTarjetaLote->set(array(
            'cobro_tarjeta_tipo_id' => $this->request->data['cobro_tarjeta_tipo_id'],
            'numero' => $this->request->data['numero'],
            'fecha_cierre' => $this->request->data['fecha_cierre'],
            'monto_total' => $this->request->data['monto_total'],
            'cerrado_por' => $this->request->data['cerrado_por']
        ));
        if(!$this->CobroTarjetaLote->validates()){
            $this->set('resultado','ERROR');
            $this->set('detalle',$this->CobroTarjetaLote->validationErrors);
        }else{
            $this->CobroTarjetaLote->save();
            
            $this->loadModel('CobroTarjeta');
            $this->CobroTarjeta->updateAll(
                array('CobroTarjeta.cobro_tarjeta_lote_id' => $this->CobroTarjetaLote->id),
                array('CobroTarjeta.cobro_tarjeta_tipo_id' => $this->request->data['cobro_tarjeta_tipo_id'],
                         'CobroTarjeta.lote' => $this->request->data['numero'])
            );
            $this->set('resultado','OK');
            $this->set('detalle','');
        }
        $this->set('_serialize', array(
            'resultado',
            'detalle' 
        ));
    }
    
    public function acreditar(){
    	$fecha_pago = $this->request->data['fecha_pago'];
    	
	    $date_parts = explode("/",$fecha_pago);
	    $fecha_pago =  $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
	    
	    $fecha_acreditacion = $this->request->data['fecha_acreditacion'];
    	
	    $date_parts = explode("/",$fecha_acreditacion);
	    $fecha_acreditacion =  $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
	   
    	if ($fecha_acreditacion<$fecha_pago) {
        	$errores['fecha_acreditacion'][]='La fecha de acreditacion debe ser igual o posterior a la fecha de pago';
        	$this->set('resultado','ERROR');
            $this->set('detalle',$errores);	 
        }
    	elseif (($this->request->data['descuentos']<0)&&(!$this->request->data['diferencia'])) {
        	$errores['descuentos'][]='Numero mayor a 0';
        	$this->set('resultado','ERROR');
            $this->set('detalle',$errores);	 
        }
	    else{
	   	 	$this->CobroTarjetaLote->set(array(
	            'cobro_tarjeta_tipo_id' => $this->request->data['cobro_tarjeta_tipo_id'],
	            'numero' => $this->request->data['numero'],
	            'fecha_cierre' => $this->request->data['fecha_acreditacion'],
	            'monto_total' => $this->request->data['monto_total'],
	            'cerrado_por' => $this->request->data['acreditado_por']
	        ));
	        if(!$this->CobroTarjetaLote->validates()){
	            $this->set('resultado','ERROR');
	            $this->set('detalle',$this->CobroTarjetaLote->validationErrors);
	        }else{
	            $this->CobroTarjetaLote->save();
	            
	            $this->loadModel('CobroTarjeta');
	            $this->CobroTarjeta->updateAll(
	                array('CobroTarjeta.cobro_tarjeta_lote_id' => $this->CobroTarjetaLote->id),
	                array('CobroTarjeta.cobro_tarjeta_tipo_id' => $this->request->data['cobro_tarjeta_tipo_id'],
	                         'CobroTarjeta.lote' => $this->request->data['numero'])
	            );
	            
	        }
	    	
	    	
	        $cobro_tarjeta_lote = $this->CobroTarjetaLote->read(null,$this->CobroTarjetaLote->id);
	        $this->CobroTarjetaLote->set(array(
	            'fecha_acreditacion' => $this->request->data['fecha_acreditacion'],
	            'descuentos' => $this->request->data['descuentos'],
	            'acreditado_por' => $this->request->data['acreditado_por']
	        ));
	        if(!$this->CobroTarjetaLote->validates()){
	            $this->set('resultado','ERROR');
	            $this->set('detalle',$this->CobroTarjetaLote->validationErrors);
	        }else{
	        	$diferencia = $this->request->data['diferencia'];
	        	$descuento = $this->request->data['descuentos'];
	        	if ($diferencia) {
	        		$descuento = $descuento*(-1);
	        		$this->CobroTarjetaLote->set(array(
			            'descuentos' => $descuento
			        ));
	        	}
	        	
	            $this->CobroTarjetaLote->save();
	            
	            //aplico un proporcional del descuento del lote a cada transaccion para el informe economico
	            $this->loadModel('CobroTarjeta');
	            $cobros_tarjeta = $this->CobroTarjeta->find('all',array('conditions' => array('cobro_tarjeta_lote_id' => $this->CobroTarjetaLote->id)));
	            foreach($cobros_tarjeta as $cobro){
	                $this->CobroTarjeta->read(null,$cobro['CobroTarjeta']['id']);
	                $lote_descuento = round((($cobro['CobroTarjeta']['monto_neto'] + $cobro['CobroTarjeta']['interes']) / $cobro_tarjeta_lote['CobroTarjetaLote']['monto_total']) * $descuento,2);
	                
	                $this->CobroTarjeta->updateAll(
	                    array('CobroTarjeta.descuento_lote' => $lote_descuento),
	                    array('CobroTarjeta.id' => $cobro['CobroTarjeta']['id'])
	                );
	            }
	            
	            //impacto la acreditacion en la cuenta
	            $this->loadModel('CuentaMovimiento');
	            $this->CuentaMovimiento->set(array(
	                'cuenta_id' => $cobro_tarjeta_lote['CobroTarjetaTipo']['cuenta_id'],
	                'origen' => 'acreditacionlote_'.$this->CobroTarjetaLote->id,
	                'monto' => $cobro_tarjeta_lote['CobroTarjetaLote']['monto_total'] - $descuento,
	                'fecha' => $this->request->data['fecha_acreditacion']
	            ));
	            $this->CuentaMovimiento->save();
	            
	            $this->set('resultado','OK');
	            $this->set('detalle','');
	        }
	    }
        $this->set('_serialize', array(
            'resultado',
            'detalle' 
        ));
    }
    
	public function anular(){
        $cobro_tarjeta_lote = $this->CobroTarjetaLote->read(null,$this->request->data['id']);
        
		$this->loadModel('CobroTarjeta');
            $cobros_tarjeta = $this->CobroTarjeta->find('all',array('conditions' => array('cobro_tarjeta_lote_id' => $this->request->data['id'])));
            
            foreach($cobros_tarjeta as $cobro){
            	
                $this->CobroTarjeta->read(null,$cobro['CobroTarjeta']['id']);
              	
                $this->CobroTarjeta->updateAll(
                    array('CobroTarjeta.descuento_lote' => 0),
                    
                    array('CobroTarjeta.id' => $cobro['CobroTarjeta']['id'])
                );
                
                $this->CobroTarjeta->saveField('cobro_tarjeta_lote_id', NULL);
            }
        
        
        
        
	   
		
	    $this->loadModel('CuentaMovimiento');
	           
		$this->CuentaMovimiento->deleteAll(array('cuenta_id' => $cobro_tarjeta_lote['CobroTarjetaTipo']['cuenta_id'], 'origen' => 'acreditacionlote_'.$this->request->data['id']), false);

		$this->CobroTarjetaLote->delete($this->request->data['id'],true);    
	            
	    $this->set('resultado','OK');
	    $this->set('detalle','');
      
       
        $this->set('_serialize', array(
            'resultado',
            'detalle' 
        ));
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
