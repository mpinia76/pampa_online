
<ul class="action_bar">
	
	<li onclick="ver_extra_economico()" class="boton torta">&nbsp&nbspInforme economico</li>
	<li onclick="ver_listado()" class="boton users">&nbspListado de extras</li>
	
</ul>

       
        
    
 
<div id="informe_operaciones"></div>
<script>
function ver_extra_economico(){
		
	    $.ajax({
	        url: '<?php echo $this->Html->url('/informes/index_ventas_extras_economico', true);?>',
	        dataType: 'html',
	        success: function(data){
	            
	            $('#informe_operaciones').html(data);
	        }
	    })
	
    
}

function ver_listado(){
		
	    $.ajax({
	        url: '<?php echo $this->Html->url('/informes/index_ventas_extras_listado', true);?>',
	        dataType: 'html',
	        success: function(data){
	            
	            $('#informe_operaciones').html(data);
	        }
	    })
	
    
}
<?php
/*switch ($_SESSION['paginaOperaciones']) {
 	case 1:?>
 		ver_diario(); 
 	<?php break;
 	case 2:?>
 		ver_semanal(); 
 	<?php break;
 	}*/
?>

</script>
