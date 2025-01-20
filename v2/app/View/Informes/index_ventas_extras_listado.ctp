<?php $ano= date('Y');
	  //$mes= date('m');

$currentYear = date("Y"); // Año actual
$startYear = 2011; // Año de inicio del rango


?>

<strong>A&ntilde;o</strong>:
<select id="financiero_mensual_ano">
    <?php for ($year = $startYear; $year <= $currentYear; $year++): ?>
        <option value="<?php echo $year; ?>" <?php if ($ano == $year) echo 'selected="selected"'; ?>>
            <?php echo $year; ?>
        </option>
    <?php endfor; ?>
</select>
<strong>Check out</strong>:
<select id="financiero_mesual_mes">
    <option>Seleccionar...</option>
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

<strong>Carga</strong>:
<select id="financiero_carga_mes">
    <option>Seleccionar...</option>
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

<strong>Tipo</strong>:
<select id="tipo">
	<option>Seleccionar...</option>
	<?php if($permisoAdelantada){ ?>
    	<option value="1">Adelantados</option>
    <?php } ?>
    <?php if($permisoNoAdelantada){ ?>
    <option value="2">No adelantados</option>
    <?php } ?>

</select>
<strong>Rubros</strong>:
<select id="extra_rubro">
	<option>Seleccionar...</option>
    <?php foreach($extra_rubros as $id => $rubro){

        echo '<option value="'.$id.'">'.$rubro.'</option>';
     } ?>



</select>

<strong>Subrubros</strong>:
<span id="extra_subrubros">
<select id="extra_subrubro">
	<option>Seleccionar...</option>




</select>
</span>
<input type="button" onclick="ver_extras();" value="Ver" /> <span id="cargando2" style="display:none;">Cargando ...</span>
<div id="informe_listado"></div>
<script>
    $('#financiero_mesual_mes').change(function(){
        $('#financiero_carga_mes').val("");
    });
    $('#financiero_carga_mes').change(function(){
        $('#financiero_mesual_mes').val("");
    });
$('#extra_rubro').change(function(){
    if($(this).val() != ""){
        $.ajax({
          url: '<?php echo $this->Html->url('/extras/getSubrubrosInforme', true);?>',
          data: {'rubro_id' : $(this).val() },
          success: function(data){

            $('#extra_subrubros').html(data);

          },
          dataType: 'html'
        });
    }else{

        $('#extra_subrubros').html('');
    }
});

function ver_extras(){

    $('#cargando2').show();
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/ventas_extras_listado', true);?>/'+$('#financiero_mesual_mes').val()+'/'+$('#financiero_mensual_ano').val()+'/'+$('#tipo').val()+'/'+$('#extra_rubro').val()+'/'+$('#extra_subrubro').val()+'/'+$('#financiero_carga_mes').val(),
        dataType: 'html',
        success: function(data){
            $('#cargando2').hide();
            $('#informe_listado').html(data);
        }
    })
}
</script>
