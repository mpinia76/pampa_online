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
            <strong>Check In: </strong> <?php echo $reserva['Reserva']['check_in'];?> 15:00 hs. <strong>Check Out: </strong> <?php echo $reserva['Reserva']['check_out'];?> <?php echo $reserva['Reserva']['late_check_out'];?> hs.
        </div>
    </div>
    <div class="ym-g50 ym-gl" style="text-align: right;">
        <div class="ym-gbox">
            <strong>Monto total de la estadia:  $<?php echo $reserva['Reserva']['total_estadia']; ?> </strong> <br/>
            <strong>Extras adelantados:  $<?php echo $adelantadas; ?> </strong> <br/>
            <strong>Monto bruto de la reserva:  $<?php echo $reserva['Reserva']['total']; ?> </strong> <br/>
            <strong>Extras no adelantados:  $<?php echo $no_adelantadas; ?> </strong>
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
                <td><strong>Motivo</strong></td>
                <td width="75"  align="right"><strong>Monto</strong></td>
            </tr>
            <?php foreach($reserva_descuentos as $descuento){ 
                $descuentos = $descuentos + $descuento['Descuento']['monto'];?>
            <tr>
                <td align="left"><a onclick="eliminarDescuento('<?php echo $descuento['ReservaCobro']['id'];?>')">quitar</a></td>
                <td><?php echo $descuento['ReservaCobro']['fecha'];?></td>
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
    <div class="ym-g25 ym-gl">
        <?php echo $this->Form->input('ReservaCobro.fecha',array('class' => 'datepicker', 'type' => 'text', 'value' => date('d/m/Y'))); ?>
    </div>
    <div class="ym-g25 ym-gl">
        <div class="ym-gbox"><strong>Forma de cobro:</strong> 
            <select id="ReservaCobroTipo" name="data[ReservaCobro][tipo]" >
                <option value="">Seleccione...</option>
                <option value="TARJETA">Tarjeta</option>
                <option value="EFECTIVO">Efectivo</option>
                <option value="CHEQUE">Cheque</option>
                <option value="TRANSFERENCIA">Transferencia</option>
            </select>
        </div>
    </div>
    <div class="ym-g25 ym-gl">
        <?php echo $this->Form->input('ReservaCobro.monto_neto',array('class' => 'number', 'type' => 'text', 'label' => 'Monto libre de intereses')); ?>
    </div>
    <div class="ym-g25 ym-gl">
        <span id="btn_agregar_cobro" style="margin-top: 15px;" class="boton agregar" onclick="agregar_cobro();">+ agregar</span>
    </div>
</div>
<?php } ?>
<div id="cobro_tipos"></div>

<div class="sectionTitle">Historial de Cobros</div>

<div class="ym-gbox">
<?php if(count($reserva_cobros)>0){ 
    $pagado = 0; 
    $intereses = 0; ?>
    <table width="100%">
        <tr>
            <td width="55">&nbsp;</td>
            <td width="80"><strong>Fecha</strong></td>
            <td width="120"><strong>Tipo</strong></td>
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
                    <td><a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
                    <td><?php echo $cobro['ReservaCobro']['fecha']?></td>
                    <td><?php echo $cobro['ReservaCobro']['tipo']?></td>
                    <td><a onclick="createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/'.$cobro['ReservaCobro']['id'], true);?>','430','400');"><?php echo $tarjetas_tipo[$cobro['CobroTarjeta']['cobro_tarjeta_tipo_id']]?> - <?php echo $cobro['CobroTarjeta']['tarjeta_numero']?> <?php echo $cobro['CobroTarjeta']['cuotas']?> cuota/s</a></td>
                    <td align="right">$<?php echo $cobro['CobroTarjeta']['monto_neto']?></td>
                    <td align="right">$<?php echo $cobro['CobroTarjeta']['interes']?></td>
                    <td align="right">$<?php echo $cobro['CobroTarjeta']['monto_neto'] + $cobro['CobroTarjeta']['interes']?></td>                    
                </tr>
                <?php $pagado = $pagado + $cobro['CobroTarjeta']['monto_neto']; $intereses = $intereses + $cobro['CobroTarjeta']['interes']; break; 
                
                case 'CHEQUE': ?>
                <tr>
                    <td><a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
                    <td><?php echo $cobro['ReservaCobro']['fecha']?></td>
                    <td><?php echo $cobro['ReservaCobro']['tipo']?></td>
                    <td><a onclick="createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/'.$cobro['ReservaCobro']['id'], true);?>','430','400');"><?php echo $cobro['CobroCheque']['banco']?> <?php echo substr($cobro['CobroCheque']['numero'],strlen($cobro['CobroCheque']['numero'])-4);?> </a></td>
                    <td align="right">$<?php echo $cobro['CobroCheque']['monto_neto']?></td>
                    <td align="right">$<?php echo $cobro['CobroCheque']['interes']?></td> 
                    <td align="right">$<?php echo $cobro['CobroCheque']['monto_neto'] + $cobro['CobroCheque']['interes']?></td>                    
                </tr>
                <?php $pagado = $pagado + $cobro['CobroCheque']['monto_neto']; $intereses = $intereses + $cobro['CobroCheque']['interes']; break; 
            
                case 'EFECTIVO': ?>
                <tr>
                    <td ><a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
                    <td><?php echo $cobro['ReservaCobro']['fecha']?></td>
                    <td><?php echo $cobro['ReservaCobro']['tipo']?></td>
                    <td> a <?php echo $cajas[$cobro['CobroEfectivo']['caja_id']]?> </td>
                    <td align="right">$<?php echo $cobro['CobroEfectivo']['monto_neto']?></td>
                    <td align="right">$0</td>
                    <td align="right">$<?php echo $cobro['CobroEfectivo']['monto_neto']?></td>
                </tr>
                <?php $pagado = $pagado + $cobro['CobroEfectivo']['monto_neto']; break; 
            
                case 'TRANSFERENCIA': ?>
                <tr>
                    <td><a href="<?php echo $this->Html->url('/reserva_cobros/recibo/'.$cobro['ReservaCobro']['id'], true);?>">recibo</a></td>
                    <td><?php echo $cobro['ReservaCobro']['fecha']?></td>
                    <td><?php echo $cobro['ReservaCobro']['tipo']?></td>
                    <td><a onclick="createWindow('w_reservas_view_cobro','Detalles','<?php echo $this->Html->url('/reserva_cobros/detalle/'.$cobro['ReservaCobro']['id'], true);?>','430','400');"> a <?php echo $cuentas[$cobro['CobroTransferencia']['cuenta_id']]?></a></td>
                    <td align="right">$<?php echo $cobro['CobroTransferencia']['monto_neto']?></td>
                    <td align="right">$<?php echo $cobro['CobroTransferencia']['interes']?></td>
                    <td align="right">$<?php echo $cobro['CobroTransferencia']['monto_neto'] + $cobro['CobroTransferencia']['interes']?></td>
                    
                </tr>
                <?php $pagado = $pagado + $cobro['CobroTransferencia']['monto_neto']; $intereses = $intereses + $cobro['CobroTransferencia']['interes']; break; 
            
            } //end switch 
         } //end foreach?>
                <tr class="saldo_pendiente">
                    <td align="right" colspan="4">Total</td>
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
    foreach($reserva['ReservaDevolucion'] as $devolucion){
        $devoluciones += $devolucion['monto'];
    }
}
?>

<div class="ym-gbox saldo_pendiente">
    Cobro neto de intereses: $<?php echo $pagado?><br/>
    <?php if($devoluciones > 0){ ?> Devoluciones: $<?php echo $devoluciones?><br/> <?php } ?>
    Saldo pendiente: $<?php echo $reserva['Reserva']['total'] + $no_adelantadas - $pagado - $descuentos + $devoluciones?>
</div>

<?php echo $this->Form->hidden('pendiente',array('value' => $reserva['Reserva']['total'] + $no_adelantadas - $pagado - $descuentos + $devoluciones)); ?>
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
        }
    })
}
</script>