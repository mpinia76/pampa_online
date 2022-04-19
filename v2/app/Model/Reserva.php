<?php
class Reserva extends AppModel {
    public $belongsTo = array('Cliente','Apartamento','Subcanal',
        'Usuario' => array(
            'className'    => 'Usuario',
            'foreignKey'   => 'cargado_por'
        ),
        'Empleado' => array(
            'className'    => 'Empleado',
            'foreignKey'   => 'reservado_por'
        )
    );
    public $hasMany = array('ReservaCobro','ReservaExtra','ReservaFactura','ReservaDevolucion');
    
    public $validate = array(
        'check_in' => array(
            'rule'     => array('date','dmy'),
            'required' => true,
            'message' => 'Ingrese una fecha valida'
        ),
        'check_out' => array(
            'format' => array(
                'rule'     => array('date','dmy'),
                'required' => true,
                'message' => 'Ingrese una fecha valida'
            ),
            'after' => array(
                'rule' => 'after_check_in',
                'message' => 'La fecha debe ser posterior al check-in'
            )
        ),
        'apartamento_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar un apartamento'
        )/*,
        'reservado_por' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar quien realizo la reserva'
        )*/
    );
    
    public $virtualFields = array(
        'mes' => 'MONTH(check_out)',
        'noches' => 'DATEDIFF(check_out,check_in)'
    );
    
    public function after_check_in($data){
        $check_in_part = explode("/",$this->data[$this->alias]['check_in']);
        $check_out_part = explode("/",$data['check_out']);
        $check_in = strtotime($check_in_part[2]."-".$check_in_part[1]."-".$check_in_part[0]);
        $check_out = strtotime($check_out_part[2]."-".$check_out_part[1]."-".$check_out_part[0]);
        if($check_in < $check_out) return true;
    }

    public function beforeSave($options = Array()) {
        if (!empty($this->data['Reserva']['check_in']) && !empty($this->data['Reserva']['check_out'])) {
            $this->data['Reserva']['check_in'] = $this->dateFormatBeforeSave($this->data['Reserva']['check_in']);
            $this->data['Reserva']['check_out'] = $this->dateFormatBeforeSave($this->data['Reserva']['check_out']);
        }

        if(isset($this->data['Reserva']['creado'])){
            $this->data['Reserva']['creado'] = $this->dateFormatBeforeSave($this->data['Reserva']['creado']);
        }
        return true;
    }
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (!empty($val) and isset($val['Reserva']['check_in'])) {
                $results[$key]['Reserva']['check_in']= $this->dateFormatAfterFind($val['Reserva']['check_in']);
            }
            if (!empty($val) and isset($val['Reserva']['check_out'])) {
                $results[$key]['Reserva']['check_out']= $this->dateFormatAfterFind($val['Reserva']['check_out']);
            }
            if (!empty($val) and isset($val['Reserva']['creado'])) {
                $results[$key]['Reserva']['creado']= $this->dateFormatAfterFind($val['Reserva']['creado']);
            }   
        }
        return $results;
    }
    
}
?>
