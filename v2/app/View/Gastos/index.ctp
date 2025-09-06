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
            "sUrl": "'.$this->webroot.'app/webroot/js/dataTables.spanish.txt"
        },
        "fnDrawCallback": function( oSettings ) { console.log(oSettings);
            $("#dataTable tr").unbind("dblclick").dblclick(function(){
                var data = oTable.fnGetData( this );

                createWindow("w_gastos_view","Ver gasto","gastos.view.php?dataid="+data[0]+"&action=consultar","600","400");

            });
            $("#dataTable tr").click(function(e){
                if(e.shiftKey){
                    $(this).toggleClass("row_selected");
                }else{
                    $("#dataTable tr").removeClass("row_selected");
                    $(this).toggleClass("row_selected");
                }
             });
             var montos = oTable.fnGetColumnData(12);

             if(montos.length > 0){

                $("#monto_container").html("$"+montos);
             }else{
                $("#monto_container").html("$0");
             }
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/gastos/dataTable/100', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            null,

            {"sType": "date-euro"},
            {"sType": "date-euro"},
            {"sType": "date-euro"},
            null,
            null,
            null,
            null,
            null,
            {"bSortable": false},
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

    $( "#chDescrip" ).on( "click", function() {
        if( $(this).is(":checked") ){
            oTable.fnFilter("on",12);
        } else {
            // Hacer algo si el checkbox ha sido deseleccionado
            //alert("El checkbox con valor " + $(this).val() + " ha sido deseleccionado");
            oTable.fnFilter("off",12);
        }
        $("#data_search").keyup();
    });

    $("#total_rows").change(function(){
        oTable.fnReloadAjax("dataTable/"+$(this).val());
    });
    $("#filter_orden").keyup(function(){
        oTable.fnFilter($(this).val(),1);
    });

    $("#filter_creado").keyup(function(){
        oTable.fnFilter($(this).val(),2);
    });

    $("#filter_devengado").keyup(function(){
        oTable.fnFilter($(this).val(),3);
    });

	$("#filter_vencimiento").keyup(function(){
        oTable.fnFilter($(this).val(),4);
    });

    $("#filter_factura").keyup(function(){
        oTable.fnFilter($(this).val(),8);
    });
    $("#filter_proveedor").keyup(function(){
        oTable.fnFilter($(this).val(),7);
    });
    $("#filter_rubro").change(function(){
        if($(this).val() == ""){
            oTable.fnFilter($(this).val(),5);
            oTable.fnFilter($(this).val(),6);
            $("#filter_subrubro_container").html("");
        }else{
            oTable.fnFilter($("#filter_rubro option:selected").text(),5);
            $.ajax({
                url : "'.$this->Html->url('/subrubros/combo', true).'/"+$(this).val(),
                dataType : "html",
                success: function(data){
                    $("#filter_subrubro_container").html(data);
                    $("#filter_subrubro").change(function(){
                        if($(this).val() == ""){
                            oTable.fnFilter($(this).val(),6);
                        }else{
                            oTable.fnFilter($("#filter_subrubro option:selected").text(),6);
                         }
                     });
                }
            });
         }
    });

	$("#filter_estado").change(function(){
        if($(this).val() == ""){
            oTable.fnFilter($(this).val(),10);
        }else{
            oTable.fnFilter($("#filter_estado option:selected").text(),10);
         }
     });

    $("#filter_responsable").keyup(function(){
        oTable.fnFilter($(this).val(),11);
    });

');

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_gasto").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>
function abonar(){
    var selected = new Array();
    $('.row_selected').each(function(e,i){
        var data = oTable.fnGetData(e);
        selected.push(data[0]);
    });

    	createWindow('w_gastos_pagar','Agregar gasto','gastos.view.php?action=abonar&dataid='+selected.join(','),'600','400');


}

function generarPlan(){
	var ok=1;
	var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
	    var selected = new Array();
	    $('.row_selected').each(function(e,i){

	        var data = oTable.fnGetData(i);

	       	if(data[10]!='Falta abonar'){
	       		ok=0;
	       	}
	       	if(ok){
	       		selected.push(data[0]);
	       	}


	    });
	    if(ok){
			$.ajax({
	        url : '<?php echo $this->Html->url('/plans/controlar', true);?>',
	        data: {'data' : {'items' : selected.join(',')},'tipo':'gastos'},
	        type: 'POST',
	        dataType: 'json',
	        success: function(data){
	            $('#loading_save').hide();
	            if(data.resultado == 'ERROR'){


	                    alert(data.detalle);

	            }else{
	                createWindow('w_generar_plan','Generar plan de pagos','<?php echo $this->Html->url('/plans/crear', true);?>/'+selected.join(',')+'/gastos','430','450');
	            }
	        }
	    	})
	    }
    	else{
       		alert('Para pagos conjuntos seleccione ordenes en estado "Falta abonar"');
       	}

	}


}

function borrarPlan(){
    var row = $('.row_selected');
    if(row.length > 1){
        alert('Debe seleccionar un solo registro');
    }else if(row.length == 0){
        alert('Debe seleecionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
		if(confirm('¿Seguro desea anular el plan?')){
    		$.ajax({
	        url : '<?php echo $this->Html->url('/plans/borrar', true);?>',
	        data: {'data' : {'id' : data},'tipo':'gastos'},
	        type: 'POST',
	        dataType: 'json',
	        success: function(data){
	            $('#loading_save').hide();
	            if(data.resultado == 'ERROR'){


	                    alert(data.detalle);

	            }else{
	                document.location.reload();
	            }
	        }
	    	})
    	}
	}
}


function borrar(){
    var row = $('.row_selected');
    if(row.length > 1){
        alert('Debe seleccionar un solo registro');
    }else if(row.length == 0){
        alert('Debe seleecionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
		if(confirm('¿Seguro desea anular el pago?')){
    		createWindow('w_gastos_anular','Anular pago','anular_gasto_procesa.php?dataid='+data[0],'600','400');
    	}
	}
}
function action(action){
    var row = $('.row_selected');
    if(row.length > 1){
        alert('Debe seleccionar un solo registro');
    }else if(row.length == 0){
        alert('Debe seleecionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data['10']!='Plan de pagos'){
            createWindow('w_gastos_consultar','ver gasto','gastos.view.php?action='+action+'&dataid='+data[0],'660','400');
        }
        else{
            alert('La orden se encuentra en un Plan de Pagos');
        }
    }
}
</script>
<ul class="action_bar">

    <li onclick="window.location.reload()" class="boton actualizar">Actualizar</li>
    <li onclick="createWindow('w_gastos_add','Agregar gasto','gastos.add.php','600','400');" class="boton agregar">Agregar</li>
    <?php if(isset($usuario_accion['21'])){ ?><li onclick="action('consultar');" class="boton consultar">Consultar</li> <?php  } ?>
    <?php if(isset($usuario_accion['34'])){ ?><li onclick="action('editar');" class="boton editar">Editar</li> <?php  } ?>
    <?php if(isset($usuario_accion['21'])){ ?><li onclick="action('abonar');"  class="boton abonar">Abonar</li><?php  } ?>
    <?php if(isset($usuario_accion['20'])){ ?><li onclick="action('autorizar');" class="boton autorizar">Autorizar</li> <?php  } ?>
    <?php if(isset($usuario_accion['21'])){ ?><li onclick="borrar();"  class="boton anular">Anular Pago</li><?php  } ?>
    <?php if(isset($usuario_accion['136'])){ ?><li onclick="generarPlan();"  class="boton abonar">Generar plan de pagos</li><?php  } ?>
    <?php if(isset($usuario_accion['136'])){ ?><li onclick="borrarPlan();"  class="boton anular">Anular plan de pagos</li><?php  } ?>
    <li class="filtro">Buscar <input id="data_search" type="text" style="width: 100px;"/><input id="chDescrip" name="chDescrip" type="checkbox">Descripci&oacute;n</li>
</ul>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="100">Id</th>
            <!-- Campo Orden -->
            <th width="100">
                <input type="text" style="width: 90%;" id="filter_orden" />
            </th>

            <!-- Campo Creado -->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_creado" />
            </th>

            <!-- Campo Devengao-->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_devengado" />
            </th>

       		<!-- Campo Vencimiento-->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_vencimiento" />
            </th>

            <th width="150">
                <?php echo $this->Form->input('rubro',array('label'=>false, 'options' => $rubros, 'empty' => 'Rubro', 'id' => 'filter_rubro'));?>
            </th>
            <th width="150" id="filter_subrubro_container"></th>
            <th width="100">
                <input type="text" style="width: 90%;" id="filter_proveedor" />
            </th>
            <th width="150">
                <input type="text" style="width: 90%;" id="filter_factura" />
            </th>
            <th width="70" id="monto_container"></th>
            <th width="100">
            <select id="filter_estado">
                    <option value="">Estado</option>
                    <option>Esperando nro. orden</option>
                    <option>Falta abonar</option>
                    <option>Falta factura</option>
                    <option>Plan de pagos</option>
                    <option>Procesado</option>
                    <option>Desaprobado</option>
                </select>
            </th>
            <th width="100">
                <input type="text" style="width: 90%;" id="filter_responsable" />
            </th>
        </tr>
        <tr>
            <th width="100">Id</th>
            <th width="100">Orden</th>

            <th width="150">Creado</th>
            <th width="150">Devengado</th>
            <th width="150">F. Facutura</th>
            <th width="150">Rubro</th>
            <th width="150">Subrubro</th>
            <th width="100">Proveedor</th>
            <th width="150">Factura</th>
            <th width="70">Monto</th>
            <th width="100">Estado</th>
            <th width="50">Responsable</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
