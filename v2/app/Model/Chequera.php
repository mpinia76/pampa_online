<?php
class Chequera extends AppModel {
   	public $belongsTo = array('Cuenta','Usuario');
	public $hasMany = array('ChequeraCheque');
	 public $displayField = 'chequera';
    public $virtualFields = array(
			'chequera' => "CONCAT(Chequera.numero,' (', Chequera.inicio,' - ', Chequera.final,')')"
			);
    public $validate = array(
        'cuenta_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una cuenta'
        ),
        'numero' => array(
            'rule'     => 'numeric',
            
           
            'message' => 'Ingrese solo numeros'
        ),
        'cantidad' => array(
            'rule' => 'control_cantidad',
            'message' => 'Verifique el intervalo'
        ),
        'inicio' => array(
            'no_vacio' => array(
                'required'   => true,
                'rule' => 'numeric',
                'message' => 'Ingrese solo numeros'
             ),
           
             'minLength' => array(
                    'rule' => array('minLength', 8),
                    'message' => 'Ingrese 8 digitos.'
                ),
             'maxLength' => array(
                    'rule' => array('maxLength', 8),
                    'message' => 'Ingrese 8 digitos.'
                
            )
        ),
        'final' => array(
            'no_vacio' => array(
                'required'   => true,
                'rule' => 'numeric',
                'message' => 'Ingrese solo numeros'
             ),
           
             'minLength' => array(
                    'rule' => array('minLength', 8),
                    'message' => 'Ingrese 8 digitos.'
                ),
             'maxLength' => array(
                    'rule' => array('maxLength', 8),
                    'message' => 'Ingrese 8 digitos.'
                
            )
        ),
        'usuario_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un responsable'
        )
       
    );
    
	public function control_cantidad(){
		//echo $this->data['Chequera']['cantidad'] .'!= '.intval($this->data['Chequera']['inicio']).'-'.intval($this->data['Chequera']['final']);
        if($this->data['Chequera']['cantidad'] != intval($this->data['Chequera']['final'])-intval($this->data['Chequera']['inicio'])+1){
            return false;
        }else{
            return true;
        }
    }
	
    
}
?>
