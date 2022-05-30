<?php 
$i = 0;
foreach ($scaffoldFields as $_field):
    if($i == 0){ $cols[] = "{'bVisible':    false } "; }else{ $cols[] = "null"; }
    $i++;
endforeach;?>
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
            '.implode(",",$cols).'
        ]
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
function edit(){
    var row = $('tr.row_selected');
    var data = oTable.fnGetData(row[0]);
    createWindow('w_<?php echo $this->params['controller'];?>_edit','Editar','<?php echo $this->Html->url('/'.$this->params['controller'].'/edit', true);?>/'+data,'430','300');
}
function eliminar(){
    var row = $('tr.row_selected');
    var dataElem = oTable.fnGetData(row[0]);
    if(confirm('Seguro desea eliminar?')){
        $.ajax({
            url : '<?php echo $this->Html->url('/'.$this->params['controller'].'/eliminar', true);?>',
            type : 'POST',
            dataType: 'json',
            data: {'id' : dataElem},
            success : function(data){
                if(data.resultado == 'ERROR'){
                    alert(data.mensaje+' '+data.detalle);
                }
                location.reload();
            }
        });
    }
}

</script>
<ul class="action_bar">
    <li class="boton agregar"><a onclick="createWindow('w_<?php echo $this->params['controller'];?>_add','Crear','<?php echo $this->Html->url('/'.$this->params['controller'].'/add', true);?>','430','300');">Crear</a></li>
    <li class="boton editar"><a onclick="edit();">Editar</a></li>
    <?php if ($this->params['controller'] == 'grilla_feriados'){ ?>
        <li class="boton anular"><a onclick="eliminar();">Eliminar</a></li>
    <?php } ?>
</ul>
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
    <thead>
        <tr> 
            <?php foreach ($scaffoldFields as $_field):?>
                <th><?php echo $this->Paginator->sort($_field);?></th>
            <?php endforeach;?>
        </tr>
    </thead>
    <tbody>
    <?php
    $i = 0;
    
    foreach (${$pluralVar} as ${$singularVar}):
        echo "<tr>";
            foreach ($scaffoldFields as $_field) {
            	
                $isKey = false;
                if (!empty($associations['belongsTo'])) {
                    foreach ($associations['belongsTo'] as $_alias => $_details) {
                    	
                        if ($_field === $_details['foreignKey']) {
                            $isKey = true;
                            echo "<td>" .${$singularVar}[$_alias][$_details['displayField']]. "</td>";
                            break;
                        }
                    }
                }
                if ($isKey !== true) {
                	
                    	//echo "<br>";
                    echo "<td>" . h(${$singularVar}[$modelClass][$_field]) . "</td>";
                }
            }
        echo '</tr>';
    endforeach;
    ?>
    </tbody>
</table>