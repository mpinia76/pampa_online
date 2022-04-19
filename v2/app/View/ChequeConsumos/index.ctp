<?php
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
            $("#dataTable tr").click(function(e){
                $("#dataTable tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
            var montos = oTable.fnGetColumnData(11);
            
             if(montos.length > 0){
                
                $("#monto_container").html("$"+montos);
             }else{
                $("#monto_container").html("$0");
             }
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/cheque_consumos/dataTable2/pendiente', true).'",
        //"bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            {"sType": "date-uk"},
            {"sType": "date-uk"},
            {"bSortable": false},
            null,
            null,
            null,
            null,
           null,
           null,
            {"bSortable": false},
            {"bSortable": false},
            {"bVisible": false }
        ]
    });
     $("#filter_estado").change( function () { 
    
    oTable.fnFilter(this.value); });
    $("#confirm_box").jqm();
');
$this->Js->buffer('
   
    

    $("#filter_fecha").change(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",2);
	    }
	    else{
	        oTable.fnFilter($(this).val(),2);
	    }
    });

    $("#filter_fechaDebitado").change(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",3);
	    }
	    else{
        	oTable.fnFilter($(this).val(),3);
        	}
    });


    $("#filter_mes").change(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",4);
	    }
	    else{
       		oTable.fnFilter($(this).val(),4);
       		}
    });
    $("#filter_banco").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",5);
	    }
	    else{
       		oTable.fnFilter($(this).val(),5);
       		}
    });
    $("#filter_cuenta").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",6);
	    }
	    else{
       		oTable.fnFilter($(this).val(),6);
       		}
    });
    $("#filter_chequera").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",7);
	    }
	    else{
       		oTable.fnFilter($(this).val(),7);
       		}
    });
    $("#filter_numero").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",8);
	    }
	    else{
       		oTable.fnFilter($(this).val(),8);
       		}
    });
    $("#filter_titular").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",9);
	    }
	    else{
       		oTable.fnFilter($(this).val(),9);
       		}
    });
    $("#filter_concepto").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",10);
	    }
	    else{
       		oTable.fnFilter($(this).val(),10);
       		}
    });
	$("#filter_monto").keyup(function(){
    	if( $(this).val()==""){
  
    	oTable.fnFilter(":-:-@",11);
	    }
	    else{
       		oTable.fnFilter($(this).val(),11);
       		}
    });
');
//extra libreria para agregar filtro de fecha
echo $this->Html->script('dataTables.dateSort', array('block' => 'extra_scripts'));
//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cheque_consumo").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<div id="confirm_box" class="jqWindow">
    <p class="titulo">Desea confirmar la acreditacion?</p>
    Seleccione la fecha de acreditacion: &nbsp; <input class="datepicker" id="fecha_acreditado" value="<?php echo date('d/m/Y'); ?>" />
    <div class="error-message"></div>
    <span onclick="acreditar();" class="boton guardar">Confirmar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
    <p align="center"><a onclick="$('#confirm_box').jqmHide();">cancelar</a></p>
</div>

<ul class="action_bar">
	<li onclick="actualizar();" class="boton actualizar">Actualizar</li>
    <?php if($permisoEditar){ ?><li class="boton editar"><a onclick="editar();">Editar</a></li><?php  } ?>
    
    <?php if($permisoAgregar){ ?><li class="boton agregar"><a onclick="agregar();">Extraviados y anulados</a></li><?php  } ?>
    <?php if($permisoReemplazar){ ?><li class="boton editar"><a onclick="reemplazar();">Reemplazar</a></li><?php  } ?>
    <?php  if($permisoAprobar){ ?><li class="boton autorizar"><a onclick="aprobar();">  Confirmar d&eacute;bito<img id="loading" src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" style="display:none" /></a></li><?php  } ?>
    <?php  if($permisoAprobar){ ?><li class="boton anular"><a onclick="anular();">  Anular d&eacute;bito<img id="loading1" src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" style="display:none" /></a></li><?php  } ?>
   
</ul>
<?php 
    echo $this->Form->create(false, array('class' => 'form-inline'));

   
   

   
   
   echo $this->Form->input('desde',array('label' => false,'placeholder' => 'Fecha Desde', 'class' => 'datepicker', 'type' => 'text','style' => 'float: left; display:inline;'));
   echo $this->Form->input('hasta',array('label' => false,'placeholder' => 'Fecha Hasta', 'class' => 'datepicker', 'type' => 'text','style' => 'float: left; display:inline;'));
   
echo $this->Form->end('Cargar');
?>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
    	<tr>
            <th width="50">Id</th>
            
            <!-- Campo Orden -->
            <th width="80">
                <input type="text" style="width: 90%;" id="filter_fecha" class="datepicker"  value="<?php echo $_SESSION["chequeConsumoFechaSearch"]?>"/>
            </th>
            <th width="80">
                <input type="text" style="width: 90%;" id="filter_fechaDebitado" class="datepicker"  value="<?php echo $_SESSION["chequeConsumoFechaDebitadoSearch"]?>"/>
            </th>
			<th width="50">
				<?php
   
		    if(($_SESSION["chequeConsumoMesSearch"])){
		     	
				switch ($_SESSION["chequeConsumoMesSearch"]) {
					case '01':
					 	$selectSearchMes1=' selected="selected" ';
					break;
					case '02':
					 	$selectSearchMes2=' selected="selected" ';
					break;
					case '03':
					 	$selectSearchMes3=' selected="selected" ';
					break;
					case '04':
					 	$selectSearchMes4=' selected="selected" ';
					break;
					case '05':
					 	$selectSearchMes5=' selected="selected" ';
					break;
					case '06':
					 	$selectSearchMes6=' selected="selected" ';
					break;
					case '07':
					 	$selectSearchMes7=' selected="selected" ';
					break;
					case '08':
					 	$selectSearchMes8=' selected="selected" ';
					break;
					case '09':
					 	$selectSearchMes9=' selected="selected" ';
					break;
					case '10':
					 	$selectSearchMes10=' selected="selected" ';
					break;
					case '11':
					 	$selectSearchMes11=' selected="selected" ';
					break;
					case '12':
					 	$selectSearchMes12=' selected="selected" ';
					break;
					
				}
			}
			
			
			
		     ?>
                <select id="filter_mes" style="width:90px;">
            <option value="" >Todos</option>
           
            <option value="01" <?php echo $selectSearchEstado1;?>>Enero</option>
            <option value="02" <?php echo $selectSearchEstado2;?>>Febrero</option>
            <option value="03" <?php echo $selectSearchEstado3;?>>Marzo</option>
            <option value="04" <?php echo $selectSearchEstado4;?>>Abril</option>
            <option value="05" <?php echo $selectSearchEstado5;?>>Mayo</option>
            <option value="06" <?php echo $selectSearchEstado6;?>>Junio</option>
            <option value="07" <?php echo $selectSearchEstado7;?>>Julio</option>
            <option value="08" <?php echo $selectSearchEstado8;?>>Agosto</option>
            <option value="09" <?php echo $selectSearchEstado9;?>>Septiembre</option>
            <option value="10" <?php echo $selectSearchEstado10;?>>Octubre</option>
            <option value="11" <?php echo $selectSearchEstado11;?>>Noviembre</option>
            <option value="12" <?php echo $selectSearchEstado12;?>>Diciembre</option>
            
            
           
    </select>
                
            </th>
            <th width="100">
            	<input type="text" style="width: 90%;" id="filter_banco" value="<?php echo $_SESSION["chequeConsumoBancoSearch"]?>"/>
            </th>
            <th width="150">
            	<input type="text" style="width: 90%;" id="filter_cuenta" value="<?php echo $_SESSION["chequeConsumoCuentaSearch"]?>"/>
            </th>
            <th width="30">
            	<input type="text" style="width: 90%;" id="filter_chequera" value="<?php echo $_SESSION["chequeConsumoChequeraSearch"]?>"/>
            </th>
            <th width="100">
            	<input type="text" style="width: 90%;" id="filter_numero" value="<?php echo $_SESSION["chequeConsumoNumeroSearch"]?>"/>
            </th>
            <th width="150">
            	<input type="text" style="width: 90%;" id="filter_titular" value="<?php echo $_SESSION["chequeConsumoTitularSearch"]?>"/>
            </th>
            <th width="150">
            	<input type="text" style="width: 90%;" id="filter_concepto" value="<?php echo $_SESSION["chequeConsumoConceptoSearch"]?>"/>
            </th>
            <th width="50">
            	<input type="text" style="width: 90%;" id="filter_monto" value="<?php echo $_SESSION["chequeConsumoMontoSearch"]?>"/>
            </th>
           
            <th width="100">
            <?php
   
		    if(($_SESSION["chequeConsumoSearchEstado"])){
		     	
				switch ($_SESSION["chequeConsumoSearchEstado"]) {
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
					case 'EST_5':
					 	$selectSearchEstado5=' selected="selected" ';
					break;
					case 'EST_6':
					 	$selectSearchEstado6=' selected="selected" ';
					break;
					case 'EST_7':
					 	$selectSearchEstado7=' selected="selected" ';
					break;
					case 'EST_8':
					 	$selectSearchEstado8=' selected="selected" ';
					break;
				}
			}
			else{
				$selectSearchEstado3=' selected="selected" ';
			}
			
			
		     ?>
                <select id="filter_estado" style="width:150px;">
            <option value="EST_1" <?php echo $selectSearchEstado1;?>>Todos</option>
            <!-- <option value="EST_2" <?php echo $selectSearchEstado2;?>>Pendiente</option> -->
            <option value="EST_6" <?php echo $selectSearchEstado6;?>>Anulado</option>
            <option value="EST_7" <?php echo $selectSearchEstado7;?>>Extraviado</option>
            <option value="EST_4" <?php echo $selectSearchEstado4;?>>Debitado</option>
            <option value="EST_3" <?php echo $selectSearchEstado3;?>>Pendiente</option>
            <option value="EST_8" <?php echo $selectSearchEstado8;?>>Reemplazado</option>
            <option value="EST_5" <?php echo $selectSearchEstado5;?>>Vencido</option>
            
           
    </select>
            </th>
           
        </tr>
        <tr>
            <th width="50">Id</th>
            
            <th width="80">Fecha</th>
            <th width="80">Fecha debitado</th>
            <th width="50">Mes</th>
            <th width="100">Banco</th>
            <th width="150">Cuenta</th>
            <th width="30">Chequera</th>
            <th width="100">Numero</th>
            <th width="150">Titular</th>
            <th width="150">Concepto</th>
            <th width="50">Monto</th>
            <th width="100">Estado</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<script>
function add(){
	createWindow('w_cheque_consumo_add','Agregar cheque','cheque_consumo.am.php','600','400'); //nombre de los divs
}

function editar_old(){
	var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro.');
    }else{
    
    	var data = oTable.fnGetData(row[0]);
		createWindow('w_cheque_consumo_edit','Editar cheque','cheque_consumo.am.php?dataid='+data[0],'600','400'); //nombre de los divs
		refreshOnClose('w_cheque_consumo_edit');    	
	}
}

function editar(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro.');
    }else{
        var data = oTable.fnGetData(row[0]);
       
        		
		createWindow("w_cheque_consumo_edit","Editar cheque","<?php echo $this->Html->url('/cheque_consumos/editar', true);?>/"+data[0],"450","300");
		refreshOnClose('w_cheque_consumo_edit');   
	        	
        	
        
    }
}


function reemplazar(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro.');
    }else{
        var data = oTable.fnGetData(row[0]);
        var estado = data[11];
       if((estado.indexOf('Debitado') != -1)){
        	 alert('El cheque ya se encuentra debitado.');
        }
        else{
	         var concepto = data[9];
	        if(concepto.indexOf('Anulado') == -1){
	        	if(concepto.indexOf('Extraviado') == -1){
	        		
	        		createWindow("w_cheque_consumo_reemplazar","Reemplazar cheque","<?php echo $this->Html->url('/cheque_consumos/reemplazar', true);?>/"+data[0],"450","475");
	        		refreshOnClose('w_cheque_consumo_reemplazar');   
		        	
	        	}
	        	else{
	        		if(confirm("Verificar que el cheque no ha sido debitado de la cuenta de que le da origen  \n \n Continuar?")) {
		        		createWindow("w_cheque_consumo_reemplazar","Reemplazar cheque","<?php echo $this->Html->url('/cheque_consumos/reemplazar', true);?>/"+data[0],"450","475");
		        		refreshOnClose('w_cheque_consumo_reemplazar');   
		        	}
	        	}
	        	 	
	        }
	        else{
	      		alert('No es posible reemplazar un cheque que se encuentra anulado.');
	      	}
	    }
        
    }
}

function agregar(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        createWindow("w_cheque_consumo_add","Extraviados y anulados","<?php echo $this->Html->url("/chequera_cheques/agregar", true);?>","870","350");
    }else{
       
        var data = oTable.fnGetData(row[0]);
        var estado = data[9];
        if((estado.indexOf('Anulado') != -1)|| (estado.indexOf('Extraviado') != -1)){
         
        
      		createWindow("w_cheque_consumo_add","Extraviados y anulados","<?php echo $this->Html->url("/chequera_cheques/editar", true);?>/"+data[0],"430","280");
      	}
      	else{
      		alert('Operacion no permitida.');
      		location.reload();
      	}
       
    }
     refreshOnClose('w_cheque_consumo_add');    	
}

function aprobar(){
	
	var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro.');
    }else{
    	var data = oTable.fnGetData(row[0]);
    	
    	 var concepto = data[9];
    	 var estado = data[11];
        if((concepto.indexOf('Anulado') != -1)){
        	 alert('El cheque se encuentra en estado anulado.');
        }
        else if((concepto.indexOf('Extraviado') != -1)){
        	 alert('El cheque se encuentra en estado extraviado.');
        }
        else if((estado.indexOf('Vencido') != -1)){
        	 alert('El cheque seleccionado se encuentra vencido para el cobro.');
        }
        else if((concepto.indexOf('Reemplazado') != -1)){
        	 alert('El cheque se encuentra en estado reemplazado.');
        }
        else{
	    	dataid = data[0];
			var datos = ({
				'id' : dataid,
				'tabla' : 'cheque_consumo'
			});
			
			$.ajax({
				beforeSend: function(){
					$('#loading').show();
				},
				data: datos,
				url: '../../functions/checkDebitado.php',
				success: function(data) {
				
					if(data == 'si'){		
						if(confirm("El cheque ya fue debitado  \n \n Desea continuar para cambiar la fecha de debito del cheque?")) {
							createWindow('w_cheque_consumo_debitar','Debitar cheque','cheques_debitar.php?actualizar=1&dataid='+dataid,'600','200'); //nombre de los divs
						}
					}else{
						createWindow('w_cheque_consumo_debitar','Debitar cheque','cheques_debitar.php?dataid='+dataid,'600','200'); //nombre de los divs
						
					}
					$('#loading').hide();
					
				}
			});
		}
	}
}

function anular(){
	
	var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro.');
    }else{
    	var data = oTable.fnGetData(row[0]);
    	dataid = data[0];
		var datos = ({
			'id' : dataid,
			'tabla' : 'cheque_consumo'
		});
		
		$.ajax({
			beforeSend: function(){
				$('#loading1').show();
			},
			data: datos,
			url: '../../functions/checkDebitado.php',
			success: function(data) {
			
				if(data == 'si'){		
					if(confirm("Anular el debito?")) {
						
						$.ajax({
							data: datos,
							url: '../../cheque_anular_debito.php',
							success: function(data) {
								actualizar();
								/*mygrid.clearAll();
								mygrid.load("cheques_movimientos.json.php?debitado="+estado,"json");*/
					
								
								
							}
						});
					}
				}else{
					alert('No fue debitado.');
					
				}
				
				$('#loading1').hide();	
			}
		});
		
		
	}
}

    





function actualizar(){
	
	//$("#data_search").val('');
	$("#filter_estado").val('EST_3');
	oTable.fnFilter(":-:-@@@");
	$("#filter_fecha").val('');
	oTable.fnFilter(":-:-@@@",2);
	$("#filter_fechaDebitado").val('');
	oTable.fnFilter(":-:-@@@",3);
	$("#filter_mes").val('');
	oTable.fnFilter(":-:-@@@",4);
	$("#filter_banco").val('');
	oTable.fnFilter(":-:-@@@",5);
	$("#filter_cuenta").val('');
	oTable.fnFilter(":-:-@@@",6);
	$("#filter_chequera").val('');
	oTable.fnFilter(":-:-@@@",7);
	$("#filter_numero").val('');
	oTable.fnFilter(":-:-@@@",8);
	$("#filter_titular").val('');
	oTable.fnFilter(":-:-@@@",9);
	$("#filter_concepto").val('');
	oTable.fnFilter(":-:-@@@",10);
	$("#filter_monto").val('');
	oTable.fnFilter(":-:-@@@",11);
}
</script>