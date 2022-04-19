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
            $("#dataTable tr").unbind("dblclick").dblclick(function(){
                var data = oTable.fnGetData( this );
                createWindow("w_puntoVentas_view","Ver punto de venta","'.$this->Html->url('/puntoVentas/editar', true).'/"+data[0],"450","300");
            });
            $("#dataTable tr").click(function(e){
                $("#dataTable tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
        },
        "aaSorting": [],
		"sAjaxSource": "'.$this->Html->url('/puntoVentas/dataTable', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
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


//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_puntoVentas").getPosition();
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
        
        createWindow("w_puntoVentas_view","Ver Punto de Venta","<?php echo $this->Html->url('/puntoVentas/editar', true);?>/"+data[0],"450","300");
        
    }
}

</script>
<ul class="action_bar">
    <li class="boton agregar"><a onclick="createWindow('w_puntoVentas_add','Crear Punto de Venta','<?php echo $this->Html->url('/puntoVentas/crear', true);?>','450','300');">Crear</a></li>
    <li class="boton editar"><a onclick="editar();">Editar</a></li>
    <li class="filtro">Buscar <input id="data_search" type="text" with="10"/></li>
</ul>
  
  
 



 
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="50">Id</th>
            <th width="50">CUIT</th>
            <th width="50">Numero</th>
            <th width="20">Alicuota</th>
            <th width="100">Descripcion</th>
            <th width="300">Direccion</th>
           	
            <th width="20">IVA Ventas</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
