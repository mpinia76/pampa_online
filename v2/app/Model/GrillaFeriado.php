<?php
class GrillaFeriado extends AppModel {
    public $validate = array(
        'nombre' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        ),
        'desde' => array(
            'rule'     => array('date','dmy'),
            'required' => true,
            'message' => 'Ingrese una fecha valida'
        ),


        'hasta' => array(
            'format' => array(
                'rule'     => array('date','dmy'),
                'required' => true,
                'message' => 'Ingrese una fecha valida'
            ),
            'after' => array(
                'rule' => 'after_hasta',
                'message' => 'La fecha hasta debe ser igual o posterior a la fecha desde'
            )
        )


    );

    public function after_hasta($data){



        $desdeArray = explode("/", $this->data['GrillaFeriado']['desde']);
        //print_r($desdeArray);
        $desde = $desdeArray[2].'-'.$desdeArray[1].'-'.$desdeArray[0];
        $hastaArray = explode("/", $this->data['GrillaFeriado']['hasta']);
        $hasta = $hastaArray[2].'-'.$hastaArray[1].'-'.$hastaArray[0];

        if($desde <= $hasta) return true;
    }

    public function beforeSave($options = Array()) {


        if(isset($this->data['GrillaFeriado']['desde'])){
            $this->data['GrillaFeriado']['desde'] = $this->dateFormatBeforeSave($this->data['GrillaFeriado']['desde']);
        }

        if(($this->data['GrillaFeriado']['hasta'])){
            $this->data['GrillaFeriado']['hasta'] = $this->dateFormatBeforeSave($this->data['GrillaFeriado']['hasta']);
        }



        return true;
    }

    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {

            if (!empty($val) and isset($val['GrillaFeriado']['desde'])) {
                $results[$key]['GrillaFeriado']['desde']= $this->dateFormatAfterFind($val['GrillaFeriado']['desde']);
            }
            if (!empty($val) and isset($val['GrillaFeriado']['hasta'])) {
                $results[$key]['GrillaFeriado']['hasta']= $this->dateFormatAfterFind($val['GrillaFeriado']['hasta']);
            }

        }
        return $results;
    }

}
?>