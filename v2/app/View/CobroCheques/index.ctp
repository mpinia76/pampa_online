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
        "sAjaxSource": "'.$this->Html->url('/cobro_cheques/dataTable/pendiente', true).'",
        "bDeferRender": true,
        "aoColumns": [
            {"bVisible": false },
            {"bVisible": false },
            {"sType": "date-uk"},
            {"sType": "date-uk"},
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
    $("#confirm_box").jqm();
    $("#confirm_box1").jqm();
');

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_cobro_cheques").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<div id="confirm_box" class="jqWindow">
    <p class="titulo">Desea confirmar la acreditacion?</p>
    Fecha de acreditacion: &nbsp; <input class="datepicker" id="fecha_acreditado" value="<?php echo date('d/m/Y'); ?>" /> 
    <div class="error-message error_fecha_acreditado"></div>
    Cuenta donde se deposito: &nbsp; <?php echo $this->Form->input('cuenta',array('type' => 'select', 'options' => $cuentas, 'empty' => 'Seleccionar...', 'div' => false, 'label' => false)); ?>
    <div class="error-message error_cuenta_acreditado"></div>
    <span onclick="acreditar();" class="boton guardar">Confirmar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
    <p align="center"><a onclick="$('#confirm_box').jqmHide();">cancelar</a></p>
</div>

<div id="confirm_box1" class="jqWindow">
    <p class="titulo">Desea confirmar la acreditacion?</p>
    Fecha de acreditacion: &nbsp; <input class="datepicker" id="fecha_acreditado1" value="<?php echo date('d/m/Y'); ?>" /> 
    <div class="error-message error_fecha_acreditado"></div>
    Caja: &nbsp; <?php echo $this->Form->input('caja',array('type' => 'select', 'options' => $cajas, 'empty' => 'Seleccionar...', 'div' => false, 'label' => false)); ?>
    <div class="error-message error_caja_acreditado"></div>
    <span onclick="cobro_caja();" class="boton guardar">Confirmar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save1" /></span>
    <p align="center"><a onclick="$('#confirm_box1').jqmHide();">cancelar</a></p>
</div>

<ul class="action_bar">
    <li class="boton editar"><a onclick="editar();">Editar</a></li>
    <li onclick="open_confirm_box();" class="boton autorizar">Confirmar acreditacion</li>
    <li onclick="open_confirm_box1();" class="boton autorizar">Cobro por caja</li>
    <li class="boton anular"><a onclick="anular();">Anular acreditacion</a></li>
    <li class="filtro">Estado<select id="filter_estado">
            <option value="">Todos</option>
            <option value="pendiente" selected="selected">Pendiente</option>
            <option value="acreditado">Acreditado</option>
            <option value="cobrado">Cobrado por caja</option>
            <option value="asociado">Asociado a pago</option>
    </select></li>
</ul>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th width="50">Id</th>
            <th width="50">Cobro Id</th>
            <th width="150">Fecha del cheque</th>
            <th width="150">Acreditado</th>
            <th width="60">Numero</th>
            <th width="150">Banco</th>
            <th width="150">Tipo</th>
            <th width="150">Librado por</th>
            <th width="150">A la orden de</th>
            <th width="100">Concepto</th>
            <th width="100">Monto</th>
            <th width="100">Estado</th>
            <th width="100">Detalle</th>
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
function open_confirm_box(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[11] == 'Acreditado'){
            if(confirm("El cheque ya fue acreditado  \n \n Desea continuar para cambiar la fecha de acreditacion del cheque?")) {
            	$('#confirm_box').jqmShow();
            }
        }else if(data[11] == 'Cobrado por caja'){
            alert('Este cheque fue cobrado por caja');
        }else if(data[11] == 'Asociado a pago'){
            alert('Este cheque esta asociado a un pago');
        }else{
            $('#confirm_box').jqmShow();
        }
    }
}
function open_confirm_box1(){
    var row = $("#dataTable tr.row_selected");
    if(row.length == 0){
        alert('Debe seleccionar un registro');
    }else{
        var data = oTable.fnGetData(row[0]);
        if(data[11] == 'Acreditado'){
            alert('Este cheque fue acreditado');
        }else if(data[11] == 'Cobrado por caja'){
            if(confirm("El cheque fue cobrado por caja  \n \n Desea continuar para cambiar la fecha de cobro del cheque?")) {
            	$('#confirm_box1').jqmShow();
            }
        }else if(data[11] == 'Asociado a pago'){
            alert('Este cheque esta asociado a un pago');
        }else{
            $('#confirm_box1').jqmShow();
        }
    }
}
function acreditar(){
    var row = $("#dataTable tr.row_selected");
    var data = oTable.fnGetData(row[0]);
    var acreditado = (data[11] == 'Acreditado')?1:0;
    $('#loading_save').show();
    $('.error-message').html('');
    $.ajax({
        url : '<?php echo $this->Html->url('/cobro_cheques/acreditar', true);?>',
        data: {'data' : {'id' : data[0], 'fecha' : $('#fecha_acreditado').val(), 'usuario' : <?php echo $usuario['Usuario']['id']; ?>, 'cuenta' : $('#cuenta').val(),'acreditado' : acreditado}},
        type: 'POST',
        dataType: 'json',
        success: function(data){
            $('#loading_save').hide();
            if(data.resultado == 'ERROR'){
                $.each(data.detalle,function(index,error){
                    $('.error_'+index).html(error[0]);
                });
            }else{
                $('#confirm_box').jqmHide();
                $("#filter_estado").change();
            }
        }
    })
}

function cobro_caja(){
    var row = $("#dataTable tr.row_selected");
    var data = oTable.fnGetData(row[0]);
    var acreditado = (data[11] == 'Cobrado por caja')?1:0;
    $('#loading_save1').show();
    $('.error-message').html('');
    $.ajax({
        url : '<?php echo $this->Html->url('/cobro_cheques/cobro_caja', true);?>',
        data: {'data' : {'id' : data[0], 'fecha' : $('#fecha_acreditado1').val(), 'usuario' : <?php echo $usuario['Usuario']['id']; ?>, 'caja' : $('#caja').val(),'acreditado' : acreditado}},
        type: 'POST',
        dataType: 'json',
        success: function(data){
            $('#loading_save1').hide();
            if(data.resultado == 'ERROR'){
                $.each(data.detalle,function(index,error){
                    $('.error_'+index).html(error[0]);
                });
            }else{
                $('#confirm_box1').jqmHide();
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
        if((data[11] == 'Acreditado')||(data[11] == 'Cobrado por caja')){
        	if(confirm("Anular acreditacion?")) {
			    $('#loading_save').show();
			    $.ajax({
			        url : '<?php echo $this->Html->url('/cobro_cheques/anular', true);?>',
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

$('#caja').change(function(){
	
    if($(this).val()!=''){
        var datos = ({
			'caja_id' : $(this).val(),
			'fecha' : $('#fecha_acreditado1').val()
		});
			
		$.ajax({
			beforeSend: function(){
				$('#loading').show();
			},
			data: datos,
			url: '../../functions/consultarSincronismoFecha.php',
			success: function(data) {
			
				if(data == 'no'){		
					alert('Movimiento no permitido: La caja se encuentra conciliada y sincronizada para la fecha del movimiento que intenta realizar. Contactar administrador');
					$('#caja').prop('selectedIndex',0);
				}
				$('#loading').hide();
				
			}
		});
    }
})
</script>