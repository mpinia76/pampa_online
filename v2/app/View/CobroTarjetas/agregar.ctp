<div class="ym-grid border_bottom">
    <div class="ym-gbox"><?php echo $reserva_cobro['fecha']?> - TARJETA</div>
</div>
<?php echo $this->Form->hidden('CobroTarjeta.monto_neto',array('value' => $reserva_cobro['monto_neto'])); ?>
<?php echo $this->Form->hidden('CobroTarjeta.interes'); ?>
<div class="ym-grid">
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.posnet_id',array('label' => 'Estacion','options' => $posnets, 'empty' => 'Seleccionar...')); ?></div></div>
    <div class="ym-g25 ym-gl"><div id="tarjeta_marcas"></div></div>
    <div class="ym-g25 ym-gl"><div id="tarjeta_cuotas"></div></div>
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.tarjeta_numero',array('label' => 'Ultimos 4 digitos', 'type' => 'text', 'class' => '','default' => '')); ?></div></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.lote_nuevo',array('label' => 'Lote')); ?></div></div>
    <div class="ym-g50 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.autorizacion'); ?></div></div>
    
</div>
<div class="ym-grid">
    
    <div class="ym-g50 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.cupon'); ?></div></div>
    <div class="ym-g50 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.titular'); ?></div></div>
</div>
<!--<div class="ym-grid">
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.titular'); ?></div></div>
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.dni'); ?></div></div>
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.domicilio'); ?></div></div>
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><?php echo $this->Form->input('CobroTarjeta.nacimiento'); ?></div></div>
</div>-->
<div class="ym-grid" style="text-align: right;">
    <div class="ym-gbox">Libre de intereses: $<span id="monto_neto" style="font-weight: bold;"><?php echo $reserva_cobro['monto_neto']; ?></span></div>
    <div class="ym-gbox">Intereses: $<span id="intereses" style="font-weight: bold;">0</span></div>
    <div class="ym-gbox">Total a cobrar: $<span id="total" style="font-weight: bold;"></span></div>
</div>
<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/reserva_cobros/guardar.json', true);?>',$('form').serialize(),'w_reservas');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonGuardarError" class="boton guardar" style="display:none">Procesando...</span>
<div class="ym-gbox" style="text-align: center;"><a onclick="location.reload();">cancelar</a></div>
<script>
function actualizaTotal(){
    var total = parseFloat($('#monto_neto').html()) + parseFloat($('#intereses').html());
    $('#total').html(total);
}
actualizaTotal();
$('#CobroTarjetaPosnetId').change(function(){
    if($(this).val()!=''){
        $.ajax({
            url: '<?php echo $this->Html->url('/cobro_tarjeta_tipos/getMarcas/', true);?>'+$(this).val(),
            dataType: 'html',
            data: {'model' : 'CobroTarjeta'},
            success: function(data){
                $('#tarjeta_marcas').html(data);
                $('#CobroTarjetaCobroTarjetaTipoId').change(function(){
                    if($(this).val()!=''){
                        $.ajax({
                            url: '<?php echo $this->Html->url('/cobro_tarjeta_cuotas/getCuotas/', true);?>'+$(this).val(),
                            dataType: 'html',
                            success: function(data){
                                $('#tarjeta_cuotas').html(data);
                                $('#CobroTarjetaCuotas').change(function(){
                                    if($(this).val() != ''){
                                        var num_interes = Math.round(parseFloat($('#monto_neto').html())*(parseFloat(intereses[$(this).val()])-1)*100)/100;
                                        $('#intereses').html(num_interes);
                                        $('#CobroTarjetaInteres').val(num_interes);
                                        actualizaTotal();
                                    }else{
                                        $('#intereses').html('0');
                                    }
                                })
                            }
                        });
                    }else{
                         $('#tarjeta_cuotas').html('');
                    }
                });
            }
        });
    }else{
         $('#tarjeta_marcas').html('');
         $('#tarjeta_cuotas').html('');
    }
});
</script>