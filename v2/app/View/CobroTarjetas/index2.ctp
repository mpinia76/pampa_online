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
        "sAjaxSource": "'.$this->Html->url('/cobro_tarjetas/dataTable2/', true).$cobro_tarjeta_tipo_id.'/'.$nro_lote.'",
        "bDeferRender": true,
        "aoColumns": [
            
            {"sType": "date-uk"},
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
    position = dhxWins.window("w_cobro_tarjetas_detalle").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>

</script>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            
            <th width="100">Fecha</th>
            
            <th width="50">Lote</th>
            <th width="50">Nro liquidacion</th>
            
            <th width="50">Monto</th>
            
        </tr>
    </thead>
    <tbody></tbody>
</table>
