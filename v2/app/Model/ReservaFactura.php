<?php
class ReservaFactura extends AppModel {
    public $belongsTo = array('Reserva','PuntoVenta');
    public $validate = array(
    	'tipoDoc' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un tipo'
        ),
        'fecha_emision' => array(
            'rule'     => array('date','dmy'),
            'required' => true,
            'message' => 'Ingrese una fecha valida'
        ),
        'tipo' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una forma de cobro'
        ),
        'titular' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completer el titular'
        ),
        'numero' => array(
            'no_vacio' => array(
                'required'   => true,
                'rule' => 'numeric',
                'message' => 'Ingrese solo numeros'
             ),
            /*'longitud' => array(
                'rule'    => array('minLength', 4),
                'message' => 'Ingrese como minimo 4 digitos.'
            ),*/
             'minLength' => array(
                    'rule' => array('minLength', 8),
                    'message' => 'Ingrese 8 digitos.'
                ),
                'maxLength' => array(
                    'rule' => array('maxLength', 8),
                    'message' => 'Ingrese 8 digitos.'
                
            ),
            
            "unique"=>array( 
                                "rule"=>array("checkUnique", array("punto_venta_id", "tipoDoc", "tipo", "numero")), 
                                "message"=>"El numero de factura ya existe" 
                        )/*,
            'unico' => array(
                'rule' => 'isUnique',
                'message' => 'Este numero de factura ya existe'
            )*/
        ),
        'monto' => array(
            'basico' => array(
                'rule'    => array('range', 0,999999),
                'required'   => true,
                'message' => 'Ingrese un numero mayor a 0'
            ),
            'max' => array(
                'rule' => 'monto_max',
                'message' => 'El monto de la factura, no puede ser mayor a la tarifa Neta, menos el monto total facturado a la fecha'
            )
        )
    );
    
public function monto_max(){
    	//print_r($this->data);
    	if (strpos($this->data['ReservaFactura']['titular'], '||') !== false) {
    		return true;
    	}
    	else{
	        /*$reserva_cobro = ClassRegistry::init('ReservaCobro');
	        $cobrado = 0;
	        $cobros = $reserva_cobro->find('all',array('conditions'=>array('reserva_id' => $this->data['ReservaFactura']['reserva_id'])));
	        foreach($cobros as $cobro){
	            if($cobro['ReservaCobro']['tipo'] != 'DESCUENTO'){
	                $cobrado += $cobro['ReservaCobro']['monto_cobrado'];
	            }
	        }*/
    	$reservaClase = ClassRegistry::init('Reserva');
    	$reservaClase->id = $this->data['ReservaFactura']['reserva_id'];
    	$reserva = $reservaClase->read();
        
        
       
        
        
        $pagado = 0;
        $descontado = 0;
        $adelantadas = 0;
        $no_adelantadas = 0;
        if(count($reserva['ReservaCobro'])>0){
            foreach($reserva['ReservaCobro'] as $cobro){
                if($cobro['finalizado'] == 0){
                    if($cobro['tipo'] == 'DESCUENTO'){
                        $descontado = $descontado + $cobro['monto_neto'];
                    }else{
                        $pagado = $pagado + $cobro['monto_neto'];
                    }
                }
            }
        }
        //if(count($reserva['ReservaExtra']>0)){
            foreach($reserva['ReservaExtra'] as $extra){
                if($extra['adelantada'] == 1){
                    $adelantadas = $adelantadas + $extra['cantidad'] * $extra['precio'];
                }else{
                    $no_adelantadas = $no_adelantadas + $extra['cantidad'] * $extra['precio'];
                }
            }
        //}
        
        $devoluciones = 0;
        if(count($reserva['ReservaDevolucion']) > 0){
            foreach($reserva['ReservaDevolucion'] as $devolucion){
                $devoluciones += $devolucion['monto'];
            }
        }
        $total = $reserva['Reserva']['total'] + $no_adelantadas - $descontado;
	    	$facturado = 0;
	    	$reserva_factura = ClassRegistry::init('ReservaFactura');
	        $facturas = $reserva_factura->find('all',array('conditions'=>array('reserva_id' => $this->data['ReservaFactura']['reserva_id'], 'ReservaFactura.id != '=>$this->data['ReservaFactura']['id'])));
	        foreach($facturas as $factura){
	            
	                $facturado += $factura['ReservaFactura']['monto'];
	            
	        }
    	 $monto = ($this->data['ReservaFactura']['tipoDoc']==2)?$this->data['ReservaFactura']['monto']*(-1):$this->data['ReservaFactura']['monto'];
		   
		   //echo floatval($monto) ."> (".floatval($total+0.5)."-".floatval($facturado).")";

	        if(floatval($monto) > (floatval($total+0.5)-floatval($facturado))){
	            return false;
	        }else{
	            return true;
	        }
    	}
    }
    
    public function beforeSave($options = Array()) {
        if (!empty($this->data['ReservaFactura']['fecha_emision'])) {
            $this->data['ReservaFactura']['fecha_emision'] = $this->dateFormatBeforeSave($this->data['ReservaFactura']['fecha_emision']);
        }
        if ($this->data['ReservaFactura']['tipoDoc']==2) {
        	$this->data['ReservaFactura']['monto']=$this->data['ReservaFactura']['monto']*(-1);
        }
        return true;
    }
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['ReservaFactura']['fecha_emision'])) {
                $results[$key]['ReservaFactura']['fecha_emision']= $this->dateFormatAfterFind($val['ReservaFactura']['fecha_emision']);
            }
        }
        return $results;
    }

}
?>