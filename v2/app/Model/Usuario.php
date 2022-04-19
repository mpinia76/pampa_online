<?php
class Usuario extends AppModel {
    public $useTable = 'usuario';
    public $hasMany = 'UsuarioPermiso';
    public $hasAndBelongsToMany = array(
        'Cuentas' => array(
            'className'              => 'Cuenta',
            'joinTable'              => 'usuario_cuenta',
            'foreignKey'             => 'usuario_id',
            'associationForeignKey'  => 'cuenta_id',
            'unique'                 => true
        ),
        'Cajas' => array(
            'className'              => 'Caja',
            'joinTable'              => 'usuario_caja',
            'foreignKey'             => 'usuario_id',
            'associationForeignKey'  => 'caja_id',
            'unique'                 => true
        )
    );
    public $belongsTo = 'EspacioTrabajo';
}
?>
