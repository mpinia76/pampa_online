<?php
$year= (isset($year))?$year:date('Y');
$mes= (isset($mes))?$mes:date('m');
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

            // AquÃƒÂ­ asumimos que la columna correspondiente al estado de la auditorÃƒÂ­a es la 4Ã‚Âª columna
            var estadoAuditoria = aData[0];
	    //console.log(estadoAuditoria);
            //alert(estadoAuditoria);
            // Si no hay auditorÃƒÂ­a, aplicamos el color de fondo rojo a toda la fila
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
<div id="informe_auditoria">
    <select size="1" name="year" id="year">
	<option <?php if($year == '2018'){?> selected="selected" <?php } ?> >2018</option>
        <option <?php if($year == '2019'){?> selected="selected" <?php } ?> >2019</option>
        <option <?php if($year == '2020'){?> selected="selected" <?php } ?> >2020</option>
        <option <?php if($year == '2021'){?> selected="selected" <?php } ?> >2021</option>
        <option <?php if($year == '2022'){?> selected="selected" <?php } ?> >2022</option>
        <option <?php if($year == '2023'){?> selected="selected" <?php } ?> >2023</option>
        <option <?php if($year == '2024'){?> selected="selected" <?php } ?> >2024</option>
        <option <?php if($year == '2025'){?> selected="selected" <?php } ?> >2025</option>
        <option <?php if($year == '2026'){?> selected="selected" <?php } ?> >2026</option>
        <option <?php if($year == '2027'){?> selected="selected" <?php } ?> >2027</option>
    </select>
    <select id="mes">
        <option value="01" <?php if($mes == '01'){?> selected="selected" <?php } ?>>Enero</option>
        <option value="02" <?php if($mes == '02'){?> selected="selected" <?php } ?>>Febrero</option>
        <option value="03" <?php if($mes == '03'){?> selected="selected" <?php } ?>>Marzo</option>
        <option value="04" <?php if($mes == '04'){?> selected="selected" <?php } ?>>Abril</option>
        <option value="05" <?php if($mes == '05'){?> selected="selected" <?php } ?>>Mayo</option>
        <option value="06" <?php if($mes == '06'){?> selected="selected" <?php } ?>>Junio</option>
        <option value="07" <?php if($mes == '07'){?> selected="selected" <?php } ?>>Julio</option>
        <option value="08" <?php if($mes == '08'){?> selected="selected" <?php } ?>>Agosto</option>
        <option value="09" <?php if($mes == '09'){?> selected="selected" <?php } ?>>Septiembre</option>
        <option value="10" <?php if($mes == '10'){?> selected="selected" <?php } ?>>Octubre</option>
        <option value="11" <?php if($mes == '11'){?> selected="selected" <?php } ?>>Noviembre</option>
        <option value="12" <?php if($mes == '12'){?> selected="selected" <?php } ?>>Diciembre</option>
    </select>
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
            <th width="150">Ultima</th>
            <th width="150">Horas</th>
            <th width="150">Ult. Interaccion</th>
            <th width="150">IP</th>

        </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>