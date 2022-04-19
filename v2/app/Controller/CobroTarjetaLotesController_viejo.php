<?php ini_set('memory_limit', '-1');
class CobroTarjetaLotesController extends AppController {
    
    public function index(){
        $this->layout = 'index';
        
        $this->loadModel('Usuario');
        $this->Usuario->id = $_COOKIE['userid'];
        $usuario = $this->Usuario->read();
    }
    


     public function get_lotes_cobro_tarjetas() {

        $result = Cache::read('get_lotes_cobro_tarjetas', 'long');
            if (!$result) {
                $lotes = $this->CobroTarjeta->find('all',array('fields'=>array('CobroTarjeta.*', 'CobroTarjetaTipo.*','CobroTarjetaLote.*', 'sum(CobroTarjeta.interes + CobroTarjeta.monto_neto) as monto_total, count(CobroTarjeta.id) as operaciones'), 'group' => array('CobroTarjeta.cobro_tarjeta_tipo_id', 'CobroTarjeta.lote'), 'conditions' => array( 'CobroTarjeta.lote !=' => '' ), 'recursive' => 2));
                Cache::write('get_lotes_cobro_tarjetas', $lotes, 'long');
            }
            return $lotes;
    }



    public function dataTable(){
        $this->layout = 'ajax';
        
        $rows = array();
        $this->loadModel('CobroTarjeta');

        //Se agrega cache para query
        $lotes =  $this->get_lotes_cobro_tarjetas();

        
        foreach($lotes as $lote){
            if($lote['CobroTarjeta']['cobro_tarjeta_lote_id'] == 0){
                $estado = 'Pendiente de cierre';
            }elseif($lote['CobroTarjetaLote']['fecha_cierre'] != '' and $lote['CobroTarjetaLote']['fecha_acreditacion'] == ''){
                $estado = 'Pendiente de acreditacion';
            }elseif($lote['CobroTarjetaLote']['fecha_cierre'] != '' and $lote['CobroTarjetaLote']['fecha_acreditacion'] != ''){
                $estado = 'Acreditado';
            }else{
                $estado = 'Revisar';
            }
            
            $rows[] = array(
                $lote['CobroTarjeta']['cobro_tarjeta_lote_id'],
                $lote['CobroTarjetaTipo']['id'],
                $lote['CobroTarjetaTipo']['CobroTarjetaPosnet']['posnet'],
                $lote['CobroTarjetaTipo']['marca'],
                $lote['CobroTarjetaTipo']['Cuenta']['nombre'],
                $lote['CobroTarjeta']['lote'],
                '$'.$lote['0']['monto_total'],
                $lote['0']['operaciones'],
                $lote['CobroTarjetaLote']['fecha_cierre'],
                $lote['CobroTarjetaLote']['fecha_acreditacion'],
                $estado
            );
        }
        $this->set('aaData',$rows);
        $this->set('_serialize', array(
            'aaData'
        ));
    }
    
    public function cerrar(){
        $this->CobroTarjetaLote->set(array(
            'cobro_tarjeta_tipo_id' => $this->request->data['cobro_tarjeta_tipo_id'],
            'numero' => $this->request->data['numero'],
            'fecha_cierre' => $this->request->data['fecha_cierre'],
            'monto_total' => $this->request->data['monto_total'],
            'cerrado_por' => $this->request->data['cerrado_por']
        ));
        if(!$this->CobroTarjetaLote->validates()){
            $this->set('resultado','ERROR');
            $this->set('detalle',$this->CobroTarjetaLote->validationErrors);
        }else{
            $this->CobroTarjetaLote->save();
            
            $this->loadModel('CobroTarjeta');
            $this->CobroTarjeta->updateAll(
                array('CobroTarjeta.cobro_tarjeta_lote_id' => $this->CobroTarjetaLote->id),
                array('CobroTarjeta.cobro_tarjeta_tipo_id' => $this->request->data['cobro_tarjeta_tipo_id'],
                         'CobroTarjeta.lote' => $this->request->data['numero'])
            );
            $this->set('resultado','OK');
            $this->set('detalle','');
        }
        $this->set('_serialize', array(
            'resultado',
            'detalle' 
        ));
    }
    
    public function acreditar(){
        $cobro_tarjeta_lote = $this->CobroTarjetaLote->read(null,$this->request->data['id']);
        $this->CobroTarjetaLote->set(array(
            'fecha_acreditacion' => $this->request->data['fecha_acreditacion'],
            'descuentos' => $this->request->data['descuentos'],
            'acreditado_por' => $this->request->data['acreditado_por']
        ));
        if(!$this->CobroTarjetaLote->validates()){
            $this->set('resultado','ERROR');
            $this->set('detalle',$this->CobroTarjetaLote->validationErrors);
        }else{
            $this->CobroTarjetaLote->save();
            
            //aplico un proporcional del descuento del lote a cada transaccion para el informe economico
            $this->loadModel('CobroTarjeta');
            $cobros_tarjeta = $this->CobroTarjeta->find('all',array('conditions' => array('cobro_tarjeta_lote_id' => $this->CobroTarjetaLote->id)));
            foreach($cobros_tarjeta as $cobro){
                $this->CobroTarjeta->read(null,$cobro['CobroTarjeta']['id']);
                $lote_descuento = round((($cobro['CobroTarjeta']['monto_neto'] + $cobro['CobroTarjeta']['interes']) / $cobro_tarjeta_lote['CobroTarjetaLote']['monto_total']) * $this->request->data['descuentos'],2);
                
                $this->CobroTarjeta->updateAll(
                    array('CobroTarjeta.descuento_lote' => $lote_descuento),
                    array('CobroTarjeta.id' => $cobro['CobroTarjeta']['id'])
                );
            }
            
            //impacto la acreditacion en la cuenta
            $this->loadModel('CuentaMovimiento');
            $this->CuentaMovimiento->set(array(
                'cuenta_id' => $cobro_tarjeta_lote['CobroTarjetaTipo']['cuenta_id'],
                'origen' => 'acreditacionlote_'.$this->CobroTarjetaLote->id,
                'monto' => $cobro_tarjeta_lote['CobroTarjetaLote']['monto_total'] - $this->request->data['descuentos'],
                'fecha' => $this->request->data['fecha_acreditacion']
            ));
            $this->CuentaMovimiento->save();
            
            $this->set('resultado','OK');
            $this->set('detalle','');
        }
        $this->set('_serialize', array(
            'resultado',
            'detalle' 
        ));
    }
    
    //protege el controlador solo para usuarios
    public function beforeFilter(){
        if(isset($_COOKIE['userid'])){
            $this->loadModel('Usuario');
            $this->set('usuario',$this->Usuario->findById($_COOKIE['userid']));
        }else{
            $this->redirect('/index');
        }
    }
}
?>
