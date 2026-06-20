<style>
    #EditTable td select { width:100%; max-width:100%; box-sizing:border-box; }
</style>
<?php
 if(!$pdf){
 	echo '<ul class="action_bar">
   
    <li class="boton pdf"><a onclick="imprimir();">Imprimir</a></li>
	<li class="boton excel"><a onclick="descargar();">Excel</a></li>
</ul>';
 }
 ?>

<table width="100%" cellspacing="0" id="EditTable" style="width:100%; table-layout: fixed;">
     <?php
 		if($pdf){?>
    		<tr class="titulo">
                <td colspan='8' align='center' style='border: 1px solid black;'><?php echo 'Planificación de Ingresos, repasos y salidas período '.date("d/m/Y",strtotime($_SESSION['primerDia'])).' - '.date("d/m/Y",strtotime($_SESSION['ultimoDia'])).' Fecha Informe: '.date("d/m/Y H:i");?></td>
        
    		</tr>
    <?php } ?>		
    <tr class="titulo">
        <td style='border: 1px solid black;'>ACCION</td>

        
        <td style='border: 1px solid black;'>TITULAR</td>

        <td style='border: 1px solid black;'>DEPARTAMENTO</td>
        <td style='border: 1px solid black; width:5%;'>Q PAX</td>
        <td style='border: 1px solid black; width:5%;'>BB</td>
        <td style='border: 1px solid black;width:32%;'>OBS</td>

        <td style='border: 1px solid black;'>RESPONSABLE</td>
        <td style='border: 1px solid black;'>PRIORIDAD</td>
    </tr>
    <?php 
    
    foreach($reservas as $reservaDia){?>
    	<tr style="font-weight: bold;">
	        <td colspan='8' align='center' style='border: 1px solid black;background-color: #a4a6a6;'><?php echo $reservaDia[0]['dia'];?></td>
	        
	    </tr>
    	<?php foreach($reservaDia as $reserva){?> 
    	
    	
	    	<tr id='<?php echo $reserva['id_reserva'];?>'>
	    	<td style='border: 1px solid black;'><?php echo utf8_encode($reserva['tipo']);?></td>

	       
	        
	        <td style='border: 1px solid black;'><?php echo ($reserva['titular']);?></td>

	        <td style='border: 1px solid black;'><?php echo ($reserva['apartamento']);?></td>
            <td style='border: 1px solid black; text-align:center;'><?php echo intval($reserva['pax']);?></td>
                <td style='border: 1px solid black; text-align:center;'><?php echo intval($reserva['bb']);?></td>
                <td style='border: 1px solid black; word-wrap:break-word; overflow-wrap:break-word; white-space:normal; overflow:hidden;width:32%;'><?php echo (trim($reserva['obs'])!=='') ? nl2br($reserva['obs']) : '&nbsp;';?></td>

	        
	        <?php
 			if(!$pdf){	?>
                <td style='border: 1px solid black;'>
                    <select id="selectResponsable_<?php echo $reserva['id_reserva'];?>_<?php echo $reserva['fecha'];?>"
                            onChange="guardarDiaOperacion('<?php echo $reserva['id_reserva'];?>','<?php echo $reserva['fecha'];?>',this,'responsable')">
                        <option value="0">Seleccionar...</option>
                        <?php foreach($empleados as $key => $value){
                            $selected = ($reserva['responsable']==$key)?"selected='selected'":"";
                            ?>
                            <option value="<?php echo $key;?>" <?php echo $selected;?>><?php echo $value;?></option>
                        <?php }?>
                    </select>
                </td>
                <td style='border: 1px solid black;'>
                    <select id="selectPrioridad_<?php echo $reserva['id_reserva'];?>_<?php echo $reserva['fecha'];?>"
                            onChange="guardarDiaOperacion('<?php echo $reserva['id_reserva'];?>','<?php echo $reserva['fecha'];?>',this,'prioridad')">
                        <option value="0">-</option>
                        <?php for($p=1;$p<=3;$p++){
                            $selP = ($reserva['prioridad']==$p)?"selected='selected'":"";
                            ?>
                            <option value="<?php echo $p;?>" <?php echo $selP;?>><?php echo $p;?></option>
                        <?php }?>
                    </select>
                </td>
	         <?php } 
	         else{
	         ?>
                 <td style='border: 1px solid black;'>
                     <?php foreach($empleados as $key => $value){ if($reserva['responsable']==$key){ echo $value; } } ?>
                 </td>
                 <td style='border: 1px solid black; text-align:center;'><?php echo ($reserva['prioridad']>0)?$reserva['prioridad']:'';?></td>
	         <?php } 
	         
	         ?>
	    	</tr>
    	
	    <?php //print_r($reserva);
	    } 
    }
 		if($pdf){?>
    		<tr class="titulo">
        		<td colspan='8' style='border: 1px solid black;color: #fb061c;'><?php echo '* Tenga presente que este informe es parcial y no incluye reservas cargadas en el sistema posteriormente a la hora en la que se emitió este informe.';?></td>
        
    		</tr>
    <?php } ?>	    
</table>

<script>
function imprimir(){
	var strDesde = $('#desde').val().split("/"); 
	var desde = strDesde[2]+'-'+strDesde[1]+'-'+strDesde[0];	
	var strHasta = $('#hasta').val().split("/"); 
	var hasta = strHasta[2]+'-'+strHasta[1]+'-'+strHasta[0];	
	
	if ((desde!='undefined-undefined-')&&(hasta!='undefined-undefined-')) {
    	document.location = "<?php echo $this->Html->url('/informes/ventas_semanal', true);?>/"+desde+"/"+hasta+"/1";
    	}
    
}
<?php
//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_ventas_informe_operaciones").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
function descargar(){
		var strDesde = $('#desde').val().split("/"); 
		var desde = strDesde[2]+'-'+strDesde[1]+'-'+strDesde[0];	
		var strHasta = $('#hasta').val().split("/"); 
		var hasta = strHasta[2]+'-'+strHasta[1]+'-'+strHasta[0];	
		
		if ((desde!='undefined-undefined-')&&(hasta!='undefined-undefined-')) {
	   
		    createWindow('w_ventas_informe_operaciones_descargar','Descargar','<?php echo $this->Html->url('/informes/exportarOperacionesSemanal', true);?>/'+desde+'/'+hasta,'430','300');
		    setTimeout('dhxWins.window("w_ventas_informe_operaciones_descargar").close()', 2000);
	    }
	
}
$('#EditTable tr').dblclick(function () {
     var id = $(this).attr('id');
     if(id){
     	createWindow("w_reservas_view","Ver reserva","<?php echo $this->Html->url('/reservas/editar', true);?>/"+id+"/2","630","600");
     }
});

function guardarDiaOperacion(id_reserva, fecha, select, campo){
    var valor = $('#'+select.id).val();
    $.ajax({
        url: '<?php echo $this->Html->url('/reservas/guardar_dia_operacion', true);?>',
        type: 'POST',
        dataType: 'json',
        data: {'id_reserva': id_reserva, 'fecha': fecha, 'campo': campo, 'valor': valor}
    });
}

</script>