<strong>Informe Financiero</strong>: 
<select id="financiero_ano">
    <option>2012</option>
    <option>2013</option>
    <option>2014</option>
    <option>2015</option>
    <option>2016</option>
    <option>2017</option>
    <option>2018</option>
</select> <input type="button" onclick="ver_financiero();" value="Ver" />
<div id="informe_financiero"></div>
<script>
function ver_financiero(){
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/ventas_financiero', true);?>/'+$('#financiero_ano').val(),
        dataType: 'html',
        success: function(data){
            $('#informe_financiero').html(data);
        }
    })
}
</script>
<br/>
<strong>Informe Financiero Mensual</strong>: 
<select id="financiero_mesual_mes">
    <option value="01">Enero</option>
    <option value="02">Febrero</option>
    <option value="03">Marzo</option>
    <option value="04">Abril</option>
    <option value="05">Mayo</option>
    <option value="06">Junio</option>
    <option value="07">Julio</option>
    <option value="08">Agosto</option>
    <option value="09">Septiembre</option>
    <option value="10">Octubre</option>
    <option value="11">Noviembre</option>
    <option value="12">Diciembre</option>
</select> 
<select id="financiero_mensual_ano">
    <option>2012</option>
    <option>2013</option>
    <option>2014</option>
    <option>2015</option>
    <option>2016</option>
    <option>2017</option>
    <option>2018</option>
</select> <input type="button" onclick="ver_financiero_mensual();" value="Ver" />
<div id="financiero_mensual"></div>
<script>
function ver_financiero_mensual(){
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/ventas_economico_financiero', true);?>/'+$('#financiero_mesual_mes').val()+'/'+$('#financiero_mensual_ano').val(),
        dataType: 'html',
        success: function(data){
            $('#financiero_mensual').html(data);
        }
    })
}
</script>
