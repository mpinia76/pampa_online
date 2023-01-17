<?php
$this->Js->buffer('
    oTable = $("#dataTable").dataTable( {
        "sDom": "<\"dataTables_top\"i>t<\"dataTables_bottom\"lp>r",
        "bProcessing": true,
        "bAutoWidth": false,
        "oLanguage": {
            "sUrl": "/dataTables.spanish.txt"
        },
        "fnDrawCallback": function( oSettings ) {
            $("#dataTable tr").unbind("dblclick").dblclick(function(){
                var data = oTable.fnGetData( this );
               
                createWindow("w_'.$this->params['controller'].'_view","Editar","'.$this->Html->url('/'.$this->params['controller'].'/edit', true).'/"+data[0],"430","300");
            });
            $("#dataTable tr").click(function(e){
                $("#dataTable tr").removeClass("row_selected");
                $(this).toggleClass("row_selected");
            });
            $("#dataTable a").removeAttr("href")
        },
        "aoColumns" : [
          {"bVisible" : false },
          null,
          null,
          null
        ]
    });
    $("#locacion").change(function(){ 
        if($(this).val() == ""){
            oTable.fnFilter($(this).val(),3);
        }else{
            oTable.fnFilter($("#locacion option:selected").text(),3);
         }
     });
');
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_'.$this->params['controller'].'").getPosition();
    xpos = position[0];
    ypos = position[1];
');
?>
<script>
    $(document).ready( function() {   // Esta parte del c�digo se ejecutar� autom�ticamente cuando la p�gina est� lista.
        $("#locacion").change();

    });
function edit(){
    var row = $('tr.row_selected');
    var data = oTable.fnGetData(row[0]);

    createWindow('w_<?php echo $this->params['controller'];?>_edit','Editar','<?php echo $this->Html->url('/'.$this->params['controller'].'/edit', true);?>/'+data,'430','300');
}
</script>
<ul class="action_bar">
    <li class="boton agregar"><a onclick="createWindow('w_<?php echo $this->params['controller'];?>_add','Crear','<?php echo $this->Html->url('/'.$this->params['controller'].'/add', true);?>','430','300');">Crear</a></li>
    <li class="boton editar"><a onclick="edit();">Editar</a></li>
    <li class="filtro">Activo <?php  echo $this->Form->input('locacion',array('type' => 'select', 'options' => array('NO','SI'), 'empty' => 'Seleccionar ...','default' => '1', 'label' => false, 'div' => false));?></li>
</ul>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr>
            <th>id</th>
            <th>Extra Rubro</th>
            <th>Extra Surubro</th>
            <th>Activo</th>

        </tr>
    </thead>
    <tbody>
        <?php 
        foreach($extras as $extra){
            //print_r($extra);
            ?>
        <tr>
            <td><?php echo $extra['ExtraSubrubro']['id']; ?></td>
            <td><?php echo $extra['ExtraRubro']['rubro']; ?></td>
            <td><?php echo $extra['ExtraSubrubro']['subrubro']; ?></td>

            <td><?php echo ($extra['ExtraSubrubro']['activo'])?'SI':'NO'; ?></td>

        </tr>
        <?php } ?>
    </tbody>
</table>