<?php
class CajaMovimientosController extends AppController {
    function index(){
        $movimientos = $this->CajaMovimiento->find('all',array('limit' => '100', 'order' => 'CajaMovimiento.fecha desc', 'recursive' => 2)); 
        print_r($movimientos);
    }
}
?>
