<?php


//dataTables
$this->Js->buffer('
    oTable = $("#dataTable2").dataTable( {
	    "sDom": "<\"dataTables_top\"i>t<\"dataTables_bottom\"lp>r",
        "bProcessing": true,
    	"bAutoWidth": false,
        "oLanguage": {
            "sUrl": "/dataTables.spanish.txt"
        },
        "fnDrawCallback": function( oSettings ) {
           
            $("#dataTable2 tr").click(function(e){
                $("#dataTable2 tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
        },
        "aaSorting": [],
		"sAjaxSource": "'.$this->Html->url('/chequeras/dataTable2/'.$chequera_id, true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            null,
            null
        ]
    });
    $(".date_filter").change(function(){ oTable.fnDraw(); })
    $("#data_search").keyup( function () { oTable.fnFilter(this.value); });
	
');


//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_chequeras_control").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>



</script>
<ul class="action_bar">
    
    <li class="filtro">Buscar <input id="data_search" type="text" with="10"/></li>
</ul>
  
  
 



 
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable2">
    <thead>
        <tr>
            <th width="50">Id</th>
            
            <th width="20">Numero</th>
            
            <th width="20">Estado</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
