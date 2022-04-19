<?php
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_reservas").getPosition();
    xpos = position[0];
    ypos = position[1];
');

//formulario 
echo $this->Form->create(null, array('url' => '/extras/crear','inputDefaults' => (array('div' => 'ym-gbox'))));
echo $this->Form->hidden('Voucher.id',array('value' => $voucher['Voucher']['id']));
echo $this->Form->input('Voucher.restricciones',array('label' => 'Condiciones','value' => $voucher['Voucher']['restricciones'], 'rows' => '15'));
echo $this->Form->input('Voucher.politica_cancelacion',array('label' => 'Politica de cancelacion','value' => $voucher['Voucher']['politica_cancelacion'], 'rows' => '12'));
echo $this->Form->end();
?>
<span onclick="guardarVoucher();" class="boton guardar">Descargar Voucher <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span onclick="enviarVoucher();" class="boton guardar">Enviar por mail</span>

<script>
//function para actualizar el contenido del voucher y crear pdf
function guardarVoucher(){
    $('#loading_save').show();
    $.ajax({
        url : '<?php echo $this->Html->url('/vouchers/guardar.json', true);?>',
        data: $('form').serialize(),
        type : 'POST',
        success: function(data){
            $('#loading_save').hide();
            document.location = '<?php echo $this->Html->url('/vouchers/ver/'.$reserva_id, true);?>';
        }
    });
}

function enviarVoucher(){
   
    $.ajax({
        url : '<?php echo $this->Html->url('/vouchers/guardar.json', true);?>',
        data: $('form').serialize(),
        type : 'POST',
        success: function(data){
           
            createWindow('w_enviar_voucher','Enviar voucher','<?php echo $this->Html->url('/vouchers/formMail/'.$reserva_id, true);?>','430','400');
        }
    });
}

</script>