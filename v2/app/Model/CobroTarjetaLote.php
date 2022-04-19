<?php
class CobroTarjetaLote extends AppModel {
    public $belongsTo = 'CobroTarjetaTipo';
    
    /*public $validate = array(
        'fecha_cierre' => array(
            'format_fecha' => array(
                'rule'     => array('date','dmy'),
                'message' => 'Ingrese una fecha valida'
             ),
            'fecha_anterior' => array(
                'rule' => 'fecha_cierra_menor_hoy',
                'message' => 'La fecha no puede ser posterior a hoy'
            )
        ),
        'fecha_acreditacion' => array(
            'format_fecha' => array(
                'rule'     => array('date','dmy'),
                'message' => 'Ingrese una fecha valida'
            ),
            'fecha_anterior' => array(
                'rule' => 'fecha_acredita_menor_hoy',
                'message' => 'La fecha no puede ser posterior a hoy o anterior a la fecha de cierre'
            )
        ),
        'descuentos' => array(
            'rule'    => array('range', 0,999999),
            'message' => 'Numero mayor a 0'
        )
   );*/

    public $validate = array(
        
        'fecha_acreditacion' => array(
            'format_fecha' => array(
                'rule'     => array('date','dmy'),
                'message' => 'Ingrese una fecha valida'
            ),
            'fecha_anterior' => array(
                'rule' => 'fecha_acredita_menor_hoy',
                'message' => 'La fecha no puede ser posterior a hoy'
            )
        )/*,
        'descuentos' => array(
            'rule'    => array('range', 0,999999),
            'message' => 'Numero mayor a 0'
        )*/
   );
    
    public $virtualFields = array(
        'mes_cierre' => 'MONTH(fecha_cierre)',
        'mes_acreditacion' => 'MONTH(CobroTarjetaLote.fecha_acreditacion)',
        'ano_mes_acreditado' => 'DATE_FORMAT(CobroTarjetaLote.fecha_acreditacion,"%y%m")'
    );
    
   /*public  function fecha_acredita_menor_hoy(){
       $fecha_hoy = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
       if(isset($this->data['CobroTarjetaLote']['fecha_acreditacion'])){
           $parts = explode("/",$this->data['CobroTarjetaLote']['fecha_acreditacion']);
           $fecha_acreditacion = mktime(0,0,0, $parts[1], $parts[0], $parts[2]);
           
           $parts = explode("/",$this->data['CobroTarjetaLote']['fecha_cierre']);
           $fecha_cierre = mktime(0,0,0, $parts[1], $parts[0], $parts[2]); //echo $fecha_cierre;
           
           if($fecha_acreditacion <= $fecha_hoy && $fecha_acreditacion > $fecha_cierre){
               return true;
           }
       }
   }*/
    
	public  function fecha_acredita_menor_hoy(){
       $fecha_hoy = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
       if(isset($this->data['CobroTarjetaLote']['fecha_acreditacion'])){
           $parts = explode("/",$this->data['CobroTarjetaLote']['fecha_acreditacion']);
           $fecha_acreditacion = mktime(0,0,0, $parts[1], $parts[0], $parts[2]);
           
           
           
           if($fecha_acreditacion <= $fecha_hoy){
               return true;
           }
       }
   }

   public  function fecha_cierra_menor_hoy(){
       $fecha_hoy = mktime(0, 0, 0, date('m'), date('d'), date('Y')); //echo $fecha_hoy." ]] ";
       if(isset($this->data['CobroTarjetaLote']['fecha_cierre'])){ 
           $parts = explode("/",$this->data['CobroTarjetaLote']['fecha_cierre']);
           $fecha_cierre = mktime(0,0,0, $parts[1], $parts[0], $parts[2]); //echo $fecha_cierre;
           if($fecha_cierre <= $fecha_hoy){
               return true;
           }
       }
   }

   public function beforeSave($options = Array()) {
        if (!empty($this->data['CobroTarjetaLote']['fecha_cierre'])) {
            $this->data['CobroTarjetaLote']['fecha_cierre'] = $this->dateFormatBeforeSave($this->data['CobroTarjetaLote']['fecha_cierre']);
        }
        if (!empty($this->data['CobroTarjetaLote']['fecha_acreditacion'])) {
            $this->data['CobroTarjetaLote']['fecha_acreditacion'] = $this->dateFormatBeforeSave($this->data['CobroTarjetaLote']['fecha_acreditacion']);
        }
        return true;
    }
    
    public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (!empty($val) and isset($val['CobroTarjetaLote']['fecha_cierre'])) {
                $results[$key]['CobroTarjetaLote']['fecha_cierre']= $this->dateFormatAfterFind($val['CobroTarjetaLote']['fecha_cierre']);
            }
            if (!empty($val) and isset($val['CobroTarjetaLote']['fecha_acreditacion'])) {
                $results[$key]['CobroTarjetaLote']['fecha_acreditacion']= $this->dateFormatAfterFind($val['CobroTarjetaLote']['fecha_acreditacion']);
            }
        }
        return $results;
    }
}
?>
