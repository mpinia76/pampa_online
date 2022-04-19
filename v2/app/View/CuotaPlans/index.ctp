<?php
//calendario
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yyyy", altFormat: "yyyy-mm-dd" });');

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
        "fnDrawCallback": function( oSettings ) { console.log(oSettings);
            $("#dataTable tr").unbind("dblclick").dblclick(function(){
                var data = oTable.fnGetData( this );



            });
            $("#dataTable tr").click(function(e){
                if(e.shiftKey){
                    $(this).toggleClass("row_selected");
                }else{
                    $("#dataTable tr").removeClass("row_selected");
                    $(this).toggleClass("row_selected");
                }
             });
             /*var montos = oTable.fnGetColumnData(11);
             if(montos.length > 0){

                $("#monto_container").html("$"+montos);
             }else{
                $("#monto_container").html("$0");
             }*/
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/cuota_plans/dataTable/Pendiente', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            null,
            null,
            {"bSortable": false},
            null,
            null,
            null,
            null,
            {"bSortable": false},
            {"sType": "date-euro"},

            null,
            {"bVisible": false }
        ]
    });
   $(".date_filter").change(function(){ oTable.fnDraw(); })
   $("#data_search").keyup( function () { oTable.fnFilter(this.value); });
');
//extra libreria para agregar filtro de fecha
//echo $this->Html->script('dataTables.dateSort', array('block' => 'extra_scripts'));
//echo $this->Html->script('dataTables.dateSort_1', array('block' => 'extra_scripts'));


//filtrar total de resultados
$this->Js->buffer('
    $("#total_rows").change(function(){
        oTable.fnReloadAjax("dataTable/"+$(this).val());
    });
    $("#filter_plan").keyup(function(){
        oTable.fnFilter($(this).val(),1);
    });

    $("#filter_tipo").keyup(function(){
        oTable.fnFilter($(this).val(),2);
    });

    $("#filter_proveedor").keyup(function(){
        oTable.fnFilter($(this).val(),4);
    });


    $("#filter_rubro").change(function(){

        if($(this).val() == ""){
            oTable.fnFilter($(this).val(),5);
            oTable.fnFilter($(this).val(),6);
            $("#filter_subrubro_container").html("");
        }else{
            oTable.fnFilter($("#filter_rubro option:selected").val(),5);
            $.ajax({
                url : "'.$this->Html->url('/subrubros/combo', true).'/"+$(this).val(),
                dataType : "html",
                success: function(data){
                    $("#filter_subrubro_container").html(data);
                    $("#filter_subrubro").change(function(){
                        if($(this).val() == ""){
                            oTable.fnFilter($(this).val(),5);
                        }else{
                            oTable.fnFilter($("#filter_subrubro option:selected").val(),6);
                         }
                     });
                }
            });
         }
    });

	$("#filter_monto").keyup(function(){
        oTable.fnFilter($(this).val(),7);
    });

    $("#filter_estado").change(function(){
        if($(this).val() == ""){
            oTable.fnFilter($(this).val(),8);
        }else{
            oTable.fnFilter($("#filter_estado option:selected").text(),8);
         }
     });

	$("#filter_vencimiento").keyup(function(){
        oTable.fnFilter($(this).val(),9);
    });

    $("#filter_responsable").keyup(function(){
        oTable.fnFilter($(this).val(),10);
    });

');

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_planes_pagos").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>

function borrar(){
    var row = $('.row_selected');
    if(row.length > 1){
        alert('Debe seleccionar un solo registro');
    }else if(row.length == 0){
        alert('Debe seleecionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
		if(confirm('¿Seguro desea anular el pago?')){
    		createWindow('w_cuota_plan_anular','Anular pago','anular_cuota_plan_procesa.php?dataid='+data[0],'600','400');
    	}
	}
}

function refinanciar(){
    var row = $('.row_selected');
    if(row.length > 1){
        alert('Debe seleccionar un solo registro');
    }else if(row.length == 0){
        alert('Debe seleecionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[8] == 'Pendiente de pago'){
            createWindow('w_refinanciar_plan','Refinanciar plan de pagos','v2/plans/refinanciar/'+data[0],'430','250');
        }else {
            alert('La cuota debe estar pendiente de pago');
        }

    }
}




function edit(action){
	var row = $('.row_selected');
    if(row.length == 0){
		alert('Debe seleccionar un registro');
	}else{
		if(row.length >1 && action != 'abonar'){
			alert('Debe seleccionar un solo registro');
		}else{
			var selected = new Array();
			$('.row_selected').each(function(e,i){

	        	var data = oTable.fnGetData(i);
	        	selected.push(data[0]);
			});

			createWindow('w_cuota_plan_edit','Ver Cuota','cuota_plan.view.php?dataid='+selected.join(',')+'&action='+action,'600','400'); //nombre de los divs
		}
	}
}







</script>
<ul class="action_bar">

    <li onclick="window.location.reload()" class="boton actualizar">Actualizar</li>

    <?php if(isset($usuario_accion['136'])){ ?><li onclick="edit('consultar');" class="boton consultar">Consultar</li> <?php  } ?>

    <?php if(isset($usuario_accion['136'])){ ?><li onclick="edit('abonar');"  class="boton abonar">Abonar</li><?php  } ?>

    <?php if(isset($usuario_accion['136'])){ ?><li onclick="borrar();"  class="boton anular">Anular Pago</li><?php  } ?>
    <?php if(isset($usuario_accion['136'])){ ?><li onclick="refinanciar();"  class="boton abonar">Refinanciar</li><?php  } ?>
    <li class="filtro">Buscar <input id="data_search" type="text"/></li>
</ul>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="100">Id</th>
            <!-- Campo Plan -->
            <th width="100">
                <input type="text" style="width: 90%;" id="filter_plan" />
            </th>

            <!-- Campo Tipo -->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_tipo" />
            </th>
			<th width="150"></th>

            <!-- Campo Proveedor-->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_proveedor" />
            </th>



            <th width="150">
                <?php echo $this->Form->input('rubro',array('label'=>false, 'options' => $rubros, 'empty' => 'Rubro', 'id' => 'filter_rubro'));?>
            </th>
            <th width="150" id="filter_subrubro_container"></th>


            <th width="70">
            	<input type="text" style="width: 90%;" id="filter_monto" />
            </th>
            <th width="100">
                <select id="filter_estado">
                    <option value="-1">Estado</option>
                    <option selected="selected">Pendiente</option>
                    <option>Pagada</option>
                    <option>Refinanciada</option>
                </select>
            </th>
            <!-- Campo Devengao-->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_vencimiento" />
            </th>

            <th width="100">
                <input type="text" style="width: 90%;" id="filter_responsable" />
            </th>
        </tr>
        <tr>
            <th width="100">Id</th>
            <th width="100">Plan</th>
            <th width="150">Origen</th>
            <th width="150">Primer orden</th>
            <th width="150">Proveedor</th>

            <th width="150">Rubro</th>
            <th width="150">Subrubro</th>


            <th width="70">Monto</th>
            <th width="100">Estado</th>
            <th width="70">Vencimiento</th>

            <th width="50">Responsable</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>

