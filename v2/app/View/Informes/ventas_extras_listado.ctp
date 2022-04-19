
<table width="100%" cellspacing="0">
    <tr class="titulo">
        <td>Fecha out</td>
        <td>Nro. Reserva</td>
        <td>Titular</td>
        <td>Departamento</td>
        <!-- <td>Adelantada</td>-->
        <td>Fecha carga</td>
        <td>Cantidad</td>
        <td>Rubro</td>
        <td>Subrubro</td>
        <td>Detalle</td>
        <td>Monto</td>
       
       
    </tr>
    <?php 
    $totalMonto=0;
    foreach($reservas as $reserva){
             
             $totalMonto +=$reserva['monto'];
             
             
             ?>
    <tr class="contenido">
       
        
            <td><?php echo $reserva['check_out']; ?></td>
            <td><?php echo $reserva['nro_reserva']; ?></td>
            
            <td align="left"><?php echo $reserva['titular']; ?></td>
            <td align="left"><?php echo $reserva['apartamento']; ?></td>
            <!--<td><?php echo $reserva['adelantada']; ?></td>-->
            <td><?php echo $reserva['agregada']; ?></td>
            <td><?php echo $reserva['cantidad']; ?></td>
            <td align="left"><?php echo $reserva['rubro']; ?></td>
            <td align="left"><?php echo $reserva['subrubro']; ?></td>
            <td align="left"><?php echo $reserva['detalle']; ?></td>
            <td align="right"><?php echo number_format($reserva['monto'],2); ?></td>
        
            
    </tr>
    <?php } ?>
    <tr class="contenido">
       
        
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <!--<td></td>-->
            <td></td>
            <td></td>
        	 <td></td>
            <td></td>
            <td></td>
            <td></td>
            
    </tr>
    <tr class="contenido">
       
        
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <!--<td></td>-->
            <td></td>
            <td></td>
             <td></td>
            <td></td>
        	<td></td>
            <td></td>
            
    </tr>
    <tr class="titulo">
       
        
            <td></td>
             <td></td>
            <td></td>
             <td></td>
            <td></td>
           <!--  <td></td>-->
            <td></td>
            <td></td>
            <td></td>
            <td align="right">Total</td>
            
            <td align="right"><?php echo number_format($totalMonto,2); ?></td>
        
            
    </tr>
</table>
<script>
$('tr').mouseover(function(){
    $('tr').removeClass('hover');
    $(this).addClass('hover');
});
</script>