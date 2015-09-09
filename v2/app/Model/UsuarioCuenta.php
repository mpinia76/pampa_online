<?php
class UsuarioCuenta extends AppModel {
    public $useTable = 'usuario_cuenta';
    public $belongsTo = array('Usuario','Cuenta');
}
?>
