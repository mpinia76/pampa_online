<div class="content">
    <table width="100%">
        <tr>
            <td width="28%"><?php echo $this->Html->image('logo_s.jpg', array('width' => '150')); ?></td>
            <td width="36%" class="locacion">
                <table width="100%" cellspacing="0" cellpadding="0"><tr><td colspan="2">
                <span class="ciudad">Buenos Aires</span>  </td></tr>
                <tr><td colspan="2">Oficina de Reservas Puerto Madero </td></tr>
                <tr><td colspan="2">Tel:(011)-5254-7734 / (011)-5254-7735</td></tr>
                <tr><td style="padding-right: -4px;"> <?php echo $this->Html->image('whatsapp.png', array('width' => '15','align' => 'middle')); ?></td><td style="padding-left: -4px;">+54 9 11 7021-6426 </td></tr>
                <tr><td colspan="2" height="30px;" valign="top"><span style="font-weight:bold">Exclusivo Reservas </span> </td></tr>
                </table>
            </td>
            <td width="36%"  class="locacion">
            	<table width="100%" cellspacing="0" cellpadding="0"><tr><td colspan="2">
                <span class="ciudad">Mar de las Pampas</span> </td></tr>
                <tr><td colspan="2">Corvina e/Joaquin V. González y M. Sosa</td></tr>
                <tr><td colspan="2">(02255)-454244 / (02255)-454243</td></tr>
                <tr><td style="padding-right: -9px;"> <?php echo $this->Html->image('whatsapp.png', array('width' => '15','align' => 'middle')); ?></td><td style="padding-left: -9px;">+54 9 225 541-6128 </td></tr>
              <tr><td colspan="2" ><span style="font-weight:bold">Exclusivo Recepci&oacute;n </span>(No responde consultas de tarifas y disponibilidad) </td></tr>
                </table>
            </td>
        </tr>
    </table>
    <hr/>
    <h1>Confirmaci&oacute;n de la reserva</h1>
    <table width="680" align="center" cellpadding="3" cellspacing="3" border="0">
        <tr>
            <td width="200"><strong>N&uacute;mero de la reserva</strong></td>
            <td><?php echo $reserva['Reserva']['numero'];?></td>
        </tr>
        <tr>
            <td width="200"v><strong>Titular de la reserva</strong></td>
            <td><?php echo $reserva['Cliente']['nombre_apellido'];?></td>
        </tr>
        <tr>
            <td width="200"><strong>Check In</strong></td>
            <td><?php echo $reserva['Reserva']['check_in'];?> <?php echo $reserva['Reserva']['hora_check_in'];?> hs.</td>
        </tr>
        <tr>
            <td width="200"><strong>Check Out</strong></td>
            <td><?php echo $reserva['Reserva']['check_out'];?> <?php echo $reserva['Reserva']['late_check_out'];?> hs.</td>
        </tr>
        <tr>
            <td width="200"><strong>Categoría de Apartamento</strong></td>
            <td><?php echo $apartamento['Categoria']['categoria'];?></td>
        </tr>
        <tr>
            <td width="200"><strong>Descripción</strong></td>
            <td><?php echo $apartamento['Categoria']['descripcion'];?></td>
        </tr>
        <tr>
            <td width="200"><strong>Cantidad de pasajeros</strong></td>
            <td><?php echo $reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'];?></td>
        </tr>
        <tr>
            <td width="200">Mayores</td>
            <td><?php echo $reserva['Reserva']['pax_adultos'];?></td>
        </tr>
        <tr>
            <td width="200">Menores</td>
            <td><?php echo $reserva['Reserva']['pax_menores'];?></td>
        </tr>
        <tr>
            <td width="200">Beb&eacute;s</td>
            <td><?php echo $reserva['Reserva']['pax_bebes'];?></td>
        </tr>


        <?php

     if(count($extras) > 0){ ?>
        <tr>
            <td colspan="2"><strong>Extras incluidos</strong></td>

        </tr>







        <?php foreach($extras as $extra){
       // print_r($extra);
        ?>
            <tr>
                <td class="border" width="400"> <?php echo utf8_encode($extra['Extra']['ExtraRubro']['rubro']);?> <?php echo utf8_encode($extra['Extra']['ExtraSubrubro']['subrubro']);?> <?php echo utf8_encode($extra['Extra']['detalle']); ?></td>
                <td class="border"><?php echo $extra['ReservaExtra']['cantidad'];?></td>
            </tr>
        <?php } ?>





    <?php } ?>
        <?php if($pendiente > 0){ ?>
        <tr>
            <td width="200"><strong>Saldo a pagar</strong></td>
            <td>$<?php echo $pendiente; ?></td>
        </tr>
        <?php } ?>

    </table>
    <p><strong><?php echo nl2br($voucher['Voucher']['restricciones']); ?></strong></p>

    <h1>Politica de cancelaci&oacute;n</h1>
    <p><?php echo nl2br($voucher['Voucher']['politica_cancelacion']); ?></p>
    <p>&nbsp;</p>
    <p align="center"><em>Gracias por habernos elegido!</em></p>
</div>
