<?php
class ReservaDevolucion extends AppModel {
    public $useTable = 'reserva_devoluciones';
    
    public $virtualFields = array(
        'mes' => 'MONTH(fecha)',
        'ano_mes' => 'DATE_FORMAT(ReservaDevolucion.fecha,"%y%m")'
    );
}
?>
