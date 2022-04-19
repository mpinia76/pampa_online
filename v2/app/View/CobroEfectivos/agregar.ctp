<div class="ym-grid border_bottom">
    <div class="ym-gbox"><?php echo $reserva_cobro['fecha']?> - EFECTIVO</div>
</div>
<?php echo $this->Form->hidden('CobroEfectivo.monto_neto',array('value' => $reserva_cobro['monto_neto'])); ?>
<?php echo $this->Form->hidden('CobroEfectivo.moneda_id',array('value' => $reserva_cobro['moneda_id'])); ?>
<?php echo $this->Form->hidden('CobroEfectivo.monto_moneda',array('value' => $reserva_cobro['monto_moneda'])); ?>
<?php echo $this->Form->hidden('CobroEfectivo.cambio',array('value' => $reserva_cobro['cambio'])); ?>
<div class="ym-grid">
    <div class="ym-gbox"><?php echo $this->Form->input('CobroEfectivo.caja_id',array( 'label' => 'Caja de ingreso', 'empty' => 'Seleccionar...')); ?></div>
</div>
<div class="ym-grid" style="text-align: right;">
    <div class="ym-gbox">Total a cobrar: $<span id="total" style="font-weight: bold;"><?php echo $reserva_cobro['monto_neto']; ?></span></div>
</div>
<?php echo $this->Form->hidden('CobroEfectivo.interes',array('value' => 0)); ?>
<span id="botonGuardar" onclick="guardar('<?php echo $this->Html->url('/reserva_cobros/guardar.json', true);?>',$('form').serialize(),'w_reservas');" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonGuardarError" class="boton guardar" style="display:none">Procesando...</span>
<div class="ym-gbox" style="text-align: center;"><a onclick="location.reload();">cancelar</a></div>
<script>
$('#CobroEfectivoCajaId').change(function(){
	
    if($(this).val()!=''){
        var datos = ({
			'caja_id' : $(this).val(),
			'fecha' : '<?php echo $reserva_cobro['fecha']?>'
		});
			
		$.ajax({
			beforeSend: function(){
				$('#loading').show();
			},
			data: datos,
			url: '../../../functions/consultarSincronismoFecha.php',
			success: function(data) {
			
				if(data == 'no'){		
					alert('Movimiento no permitido: La caja se encuentra conciliada y sincronizada para la fecha del movimiento que intenta realizar. Contactar administrador');
					$('#CobroEfectivoCajaId').prop('selectedIndex',0);
				}
				$('#loading').hide();
				
			}
		});
    }
})
</script>