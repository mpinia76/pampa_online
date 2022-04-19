<?php
class UsuarioCaja extends AppModel {
    public $useTable = 'usuario_caja';
    public $belongsTo = array('Usuario','Caja');
}
?>
