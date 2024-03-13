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
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
            // aData contiene los datos de la fila actual
            // Puedes acceder a cada columna usando aData[index]

            // Aquí asumimos que la columna correspondiente al estado de la auditoría es la 4ª columna
            var estadoAuditoria = aData[2];
            //alert(estadoAuditoria);
            // Si no hay auditoría, aplicamos el color de fondo rojo a toda la fila
            if (estadoAuditoria === null) {
                $(nRow).css("background-color", "#ffcccc");
            }
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/usuario_auditorias/dataTable/100', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
             null,
            {"sType": "date-euro","bSortable": false},
                  {"sType": "date-euro","bSortable": false},
                  {"bSortable": false},
           {"bSortable": false},

           {"bSortable": false}
            
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
    
$("#filter_nombre").keyup(function(){
        oTable.fnFilter($(this).val(),1);
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
	$('#desdeA').val('');
	$('#hastaA').val('');
}



</script>
<?php 
    /*echo $this->Form->create(false, array('class' => 'form-inline'));

    
   
   echo $this->Form->input('desde',array('label' => false,'placeholder' => 'Fecha Desde', 'class' => 'datepicker', 'type' => 'text','style' => 'float: left; display:inline;'));
   echo $this->Form->input('hasta',array('label' => false,'placeholder' => 'Fecha Hasta', 'class' => 'datepicker', 'type' => 'text','style' => 'float: left; display:inline;'));
   
echo $this->Form->end('Cargar');*/
?>
<div id="informe_auditoria"><strong>&nbsp;&nbsp;Entre fechas&nbsp;<input type="text" name="desdeA" id="desdeA" class="datepicker" value="<?php echo $desde?>">&nbsp;&nbsp;y&nbsp;<input type="text" name="hastaA" id="hastaA" class="datepicker" value="<?php echo $hasta?>"></strong>

    <input type="button" onclick="ver_auditorias();" value="Ver" /> <span id="cargandoA" style="display:none;">Cargando ...</span>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="100">Id</th>

            <!-- Campo Nombre-->
            <th width="150">
                <input type="text" style="width: 90%;" id="filter_nombre" />
            </th>

            <!-- Campo Creado -->
            <th width="150">

            </th>
            <th width="150">

            </th>
            <th width="150">

            </th>

       
       		<!-- Campo Accion-->
            <th width="150">

            </th>


            <th width="100">

            </th>
        </tr>
        <tr>
            <th width="100">Id</th>
            <th width="150">Usuario</th>
            <th width="150">Fecha</th>
            <th width="150">Login</th>
            <th width="150">Horas</th>
            <th width="150">Ult. Interaccion</th>
            <th width="150">IP</th>
           
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>