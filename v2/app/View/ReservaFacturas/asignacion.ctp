<?php
//abrir ventanas
	$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_reserva_factura_exportar_descargar").getPosition();
    xpos = position[0];
    ypos = position[1];
	');
?>
<script>
	
    function importar(url,form_data,ventana){
        
    	$("#botonGuardar").hide();
    	$("#botonGuardarError").show();
    	
        $('.error-message').remove();
      	
      	var file_data = $('#ReservaFacturaExcel').prop('files')[0];   
		var facturacionBeta=0;
		if( $('#ReservaFacturaBeta').prop('checked') ) {
		    facturacionBeta=1;
		}
		
		var formData = new FormData();
		formData.append('ReservaFacturaExcel',file_data);
		formData.append('PuntoVenta',$('#ReservaFacturaPuntoVentaId').val());
		formData.append('FacturacionBeta',facturacionBeta);
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
        	cache: false,
        	contentType: false,
        	processData: false,
            data: formData,
            success: function(data) {
               
                
                
		    	
		    	
                if(data.resultado == 'ERROR'){
                    
                    
                    $.each(data.detalle,function(model,items){ 
                        $.each(items,function(item,error){
                            var campo = new String(item).split("_");
                            if(campo.length > 0){
                                var div_id = "";
                                $.each(campo, function(x,palabra){
                                    div_id += palabra.charAt(0).toUpperCase() + palabra.slice(1);
                                });                    
                            }
                            $('#'+model+div_id).after('<div class="error-message">'+error+'</div>');
                            
                        })
                    })
                    
                }
                else{
                	createWindow('w_reserva_factura_exportar_descargar','Descargar','<?php echo $this->Html->url('/reserva_facturas/exportar', true);?>/'+data.detalle,'430','300');
    				setTimeout('dhxWins.window("w_reserva_factura_exportar_descargar").close()', 2000);
                	
                }
                $("#botonGuardar").show();
		    	$("#botonGuardarError").hide();
                               
            }
        });
    }
    </script>
<?php

//formulario
echo $this->Form->create(null, array('url' => '/reserva_facturas/asignacion','inputDefaults' => (array('div' => 'ym-gbox','type' => 'file'))));


        ?>
        <div class="sectionTitle">Importar facturacion</div>
        
        <?php 
       echo $this->Form->input('punto_venta_id',array('empty' => 'Seleccionar...', 'type'=>'select', 'options' => $puntos_venta));
        echo $this->Form->input('excel',array( 'label' => 'Excel','type'=>'file'));
        echo $this->Form->input('beta',array( 'label' => 'Controla monto BETA','type'=>'checkbox','checked'=>'checked'));
        
        ?>
            <span id="botonGuardar" onclick="importar('<?php echo $this->Html->url('/reserva_facturas/asignacion.json', true);?>',$('form'),false);" class="boton guardar">Procesar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
        	<span id="botonGuardarError" class="boton guardar" style="display:none">Procesando...</span>

   

<?php echo $this->Form->end(); ?>
<div id="logExito"></div>
<div id="logNoExito"></div>