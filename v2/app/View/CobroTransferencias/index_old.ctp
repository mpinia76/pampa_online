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
        },
        "aaSorting": [],
        "sAjaxSource": "'.$this->Html->url('/cobro_transferencias/dataTable/pendiente', true).'",
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
            null
        ]
    });
    $("#filter_estado").change(function(){
        oTable.fnReloadAjax("dataTable/"+$(this).val());
    });
    $("#confirm_box").jqm();
');
//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cobro_transferencias").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<div id="confirm_box" class="jqWindow">
    <p class="titulo">Desea confirmar la acreditacion?</p>
    Seleccione la fecha de acreditacion: &nbsp; <input class="datepicker" id="fecha_acreditado" value="<?php echo date('d/m/Y'); ?>" />
    <div class="error-message"></div>
    <span onclick="acreditar();" class="boton guardar">Confirmar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
    <p align="center"><a onclick="$('#confirm_box').jqmHide();">cancelar</a></p>
</div>

<ul class="action_bar">
    <li class="boton editar"><a onclick="editar();">Editar</a></li>
    <li onclick="open_confirm_box();" class="boton autorizar">Confirmar acreditacion</li>
    <li class="boton anular"><a onclick="anular();">Anular acreditacion</a></li>
    <li class="filtro">Estado<select id="filter_estado">
            <option value="">Todos</option>
            <option value="pendiente" selected="selected">Pendiente</option>
            <option value="acreditado">Acreditado</option>
    </select></li>
</ul>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="50">Id</th>
            <th width="50">Cobro Id</th>
            <th width="150">Fecha</th>
            <th width="200">Banco</th>
            <th width="150">Cuenta</th>
            <th width="250">Quien Transfiere</th>
            <th width="150">Operacion</th>
            <th width="100">Concepto</th>
            <th width="100">Monto</th>
            <th width="100">Estado</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<script>
    function editar(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[9] == 'Acreditado'){
            alert('Esta transferencia ya se encuentra acreditada y no puede editarse');
        }else{
            createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/', true);?>'+data[1],'430','400');
            refreshOnClose('w_reservas_view_cobro');
       }
    }
}
function open_confirm_box(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[9] == 'Acreditado'){
        	if(confirm("La transferencia ya fue acreditada  \n \n Desea continuar para cambiar la fecha de acreditacion de la transferencia?")) {
            	$('#confirm_box').jqmShow();
            }
        }else{
            $('#confirm_box').jqmShow();
        }
    }
}
function acreditar(){
    var row = $("#dataTable tr.row_selected");
    var data = oTable.fnGetData(row[0]);
    var acreditado = (data[9] == 'Acreditado')?1:0;
    $('#loading_save').show();
    $.ajax({
        url : '<?php echo $this->Html->url('/cobro_transferencias/acreditar', true);?>',
        data: {'data' : {'id' : data[0], 'fecha' : $('#fecha_acreditado').val(), 'usuario' : <?php echo $usuario['Usuario']['id']; ?>,'acreditado' : acreditado}},
        type: 'POST',
        dataType: 'json',
        success: function(data){
            $('#loading_save').hide();
            if(data.resultado == 'ERROR'){
                $('.error-message').html(data.detalle.fecha_acreditado[0]);
            }else{
            	$('#confirm_box').jqmHide();
                $("#filter_estado").change();	
            }
        }
    })
}

function anular(){
    var row = $("#dataTable tr.row_selected");
   if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[9] == 'Acreditado'){
        	if(confirm("Anular acreditacion?")) {
			    $('#loading_save').show();
			    $.ajax({
			        url : '<?php echo $this->Html->url('/cobro_transferencias/anular', true);?>',
			        data: {'data' : {'id' : data[0]}},
			        type: 'POST',
			        dataType: 'json',
			        success: function(data){
			            $('#loading_save').hide();
			            if(data.resultado == 'ERROR'){
			                //$('.error-message').html(data.detalle.fecha_acreditado[0]);
			            }else{
			                $("#filter_estado").change();
			            }
			        }
			    })
			 }
		}
		else{
			alert('La transferencia no fue acreditada');
		}
	}
}
</script>