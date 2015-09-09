<?php
class Cliente extends AppModel {
    public $displayField = 'nombre_apellido';
    public $validate = array(
        'nombre_apellido' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre y apellido valido'
        ),
        'dni' => array(
            'rule'     => 'numeric',
            'required' => true,
            'message' => 'Ingrese solo numeros'
        ),
        'telefono' => array(
            'rule' => 'telefono_o_celular',
            'message' => 'Ingrese un telefono o celular valido'
        ),
        'celular' => array(
            'rule' => 'telefono_o_celular',
            'message' => 'Ingrese un telefono o celular valido'
        ),
        'email' => array(
            'rule'     => 'email',
            'required' => true,
            'message' => 'Ingrese un email valido'
        )
    );
    public function telefono_o_celular(){
        if((isset($this->data['Cliente']['telefono']) and $this->data['Cliente']['telefono'] != '') or (isset($this->data['Cliente']['celular']) and $this->data['Cliente']['celular'] != '')) return true;
    }
}
?>
