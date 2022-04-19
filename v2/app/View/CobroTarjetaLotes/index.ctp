<?php
session_start();
//calendario
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');


//dataTables
$this->Js->buffer('
    oTable = $("#dataTable").dataTable( {
        "sDom": "<\"dataTables_top\"i>t<\"dataTables_bottom\"lp>r",
        "bProcessing": true,
        "bServerSide": true,
        "bAutoWidth": false,
        "oLanguage": {
            "sUrl": "/dataTables.spanish.txt"
        },
        
        "fnDrawCallback": function( oSettings ) {
        	$("#dataTable tr").unbind("dblclick").dblclick(function(){
                var data1 = oTable.fnGetData( this );
        		createWindow("w_cobro_tarjetas_detalle","Detalles","'.$this->Html->url('/cobro_tarjetas/index2/', true).'"+data1[1]+"/"+data1[6],"600","400");     
            });
            $("#dataTable tr").click(function(e){
                $("#dataTable tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/cobro_tarjeta_lotes/dataTable', true).'",
        
        "aoColumns": [
            {"bVisible": false },
            {"bVisible": false },
            null,
            null,
            null,
            null,
            null,
            {"bSortable": false},
            {"bSortable": false},
            {"sType": "date-uk"},
            {"sType": "date-uk"},
            {"bSortable": false}
        ]
    });
    /*$("#data_search").keyup( function () { 
    if( this.value==""){
  
    	oTable.fnFilter(":-:-@");
    }
    else{
    	
    	oTable.fnFilter(this.value);
    }
     });*/
    $("#filter_estado").change( function () { 
    
    oTable.fnFilter(this.value); });

   
    $("#cerrar_box").jqm();
    $("#acreditar_box").jqm();
');
$this->Js->buffer('
   
    

    $("#filter_locacion").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",2);
	    }
	    else{
	        oTable.fnFilter($(this).val(),2);
	    }
    });

    $("#filter_marca").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",3);
	    }
	    else{
        	oTable.fnFilter($(this).val(),3);
        	}
    });


    $("#filter_comercio").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",4);
	    }
	    else{
       		oTable.fnFilter($(this).val(),4);
       		}
    });
    $("#filter_cuenta").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",5);
	    }
	    else{
       		oTable.fnFilter($(this).val(),5);
       		}
    });
    $("#filter_liquidacion").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",6);
	    }
	    else{
       		oTable.fnFilter($(this).val(),6);
       		}
    });
    $("#filter_monto").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",7);
	    }
	    else{
       		oTable.fnFilter($(this).val(),7);
       		}
    });
    $("#filter_operaciones").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",8);
	    }
	    else{
       		oTable.fnFilter($(this).val(),8);
       		}
    });
    $("#filter_fechapago").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",9);
	    }
	    else{
       		oTable.fnFilter($(this).val(),9);
       		}
    });
	$("#filter_fechaacreditacion").change(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",10);
	    }
	    else{
       		oTable.fnFilter($(this).val(),10);
       		}
    });
');
//extra libreria para agregar filtro de fecha
echo $this->Html->script('dataTables.dateSort', array('block' => 'extra_scripts'));

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cobro_tarjeta_lotes").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<!--CERRAR LOTE-->
<div id="cerrar_box" class="jqWindow">
    <p class="titulo">Desea confirmar el cierre de lote?</p>
    <strong>Fecha de cierre:</strong> &nbsp; <input class="datepicker" id="fecha_cierre" value="<?php echo date('d/m/Y'); ?>" /> 
    <div class="error-message error_fecha_cierre"></div>
    <span onclick="cerrar_lote();" class="boton guardar">Cerrar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
    <p align="center"><a onclick="location.reload();">cancelar</a></p>
</div>

<!--ACREDITAR LOTE-->
<div id="acreditar_box" class="jqWindow">
    <p class="titulo">Desea acreditar la liquidacion <span id="span_liquidacion"></span>?</p>
    
    <table width="100%">
        <tr>
            <td width="50%"><strong>Fecha de acreditacion:</strong></td>
            <td><span id="span_fecha_acreditacion"></span>
                    <div class="error-message error_fecha_acreditacion"></div></td>
        </tr>
        <tr>
            <td><strong>Monto total:</strong></td>
            <td><span id="monto_total"></span></td>
        </tr>
        <tr>
            <td><strong>Descuentos:</strong></td>
            <td>$<input type="text" size="5" id="descuentos" value="0" onkeyup="updateTotal();" />Dif. de cambio<input type="checkbox"  id="diferencia" onClick="updateTotal();" />
                   <div class="error-message error_descuentos"></div></td>
        </tr>
        <tr>
            <td><strong>Monto a acreditar:</strong></td>
            <td>$<span id="monto_acreditar"></span></td>
        </tr>
    </table>      
    <span onclick="acreditar_lote();" class="boton guardar">Confirmar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
    <p align="center"><a onclick="location.reload();">cancelar</a></p>
</div>

<ul class="action_bar">
	<li onclick="actualizar();" class="boton actualizar">Actualizar</li>
    <!--<li class="boton abonar"><a onclick="open_cerrar_box();">Cerrar</a></li>-->
    <li class="boton abonar"><a onclick="detalle();">Detalles</a></li>
    <li class="boton abonar"><a onclick="open_acreditar_box();">Acreditar</a></li>
    <li class="boton anular"><a onclick="anular();">Revertir acreditacion</a></li>
    <!--<table style="margin-top:-7px;float:right;">
    <tr><td>
    Buscar</td><td> <input id="data_search" type="text" value="<?php echo $_SESSION["cobroTarjetaLoteSearch"]?>"/>
    
    </td>
    <td>
    
    
    
    </tr></table>-->
</ul>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
    	<tr>
            <th width="50">Id</th>
            <th width="50">Id</th>
            <!-- Campo Orden -->
            <th width="80">
                <input type="text" style="width: 90%;" id="filter_locacion" value="<?php echo $_SESSION["cobroTarjetaLoteLocacionSearch"]?>"/>
            </th>
            <th width="80">
                <input type="text" style="width: 90%;" id="filter_marca" value="<?php echo $_SESSION["cobroTarjetaLoteMarcaSearch"]?>"/>
            </th>
			<th width="80">
                <input type="text" style="width: 90%;" id="filter_comercio" value="<?php echo $_SESSION["cobroTarjetaLoteComercioSearch"]?>"/>
            </th>
            <th width="100">
            	<input type="text" style="width: 90%;" id="filter_cuenta" value="<?php echo $_SESSION["cobroTarjetaLoteCuentaSearch"]?>"/>
            </th>
            <th width="50">
            	<input type="text" style="width: 90%;" id="filter_liquidacion" value="<?php echo $_SESSION["cobroTarjetaLoteLiquidacionSearch"]?>"/>
            </th>
            <th width="80">
            	<input type="text" style="width: 90%;" id="filter_monto" value="<?php echo $_SESSION["cobroTarjetaLoteMontoSearch"]?>"/>
            </th>
            <th width="100">
            	<input type="text" style="width: 90%;" id="filter_operaciones" value="<?php echo $_SESSION["cobroTarjetaLoteOperacionesSearch"]?>"/>
            </th>
            <th width="80">
            	<input type="text" style="width: 90%;" id="filter_fechapago" value="<?php echo $_SESSION["cobroTarjetaLoteFechaPagoSearch"]?>"/>
            </th>
            <th width="80">
            	<input type="text" style="width: 90%;" id="filter_fechaacreditacion" class="datepicker" value="<?php echo $_SESSION["cobroTarjetaLoteFechaAcreditacionSearch"]?>"/>
            </th>
           
            <th width="100">
            <?php
   
		    if(($_SESSION["cobroTarjetaLoteSearchEstado"])){
		     	
				switch ($_SESSION["cobroTarjetaLoteSearchEstado"]) {
					case 'EST_1':
					 	$selectSearchEstado1=' selected="selected" ';
					break;
					case 'EST_2':
					 	$selectSearchEstado2=' selected="selected" ';
					break;
					case 'EST_3':
					 	$selectSearchEstado3=' selected="selected" ';
					break;
					case 'EST_4':
					 	$selectSearchEstado4=' selected="selected" ';
					break;
				}
			}
			else{
				$selectSearchEstado3=' selected="selected" ';
			}
			
			
		     ?>
                <select id="filter_estado" style="width:150px;">
            <option value="EST_1" <?php echo $selectSearchEstado1;?>>Todos</option>
            <!-- <option value="EST_2" <?php echo $selectSearchEstado2;?>>Pendiente de cierre</option> -->
            <option value="EST_3" <?php echo $selectSearchEstado3;?>>Pendiente de acreditacion</option>
            <option value="EST_4" <?php echo $selectSearchEstado4;?>>Acreditado</option>
            
           
    </select>
            </th>
           
        </tr>
        <tr>
            <th width="50">Id</th>
            <th width="50">Tarjeta marca id</th>
            <th width="80">Locacion</th>
            <th width="80">Marca</th>
            <th width="80">Comercio</th>
            <th width="100">Cuenta</th>
            <th width="50">Nro Liquidacion</th>
            <th width="80">$ Total</th>
            <th width="100"># Operaciones</th>
            <th width="80">Pago</th>
            <th width="80">Acreditacion</th>
            <th width="100">Estado de lote</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

<script>
function detalle(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data1 = oTable.fnGetData(row[0]);
        
      
        createWindow('w_cobro_tarjetas_detalle','Detalles','<?php echo $this->Html->url('/cobro_tarjetas/index2/', true);?>'+data1[1]+'/'+data1[6],'600','400');
        	
        
    }
}
function open_cerrar_box(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
       
        if((data[11] != 'Pendiente de cierre')){
            alert('Este lote ya se encuentra cerrado');
        }else{
            $('#cerrar_box').jqmShow();
        }
    }
}
function cerrar_lote(){
    var row = $("#dataTable tr.row_selected");
    var data = oTable.fnGetData(row[0]);
    $('#loading_save').show();
    $('.error-message').html('');
    $.ajax({
        url : '<?php echo $this->Html->url('/cobro_tarjeta_lotes/cerrar', true);?>',
        data: {'data' : {'fecha_cierre' : $('#fecha_cierre').val(), 'cerrado_por' : <?php echo $usuario['Usuario']['id']; ?>, 'monto_total' : data[7].replace('$',''), 'numero' : data[6], 'cobro_tarjeta_tipo_id' : data[1]}},
        type: 'POST',
        dataType: 'json',
        success: function(data){
            $('#loading_save').hide();
            if(data.resultado == 'ERROR'){
                $.each(data.detalle,function(index,error){
                    $('.error_'+index).html(error[0]);
                });
            }else{
                document.location.reload();
            }
        }
    })
}
function updateTotal(){
    var monto_total = parseFloat($('#monto_total').html().replace('$',''));
    var descuentos = $('#descuentos').val() == '' ? 0 : parseFloat($('#descuentos').val());
    if( $('#diferencia').prop('checked') ) {
	    descuentos = descuentos * (-1);
	}
    var monto_acreditar = monto_total - descuentos;
    $('#monto_acreditar').html(monto_acreditar);
}
function open_acreditar_box(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[10]){
            alert('Ya se encuentra acreditada')
        }else{
            $('#monto_total').html(data[7]);
            updateTotal();
            $('#span_fecha_acreditacion').html('<input class="datepicker" id="fecha_acreditacion" value="'+data[9]+'" />' );
            $('#span_liquidacion').html(data[6]);
            $('#acreditar_box').jqmShow();
        }
    }
}
function acreditar_lote(){
    var row = $("#dataTable tr.row_selected");
    var data = oTable.fnGetData(row[0]);
   var diferencia=0;
   	if( $('#diferencia').prop('checked') ) {
	    diferencia=1;
	}
    $('#loading_save').show();
    $('.error-message').html('');
    $.ajax({
        url : '<?php echo $this->Html->url('/cobro_tarjeta_lotes/acreditar', true);?>',
        data: {'data' : {'fecha_acreditacion' : $('#fecha_acreditacion').val(), 'acreditado_por' : <?php echo $usuario['Usuario']['id']; ?>, 'id' : data[0], 'descuentos' : $('#descuentos').val(), 'monto_total' : data[7].replace('$',''), 'numero' : data[6], 'cobro_tarjeta_tipo_id' : data[1], 'fecha_pago' : data[9], 'diferencia' : diferencia}},
        type: 'POST',
        dataType: 'json',
        success: function(data){
            $('#loading_save').hide();
            if(data.resultado == 'ERROR'){
                $.each(data.detalle,function(index,error){
                    $('.error_'+index).html(error[0]);
                });
            }else{
                document.location.reload();
            }
        }
    })
}
function anular(){
    var row = $("#dataTable tr.row_selected");
   if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        
        if(data[11] == 'Acreditado'){
        	if(confirm("Revertir acreditacion?")) {
        		
			    $('#loading_save').show();
			    $.ajax({
			        url : '<?php echo $this->Html->url('/cobro_tarjeta_lotes/anular', true);?>',
			        data: {'data' : {'id' : data[0]}},
			        type: 'POST',
			        dataType: 'json',
			        success: function(data){
			            $('#loading_save').hide();
			            if(data.resultado == 'ERROR'){
			                //$('.error-message').html(data.detalle.fecha_acreditado[0]);
			            }else{
			                document.location.reload();
			            }
			        }
			    })
			 }
		}
		else{
			alert('La liquidacion no fue acreditada');
		}
	}
}

function actualizar(){
	
	//$("#data_search").val('');
	$("#filter_estado").val('EST_3');
	oTable.fnFilter(":-:-@@@");
	$("#filter_locacion").val('');
	oTable.fnFilter(":-:-@@@",2);
	$("#filter_marca").val('');
	oTable.fnFilter(":-:-@@@",3);
	$("#filter_comercio").val('');
	oTable.fnFilter(":-:-@@@",4);
	$("#filter_cuenta").val('');
	oTable.fnFilter(":-:-@@@",5);
	$("#filter_liquidacion").val('');
	oTable.fnFilter(":-:-@@@",6);
	$("#filter_monto").val('');
	oTable.fnFilter(":-:-@@@",7);
	$("#filter_operaciones").val('');
	oTable.fnFilter(":-:-@@@",8);
	$("#filter_fechapago").val('');
	oTable.fnFilter(":-:-@@@",9);
	$("#filter_fechaacreditacion").val('');
	oTable.fnFilter(":-:-@@@",10);
}
</script>
