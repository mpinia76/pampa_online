<?php
class ExtraRubro extends AppModel {
    public $displayField = 'rubro';
    public $hasMany = array('ExtraSubrubro','Extra');
    public $validate = array(
        'rubro' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe completar con un rubro'
        )
    );
}
?>
