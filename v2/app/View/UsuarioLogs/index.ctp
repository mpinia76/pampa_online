<?php
//calendario
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yyyy-mm-dd" });');

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
            $("#dataTable tr").click(function(e){
                $("#dataTable tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/usuario_logs/dataTable/100', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            
            {"sType": "date-euro"},
                  
            null,
            null,
            null
            
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
    

    $("#filter_creado").keyup(function(){
        oTable.fnFilter($(this).val(),1);
    });

    

    $("#filter_nombre").keyup(function(){
        oTable.fnFilter($(this).val(),2);
    });
    $("#filter_accion").keyup(function(){
        oTable.fnFilter($(this).val(),3);
    });
    

    $("#filter_ip").keyup(function(){
        oTable.fnFilter($(this).val(),4);
    });

');

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_compra").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>
function limpiarFechas(){
	$('#desde').val('');
	$('#hasta').val('');
}



</script>
<?php 
    echo $this->Form->create(false, array('class' => 'form-inline'));

    
   
   echo $this->Form->input('desde',array('label' => false,'placeholder' => 'Fecha Desde', 'class' => 'datepicker', 'type' => 'text','style' => 'float: left; display:inline;'));
   echo $this->Form->input('hasta',array('label' => false,'placeholder' => 'Fecha Hasta', 'class' => 'datepicker', 'type' => 'text','style' => 'float: left; display:inline;'));
   
echo $this->Form->end('Cargar');
?>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="100">Id</th>
            

            <!-- Campo Creado -->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_creado" />
            </th>

            <!-- Campo Nombre-->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_nombre" />
            </th>
       
       		<!-- Campo Accion-->
            <th width="150">
               <input type="text" style="width: 90%;" id="filter_accion" />
            </th>

            
            <th width="100">
                <input type="text" style="width: 90%;" id="filter_ip" />
            </th>
        </tr>
        <tr>
            <th width="100">Id</th>
            
            <th width="150">Fecha/Hora</th>
            <th width="150">Usuario</th>
            <th width="150">Accion</th>
            <th width="150">IP</th>
           
        </tr>
    </thead>
    <tbody></tbody>
</table>
