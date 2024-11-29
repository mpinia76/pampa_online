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
                document.location = "'.$this->Html->url('/reservas/plantilla', true).'/"+data[0];
            });
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/reservas/dataTable/restringido/Reserva.check_out desc', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            null,
            {"sType": "date-uk"},
            null,
            null,
            {"sType": "date-uk"},
            {"sType": "date-uk"},
            null,
            null,
            null,
            null,
            {"bVisible": false },
            {"bSortable": false},
            {"bSortable": false}
        ]
    });
    $(".date_filter").change(function(){ oTable.fnDraw(); })
    $("#data_search").keyup( function () { oTable.fnFilter(this.value); });
');
//extra libreria para agregar filtro de fecha
echo $this->Html->script('dataTables.dateSort', array('block' => 'extra_scripts'));

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_reservas").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>
function extras_facturas(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        createWindow('w_reservas_finalizar','Finalizar reserva','<?php echo $this->Html->url('/reserva_cobros/finalizar', true);?>/'+data[0]+'/1','800','500');
        refreshOnClose('w_reservas_finalizar');
    }
}
function plantilla(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        document.location = "<?php echo $this->Html->url('/reservas/plantilla', true);?>/"+data[0];
    }
}
function checkConsumo(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        document.location = "<?php echo $this->Html->url('/reservas/check_consumo', true);?>/"+data[0];
    }
}

function finalizar(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[11] == 'FINALIZA'){
            $('#loading_finalizar').show();
            $.ajax({
                url: "<?php echo $this->Html->url('/reservas/finalizar', true);?>",
                data: {'reserva_id' : data[0]},
                type: 'POST',
                dataType: 'json',
                success: function(data){
                    $('#loading_cancelar').hide();
                    if(data.resultado == 'ERROR'){
                        alert(data.mensaje);
                    }
                    document.location.reload();
                }
            })
        }else{
            alert('No se puede finalizar esta reserva');
        }
    }
}
function asignacion_masiva(){
    
      
    createWindow('w_reserva_factura_asignacion','Asignacion masiva de facturas','<?php echo $this->Html->url('/reserva_facturas/asignacion/', true);?>','420','300');
    refreshOnClose('w_reserva_factura_asignacion');    	
        
    
}
</script>
<ul class="action_bar">
    <?php if($permisoPlanilla){ ?><li class="boton pdf"><a onclick="plantilla();">Planilla</a></li><?php  } ?>
    <?php if($permisoCheckConsumo){ ?><li class="boton pdf"><a onclick="checkConsumo();">Check de consumos</a></li><?php  } ?>
    <?php if($permisoCarga){ ?><li class="boton abonar"><a onclick="extras_facturas();">Carga de extras y facturas</a></li><?php  } ?>
    <?php if($permisoFinalizar){ ?><li class="boton abonar"><a onclick="finalizar();">Finalizar <img src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" id="loading_finalizar" align="absmiddle" /></a></li><?php  } ?>
    <?php if($permisoMasiva){ ?><li class="boton abonar"><a onclick="asignacion_masiva();">Asignacion masiva</a></li><?php  } ?>
    <li class="filtro">Hasta  <input class="datepicker date_filter"  id="ffin" type="text" /> <input type="hidden" id="ffin_col" value="6"/></li>
    <li class="filtro">Desde <input class="datepicker date_filter" id="fini" type="text" /> <input type="hidden" id="fini_col" value="5"/></li>
    <li class="filtro">Buscar <input id="data_search" type="text"/></li>
</ul>

   <p><b>Filtro campo: OUT</b></p>
  
  <?php 
    echo $this->Form->create(false, array('class' => 'form-inline'));
   
   
    echo $this->Form->input('year' ,array(
                                        'type' => 'date',
                                        'label' => false,
                                        'minYear' => '2012', 
                                        'maxYear' => date('Y') + 2,
                                        'dateFormat'=> 'Y',
                                        'style' => 'float: left; display:inline;'
                                       ));
   

    echo $this->Form->input('month' ,array(
   'type' => 'select',
   'label' => false,
   'options' => array('01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo', '06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'), 'default'=>'01',
   'style' => 'float: left; display:inline;'
   ));
   echo $this->Form->end('Cargar');
?>

<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="50">Id</th>
            <th width="50">Numero</th>
            <th width="100">Fecha</th>
            <th width="250">Titular</th>
            <th width="250">Apartamento</th>
            <th width="150">In</th>
            <th width="150">Out</th>
            <th width="150">Extras No Ad.</th>
            <th width="100">Descuento</th>
            <th width="100">Pendiente</th>
            <th width="150">Estado</th>
            <th width="150">Estado num</th>
            <th width="50">Planilla</th>
            <th width="50">Voucher</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
