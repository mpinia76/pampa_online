<?php
class Proveedor extends AppModel {
    public $useTable = 'proveedor';
    public $belongsTo = array('Rubro','CondicionImpositiva','JurisdiccionInscripcion');
    public $validate = array(
    	'rubro_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un rubro'
        ),
        'nombre' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        ),
        'razon' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese una Razon Social valida'
        ),
        'cuit'=> array(
                 /*'rule' => array('CPcuitValido'),
        		 'required' => true,	
                 'message' => 'Cuit invalido.'
               ),*/
               'no_vacio' => array(
                'required'   => true,
                'rule' => 'numeric',
                'message' => 'Ingrese solo numeros'
             ),
          
             'minLength' => array(
                    'rule' => array('minLength', 11),
                    'message' => 'Ingrese 11 digitos.'
                ),
                'maxLength' => array(
                    'rule' => array('maxLength',11),
                    'message' => 'Ingrese 11 digitos.'
                )
                
            ),
        'condicion_impositiva_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una Condicion Impositiva'
        ),
        'jurisdiccion_inscripcion_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una Jurisdiccion Inscripcion'
        )
    );
    
	public function CPcuitValido( $data ) {
					$cuit = str_replace('-', '', $this->data['Proveedor']['cuit']);
                    $esCuit=false;
                      if ( strlen($cuit) <> 11) {  // si to estan todos los digitos
                        $esCuit=false;
                    } else
                     {
                        $x=$i=$dv=0;
                        // Multiplico los dígitos.
                        $vec[0] = (substr($cuit, 0, 1)) * 5;
                        $vec[1] = (substr($cuit, 1, 1)) * 4;
                        $vec[2] = (substr($cuit, 2, 1)) * 3;
                        $vec[3] = (substr($cuit, 3, 1)) * 2;
                        $vec[4] = (substr($cuit, 4, 1)) * 7;
                        $vec[5] = (substr($cuit, 5, 1)) * 6;
                        $vec[6] = (substr($cuit, 6, 1)) * 5;
                        $vec[7] = (substr($cuit, 7, 1)) * 4;
                        $vec[8] = (substr($cuit, 8, 1)) * 3;
                        $vec[9] = (substr($cuit, 9, 1)) * 2;

                        // Suma cada uno de los resultado.
                        for( $i = 0;$i<=9; $i++) {
                            $x += $vec[$i];
                        }
                        $dv = (11 - ($x % 11)) % 11;
                        if ($dv == (substr($cuit, 10, 1)) ) {
                            $esCuit=true;
                        }
                    }
                    return( $esCuit );
                }
}
?>
