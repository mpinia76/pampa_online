<?php
ini_set('memory_limit', '-1');
session_start();
class PlansController extends AppController {
    public $scaffold;

    public function dateFormatSQL($dateString) {
        //echo $dateString."<br>";
        $date_parts = explode("/",$dateString);
        switch (count($date_parts)) {
            case 1:
                $result = $date_parts[0];
                break;
            case 2:
                $result = $date_parts[1]."-".$date_parts[0];
                break;
            default:
                $result = $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
                break;
        }
        return $result;
    }




    public function crear($ids,$tipo){
        $this->layout = 'form';


        $idsArray = explode(",",$ids);
        $ok=1;
        $proveedorAnt='';
        $rubroAnt='';
        $subrubroAnt='';
        $total=0;
        foreach ($idsArray as $id) {
            switch ($tipo) {
                case 'gastos':
                    $this->loadModel('Gasto');
                    $this->Gasto->id = $id;
                    $gasto = $this->Gasto->read();
                    if(isset($gasto['Proveedor']['id'])){
                        $proveedor = $gasto['Proveedor']['nombre'];
                    }else{
                        $proveedor = $gasto['Gasto']['proveedor'];
                    }
                    $rubro=$gasto['Rubro']['id'];
                    $factura=$gasto['factura_nro'];

                    $subrubro=$gasto['Subrubro']['id'];
                    $total +=$gasto['Gasto']['monto'];
                    $strTipo='Gastos y compras';
                    $id_ventana='w_gasto';
                    $url_ventana='v2/gastos/index';
                    break;
                case 'compras':
                    $this->loadModel('Compra');
                    $this->Compra->id = $id;
                    $compra = $this->Compra->read();
                    if(isset($compra['Proveedor']['id'])){
                        $proveedor = $compra['Proveedor']['nombre'];
                    }else{
                        $proveedor = $compra['Compra']['proveedor'];
                    }
                    $rubro=$compra['Rubro']['id'];

                    $factura=$compra['factura_nro'];
                    $subrubro=$compra['Subrubro']['id'];
                    $total +=$compra['Compra']['monto'];
                    $strTipo='Impuestos, tasas y cargas sociales';
                    $id_ventana='w_compra';
                    $url_ventana='v2/compras/index';
                    break;
                case 'cgasto':
                    $this->loadModel('CuentaPagar');
                    $this->CuentaPagar->id = $id;
                    $CuentaPagar = $this->CuentaPagar->read();
                    $this->loadModel('Gasto');
                    $this->Gasto->id = $CuentaPagar['CuentaPagar']['operacion_id'];
                    $gasto = $this->Gasto->read();
                    if(isset($gasto['Proveedor']['id'])){
                        $proveedor = $gasto['Proveedor']['nombre'];
                    }else{
                        $proveedor = $gasto['Gasto']['proveedor'];
                    }
                    $rubro=$gasto['Rubro']['id'];
                    $factura=$gasto['factura_nro'];

                    $subrubro=$gasto['Subrubro']['id'];
                    $total +=$gasto['Gasto']['monto'];
                    $strTipo='Cuentas a pagar';
                    $id_ventana='w_cuenta_a_pagar';
                    $url_ventana='cuentas_pagar.php';
                    break;

                case 'ccompra':
                    $this->loadModel('CuentaPagar');
                    $this->CuentaPagar->id = $id;
                    $CuentaPagar = $this->CuentaPagar->read();
                    $this->loadModel('Compra');
                    $this->Compra->id = $CuentaPagar['CuentaPagar']['operacion_id'];
                    $compra = $this->Compra->read();
                    if(isset($compra['Proveedor']['id'])){
                        $proveedor = $compra['Proveedor']['nombre'];
                    }else{
                        $proveedor = $compra['Compra']['proveedor'];
                    }
                    $rubro=$compra['Rubro']['id'];

                    $factura=$compra['factura_nro'];
                    $subrubro=$compra['Subrubro']['id'];
                    $total +=$compra['Compra']['monto'];
                    $strTipo='Cuentas a pagar ';
                    $id_ventana='w_cuenta_a_pagar';
                    $url_ventana='cuentas_pagar.php';
                    break;
            }
        }

        $this->set('proveedor', $proveedor);
        $this->set('rubro', $rubro);
        $this->set('subrubro', $subrubro);
        $this->set('monto', $total);
        $this->set('factura', $factura);
        $this->set('tipo', $strTipo);
        $this->set('ids', $ids);
        $this->set('user', $_COOKIE['userid']);
        $this->set('id_ventana', $id_ventana);
        $this->set('url_ventana', $url_ventana);

    }
    public function editar($id = null){
        $this->layout = 'form';

        $this->loadModel('ExtraRubro');
        $this->loadModel('ExtraSubrubro');

        //lista de apartamentos
        $this->set('apartamentos', $this->Reserva->Apartamento->find('list'));

        $this->Reserva->id = $id;
        if ($this->request->is("get")) {
            $this->request->data = $this->Reserva->read();
            $this->set('reserva', $this->Reserva->read());
            $this->set('extra_rubros',$this->ExtraRubro->find('list'));
            $this->set('extra_subrubros',$this->ExtraSubrubro->find('list'));
        }
    }

    public function guardar(){

        //load modules
        $this->loadModel('CuotaPlan');
        $this->loadModel('Gasto');
        $this->loadModel('Compra');
        $this->loadModel('CuentaPagar');
        //print_r($this->request->data);
        if(!empty($this->request->data)) {

            $plan = $this->request->data['Plan'];
            $this->Plan->set($plan);
            if(!$this->Plan->validates()){
                $errores['Plan'] = $this->Plan->validationErrors;
            }

            //muestro resultado
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
                $this->Plan->begin();
                $this->Gasto->begin();
                $this->Compra->begin();
                $this->CuotaPlan->begin();
                $grabar=1;
                //guardo plan
                $this->Plan->save();
                $ordenes=array();
                $ids = explode(",",$plan['ids']);
                foreach ($ids as $id) {

                    switch ($plan['tipo']) {
                        case 'Gastos y compras':
                            $this->Gasto->id = $id;
                            $gasto =$this->Gasto->read();
                            $this->Gasto->set('plan_id',$this->Plan->id);
                            if(!$this->Gasto->save()){
                                $grabar=0;
                            }
                            $ordenes[]=$gasto['Gasto']['nro_orden'];
                            break;
                        case 'Impuestos, tasas y cargas sociales':
                            $this->Compra->id = $id;
                            $compra=$this->Compra->read();
                            $this->Compra->set('plan_id',$this->Plan->id);
                            if(!$this->Compra->save()){
                                $grabar=0;
                            }
                            $ordenes[]=$compra['Compra']['nro_orden'];
                            break;

                        default:
                            $this->CuentaPagar->id = $id;
                            $cuentaPagar=$this->CuentaPagar->read();
                            $this->CuentaPagar->set('plan_id',$this->Plan->id);
                            $this->CuentaPagar->set('estado',2);
                            if(!$this->CuentaPagar->save()){
                                $grabar=0;
                            }
                            if ($cuentaPagar['CuentaPagar']['operacion_tipo']=='gasto'){
                                $this->Gasto->id = $cuentaPagar['CuentaPagar']['operacion_id'];
                                $gasto =$this->Gasto->read();
                                $ordenes[]=$gasto['Gasto']['nro_orden'];
                            }
                            if ($cuentaPagar['CuentaPagar']['operacion_tipo']=='compra'){
                                $this->Compra->id = $cuentaPagar['CuentaPagar']['operacion_id'];
                                $compra =$this->Compra->read();
                                $ordenes[]=$compra['Compra']['nro_orden'];
                            }
                            break;
                    }

                }
                if ($grabar) {
                    //print_r($ordenes);
                    $this->Plan->saveField('ordenes',implode(',', $ordenes));

                    //$this->Plan->save();
                    $montoCuota=($plan['monto']+$plan['intereses'])/$plan['cuotas'];
                    for ($i = 1; $i <= $plan['cuotas']; $i++) {
                        switch ($i) {
                            case 1:
                                $vencimiento=$plan['vencimiento1'];
                                break;
                            case 2:
                                $vencimiento=$plan['vencimiento2'];
                                break;
                            default:
                                $date_parts = explode("/",$plan['vencimiento2']);
                                $fecha = date($date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0]);
                                $nuevafecha = strtotime ( '+'.($i-2).' month' , strtotime ( $fecha ) ) ;

                                $vencimiento = date ( 'j/m/y' , $nuevafecha );

                                break;
                        }
                        $this->CuotaPlan->create();
                        $this->CuotaPlan->set('plan_id',$this->Plan->id);
                        $this->CuotaPlan->set('vencimiento',$vencimiento);
                        $this->CuotaPlan->set('monto',$montoCuota);
                        $this->CuotaPlan->set('estado',0);
                        if(!$this->CuotaPlan->save()){
                            $grabar=0;
                        }
                    }
                }

                if($grabar) {
                    $this->Plan->commit();
                    $this->Gasto->commit();
                    $this->Compra->commit();
                    $this->CuotaPlan->commit();
                    $this->CuentaPagar->commit();
                    $this->set('resultado','OK');
                    $this->set('mensaje','Datos guardados');
                    $this->set('detalle','');
                }
                else
                {
                    $this->Plan->rollback();
                    $this->Gasto->rollback();
                    $this->Compra->rollback();
                    $this->CuotaPlan->rollback();
                    $this->CuentaPagar->rollback();
                    $this->set('resultado','ERROR');
                    $this->set('mensaje','No se pudo guardar');
                    $this->set('detalle','');
                }



            }
            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }
    }



    public function controlar(){
        $ids = $this->request->data['items'];
        $tipo = $this->request->data['tipo'];
        $ids = explode(",",$ids);
        $ok=1;
        $proveedorAnt='';
        $rubroAnt='';
        $subrubroAnt='';
        foreach ($ids as $id) {
            switch ($tipo) {
                case 'gastos':
                    $this->loadModel('Gasto');
                    $this->Gasto->id = $id;
                    $gasto = $this->Gasto->read();
                    // print_r($gasto);
                    if(isset($gasto['Proveedor']['id'])){
                        $proveedor = $gasto['Proveedor']['nombre'];
                    }else{
                        $proveedor = $gasto['Gasto']['proveedor'];
                    }
                    if (($proveedorAnt!='')&&(strcasecmp($proveedorAnt, $proveedor) != 0)) {
                        //echo $proveedorAnt.' - '.$proveedor;
                        $ok=0;

                    }
                    $proveedorAnt=$proveedor;

                    if (($rubroAnt!='')&&($rubroAnt!=$gasto['Rubro']['id'])) {

                        $ok=0;

                    }
                    $rubroAnt=$gasto['Rubro']['id'];

                    if (($subrubroAnt!='')&&($subrubroAnt!=$gasto['Subrubro']['id'])) {
                        $ok=0;

                    }
                    $subrubroAnt=$gasto['Subrubro']['id'];
                    break;
                case 'compras':
                    $this->loadModel('Compra');
                    $this->Compra->id = $id;
                    $compra = $this->Compra->read();
                    // print_r($compra);
                    if(isset($compra['Proveedor']['id'])){
                        $proveedor = $compra['Proveedor']['nombre'];
                    }else{
                        $proveedor = $compra['Compra']['proveedor'];
                    }
                    if (($proveedorAnt!='')&&(strcasecmp($proveedorAnt, $proveedor) != 0)) {
                        //echo $proveedorAnt.' - '.$proveedor;
                        $ok=0;

                    }
                    $proveedorAnt=$proveedor;

                    if (($rubroAnt!='')&&($rubroAnt!=$compra['Rubro']['id'])) {

                        $ok=0;

                    }
                    $rubroAnt=$compra['Rubro']['id'];

                    if (($subrubroAnt!='')&&($subrubroAnt!=$compra['Subrubro']['id'])) {
                        $ok=0;

                    }
                    $subrubroAnt=$compra['Subrubro']['id'];
                    break;
                default:
                    ;
                    break;
            }
        }


        if ($ok) {

            $this->set('resultado','OK');
            $this->set('mensaje','');
            $this->set('detalle','');
        }
        else{

            $this->set('resultado','ERROR');
            $this->set('mensaje','No se puede generar - ');
            $this->set('detalle','Los items deben ser del mismo rubro, subrubro y proveedor');
        }

        $this->set('_serialize', array(
            'resultado',
            'mensaje' ,
            'detalle'
        ));


    }

    public function borrar(){
        //print_r($this->request->data);
        $id = $this->request->data['id'];
        $tipo = $this->request->data['tipo'];
        $this->loadModel('CuotaPlan');
        $this->loadModel('Gasto');
        $this->loadModel('Compra');
        $this->loadModel('CuentaPagar');


        $this->Plan->begin();
        $this->Gasto->begin();
        $this->Compra->begin();
        $this->CuotaPlan->begin();
        $this->CuentaPagar->begin();
        $grabar=1;

        $result['error']='';


        switch ($tipo) {
            case 'gastos':
                $this->Gasto->id = $id;
                $gasto = $this->Gasto->read();
                //print_r($gasto);
                $id_plan=$gasto['Gasto']['plan_id'];
                $operacion_tipo = 'gasto';

                break;
            case 'compras':
                $this->Compra->id = $id;
                $compra = $this->Compra->read();
                $id_plan=$compra['Compra']['plan_id'];
                $operacion_tipo = 'compra';
                break;
            default:
                $this->CuentaPagar->id = $id;
                $cuentaPagar = $this->CuentaPagar->read();
                $id_plan=$cuentaPagar['CuentaPagar']['plan_id'];
                break;
        }


        if ($id_plan) {


            $cuotas = $this->CuotaPlan->find('all',array('conditions' => array('plan_id' => $id_plan, 'estado' => 1)));

            if(count($cuotas)>0){

                $errores='Existen cuotas del plan ya abonadas';
                $result['error']='Existen cuotas del plan ya abonadas';
                $grabar=0;
            }
            else{
                $this->CuotaPlan->deleteAll(array('plan_id' => $id_plan), false);
                $this->Plan->delete($id_plan,true);
                switch ($tipo) {
                    case 'gastos':
                        $gastos = $this->Gasto->find('all',array('conditions' => array('plan_id' => $id_plan)));
                        foreach ($gastos as $g) {
                            $this->Gasto->id = $g['Gasto']['id'];
                            $gasto = $this->Gasto->read();
                            $this->Gasto->set('plan_id',null);
                            if(!$this->Gasto->save()){
                                $grabar=0;
                            }
                        }



                        break;
                    case 'compras':

                        $compras = $this->Compra->find('all',array('conditions' => array('plan_id' => $id_plan)));
                        foreach ($compras as $c) {
                            $this->Compra->id = $c['Compra']['id'];
                            $compra = $this->Compra->read();
                            $this->Compra->set('plan_id',null);
                            if(!$this->Compra->save()){
                                $grabar=0;
                            }
                        }
                        break;
                    default:
                        $cuentaPagars = $this->CuentaPagar->find('all',array('conditions' => array('plan_id' => $id_plan)));
                        foreach ($cuentaPagars as $c) {
                            $this->CuentaPagar->id = $c['CuentaPagar']['id'];
                            $cuentaPagar = $this->CuentaPagar->read();
                            $this->CuentaPagar->set('plan_id',null);
                            $this->CuentaPagar->set('estado',0);
                            if(!$this->CuentaPagar->save()){
                                $grabar=0;
                            }
                        }
                        break;
                }
            }
        }
        else{
            $cuentaPagar = $this->CuentaPagar->find('all',array('conditions' => array('operacion_id' => $id[0], 'operacion_tipo' => $operacion_tipo,'plan_id <>' => null)));
            //echo 'operacion_id '.$id[0]. ' operacion_id '.$tipo;
            if (!empty($cuentaPagar)){
                $errores='Anule el plan desde cuentas a pagar';
                $result['error']='Anule el plan desde cuentas a pagar';
            }
            else{
                $errores='No existe el plan';
                $result['error']='No existe el plan';
            }

            $grabar=0;
        }

        if($grabar) {
            $this->Plan->commit();
            $this->Gasto->commit();
            $this->Compra->commit();
            $this->CuotaPlan->commit();
            $this->set('resultado','OK');
            $this->set('mensaje','Plan eliminado');
            $this->set('detalle','');
        }
        else
        {
            $this->Plan->rollback();
            $this->Gasto->rollback();
            $this->Compra->rollback();
            $this->CuotaPlan->rollback();
            $this->set('resultado','ERROR');
            $this->set('mensaje','No se pudo borrar');
            $this->set('detalle',$errores);
        }




        //print_r($result);
        if ($tipo=='cuentas') {
            echo json_encode( $result );
        }
        else{
            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }

        /**/

    }

    public function refinanciar($id){
        $this->layout = 'form';
        $this->loadModel('CuotaPlan');
        $this->CuotaPlan->id = $id;
        $cuota=$this->CuotaPlan->read();

        $proveedor=$cuota['Plan']['proveedor'];
        $rubro=$cuota['Plan']['rubro_id'];
        $subrubro=$cuota['Plan']['subrubro_id'];
        $strTipo=$cuota['Plan']['tipo'];


        $cuotas = $this->CuotaPlan->find('all',array('conditions' => array('plan_id' => $cuota['CuotaPlan']['plan_id'], 'estado' => 0)));

        $total=0;
        $arrayIds=array();
        foreach ($cuotas as $cta) {
            $arrayIds[]=$cta['CuotaPlan']['id'];
            $total +=$cta['CuotaPlan']['monto'];

            //print_r($cta);
        }



        $ids = implode(",",$arrayIds);

        $this->set('proveedor', $proveedor);
        $this->set('rubro', $rubro);
        $this->set('subrubro', $subrubro);
        $this->set('monto', $total);
        //$this->set('factura', $factura);
        $this->set('tipo', $strTipo);
        $this->set('ids', $ids);
        $this->set('ordenes', $cuota['Plan']['ordenes']);
        $this->set('user', $_COOKIE['userid']);


    }

    public function guardarRefinanciacion(){

        //load modules
        $this->loadModel('CuotaPlan');
        $this->loadModel('Gasto');
        $this->loadModel('Compra');
        $this->loadModel('CuentaPagar');
        //print_r($this->request->data);
        if(!empty($this->request->data)) {

            $plan = $this->request->data['Plan'];
            $this->Plan->set($plan);
            if(!$this->Plan->validates()){
                $errores['Plan'] = $this->Plan->validationErrors;
            }

            //muestro resultado
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
                $this->Plan->begin();
                $this->Gasto->begin();
                $this->Compra->begin();
                $this->CuotaPlan->begin();
                $grabar=1;
                $ids = explode(",",$plan['ids']);
                foreach ($ids as $id) {
                    $this->CuotaPlan->id = $id;
                    $cuotaPlan=$this->CuotaPlan->read();
                    $this->CuotaPlan->set('estado','2');
                    $planAnterior_id=$cuotaPlan['Plan']['id'];
                    if(!$this->CuotaPlan->save()){
                        $grabar=0;
                    }
                }


                //guardo plan
                $this->Plan->save();



                switch ($plan['tipo']) {
                    case 'Gastos y compras':
                        $gastos = $this->Gasto->find('all',array('conditions' => array('plan_id' => $planAnterior_id)));

                        foreach ($gastos as $gasto) {
                            $this->Gasto->id = $gasto['Gasto']['id'];
                            $this->Gasto->read();
                            $this->Gasto->set('plan_id',$this->Plan->id);
                            if(!$this->Gasto->save()){
                                $grabar=0;
                            }
                        }


                        break;
                    case 'Impuestos, tasas y cargas sociales':
                        $compras = $this->Compra->find('all',array('conditions' => array('plan_id' => $planAnterior_id)));

                        foreach ($compras as $compra) {
                            $this->Compra->id = $compra['Compra']['id'];
                            $this->Compra->read();
                            $this->Compra->set('plan_id',$this->Plan->id);
                            if(!$this->Compra->save()){
                                $grabar=0;
                            }
                        }



                        break;

                    default:
                        $cuentasPagar = $this->CuentaPagar->find('all',array('conditions' => array('plan_id' => $planAnterior_id)));

                        foreach ($cuentasPagar as $cuentaPagar) {
                            $this->CuentaPagar->id = $cuentaPagar['CuentaPagar']['id'];
                            $this->CuentaPagar->read();
                            $this->CuentaPagar->set('plan_id',$this->Plan->id);
                            //$this->CuentaPagar->set('estado',2);
                            if(!$this->CuentaPagar->save()){
                                $grabar=0;
                            }
                        }

                        break;
                }


                if ($grabar) {
                    $montoCuota=($plan['monto']+$plan['intereses'])/$plan['cuotas'];
                    for ($i = 1; $i <= $plan['cuotas']; $i++) {
                        switch ($i) {
                            case 1:
                                $vencimiento=$plan['vencimiento1'];
                                break;
                            case 2:
                                $vencimiento=$plan['vencimiento2'];
                                break;
                            default:
                                $date_parts = explode("/",$plan['vencimiento2']);
                                $fecha = date($date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0]);
                                $nuevafecha = strtotime ( '+'.($i-2).' month' , strtotime ( $fecha ) ) ;

                                $vencimiento = date ( 'j/m/y' , $nuevafecha );

                                break;
                        }
                        $this->CuotaPlan->create();
                        $this->CuotaPlan->set('plan_id',$this->Plan->id);
                        $this->CuotaPlan->set('vencimiento',$vencimiento);
                        $this->CuotaPlan->set('monto',$montoCuota);
                        $this->CuotaPlan->set('estado',0);
                        if(!$this->CuotaPlan->save()){
                            $grabar=0;
                        }
                    }
                }

                if($grabar) {
                    $this->Plan->commit();
                    $this->Gasto->commit();
                    $this->Compra->commit();
                    $this->CuotaPlan->commit();
                    $this->CuentaPagar->commit();
                    $this->set('resultado','OK');
                    $this->set('mensaje','Datos guardados');
                    $this->set('detalle','');
                }
                else
                {
                    $this->Plan->rollback();
                    $this->Gasto->rollback();
                    $this->Compra->rollback();
                    $this->CuotaPlan->rollback();
                    $this->CuentaPagar->rollback();
                    $this->set('resultado','ERROR');
                    $this->set('mensaje','No se pudo guardar');
                    $this->set('detalle','');
                }



            }
            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }
    }


}
?>
