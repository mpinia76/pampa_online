<?php
$this->Js->buffer('$.datepicker.regional[ "es" ]');
$this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');

//formulario
echo $this->Form->create(null, array('url' => '/extras/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

switch($cobro['ReservaCobro']['tipo']){
    case 'TARJETA': 
        echo $this->Form->hidden('CobroTarjeta.id',array('value' => $cobro['CobroTarjeta']['id'])); ?>
        <div class="sectionTitle">Cobro con Tarjeta</div>
        <div class="ym-gbox">
            <strong>Locacion</strong> <br/>
            <?php echo $tarjeta_tipo['CobroTarjetaPosnet']['posnet']; ?>
        </div>
        <div class="ym-gbox">
            <strong>Tarjeta</strong> <br/>
            <?php echo $tarjeta_tipo['CobroTarjetaTipo']['marca']; ?>
        </div>
        <div class="ym-gbox">
            <strong>Cuotas</strong> <br/>
            <?php echo $cobro['CobroTarjeta']['cuotas']; ?>
        </div>
        <?php 
        echo $this->Form->input('CobroTarjeta.tarjeta_numero',array('value' => $cobro['CobroTarjeta']['tarjeta_numero']));
        echo $this->Form->input('CobroTarjeta.lote_nuevo',array('value' => $cobro['CobroTarjeta']['lote_nuevo'], 'label' => 'Lote','maxlength'=>'4'));
        echo $this->Form->input('CobroTarjeta.lote',array('value' => $cobro['CobroTarjeta']['lote'], 'label' => 'Nro liquidacion','maxlength'=>'8'));
        echo $this->Form->input('CobroTarjeta.autorizacion',array('value' => $cobro['CobroTarjeta']['autorizacion']));
        echo $this->Form->input('CobroTarjeta.cupon',array('value' => $cobro['CobroTarjeta']['cupon']));
        echo $this->Form->input('CobroTarjeta.titular',array('value' => $cobro['CobroTarjeta']['titular']));
        echo $this->Form->input('CobroTarjeta.fecha_pago',array('class'=>'datepicker','type'=>'text','value' => $cobro['CobroTarjeta']['fecha_pago']));
        /*echo $this->Form->input('CobroTarjeta.dni',array('value' => $cobro['CobroTarjeta']['dni']));
        echo $this->Form->input('CobroTarjeta.domicilio',array('value' => $cobro['CobroTarjeta']['domicilio']));
        echo $this->Form->input('CobroTarjeta.nacimiento',array('value' => $cobro['CobroTarjeta']['nacimiento']));*/
        //if($cobro['CobroTarjeta']['cobro_tarjeta_lote_id'] == 0){ ?>
            <span onclick="guardar('<?php echo $this->Html->url('/cobro_tarjetas/guardar.json', true);?>',$('form').serialize(),false);" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
        <?php //}else{ ?>
            <!-- <p align="center">Esta transaccion ya se encuentra dentro de un lote cerrado</p> -->
        <?php //} ?>
    <?php
    break;
    case 'CHEQUE':
        echo $this->Form->hidden('CobroCheque.id',array('value' => $cobro['CobroCheque']['id'])); ?>
        <div class="sectionTitle">Cobro con Cheque</div>
        <?php
        echo $this->Form->input('CobroCheque.numero',array('value' => $cobro['CobroCheque']['numero']));
        echo $this->Form->input('CobroCheque.banco',array('value' => $cobro['CobroCheque']['banco']));
        echo $this->Form->input('CobroCheque.tipo',array('value' => $cobro['CobroCheque']['tipo'], 'options' => $tipos));
        echo $this->Form->input('CobroCheque.librado_por',array('value' => $cobro['CobroCheque']['librado_por'])); 
        echo $this->Form->input('CobroCheque.fecha_cobro',array('value' => $cobro['CobroCheque']['fecha_cobro'], 'class' => 'datepicker', 'type' => 'text')); 
        echo $this->Form->input('CobroCheque.cuit',array('value' => $cobro['CobroCheque']['cuit'])); 
        echo $this->Form->input('CobroCheque.a_la_orden_de',array('value' => $cobro['CobroCheque']['a_la_orden_de'], 'label' => 'A la orden de'));
        if($cobro['CobroCheque']['acreditado'] == 0){ ?><span onclick="guardar('<?php echo $this->Html->url('/cobro_cheques/guardar.json', true);?>',$('form').serialize(),false);" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span> <?php } 
    break;
    
    case 'TRANSFERENCIA':
        echo $this->Form->hidden('CobroTransferencia.id',array('value' => $cobro['CobroTransferencia']['id'])); ?>
        <div class="sectionTitle">Cobro con Transferencia</div>
        <?php
        echo $this->Form->input('CobroTransferencia.cuenta_id',array('options' => $cuentas, 'value' => $cobro['CobroTransferencia']['cuenta_id']));
        echo $this->Form->input('CobroTransferencia.quien_transfiere',array('label' => 'Quen transfiere', 'value' => $cobro['CobroTransferencia']['quien_transfiere'])); 
        echo $this->Form->input('CobroTransferencia.numero_operacion',array('label' => 'Numero de operacion', 'value' => $cobro['CobroTransferencia']['numero_operacion'])); 
        if($cobro['CobroTransferencia']['acreditado'] == 0){ ?><span onclick="guardar('<?php echo $this->Html->url('/cobro_transferencias/guardar.json', true);?>',$('form').serialize(),false);" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span> <?php } 
     break;
 } //end switch?>

<?php echo $this->Form->end(); ?>