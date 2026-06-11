<?php 	

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_ventas_informe_operaciones").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>


        <strong>Desde<input type="text" name="desde" id="desde" class="datepicker" value="<?php echo $primerDia;?>"></strong>
        <strong>Hasta<input type="text" name="hasta" id="hasta" class="datepicker" value="<?php echo $ultimoDia;?>"></strong>
        
    
 <input type="button" onclick="ver_informe_semanal();" value="Ver" /> <span id="cargando2" style="display:none;">Cargando ...</span>
<div id="informe_semanal"></div>
<script>



function ver_informe_semanal(){
	var strDesde = $('#desde').val().split("/"); 
	var desde = strDesde[2]+'-'+strDesde[1]+'-'+strDesde[0];	
	var strHasta = $('#hasta').val().split("/"); 
	var hasta = strHasta[2]+'-'+strHasta[1]+'-'+strHasta[0];	
	
	if ((desde!='undefined-undefined-')&&(hasta!='undefined-undefined-')) {
		$('#cargando2').show();
	    $.ajax({
	        url: '<?php echo $this->Html->url('/informes/ventas_semanal', true);?>/'+desde+'/'+hasta,
	        dataType: 'html',
	        success: function(data){
	            $('#cargando2').hide();
	            $('#informe_semanal').html(data);
	        }
	    })
	}
    
}


$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });
/*ver_informe_semanal(); */
</script>
