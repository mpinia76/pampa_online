<?php
class Subrubro extends AppModel {
    public $displayField = 'subrubro';
    public $useTable = 'subrubro';
    public $belongsTo = array('Rubro');
}
?>
