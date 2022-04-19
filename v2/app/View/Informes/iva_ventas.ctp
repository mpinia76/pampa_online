<ul class="action_bar">
    <li class="boton excel"><a onclick="descargar();">Excel</a></li>
</ul>
<table width="100%" cellspacing="0">
    <tr class="titulo">
        <td>Fecha comprobante</td>
        <td>Factura/N. de credito</td>
        <td>Titular</td>
        <td>Nro. Reserva</td>
        <td>IVA</td>
        
        <td>Monto bruto</td>
       
       
    </tr>
    <?php 
    
    $total21=0;
    
    $totalMonto=0;
    
    foreach($reservas as $reserva){
             
             $total21 +=$reserva['iva_21'];
             
             $totalMonto +=$reserva['monto'];
             
             
             ?>
    <tr class="contenido">
       
        
            <td><?php echo $reserva['fechaMostrar']; ?></td>
            <td><?php echo $reserva['factura']; ?></td>
            <td align="left"><?php echo $reserva['titular']; ?></td>
            <td align="center"><?php echo $reserva['nroReserva']; ?></td>
            <td align="right"><?php echo number_format($reserva['iva_21'],2); ?></td>
            <td align="right"><?php echo number_format($reserva['monto'],2); ?></td>
        
            
    </tr>
    <?php } ?>
    <tr class="contenido">
       
        
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
            <td></td>
            <td></td>
            
        
            
    </tr>
    <tr class="titulo">
       
        
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td align="right"><?php echo number_format($total21,2); ?></td>
            <td align="right"><?php echo number_format($totalMonto,2); ?></td>
        
            
    </tr>
</table>
<script>
$('tr').mouseover(function(){
    $('tr').removeClass('hover');
    $(this).addClass('hover');
});
</script>