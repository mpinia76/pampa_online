<?php
class PuntoVenta extends AppModel {
    public $displayField = 'puntoVenta';
    public $virtualFields = array(
			'puntoVenta' => "CONCAT(PuntoVenta.numero,' ', PuntoVenta.cuit,' ', PuntoVenta.descripcion,' ', PuntoVenta.direccion)"
			);
    
    public $validate = array(
        'cuit' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un cuit valido'
        ),
        'numero' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        ),
        'alicuota' => array(
            'rule'    => array('range', -0.99,0.31),
            
            'message' => 'Ingrese un numero entre 0 y 0.3'
        )
    );
}
?>
