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
            <td width="200"v><strong>Titular de la reserva</strong></td>
            <td><?php echo $reserva['Cliente']['nombre_apellido'];?></td>
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
        <tr>
            <td width="200"><strong>Categoría de Apartamento</strong></td>
            <td><?php echo $apartamento['Categoria']['categoria'];?></td>
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
            <td width="200"><strong>Tarifa acordada</strong></td>
            <td>$<?php echo $total; ?></td>
        </tr>
        <tr>
            <td width="200"><strong>Pago recibido</strong></td>
            <td>$<?php echo $pagado; ?></td>
        </tr>
        <tr>
            <td width="200"><strong>Saldo a pagar</strong></td>
            <td>$<?php echo $pendiente; ?></td>
        </tr>
        <tr>
            <td width="200"><strong>N&uacute;mero de la reserva</strong></td>
            <td><?php echo $reserva['Reserva']['id'];?></td>
        </tr>
    </table>
    <p><strong>* Le recordamos que el apartamento no est&aacute; equipado con bater&iacute;a de cocina, s&oacute;lo posee vajilla para la cantidad de personas que ingresen.</strong></p>
    <p><strong>* La COCHERA CUBIERTA es un Servicio Opcional, por lo que solo estar&aacute; incluido si lo reserva con anticipaci&oacute;n.</strong></p>
    <p><strong>* PISCINA CUBIERTA CLIMATIZADA (VIER-SAB-DOM).</strong></p>
    <p><strong>* LA PISCINA DESCUBIERTA SE ENCUENTRA ATEMPERATURA AMBIENTE.</strong></p>
    <p>&nbsp;</p>
    <p><strong>Politica de cancelaci&oacute;n</strong></p>
    <p>Si la cancelaci&oacute;n se solicita con una anticipaci&oacute;n mayor a 10 d&aacute;as previos a la fecha de ingreso del pasajero, el total depositado quedar&aacute; como cr&eacute;dito aplicable a futuras reservas para el establecimiento contratado inicialmente, sujeto a disponibilidad y con una vigencia de tres meses o 180 d&iacute;as. Las tarifas a aplicar, ser&aacute;n las vigentes para ese momento.</p>
    <p>Si la cancelaci&oacute;n se realiza entre los 10 y 4 d&iacute;as previos a la fecha de ingreso del pasajero, se aplicar&aacute; un gasto de cancelaci&oacute;n equivalente al valor de una estad&iacute;a de 24 hs. proporcional a la tarifa contratada y el saldo del dep&oacute;sito quedar&aacute; como cr&eacute;dito aplicable a futuras reservas para el establecimiento contratado sujeto a disponibilidad con una vigencia de tres meses.</p>
    <p>Si la cancelaci&oacute;n se produce dentro de las 72hs previas al ingreso o el mismo d&iacute;a del se retendr&aacute; el 100 % del dinero abonado en concepto de gasto de cancelaci&oacute;n.</p>
    <p>No habr&aacute; reintegros ni devoluciones por No-Shows (ausencia al momento del check-in) o Early Check Outs (salida antes de la fecha de check-out estipulada).</p>
    <p>&nbsp;</p>
    <p align="center"><em>Gracias por habernos elegido!</em></p>
</div>
