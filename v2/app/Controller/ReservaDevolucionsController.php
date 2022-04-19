<?php
session_start();
class ReservaDevolucionsController extends AppController {
    
	public function eliminar(){
		$this->loadModel('Usuario');
		$user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));
       	$tienePermiso=1;	
       	$tienePermisoSincro=1;	
       
	if ($user['Usuario']['admin'] != '1'){
	        $this->loadModel('UsuarioPermiso');
	        $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
	        $tienePermiso=0;
	        $tienePermisoSincro=0;
	    	foreach($permisos as $permiso){
	    			if ($permiso['UsuarioPermiso']['permiso_id']==134){
	    				$tienePermisoSincro=1;
	    			}
	               if (($permiso['UsuarioPermiso']['permiso_id']==101)||($permiso['UsuarioPermiso']['permiso_id']==102)) {
	               		$tienePermiso=1;
	               		//continue;
	               }
	        }
        }
        if ($tienePermiso) {
        	$descubierto=1;
        	$sincronizada=1;
        	/*$this->ReservaDevolucion->id = $this->request->data['id'];
	        $devolucion = $this->ReservaDevolucion->read();*/
	        
	        $devolucion = $this->ReservaDevolucion->find('first',array('joins' => array(
	       
	        array(
	            'table' => 'rel_pago_operacion',
	            'alias' => 'RelPagoOperacion',
	            'type' => 'LEFT',
	            'conditions' => array(
	                'ReservaDevolucion.id = RelPagoOperacion.operacion_id and RelPagoOperacion.operacion_tipo = "reserva_devolucion" '
	            )
	        ),
	        array(
	            'table' => 'reservas',
	            'alias' => 'Reservas',
	            'type' => 'LEFT',
	            'conditions' => array(
	                'Reservas.id = ReservaDevolucion.reserva_id'
	            )
	        ),
	        array(
	            'table' => 'efectivo_consumo',
	            'alias' => 'EfectivoConsumo',
	            'type' => 'LEFT',
	            'conditions' => array(
	                'EfectivoConsumo.id = RelPagoOperacion.forma_pago_id'
	            )
	        )
	        
	    ),'fields'=>array('EfectivoConsumo.caja_id','EfectivoConsumo.monto','ReservaDevolucion.forma_pago','RelPagoOperacion.forma_pago_id'), 'conditions' => array('ReservaDevolucion.id =' => $this->request->data['id']), 'recursive' => -1));
	        
	      /*App::uses('ConnectionManager', 'Model');
        	$dbo = ConnectionManager::getDatasource('default');
		    $logs = $dbo->getLog();
		    
	        print_r($logs);*/
	    	//print_r($devolucion);
	        switch($devolucion['ReservaDevolucion']['forma_pago']){
	        	case 'EFECTIVO':
	               //print_r($devolucion);
	          		$this->loadModel('CajaSincronizada');
	        		$fechaSincronizada = $this->CajaSincronizada->find('first',array('fields'=>'MAX(CajaSincronizada.fecha) as fecha','conditions' => array('CajaSincronizada.caja_id =' => $devolucion['EfectivoConsumo']['caja_id'])));
	        		
	        		 $this->loadModel('CajaMovimiento');
	        		$movimientos = $this->CajaMovimiento->find('all',array('conditions' => array('registro_id' => $devolucion['RelOperacionPago']['forma_pago_id'])));
	        		 
	        		foreach ($movimientos as $movimiento) {
	        			//print_r($movimiento);
	        			
		        		if ($fechaSincronizada[0]['fecha']>=$movimiento['CajaMovimiento']['fecha']) {
		        			if (!$tienePermisoSincro) {
		        				$sincronizada=0;
		        			}
		        			
		        		}
	        		}
	        		
	          		
	                $this->loadModel('Caja');
	                $caja=$this->Caja->findById($devolucion['EfectivoConsumo']['caja_id']);
	                 
	               
	                
	                if (!$caja['Caja']['descubierto']) {
	                	$monto = $this->CajaMovimiento->find('first',array('fields'=>'SUM(CajaMovimiento.monto) as total','conditions' => array('CajaMovimiento.caja_id =' => $devolucion['EfectivoConsumo']['caja_id'])));
	                	
	                	$montoTotal =$monto[0]['total']-$devolucion['EfectivoConsumo']['monto'];
						//echo $montoTotal;
	                	if ($montoTotal<0) {
	                		$descubierto=0;
	                	}
	                }
	                if($descubierto&&$sincronizada){
	          
	                	$this->CajaMovimiento->deleteAll(array('registro_id' => $devolucion['RelPagoOperacion']['forma_pago_id']), false);
	                	/*App::uses('ConnectionManager', 'Model');
			        	$dbo = ConnectionManager::getDatasource('default');
					    $logs = $dbo->getLog();
					    
				        print_r($logs);*/
	                }
	           		
					
	                break;
	           /* case 'CHEQUE':
	                //print_r($cobro['CobroCheque']);       	
	               	$this->loadModel('CuentaMovimiento');
	               	$this->CuentaMovimiento->deleteAll(array('origen' => 'reservacheque_'.$cobro['CobroCheque']['id']), false);
	           		//$this->CuentaMovimiento->deleteAll(array('cuenta_id' => $cobro['CobroCheque']['cuenta_id'],'origen' => 'reservacheque_'.$cobro['CobroCheque']['id']), false);
	               					
	                break;
	           
	            case 'TRANSFERENCIA':
	            	//print_r($cobro['CobroTransferencia']);
	               $this->loadModel('CuentaMovimiento');
	           		$this->CuentaMovimiento->deleteAll(array('cuenta_id' => $cobro['CobroTransferencia']['cuenta_id'],'origen' => 'reservatransferencia_'.$cobro['CobroTransferencia']['id']), false);
	                break;*/
	        }
        	//
        	if (!$descubierto) {
	        	$this->set('resultado','ERROR');
		        $this->set('mensaje','Devolucion no eliminada, ');
		        $this->set('detalle','genera saldo negativo en caja no autorizada');
	        }
	        elseif(!$sincronizada) {
	        	$this->set('resultado','ERROR');
		        $this->set('mensaje','Devolucion no eliminada, ');
		        $this->set('detalle','la caja se encuentra conciliada y sincronizada para la fecha del movimiento que intenta realizar. Contactar administrador');
	        }
        	else{
        		$this->ReservaDevolucion->delete($this->request->data['id'],true);
        
		        $this->set('resultado','OK');
		        $this->set('mensaje','Devolucion eliminada');
		        $this->set('detalle','');
        	}
	        
        }
		else{
        	$this->set('resultado','ERROR');
	        $this->set('mensaje','Devolucion no eliminada');
	        $this->set('detalle','No tiene permiso');
        }    
	        $this->set('_serialize', array(
	            'resultado',
	            'mensaje' ,
	            'detalle' 
	        ));
        
    }
}
?>