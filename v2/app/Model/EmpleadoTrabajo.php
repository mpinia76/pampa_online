<?php
class EmpleadoTrabajo extends AppModel {
    public $useTable = 'empleado_trabajo';
    public $belongsTo = array(
        'Empleado' => array(
            'className'    => 'Empleado',
            'foreignKey'   => 'empleado_id'
        )
    );
}
?>
