<?php $ano= date('Y'); ?>
<strong>Informe Financiero</strong>: 
<select id="financiero_ano">
    <option>Seleccionar...</option>
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
<strong>&nbsp;&nbsp;Entre fechas&nbsp;<input type="text" name="desde" id="desde" class="datepicker">&nbsp;&nbsp;y&nbsp;<input type="text" name="hasta" id="hasta" class="datepicker"></strong>
<input type="button" onclick="ver_financiero();" value="Ver" /> <span id="cargando" style="display:none;">Cargando ...</span>
<div id="informe_financiero"></div>
<script>
function ver_financiero(){
	var strDesde = $('#desde').val().split("/"); 
	var Fecha1 = new Date(parseInt(strDesde[2]),parseInt(strDesde[1]-1),parseInt(strDesde[0]));
	
	var strHasta = $('#hasta').val().split("/"); 
	var Fecha2 = new Date(parseInt(strHasta[2]),parseInt(strHasta[1]-1),parseInt(strHasta[0]));
	if(!isNaN(strHasta[2])){
		if(strDesde[2]!=strHasta[2]){
			alert('Las fechas deben ser del mismo a\u00F1o');
			return false;
		}
		
		if(Fecha1>Fecha2){
			alert('La fecha Hasta tiene que ser posterior a la fecha Desde');
			return false;
		}
	}
	
	var desde = strDesde[2]+'-'+strDesde[1]+'-'+strDesde[0];	
	
	var hasta = strHasta[2]+'-'+strHasta[1]+'-'+strHasta[0];
    $('#cargando').show();
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/ventas_financiero', true);?>/'+$('#financiero_ano').val()+'/'+desde+'/'+hasta,
        dataType: 'html',
        success: function(data){
        	$('#cargando').hide();
            $('#informe_financiero').html(data);
        }
    })
}
$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });
 $("#financiero_ano").change(function(){
 		if($('#financiero_ano').val()!='Seleccionar...'){
        	$('#desde').val('01/01/'+$('#financiero_ano').val());
        }
        $('#hasta').val('');
    });
 $("#financiero_ano").change();
</script>
<br/>
<strong>Informe Financiero Mensual</strong>: 
<select id="financiero_mesual_mes">
    <option value="01">Enero</option>
    <option value="02">Febrero</option>
    <option value="03">Marzo</option>
    <option value="04">Abril</option>
    <option value="05">Mayo</option>
    <option value="06">Junio</option>
    <option value="07">Julio</option>
    <option value="08">Agosto</option>
    <option value="09">Septiembre</option>
    <option value="10">Octubre</option>
    <option value="11">Noviembre</option>
    <option value="12">Diciembre</option>
</select> 
<select id="financiero_mensual_ano">
    <option>Seleccionar...</option>
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
</select> <input type="button" onclick="ver_financiero_mensual();" value="Ver" />
<div id="financiero_mensual"></div>
<script>
function ver_financiero_mensual(){
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/ventas_economico_financiero', true);?>/'+$('#financiero_mesual_mes').val()+'/'+$('#financiero_mensual_ano').val(),
        dataType: 'html',
        success: function(data){
            $('#financiero_mensual').html(data);
        }
    })
}
</script>
