<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model {

    public function dateFormatAfterFind($dateString){
        if($dateString == '0000-00-00'){
            return '';
        }else{
            return date('d/m/Y',strtotime($dateString));
        }
    }

    public function dateFormatBeforeSave($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }

    function Format_toCuil( $dni, $sexo ){
        if( $sexo == 1 )
            //si es masculino
            $Primero = '20';
        else if( $sexo == 2 )
            //si es femenino
            $Primero = '27';
        else
            //si es sociedad
            $Primero = '30';

        $multiplicadores = Array('3', '2', '7','6', '5', '4', '3', '2');
        $calculo = (substr($Primero,0,1)*5)+(substr($Primero,1,1)*4);

        for($i=0;$i<8;$i++) {
            $calculo += substr($dni,$i,1) * $multiplicadores[$i];
        }

        $resto = ($calculo)%11;

        if( ( $sexo!='3' ) && ( $resto<=1 ) ){
            if($resto==0){
                $C = '0';
            } else {
                if($sexo==1){
                    $C = '9';
                } else {
                    $C = '4';
                }
            }
            $Primero = '23';
        } else {
            $C = 11-$resto;
        }

        return $cuil_cuit = $Primero . "-" . $dni . "-" . $C;
    }

	function checkUnique($data, $fields) {
                if (!is_array($fields)) {
                        $fields = array($fields);
                }
                foreach($fields as $key) {
                        $tmp[$key] = $this->data[$this->name][$key];
                }
                if (isset($this->data[$this->name][$this->primaryKey])) {
                        $tmp[$this->primaryKey] = "<>".$this->data[$this->name][$this->primaryKey];

                }
                return $this->isUnique($tmp, false);
        }

}
