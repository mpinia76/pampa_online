<?php
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_reservas").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<div class="content">
    <h1><?php echo $this->Html->image('icon.jpg', array('height' => '30', 'align' => 'absmiddle')); ?> Planilla de reserva</h1>
    <hr/>
    <table width="100%">
        <tr>
            <td width="150"><strong>Creada: </strong> <?php echo $reserva['Reserva']['creado']; ?></td>
            <td align="center"><strong>Reservada por: </strong> <?php echo $reserva['Usuario']['nombre']." ".$reserva['Usuario']['apellido']?></td>
            <td width="100" align="right"><strong>Numero: </strong> <?php echo $reserva['Reserva']['numero']; ?></td>
        </tr>
    </table>
    <h2>Datos personales</h2>
    <table width="100%">
        <tr>
            <td width="50%"><strong>Nombre y Apelido: </strong> <?php echo $reserva['Cliente']['nombre_apellido']; ?></td>
            <td width="50%"><strong>DNI: </strong> <?php echo $reserva['Cliente']['dni']; ?></td>
        </tr>
        <tr>
            <td><strong>Telefono: </strong> <?php echo $telefono; ?></td>
            <td><strong>Celular: </strong> <?php echo $celular; ?></td>
        </tr>
        <tr>
            <td><strong>Direccion: </strong> <?php echo $reserva['Cliente']['direccion']; ?></td>
            <td><strong>Localidad: </strong> <?php echo $reserva['Cliente']['localidad']; ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Email: </strong> <?php echo $reserva['Cliente']['email']; ?></td>
        </tr>
        <tr>
            <td colspan="2" height="30"><em>Datos Adicionales</em></td>
        </tr>
        <tr>
            <td width="50%"><strong>IVA: </strong> <?php echo $reserva['Cliente']['iva']; ?></td>
            <td width="50%"><strong>CUIT: </strong> <?php echo $reserva['Cliente']['cuit']; ?></td>
        </tr>
        <tr>
            <td width="50%"><strong>Nacimiento: </strong> <?php echo $reserva['Cliente']['nacimiento']; ?></td>
            <td width="50%"><strong>Profesion: </strong> <?php echo $reserva['Cliente']['profesion']; ?></td>
        </tr>
        <tr>
            <td width="50%"><strong>1er contacto: </strong> <?php echo $reserva['Cliente']['1er_contacto']; ?></td>
            <td width="50%"><strong>Fumador: </strong> <?php echo $reserva['Cliente']['fumador']; ?></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Razones nos eligio: </strong> <?php echo $reserva['Cliente']['razones_eligio']; ?></td>
        </tr>
    </table>
    <h2>Datos de la estadia</h2>
    <table width="100%">
        <tr>
            <td width="33%"><strong>Apartamento: </strong><?php echo $reserva['Apartamento']['apartamento']; ?></td>
            <td width="33%"><strong>Check In: </strong><?php echo $reserva['Reserva']['check_in']; ?> <?php echo $reserva['Reserva']['hora_check_in']; ?></td>
            <td width="33%"><strong>Check Out: </strong><?php echo $reserva['Reserva']['check_out']; ?> <?php echo $reserva['Reserva']['late_check_out']; ?></td>
        </tr>
        <tr>
            <td colspan="3"><strong>Total Pasajeros: </strong><?php echo $reserva['Reserva']['pax_adultos'] + $reserva['Reserva']['pax_menores'] ; ?> </td>
        </tr>
        <tr>
            <td width="33%"><strong>Mayores: </strong><?php echo $reserva['Reserva']['pax_adultos']; ?></td>
            <td width="33%"><strong>Menores: </strong><?php echo $reserva['Reserva']['pax_menores']; ?></td>
            <td width="33%"><strong>Bebes: </strong><?php echo $reserva['Reserva']['pax_bebes']; ?> &nbsp; <strong>Practicuna: </strong> <?php echo $reserva['Reserva']['practicuna'] ? 'Si' : 'No' ?></td>
        </tr>
        <tr>
            <td colspan="3"><strong>A&B</strong></td>
        </tr>
        <tr>
            <td width="33%"><strong>Desayuno: </strong><?php echo $reserva['Reserva']['desayuno'] ? 'Si' : 'No'; ?></td>
            <td width="33%"><strong>Algunas Comidas: </strong><?php echo $reserva['Reserva']['algunas_comidas'] ? 'Si' : 'No'; ?></td>
            <td width="33%"><strong>Media Pension?: </strong><?php echo $reserva['Reserva']['media_pension'] ? 'Si' : 'No'; ?></td>
        </tr>

    </table>
     <?php
     if(count($extras) > 0){ ?>
    <h2>Extras</h2>
    <table width="100%">




        <tr>
            <td colspan="2"><strong>Detalle</strong></td>
            <td><strong>Cantidad</strong></td>
        </tr>
        <?php foreach($extras as $extra){
       // print_r($extra);
        ?>
            <tr>
                <td class="border" colspan="2"> <?php echo utf8_encode($extra['Extra']['ExtraRubro']['rubro']);?> <?php echo utf8_encode($extra['Extra']['ExtraSubrubro']['subrubro']);?> <?php echo utf8_encode($extra['Extra']['detalle']); ?></td>
                <td class="border"><?php echo $extra['ReservaExtra']['cantidad'];?></td>
            </tr>
        <?php } ?>
    </table>
    <?php } ?>
    <?php if($reserva['Reserva']['comentarios'] != ''){ ?>
    <h2>Comentarios</h2>
    <p style="padding-left:5px;"><?php echo nl2br($reserva['Reserva']['comentarios']); ?></p>
    <?php } ?>
    <p style="font-size:16px;" align="right"><strong>Saldo Pendiente: $<?php echo ($pendiente==-0)?0:$pendiente; ?></strong></p>
</div>
<?php if(!$pdf){ ?>
<span id="botonDescargar" onclick="descargar()" class="boton guardar">Descargar</span>
<span id="botonEnviar" onclick="enviarVoucher()" class="boton guardar">Enviar</span>

<script>

function descargar(){
	document.location = "<?php echo $this->Html->url('/reservas/plantilla/'.$reserva['Reserva']['id'].'/1', true);?>";

}

function enviarVoucher(){




	createWindow('w_enviar_palnilla','Enviar planilla','<?php echo $this->Html->url('/reservas/formMailPlanilla/'.$reserva['Reserva']['id'], true);?>','430','400');




}

</script>

<?php }?>
