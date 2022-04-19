
<ul class="action_bar">
	
	
	
	<li class="filtro">Buscar <input onkeyup="ver_listado()" id="data_search" type="text" with="10"/></li>
	
</ul>

       
        
    
 
<div id="informe_operaciones"></div>
<script>


function ver_listado(){
		
	    $.ajax({
	        url: '<?php echo $this->Html->url('/chequera_cheques/getCheques/'.$chequera_id, true);?>/0/'+$('#data_search').val(),
	        dataType: 'html',
	        success: function(data){
	            
	            $('#informe_operaciones').html(data);
	        }
	    })
	
    
}
ver_listado();

</script>
