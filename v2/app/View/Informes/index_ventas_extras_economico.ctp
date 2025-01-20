<?php $ano= date('Y');
$currentYear = date("Y"); // Año actual
$startYear = 2011; // Año de inicio del rango
?>

<strong>Informe Economico</strong>:
<select id="economico_mes">
	<option>Seleccionar...</option>
    <?php for ($year = $startYear; $year <= $currentYear; $year++): ?>
        <option value="<?php echo $year; ?>" <?php if ($ano == $year) echo 'selected="selected"'; ?>>
            <?php echo $year; ?>
        </option>
    <?php endfor; ?>
</select> <strong>&nbsp;&nbsp;Entre fechas&nbsp;<input type="text" name="desde" id="desde" class="datepicker">&nbsp;&nbsp;y&nbsp;<input type="text" name="hasta" id="hasta" class="datepicker"></strong>

<input type="button" onclick="ver_economico();" value="Ver" /> <span id="cargando" style="display:none;">Cargando ...</span>
<div id="informe_economico"></div>
<script>
function ver_economico(){
	var strDesde = $('#desde').val().split("/");
	var Fecha1 = new Date(parseInt(strDesde[2]),parseInt(strDesde[1]-1),parseInt(strDesde[0]));
	var strHasta = $('#hasta').val().split("/");
	var Fecha2 = new Date(parseInt(strHasta[2]),parseInt(strHasta[1]-1),parseInt(strHasta[0]));
	if(!isNaN(strHasta[2])){


		if(Fecha1>Fecha2){
			alert('La fecha Hasta tiene que ser posterior a la fecha Desde');
			return false;
		}
	}



	var desde = strDesde[2]+'-'+strDesde[1]+'-'+strDesde[0];

	var hasta = strHasta[2]+'-'+strHasta[1]+'-'+strHasta[0];
    $('#cargando').show();
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/ventas_extras', true);?>/'+$('#economico_mes').val()+'/'+desde+'/'+hasta,
        dataType: 'html',
        success: function(data){
            $('#cargando').hide();
            $('#informe_economico').html(data);
        }
    })
}

$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });
 $("#economico_mes").change(function(){
 		if($('#economico_mes').val()!='Seleccionar...'){
        	$('#desde').val('01/01/'+$('#economico_mes').val());
        }
        $('#hasta').val('');
    });
 /*$("#economico_mes").change();*/
</script>
