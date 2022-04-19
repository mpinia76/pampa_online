<?php
class Apartamento extends AppModel {
	public $belongsTo = array('Categoria');
    public $displayField = 'apartamento';
    public $validate = array(
    	'categoria_id' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Debe seleccionar una categoría'
        ),
        'apartamento' => array(
            'required'   => true,
            'rule' => 'notEmpty',
            'message' => 'Ingrese un nombre valido'
        )
    );
    
	/*public function afterFind($results, $primary = false) {
        foreach ($results as $key => $val) {
            if (isset($val['Apartamento']['excluir'])) {
                $results[$key]['Apartamento']['excluir']= ($results[$key]['Apartamento']['excluir']==1)?'SI':'NO';
            }
        }
        return $results;
    }*/
}
?>
