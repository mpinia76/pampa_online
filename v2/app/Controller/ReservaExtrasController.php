<?php
class ReservaExtrasController extends AppController {
    public $scaffold;
    public function index(){
        $this->set('rows',$this->ReservaExtra->find('all'));
        $this->set('_serialize', array(
            'rows'
        ));
    }
    public function eliminar(){
        $id = $this->request->data['reserva_extra_id'];
        $this->ReservaExtra->id = $id;
        $reserva_extra = $this->ReservaExtra->read();
        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_extra['ReservaExtra']['reserva_id'];
        $reserva = $this->Reserva->read();
        //print_r($reserva);
        $adelantadas = 0;
            $no_adelantadas = 0;
            $pagado = 0;
            $fiscal = 0;
            $descontado = 0;
            if(count($reserva['ReservaCobro'])>0){
                foreach($reserva['ReservaCobro'] as $cobro){
                    if($cobro['tipo'] == 'DESCUENTO'){
                        $descontado += $cobro['monto_neto'];
                    }else{
                        if($cobro['tipo'] == 'TARJETA' or $cobro['tipo'] == 'TRANSFERENCIA'){
                            $fiscal += $cobro['monto_cobrado'];
                        }
                        $pagado += $cobro['monto_neto'];
                    }
                }
            }
            if(count($reserva['ReservaExtra']>0)){
                foreach($reserva['ReservaExtra'] as $extra){
                    if($extra['adelantada'] == 1){
                        $adelantadas = $adelantadas + $extra['cantidad'] * $extra['precio'];
                    }else{
                        $no_adelantadas = $no_adelantadas + $extra['cantidad'] * $extra['precio'];
                    }
                }
            }

            $devoluciones = 0;
            if(count($reserva['ReservaDevolucion']) > 0){
                foreach($reserva['ReservaDevolucion'] as $devolucion){
                    $devoluciones += $devolucion['monto'];
                }
            }

            $facturado = 0;
            if(count($reserva['ReservaFactura']) > 0){
                foreach($reserva['ReservaFactura'] as $factura){
                    $facturado += $factura['monto'];
                }
            }
            $pendiente = round(round($reserva['Reserva']['total'],2) + round($no_adelantadas,2) - round($descontado,2) - round($pagado,2) + round($devoluciones,2),2);
            $pendiente = ($pendiente==-0)?0:$pendiente;
        if ($pendiente > 0) {
	        if($reserva_extra['ReservaExtra']['extra_id']){
	            $this->ReservaExtra->delete($id);
	        }else if($reserva_extra['ReservaExtra']['extra_variable_id']){
	            $this->loadModel('ExtraVariable');
	            $this->ExtraVariable->delete($reserva_extra['ReservaExtra']['extra_variable_id']);
	            $this->ReservaExtra->delete($id);
	        }
	        $this->set('resultado','OK');
	        $this->set('mensaje','Extra eliminada');
	        $this->set('detalle','');
        }    
        else{    
        
	        $this->set('resultado','ERROR');
	        $this->set('mensaje','Extra no eliminada - ');
	        $this->set('detalle','Realice antes la devolucion correspondiente y luego elimine los extras que equivalen al monto devuelto');    
        }
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
       // $this->autoRender = false;
        
    }
    
	public function controlarEliminacion(){
        $id = $this->request->data['reserva_extra_id'];
        $this->ReservaExtra->id = $id;
        $reserva_extra = $this->ReservaExtra->read();
        $this->loadModel('Reserva');
        $this->Reserva->id = $reserva_extra['ReservaExtra']['reserva_id'];
        $reserva = $this->Reserva->read();
        //print_r($reserva);
        $adelantadas = 0;
            $no_adelantadas = 0;
            $pagado = 0;
            $fiscal = 0;
            $descontado = 0;
            if(count($reserva['ReservaCobro'])>0){
                foreach($reserva['ReservaCobro'] as $cobro){
                    if($cobro['tipo'] == 'DESCUENTO'){
                        $descontado += $cobro['monto_neto'];
                    }else{
                        if($cobro['tipo'] == 'TARJETA' or $cobro['tipo'] == 'TRANSFERENCIA'){
                            $fiscal += $cobro['monto_cobrado'];
                        }
                        $pagado += $cobro['monto_neto'];
                    }
                }
            }
            if(count($reserva['ReservaExtra']>0)){
                foreach($reserva['ReservaExtra'] as $extra){
                    if($extra['adelantada'] == 1){
                        $adelantadas = $adelantadas + $extra['cantidad'] * $extra['precio'];
                    }else{
                        $no_adelantadas = $no_adelantadas + $extra['cantidad'] * $extra['precio'];
                    }
                }
            }

            $devoluciones = 0;
            if(count($reserva['ReservaDevolucion']) > 0){
                foreach($reserva['ReservaDevolucion'] as $devolucion){
                    $devoluciones += $devolucion['monto'];
                }
            }

            $facturado = 0;
            if(count($reserva['ReservaFactura']) > 0){
                foreach($reserva['ReservaFactura'] as $factura){
                    $facturado += $factura['monto'];
                }
            }
            $pendiente = round(round($reserva['Reserva']['total'],2) + round($no_adelantadas,2) - round($descontado,2) - round($pagado,2) + round($devoluciones,2),2);
            $pendiente = ($pendiente==-0)?0:$pendiente;
        if ($pendiente > 0) {
	        
	        $this->set('resultado','OK');
	        $this->set('mensaje','Extra eliminada');
	        $this->set('detalle','');
        }    
        else{    
        
	        $this->set('resultado','ERROR');
	        $this->set('mensaje','Extra no eliminada - ');
	        $this->set('detalle','Realice antes la devolucion correspondiente y luego elimine los extras que equivalen al monto devuelto');    
        }
        
        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle' 
        ));
       // $this->autoRender = false;
        
    }
    
    public function getRow(){
        $this->layout = 'ajax';

        if($this->request->data){
            $this->set('cantidad',$this->request->data['cantidad']);
            $extra = $this->ReservaExtra->Extra->findById($this->request->data['extra_id']);
            $this->set('extra',$extra);
            
            //guardo la relacion automaticamente
            $this->ReservaExtra->set(array(
                'reserva_id' => $this->request->data['reserva_id'],
                'extra_id' => $this->request->data['extra_id'],
                'cantidad' => $this->request->data['cantidad'],
                'precio' => $extra['Extra']['tarifa'],
                'adelantada' => 0,
                'agregada' => date('Y-m-d')
            ));
            $this->ReservaExtra->save();
            $this->set('reserva_extra_id',$this->ReservaExtra->id);
        }else{
            $this->set('cantidad',$this->request->query['cantidad']);
            $this->set('extra',$this->ReservaExtra->Extra->findById($this->request->query['extra_id']));
        }
    }
    public function getRowVariable(){
        $this->layout = 'ajax';
        
        if($this->request->data){
            $this->set('precio',$this->request->data['precio']);
            $this->set('detalle',$this->request->data['detalle']);
            
            $this->loadModel('ExtraRubro');
            $this->set('rubro',$this->ExtraRubro->findById($this->request->data['rubro_id']));
            
            $this->loadModel('ExtraVariable');
            $this->ExtraVariable->set(array(
                'extra_rubro_id' => $this->request->data['rubro_id'],
                'detalle' => $this->request->data['detalle']
            ));
            $this->ExtraVariable->save();
            
            //guardo la relacion automaticamente
            $this->ReservaExtra->set(array(
                'reserva_id' => $this->request->data['reserva_id'],
                'extra_variable_id' => $this->ExtraVariable->id,
                'precio' => $this->request->data['precio'],
                'adelantada' => 0,
                'cantidad' => 1,
                'agregada' => date('Y-m-d')
            ));
            $this->ReservaExtra->save();
            $this->set('reserva_extra_id',$this->ReservaExtra->id);
            
        }else{
            $this->set('precio',$this->request->query['precio']);
            $this->set('detalle',$this->request->query['detalle']);
            $this->loadModel('ExtraRubro');
            $this->set('rubro',$this->ExtraRubro->findById($this->request->query['rubro_id']));
        }
    }
}
?>
