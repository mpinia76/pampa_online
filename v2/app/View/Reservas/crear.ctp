<?php
//agregar el calendario
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');
$this->Js->buffer('$("#ReservaTotalEstadia").keyup(updateTotal)');

$this->Js->buffer('
  $("#ClienteEmail2").on(\'paste\', function(e){
    e.preventDefault();
    alert(\'Introduzca el email manualmente\');
  })
');

//formulario
echo $this->Form->create(null, array('url' => '/reservas/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

?>
<?php echo $this->Form->hidden('Cliente.cuit'); ?>
<?php echo $this->Form->hidden('Cliente.codPais'); ?>

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
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Subcanal.canal_id',array('label' => 'Canal de venta','empty' => 'Seleccionar', 'type'=>'select'));?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Reserva.subcanal_id',array('label' => 'Subcanal de venta', 'empty' => 'Seleccionar', 'type'=>'select'));?></div>
</div>
<div class="sectionTitle">Datos personales</div>
<div class="ym-grid">
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('Cliente.nombre_apellido');?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('Cliente.tipoDocumento',array('type' => 'select', 'empty' => 'Seleccionar ...', 'options' => $tipoDocumento_ops, 'label' => 'Tipo')); ?></div>
    <div class="ym-g33 ym-gl"><?php echo $this->Form->input('Cliente.dni',array('label'=>'DNI/Pasaporte', 'value' => $reserva['Cliente']['dni']));?></div>
</div>
<div class="ym-grid">
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Cliente.sexo',array('empty' => 'Seleccionar', 'type'=>'select', 'options' => $sexos));?></div>
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Cliente.tipoTelefono',array('type' => 'select', 'empty' => 'Seleccionar ...', 'options' => $tipoTelefono_ops, 'label' => 'Tipo')); ?></div>
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Cliente.codPaisAux',array('label'=>'Cod. Pais', 'value' => $reserva['Cliente']['codPais'])); ?></div>
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Cliente.codArea',array('label'=>'Cod. Area')); ?></div>
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Cliente.telefono'); ?></div>
    <!--<div class="ym-g33 ym-gl"><?php echo $this->Form->input('Cliente.celular'); ?></div>-->
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.direccion'); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.localidad'); ?></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.email');; ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.email2',array('label'=>'Repita el E-mail')); ?></div>
</div>

<div class="sectionSubtitle">Datos Adicionales</div>

<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.nacimiento',array('class'=>'datepicker','type'=>'text')); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.profesion'); ?></div>
</div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.1er_contacto'); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.fumador'); ?></div>
</div>
<?php echo $this->Form->input('Cliente.razones_eligio'); ?>

<div class="sectionTitle">Datos Reserva</div>
<div class="ym-grid">
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Reserva.apartamento_id',array('empty' => 'Seleccionar', 'type'=>'select', 'default' => $defaultApartamento));?></div>
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Reserva.check_in',array('class'=>'datepicker','type'=>'text','default'=>$checkIn));?></div>
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Reserva.hora_check_in',array('label'=>'check in (HH:MM)','type' => 'text','default' => '15:00', 'class' => 'number')); ?></div>
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Reserva.check_out',array('class'=>'datepicker','type'=>'text','default'=>$checkOut)); ?></div>
    <div class="ym-g20 ym-gl"><?php echo $this->Form->input('Reserva.late_check_out',array('label'=>'check out (HH:MM)','type' => 'text','default' => '10:00', 'class' => 'number')); ?></div>
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
        <div class="ym-gbox"><strong>Tarifa bruta inicial (total estad&iacute;a+extras adelantados) $</strong> <input style="width: 100px;" type="hidden" name="data[Reserva][total]" id="ReservaTotal" value="0" /><span id="reservaTotalSpan">0</span></div>
    </div>
</div>
<div class="sectionSubtitle">Informacion de Facturacion</div>


<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.iva',array('type' => 'select','disabled'=>'disabled', 'options' => $iva_ops, 'empty' => 'Seleccionar ...', 'label' => 'Condicion Impositiva')); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.tipoPersona',array('type' => 'select','disabled'=>'disabled', 'options' => $tipoPersona_ops, 'empty' => 'Seleccionar ...', 'label' => 'Tipo de Persona')); ?></div>
</div>

<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.titular_factura',array('label'=>'Facturar a titular de reserva','disabled'=>'disabled','default' => '0')); ?></div>
<div class="ym-grid">
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.razon_social',array('label'=>'Nombre Apellido/Razon Social','disabled'=>'disabled')); ?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Cliente.cuitAux', array('label' => 'CUIT','disabled'=>'disabled', 'value' => $reserva['Cliente']['cuit'])); ?></div>
</div>
<div class="sectionSubtitle">Comentarios</div>
<div class="ym-grid">
    <?php echo $this->Form->input('Reserva.comentarios',array('label' => false, 'type' => 'textarea')); ?>
</div>
<?php if($grilla){ ?>
<span id="botonGuardar" onclick="guardarSinRefrescar('<?php echo $this->Html->url('/reservas/guardar.json', true);?>',$('form').serialize());" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonBloquear" onclick="guardarSinRefrescar('<?php echo $this->Html->url('/reservas/bloquearApartamento.json', true);?>',$('form').serialize());" class="boton bloquear">Bloquear Apartamento <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<span id="botonVolver" onclick="volver();" class="boton volver">Volver a la grilla</span>
<?php }else{ ?>
<span onclick="guardar('guardar.json',$('form').serialize(),{id:'w_reservas',url:'v2/reservas/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php } ?>

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
    $('#reservaTotalSpan').html(result);
    $('.extra_total').html(extra_total);
    if(extra_total == 0){
        $('.extras_totales').hide();
    }
}

$('#SubcanalCanalId').change(function(){
    if($(this).val()!=''){
        $.ajax({
            url: '<?php echo $this->Html->url('/reservas/getSubcanals/', true);?>'+$(this).val(),
            dataType: 'html',

            success: function(data){
                $('#ReservaSubcanalId').html(data);
            }
        });
    }else{
         $('#ReservaSubcanalId').html('');
    }
})

function volver(){
	document.location = "<?php echo $this->Html->url('/informes/index_ventas_grilla', true);?>";

}

$(document).ready(function(){


    $("#ClienteCodPaisAux").autocomplete({
        source: '<?php echo $this->Html->url('/clientes/autoCompletePrefijo', true);?>',
        minLength: 2
    });

    $("#ClienteCodPaisAux").autocomplete({
        select: function(event, ui) {
            selected_id = ui.item.id;

            $("#ClienteCodPais").val(selected_id);

        }
    });
    $("#ClienteCodPaisAux").autocomplete({
        open: function(event, ui) {
            $("#CodPaisId").remove();
        }
    });

    $("#ClienteDni").autocomplete({
        source: '<?php echo $this->Html->url('/clientes/autoCompleteDni', true);?>',
        minLength: 2
    });

    $("#ClienteDni").autocomplete({
        select: function(event, ui) {
            selected_id = ui.item.id;
            selected_dni = ui.item.value;
            $("#ClienteDni").val(selected_dni);
            $.ajax({
                url: '<?php echo $this->Html->url('/clientes/getDatos', true);?>',
                data: {'id' : selected_id },
                dataType: 'json',
                success: function(data){

                    $('#ClienteNombreApellido').val(data.Cliente.nombre_apellido);
                    $('#ClienteTipoDocumento').val(data.Cliente.tipoDocumento);
                    $('#ClienteTipoTelefono').val(data.Cliente.tipoTelefono);
                    $('#ClienteCodPaisAux').val(data.Cliente.codPais);
                    $('#ClienteCodPais').val(data.Cliente.codPais);
                    $('#ClienteCodArea').val(data.Cliente.codArea);
                    $('#ClienteTelefono').val(data.Cliente.telefono);
                    $('#ClienteDireccion').val(data.Cliente.direccion);
                    $('#ClienteLocalidad').val(data.Cliente.localidad);
                    $('#ClienteEmail').val(data.Cliente.email);
                    $('#ClienteEmail2').val(data.Cliente.email2);
                    $('#ClienteProfesion').val(data.Cliente.profesion);

                    $('#Cliente1erContacto').val(data.Cliente['1er_contacto']);
                    $('#ClienteFumador').val(data.Cliente.fumador);
                    $('#ClienteClienteRazonesEligio').val(data.Cliente.razones_eligio);

                    $('#ClienteSexo').val(data.Cliente.sexo);


                },
            });

        }
    });
});
function modificarFacturacion(){
    if ($('#ClienteTipoDocumento').val()=='DNI'){
        $('#ClienteIva').prop( "disabled", false );
        /*$('#ClienteTipoPersona').prop( "disabled", false );*/
        $('#ClienteRazonSocial').prop( "disabled", false );

    }
    else{
        $('#ClienteIva').val('');
        $('#ClienteIva').prop( "disabled", true );
        $('#ClienteTipoPersona').val('');
        $('#ClienteTipoPersona').prop( "disabled", true );
        $('#ClienteRazonSocial').val('');
        $('#ClienteRazonSocial').prop( "disabled", true );
        $('#ClienteCuitAux').val('');
        $('#ClienteCuitAux').prop( "disabled", true );
        $('#ClienteTitularFactura').prop( "disabled", true );
        $("#ClienteTitularFactura").prop('checked', false);

    }

}

$('#ClienteTipoDocumento').change(function(){
    if ($('#ClienteTipoDocumento').val()=='DNI'){
        $("#ClienteDni").attr('maxlength', 8);
    }
    else{

        $("#ClienteDni").removeAttr('maxLength');
    }
    modificarFacturacion();
});



$('#ClienteTipoPersona').change(function(){
    if ($('#ClienteTipoPersona').val()=='Fisica'){

        $('#ClienteCuitAux').prop( "disabled", true );
        $('#ClienteTitularFactura').prop( "disabled", false );

    }
    else{
        $('#ClienteCuitAux').val('');
        $('#ClienteCuitAux').prop( "disabled", false );
        $('#ClienteTitularFactura').prop( "disabled", true );
        $("#ClienteRazonSocial").val('');
        $("#ClienteTitularFactura").prop('checked', false);
    }
});

$("#ClienteTitularFactura").click( function(){
    if( $(this).is(':checked') ) {
        $("#ClienteRazonSocial").val($("#ClienteNombreApellido").val());
        $('#ClienteCuitAux').prop( "disabled", true );
        $.ajax({
            url: '<?php echo $this->Html->url('/clientes/getCuit', true);?>',
            data: {'dni' : $("#ClienteDni").val(),'sexo' : $("#ClienteSexo").val() },
            dataType: 'json',
            success: function(data){

                $('#ClienteCuitAux').val(data.cuit);

            },

        });
    }
    else{
        $("#ClienteRazonSocial").val('');
        $('#ClienteCuitAux').val('');
        $('#ClienteCuitAux').prop( "disabled", false );
    }

})

$('#ClienteCuitAux').change(function(){

    $('#ClienteCuit').val($('#ClienteCuitAux').val());


});

$('#ClienteDni').blur(function(){
    modificarFacturacion();
});



$("#ClienteIva").one('focus', function () {
    var ddl = $(this);
    ddl.data('previous', ddl.val());
}).on('change', function () {
    var ddl = $(this);
    var previous = ddl.data('previous');
    ddl.data('previous', ddl.val());
    if ($(this).val()==''){
        if(confirm('Desea facturar a consumidor final?')) {

            $('#ClienteTipoPersona').val('');
            $('#ClienteTipoPersona').prop("disabled", true);
            $('#ClienteRazonSocial').val('');
            $('#ClienteRazonSocial').prop("disabled", true);
            $('#ClienteCuitAux').val('');
            $('#ClienteCuitAux').prop("disabled", true);
            $('#ClienteTitularFactura').prop( "disabled", true );
            $("#ClienteTitularFactura").prop('checked', false);
        }else{

            $("option[value='"+previous+"']", this).attr("selected",true);
        }

    }
    else{
        $('#ClienteTipoPersona').prop( "disabled", false );
    }
});
</script>
