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
        "sAjaxSource": "'.$this->Html->url('/cobro_tarjeta_lotes/dataTable', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            {"bVisible": false },
            null,
            null,
            null,
            null,
            null,
            null,
            {"sType": "date-uk"},
            {"sType": "date-uk"},
            null
        ]
    });
    $("#filter_estado").change(function(){
        oTable.fnReloadAjax("dataTable/"+$(this).val());
    });

   $("#data_search").keyup( function () { oTable.fnFilter(this.value); });
    $("#cerrar_box").jqm();
    $("#acreditar_box").jqm();
');


//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cobro_tarjeta_lotes").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<!--CERRAR LOTE-->
<div id="cerrar_box" class="jqWindow">
    <p class="titulo">Desea confirmar el cierre de lote?</p>
    <strong>Fecha de cierre:</strong> &nbsp; <input class="datepicker" id="fecha_cierre" value="<?php echo date('d/m/Y'); ?>" /> 
    <div class="error-message error_fecha_cierre"></div>
    <span onclick="cerrar_lote();" class="boton guardar">Cerrar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
    <p align="center"><a onclick="location.reload();">cancelar</a></p>
</div>

<!--ACREDITAR LOTE-->
<div id="acreditar_box" class="jqWindow">
    <p class="titulo">Desea acreditar el lote?</p>
    <table width="100%">
        <tr>
            <td width="50%"><strong>Fecha de acreditacion:</strong></td>
            <td><input class="datepicker" id="fecha_acreditacion" value="<?php echo date('d/m/Y'); ?>" /> 
                    <div class="error-message error_fecha_acreditacion"></div></td>
        </tr>
        <tr>
            <td><strong>Monto total:</strong></td>
            <td><span id="monto_total"></span></td>
        </tr>
        <tr>
            <td><strong>Descuentos:</strong></td>
            <td>$<input type="text" size="5" id="descuentos" value="0" onkeyup="updateTotal();" />
                   <div class="error-message error_descuentos"></div></td>
        </tr>
        <tr>
            <td><strong>Monto a acreditar:</strong></td>
            <td>$<span id="monto_acreditar"></span></td>
        </tr>
    </table>    
    <span onclick="acreditar_lote();" class="boton guardar">Confirmar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
    <p align="center"><a onclick="location.reload();">cancelar</a></p>
</div>

<ul class="action_bar">
    <li class="boton abonar"><a onclick="open_cerrar_box();">Cerrar</a></li>
    <li class="boton abonar"><a onclick="open_acreditar_box();">Acreditar</a></li>
    <li class="filtro">Buscar <input id="data_search" type="text"/></li>
</ul>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="50">Id</th>
            <th width="50">Tarjeta marca id</th>
            <th width="80">Locacion</th>
            <th width="80">Marca</th>
            <th width="100">Cuenta</th>
            <th width="50">Lote</th>
            <th width="80">$ Total</th>
            <th width="100"># Operaciones</th>
            <th width="80">CIerre</th>
            <th width="80">Acreditacion</th>
            <th width="100">Estado de lote</th>
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
        createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/', true);?>'+data[1],'430','400');
        refreshOnClose('w_reservas_view_cobro');
    }
}
function open_cerrar_box(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[0] != 0){
            alert('Este lote ya se encuentra cerrado');
        }else{
            $('#cerrar_box').jqmShow();
        }
    }
}
function cerrar_lote(){
    var row = $("#dataTable tr.row_selected");
    var data = oTable.fnGetData(row[0]);
    $('#loading_save').show();
    $('.error-message').html('');
    $.ajax({
        url : '<?php echo $this->Html->url('/cobro_tarjeta_lotes/cerrar', true);?>',
        data: {'data' : {'fecha_cierre' : $('#fecha_cierre').val(), 'cerrado_por' : <?php echo $usuario['Usuario']['id']; ?>, 'monto_total' : data[6].replace('$',''), 'numero' : data[5], 'cobro_tarjeta_tipo_id' : data[1]}},
        type: 'POST',
        dataType: 'json',
        success: function(data){
            $('#loading_save').hide();
            if(data.resultado == 'ERROR'){
                $.each(data.detalle,function(index,error){
                    $('.error_'+index).html(error[0]);
                });
            }else{
                document.location.reload();
            }
        }
    })
}
function updateTotal(){
    var monto_total = parseFloat($('#monto_total').html().replace('$',''));
    var descuentos = $('#descuentos').val() == '' ? 0 : parseFloat($('#descuentos').val());
    var monto_acreditar = monto_total - descuentos;
    $('#monto_acreditar').html(monto_acreditar);
}
function open_acreditar_box(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(!data[8]){
            alert('Este lote no se encuentra cerrado todavia');
        }else if(data[9]){
            alert('Este lote ya se encuentra acreditado')
        }else{
            $('#monto_total').html(data[6]);
            updateTotal();
            $('#acreditar_box').jqmShow();
        }
    }
}
function acreditar_lote(){
    var row = $("#dataTable tr.row_selected");
    var data = oTable.fnGetData(row[0]);
    $('#loading_save').show();
    $('.error-message').html('');
    $.ajax({
        url : '<?php echo $this->Html->url('/cobro_tarjeta_lotes/acreditar', true);?>',
        data: {'data' : {'fecha_acreditacion' : $('#fecha_acreditacion').val(), 'acreditado_por' : <?php echo $usuario['Usuario']['id']; ?>, 'id' : data[0], 'descuentos' : $('#descuentos').val()}},
        type: 'POST',
        dataType: 'json',
        success: function(data){
            $('#loading_save').hide();
            if(data.resultado == 'ERROR'){
                $.each(data.detalle,function(index,error){
                    $('.error_'+index).html(error[0]);
                });
            }else{
                document.location.reload();
            }
        }
    })
}
</script>
