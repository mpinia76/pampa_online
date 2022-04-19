<?php
class Subcanal extends AppModel {
	public $belongsTo = array('Canal');
    public $displayField = 'subcanal';
    
    public $validate = array(
    	'canal_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un canal'
        ),
        'subcanal' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un subcanal'
        )
    );
    
    
	
    
	
}
?>
