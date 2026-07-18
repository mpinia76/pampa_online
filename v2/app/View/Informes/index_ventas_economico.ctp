<?php
    $ano       = date('Y');   // año actual, preseleccionado
    $anoInicio = 2012;        // primer año disponible
    $anoMax    = $ano + 1;    // año siguiente como máximo (se genera solo cada año)
?>
<table cellpadding="0" cellspacing="0" border="0">
<tr>
	<td><span id="cargando" style="display:none;">Cargando ...</span></td><td align="center"><strong>Informe Ventas</strong>: </td><td align="center"><strong>Cargadas entre fechas</strong>: </td><td align="center"><strong>Categor&iacute;a</strong>: </td><td align="center"><strong>Apartamentos</strong>: </td>
</tr>	
<tr>
	<td><input type="button" onclick="ver_economico();" value="Ver" /> </td><td><select id="economico_mes">
	<option>Seleccionar...</option>
    <?php for($y = $anoInicio; $y <= $anoMax; $y++){ ?>
    <option <?php if($ano == $y){?> selected="selected" <?php } ?>><?php echo $y; ?></option>
    <?php } ?>
</select></td><td><input type="text" name="desde" id="desde" class="datepicker">&nbsp;&nbsp;y&nbsp;<input type="text" name="hasta" id="hasta" class="datepicker"></td><td><select id="ApartamentoCategoriaId">
	<option>Seleccionar...</option>
	<?php foreach($categorias as $categoria){ ?>
	<option value="<?php echo $categoria['Categoria']['id']?>"><?php echo $categoria['Categoria']['categoria']?></option>
    <?php } ?>
</select></td><td><select name="InformeApartamentoId[]" multiple="multiple" style="height:40px; width:200px; margin:2px 0px" id="InformeApartamentoId">
	
	
</select></td>
</tr>	
</table>


 


        
        
    
 
<div id="informe_economico"></div>
<script>
$('#ApartamentoCategoriaId').change(function(){
		
    if($(this).val()!=''){
        $.ajax({
            url: '<?php echo $this->Html->url('/informes/getApartamentos/', true);?>'+$(this).val()+'/'+$('#economico_mes').val(),
            dataType: 'html',
            
            success: function(data){
                $('#InformeApartamentoId').html(data);
            }
        });
    }else{
         $('#InformeApartamentoId').html('');
    }
})
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
        url: '<?php echo $this->Html->url('/informes/ventas_economico', true);?>/'+$('#economico_mes').val()+'/'+desde+'/'+hasta+'/'+$('#ApartamentoCategoriaId').val()+'/'+$('#InformeApartamentoId').val(),
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
        $('#ApartamentoCategoriaId').val('');
        $('#ApartamentoCategoriaId').change();
    });
 	$("#desde").change(function(){
 		
        $('#ApartamentoCategoriaId').val('');
        $('#ApartamentoCategoriaId').change();
    });
    $("#hasta").change(function(){
 		
        $('#ApartamentoCategoriaId').val('');
        $('#ApartamentoCategoriaId').change();
    });
 $("#economico_mes").change();
</script>
