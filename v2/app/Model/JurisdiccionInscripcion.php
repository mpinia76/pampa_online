<?php
class JurisdiccionInscripcion extends AppModel {
	public $useTable = 'jurisdiccion_inscripcion';
    public $displayField = 'nombre';
    public $validate = array(
        'nombre' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        )
    );
}
?>
