<strong>Informe Economico</strong>: 
<select id="economico_mes">
    <option>2012</option>
    <option>2013</option>
    <option>2014</option>
    <option>2015</option>
    <option>2016</option>
    <option>2017</option>
    <option>2018</option>
</select> <input type="button" onclick="ver_economico();" value="Ver" /> <span id="cargando" style="display:none;">Cargando ...</span>
<div id="informe_economico"></div>
<script>
function ver_economico(){
    $('#cargando').show();
    $.ajax({
        url: '<?php echo $this->Html->url('/informes/ventas_economico', true);?>/'+$('#economico_mes').val(),
        dataType: 'html',
        success: function(data){
            $('#cargando').hide();
            $('#informe_economico').html(data);
        }
    })
}
</script>
