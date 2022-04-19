<?php


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
            $("#dataTable tr").unbind("dblclick").dblclick(function(){
                var data = oTable.fnGetData( this );
                createWindow("w_cobro_tarjeta_exportar_descargar","Descargar","'.$this->Html->url('/cobro_tarjetas/exportar', true).'/"+data[0],"450","300");
                setTimeout("dhxWins.window(\"w_cobro_tarjeta_exportar_descargar\").close()", 400);
            });
            $("#dataTable tr").click(function(e){
                $("#dataTable tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
        },
        "aaSorting": [],
		"sAjaxSource": "'.$this->Html->url('/cobro_tarjeta_importacions/dataTable', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            null
        ]
    });
    $(".date_filter").change(function(){ oTable.fnDraw(); })
    $("#data_search").keyup( function () { oTable.fnFilter(this.value); });
	
');


//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cobro_tarjeta_importacions").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>
function descargar(){
	var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
   
	    var data = oTable.fnGetData(row[0]);
	    
	    createWindow('w_cobro_tarjeta_exportar_descargar','Descargar','<?php echo $this->Html->url('/cobro_tarjetas/exportar', true);?>/'+data[0],'430','300');
	    setTimeout('dhxWins.window("w_cobro_tarjeta_exportar_descargar").close()', 2000);
	}
}
</script>
<ul class="action_bar">
    <li class="boton excel"><a onclick="descargar();">Descargar excel</a></li>
</ul>
  
  
 



 
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="50">Id</th>
            <th width="100">Fecha</th>
            
     
        </tr>
    </thead>
    <tbody></tbody>
</table>
