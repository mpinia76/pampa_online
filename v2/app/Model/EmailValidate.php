<?php
class EmailValidate extends AppModel {
    
    public $validate = array(
        
        'email' => array(
            'rule'     => 'email',
            //'required' => true,
            'allowEmpty' => true,
            'message' => 'Error en el/los mail/s'
        )
    );
   
}
?>
