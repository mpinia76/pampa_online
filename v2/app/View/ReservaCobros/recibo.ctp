<div class="content">
    <table width="100%">
        <tr>
            <td width="70%" align="center" valign="top">
                <?php echo $this->Html->image('logo_s.jpg', array('width' => '130')); ?>
            </td>
            <td align="right" valign="top">
                <table widht="100%" class="top_info">
                    <tr><td align="right"><span class="titulo"><strong>Recibo de pago</strong></span></td></tr>
                    <tr><td align="right"><span class="titulo"><?php echo $cobro['ReservaCobro']['fecha'];?></span></td></tr>
                    <tr><td align="right"><span class="numero">0001 - <?php echo str_pad($cobro['ReservaCobro']['id'], 8, "0", STR_PAD_LEFT); ?></span></td></tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center">
                Corvina e/Joaquin V. González y M. Sosa, (7165) Mar de las Pampas, Pcia. de Bs. As.<br/>
                Tel.: (02255) 45-4243 / 4244 <br/>
                info@villagedelaspampas.com.ar
            </td>
            <td  align="right">
                C.U.I.T. 30-70840526-0 <br/>
                Ing.. Brutos: 30-70840526 <br/>
                Inicio de Actividades: 06/01/2004 <br/>
            </td>
        </tr>
    </table>
    <hr/>
    <table width="100%" class="info">
        <tr>
            <td><strong>Titular de la reserva:</strong> <?php echo $cliente['Cliente']['nombre_apellido']; ?></td>
        </tr>
        <?php if(!$cliente['Cliente']['cuit'] == ''){ ?>
        <tr>
            <td><strong>CUIT:</strong> <?php echo $cliente['Cliente']['cuit']; ?></td>
        </tr>
        <? }else{ ?>
        <tr>
            <td><strong>DNI:</strong> <?php echo $cliente['Cliente']['dni']; ?></td>
        </tr>
        <? } ?>
        <?php if(!$cliente['Cliente']['iva'] == ''){ ?>
        <tr>
            <td ><strong>IVA:</strong> <?php echo $cliente['Cliente']['iva']; ?></td>
        </tr>
        <? } ?>
        <tr>
            <td ><strong>Forma de pago:</strong> <?php echo $cobro['ReservaCobro']['tipo'];?></td>
        </tr>
        <tr>
            <td >&nbsp;</td>
        </tr>
        <tr>
            <td align="right"><span style="font-size: 18px;">Importe cobrado:</span> <span class="numero">$<?php echo number_format($cobro['ReservaCobro']['monto_cobrado'], 2, '.', '');?></span></td>
        </tr>
        <?php if($cobro['ReservaCobro']['monto_pendiente'] > 0){ ?>
        <tr>
            <td align="right">Saldo pendiente: <strong>$<?php echo number_format($cobro['ReservaCobro']['monto_pendiente'], 2, '.', '');?></strong></td>
        </tr>
        <?php } ?>
            
    </table>
    <p><strong>Politica de cancelaci&oacute;n</strong></p>
    <p><?php echo nl2br($voucher['Voucher']['politica_cancelacion']); ?></p>
    <p>&nbsp;</p>
    <p align="center"><em>Gracias por habernos elegido!</em></p>
    <p>&nbsp;</p>
    <table width="100%">
        <tr>
            <td width="5%">Firma:</td>
            <td style="border-bottom: 1px solid #000;">&nbsp;</td>
            <td align="right" width="15%">Aclaracion:</td>
            <td style="border-bottom: 1px solid #000;">&nbsp;</td>
        </tr>
    </table>
    <p>&nbsp;</p>
    <p>&nbsp;</p>
    <p align="center" style="font-size: 10px;">Formulario de control interno</p>
</div>