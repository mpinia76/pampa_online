    <table width="100%" cellspacing="0">
    <tr class="titulo">
        <td width="150"><?php echo $ano?></td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td  width="90""><?php echo $meses[$i]?></td>
        <?php } ?>
        <td width="90">Total</td>
    </tr>
    
    <?php 
    //echo 'ad: '.$permisoAdelantada;
    if($permisoAdelantada){
    foreach($adelantadas_rubro as $rubro_id=>$valores){ 
        if(array_sum($valores) > 0){ ?>
        <tr class="contenido extras_adelantados">
            <td align="left"><?php echo $extra_rubros[$rubro_id]?></td>
            <?php for($i=1; $i<=12; $i++){ ?>
            <td><?php echo $valores[$i]?></td>
            <?php } ?>
            <td><?php echo array_sum($valores)?></td>
        </tr>
    <?php }}} 
    if($permisoAdelantada){?>
    <tr class="contenido extras adelantados_totales">
        <td class="mes link">Extras adelantados totales</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $adelantadas[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($adelantadas);?></td>
    </tr>
    <?php 
    }
    if($permisoNoAdelantada){
    foreach($no_adelantadas_rubro as $rubro_id=>$valores){ 
        if(array_sum($valores) > 0){ ?>
        <tr class="contenido extras_no_adelantados">
            <td align="left"><?php echo $extra_rubros[$rubro_id]?></td>
            <?php for($i=1; $i<=12; $i++){ ?>
            <td><?php echo $valores[$i]?></td>
            <?php } ?>
            <td><?php echo array_sum($valores)?></td>
        </tr>
    <?php }}} 
    if($permisoNoAdelantada){?>
    <tr class="contenido extras no_adelantados_totales">
        <td class="mes link">Extras no adelantados totales</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php echo $no_adelantadas[$i]?></td>
        <?php } ?>
        <td><?php echo array_sum($no_adelantadas);?></td>
    </tr>
    <?php }?>
    <tr class="contenido extras_totales">
        <td class="mes link">Extras totales</td>
        <?php for($i=1; $i<=12; $i++){ ?>
        <td><?php 
        $totalAdelantada=($permisoAdelantada)?$adelantadas[$i]:0;
        $totalNoAdelantada=($permisoNoAdelantada)?$no_adelantadas[$i]:0;
        
        echo $totalAdelantada+$totalNoAdelantada;?></td>
        <?php } ?>
        <td><?php echo array_sum(($permisoAdelantada)?$adelantadas:array())+array_sum(($permisoNoAdelantada)?$no_adelantadas:array());?></td>
    </tr>
    
</table>

<p>&nbsp;</p>



<script>
$('tr').mouseover(function(){
    $('tr').removeClass('hover');
    $(this).addClass('hover');
});
$('.extras_totales').click(function(){
    $('.extras').toggle()
})
$('.adelantados_totales').click(function(){
    $('.extras_adelantados').toggle()
})
$('.no_adelantados_totales').click(function(){
    $('.extras_no_adelantados').toggle()
})
</script>