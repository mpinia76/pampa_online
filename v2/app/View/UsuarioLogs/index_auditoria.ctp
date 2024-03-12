
<ul class="action_bar">
	
	<li onclick="ver_log()" class="boton users">&nbspConsulta de interacciones</li>
	<li onclick="ver_auditoria()" class="boton users">&nbspControl de logueos</li>
	
</ul>

       
        
    
 
<div id="informe_operaciones"></div>
<script>
function ver_log(){
		
	    $.ajax({
	        url: '<?php echo $this->Html->url('/usuario_logs/index', true);?>',
	        dataType: 'html',
	        success: function(data){
	            
	            $('#informe_operaciones').html(data);
	        }
	    })
	
    
}

function ver_auditoria(){
		
	    $.ajax({
	        url: '<?php echo $this->Html->url('/usuario_auditorias/index', true);?>',
	        dataType: 'html',
	        success: function(data){
	            
	            $('#informe_operaciones').html(data);
	        }
	    })
	
    
}

function ver_logs(){

    var desde='';
    var hasta='';
    if ($('#desde').val() && $('#hasta').val()){
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
    }

    $('#cargando').show();
    $.ajax({
        url: '<?php echo $this->Html->url('/usuario_logs/index', true);?>/'+desde+'/'+hasta,
        dataType: 'html',
        success: function(data){
            $('#cargando').hide();
            $('#informe_logs').html(data);
        }
    })
}
function ver_auditorias(){

    var desde='';
    var hasta='';
    if ($('#desdeA').val() && $('#hastaA').val()){
        var strDesde = $('#desdeA').val().split("/");
        var Fecha1 = new Date(parseInt(strDesde[2]),parseInt(strDesde[1]-1),parseInt(strDesde[0]));
        var strHasta = $('#hastaA').val().split("/");
        var Fecha2 = new Date(parseInt(strHasta[2]),parseInt(strHasta[1]-1),parseInt(strHasta[0]));
        if(!isNaN(strHasta[2])){


            if(Fecha1>Fecha2){
                alert('La fecha Hasta tiene que ser posterior a la fecha Desde');
                return false;
            }
        }



        var desde = strDesde[2]+'-'+strDesde[1]+'-'+strDesde[0];

        var hasta = strHasta[2]+'-'+strHasta[1]+'-'+strHasta[0];
    }

    $('#cargando').show();
    $.ajax({
        url: '<?php echo $this->Html->url('/usuario_auditorias/index', true);?>/'+desde+'/'+hasta,
        dataType: 'html',
        success: function(data){
            $('#cargandoA').hide();
            $('#informe_auditoria').html(data);
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
