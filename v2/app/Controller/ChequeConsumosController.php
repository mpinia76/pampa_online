<?php
ini_set('memory_limit', '-1');
session_start();

class ChequeConsumosController extends AppController {
    public $scaffold;

    public function dateFormatSQL($dateString) {
        $date_parts = explode("/",$dateString);
        return $date_parts[2]."-".$date_parts[1]."-".$date_parts[0];
    }

    public function index(){
        $this->layout = 'index';
        $_SESSION['desde'] = '';
        $_SESSION['hasta'] = '';

        if (isset($this->data['desde'])) {
            $_SESSION['desde'] = $this->data['desde'];
        }
        if (isset($this->data['hasta'])) {
            $_SESSION['hasta'] = $this->data['hasta'];
        }
        $this->setLogUsuario('Cheques a debitar');
        $this->loadModel('Usuario');
        $user_id = $_SESSION['userid'];
        $user = $this->Usuario->find('first',array('conditions'=>array('Usuario.id'=>$_SESSION['userid'])));

        $permisoEditar=1;
        $permisoAgregar=1;
        $permisoAprobar=1;
        $permisoReemplazar=1;
        if ($user['Usuario']['admin'] != '1'){
            $this->loadModel('UsuarioPermiso');
            $permisos = $this->UsuarioPermiso->findAllByUsuarioId($user_id);
            $permisoEditar=0;
            $permisoAgregar=0;
            $permisoAprobar=0;
            $permisoReemplazar=0;
            foreach($permisos as $permiso){
                if ($permiso['UsuarioPermiso']['permiso_id']==37) {
                    $permisoEditar=1;
                    continue;
                }
                if ($permiso['UsuarioPermiso']['permiso_id']==49) {
                    $permisoAgregar=1;
                    continue;
                }
                if ($permiso['UsuarioPermiso']['permiso_id']==146) {
                    $permisoAprobar=1;
                    continue;
                }
                if ($permiso['UsuarioPermiso']['permiso_id']==145) {
                    $permisoReemplazar=1;
                    continue;
                }
            }
        }
        $this->set('permisoEditar',$permisoEditar);
        $this->set('permisoAgregar',$permisoAgregar);
        $this->set('permisoAprobar',$permisoAprobar);
        $this->set('permisoReemplazar',$permisoReemplazar);
    }
    public function dataTable2($estado = ''){

        $desde = $_SESSION['desde'];
        $hasta = $_SESSION['hasta'];
        switch ($_GET['iSortCol_0']) {
            case 1:
                $orderField='ChequeConsumo.fecha';
                break;
            case 2:
                $orderField='ChequeConsumo.fecha_debitado';
                break;
            case 3:
                $orderField='ChequeConsumo.mes';
                break;
            case 4:
                $orderField='Cuenta.sucursal';
                break;
            case 5:
                $orderField='Cuenta.nombre';
                break;
            case 6:
                $orderField='Chequera.numero';
                break;
            case 7:
                $orderField='ChequeConsumo.numero';
                break;
            case 8:
                $orderField='ChequeConsumo.titular';
                break;

            default:
                $orderField='ChequeConsumo.fecha';
                break;
        }


        $orderType= ($_GET['sSortDir_0'])? $_GET['sSortDir_0']:'DESC';
        $rows = array();


        $estado = explode('_', $_GET['sSearch']);

        $search = (($estado[1])&&($estado[0]=='EST'))?'':$_GET['sSearch'];

        if ($search!='') {
            if ($search==':-:-@') {
                $search='';
            }
            elseif ($search==':-:-@@@') {
                $search='';
                $_SESSION["chequeConsumoSearchEstado"]='EST_3';
            }
            //$_SESSION["chequeConsumoSearch"]=$search;
        }

        if ($_GET['sSearch_2']!='') {
            if ($_GET['sSearch_2']==':-:-@') {
                $_GET['sSearch_2']='';
            }
            elseif ($_GET['sSearch_2']==':-:-@@@') {
                $_GET['sSearch_2']='';

            }
            $_SESSION["chequeConsumoFechaSearch"]=$_GET['sSearch_2'];
        }
        if ($_GET['sSearch_3']!='') {
            if ($_GET['sSearch_3']==':-:-@') {
                $_GET['sSearch_3']='';
            }
            elseif ($_GET['sSearch_3']==':-:-@@@') {
                $_GET['sSearch_3']='';

            }
            $_SESSION["chequeConsumoFechaDebitadoSearch"]=$_GET['sSearch_3'];
        }
        if ($_GET['sSearch_4']!='') {
            if ($_GET['sSearch_4']==':-:-@') {
                $_GET['sSearch_4']='';
            }
            elseif ($_GET['sSearch_4']==':-:-@@@') {
                $_GET['sSearch_4']='';

            }
            $_SESSION["chequeConsumoMesSearch"]=$_GET['sSearch_4'];
        }
        if ($_GET['sSearch_5']!='') {
            if ($_GET['sSearch_5']==':-:-@') {
                $_GET['sSearch_5']='';
            }
            elseif ($_GET['sSearch_5']==':-:-@@@') {
                $_GET['sSearch_5']='';

            }
            $_SESSION["chequeConsumoBancoSearch"]=$_GET['sSearch_5'];
        }
        if ($_GET['sSearch_6']!='') {
            if ($_GET['sSearch_6']==':-:-@') {
                $_GET['sSearch_6']='';
            }
            elseif ($_GET['sSearch_6']==':-:-@@@') {
                $_GET['sSearch_6']='';

            }
            $_SESSION["chequeConsumoCuentaSearch"]=$_GET['sSearch_6'];
        }
        if ($_GET['sSearch_7']!='') {
            if ($_GET['sSearch_7']==':-:-@') {
                $_GET['sSearch_7']='';
            }
            elseif ($_GET['sSearch_7']==':-:-@@@') {
                $_GET['sSearch_7']='';

            }
            $_SESSION["chequeConsumoChequeraSearch"]=$_GET['sSearch_7'];
        }
        if ($_GET['sSearch_8']!='') {
            if ($_GET['sSearch_8']==':-:-@') {
                $_GET['sSearch_8']='';
            }
            elseif ($_GET['sSearch_8']==':-:-@@@') {
                $_GET['sSearch_8']='';

            }
            $_SESSION["chequeConsumoNumeroSearch"]=$_GET['sSearch_8'];
        }
        if ($_GET['sSearch_9']!='') {
            if ($_GET['sSearch_9']==':-:-@') {
                $_GET['sSearch_9']='';
            }
            elseif ($_GET['sSearch_9']==':-:-@@@') {
                $_GET['sSearch_9']='';

            }
            $_SESSION["chequeConsumoTitularSearch"]=$_GET['sSearch_9'];
        }
        if ($_GET['sSearch_10']!='') {
            if ($_GET['sSearch_10']==':-:-@') {
                $_GET['sSearch_10']='';
            }
            elseif ($_GET['sSearch_10']==':-:-@@@') {
                $_GET['sSearch_10']='';

            }
            $_SESSION["chequeConsumoConceptoSearch"]=$_GET['sSearch_10'];
        }
        if ($_GET['sSearch_11']!='') {
            if ($_GET['sSearch_11']==':-:-@') {
                $_GET['sSearch_11']='';
            }
            elseif ($_GET['sSearch_11']==':-:-@@@') {
                $_GET['sSearch_11']='';

            }
            $_SESSION["chequeConsumoMontoSearch"]=$_GET['sSearch_11'];
        }
        if(($estado[1])&&($estado[0]=='EST')){

            switch ($estado[1]) {
                case 1:
                    $filtroEstado='1=1';
                    $_SESSION["chequeConsumoSearchEstado"]='EST_1';
                    break;
                case 2:
                    $filtroEstado=array('CobroTarjetaLote.id' => null, array('OR' => array(array('ChequeConsumo.concepto' => null),'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado')));
                    $_SESSION["chequeConsumoSearchEstado"]='EST_2';
                    break;
                case 3:

                    $filtroEstado=array('ChequeConsumo.debitado' => 0, array('OR' => array(array('ChequeConsumo.concepto' => null),'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado')));

                    $_SESSION["chequeConsumoSearchEstado"]='EST_3';
                    break;
                case 4:
                    $filtroEstado=array('ChequeConsumo.debitado' => 1, 'ChequeConsumo.vencido' => 0, array('OR' => array(array('ChequeConsumo.concepto' => null),'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado')));
                    $_SESSION["chequeConsumoSearchEstado"]='EST_4';
                    break;
                case 5:
                    $filtroEstado=array('ChequeConsumo.vencido' => 1);
                    $_SESSION["chequeConsumoSearchEstado"]='EST_5';
                    break;
                case 6:
                    $filtroEstado=array('ChequeConsumo.concepto LIKE '=>'%Anulado%');
                    $_SESSION["chequeConsumoSearchEstado"]='EST_6';
                    break;
                case 7:
                    $filtroEstado=array('ChequeConsumo.concepto LIKE '=>'%Extraviado%');
                    $_SESSION["chequeConsumoSearchEstado"]='EST_7';
                    break;
                case 8:
                    $filtroEstado=array('ChequeConsumo.concepto LIKE '=>'%Reemplazado%');
                    $_SESSION["chequeConsumoSearchEstado"]='EST_8';
                    break;

            }
        }
        elseif(!$estado[1]&&!$_SESSION["chequeConsumoSearchEstado"]){
            $filtroEstado=array('ChequeConsumo.debitado' => 0, array('OR' => array(array('ChequeConsumo.concepto' => null),'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado')));

            $_SESSION["chequeConsumoSearchEstado"]='EST_3';

        }
        elseif(!$_SESSION["chequeConsumoSearchEstado"]){
            $filtroEstado=array('ChequeConsumo.debitado' => 0, array('OR' => array(array('ChequeConsumo.concepto' => null),'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado')));

            $_SESSION["chequeConsumoSearchEstado"]='EST_3';
        }
        else{
            switch ($_SESSION["chequeConsumoSearchEstado"]) {
                case 'EST_1':
                    $filtroEstado='1=1';

                    break;
                case 'EST_2':
                    //$filtroEstado=array('CobroTarjetaLote.id' => null, 'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado');
                    $filtroEstado=array('CobroTarjetaLote.id' => null, array('OR' => array(array('ChequeConsumo.concepto' => null),'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado')));

                    break;
                case 'EST_3':
                    //$filtroEstado=array('ChequeConsumo.debitado' => 0, 'ChequeConsumo.vencido' => 0, 'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado');
                    $filtroEstado=array('ChequeConsumo.debitado' => 0, array('OR' => array(array('ChequeConsumo.concepto' => null),'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado')));
                    break;
                case 'EST_4':
                    //$filtroEstado=array('ChequeConsumo.debitado' => 1, 'ChequeConsumo.vencido' => 0, 'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado');
                    $filtroEstado=array('ChequeConsumo.debitado' => 1, 'ChequeConsumo.vencido' => 0, array('OR' => array(array('ChequeConsumo.concepto' => null),'ChequeConsumo.concepto NOT REGEXP'=>'Anulado|Extraviado|Reemplazado')));
                    break;
                case 5:
                    $filtroEstado=array('ChequeConsumo.vencido' => 1);
                    $_SESSION["chequeConsumoSearchEstado"]='EST_5';
                    break;
                case 6:
                    $filtroEstado=array('ChequeConsumo.concepto LIKE '=>'%Anulado%');
                    $_SESSION["chequeConsumoSearchEstado"]='EST_6';
                    break;
                case 7:
                    $filtroEstado=array('ChequeConsumo.concepto LIKE '=>'%Extraviado%');
                    $_SESSION["chequeConsumoSearchEstado"]='EST_7';
                    break;
                case 8:
                    $filtroEstado=array('ChequeConsumo.concepto LIKE '=>'%Reemplazado%');
                    $_SESSION["chequeConsumoSearchEstado"]='EST_8';
                    break;
            }

        }

        $date_parts = explode("/",$_SESSION["chequeConsumoFechaSearch"]);
        switch (count($date_parts)) {
            case 1:
                $fecha = $date_parts[0];
                break;

            case 2:
                $fecha = $date_parts[1].'-'.$date_parts[0];
                break;
            case 3:
                $fecha = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
                break;
        }
        $filtroFecha=($_SESSION["chequeConsumoFechaSearch"])?array('ChequeConsumo.fecha LIKE '=>'%'.$fecha.'%'):array(1=>1);

        $date_parts = explode("/",$_SESSION["chequeConsumoFechaDebitadoSearch"]);
        switch (count($date_parts)) {
            case 1:
                $fechaDebitado = $date_parts[0];
                break;

            case 2:
                $fechaDebitado = $date_parts[1].'-'.$date_parts[0];
                break;
            case 3:
                $fechaDebitado = $date_parts[2].'-'.$date_parts[1].'-'.$date_parts[0];
                break;
        }
        $filtroFechaDebitadoPago=($_SESSION["chequeConsumoFechaDebitadoSearch"])?array('ChequeConsumo.fecha_debitado LIKE '=>'%'.$fechaDebitado.'%'):array(1=>1);




        $filtroMes=($_SESSION["chequeConsumoMesSearch"])?array('MONTH(ChequeConsumo.fecha)'=>$_SESSION["chequeConsumoMesSearch"]):array(1=>1);
        $filtroBanco=($_SESSION["chequeConsumoBancoSearch"])?array('Banco.banco LIKE '=>'%'.$_SESSION["chequeConsumoBancoSearch"].'%'):array(1=>1);

        $filtroCuenta=($_SESSION["chequeConsumoCuentaSearch"])?array('Cuenta.nombre LIKE '=>'%'.$_SESSION["chequeConsumoCuentaSearch"].'%'):array(1=>1);
        $filtroChequera=($_SESSION["chequeConsumoChequeraSearch"])?array('Chequera.numero LIKE '=>'%'.$_SESSION["chequeConsumoChequeraSearch"].'%'):array(1=>1);
        $filtroNumero=($_SESSION["chequeConsumoNumeroSearch"])?array('LPAD(ChequeConsumo.numero,8,\'0\') LIKE '=>'%'.$_SESSION["chequeConsumoNumeroSearch"].'%'):array(1=>1);
        $filtroTitular=($_SESSION["chequeConsumoTitularSearch"])?array('ChequeConsumo.titular LIKE '=>'%'.$_SESSION["chequeConsumoTitularSearch"].'%'):array(1=>1);

        $filtroMonto=($_SESSION["chequeConsumoMontoSearch"])?array('ChequeConsumo.monto LIKE '=>'%'.$_SESSION["chequeConsumoMontoSearch"].'%'):array(1=>1);


        $filtroConcepto=($_SESSION["chequeConsumoConceptoSearch"])?"(Gasto.nro_orden LIKE '%".$_SESSION["chequeConsumoConceptoSearch"]."%' OR Compra.nro_orden LIKE '%".$_SESSION["chequeConsumoConceptoSearch"]."%' OR Reservas.numero LIKE '%".$_SESSION["chequeConsumoConceptoSearch"]."%' OR ChequeConsumo.concepto LIKE '%".$_SESSION["chequeConsumoConceptoSearch"]."%')":array(1=>1);


        //echo $filtroConcepto;


        $order = $orderField.' '.$orderType;
        $fechas='';
        if (($desde!='')&&($hasta!='')) {

            $filtroFechas = "(ChequeConsumo.fecha >= '".$this->dateFormatSQL($desde)."' AND ChequeConsumo.fecha <= '".$this->dateFormatSQL($hasta)."')";



            //$fechas= "(Reserva.creado between ? and ?' => array($this->dateFormatSQL($desde), $this->dateFormatSQL($hasta))
        }
        else{
            $filtroFechas =array(1=>1);
        }
        $FiltroFijo='';//'RelPagoOperacion.forma_pago = \'cheque\' AND (RelPagoOperacion.operacion_tipo = \'gasto\' OR RelPagoOperacion.operacion_tipo = \'compra\' )';
        $condicion=array($filtroEstado,$filtroFecha,$filtroFechaDebitadoPago,$filtroMes,$filtroBanco,$filtroCuenta,$filtroChequera,$filtroNumero,$filtroTitular,$filtroMonto
        ,$filtroConcepto,$filtroFechas,$FiltroFijo);
        $cheques = $this->ChequeConsumo->find('all',array('joins' => array(
            array(
                'table' => 'cuenta',
                'alias' => 'Cuenta',
                'type' => 'INNER',
                'conditions' => array(
                    'Cuenta.id = ChequeConsumo.cuenta_id'
                )
            ),
            array(
                'table' => 'banco',
                'alias' => 'Banco',
                'type' => 'INNER',
                'conditions' => array(
                    'Banco.id = Cuenta.banco_id'
                )
            ),
            array(
                'table' => 'cuenta_tipo',
                'alias' => 'CuentaTipo',
                'type' => 'INNER',
                'conditions' => array(
                    'CuentaTipo.id = Cuenta.cuenta_tipo_id'
                )
            ),
            array(
                'table' => 'rel_pago_operacion',
                'alias' => 'RelPagoOperacion',
                'type' => 'LEFT',
                'conditions' => array(
                    'ChequeConsumo.id = RelPagoOperacion.forma_pago_id','RelPagoOperacion.forma_pago = \'cheque\''
                )
            ),
            array(
                'table' => 'gasto',
                'alias' => 'Gasto',
                'type' => 'LEFT',
                'conditions' => array(
                    'Gasto.id = RelPagoOperacion.operacion_id','RelPagoOperacion.operacion_tipo = \'gasto\''
                )
            ),
            array(
                'table' => 'compra',
                'alias' => 'Compra',
                'type' => 'LEFT',
                'conditions' => array(
                    'Compra.id = RelPagoOperacion.operacion_id ','RelPagoOperacion.operacion_tipo = \'compra\' '
                )
            ),
            array(
                'table' => 'cuota_plans',
                'alias' => 'CuotaPlans',
                'type' => 'LEFT',
                'conditions' => array(
                    'CuotaPlans.id = RelPagoOperacion.operacion_id ','RelPagoOperacion.operacion_tipo = \'cuota_plan\' '
                )
            ),
            array(
                'table' => 'plans',
                'alias' => 'Plans',
                'type' => 'LEFT',
                'conditions' => array(
                    'Plans.id = CuotaPlans.plan_id'
                )
            ),
            array(
                'table' => 'reserva_devoluciones',
                'alias' => 'ReservaDevoluciones',
                'type' => 'LEFT',
                'conditions' => array(
                    'ReservaDevoluciones.id = RelPagoOperacion.operacion_id'
                )
            ),
            array(
                'table' => 'reservas',
                'alias' => 'Reservas',
                'type' => 'LEFT',
                'conditions' => array(
                    'Reservas.id = ReservaDevoluciones.reserva_id'
                )
            ),
            array(
                'table' => 'chequeras',
                'alias' => 'Chequera',
                'type' => 'LEFT',
                'conditions' => array(
                    'Chequera.id = ChequeConsumo.chequera_id'
                )
            )


        ),'fields'=>array(' Reservas.numero as reserva_numero, Chequera.numero as chequera_numero, Gasto.nro_orden as gasto_orden, Compra.nro_orden as compra_orden,ChequeConsumo.*,sum(ChequeConsumo.monto) as suma_monto,Banco.banco,Cuenta.sucursal,Cuenta.nombre,MONTH(ChequeConsumo.fecha) as mes,CuotaPlans.id as id_cuotaPlan, Plans.plan, CuotaPlans.vencimiento '), 'group' => array('ChequeConsumo.id'), 'conditions' => $condicion,'order' => $order, 'offset'=>$_GET['iDisplayStart'], 'limit'=>$_GET['iDisplayLength'], 'recursive' => -1));
        //print_r($filtroEstado);
        /*App::uses('ConnectionManager', 'Model');
                 $dbo = ConnectionManager::getDatasource('default');
                 $logs = $dbo->getLog();
                 $lastLog = $logs['log'][0];
                 echo $lastLog['query'];  */

        $filtroCuenta=($_SESSION["chequeConsumoCuentaSearch"])?array('Cuenta1.nombre LIKE '=>'%'.$_SESSION["chequeConsumoCuentaSearch"].'%'):array(1=>1);
        $condicion=array($filtroEstado,$filtroFecha,$filtroFechaDebitadoPago,$filtroMes,$filtroBanco,$filtroCuenta,$filtroChequera,$filtroNumero,$filtroTitular,$filtroMonto
        ,$filtroConcepto,$filtroFechas,$FiltroFijo);


        $contarCheques = $this->ChequeConsumo->find('count',array('joins' => array(
            array(
                'table' => 'cuenta',
                'alias' => 'Cuenta1',
                'type' => 'INNER',
                'conditions' => array(
                    'Cuenta1.id = ChequeConsumo.cuenta_id'
                )
            ),
            array(
                'table' => 'banco',
                'alias' => 'Banco',
                'type' => 'INNER',
                'conditions' => array(
                    'Banco.id = Cuenta1.banco_id'
                )
            ),
            array(
                'table' => 'cuenta_tipo',
                'alias' => 'CuentaTipo',
                'type' => 'INNER',
                'conditions' => array(
                    'CuentaTipo.id = Cuenta1.cuenta_tipo_id'
                )
            ),
            array(
                'table' => 'rel_pago_operacion',
                'alias' => 'RelPagoOperacion',
                'type' => 'LEFT',
                'conditions' => array(
                    'ChequeConsumo.id = RelPagoOperacion.forma_pago_id','RelPagoOperacion.forma_pago = \'cheque\''
                )
            ),
            array(
                'table' => 'gasto',
                'alias' => 'Gasto',
                'type' => 'LEFT',
                'conditions' => array(
                    'Gasto.id = RelPagoOperacion.operacion_id','RelPagoOperacion.operacion_tipo = \'gasto\''
                )
            ),
            array(
                'table' => 'compra',
                'alias' => 'Compra',
                'type' => 'LEFT',
                'conditions' => array(
                    'Compra.id = RelPagoOperacion.operacion_id ','RelPagoOperacion.operacion_tipo = \'compra\' '
                )
            ),
            array(
                'table' => 'cuota_plans',
                'alias' => 'CuotaPlans',
                'type' => 'LEFT',
                'conditions' => array(
                    'CuotaPlans.id = RelPagoOperacion.operacion_id ','RelPagoOperacion.operacion_tipo = \'cuota_plan\' '
                )
            ),
            array(
                'table' => 'plans',
                'alias' => 'Plans',
                'type' => 'LEFT',
                'conditions' => array(
                    'Plans.id = CuotaPlans.plan_id'
                )
            ),
            array(
                'table' => 'reserva_devoluciones',
                'alias' => 'ReservaDevoluciones',
                'type' => 'LEFT',
                'conditions' => array(
                    'ReservaDevoluciones.id = RelPagoOperacion.operacion_id'
                )
            ),
            array(
                'table' => 'reservas',
                'alias' => 'Reservas',
                'type' => 'LEFT',
                'conditions' => array(
                    'Reservas.id = ReservaDevoluciones.reserva_id'
                )
            ),
            array(
                'table' => 'chequeras',
                'alias' => 'Chequera',
                'type' => 'LEFT',
                'conditions' => array(
                    'Chequera.id = ChequeConsumo.chequera_id'
                )
            )

        ), 'group' => array('ChequeConsumo.id'), 'conditions' => $condicion));

        /*$iMonto = $this->ChequeConsumo->find('first',array('joins' => array(
            array(
                'table' => 'cuenta',
                'alias' => 'Cuenta1',
                'type' => 'INNER',
                'conditions' => array(
                    'Cuenta1.id = ChequeConsumo.cuenta_id'
                )
            ),
            array(
                'table' => 'banco',
                'alias' => 'Banco',
                'type' => 'INNER',
                'conditions' => array(
                    'Banco.id = Cuenta1.banco_id'
                )
            ),
            array(
                'table' => 'cuenta_tipo',
                'alias' => 'CuentaTipo',
                'type' => 'INNER',
                'conditions' => array(
                    'CuentaTipo.id = Cuenta1.cuenta_tipo_id'
                )
            ),
             array(
                'table' => 'rel_pago_operacion',
                'alias' => 'RelPagoOperacion',
                'type' => 'LEFT',
                'conditions' => array(
                    'ChequeConsumo.id = RelPagoOperacion.forma_pago_id','RelPagoOperacion.forma_pago = \'cheque\''
                )
            ),
           array(
                'table' => 'gasto',
                'alias' => 'Gasto',
                'type' => 'LEFT',
                'conditions' => array(
                    'Gasto.id = RelPagoOperacion.operacion_id','RelPagoOperacion.operacion_tipo = \'gasto\''
                )
            ),
            array(
                'table' => 'compra',
                'alias' => 'Compra',
                'type' => 'LEFT',
                'conditions' => array(
                    'Compra.id = RelPagoOperacion.operacion_id ','RelPagoOperacion.operacion_tipo = \'compra\' '
                )
            ),
            array(
                'table' => 'reserva_devoluciones',
                'alias' => 'ReservaDevoluciones',
                'type' => 'LEFT',
                'conditions' => array(
                    'ReservaDevoluciones.id = RelPagoOperacion.operacion_id'
                )
            ),
            array(
                'table' => 'reservas',
                'alias' => 'Reservas',
                'type' => 'LEFT',
                'conditions' => array(
                    'Reservas.id = ReservaDevoluciones.reserva_id'
                )
            )

        ),'fields'=>array('SUM(ChequeConsumo.monto) as total'), 'conditions' => $condicion));*/



        //print_r($cheques);
        foreach($cheques as $cheque){
            //print_r($cheque);
            $concepto = '';
            $estado	= '';

            //$monto		= $cheque[0]['suma_monto'];
            $monto = $cheque['ChequeConsumo']['monto'];
            if($cheque['ChequeConsumo']['debitado'] == 1){
                $estado = 'Debitado';
            }else{
                $estado = 'Pendiente';
            }
            if($cheque['ChequeConsumo']['vencido'] == 1){
                $estado = 'Vencido';
            }
            else{
                if (strpos($cheque['ChequeConsumo']['concepto'], 'Anulado') !== false) {
                    $estado = 'Anulado';
                }
                if (strpos($cheque['ChequeConsumo']['concepto'], 'Extraviado') !== false) {
                    $estado = 'Extraviado';
                }
                if (strpos($cheque['ChequeConsumo']['concepto'], 'Reemplazado') !== false) {
                    $estado = 'Reemplazado';
                }
            }
            if($cheque['Gasto']['gasto_orden'] != ''){
                $gasto_buscar = $this->ChequeConsumo->find('first',array('joins' => array(

                    array(
                        'table' => 'rel_pago_operacion',
                        'alias' => 'RelPagoOperacion',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ChequeConsumo.id = RelPagoOperacion.forma_pago_id','RelPagoOperacion.forma_pago = \'cheque\''
                        )
                    ),
                    array(
                        'table' => 'gasto',
                        'alias' => 'Gasto',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Gasto.id = RelPagoOperacion.operacion_id','RelPagoOperacion.operacion_tipo = \'gasto\' '
                        )
                    )

                ), 'conditions' => array('Gasto.nro_orden '=>$cheque['Gasto']['gasto_orden'])));

                //print_r($gasto_buscar);
                if ($gasto_buscar['ChequeConsumo']['id']) {
                    $concepto = "Gasto - ".$cheque['Gasto']['gasto_orden'];
                }

            }
            if($cheque['Compra']['compra_orden'] != ''){
                $compra_buscar = $this->ChequeConsumo->find('first',array('joins' => array(

                    array(
                        'table' => 'rel_pago_operacion',
                        'alias' => 'RelPagoOperacion',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ChequeConsumo.id = RelPagoOperacion.forma_pago_id','RelPagoOperacion.forma_pago = \'cheque\''
                        )
                    ),
                    array(
                        'table' => 'compra',
                        'alias' => 'Compra',
                        'type' => 'INNER',
                        'conditions' => array(
                            'Compra.id = RelPagoOperacion.operacion_id','RelPagoOperacion.operacion_tipo = \'compra\' '
                        )
                    )

                ), 'conditions' => array('Compra.nro_orden '=>$cheque['Compra']['compra_orden'])));

                //print_r($gasto_buscar);
                if ($compra_buscar['ChequeConsumo']['id']) {


                    $concepto = "Impuestos, tasas y cargas sociales - ".$cheque['Compra']['compra_orden'];
                }
            }
            if($cheque['CuotaPlans']['id_cuotaPlan'] != ''){
                $cuota_buscar = $this->ChequeConsumo->find('first',array('joins' => array(

                    array(
                        'table' => 'rel_pago_operacion',
                        'alias' => 'RelPagoOperacion',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ChequeConsumo.id = RelPagoOperacion.forma_pago_id','RelPagoOperacion.forma_pago = \'cheque\''
                        )
                    ),
                    array(
                        'table' => 'cuota_plans',
                        'alias' => 'CuotaPlans',
                        'type' => 'INNER',
                        'conditions' => array(
                            'CuotaPlans.id = RelPagoOperacion.operacion_id','RelPagoOperacion.operacion_tipo = \'cuota_plan\' '
                        )
                    )

                ), 'conditions' => array('CuotaPlans.id '=>$cheque['CuotaPlans']['id_cuotaPlan'])));

                //print_r($cuota_buscar);
                if ($cuota_buscar['ChequeConsumo']['id']) {


                    $concepto = "Cuota Plan - ".$cheque['Plans']['plan']." Vencimiento ".$cheque['CuotaPlans']['vencimiento'];
                }
            }
            elseif($cheque['Reservas']['reserva_numero'] != ''){
                $concepto = "Devolucion de Reserva nro: ".$cheque['Reservas']['reserva_numero'];
            }elseif($cheque['ChequeConsumo']['concepto'] != ''){
                $concepto = $cheque['ChequeConsumo']['concepto'];
            }

            if($cheque['ChequeConsumo']['fecha_debitado'] == '0000-00-00'){
                $debitado = '';
            }else{
                $debitado = $cheque['ChequeConsumo']['fecha_debitado'];
            }
            switch($cheque[0]['mes']){
                case 1:
                    $mes = "Enero";
                    break;

                case 2:
                    $mes = "Febrero";
                    break;

                case 3:
                    $mes = "Marzo";
                    break;

                case 4:
                    $mes = "Abril";
                    break;

                case 5:
                    $mes = "Mayo";
                    break;

                case 6:
                    $mes = "Junio";
                    break;

                case 7:
                    $mes = "Julio";
                    break;

                case 8:
                    $mes = "Agosto";
                    break;

                case 9:
                    $mes = "Septiembre";
                    break;

                case 10:
                    $mes = "Octubre";
                    break;

                case 11:
                    $mes = "Noviembre";
                    break;

                case 12:
                    $mes = "Diciembre";
                    break;

            }

            $rows[] = array(
                $cheque['ChequeConsumo']['id'],
                $cheque['ChequeConsumo']['fecha'],
                $debitado,
                $mes,
                $cheque['Banco']['banco']." (".$cheque['Cuenta']['sucursal'].")",

                $cheque['Cuenta']['nombre'],
                $cheque['Chequera']['chequera_numero'],
                str_pad($cheque['ChequeConsumo']['numero'], 8,'0',STR_PAD_LEFT),
                $cheque['ChequeConsumo']['titular'],
                $concepto,
                round($monto,'2'),
                $estado,
                round($iMonto[0]['total'],2)
            );
        }
        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => count($rows),
            "iTotalDisplayRecords" => $contarCheques,
            "aaData" => array()
        );

        $output['aaData'] = $rows;
        $this->set('aoData',$output);
        //print_r($output);
        $this->set('_serialize',
            'aoData'
        );
    }

    public function dataTable($limit = ""){
        $rows = array();
        if($limit == "todos"){
            $cheques = $this->ChequeConsumo->find('all',array('order' => 'ChequeConsumo.fecha desc'));
        }else{
            $cheques = $this->ChequeConsumo->find('all',array('limit' => $limit, 'order' => 'ChequeConsumo.fecha desc'));
        }

        foreach($cheques as $cheque){
            $rows[] = array(
                $cheque['ChequeConsumo']['id'],
                $cheque['ChequeConsumo']['fecha'],
                $cheque['ChequeConsumo']['fecha_debitado']
            );
        }

        $this->set('cheques',$cheques);
        $this->set('_serialize', array(
            'cheques'
        ));
    }


    public function guardar(){

        //print_r($this->request->data);
        if(!empty($this->request->data)) {
            $this->loadModel('Chequera');
            $chequera = $this->request->data['Chequera'];
            //print_r($chequera);
            //$this->Chequera->set($chequera);
            $cheques = $this->request->data['cheques'];
            if ($chequera['cuenta_id']=='') {
                $errores['Chequera']['cuenta_id']= 'Debe seleccionar una cuenta';
            }
            if ($chequera['concepto']=='') {
                $errores['Chequera']['Concepto'] = 'Debe seleccionar un concepto';
            }
            if (count($cheques)==0) {
                $errores['Chequera']['chequera_id'] = 'Debe seleccionar al menos un cheque';
            }
            if (strlen($chequera['obs'])=='') {
                $errores['Chequera']['obs'] = 'Incluya una descripcion corta';
            }
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
                $this->loadModel('ChequeraCheque');
                $user_id = $_SESSION['userid'];
                foreach ($cheques as $key => $value) {
                    $this->ChequeraCheque->id = $value;
                    $chequeraCheque = $this->ChequeraCheque->read();
                    $numero = $chequeraCheque['ChequeraCheque']['numero'];
                    //print_r($chequeraCheque);
                    $this->ChequeraCheque->set('estado',$chequera['concepto']);
                    $this->ChequeraCheque->save();
                    $concepto = ($chequera['concepto']==3)?'Anulado':'Extraviado';
                    $concepto .=' - '.$chequera['obs'];
                    $this->ChequeConsumo->create();
                    $this->ChequeConsumo->set('numero',$numero);
                    $this->ChequeConsumo->set('cuenta_id',$chequera['cuenta_id']);
                    $this->ChequeConsumo->set('chequera_id',$chequera['chequera_id']);
                    $this->ChequeConsumo->set('concepto',$concepto);
                    $this->ChequeConsumo->set('titular',$concepto);
                    $this->ChequeConsumo->set('fecha',date('Y-m-d'));
                    $this->ChequeConsumo->set('monto',0);
                    $this->ChequeConsumo->set('interes',0);
                    $this->ChequeConsumo->set('descuento',0);
                    $this->ChequeConsumo->set('creado_por',$user_id);
                    $this->ChequeConsumo->save();
                }
                //echo $chequera['chequera_id'];
                $chequeraUsada = $this->ChequeraCheque->find('first',array('conditions'=>array('ChequeraCheque.chequera_id'=>$chequera['chequera_id'],'ChequeraCheque.estado'=>0)));
                /*App::uses('ConnectionManager', 'Model');
            $dbo = ConnectionManager::getDatasource('default');
            $logs = $dbo->getLog();
            $lastLog = $logs['log'][0];
            echo $lastLog['query'];*/
                $this->Chequera->id = $chequera['chequera_id'];
                $this->Chequera->set($this->Chequera->read());

                if ($chequeraUsada) {
                    $this->Chequera->set('estado',1);
                }
                else{
                    $this->Chequera->set('estado',3);
                }
                //print_r($this->Chequera);
                $this->Chequera->save();
                $this->set('resultado','OK');
                $this->set('mensaje','Datos guardados');
                $this->set('detalle','');
            }
            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }



    }

    public function desanular(){

        //print_r($this->request->data);
        if(!empty($this->request->data)) {
            $this->loadModel('Chequera');
            $chequera = $this->request->data['Chequera'];
            $this->loadModel('ChequeraCheque');
            $cheque = $this->request->data['Cheque'];


            //print_r($chequera);
            //$this->Chequera->set($chequera);

            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{

                $user_id = $_SESSION['userid'];

                $this->ChequeraCheque->id = $cheque['id'];
                $chequeraCheque = $this->ChequeraCheque->read();

                //print_r($chequeraCheque);
                $this->ChequeraCheque->set('estado',$cheque['concepto']);
                $this->ChequeraCheque->save();
                $this->ChequeConsumo->id = $this->request->data['ChequeConsumo']['id'];
                $chequeConsumo = $this->ChequeConsumo->read();
                //print_r($chequeConsumo);
                switch ($cheque['concepto']) {
                    case 0:
                        $this->ChequeConsumo->delete($this->request->data['ChequeConsumo']['id'],true);
                        break;
                    case 3:

                        $this->ChequeConsumo->set('concepto',"Anulado");
                        $this->ChequeConsumo->set('chequera_id',$chequera['id']);
                        $this->ChequeConsumo->set('creado_por',$user_id);
                        $this->ChequeConsumo->set('fecha',$this->dateFormatSQL($chequeConsumo['fecha']));
                        $this->ChequeConsumo->save();
                        break;
                    case 4:
                        //echo $this->dateFormatSQL($chequeConsumo['fecha']);
                        $this->ChequeConsumo->set('concepto',"Extraviado");
                        $this->ChequeConsumo->set('chequera_id',$chequera['id']);
                        $this->ChequeConsumo->set('creado_por',$user_id);
                        $this->ChequeConsumo->set('fecha',$this->dateFormatSQL($chequeConsumo['ChequeConsumo']['fecha']));
                        $this->ChequeConsumo->save();
                        break;
                }





                //echo $chequera['chequera_id'];
                $chequeraUsada = $this->ChequeraCheque->find('first',array('conditions'=>array('ChequeraCheque.chequera_id'=>$chequera['id'],'ChequeraCheque.estado'=>0)));
                /*App::uses('ConnectionManager', 'Model');
            $dbo = ConnectionManager::getDatasource('default');
            $logs = $dbo->getLog();
            $lastLog = $logs['log'][0];
            echo $lastLog['query'];*/
                $this->Chequera->id = $chequera['id'];
                $this->Chequera->set($this->Chequera->read());

                if ($chequeraUsada) {
                    $this->Chequera->set('estado',1);
                }
                else{
                    $this->Chequera->set('estado',3);
                }
                //print_r($this->Chequera);
                $this->Chequera->save();
                $this->set('resultado','OK');
                $this->set('mensaje','Datos guardados');
                $this->set('detalle','');
            }
            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }



    }

    public function editar($id = null){
        $this->layout = 'form';

        $this->ChequeConsumo->id = $id;
        $this->request->data = $this->ChequeConsumo->read();
        //print_r($this->request->data);

        $this->set('cheque_consumo', $this->ChequeConsumo->read());

        $this->loadModel('Cuenta');
        $cuentas = $this->Cuenta->find('all');
        foreach($cuentas as $cuenta){
            $list2[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." - ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$list2);



    }

    public function reemplazar($id = null){
        $this->layout = 'form';

        $this->ChequeConsumo->id = $id;
        $this->request->data = $this->ChequeConsumo->read();
        //print_r($this->request->data);

        $this->set('cheque_consumo', $this->ChequeConsumo->read());

        $this->loadModel('Cuenta');
        $cuentas = $this->Cuenta->find('all');
        foreach($cuentas as $cuenta){
            $list2[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." - ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas2',$list2);
        $cuentas = $this->Cuenta->find('all',array('conditions' => array('emite_cheques' => 1),'recursive' => 1));
        foreach($cuentas as $cuenta){
            $list[$cuenta['Cuenta']['id']] = $cuenta['Banco']['banco']." - ".$cuenta['Cuenta']['nombre'];
        }
        $this->set('cuentas',$list);
        $this->set('defaultCuenta',$this->request->data['ChequeConsumo']['cuenta_id']);
        $this->set('conceptos',array('3' => 'Anulado','4' => 'Extraviado'));


    }

    public function guardar2(){

        //print_r($this->request->data);
        if(!empty($this->request->data)) {

            $chequeConsumo = $this->request->data['ChequeConsumo'];
            $this->ChequeConsumo->set($chequeConsumo);


            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{

                $this->ChequeConsumo->set('fecha',$this->dateFormatSQL($chequeConsumo['fecha']));
                $this->ChequeConsumo->save();



                $this->set('resultado','OK');
                $this->set('mensaje','Datos guardados');
                $this->set('detalle','');
            }
            $this->set('_serialize', array(
                'resultado',
                'mensaje' ,
                'detalle'
            ));
        }



    }


    public function reemplazo(){

        //print_r($this->request->data);
        if(!empty($this->request->data)) {
            $this->loadModel('Chequera');
            $chequera = $this->request->data['Chequera'];
            //print_r($chequera);
            //$this->Chequera->set($chequera);
            $chequeConsumo = $this->request->data['ChequeConsumo'];
            $this->ChequeConsumo->set($chequeConsumo);
            if ($chequeConsumo['vencido']=='0') {
                if ($chequeConsumo['vencio']=='0') {
                    if ($chequera['chequera_id']=='') {
                        $errores['Chequera']['chequera_id']= 'Debe seleccionar una chequera';
                    }
                    if ($chequeConsumo['cuenta_id']=='') {
                        $errores['ChequeConsumo']['cuenta_id']= 'Debe seleccionar una cuenta';
                    }
                    if ($chequeConsumo['cheque_id']=='') {
                        $errores['ChequeConsumo']['cheque_id']= 'Debe seleccionar un numero';
                    }
                    if ($chequeConsumo['titular']=='') {
                        $errores['ChequeConsumo']['titular']= 'Ingrese un titular';
                    }
                    if ($chequeConsumo['fecha']=='') {
                        $errores['ChequeConsumo']['fecha']= 'Ingrese una fecha valida';
                    }
                }
            }
            if(isset($errores) and count($errores) > 0){
                $this->set('resultado','ERROR');
                $this->set('mensaje','No se pudo guardar');
                $this->set('detalle',$errores);
            }else{
                if ($chequeConsumo['vencido']=='0') {
                    if ($chequeConsumo['vencio']=='0') {
                        $this->loadModel('ChequeraCheque');
                        $this->ChequeraCheque->id = $chequeConsumo['cheque_id'];
                        $cheque=$this->ChequeraCheque->read();
                        $this->ChequeraCheque->set('estado',1);
                        $this->ChequeraCheque->save();
                        $numeroNuevo = $cheque['ChequeraCheque']['numero'];
                        $cheque = $this->ChequeraCheque->find('first',array('conditions'=>array('ChequeraCheque.chequera_id'=>$chequera['chequera_id'],'ChequeraCheque.numero'=>str_pad($chequeConsumo['numero'], 8,'0',STR_PAD_LEFT))));
                        /*App::uses('ConnectionManager', 'Model');
                    $dbo = ConnectionManager::getDatasource('default');
                    $logs = $dbo->getLog();
                    print_r($logs);*/

                        if ($cheque) {
                            $this->ChequeraCheque->set($cheque);
                            $this->ChequeraCheque->set('estado',5);
                            $this->ChequeraCheque->save();
                        }
                        $chequeConsumoAnterior=$this->ChequeConsumo->findById($chequeConsumo['id']);
                        $this->loadModel('Cuenta');


                        $this->ChequeConsumo->set('numero',$numeroNuevo);
                        $this->ChequeConsumo->set('cuenta_id',$chequeConsumo['cuenta_id']);
                        $this->ChequeConsumo->set('chequera_id',$chequera['chequera_id']);
                        $this->ChequeConsumo->set('concepto',$chequeConsumo['concepto']);
                        $this->ChequeConsumo->set('titular',$chequeConsumo['titular']);
                        $this->ChequeConsumo->set('fecha',$this->dateFormatSQL($chequeConsumo['fecha']));
                        $this->ChequeConsumo->set('monto',$chequeConsumo['monto']);
                        $this->ChequeConsumo->set('interes',$chequeConsumo['interes']);
                        $this->ChequeConsumo->set('descuento',$chequeConsumo['descuento']);
                        $this->ChequeConsumo->set('fecha',$this->dateFormatSQL($chequeConsumo['fecha']));

                        $this->ChequeConsumo->save();
                        $cuenta=$this->Cuenta->findById($chequeConsumo['cuenta_id']);

                        $user_id = $_SESSION['userid'];
                        $this->ChequeConsumo->create();
                        $this->ChequeConsumo->set('numero',$chequeConsumoAnterior['ChequeConsumo']['numero']);
                        $this->ChequeConsumo->set('cuenta_id',$chequeConsumoAnterior['ChequeConsumo']['cuenta_id']);
                        $this->ChequeConsumo->set('chequera_id',$chequeConsumoAnterior['ChequeConsumo']['chequera_id']);
                        $this->ChequeConsumo->set('concepto','Reemplazado por '.$numeroNuevo.' de '.$cuenta['Banco']['banco'].'-'.$cuenta['Cuenta']['nombre']);
                        $this->ChequeConsumo->set('titular',$chequeConsumoAnterior['ChequeConsumo']['titular']);
                        $this->ChequeConsumo->set('fecha',$this->dateFormatSQL($chequeConsumoAnterior['ChequeConsumo']['fecha']));
                        $this->ChequeConsumo->set('monto',$chequeConsumoAnterior['ChequeConsumo']['monto']);
                        $this->ChequeConsumo->set('interes',$chequeConsumoAnterior['ChequeConsumo']['interes']);
                        $this->ChequeConsumo->set('descuento',$chequeConsumoAnterior['ChequeConsumo']['descuento']);
                        $this->ChequeConsumo->set('creado_por',$user_id);

                        $this->ChequeConsumo->save();
                        $chequera_id = $chequera['chequera_id'];
                    }
                    else{
                        $this->loadModel('ChequeraCheque');
                        $cheque = $this->ChequeraCheque->find('first',array('conditions'=>array('Chequera.cuenta_id'=>$chequeConsumo['cuenta'],'ChequeraCheque.numero'=>str_pad($chequeConsumo['numero'], 8,'0',STR_PAD_LEFT))));
                        /*App::uses('ConnectionManager', 'Model');
                    $dbo = ConnectionManager::getDatasource('default');
                    $logs = $dbo->getLog();
                    print_r($logs);*/
                        $chequera_id = $cheque['ChequeraCheque']['chequera_id'];
                        //print_r($cheque);
                        $this->ChequeraCheque->id = $cheque['ChequeraCheque']['id'];
                        $this->ChequeraCheque->set($this->ChequeraCheque->read());
                        $this->ChequeraCheque->set('estado',0);
                        $this->ChequeraCheque->save();

                        $this->ChequeConsumo->set('fecha',$this->dateFormatSQL($chequeConsumo['fecha']));
                        $this->ChequeConsumo->set('vencido',0);
                        $this->ChequeConsumo->set('cuenta_id',$chequeConsumo['cuenta']);
                        $this->ChequeConsumo->set('chequera_id',$chequera_id);
                        $this->ChequeConsumo->save();
                    }

                    $chequeraUsada = $this->ChequeraCheque->find('first',array('conditions'=>array('ChequeraCheque.chequera_id'=>$chequera_id,'ChequeraCheque.estado'=>0)));

                    $this->Chequera->id = $chequera_id;
                    $this->Chequera->set($this->Chequera->read());

                    if ($chequeraUsada) {
                        $this->Chequera->set('estado',1);
                    }
                    else{
                        $this->Chequera->set('estado',3);
                    }
                    $this->Chequera->save();
                }
                else{
                    $this->loadModel('ChequeraCheque');
                    $cheque = $this->ChequeraCheque->find('first',array('conditions'=>array('Chequera.cuenta_id'=>$chequeConsumo['cuenta'],'ChequeraCheque.numero'=>str_pad($chequeConsumo['numero'], 8,'0',STR_PAD_LEFT))));
                    $chequera_id = $cheque['ChequeraCheque']['chequera_id'];
                    //print_r($cheque);
                    $this->ChequeraCheque->id = $cheque['ChequeraCheque']['id'];
                    $this->ChequeraCheque->set($this->ChequeraCheque->read());
                    $this->ChequeraCheque->set('estado',2);
                    $this->ChequeraCheque->save();

                    $this->ChequeConsumo->set('fecha',$this->dateFormatSQL($chequeConsumo['fecha']));
                    $this->ChequeConsumo->set('vencido',1);
                    $this->ChequeConsumo->set('chequera_id',$chequera_id);
                    $this->ChequeConsumo->set('cuenta_id',$chequeConsumo['cuenta']);
                    $this->ChequeConsumo->save();
                    $chequeraUsada = $this->ChequeraCheque->find('first',array('conditions'=>array('ChequeraCheque.chequera_id'=>$cheque['ChequeraCheque']['chequera_id'],'ChequeraCheque.estado'=>0)));

                    $this->Chequera->id = $chequera_id;
                    $this->Chequera->set($this->Chequera->read());

                    if ($chequeraUsada) {
                        $this->Chequera->set('estado',1);
                    }
                    else{
                        $this->Chequera->set('estado',3);
                    }
                    $this->Chequera->save();
                }
                $this->set('resultado','OK');
                $this->set('mensaje','Datos guardados');
                $this->set('detalle','');
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
