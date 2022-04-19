<?php
//formulario

echo $this->Form->create(null, array('url' => '/reservas','inputDefaults' => (array('div' => 'ym-gbox'))));
echo $this->Form->hidden('reserva_id',array('value' => $reserva['Reserva']['id'])); 
Echo 'En todos los campos puede agregar varias direcciones de E-mail separadas por comas<br>';
echo $this->Form->input('mails',array('label' => 'E-mail','type'=>'text', 'default' => 'recepcion@villagedelaspampas.com.ar'));
//echo $this->Form->input('mailsCC',array('label' => 'CC','type'=>'text', 'default' => 'recepcion@villagedelaspamas.com.ar,info@villagedelaspampas.com.ar,frontdesk@villagedelaspampas.com.ar'));
echo $this->Form->input('mailsCCO',array('label' => 'CCO','type'=>'text', 'default' => 'info@villagedelaspampas.com.ar'));

echo $this->Form->end();
?>
<span onclick="enviarVoucher();" class="boton guardar">Enviar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>


<script>
//function para actualizar el contenido del voucher y crear pdf
function enviarVoucher(){
    $('#loading_save').show();
    $.ajax({
        url : '<?php echo $this->Html->url('/reservas/plantilla/'.$reserva['Reserva']['id'].'/1/F', true);?>',
        
        success: function(data){
            guardar('<?php echo $this->Html->url('/reservas/enviarPlanilla.json', true);?>',$('form').serialize());
        }
    });
}


</script>