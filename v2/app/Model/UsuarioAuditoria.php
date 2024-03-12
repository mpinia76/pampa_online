<?php
class UsuarioAuditoria extends AppModel {
    public $useTable = 'usuario_auditoria';
    public $displayField = 'usuario_auditoria';
    
    public $belongsTo = array('Usuario');
	
    
}
?>
