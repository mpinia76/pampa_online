<ul class="action_bar">
    <li class="boton excel"><a onclick="descargar();">Excel</a></li>
</ul>
<table width="100%" cellspacing="0">
    <tr class="titulo">
        <td>Nombre Apellido <input type="checkbox" checked="checked" id="colNombre" name="colNombre"></input></td>
        <td>DNI <input type="checkbox" checked="checked"  id="colDni" name="colDni"></input></td>
        <td>Telefono <input type="checkbox" checked="checked"  id="colTelefono" name="colTelefono"></input></td>
        <td>Celular <input type="checkbox" checked="checked"  id="colCelular" name="colCelular"></input></td>
        <td>Direccion <input type="checkbox" checked="checked"  id="colDireccion" name="colDireccion"></input></td>
        <td>Localidad <input type="checkbox" checked="checked"  id="colLocalidad" name="colLocalidad"></input></td>
        <td>E-mail <input type="checkbox" checked="checked"  id="colEmail" name="colEmail"></input></td>



    </tr>
    <?php

    foreach($clientes as $cliente){

             ?>
    <tr class="contenido">


            <td align="left"><?php echo $cliente['nombre_apellido']; ?></td>
            <td align="left"><?php echo $cliente['dni']; ?></td>

            <td align="left"><?php echo $cliente['telefono']; ?></td>
            <td align="left"><?php echo $cliente['celular']; ?></td>
            <td align="left"><?php echo $cliente['direccion']; ?></td>
            <td align="left"><?php echo $cliente['localidad']; ?></td>
            <td align="left"><?php echo $cliente['email']; ?></td>




    </tr>
    <?php } ?>

</table>
<script>
$('tr').mouseover(function(){
    $('tr').removeClass('hover');
    $(this).addClass('hover');
});
</script>
