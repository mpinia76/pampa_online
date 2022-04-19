<?php
class UsuarioLog extends AppModel {
    public $useTable = 'usuario_log';
    public $displayField = 'usuario_log';
    
    public $belongsTo = array('Usuario');
	
    
}
?>
