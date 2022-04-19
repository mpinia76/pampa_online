<?php
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_reservas_add_cobro").getPosition();
    xpos = position[0];
    ypos = position[1];
');

//formulario
echo $this->Form->create(null, array('url' => '/reserva_cobros/agregar','inputDefaults' => (array('div' => 'ym-gbox'))));
echo $this->Form->hidden('ReservaCobro.usuario_id',array('value' => $usuario['Usuario']['id']));
echo $this->Form->hidden('ReservaCobro.reserva_id',array('value' => $reserva['Reserva']['id']));
?>
<div class="ym-grid">
    <div class="ym-g50 ym-gl">
        <div class="ym-gbox">
            <strong>Titular:</strong> <?php echo $reserva['Cliente']['nombre_apellido'];?><br/>
            <strong>Apartamento: </strong> <?php echo $reserva['Apartamento']['apartamento']; ?> <br/>
            <strong>Check In: </strong> <?php echo $reserva['Reserva']['check_in'];?> <?php echo $reserva['Reserva']['hora_check_in'];?> hs. <strong>Check Out: </strong> <?php echo $reserva['Reserva']['check_out'];?> <?php echo $reserva['Reserva']['late_check_out'];?> hs.
        </div>
    </div>
    <div class="ym-g50 ym-gl" style="text-align: right;">
        <div class="ym-gbox">
            Monto total de la estadia:  $<?php echo $reserva['Reserva']['total_estadia']; ?><br/>
            Extras adelantados:  $<?php echo $adelantadas; ?>  <br/>
            <strong>Tarifa bruta inicial:  $<?php echo $reserva['Reserva']['total']; ?> </strong> <br/>
            Extras no adelantados:  $<?php echo $no_adelantadas; ?> <br/>
            <strong>Tarifa bruta final:  $<?php echo ROUND($reserva['Reserva']['total']+$no_adelantadas,2); ?> </strong>
        </div>
    </div>
</div>

<?php 
$descuentos = 0; 
if(count($reserva_descuentos) > 0){  ?>
    <div class="sectionTitle" style="margin-top: 10px;">Descuentos</div>
    <div class="ym-gbox">
        <table width="100%" >
            <tr>
                <td width="50">&nbsp;</td>
                <td width="80"><strong>Fecha</strong></td>
                <td width="100"><strong>Usuario</strong></td>
                <td><strong>Motivo</strong></td>
                <td width="75"  align="right"><strong>Monto</strong></td>
            </tr>
            <?php foreach($reserva_descuentos as $descuento){ 
                $descuentos = $descuentos + $descuento['Descuento']['monto'];?>
            <tr>
                <td align="left"><a onclick="eliminarDescuento('<?php echo $descuento['ReservaCobro']['id'];?>')">quitar</a></td>
                <td><?php echo $descuento['ReservaCobro']['fecha'];?></td>
                <td><?php echo $descuento['Usuario']['nombre'].' '.$descuento['Usuario']['apellido']?></td>
                <td><?php echo $descuento['Descuento']['motivo'];?></td>
                <td align="right">$<?php echo $descuento['Descuento']['monto'];?></td>
            </tr>
            <?php } ?>
        </table>
        <table width="100%" >
            <tr class="saldo_pendiente">
                <?php if($reserva['Reserva']['estado'] == 0){ ?>
                <td align="left"><a style="font-weight: normal;" onclick="agregar_descuento();">Agregar descuento</a></td>
                <?php }else{ ?>
                <td>&nbsp;</td>
                <?php } ?>
                <td align="right">Total descontado: $<?php echo $descuentos;?></td>
            </tr>
            <tr class="saldo_pendiente">
                <td align="left">&nbsp;</td>
                <td align="right">Tarifa neta de descuentos (acordada): $<?php echo $reserva['Reserva']['total'] + $no_adelantadas -$descuentos;?></td>
            </tr>
        </table>
    </div>
 <?php }else{ ?> 
    <?php if($reserva['Reserva']['estado'] == 0){ ?>
        <div class="sectionTitle" style="margin-top: 10px;">Descuentos</div>
        <div class="ym-gbox">
            No se ha cargado ningun descuento <a onclick="agregar_descuento();">Agregar descuento</a>
        </div>
<?php }} ?>
<div id="descuento" class="ym-gbox"></div>

<?php if($reserva['Reserva']['estado'] == 0){ ?>
<div class="sectionTitle">Cobros</div>
<div class="sectionSubtitle" style="margin-top: 5px;">Agregar cobro</div>
<div class="ym-grid" id="forma_cobro">
    <div class="ym-g10 ym-gl">
        <?php echo $this->Form->input('ReservaCobro.fecha',array('class' => 'datepicker', 'type' => 'text', 'value' => date('d/m/Y'))); ?>
    </div>
    <div class="ym-g10 ym-gl">
        <div class="ym-gbox"><strong>Forma de cobro:</strong> 
            <select id="ReservaCobroTipo" name="data[ReservaCobro][tipo]">
                <option value="">Seleccione...</option>
                <option value="TARJETA">Tarjeta</option>
                <option value="EFECTIVO">Efectivo</option>
                <option value="CHEQUE">Cheque</option>
                <option value="TRANSFERENCIA">Transferencia</option>
            </select>
        </div>
    </div>
    <div class="ym-g20 ym-gl">
        <?php echo $this->Form->input('ReservaCobro.concepto_facturacion_id',array( 'type' => 'select', 'empty' => 'Seleccionar ...', 'options' => $concepto_facturacions)); ?>
    </div>
    <div id="divMoneda" class="ym-g10 ym-gl" style="display:none">
        <?php echo $this->Form->input('ReservaCobro.moneda_id',array( 'type' => 'select', 'options' => $monedas)); ?>
    </div>
    <div id="divMonto" class="ym-g10 ym-gl" style="display:none">
        <?php echo $this->Form->input('ReservaCobro.monto_moneda',array('class' => 'number', 'type' => 'text', 'label' => 'Monto')); ?>
    </div>
    <div id="divCambio" class="ym-g10 ym-gl" style="display:none">
        <?php echo $this->Form->input('ReservaCobro.cambio',array('class' => 'number', 'type' => 'text', 'label' => 'Cambio')); ?>
    </div>
    <div class="ym-g10 ym-gl">
        <?php echo $this->Form->input('ReservaCobro.monto_neto',array('class' => 'number', 'type' => 'text', 'label' => 'M. libre Int.')); ?>
    </div>
    <div class="ym-g10 ym-gl">
        <span id="btn_agregar_cobro" style="margin-top: 15px;" class="boton agregar" onclick="agregar_cobro();">+ agregar</span>
        <span id="btn_agregar_cobro2" style="margin-top: 15px;display:none" class="boton agregar" >Procesando...</span>
    </div>
</div>
<?php } ?>
<div id="cobro_tipos"></div>

<div class="sectionTitle">Historial de Cobros </div>

<div class="ym-gbox">
<?php if(count($reserva_cobros)>0){ 
    $pagado = 0; 
    $intereses = 0; ?>
    <table width="100%">
        <tr>
            <td width="55">&nbsp;</td>
            <td width="80"><strong>Fecha</strong></td>
            <td width="100"><strong>Usuario</strong></td>
            <td width="120"><strong>Tipo</strong></td>
            <td><strong>Concepto FC</strong></td>
            <td><strong>Informacion</strong></td>
            <td width="75" align="right"><strong>Neto</strong></td>
            <td width="75" align="right"><strong>Interes</strong></td> 
            <td width="75" align="right"><strong>Cobrado</strong></td>            
        </tr>
<?php

		
        foreach($reserva_cobros as $cobro){ //print_r($reserva_cobros);
            switch($cobro['ReservaCobro']['tipo']){
                case  'TARJETA': ?>
                <tr>
                    <td><img style="display:none;" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" id="loading_delete<?php echo $cobro['ReservaCobro']['id'];?>" /><a onclick="eliminarCobro('<?php echo $cobro['ReservaCobro']['id'];?>')">eliminar</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
                    <td><?php echo $cobro['ReservaCobro']['fecha']?></td>
                    <td><?php echo $cobro['Usuario']['nombre'].' '.$cobro['Usuario']['apellido']?></td>
                    <td><?php echo $cobro['ReservaCobro']['tipo']?></td>
                    <td><?php echo $cobro['ConceptoFacturacion']['nombre']?></td>
                    <td><a onclick="createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/'.$cobro['ReservaCobro']['id'], true);?>','430','400');"><?php echo $tarjetas_tipo[$cobro['CobroTarjeta']['cobro_tarjeta_tipo_id']]?> - <?php echo $cobro['CobroTarjeta']['tarjeta_numero']?> <?php echo $cobro['CobroTarjeta']['cuotas']?> cuota/s</a></td>
                    <td align="right">$<?php echo $cobro['CobroTarjeta']['monto_neto']?></td>
                    <td align="right">$<?php echo $cobro['CobroTarjeta']['interes']?></td>
                    <td align="right">$<?php echo $cobro['CobroTarjeta']['monto_neto'] + $cobro['CobroTarjeta']['interes']?></td>                    
                </tr>
                <?php $pagado = $pagado + $cobro['CobroTarjeta']['monto_neto']; $intereses = $intereses + $cobro['CobroTarjeta']['interes']; break; 
                
                case 'CHEQUE': ?>
                <tr>
                    <td><a onclick="eliminarCobro('<?php echo $cobro['ReservaCobro']['id'];?>')">eliminar</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
                    <td><?php echo $cobro['ReservaCobro']['fecha']?></td>
                    <td><?php echo $cobro['Usuario']['nombre'].' '.$cobro['Usuario']['apellido']?></td>
                    <td><?php echo $cobro['ReservaCobro']['tipo']?></td>
                    <td><?php echo $cobro['ConceptoFacturacion']['nombre']?></td>
                    <td><a onclick="createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/'.$cobro['ReservaCobro']['id'], true);?>','430','400');"><?php echo $cobro['CobroCheque']['banco']?> <?php echo substr($cobro['CobroCheque']['numero'],strlen($cobro['CobroCheque']['numero'])-4);?> </a></td>
                    <td align="right">$<?php echo $cobro['CobroCheque']['monto_neto']?></td>
                    <td align="right">$<?php echo $cobro['CobroCheque']['interes']?></td> 
                    <td align="right">$<?php echo $cobro['CobroCheque']['monto_neto'] + $cobro['CobroCheque']['interes']?></td>                    
                </tr>
                <?php $pagado = $pagado + $cobro['CobroCheque']['monto_neto']; $intereses = $intereses + $cobro['CobroCheque']['interes']; break; 
            
                case 'EFECTIVO': ?>
                <tr>
                 
                    <td><img style="display:none;" src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" id="loading_delete<?php echo $cobro['ReservaCobro']['id'];?>" /><a onclick="eliminarCobro('<?php echo $cobro['ReservaCobro']['id'];?>')">eliminar</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
                    <td><?php echo $cobro['ReservaCobro']['fecha']?></td>
                    <td><?php echo $cobro['Usuario']['nombre'].' '.$cobro['Usuario']['apellido']?></td>
                    <td><?php echo $cobro['ReservaCobro']['tipo']?></td>
                    <td><?php echo $cobro['ConceptoFacturacion']['nombre']?></td>
                    <td> a <?php 
                    		switch ($cobro['CobroEfectivo']['moneda_id']) {
                              			case 1:
                              				$detalle ='';
                              			break;
                              			case 2:
                              				$detalle ='('.$cobro['CobroEfectivo']['monto_moneda'].' U$D x '.$cobro['CobroEfectivo']['cambio'].')';
                              			break;
                              			case 3:
                              				$detalle ='('.$cobro['CobroEfectivo']['monto_moneda'].' € x '.$cobro['CobroEfectivo']['cambio'].')';
                              			break;
                              		}
                    echo $cajas[$cobro['CobroEfectivo']['caja_id']].$detalle?> </td>
                    <td align="right">$<?php echo $cobro['CobroEfectivo']['monto_neto']?></td>
                    <td align="right">$0</td>
                    <td align="right">$<?php echo $cobro['CobroEfectivo']['monto_neto']?></td>
                    
                </tr>
                <?php $pagado = $pagado + $cobro['CobroEfectivo']['monto_neto']; break; 
            
                case 'TRANSFERENCIA': ?>
                <tr>
                    <td><a onclick="eliminarCobro('<?php echo $cobro['ReservaCobro']['id'];?>')">eliminar</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
                    <td><?php echo $cobro['ReservaCobro']['fecha']?></td>
                    <td><?php echo $cobro['Usuario']['nombre'].' '.$cobro['Usuario']['apellido']?></td>
                    <td><?php echo $cobro['ReservaCobro']['tipo']?></td>
                    <td><?php echo $cobro['ConceptoFacturacion']['nombre']?></td>
                    <td><a onclick="createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/'.$cobro['ReservaCobro']['id'], true);?>','430','400');"> a <?php echo $cuentas[$cobro['CobroTransferencia']['cuenta_id']]?></a></td>
                    <td align="right">$<?php echo $cobro['CobroTransferencia']['monto_neto']?></td>
                    <td align="right">$<?php echo $cobro['CobroTransferencia']['interes']?></td>
                    <td align="right">$<?php echo $cobro['CobroTransferencia']['monto_neto'] + $cobro['CobroTransferencia']['interes']?></td>
                    
                </tr>
                <?php $pagado = $pagado + $cobro['CobroTransferencia']['monto_neto']; $intereses = $intereses + $cobro['CobroTransferencia']['interes']; break; 
            
            } //end switch 
         } //end foreach?>
                <tr class="saldo_pendiente">
                    <td align="right" colspan="5">Total</td>
                    <td align="right">$<?php echo $pagado; ?></td>
                    <td align="right">$<?php echo $intereses; ?></td>
                    <td align="right">$<?php echo $pagado + $intereses; ?></td>
                </tr>
    </table>
</div>
<?php }else{ 
    $pagado = 0 ;?>
     <div class="ym-gbox">Todavia no se ha registrado ningun pago</div>
<?php } //end if reserva cobros ?>




<?php 
$devoluciones = 0; 
if(count($reserva['ReservaDevolucion']) > 0){  
	
?>
    <div class="sectionTitle" style="margin-top: 10px;">Devoluciones</div>
    <div class="ym-gbox">
        <table width="100%" >
            <tr>
                <td width="50">&nbsp;</td>
                <td width="80"><strong>Fecha</strong></td>
            <td width="100"><strong>Usuario</strong></td>
            <td width="120"><strong>Tipo</strong></td>
            <td><strong>Informacion</strong></td>
            <td width="75" align="right"><strong>Neto</strong></td>
            <td width="75" align="right"><strong>Interes</strong></td> 
            <td width="75" align="right"><strong>Cobrado</strong></td>
            </tr>
            <?php 
            $totalMonto=0;
            $totalInteres=0;
            $totalMontoInteres=0;
            foreach($reserva['ReservaDevolucion'] as $devolucion){
			        $devoluciones += $devolucion['monto'];
			        $totalMonto +=$devolucion['monto'];
            		$totalInteres +=$devolucion['interes'];
            		$totalMontoInteres +=$devolucion['monto'] + $devolucion['interes'];
			    ?>
            <tr>
                <td align="left"><a onclick="eliminarDevolucion('<?php echo $devolucion['id'];?>')">eliminar</a></td>
                <td><?php echo $devolucion['fecha'];?></td>
                <td><?php echo $devolucion['usuario']?></td>
                <td><?php echo $devolucion['forma_pago']?></td>
                <td><?php echo $devolucion['detalle'].' - '.$devolucion['motivo'];?></td>
               
                <td align="right">$<?php echo $devolucion['monto'];?></td>
                <td align="right">$<?php echo $devolucion['interes']?></td>
                <td align="right">$<?php echo $devolucion['monto'] + $devolucion['interes']?></td>
            </tr>
            <?php } ?>
           


		<tr class="saldo_pendiente">
                    <td align="right" colspan="5">Total</td>
                    <td align="right">$<?php echo $totalMonto; ?></td>
                    <td align="right">$<?php echo $totalInteres; ?></td>
                    <td align="right">$<?php echo $totalMontoInteres; ?></td>
                </tr>
        </table>
        
    </div>
 <?php }?> 

<div class="ym-gbox saldo_pendiente">
    Cobro neto de intereses: $<?php echo $pagado?><br/>
    <?php if($devoluciones > 0){ ?> Devoluciones: $<?php echo $devoluciones?><br/> <?php } ?>
    <?php 
    $pendiente = Round($reserva['Reserva']['total'] + $no_adelantadas - $pagado - $descuentos + $devoluciones,2);
    $pendiente = ($pendiente==-0)?0:$pendiente;
    
    echo "Saldo pendiente: $".$pendiente;
    ?>
</div>

<?php echo $this->Form->hidden('pendiente',array('value' => $reserva['Reserva']['total'] + $no_adelantadas - $pagado - $descuentos + $devoluciones)); ?>
<?php if($grilla){ ?>
<span id="botonVolver" onclick="volver();" class="boton volver">Volver a la grilla</span>
<?php }?>
<?php echo $this->Form->end(); ?>

<script>
function eliminarDescuento(cobro_id){
    if(confirm('Seguro desea eliminar el descuento?')){
        $.ajax({
            url : '<?php echo $this->Html->url('/reserva_cobros/eliminar', true);?>',
            type : 'POST',
            dataType: 'json',
            data: {'cobro_id' : cobro_id},
            success : function(data){
                location.reload();
            }
        });
    }
}
function eliminarCobro(cobro_id){
	$('#loading_delete'+cobro_id).show();
    if(confirm('Seguro desea eliminar el cobro?')){
        $.ajax({
            url : '<?php echo $this->Html->url('/reserva_cobros/eliminarCobro', true);?>',
            type : 'POST',
            dataType: 'json',
            data: {'cobro_id' : cobro_id},
            success : function(data){
            	if(data.resultado == 'ERROR'){
                        alert(data.mensaje+' '+data.detalle);
                }
                location.reload();
            }
        });
    }
    $('#loading_delete'+cobro_id).hide();
}

function eliminarDevolucion(id){
	
    if(confirm('Seguro desea eliminar la devolucion?')){
        $.ajax({
            url : '<?php echo $this->Html->url('/reserva_devolucions/eliminar', true);?>',
            type : 'POST',
            dataType: 'json',
            data: {'id' : id},
            success : function(data){
            	if(data.resultado == 'ERROR'){
                        alert(data.mensaje+' '+data.detalle);
                }
                location.reload();
            }
        });
    }
   
}

function agregar_descuento(){
    $.ajax({
        url : '<?php echo $this->Html->url('/reserva_descuentos/agregar', true);?>',
        dataType: 'html',
        data: $('form').serialize(),
        success : function(data){
            $('#descuento').html(data);
        }
    });
}

function agregar_cobro(){ 
    $('.error-message').remove();
    $('#btn_agregar_cobro').hide();
    $('#btn_agregar_cobro2').show();
    $('#ReservaCobroMontoNeto').prop('disabled', false);
    $.ajax({
    	
        url : '<?php echo $this->Html->url('/reserva_cobros/validar.json', true);?>',
        dataType: 'json',
        type: 'post',
        data: $('form').serialize(),
        success : function(data){
            if(data.error != ''){
                var model = 'ReservaCobro';
                $.each(data.error,function(item,error){
                    var campo = new String(item).split("_");
                    if(campo.length > 0){
                        var div_id = "";
                        $.each(campo, function(x,palabra){
                            div_id += palabra.charAt(0).toUpperCase() + palabra.slice(1);
                        });                    
                    }
                    $('#'+model+div_id).after('<div class="error-message">'+error+'</div>');
                });
                $('#ReservaCobroMontoNeto').prop('disabled', true);
            }else{
                switch($('#ReservaCobroTipo').val()){
                    case 'TARJETA':
                        var aurl = '<?php echo $this->Html->url('/cobro_tarjetas/agregar', true);?>';
                        break;
                     
                    case 'CHEQUE':
                        var aurl = '<?php echo $this->Html->url('/cobro_cheques/agregar', true);?>';
                        break;
                        
                    case 'EFECTIVO':
                        var aurl = '<?php echo $this->Html->url('/cobro_efectivos/agregar/'.$usuario['Usuario']['id'], true);?>';
                        break;
                        
                    case 'TRANSFERENCIA':
                        var aurl = '<?php echo $this->Html->url('/cobro_transferencias/agregar/'.$usuario['Usuario']['id'], true);?>';
                        break;
                }
                
                $.ajax({
                    url : aurl,
                    type: 'post',
                    data: $('form').serialize(),
                    dataType: 'html',
                    success : function(data){
                        $('#forma_cobro').hide();
                        $('#descuento').hide();
                        $('#cobro_tipos').html(data);
                        
                    }
                })
            }
            $('#btn_agregar_cobro').show();
            $('#btn_agregar_cobro2').hide();
        }
    })
}
function volver(){
	document.location = "<?php echo $this->Html->url('/informes/index_ventas_grilla', true);?>";
    
}

$('#ReservaCobroTipo').change(function(){
	$('#ReservaCobroMontoMoneda').val(0);
	$('#ReservaCobroCambio').val(0);
	$('#ReservaCobroMontoNeto').val(0);
	if($(this).val()=='EFECTIVO'){
		$('#ReservaCobroMonedaId').val(1);
		$('#divMoneda').show();
	}
	else{
		$('#divMoneda').hide();
		$('#divMonto').hide();
	    $('#divCambio').hide();
	}
})


$('#ReservaCobroMonedaId').change(function(){
	$('#ReservaCobroMontoMoneda').val(0);
	$('#ReservaCobroCambio').val(0);
	$('#ReservaCobroMontoNeto').val(0);
	switch($(this).val()) {
	  case '1':
	  		$('#ReservaCobroMontoNeto').prop('disabled', false);
	    	$('#divMonto').hide();
	     	$('#divCambio').hide();
	    break;
	  
	  default:
	  		$('#ReservaCobroMontoNeto').prop('disabled', true);
	    	$('#divMonto').show();
	     	$('#divCambio').show();
	} 
})

$('#ReservaCobroMontoMoneda').change(function(){
	var monto = 0;
	var cambio=0;
	if($('#ReservaCobroMontoMoneda').val()!=''){
		monto = parseFloat($('#ReservaCobroMontoMoneda').val());
	}
	if($('#ReservaCobroCambio').val()!=''){
		cambio = parseFloat($('#ReservaCobroCambio').val());
	}
	var total = monto*cambio;
	$('#ReservaCobroMontoNeto').val(parseFloat(total));
})

$('#ReservaCobroCambio').change(function(){
	var monto = 0;
	var cambio=0;
	if($('#ReservaCobroMontoMoneda').val()!=''){
		monto = parseFloat($('#ReservaCobroMontoMoneda').val());
	}
	if($('#ReservaCobroCambio').val()!=''){
		cambio = parseFloat($('#ReservaCobroCambio').val());
	}
	var total = monto*cambio;
	$('#ReservaCobroMontoNeto').val(parseFloat(total));
})
</script>