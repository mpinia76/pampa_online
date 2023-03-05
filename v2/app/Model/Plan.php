<?php
class Plan extends AppModel {
    public $displayField = 'plan';
    public $belongsTo = array(
        'Rubro' => array(
            'fields' => 'id,rubro'
        ),
        'Subrubro' => array(
            'fields' => 'id,subrubro'
        ),
        'Usuario' => array(
            'className' => 'Usuario',
            'foreignKey' => 'user_id',
            'fields' => 'id,nombre,apellido'
        ),
        'Proveedor' => array(
            'className' => 'Proveedor',
            'foreignKey' => 'proveedor',
            'fields' => 'id,nombre'
        )
    );
    public $hasMany = array('CuotaPlan');
    public $validate = array(
        'plan' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        ),
        'vencimiento1' => array(
            'rule'     => array('date','dmy'),
            'required' => true,
            'message' => 'Ingrese una fecha valida'
        ),

        'vencimiento2' => array(
            'format' => array(
                'rule'     => array('date','dmy'),
                'required' => true,
                'message' => 'Ingrese una fecha valida'
            ),
            'after' => array(
                'rule' => 'after_vencimiento1',
                'message' => 'La fecha debe ser posterior al Vencimiento cuota 1'
            )
        ),
        'monto' => array(
            'numero' => array(
                'rule'    => array('range', 0,9999999),
                'required'   => true,
                'message' => 'Ingrese un numero mayor a 0'
            )
        ),
        'intereses' => array(
            'on' => 'create',
            'rule'    => array('range', -999999,999999),
            'required'   => true,
            'message' => 'Ingrese un numero'
        ),
        'cuotas' => array(
            'no_vacio' => array(
                'rule'    => array('range', 1,999999),
                'required'   => true,
                'message' => 'Ingrese un numero mayor a 1'
            )
        )
    );


    public function after_vencimiento1($data){
        $vencimiento1_part = explode("/",$this->data[$this->alias]['vencimiento1']);
        $vencimiento2_part = explode("/",$data['vencimiento2']);
        $vencimiento1 = strtotime($vencimiento1_part[2]."-".$vencimiento1_part[1]."-".$vencimiento1_part[0]);
        $vencimiento2 = strtotime($vencimiento2_part[2]."-".$vencimiento2_part[1]."-".$vencimiento2_part[0]);
        if($vencimiento1 < $vencimiento2) return true;
    }


    public function beforeSave($options = Array()) {
        if (!empty($this->data['Plan']['vencimiento1'])) {
            $this->data['Plan']['vencimiento1'] = $this->dateFormatBeforeSave($this->data['Plan']['vencimiento1']);
        }
        if (!empty($this->data['Plan']['vencimiento2'])) {
            $this->data['Plan']['vencimiento2'] = $this->dateFormatBeforeSave($this->data['Plan']['vencimiento2']);
        }
        return true;
    }


    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (!empty($val) and isset($val['Plan']['vencimiento1'])) {
                $results[$key]['Plan']['vencimiento1']= $this->dateFormatAfterFind($val['Plan']['vencimiento1']);
            }

            if (!empty($val) and isset($val['Plan']['vencimiento2'])) {
                $results[$key]['Plan']['vencimiento2']= $this->dateFormatAfterFind($val['Plan']['vencimiento2']);
            }
        }
        return $results;
    }
}
?>
