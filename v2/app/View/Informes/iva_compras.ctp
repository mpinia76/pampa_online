<ul class="action_bar">
    <li class="boton excel"><a onclick="descargar();">Excel</a></li>
</ul>
<table width="100%" cellspacing="0">
    <tr class="titulo">
        <td>Fecha comprobante</td>
        <td>As.Tipo</td>
        <td>Factura</td>
        <td>Proveedor</td>
        <td>Tercero</td>
        <td>CUIT</td>
        <td>Cond.</td>
        <td>Jurisd.</td>
        <td>Neto</td>
        <td>IVA 10.5%</td>
        <td>IVA 21%</td>
        <td>IVA 27%</td>
        
        
        <td>Otra al&iacute;cuota</td>
        <td>Percepci&oacute;n IVA</td>
        <td>Perc. IIBB Bs.As.</td>
        <td>Perc. IIBB CABA</td>
        <td>Exento</td>
        <td>Total factura</td>
       
       
    </tr>
    <?php 
    $total27=0;
    $total21=0;
    $total10_5=0;
    $totalOtraAlicuota=0;
    $totalperc_iva=0;
    $totalperc_iibb_bsas=0;
    $totalperc_iibb_caba=0;
    $totalexento=0;
    $totalMonto=0;
    $creditoFiscal = 0;
    foreach($gastos as $gasto){
             $total27 +=$gasto['iva_27'];
             $total21 +=$gasto['iva_21'];
             $total10_5 +=$gasto['iva_10_5'];
             $totalOtraAlicuota +=$gasto['otra_alicuota'];
             $totalperc_iva +=$gasto['perc_iva'];
             $totalperc_iibb_bsas +=$gasto['perc_iibb_bsas'];
             $totalperc_iibb_caba +=$gasto['perc_iibb_caba'];
             $totalexento +=$gasto['exento'];
             $totalMonto +=$gasto['monto'];
             
             $creditoFiscal +=$gasto['monto']-$gasto['iva_27']-$gasto['iva_21']-$gasto['iva_10_5']-$gasto['otra_alicuota']-$gasto['perc_iva']-$gasto['perc_iibb_bsas']-$gasto['perc_iibb_caba']-$gasto['exento'];
             ?>
    <tr class="contenido">
       
        
            <td><?php echo $gasto['fechaMostrar']; ?></td>
            <td><?php echo $gasto['origen']; ?></td>
            <td><?php echo $gasto['factura']; ?></td>
            
            <td align="left"><?php echo $gasto['proveedor']; ?></td>
            <td align="left"><?php echo $gasto['razon']; ?></td>
            <td align="left"><?php echo $gasto['cuit']; ?></td>
            <td align="left"><?php echo $gasto['condicionImpositiva']; ?></td>
            <td align="left"><?php echo $gasto['jurisdiccionInscripcion']; ?></td>
            
            <td align="right"><?php echo number_format($gasto['monto']-$gasto['iva_27']-$gasto['iva_21']-$gasto['iva_10_5']-$gasto['otra_alicuota']-$gasto['perc_iva']-$gasto['perc_iibb_bsas']-$gasto['perc_iibb_caba']-$gasto['exento'],2); ?></td>
            <td align="right"><?php echo number_format($gasto['iva_10_5'],2); ?></td>
            <td align="right"><?php echo number_format($gasto['iva_21'],2); ?></td>
            <td align="right"><?php echo number_format($gasto['iva_27'],2); ?></td>
            <td align="right"><?php echo number_format($gasto['otra_alicuota'],2); ?></td>
            <td align="right"><?php echo number_format($gasto['perc_iva'],2); ?></td>
            <td align="right"><?php echo number_format($gasto['perc_iibb_bsas'],2); ?></td>
            <td align="right"><?php echo number_format($gasto['perc_iibb_caba'],2); ?></td>
            <td align="right"><?php echo number_format($gasto['exento'],2); ?></td>
           <td align="right"><?php echo number_format($gasto['monto'],2); ?></td>
            
        
            
    </tr>
    <?php } ?>
    <tr class="contenido">
       
        
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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
            <td></td>
            <td></td>
            <td></td>
            
            <td align="right"><?php echo number_format($creditoFiscal,2); ?></td>
             <td align="right"><?php echo number_format($total10_5,2); ?></td>
             <td align="right"><?php echo number_format($total21,2); ?></td>
            <td align="right"><?php echo number_format($total27,2); ?></td>
            
           
            <td align="right"><?php echo number_format($totalOtraAlicuota,2); ?></td>
            <td align="right"><?php echo number_format($totalperc_iva,2); ?></td>
            <td align="right"><?php echo number_format($totalperc_iibb_bsas,2); ?></td>
            <td align="right"><?php echo number_format($totalperc_iibb_caba,2); ?></td>
            <td align="right"><?php echo number_format($totalexento,2); ?></td>
            
            <td align="right"><?php echo number_format($totalMonto,2); ?></td>
        
            
    </tr>
</table>
<script>
$('tr').mouseover(function(){
    $('tr').removeClass('hover');
    $(this).addClass('hover');
});
</script>