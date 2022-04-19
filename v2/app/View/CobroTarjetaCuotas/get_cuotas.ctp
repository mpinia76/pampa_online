<?php
echo $this->Form->input('CobroTarjeta.cuotas',array('options' => $cuotas, 'empty' => 'Seleccionar...', 'type'=>'select', 'div' => 'ym-gbox', 'label' => 'Cuotas'));
?>
<script>
    var intereses = $.parseJSON('<?php echo json_encode($cuotas_interes);?>');
</script>