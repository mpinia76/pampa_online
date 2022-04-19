<?php
//calendario
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');

//dataTables
$this->Js->buffer('
    oTable = $("#dataTable").dataTable( {
        "sDom": "<\"dataTables_top\"i>t<\"dataTables_bottom\"lp>r",
        "bProcessing": true,
        "bAutoWidth": false,
        "oLanguage": {
            "sUrl": "/dataTables.spanish.txt"
        },
        "fnDrawCallback": function( oSettings ) {
            $("#dataTable tr").click(function(e){
                $("#dataTable tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
            $("#dataTable tr").unbind("dblclick").dblclick(function(){
                var data = oTable.fnGetData( this );
                createWindow("w_reservas_view_cobro","Detalles","'.$this->Html->url('/reserva_cobros/detalle', true).'/"+data[1],"430","400");
                refreshOnClose("w_reservas_view_cobro");
            });
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/cobro_tarjetas/dataTable', true).'",
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
            null,
            null
        ]
    });
    $("#filter_estado").change(function(){
        oTable.fnReloadAjax("dataTable/"+$(this).val());
    });
     $("#data_search").keyup( function () { oTable.fnFilter(this.value); });
');

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cobro_tarjetas").getPosition();
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
        createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/', true);?>'+data[1],'430','400');
        refreshOnClose('w_reservas_view_cobro');
    }
}
</script>
<ul class="action_bar">
    <li class="boton editar"><a onclick="editar();">Editar</a></li>
    <li class="filtro">Buscar <input id="data_search" type="text"/></li>
</ul>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="50">Id</th>
            <th width="100">Reserva Cobro Id</th>
            <th width="100">Fecha</th>
            <th width="80">Locacion</th>
            <th width="80">Marca</th>
            <th width="80">Cupon</th>
            <th width="80">Autorizacion</th>
            <th width="50">Lote</th>
            <th width="100">Nro. Liquidacion</th>
            <th width="150">Titular</th>
            <th width="150">Concepto</th>
            <th width="50">Monto</th>
            <th width="50">Cuotas</th>
            <th width="150">Estado de lote</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
