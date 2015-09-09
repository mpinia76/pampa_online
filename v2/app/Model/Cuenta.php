<?php
class Cuenta extends AppModel {
    public $useTable = 'cuenta';
    public $displayField = 'nombre';
    public $belongsTo = array('CuentaTipo','Banco');
}
?>
