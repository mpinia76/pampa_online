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
        <option <?php if($ano == '2025'){?> selected="selected" <?php } ?>>2025</option>
    <option <?php if($ano == '2026'){?> selected="selected" <?php } ?>>2026</option>
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
