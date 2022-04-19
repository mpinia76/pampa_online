<?php
class ExtraSubrubro extends AppModel {
    public $belongsTo = 'ExtraRubro';
    public $displayField = 'subrubro';
    public $validate = array(
        'subrubro' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar con un subrubro'
        ),
        'extra_rubro_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un rubro'
        ),
    );
}
?>
