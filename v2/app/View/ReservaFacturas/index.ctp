<?php
session_start();
//calendario


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
        
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/reserva_facturas/dataTable/', true).$reserva_id.'",
        "bDeferRender": true,
        "aoColumns": [
            
            {"sType": "date-uk"},
            null,
            null,
            null,
            null
            
        ]
    });
    
');

//extra libreria para agregar filtro de fecha
echo $this->Html->script('dataTables.dateSort', array('block' => 'extra_scripts'));

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_reserva_facturas_detalle").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>

</script>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            
            <th width="20">Fecha</th>
            <th width="50">Tipo</th>
            <th width="50">Monto</th>
            <th width="50">Tipo-Nro FC</th>
            
            <th width="100">Titular</th>
            
        </tr>
    </thead>
    <tbody></tbody>
</table>
