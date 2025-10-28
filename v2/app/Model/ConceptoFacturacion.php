<?php
class ConceptoFacturacion extends AppModel {

    // Relación: cada ConceptoFacturacion pertenece a un PuntoVenta
    public $belongsTo = array(
        'PuntoVenta' => array(
            'className' => 'PuntoVenta', // Modelo relacionado
            'foreignKey' => 'punto_venta_id', // Campo en ConceptoFacturacion que guarda la FK
        )
    );

    // Si quieres, puedes agregar validaciones aquí
    public $validate = array(
        'punto_venta_id' => array(
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un Punto de Venta'
        )
    );
}
?>