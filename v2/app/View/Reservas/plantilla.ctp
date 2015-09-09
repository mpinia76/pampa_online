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
            <td><strong>Telefono: </strong> <?php echo $reserva['Cliente']['telefono']; ?></td>
            <td><strong>Celular: </strong> <?php echo $reserva['Cliente']['celular']; ?></td>
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
            <td width="33%"><strong>Check In: </strong><?php echo $reserva['Reserva']['check_out']; ?> 15:00:00</td>
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
        <?php if(count($reserva['Extras']) > 0){ ?>
        <tr>
            <td colspan="3" height="30"><em>Detalle de extras contratados</em></td>
        </tr>
        <tr>
            <td colspan="2"><strong>Detalle</strong></td>
            <td><strong>Cantidad</strong></td>
        </tr>
        <?php foreach($reserva['Extras'] as $extra){ ?>
            <tr>
                <td class="border" colspan="2"> <?php echo $extra_rubros[$extra['extra_rubro_id']]?> <?php echo $extra_subrubros[$extra['extra_subrubro_id']]?> <?php echo $extra['detalle'];?></td>
                <td class="border"><?php echo $extra['ReservaExtra']['cantidad'];?></td>
            </tr>
        <?php }} ?>
    </table>
    <?php if($reserva['Reserva']['comentarios'] != ''){ ?>
    <h2>Comentarios</h2>
    <p style="padding-left:5px;"><?php echo nl2br($reserva['Reserva']['comentarios']); ?></p>
    <?php } ?>
    <p style="font-size:16px;" align="right"><strong>Saldo Pendiente: $<?echo $pendiente; ?></strong></p>
</div>