<?php $ano= date('Y');
	  $mes= date('m');

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_libro_iva_compras").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>
function descargar(){

    var colNombre=0
    if($('#colNombre').is(':checked')){
        colNombre=1;
    }
    var colDni=0
    if($('#colDni').is(':checked')){
        colDni=1;
    }
    var colTelefono=0
    if($('#colTelefono').is(':checked')){
        colTelefono=1;
    }
    var colDireccion=0
    if($('#colDireccion').is(':checked')){
        colDireccion=1;
    }
    var colCelular=0
    if($('#colCelular').is(':checked')){
        colCelular=1;
    }
    var colLocalidad=0
    if($('#colLocalidad').is(':checked')){
        colLocalidad=1;
    }
    var colEmail=0
    if($('#colEmail').is(':checked')){
        colEmail=1;
    }
	    createWindow('w_base_datos_exportar_descargar','Descargar','<?php echo $this->Html->url('/informes/exportarBaseDatos', true);?>/'+$('#financiero_mesual_mes').val()+'/'+$('#financiero_mensual_ano').val()+'/'+colNombre+'/'+colDni+'/'+colTelefono+'/'+colCelular+'/'+colDireccion+'/'+colLocalidad+'/'+colEmail,'430','300');
	    setTimeout('dhxWins.window("w_base_datos_exportar_descargar").close()', 4000);

}
</script>
<strong>Base de datos</strong>:
<select id="financiero_mesual_mes">
    <option value="N">Todos</option>
    <option value="01" <?php if($mes == '01'){?> selected="selected" <?php } ?>>Enero</option>
    <option value="02" <?php if($mes == '02'){?> selected="selected" <?php } ?>>Febrero</option>
    <option value="03" <?php if($mes == '03'){?> selected="selected" <?php } ?>>Marzo</option>
    <option value="04" <?php if($mes == '04'){?> selected="selected" <?php } ?>>Abril</option>
    <option value="05" <?php if($mes == '05'){?> selected="selected" <?php } ?>>Mayo</option>
    <option value="06" <?php if($mes == '06'){?> selected="selected" <?php } ?>>Junio</option>
    <option value="07" <?php if($mes == '07'){?> selected="selected" <?php } ?>>Julio</option>
    <option value="08" <?php if($mes == '08'){?> selected="selected" <?php } ?>>Agosto</option>
    <option value="09" <?php if($mes == '09'){?> selected="selected" <?php } ?>>Septiembre</option>
    <option value="10" <?php if($mes == '10'){?> selected="selected" <?php } ?>>Octubre</option>
    <option value="11" <?php if($mes == '11'){?> selected="selected" <?php } ?>>Noviembre</option>
    <option value="12" <?php if($mes == '12'){?> selected="selected" <?php } ?>>Diciembre</option>
</select>
<select id="financiero_mensual_ano">
    <option <?php if($ano == '2012'){?> selected="selected" <?php } ?>>2012</option>
    <option <?php if($ano == '2013'){?> selected="selected" <?php } ?>>2013</option>
    <option <?php if($ano == '2014'){?> selected="selected" <?php } ?>>2014</option>
    <option <?php if($ano == '2015'){?> selected="selected" <?php } ?>>2015</option>
    <option <?php if($ano == '2016'){?> selected="selected" <?php } ?>>2016</option>
    <option <?php if($ano == '2017'){?> selected="selected" <?php } ?>>2017</option>
    <option <?php if($ano == '2018'){?> selected="selected" <?php } ?>>2018</option>
    <option <?php if($ano == '2019'){?> selected="selected" <?php } ?>>2019</option>
    <option <?php if($ano == '2020'){?> selected="selected" <?php } ?>>2020</option>
    <option <?php if($ano == '2021'){?> selected="selected" <?php } ?>>2021</option>
    <option <?php if($ano == '2022'){?> selected="selected" <?php } ?>>2022</option>
    <option <?php if($ano == '2023'){?> selected="selected" <?php } ?>>2023</option>
    <option <?php if($ano == '2024'){?> selected="selected" <?php } ?>>2024</option>
</select>

<input type="button" onclick="ver_financiero_mensual();" value="Ver" /><span id="cargando" style="display:none;">Cargando ...</span>
<div id="financiero_mensual"></div>
<script>
function ver_financiero_mensual(){
    var colNombre=0
    if($('#colNombre').is(':checked')){
        colNombre=1;
    }
    var colDni=0
    if($('#colDni').is(':checked')){
        colDni=1;
    }
    var colTelefono=0
    if($('#colTelefono').is(':checked')){
        colTelefono=1;
    }
    var colDireccion=0
    if($('#colDireccion').is(':checked')){
        colDireccion=1;
    }
    var colCelular=0
    if($('#colCelular').is(':checked')){
        colCelular=1;
    }
    var colLocalidad=0
    if($('#colLocalidad').is(':checked')){
        colLocalidad=1;
    }
    var colEmail=0
    if($('#colEmail').is(':checked')){
        colEmail=1;
    }
    $('#cargando').show();
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/base_datos', true);?>/'+$('#financiero_mesual_mes').val()+'/'+$('#financiero_mensual_ano').val()+'/'+colNombre+'/'+colDni+'/'+colTelefono+'/'+colCelular+'/'+colDireccion+'/'+colLocalidad+'/'+colEmail,
        dataType: 'html',
        success: function(data){
            $('#cargando').hide();
            $('#financiero_mensual').html(data);
        }
    })
}
</script>
