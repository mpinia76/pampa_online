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
            "sUrl": "'.$this->webroot.'app/webroot/js/dataTables.spanish.txt"
        },
        "fnDrawCallback": function( oSettings ) {
            $("#dataTable tr").click(function(e){
                if(e.shiftKey){
                    $(this).toggleClass("row_selected");
                }else{
                    $("#dataTable tr").removeClass("row_selected");
                    $(this).toggleClass("row_selected");
                }
             });
            $("#dataTable tr").unbind("dblclick").dblclick(function(){
                var data1 = oTable.fnGetData( this );
                if(data1[14]=="Acreditado"){
		        	alert("Transaccion acreditada - No se pueden realizar cambios");
		        }
		        else{
		        	createWindow("w_reservas_view_cobro","Detalles","'.$this->Html->url('/reserva_cobros/detalle', true).'/"+data1[1],"440","400");
                	refreshOnClose("w_reservas_view_cobro");
		        }
            });
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/cobro_tarjetas/dataTable', true).'",
        "bDeferRender": true,
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            {"bVisible": false },
            {"sType": "date-uk"},
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            {"bSortable": false},
            null,
            {"bSortable": false}
        ]
    });
    $("#filter_estado").change( function () { 
    
    oTable.fnFilter(this.value); });
');

$this->Js->buffer('
   
    

    $("#filter_locacion").keyup(function(){
    	if( $(this).val()==""){
  			
    	oTable.fnFilter(":-:-@",3);
	    }
	    else{
	        oTable.fnFilter($(this).val(),3);
	    }
    });

    $("#filter_marca").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",4);
	    }
	    else{
        	oTable.fnFilter($(this).val(),4);
        	}
    });


    $("#filter_comercio").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",5);
	    }
	    else{
       		oTable.fnFilter($(this).val(),5);
       		}
    });
    
    $("#filter_cupon").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",6);
	    }
	    else{
       		oTable.fnFilter($(this).val(),6);
       		}
    });
    $("#filter_autoriacion").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",7);
	    }
	    else{
       		oTable.fnFilter($(this).val(),7);
       		}
    });
    
    $("#filter_lote").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",8);
	    }
	    else{
       		oTable.fnFilter($(this).val(),8);
       		}
    });
    $("#filter_liquidacion").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",9);
	    }
	    else{
       		oTable.fnFilter($(this).val(),9);
       		}
    });
	$("#filter_titular").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",10);
	    }
	    else{
       		oTable.fnFilter($(this).val(),10);
       		}
    });
    $("#filter_concepto").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",11);
	    }
	    else{
       		oTable.fnFilter($(this).val(),11);
       		}
    });
    $("#filter_monto").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",12);
	    }
	    else{
       		oTable.fnFilter($(this).val(),12);
       		}
    });
    $("#filter_cuotas").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",13);
	    }
	    else{
       		oTable.fnFilter($(this).val(),13);
       		}
    });
');

//extra libreria para agregar filtro de fecha
echo $this->Html->script('dataTables.dateSort', array('block' => 'extra_scripts'));

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cobro_tarjetas").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>
function actualizar(){

	
	var d = new Date();
	var n = d.getFullYear();
	
	$('#desde').val('01/01/'+n);
	$('#hasta').val('31/12/'+n);
	$('#limpiar').val('1');
	$("#indexForm").submit();
	
	
	
}

function editar(){
	var locacion = '';
	var marca = '';
	var comercio = '';
	var okLocacion=1;
	var okMarca=1;
	var okComercio=1;
	var okAcreditada=0;
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
    	 var selected = new Array();
	    $('.row_selected').each(function(e,i){
	    	var data = oTable.fnGetData(i);
	    	selected.push(data[0]);
	    	
	    	if(data[14]=='Acreditado'){
	    		okAcreditada=1;
	        	
	        }
	        else{
		    	if(locacion==''){
		    		locacion = data[3];
		    		
		    	}
		    	else{
		    		if(locacion!=data[3]){
		    			okLocacion=0
		    		}
		    	}
		    	if(marca==''){
		    		marca = data[4];
		    		
		    	}
		    	else{
		    		if(marca!=data[4]){
		    			okMarca=0
		    		}
		    	}
		    	if(comercio==''){
		    		comercio = data[5];
		    		
		    	}
		    	else{
		    		if(comercio!=data[5]){
		    			okComercio=0
		    		}
		    	}
		    	if(!okLocacion || !okMarca || !okComercio){
		        	
		        
		        	alert('En la seleccion multiple deben coincidir la locacion, marca y comercio');
		        	return false;
		        	
		        }
		   }
	        
	       
	    });
	    if(okAcreditada){
	    	alert('Transaccion acreditada - No se pueden realizar cambios');
	    }
	    else{
		    if(selected.length > 1){
		    	if(okLocacion && okMarca && okComercio){
			    	createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/cobro_tarjetas/detalle/', true);?>'+selected.join(','),'440','400');
			       	refreshOnClose('w_reservas_view_cobro');
			    }
		    }
		    else{
		        var data1 = oTable.fnGetData(row[0]);
		        
		        /*$.ajax({
		            url : '<?php echo $this->Html->url('/cobro_tarjetas/controlarAcreditacion', true);?>',
		            type : 'POST',
		            dataType: 'json',
		            data: { 'cobro_tarjeta_id' : data1[0]},
		            success : function(data){
		            	if(data.resultado == 'ERROR'){
		                        alert(data.mensaje+' '+data.detalle);
		                }
		                else{
		                	createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/', true);?>'+data1[1],'440','400');
		        			refreshOnClose('w_reservas_view_cobro');
		                }
		            }
		        });*/
		        if(data1[14]=='Acreditado'){
		        	alert('Transaccion acreditada - No se pueden realizar cambios');
		        }
		        else{
		        	createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/', true);?>'+data1[1],'440','400');
		        	refreshOnClose('w_reservas_view_cobro');
		        }
		    }
		}
        
    }
}
function importar(){
    
      
    createWindow('w_cobro_tarjetas_importar','Importar transacciones','<?php echo $this->Html->url('/cobro_tarjetas/importar/', true);?>','420','300');
    refreshOnClose('w_cobro_tarjetas_importar');    	
        
    
}
function historial(){
    
      
    createWindow('w_historial_importaciones','Historial de importaciones','<?php echo $this->Html->url('/cobro_tarjeta_importacions/index/', true);?>','420','550');
        	
        
    
}
</script>
<ul class="action_bar">
    <li onclick="actualizar();" class="boton actualizar">Actualizar</li>
    <li class="boton editar"><a onclick="editar();">Editar</a></li>
    <li class="boton abonar"><a onclick="importar();">Importar transacciones</a></li>
    <li class="boton abonar"><a onclick="historial();">Historial de importaciones</a></li>
</ul>
<p><b>Filtro campo: Fecha</b></p>

<?php 
    echo $this->Form->create(false, array('class' => 'form-inline'));

   echo $this->Form->hidden('limpiar',array('value' => 0)); 
   
   echo $this->Form->input('desde',array('label' => false,'placeholder' => 'Fecha Desde', 'class' => 'datepicker', 'value' => $_SESSION['cobroTarjetadesde'], 'type' => 'text','style' => 'float: left; display:inline;'));
   echo $this->Form->input('hasta',array('label' => false,'placeholder' => 'Fecha Hasta', 'class' => 'datepicker', 'value' => $_SESSION['cobroTarjetahasta'], 'type' => 'text','style' => 'float: left; display:inline;'));
   
echo $this->Form->end('Cargar');
?>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
    	<tr>
            <th width="50"></th>
            <th width="100"></th>
            <th width="100"></th>
            <!-- Campo Orden -->
            <th width="80">
                <input type="text"  id="filter_locacion" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaLocacionSearch"]?>"/>
            </th>
            <th width="80">
                <input type="text"  id="filter_marca" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaMarcaSearch"]?>"/>
            </th>
			<th width="80">
                <input type="text"  id="filter_comercio" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaComercioSearch"]?>"/>
            </th>
            <th width="80">
            	<input type="text"  id="filter_cupon" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaCuponSearch"]?>"/>
            </th>
            <th width="80">
            	<input type="text"  id="filter_autoriacion" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaAutorizacionSearch"]?>"/>
            </th>
            <th width="50">
            	<input type="text"  id="filter_lote" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaLoteSearch"]?>"/>
            </th>
            <th width="50">
            	<input type="text"  id="filter_liquidacion" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaLiquidacionSearch"]?>"/>
            </th>
            <th width="150">
            	<input type="text"  id="filter_titular" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaTitularSearch"]?>"/>
            </th>
            <th width="150">
            	<input type="text"  id="filter_concepto" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaConceptoSearch"]?>"/>
            </th>
            <th width="50">
            	<input type="text"  id="filter_monto" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaMontoSearch"]?>"/>
            </th>
            <th width="50">
            	<input type="text"  id="filter_cuotas" style="width: 90%;" value="<?php echo $_SESSION["cobroTarjetaCuotasSearch"]?>"/>
            </th>
           
            <th width="100">
            <?php
   
		    if(($_SESSION["cobroTarjetaSearchEstado"])){
		     	
				switch ($_SESSION["cobroTarjetaSearchEstado"]) {
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
            <th width="100">Reserva Cobro Id</th>
            <th width="100">Fecha</th>
            <th width="80">Locacion</th>
            <th width="80">Marca</th>
            <th width="80">Comercio</th>
            <th width="80">Cupon</th>
            <th width="80">Autorizacion</th>
            <th width="50">Lote</th>
            <th width="50">Nro liquidacion</th>
            <th width="150">Titular</th>
            <th width="150">Concepto</th>
            <th width="50">Monto</th>
            <th width="50">Cuotas</th>
            <th width="150">Estado de lote</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
