<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php echo $this->Html->charset(); ?>
    <title><?php echo $title_for_layout; ?></title>
    <?php echo $this->Html->css(array('jquery-ui','yaml/core/base.min','form')); ?>
    <?php echo $this->Html->script(array('jquery','jquery-ui','jquery.ui.datepicker-es','dhtml/dhtmlxcommon','dhtml/dhtmlxcontainer','dhtml/dhtmlxwindows')); ?>
    <?php echo $this->fetch('script'); ?>
    <?php echo $this->Js->writeBuffer(); ?>
    <script>
    function guardar(url,form_data,ventana){
    	if($("#botonGuardarError")!=null){
    		$("#botonGuardar").hide();
    		$("#botonGuardarError").show();
    	}
    	
        $('#loading_save').show();
        $('.error-message').remove();
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: form_data,
            success: function(data) {
                $('#loading_save').hide();
                if($("#botonGuardarError")!=null){
		    		$("#botonGuardar").show();
		    		$("#botonGuardarError").hide();
		    	}
                if(data.resultado == 'ERROR'){
                    alert(data.mensaje);
                    location.href="#";
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
                }else{
                    alert(data.mensaje);
                    location.reload();
                    if(ventana){
                    	
                        var dhxWins = parent.dhxWins;
                        dhxWins.window(ventana.id).attachURL(ventana.url);
                    }
                    
                    if(dhxWins.window('w_<?php echo $this->params['controller']?>')){
                        
                    }
                }                
            }
        });
    }
    function guardarSinRefrescar(url,form_data,ventana){
    	if($("#botonGuardarError")!=null){
    		$("#botonGuardar").hide();
    		$("#botonGuardarError").show();
    	}
    	
        $('#loading_save').show();
        $('.error-message').remove();
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: form_data,
            success: function(data) {
                $('#loading_save').hide();
                if($("#botonGuardarError")!=null){
		    		$("#botonGuardar").show();
		    		$("#botonGuardarError").hide();
		    	}
                if(data.resultado == 'ERROR'){
                    alert(data.mensaje);
                    location.href="#";
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
                }else{
                    alert(data.mensaje);
                    
                    if(ventana){
                    	
                        var dhxWins = parent.dhxWins;
                        dhxWins.window(ventana.id).attachURL(ventana.url);
                    }
                    
                    if(dhxWins.window('w_<?php echo $this->params['controller']?>')){
                        
                    }
                }                
            }
        });
    }
    function guardarCerrando(url,form_data,ventana,cerrar){
    	if($("#botonGuardarError")!=null){
    		$("#botonGuardar").hide();
    		$("#botonGuardarError").show();
    	}
    	
        $('#loading_save').show();
        $('.error-message').remove();
        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: form_data,
            success: function(data) {
                $('#loading_save').hide();
                if($("#botonGuardarError")!=null){
		    		$("#botonGuardar").show();
		    		$("#botonGuardarError").hide();
		    	}
                if(data.resultado == 'ERROR'){
                    alert(data.mensaje);
                    location.href="#";
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
                }else{
                    alert(data.mensaje);
                    
                    if(ventana){
                    	
                        var dhxWins = parent.dhxWins;
                        dhxWins.window(ventana.id).attachURL(ventana.url);
                        dhxWins.window(cerrar).close()
                    }
                    
                   
                }                
            }
        });
    }
    var xpos, ypos, dhxWins, position;
    function createWindow(id,titulo,url,w,h) {
        xpos = xpos+20;
        ypos = ypos+20;

        if(ypos>200){ ypos = 5; }
        if(xpos>300){ xpos = 50; }

        w1 = dhxWins.createWindow(id, xpos, ypos, w, h);
        w1.setText(titulo);
        w1.attachURL(url);
    }
    </script>
</head>
<body>
    <div class="ym-wrapper <?php echo $this->params['controller']."_".$this->params['action']?>">
        <?php echo $this->fetch('content'); ?>
    </div>
</body>
</html>