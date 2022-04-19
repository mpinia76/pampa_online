<?php
class CuotaPlansController extends AppController {
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

    public function index(){
        $this->layout = 'index';
        $this->loadModel('Rubro');
        $this->set('rubros',$this->Rubro->find('list',array('conditions'=>array('gastos'=>1,'activo'=>1),'order' => 'Rubro.rubro asc')));

        $this->loadModel('Subrubro');
        $this->set('subrubros',$this->Subrubro->find('list',array('conditions'=>array('activo'=>1),'order' => 'Subrubro.subrubro asc')));

        $this->loadModel('Usuario');
        $this->set('usuario',$this->Usuario->find('list',array('order' => 'Usuario.nombre asc')));
        $this->setLogUsuario('Planes de pagos');
    }

    public function dataTable($estado=''){
        //print_r($_GET);
        $searchEstado= ($_GET['sSearch_8'])? ($_GET['sSearch_8']=='-1')?'':$_GET['sSearch_8']:$estado;
        $orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'asc';
        switch ($_GET['iSortCol_0']) {
            case 1:
                $order='Plan.plan '.$orderType;
                break;
            case 2:
                $order='Plan.tipo '.$orderType;
                break;
            case 4:
                $order='Plan.proveedor '.$orderType;
                break;

            case 5:
                $order='Plan.rubro_id '.$orderType;
                break;
            case 6:
                $order='Plan.subrubro_id '.$orderType;
                break;


            case 7:
                $order='CuotaPlan.monto '.$orderType;
                break;
            case 8:
                $order='CuotaPlan.vencimiento '.$orderType;
                break;
            case 10:
                $order='Usuario.nombre '.$orderType.', Usuario.apellido '.$orderType;
                break;
            default:
                $order='CuotaPlan.id '.$orderType;
                break;
        }



        $rows = array();


        $cuotas = $this->get_cuotas($_GET['iDisplayStart'], $_GET['iDisplayLength'], $order, $_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $searchEstado, $_GET['sSearch_9'], $_GET['sSearch_10']);
        $iTotal = $this->get_cuotascount($_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $searchEstado, $_GET['sSearch_9'], $_GET['sSearch_10']);
        $iMonto = $this->get_cuotassum($_GET['sSearch'], $_GET['sSearch_1'], $_GET['sSearch_2'], $_GET['sSearch_4'], $_GET['sSearch_5'], $_GET['sSearch_6'], $_GET['sSearch_7'], $searchEstado, $_GET['sSearch_9'], $_GET['sSearch_10']);



        foreach($cuotas as $cuota){
            //print_r($cuota);
            $ordenes=$cuota['Plan']['ordenes'];
            $ordenesArray = explode(',', $ordenes);
            sort($ordenesArray);
            /*switch ($cuota['Plan']['tipo']) {
                case 'Gastos y compras':
                    $this->loadModel('Gasto');
                    $gastos = $this->Gasto->find('all',array('order' => 'nro_orden ASC','conditions' => array('plan_id' => $cuota['Plan']['id'])));

                    //foreach ($gastos as $gasto) {
                        $ordenes .=$gastos[0]['Gasto']['nro_orden'];
                    //}
                break;
                case 'Impuestos, tasas y cargas sociales':
                    $this->loadModel('Compra');
                    $compras = $this->Compra->find('all',array('order' => 'nro_orden ASC','conditions' => array('plan_id' => $cuota['Plan']['id'])));

                    //foreach ($compras as $compra) {
                        $ordenes .=$compras[0]['Compra']['nro_orden'];
                    //}
                break;


                default:
                    $this->loadModel('CuentaPagar');
                    $cuentasPagar = $this->CuentaPagar->find('all',array('order' => 'operacion_id ASC','conditions' => array('plan_id' => $cuota['Plan']['id'])));

                    //foreach ($cuentasPagar as $cuentaPagar) {
                        switch ($cuentasPagar[0]['CuentaPagar']['operacion_tipo']) {
                            case 'gasto':
                                $this->loadModel('Gasto');
                                 $this->Gasto->id = $cuentasPagar[0]['CuentaPagar']['operacion_id'];
                                 $gasto = $this->Gasto->read();
                                 $ordenes .=$gasto['Gasto']['nro_orden'];
                            break;

                            default:
                                $this->loadModel('Compra');
                                 $this->Compra->id = $cuentasPagar[0]['CuentaPagar']['operacion_id'];
                                 $compra = $this->Compra->read();
                                 $ordenes .=$compra['Compra']['nro_orden'];
                            break;
                        }

                    //}
                break;
            }*/
            //estado y nro de orden

            if($cuota['CuotaPlan']['estado']==0){
                $estado = 'Pendiente de pago';
                $segundos= strtotime('now')-strtotime($cuota['CuotaPlan']['vencimiento']);
            }
            elseif($cuota['CuotaPlan']['estado']==2){
                $estado = 'Refinanciada';
                $segundos= strtotime('now')-strtotime($cuota['CuotaPlan']['vencimiento']);
            }
            else{
                $estado = 'Pagada';
                $segundos= strtotime($cuota['CuotaPlan']['vencimiento'])-strtotime($cuota['CuotaPlan']['vencimiento']);
            }

            $cantidad_dias=intval($segundos/60/60/24);
            // echo round($iMonto[0]['total'],2).' / ';
            //echo $cuota['Plan']['Rubro']['rubro'].' - '.$cuota['Subrubro']['subrubro'].'/';
            $rows[] = array(
                $cuota['CuotaPlan']['id'],
                $cuota['Plan']['plan'],
                $cuota['Plan']['tipo'],
                $ordenesArray[0],
                $cuota['Plan']['proveedor'],
                $cuota['Plan']['Rubro']['rubro'],
                $cuota['Plan']['Subrubro']['subrubro'],


                $cuota['CuotaPlan']['monto'],
                $estado,
                $cuota['CuotaPlan']['vencimiento'],

                $cuota['Plan']['Usuario']['nombre'].','.$cuota['Plan']['Usuario']['apellido'],
                round($iMonto[0]['total'],2)
            );
        }
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => count($rows),
            "iTotalDisplayRecords" => $iTotal,
            "aaData" => array()
        );

        $output['aaData'] = $rows;
        $this->set('aoData',$output);
        //print_r($output);
        $this->set('_serialize',
            'aoData'
        );
    }


    //protege el controlador solo para usuarios
    public function beforeFilter(){
        $usuario_accion = '';
        if(isset($_COOKIE['userid'])){
            $this->loadModel('Usuario');
            $this->set('usuario',$this->Usuario->findById($_COOKIE['userid']));

            $this->loadModel('UsuarioPermiso');
            $permisos = $this->UsuarioPermiso->findAllByUsuarioId($_COOKIE['userid']);
            $accion = array();
            foreach($permisos as $permiso){
                $usuario_accion[$permiso['UsuarioPermiso']['permiso_id']] = true;
            }
            $this->set('usuario_accion',$usuario_accion);
        }else{
            $this->redirect('/index');
        }
    }


    /* Cache query */
    public function get_cuotas($offset, $limit, $order, $search, $search1, $search2, $search4, $search5, $search6, $search7, $search8, $search9, $search10) {

        $result = Cache::read('get_cuotass', 'long');
        if (!$result) {
            $condicionSearch1 = ($search1)?array('Plan.plan LIKE '=>'%'.$search1.'%'):array();
            $condicionSearch2 = ($search2)?array('Plan.tipo LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
            $condicionSearch4 = ($search4)?array('Plan.proveedor LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();

            $condicionSearch5 = ($search5)?array('Plan.rubro_id = '=>$search5):array();
            $condicionSearch6 = ($search6)?array('Plan.subrubro_id = '=>$search6):array();
            $condicionSearch7 = ($search7)?array('CuotaPlan.monto LIKE '=>'%'.$search7.'%'):array();

            switch ($search8) {
                case 'Pendiente':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>0);
                    break;
                case 'Pagada':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>1);
                    break;
                case 'Refinanciada':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>2);
                    break;
                default:
                    $condicionSearch8 = array();
                    break;
            }
            $condicionSearch9 = ($search9)?array('CuotaPlan.vencimiento LIKE '=> '%'.$this->dateFormatSQL($search9).'%'):array();
            $condicionSearch10 = ($search10)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search10.'%', 'Usuario.apellido LIKE '=>'%'.$search10.'%')):array();
            $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch9,$condicionSearch10,
                'or' =>
                    array('Plan.plan LIKE '=>'%'.$search.'%', 'Plan.tipo LIKE '=>'%'.$search.'%', 'CuotaPlan.vencimiento LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'CuotaPlan.monto LIKE '=>'%'.$search.'%'
                    ));
            $result = $this->CuotaPlan->find('all',array('order' => $order, 'conditions' => $condicion, 'limit'=>$limit, 'offset'=>$offset,'recursive' => 2));

            Cache::write('get_cuotas', $result, 'long');

        }

        return $result;
    }


    public function get_cuotascount($search, $search1, $search2, $search4, $search5, $search6, $search7, $search8, $search9, $search10) {
        $result = Cache::read('get_cuotascount', 'long');
        if (!$result) {
            $condicionSearch1 = ($search1)?array('Plan.plan LIKE '=>'%'.$search1.'%'):array();
            $condicionSearch2 = ($search2)?array('Plan.tipo LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
            $condicionSearch4 = ($search4)?array('Plan.proveedor LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();

            $condicionSearch5 = ($search5)?array('Plan.rubro_id = '=>$search5):array();
            $condicionSearch6 = ($search6)?array('Plan.subrubro_id = '=>$search6):array();
            $condicionSearch7 = ($search7)?array('CuotaPlan.monto LIKE '=>'%'.$search7.'%'):array();

            switch ($search8) {
                case 'Pendiente':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>0);
                    break;
                case 'Pagada':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>1);
                    break;
                case 'Refinanciada':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>2);
                    break;
                default:
                    $condicionSearch8 = array();
                    break;
            }
            $condicionSearch9 = ($search9)?array('CuotaPlan.vencimiento LIKE '=> '%'.$this->dateFormatSQL($search9).'%'):array();
            $condicionSearch10 = ($search10)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search10.'%', 'Usuario.apellido LIKE '=>'%'.$search10.'%')):array();
            $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch9,$condicionSearch10,
                'or' =>
                    array('Plan.plan LIKE '=>'%'.$search.'%', 'Plan.tipo LIKE '=>'%'.$search.'%', 'CuotaPlan.vencimiento LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'CuotaPlan.monto LIKE '=>'%'.$search.'%'
                    ));
            $result = $this->CuotaPlan->find('count',array('conditions' => $condicion,'recursive' => 2));
            /*App::uses('ConnectionManager', 'Model');
        	$dbo = ConnectionManager::getDatasource('default');
		    $logs = $dbo->getLog();
		    $lastLog = end($logs['log']);

		    echo $lastLog['query'];*/
            Cache::write('get_cuotascount', $result, 'long');

        }

        return $result;
    }

    public function get_cuotassum($search, $search1, $search2, $search4, $search5, $search6, $search7, $search8, $search9, $search10) {
        $result = Cache::read('get_gastossum', 'long');
        if (!$result) {
            $condicionSearch1 = ($search1)?array('Plan.plan LIKE '=>'%'.$search1.'%'):array();
            $condicionSearch2 = ($search2)?array('Plan.tipo LIKE '=> '%'.$this->dateFormatSQL($search2).'%'):array();
            $condicionSearch4 = ($search4)?array('Plan.proveedor LIKE '=> '%'.$this->dateFormatSQL($search4).'%'):array();

            $condicionSearch5 = ($search5)?array('Plan.rubro_id = '=>$search5):array();
            $condicionSearch6 = ($search6)?array('Plan.subrubro_id = '=>$search6):array();
            $condicionSearch7 = ($search7)?array('CuotaPlan.monto LIKE '=>'%'.$search7.'%'):array();

            switch ($search8) {
                case 'Pendiente':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>0);
                    break;
                case 'Pagada':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>1);
                    break;
                case 'Refinanciada':
                    $condicionSearch8 = array('CuotaPlan.estado = '=>2);
                    break;
                default:
                    $condicionSearch8 = array();
                    break;
            }
            $condicionSearch9 = ($search9)?array('CuotaPlan.vencimiento LIKE '=> '%'.$this->dateFormatSQL($search9).'%'):array();
            $condicionSearch10 = ($search10)?array('or' => array('Usuario.nombre LIKE '=>'%'.$search10.'%', 'Usuario.apellido LIKE '=>'%'.$search10.'%')):array();
            $condicion=array($condicionSearch1,$condicionSearch2,$condicionSearch4,$condicionSearch5,$condicionSearch6,$condicionSearch7,$condicionSearch8,$condicionSearch9,$condicionSearch10,
                'or' =>
                    array('Plan.plan LIKE '=>'%'.$search.'%', 'Plan.tipo LIKE '=>'%'.$search.'%', 'CuotaPlan.vencimiento LIKE '=>'%'.$this->dateFormatSQL($search).'%', 'CuotaPlan.monto LIKE '=>'%'.$search.'%'
                    ));
            $result = $this->CuotaPlan->find('first',array('fields'=>'SUM(CuotaPlan.monto) as total','conditions'=> $condicion,'recursive' => 2));


            Cache::write('get_cuotassum', $result, 'long');

        }

        return $result;
    }

}
?>
