<?php $ano= date('Y');
	  $mes= date('m');

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_libro_iva_ventas").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>
function descargar(){


	    createWindow('w_iva_ventas_exportar_descargar','Descargar','<?php echo $this->Html->url('/informes/exportarIvaVenta', true);?>/'+$('#financiero_mesual_mes').val()+'/'+$('#financiero_mensual_ano').val()+'/'+$('#financiero_mensual_orden').val()+'/'+$('#financiero_tipo_doc').val()+'/'+$('#financiero_tipo').val()+'/'+$('#financiero_punto_venta').val()+'/'+$('#financiero_buscar').val(),'430','300');
	    setTimeout('dhxWins.window("w_iva_ventas_exportar_descargar").close()', 2000);

}
</script>
<strong>Libro IVA ventas</strong>:
<select id="financiero_mesual_mes">
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
<?php
$currentYear = date("Y"); // Año actual
$startYear = 2011; // Año de inicio del rango


?><select id="financiero_mensual_ano" name="financiero_mensual_ano">
    <?php for ($year = $startYear; $year <= $currentYear; $year++): ?>
        <option value="<?php echo $year; ?>" <?php if ($ano == $year) echo 'selected="selected"'; ?>>
            <?php echo $year; ?>
        </option>
    <?php endfor; ?>
</select>
<select id="financiero_mensual_orden">
    <option value="fecha_emision">Ordenar por fecha</option>
    <option value="titular">Ordenar por titular</option>

</select>
<select id="financiero_tipo_doc">
	<option value="Seleccionar...">Seleccionar...</option>
    <option value="1">Factura</option>
    <option value="2">Nota de credito</option>

</select>
<select id="financiero_tipo">
	<option value="Seleccionar...">Seleccionar...</option>
    <option value="A">A</option>
    <option value="B">B</option>
    <option value="C">C</option>
    <option value="E">E</option>
    <option value="M">M</option>
</select>
<select id="financiero_punto_venta">
	<option value="Seleccionar...">Seleccionar...</option>
    <?php

    	 foreach ($puntos_venta as $punto) {

    	 	echo '<option value="'.$punto['PuntoVenta']['id'].'">'.$punto['PuntoVenta']['numero'].'</option>';

	    }
    ?>

</select>
<input type="text" name="financiero_buscar" id="financiero_buscar"  placeholder="buscar" value="">
<input type="button" onclick="ver_financiero_mensual();" value="Ver" />
<div id="financiero_mensual"></div>
<script>
function ver_financiero_mensual(){
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/iva_ventas', true);?>/'+$('#financiero_mesual_mes').val()+'/'+$('#financiero_mensual_ano').val()+'/'+$('#financiero_mensual_orden').val()+'/'+$('#financiero_tipo_doc').val()+'/'+$('#financiero_tipo').val()+'/'+$('#financiero_punto_venta').val()+'/'+$('#financiero_buscar').val(),
        dataType: 'html',
        success: function(data){
            $('#financiero_mensual').html(data);
        }
    })
}
</script>
