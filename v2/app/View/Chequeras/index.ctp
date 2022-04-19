<?php


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
                var data = oTable.fnGetData( this );
                createWindow("w_chequeras_view","Ver chequera","'.$this->Html->url('/chequeras/editar', true).'/"+data[0],"450","300");
            });
            $("#dataTable tr").click(function(e){
                $("#dataTable tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
        },
        "aaSorting": [],
		"sAjaxSource": "'.$this->Html->url('/chequeras/dataTable', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null
        ]
    });
    $(".date_filter").change(function(){ oTable.fnDraw(); })
    $("#data_search").keyup( function () { oTable.fnFilter(this.value); });
	
');

//filtrar total de resultados
$this->Js->buffer('
    $("#total_rows").change(function(){
        oTable.fnReloadAjax("dataTable/"+$(this).val());
    });
    $("#filter_cuenta").keyup(function(){
        oTable.fnFilter($(this).val(),1);
    });

    $("#filter_numero").keyup(function(){
        oTable.fnFilter($(this).val(),2);
    });

    $("#filter_tipo").keyup(function(){
        oTable.fnFilter($(this).val(),3);
    });

	
	$("#filter_cantidad").keyup(function(){
        oTable.fnFilter($(this).val(),4);
    });
    
    $("#filter_inicio").keyup(function(){
        oTable.fnFilter($(this).val(),5);
    });
    
    $("#filter_final").keyup(function(){
        oTable.fnFilter($(this).val(),6);
    });

	$("#filter_responsable").keyup(function(){
        oTable.fnFilter($(this).val(),7);
    });

    $("#filter_estado").change(function(){ 
        if($(this).val() == ""){
            oTable.fnFilter($(this).val(),8);
        }else{
            oTable.fnFilter($("#filter_estado option:selected").text(),8);
         }
     });

	

    

');


//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_chequeras").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>

function editar(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        
        createWindow("w_chequeras_view","Ver chequera","<?php echo $this->Html->url('/chequeras/editar', true);?>/"+data[0],"450","300");
        
    }
}

function control(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        
        createWindow("w_chequeras_control","Control chequera","<?php echo $this->Html->url('/chequeras/index_control', true);?>/"+data[0],"850","300");
        
    }
}

</script>
<ul class="action_bar">
    <li class="boton agregar"><a onclick="createWindow('w_chequeras_add','Crear chequera','<?php echo $this->Html->url('/chequeras/crear', true);?>','450','300');">Agregar</a></li>
    <li class="boton editar"><a onclick="editar();">Editar</a></li>
    <li class="boton abonar"><a onclick="control();">Control</a></li>
    <li class="filtro">Buscar <input id="data_search" type="text" with="10"/></li>
</ul>
  
  
 



 
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
    	 <tr>
            <th width="100">Id</th>
            <!-- Campo Cuenta -->
            <th width="150">
                <input type="text" style="width: 90%;" id="filter_cuenta" />
            </th>

            <!-- Campo Numero -->
            <th width="20">
               <input type="text" style="width: 90%;" id="filter_numero" />
            </th>
			

            <!-- Campo Tipo-->
            <th width="50">
               <input type="text" style="width: 90%;" id="filter_tipo" />
            </th>
       		<!-- Campo Cantidad -->
            <th width="20">
               <input type="text" style="width: 90%;" id="filter_cantidad" />
            </th>
            <!-- Campo Inicio -->
            <th width="20">
               <input type="text" style="width: 90%;" id="filter_inicio" />
            </th>
            <!-- Campo Final -->
            <th width="20">
               <input type="text" style="width: 90%;" id="filter_final" />
            </th>
       		
			<th width="100">
                <input type="text" style="width: 90%;" id="filter_responsable" />
            </th>
            
            <th width="100">
                <select id="filter_estado">
                    <option value="0">Estado</option>
                    <option value="1" selected="selected">Activas</option>
                    <option value="2">Inactivas</option>
                    <option value="3">Utilizadas</option>
                </select>
            </th>
            
            
            
        </tr>
    
        <tr>
            <th width="50">Id</th>
            <th width="150">Banco/Cuenta</th>
            <th width="20">Numero</th>
            <th width="50">Tipo de Cheque</th>
            <th width="20">Cantidad</th>
            <th width="20">Nro. Inicio</th>
            <th width="20">Nro. Final</th>
            <th width="100">Responsable</th>
            <th width="20">Estado</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
