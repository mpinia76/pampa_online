<?php
class EspacioTrabajo extends AppModel {
    public $useTable = 'espacio_trabajo';
    public $displayField = 'espacio_trabajo';    
    
    public $validate = array(
        'espacio' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un espacio valido'
        )
    );
}
?>
