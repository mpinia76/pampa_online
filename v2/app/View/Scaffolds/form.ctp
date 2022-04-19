<?php if(isset($status) and $status == 'exito'){?>
<script>
    var dhxWins = parent.dhxWins;
    dhxWins.window('w_<?php echo $this->params['controller'];?>').attachURL('v2/<?php echo $this->params['controller'];?>/index');
    alert('Los datos fueron guardados con exito');
</script>
<?php
}
$scaffoldFields=array_flip($scaffoldFields); 
foreach($scaffoldFields as &$v){ 
        $v=array('empty'=>'Seleccione...'); 
} 
?>
<?php $scaffoldFields['legend'] = false; ?>
<?php echo $this->Form->create(null, array('inputDefaults' => (array('div' => 'ym-gbox', 'legend' => 'false')))); ?>
<?php echo $this->Form->inputs($scaffoldFields, array('created', 'modified', 'updated')); ?>
<div style="padding:4px;"><?php echo $this->Form->end(__d('cake', 'Guardar')); ?></div>
