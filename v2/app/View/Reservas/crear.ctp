<?php
//agregar el calendario
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');
$this->Js->buffer('$("#ReservaTotalEstadia").keyup(updateTotal)');

//formulario
echo $this->Form->create(null, array('url' => '/reservas/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

?>

<div class="sectionTitle">Formulario de reserva</div>
<div class="ym-grid">
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><span class="fieldName">Creada:</span> <?php echo date('d/m/Y');?></div></div>
    <div class="ym-g25 ym-gl"><div class="ym-gbox" ><span class="fieldName">Reserva numero:</span> <?php echo $ultimo_nro?></div></div>
    <div class="ym-g25 ym-gl"><div class="ym-gbox" ><span class="fieldName">Cargado por:</span> <?php echo $usuario['Usuario']['nombre']." ".$usuario['Usuario']['apellido']?></div></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.reservado_por',array('label' => 'Reservado por:', 'options' => $empleados, 'empty' => 'Seleccionar ...', 'type'=>'select')); ?></div>
</div>
<?php echo $this->Form->hidden('Reserva.creado',array('value' => date('d/m/Y'))); ?>
<?php echo $this->Form->hidden('Reserva.actualizado',array('value' => date('Y-m-d H:i:s'))); ?>
<?php echo $this->Form->hidden('Reserva.cargado_por',array('value' => $usuario['Usuario']['id'])); ?>
<?php echo $this->Form->hidden('Reserva.numero',array('value' => $ultimo_nro)); ?>

<div class="sectionTitle">Datos personales</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.nombre_apellido');?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.dni',array('label'=>'DNI'));?></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.telefono'); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.celular'); ?></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.direccion'); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.localidad'); ?></div>
</div>
<?php echo $this->Form->input('Cliente.email'); ?>

<div class="sectionSubtitle">Datos Adicionales</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.iva',array('type' => 'select', 'options' => $iva_ops, 'empty' => 'Seleccionar ...', 'label' => 'IVA')); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.cuit', array('label' => 'CUIT')); ?></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.nacimiento'); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.profesion'); ?></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.1er_contacto'); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.fumador'); ?></div>
</div>
<?php echo $this->Form->input('Cliente.razones_eligio'); ?>

<div class="sectionTitle">Datos Reserva</div>
<div class="ym-grid">
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.apartamento_id',array('empty' => 'Seleccionar', 'type'=>'select'));?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.check_in',array('class'=>'datepicker','type'=>'text'));?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.check_out',array('class'=>'datepicker','type'=>'text')); ?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.late_check_out',array('label'=>'Late check out (HH:MM)','type' => 'text','default' => '10:00', 'class' => 'number')); ?></div>
</div>
<div class="ym-grid">
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><strong>Total</strong></div></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.pax_adultos',array('label'=>'Mayores', 'type' => 'text', 'class' => 'number','default' => '0'));?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.pax_menores',array('label'=>'Menores', 'type' => 'text', 'class' => 'number','default' => '0')); ?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.pax_bebes',array('label'=>'Bebes', 'type' => 'text', 'class' => 'number','default' => '0', 'div' => false)); ?> <?php echo $this->Form->input('Reserva.practicuna',array('label'=>false, 'div' => false)); ?> Practicuna?</div>
</div>
<div class="ym-grid">
    <div class="ym-g25 ym-gl"><div class="ym-gbox"><strong>A&B</strong></div></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.desayuno',array('label'=>'Desayuno')); ?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.algunas_comidas',array('label'=>'Algunas Comidas')); ?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Reserva.media_pension',array('label'=>'Media Pension?')); ?></div>
</div>
<div class="ym-grid">
    <div class="ym-g100 ym-gr" class="total_estadia">
        <div class="ym-gbox"><strong>Total Estadia $</strong> <input style="width: 100px;" type="text" name="data[Reserva][total_estadia]" id="ReservaTotalEstadia" value="0" /></div>
    </div>
</div>


<!-- reservas extras -->
<div class="sectionSubtitle">Extras</div>
<div class="ym-grid">
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('Extra.extra_rubro_id',array('label' => 'Seleecione un rubro', 'options' => $extra_rubros, 'empty' => 'Rubro', 'type'=>'select')); ?></div>
    <div class="ym-g50 ym-gl" id="extra_subrubros"></div>
    <div class="ym-g25 ym-gl"><div id="btn_add_extra" class="ym-gbox" style="margin-top:5px; display:none;"><span onclick="addExtra();" class="boton agregar">+ agregar</span></div></div>
</div>
<table width="100%" id="reserva_extras"></table>
<table width="100%" id="reserva_extras" class="extras_totales" style="display: none;">
    <tr>
        <td colspan="3" align="right"><strong>Total extras</strong></td>
        <td width="50" align="right">$<span class="extra_total"></span></td>
        <td width="50">&nbsp;</td>
    </tr>
</table>
<!-- fin reservas extras -->

<div class="ym-grid">
    <div class="ym-g100 ym-gr" class="total_estadia">
        <div class="ym-gbox"><strong>Tarifa bruta inicial (total estad&iacute;a+extras adelantados) $</strong> <input style="width: 100px;" type="text" name="data[Reserva][total]" id="ReservaTotal" value="0" /></div>
    </div>
</div>

<div class="sectionSubtitle">Comentarios</div>
<div class="ym-grid">
    <?php echo $this->Form->input('Reserva.comentarios',array('label' => false, 'type' => 'textarea')); ?>
</div>

<span onclick="guardar('guardar.json',$('form').serialize(),{id:'w_reservas',url:'/v2/reservas/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php echo $this->Form->end(); ?>

<script>
function addExtra(){
    var pattern = /^(([1-9]\d*))$/;
    if(pattern.test($('#ReservaExtraCantidad').val())){
        $.ajax({
          url: '<?php echo $this->Html->url('/reserva_extras/getRow', true);?>',
          data: {'extra_id' : $('#ExtraId').val(), 'cantidad' : $('#ReservaExtraCantidad').val()},
          success: function(data){
              $('#reserva_extras').append(data);
              $('.extras_totales').show();
              updateTotal();
          },
          dataType: 'html'
        });
    }else{
        alert('Ingrese un numero natural mayor a cero');
        $('#ReservaExtraCantidad').focus();
    }
}
$('#ExtraExtraRubroId').change(function(){
    if($(this).val() != ""){
        $.ajax({
          url: '<?php echo $this->Html->url('/extras/getSubrubrosPrecio', true);?>',
          data: {'rubro_id' : $(this).val() },
          success: function(data){
            $('#btn_add_extra').show();
            $('#extra_subrubros').html(data);
            updateTotal();
          },
          dataType: 'html'
        });
    }else{
        $('#btn_add_extra').hide();
        $('#extra_subrubros').html('');
    }
});
function updateTotal(){
    var result = 0;
    var extra_total = 0;
    result += parseFloat($('#ReservaTotalEstadia').val());
    $(".extra_tarifa").each(function(index,obj) { 
        result += parseFloat($('#'+$(obj).parent().parent().parent().attr('id') + ' .extra_cantidad').text()) * parseFloat($(obj).text()); 
        extra_total += parseFloat($('#'+$(obj).parent().parent().parent().attr('id') + ' .extra_cantidad').text()) * parseFloat($(obj).text()); 
    });
    $('#ReservaTotal').val(result);
    $('.extra_total').html(extra_total);
    if(extra_total == 0){
        $('.extras_totales').hide();
    }
}
</script>