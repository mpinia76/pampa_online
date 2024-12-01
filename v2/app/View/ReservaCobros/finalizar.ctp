<?php
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_reservas_finalizar").getPosition();
    xpos = position[0];
    ypos = position[1];
');
//formulario
echo $this->Form->create(null, array('url' => '/reserva_cobros/agregar','inputDefaults' => (array('div' => 'ym-gbox'))));
echo $this->Form->hidden('ReservaCobro.usuario_id',array('value' => $usuario['Usuario']['id']));
echo $this->Form->hidden('ReservaCobro.reserva_id',array('value' => $reserva['Reserva']['id']));
echo $this->Form->hidden('ReservaCobro.finalizado',array('value' => 1));
?>
<div class="ym-grid">
    <div class="ym-g50 ym-gl">
        <div class="ym-gbox">
            <strong>Titular:</strong> <?php echo $reserva['Cliente']['nombre_apellido'];?><br/>
            <strong>Apartamento: </strong> <?php echo $reserva['Apartamento']['apartamento']; ?> <br/>
            <strong>Check In: </strong> <?php echo $reserva['Reserva']['check_in'];?> <?php echo $reserva['Reserva']['hora_check_in'];?> hs. <strong>Check Out: </strong> <?php echo $reserva['Reserva']['check_out'];?> <?php echo $reserva['Reserva']['late_check_out'];?> hs.
        </div>
    </div>
    <div class="ym-g50 ym-gl" style="text-align: right; margin-top: 30px;">
        <div class="ym-gbox">
            <strong> Saldo pendiente antes de extras no adelantados:  $ <?php echo $pendiente_previo - $descontado; ?> </strong>
        </div>
    </div>
</div>

<div class="sectionTitle" style="margin-top: 10px;">Extras no adelantadas</div>
<?php if($reserva['Reserva']['estado'] == 0){ ?>
<div class="ym-grid">
    <div class="ym-g ym-gl" style="width: 15%;"><?php echo $this->Form->input('Extra.consumida',array('label' => 'Fecha','class' => 'datepicker', 'type' => 'text')); ?></div>
    <div class="ym-g ym-gl" style="width: 20%"><?php echo $this->Form->input('Extra.extra_rubro_id',array('label' => 'Seleccione un rubro', 'options' => $extra_rubros, 'empty' => 'Rubro', 'type'=>'select')); ?></div>
    <div class="ym-g ym-gl" id="extra_detalle" style="width: 65%;"></div>
</div>
<?php  } ?>
<div class="ym-gbox">
    <table width="100%" id="reserva_extras">
            <?php $total_extras = 0; 
                if(count($extras) > 0){ 
                    foreach($extras as $extra){
                        //print_r($extra['Usuario']);
                        if($extra['Extra']['id'] != ''){
                            $total_extras = $total_extras + ($extra['ReservaExtra']['cantidad']*$extra['ReservaExtra']['precio']); ?>
                            <tr class="border_bottom" id="ReservaExtra<?php echo $extra['ReservaExtra']['id']?>">
                                <td width="10%"><?php echo (!empty($extra['ReservaExtra']['consumida']))?date('d/m/Y',strtotime($extra['ReservaExtra']['consumida'])):"";?></td>
                                <td width="25%"><?php echo $extra['Extra']['ExtraRubro']['rubro'];?></td>
                                <td><?php echo $extra['Extra']['ExtraSubrubro']['subrubro'];?> <?php echo $extra['Extra']['detalle']; ?></td>
                                <td align="right" width="100"><span class="extra_cantidad"><?php echo $extra['ReservaExtra']['cantidad']?> x $<span class="extra_tarifa"><?php echo $extra['ReservaExtra']['precio']?></span></td>
                                <td align="right" width="50">$<?php echo $extra['ReservaExtra']['cantidad']*$extra['ReservaExtra']['precio']?></td>
                                <td align="right" width="50"><?php echo $extra['Usuario']['nombre'].' '.$extra['Usuario']['apellido']?></td>
                                <td align="right" width="50"><a onclick="quitarExtra('<?php echo $extra['ReservaExtra']['id']?>');">quitar</a></td>
                            </tr>
                        <?php }elseif($extra['ExtraVariable']['id'] != ''){ 
                            $total_extras = $total_extras + $extra['ReservaExtra']['precio']; ?>
                            <tr class="border_bottom" id="ReservaExtra<?php echo $extra['ReservaExtra']['id']?>">
                                <td width="10%"><?php echo (!empty($extra['ReservaExtra']['consumida']))?date('d/m/Y',strtotime($extra['ReservaExtra']['consumida'])):"";?></td>
                                <td width="25%"><?php echo $extra['ExtraVariable']['ExtraRubro']['rubro'];?></td>
                                <td colspan="2"><?php echo $extra['ExtraVariable']['detalle'];?> </td>
                                <?php if($reserva['Reserva']['estado'] == 0){ ?>
                                <td align="right" width="50">$<span class="extra_tarifa"><?php echo $extra['ReservaExtra']['precio']?></span></td>
                                    <td align="right" width="50"><?php echo $extra['Usuario']['nombre'].' '.$extra['Usuario']['apellido']?></td>
                                <td align="right" width="50"><a onclick=" quitarExtra('<?php echo $extra['ReservaExtra']['id']?>');">quitar</a></td>
                                <?php }else{ ?>
                                <td align="right" colspan="2" width="50">$<span class="extra_tarifa"><?php echo $extra['ReservaExtra']['precio']?></span></td>
                                <?php } ?>
                            </tr>
                       <?php } ?>
            <?php  }} ?>
    </table>
</div>
<div id="reserva_extras" class="ym-gbox extras_totales"  style="<?php  if(count($extras) == 0){  ?> display: none; <?php  } ?> text-align: right;">
    <strong>Total extras $<span class="extra_total"><?php echo $total_extras;?></span></strong></td>
</div>
<?php 

//if($restringido){
if ($permisoDescuento){
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
	                <td align="left">
			<?php if($permisoDescuento){ ?>
			<a onclick="eliminarDescuento('<?php echo $descuento['ReservaCobro']['id'];?>')">quitar</a>
			<?php } ?>
			</td>
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
	                <td align="left">
			<?php if($permisoDescuento){ ?>
			<a style="font-weight: normal;" onclick="agregar_descuento();">Agregar descuento</a>
			<?php } ?>
			</td>
	                <?php }else{ ?>
	                <td>&nbsp;</td>
	                <?php } ?>
	                <td align="right">Total descontado: $<?php echo $descuentos;?></td>
	            </tr>
	            <!--<tr class="saldo_pendiente">
	                <td align="left">&nbsp;</td>
	                <td align="right">Tarifa neta de descuentos (acordada): $<?php echo $reserva['Reserva']['total'] + $no_adelantadas -$descuentos;?></td>
	            </tr>-->
	        </table>
	    </div>
	 <?php }else{ ?> 
	    <?php if($reserva['Reserva']['estado'] == 0){ ?>
	        <div class="sectionTitle" style="margin-top: 10px;">Descuentos</div>
	        <div class="ym-gbox">
	            No se ha cargado ningun descuento 
		    <?php if($permisoDescuento){ ?>
		    <a onclick="agregar_descuento();">Agregar descuento</a>
		    <?php } ?>
	        </div>
	<?php }} ?>
	<div id="descuento" class="ym-gbox"></div>
<?php /*} 
else{
	$descuentos = $descontado; 
	
	/*if(count($reserva_descuentos) > 0){
		foreach($reserva_descuentos as $descuento){ 
	    	$descuentos = $descuentos + $descuento['Descuento']['monto'];
	    }
	}
}*/
}
?>
<?php if($permisoCobro){ ?>
<div class="sectionTitle">Cobros</div>
<!--lista de pagos finalizados-->
<div class="ym-gbox">
<?php 
    $pagado = 0; 
    $intereses = 0;
    if(count($reserva_cobros)>0){ ?>
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
                    <td><a onclick="eliminarCobro('<?php echo $cobro['ReservaCobro']['id'];?>')">eliminar</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
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
                    <td ><a onclick="eliminarCobro('<?php echo $cobro['ReservaCobro']['id'];?>')">eliminar</a>&nbsp;&nbsp;&nbsp;<a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
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
                    <td align="right">$<?php echo $cobro['CobroEfectivo']['monto_neto']; ?></td>
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
                <tr style="font-weight:bold;">
                    <td align="right" colspan="4">Total</td>
                    <td align="right">$<?php echo $pagado; ?></td>
                    <td align="right">$<?php echo $intereses; ?></td>
                    <td align="right">$<?php echo $pagado + $intereses; ?></td>
                </tr>
    </table>
</div>
<?php } ?>
<?php $saldo_pendiente = $pendiente_previo + $no_adelantadas - $pagado - $descuentos; ?>

<div class="ym-gbox saldo_pendiente">
    Cobrado neto de intereses: $<span id="total_pagado"><?php echo $pagado;?> </span><br/>
    Saldo pendiente: $<span id="total_a_cobrar"><?php echo $saldo_pendiente; ?></span>
    <?php echo $this->Form->hidden('ReservaCobro.pendiente',array('value'=>$pendiente_previo + $no_adelantadas - $pagado - $descuentos)); ?></td>
</div>

<!--AGREGAR COBRO-->
<?php if($reserva['Reserva']['estado'] == 0){ ?>
<div class="sectionSubtitle" style="margin-top: 5px;">Agregar cobro</div>
<div class="ym-grid" id="forma_cobro">
    <div class="ym-g8 ym-gl">
        <?php echo $this->Form->input('ReservaCobro.fecha',array('class' => 'datepicker', 'type' => 'text', 'value' => date('d/m/Y'))); ?>
    </div>
    <div class="ym-g8 ym-gl">
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
    <div id="divMoneda" class="ym-g8 ym-gl" style="display:none">
        <?php echo $this->Form->input('ReservaCobro.moneda_id',array( 'type' => 'select', 'options' => $monedas)); ?>
    </div>
    <div id="divMonto" class="ym-g8 ym-gl" style="display:none">
        <?php echo $this->Form->input('ReservaCobro.monto_moneda',array('class' => 'number', 'type' => 'text', 'label' => 'Monto')); ?>
    </div>
    <div id="divCambio" class="ym-g8 ym-gl" style="display:none">
        <?php echo $this->Form->input('ReservaCobro.cambio',array('class' => 'number', 'type' => 'text', 'label' => 'Cambio')); ?>
    </div>
    <div class="ym-g8 ym-gl">
        <?php echo $this->Form->input('ReservaCobro.monto_neto',array('class' => 'number', 'type' => 'text', 'label' => 'M. libre Int.')); ?>
    </div>
    <div class="ym-g8 ym-gl">
        <span id="btn_agregar_cobro" style="margin-top: 15px;" class="boton agregar" onclick="agregar_cobro();">+ agregar</span>
        <span id="btn_agregar_cobro2" style="margin-top: 15px;display:none" class="boton agregar" >Procesando...</span>
    </div>
</div>
<div id="cobro_tipos"></div>
 <?php } ?>
<?php } ?>
<?php if($permisoFactura){ ?>
<div class="sectionTitle">Facturas</div>
<?php 
$factura_total = 0;
if(count($facturas)>0){ ?>
<div class="ym-gbox">
    <table width="100%">
        <tr>

            <td width="100"><strong>Fecha</strong></td>
            <td width="100"><strong>Tipo</strong></td>
            <td width="250"><strong>Numero</strong></td>
            <td width="250"><strong>Titular</strong></td>
            <!--<td><strong>Punto de venta</strong></td>-->
            <td width="80" align="right"><strong>Monto</strong></td>
            <td></td>
        </tr>
        <?php foreach($facturas as $factura){ 
        //print_r($factura);
        ?>
        <tr>

            <td><?php echo $factura['ReservaFactura']['fecha_emision']?></td>
            <td><?php echo ($factura['ReservaFactura']['tipoDoc']==1)?'Factura':'Nota de credito'?></td>
            <td><?php echo $factura['ReservaFactura']['tipo']?> - <?php echo $factura['PuntoVenta']['numero']?> - <?php echo $factura['ReservaFactura']['numero']?></td>
            <td><?php echo $factura['ReservaFactura']['titular']?></td>
            <!-- <td><?php echo $factura['PuntoVenta']['puntoVenta']?></td> -->
            <td align="right">$<?php echo $factura['ReservaFactura']['monto']?></td>
            <td><a onclick="editarFactura('<?php echo $factura['ReservaFactura']['id'];?>')">editar</a>&nbsp;&nbsp;&nbsp;<a onclick="eliminarFactura('<?php echo $factura['ReservaFactura']['id'];?>')">eliminar</a>&nbsp;&nbsp;&nbsp;</td>
        </tr>
        <?php $factura_total = $factura_total + $factura['ReservaFactura']['monto']; } ?>
        <tr>
            <td colspan="4" align="right"><strong>Total: $<?php echo number_format($factura_total,2)?></strong></td>
        </tr>
    </table>
</div>
 <?php }  ?>
<!--AGREGAR FACTURA -->
<div class="sectionSubtitle" style="margin-top: 5px;">Agregar factura</div>
<div class="ym-grid">
	<div class="ym-g25 ym-gl"><?php echo $this->Form->input('ReservaFactura.tipoDoc',array('options' => $tipos_doc));?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('ReservaFactura.tipo',array('options' => $facturas_tipo, 'empty' => 'Seleccionar...'));?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('ReservaFactura.fecha_emision',array('class' => 'datepicker', 'type' => 'text', 'label' => 'Fecha de emision'));?></div>
    
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('ReservaFactura.punto_venta_id',array('empty' => 'Seleccionar...', 'type'=>'select', 'options' => $puntos_venta));?></div>
</div>
<div class="ym-grid">
	<div class="ym-g25 ym-gl"><?php echo $this->Form->input('ReservaFactura.numero',array('maxlength'=>'8'));?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('ReservaFactura.titular');?></div>
    <div class="ym-g25 ym-gl"><?php echo $this->Form->input('ReservaFactura.monto',array('type' => 'text', 'label' => 'Monto $', 'class' => 'number'));?></div>
    <div class="ym-g25 ym-gr"><span style="margin-top: 15px;" onclick="guardar('<?php echo $this->Html->url('/reserva_facturas/guardar.json', true);?>',$('form').serialize());"  class="boton agregar"> + agregar </span></div>
</div>
<?php } ?>
<script>
function eliminarFactura(factura_id){
    if(confirm('Seguro desea eliminar la factura?')){
        $.ajax({
            url : '<?php echo $this->Html->url('/reserva_facturas/eliminar', true);?>',
            type : 'POST',
            dataType: 'json',
            data: {'factura_id' : factura_id},
            success : function(data){
            	if(data.resultado == 'ERROR'){
                        alert(data.mensaje+' '+data.detalle);
                }
                location.reload();
            }
        });
    }
}
function editarFactura(factura_id){
    if(confirm('Seguro desea editar la factura?')){
    	createWindow('w_factura_edit','Editar','<?php echo $this->Html->url('/reserva_facturas/edit', true);?>/'+factura_id,'430','300');
    }
}
function addExtra(){
    if($('#ExtraConsumida').val() == ''){
        alert('Complete Fecha');
        $('#ExtraConsumida').focus();
        return false;
    }
    var pattern = /^(([1-9]\d*))$/;
    if(pattern.test($('#ReservaExtraCantidad').val())){
        $.ajax({
          url: '<?php echo $this->Html->url('/reserva_extras/getRow', true);?>',
          data: {'consumida' : $('#ExtraConsumida').val(),'extra_id' : $('#ExtraId').val(), 'cantidad' : $('#ReservaExtraCantidad').val(), 'reserva_id' : '<?php echo $reserva['Reserva']['id'];?>'},
          type: 'post',
          success: function(data){
              if(data == 'La fecha esta fuera de rango'){
                  alert(data);
              }
              else {
                  $('#reserva_extras').append(data);
                  $('.extras_totales').show();
              }
          },
          dataType: 'html'
        });
    }else{
        alert('Ingrese un numero natural mayor a cero');
        $('#ReservaExtraCantidad').focus();
    }
}
function addExtraVariable(){
    if($('#ExtraConsumida').val() == ''){
        alert('Complete Fecha');
        $('#ExtraConsumida').focus();
        return false;
    }
    var pattern = /^[1-9]|[0-9]*[.][1-9]+$/;
    if(!pattern.test($('#ReservaExtraPrecio').val())){
        alert('Ingrese un importe mayor a cero');
        $('#ReservaExtraPrecio').focus();
        return false;
    }
    if($('#ExtraVariableDetalle').val() == ''){
        alert('Complete con un detalle');
        $('#ExtraVariableDetalle').focus();
        return false;
    }
    $.ajax({
      url: '<?php echo $this->Html->url('/reserva_extras/getRowVariable', true);?>',
      data: {'consumida' : $('#ExtraConsumida').val(),'rubro_id' : $('#ExtraExtraRubroId').val(), 'precio' : $('#ReservaExtraPrecio').val(), 'detalle' : $('#ExtraVariableDetalle').val(), 'reserva_id' : '<?php echo $reserva['Reserva']['id'];?>'},
      type : 'post',
      success: function(data){
          if(data == 'La fecha esta fuera de rango'){
              alert(data);
          }
          else {
              $('#reserva_extras').append(data);
              $('.extras_totales').show();
              $('#ReservaExtraPrecio').val('');
              $('#ExtraVariableDetalle').val('');
          }
      },
      dataType: 'html'
    });
}
$('#ExtraExtraRubroId').change(function(){
    if($(this).val() != ""){
        $.ajax({
            url: '<?php echo $this->Html->url('/extra_rubros/obtenerSubrubros', true);?>/'+$(this).val(),
            success: function(data){
                $('#btn_add_extra').show();
                $('#extra_detalle').html(data);
                updateTotal();
            },
            dataType: 'html'
        });
    }else{
        $('#btn_add_extra').hide();
        $('#extra_detalle').html('');
    }
});

function updateTotal(){
    var result = 0;
    var extra_total = 0;
    result += parseFloat(<?php echo $pendiente_previo - $pagado - $descuentos; ?>);
    $(".extra_tarifa").each(function(index,obj) { 
        if($('#'+$(obj).parent().parent().parent().attr('id') + ' .extra_cantidad').length > 0){
            result += parseFloat($('#'+$(obj).parent().parent().parent().attr('id') + ' .extra_cantidad').text()) * parseFloat($(obj).text()); 
            extra_total += parseFloat($('#'+$(obj).parent().parent().parent().attr('id') + ' .extra_cantidad').text()) * parseFloat($(obj).text()); 
        }else{
            result += parseFloat($(obj).text()); 
            extra_total += parseFloat($(obj).text()); 
        }
    });
    $('#total_a_cobrar').html(result);
    $('#ReservaCobroPendiente').val(result);
    $('.extra_total').html(extra_total);
    if(extra_total == 0){
        $('.extras_totales').hide();
    }else{
        $('.extras_totales').show();
    }
}
function quitarExtra(reserva_extra_id){
    
    if(confirm('Seguro desea eliminar el extra?')){
        $.ajax({
            url : '<?php echo $this->Html->url('/reserva_extras/eliminar', true);?>',
            type : 'POST',
            dataType: 'json',
            data: { 'reserva_extra_id' : reserva_extra_id},
            success : function(data){
            	if(data.resultado == 'ERROR'){
                        alert(data.mensaje+' '+data.detalle);
                }
                location.reload();
            }
        });
    }
}

function eliminarCobro(cobro_id){
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
	  		
	    	$('#divMonto').hide();
	     	$('#divCambio').hide();
			$('#ReservaCobroMontoNeto').prop('disabled', false);
	    break;
	  
	  default:
	    	$('#divMonto').show();
	     	$('#divCambio').show();
	     	$('#ReservaCobroMontoNeto').prop('disabled', true);
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