<?php
class ExtraRubrosController extends AppController {
    public $scaffold;

    public function detalle($rubro_id){
        $this->layout = 'ajax';

        $this->ExtraRubro->id = $rubro_id;
        $er = $this->ExtraRubro->read();
        if($er['ExtraRubro']['extra_variables']){
            $this->render('extra_variable');
        }else{
            //listo los extras disponibles
            $this->loadModel('Extra');
            $this->set('subrubro_precio', $this->Extra->findAllByExtraRubroIdAndActivo($rubro_id, 1));
            $this->render('extra_precio');
        }
    }

    public function detalle_precio($rubro_id){
        $this->layout = 'ajax';

        $this->ExtraRubro->id = $rubro_id;
        $er = $this->ExtraRubro->read();
        if($er['ExtraRubro']['extra_variables']){
            $this->render('extra_variable');
        }else{
            //listo los extras disponibles
            $this->loadModel('Extra');
            //$this->set('subrubro_precio', $this->Extra->findAllByExtraRubroIdAndActivo($rubro_id, 1));
            $extras_activos = $this->Extra->findAllByExtraRubroIdAndActivo($rubro_id, 1);
            foreach ($extras_activos as $extra_activo) {
                if (isset($extra_activo['Extra']['extra_subrubro_id'])) {
                    $extra_subrubro_ids[] = $extra_activo['Extra']['extra_subrubro_id'];
                }
            }
// Eliminar duplicados
            $extra_subrubro_ids = array_unique($extra_subrubro_ids);
            //print_r($extra_subrubro_ids);
            $this->set('extra_subrubros', $this->Extra->ExtraSubrubro->find('list',array('conditions' =>array('ExtraSubrubro.id' => $extra_subrubro_ids),'order' => 'subrubro ASC')));
            $this->render('extra_subrubro');
        }
    }

    public function obtenerSubrubros($extra_rubro_id) {
        $this->layout = 'ajax';
        $this->ExtraRubro->id = $extra_rubro_id;
        $er = $this->ExtraRubro->read();
        if($er['ExtraRubro']['extra_variables']){
            $this->render('extra_variable');
        }else {
            //listo los extras disponibles
            $this->loadModel('Extra');
            // Obtener subrubros únicos para el rubro
            $subrubros = $this->Extra->find('list', array(
                'conditions' => array(
                    'Extra.extra_rubro_id' => $extra_rubro_id,
                    'Extra.activo' => 1
                ),
                'fields' => array('ExtraSubrubro.id', 'ExtraSubrubro.subrubro'), // Obtener el nombre del subrubro desde la tabla extra_subrubros
                'joins' => array(
                    array(
                        'table' => 'extra_subrubros',  // Especificar la tabla extra_subrubros
                        'alias' => 'ExtraSubrubro',    // Definir un alias para la tabla
                        'type' => 'INNER',             // Realizar una unión interna
                        'conditions' => 'Extra.extra_subrubro_id = ExtraSubrubro.id'  // Condición de unión entre extra_subrubros y Extra
                    )
                ),
                'group' => 'ExtraSubrubro.id',   // Agrupar por el ID del subrubro para evitar duplicados
                'order' => 'ExtraSubrubro.subrubro ASC' // Ordenar por el nombre del subrubro
            ));

            // Verifica los resultados de la consulta
            //print_r($subrubros);
            $this->set('subrubros', $subrubros);
            //$this->set('_serialize', ['subrubros']);
            $this->render('extra_subrubro');
        }
    }

    public function obtenerDetalles($subrubro_id) {
        $this->layout = 'ajax';
//listo los extras disponibles
        $this->loadModel('Extra');
        // Obtener detalles asociados al subrubro
        $detalles = $this->Extra->find('list', array(
            'conditions' => array(
                'Extra.extra_subrubro_id' => $subrubro_id,
                'Extra.activo' => 1
            ),
            'fields' => array('id', 'detalle'),
            'order' => 'detalle ASC'
        ));
//print_r($detalles);
        $this->set('detalles', $detalles);
        $this->set('_serialize', array('detalles'));
        //$this->render('extra_subrubro');
    }

    public function obtenerPrecio($detalle_id) {
        $this->layout = 'ajax';
//listo los extras disponibles
        $this->loadModel('Extra');
        // Obtener el precio asociado al detalle
        $precio = $this->Extra->field('tarifa', array('Extra.id' => $detalle_id));

        $this->set('precio', $precio);
        $this->set('_serialize', array('precio'));
        //$this->render('extra_subrubro');
    }

}
?>
