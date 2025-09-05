    <?php
    //calendario
    $this->Js->buffer('$.datepicker.regional[ "es" ]');
    $this->Js->buffer('$(".datepicker").datepicker({ dateFormat: "dd/mm/yy", altFormat: "yy-mm-dd" });');

    //dataTables
    $this->Js->buffer('
        oTable = $("#dataTable").dataTable( {
            "sDom": "<\"dataTables_top\"i>t<\"dataTables_bottom\"lp>r",
            "bProcessing": true,
            "bServerSide": true,
            "bAutoWidth": false,
            "oLanguage": {
                 "sUrl": "'.$this->webroot.'js/dataTables.spanish.txt"
            },
            "fnDrawCallback": function( oSettings ) {
                $("#dataTable tr").unbind("dblclick").dblclick(function(){
                    var data = oTable.fnGetData( this );
                    createWindow("w_reservas_view","Ver reserva","'.$this->Html->url('/reservas/editar', true).'/"+data[0],"630","600");
                });
                $("#dataTable tr").click(function(e){
                    $("#dataTable tr").removeClass("row_selected");
                    $(this).toggleClass("row_selected");
                });
            },
           
            "aaSorting": [],
            "sAjaxSource": "'.$this->Html->url('/reservas/dataTable', true).'",
            
            "aoColumns": [
                {"bVisible": false },
                null,
                {"sType": "date-uk"},
                null,
                null,
                {"sType": "date-uk"},
                {"sType": "date-euro"},
                null,
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bSortable": false},
                {"bVisible": false },
                {"bSortable": false},
                {"bSortable": false}
                
            ]
        });
        $(".date_filter").change(function(){ oTable.fnDraw(); })
        $("#data_search").keyup( function () { 
        $("#filter_estado").val("");
        oTable.fnFilter(this.value); });
        $("#filter_estado").change( function () { 
        $("#data_search").val("");
        oTable.fnFilter(this.value); });
        
    ');
    //extra libreria para agregar filtro de fecha
    echo $this->Html->script('dataTables.dateSort', array('block' => 'extra_scripts'));

    //abrir ventanas
    $this->Js->buffer('
        dhxWins = parent.dhxWins;
        position = dhxWins.window("w_reservas").getPosition();
        xpos = position[0];
        ypos = position[1];
    ');
    ?>
    <script>
    function agregarCobro(){
        var row = $("#dataTable tr.row_selected");
        if(row.length == 0){
            alert('Debe seleccionar un registro');
        }else{
            var data = oTable.fnGetData(row[0]);
            createWindow('w_reservas_add_cobro','Agregar Cobro','<?php echo $this->Html->url('/reserva_cobros/agregar', true);?>/'+data[0],'800','500');
        }
    }
    function extras_facturas(){
        var row = $("#dataTable tr.row_selected");
        if(row.length == 0){
            alert('Debe seleccionar un registro');
        }else{
            var data = oTable.fnGetData(row[0]);
            createWindow('w_reservas_finalizar','Finalizar reserva','<?php echo $this->Html->url('/reserva_cobros/finalizar', true);?>/'+data[0],'800','500');
            refreshOnClose('w_reservas_finalizar');
        }
    }
    function editar(){
        var row = $("#dataTable tr.row_selected");
        if(row.length == 0){
            alert('Debe seleccionar un registro');
        }else{
            var data = oTable.fnGetData(row[0]);
            if(data[15] != 2){
                createWindow("w_reservas_view","Ver reserva","<?php echo $this->Html->url('/reservas/editar', true);?>/"+data[0],"630","600");
            }else{
                alert('Esta reserva se encuentra cancelada');
            }
        }
    }
    function devolucion(){
        var row = $("#dataTable tr.row_selected");
        if(row.length == 0){
            alert('Debe seleccionar un registro');
        }else{
            var data = oTable.fnGetData(row[0]);

            createWindow("w_reservas_devolucion","Devoluciones de reserva","/pampa_online/reserva_devoluciones.php?reserva_id="+data[0],"830","400");
        }
    }
    function voucher(){
        var row = $("#dataTable tr.row_selected");
        if(row.length == 0){
            alert('Debe seleccionar un registro');
        }else{
            var data = oTable.fnGetData(row[0]);
            if(data[15] != 2){
                createWindow("w_reservas_voucher","Crear voucher","<?php echo $this->Html->url('/vouchers/actualizar', true);?>/"+data[0],"830","500");
            }else{
                alert('Esta reserva se encuentra cancelada');
            }
        }
    }
    function plantilla(){
        var row = $("#dataTable tr.row_selected");
        if(row.length == 0){
            alert('Debe seleccionar un registro');
        }else{
            var data = oTable.fnGetData(row[0]);
            createWindow("w_reservas_planilla","Planilla","<?php echo $this->Html->url('/reservas/plantilla', true);?>/"+data[0],"750","600");

        }
    }
    function cancelar(){
        var row = $("#dataTable tr.row_selected");
        if(row.length == 0){
            alert('Debe seleccionar un registro');
        }else{
            if(confirm("Esta seguro que desea cancelar la reserva?")){
                var data = oTable.fnGetData(row[0]);
                if(data[15] == 'FINALIZA' || data[15] == 0){
                    $('#loading_cancelar').show();
                    $.ajax({
                        url: "<?php echo $this->Html->url('/reservas/cancelar', true);?>",
                        data: {'reserva_id' : data[0]},
                        type: 'POST',
                        dataType: 'json',
                        success: function(data){
                            $('#loading_cancelar').hide();
                            if(data.resultado == 'ERROR'){
                                alert(data.mensaje);
                            }else{
                                document.location.reload();
                            }
                        }
                    })
                }else{
                    alert('No se puede cancelar esta reserva');
                }
            }
        }
    }

    function limpiarFechas(){
        $('#desde').val('');
        $('#hasta').val('');
    }

    function finalizar(){
        var row = $("#dataTable tr.row_selected");
        if(row.length == 0){
            alert('Debe seleccionar un registro');
        }else{
            var data = oTable.fnGetData(row[0]);
            if(data[15] == 'FINALIZA'){
                $('#loading_finalizar').show();
                $.ajax({
                    url: "<?php echo $this->Html->url('/reservas/finalizar', true);?>",
                    data: {'reserva_id' : data[0]},
                    type: 'POST',
                    dataType: 'json',
                    success: function(data){
                        $('#loading_cancelar').hide();
                        if(data.resultado == 'ERROR'){
                            alert(data.mensaje);
                        }
                        document.location.reload();
                    }
                })
            }else{
                alert('No se puede finalizar esta reserva');
            }
        }
    }
    </script>
    <ul class="action_bar">
        <li class="boton agregar"><a onclick="createWindow('w_reservas_add','Crear Reserva','<?php echo $this->Html->url('/reservas/crear', true);?>','630','600');">Crear</a></li>
        <li class="boton editar"><a onclick="editar();">Editar</a></li>
        <li class="boton abonar"><a onclick="agregarCobro();">Cobros</a></li>
        <li class="boton abonar"><a onclick="devolucion();">Devoluciones</a></li>
        <li class="boton abonar"><a onclick="extras_facturas();">Extras y facturas</a></li>
        <li class="boton abonar"><a onclick="cancelar();">Cancelar <img src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" id="loading_cancelar" align="absmiddle" /></a></li>
        <li class="boton abonar"><a onclick="finalizar();">Finalizar <img src="<?php echo $this->webroot; ?>img/loading.gif" class="loading" id="loading_finalizar" align="absmiddle" /></a></li>
        <li class="boton pdf"><a onclick="voucher();">Voucher</a></li>
        <li class="boton pdf"><a onclick="plantilla();">Planilla</a></li>

        <li> <input class="datepicker date_filter"  id="ffin" type="hidden" /> <input type="hidden" id="ffin_col" value="5"/></li>
        <li> <input class="datepicker date_filter" id="fini" type="hidden" /> <input type="hidden" id="fini_col" value="6"/></li>
        <table style="margin-top:-7px;float:right;">
        <tr><td>
        Buscar</td><td> <input id="data_search" type="text"/>

        </td>
        <td>
        Estado</td>
        <td><select id="filter_estado" style="width:150px;">
                <option value="" selected="selected">Todos</option>
                <option value="EST_1">Cobro pendiente</option>
                <option value="EST_2">Cobro parcial</option>
                <option value="EST_3">Cobrado: pendiente de cierre</option>
                <option value="EST_4">Cobrado: facturado</option>
                <option value="EST_5">Cobrado: discrepancia</option>
                <option value="EST_6">Cancelada: devolucion parcial</option>
                <option value="EST_7">Cancelada</option>
                <option value="EST_8">Revisar</option>
        </select>
        </td></tr></table>
    </ul>

      <p><b>Filtro campo: IN</b></p>

    <?php
        echo $this->Form->create(false, array('class' => 'form-inline'));

        echo $this->Form->input('year' ,array(
                                                'type' => 'date',
                                                'label' => false,
                                                'onChange' => 'limpiarFechas()',
                                                'minYear' => '2012',
                                                'maxYear' => date('Y') + 2,
                                                'dateFormat'=> 'Y',
                                                'style' => 'float: left; display:inline;'
                                               ));


        echo $this->Form->input('month' ,array(
       'type' => 'select',
       'label' => false,
       'onChange' => 'limpiarFechas()',
       'options' => array('Todos'=>'Todos','01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo', '06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'), 'default'=>'Todos',
       'style' => 'float: left; display:inline;'
       ));

       echo $this->Form->input('desde',array('label' => false,'placeholder' => 'Fecha Desde', 'class' => 'datepicker', 'type' => 'text','style' => 'float: left; display:inline;'));
       echo $this->Form->input('hasta',array('label' => false,'placeholder' => 'Fecha Hasta', 'class' => 'datepicker', 'type' => 'text','style' => 'float: left; display:inline;'));

    echo $this->Form->end('Cargar');
    ?>





    <table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable">
        <thead>
            <tr>
                <th width="50">Id</th>
                <th width="50">Numero</th>
                <th width="150">Fecha</th>
                <th width="250">Titular</th>
                <th width="250">Apartamento</th>
                <th width="150">In</th>
                <th width="150">Out</th>
                <th width="100">Alojamiento</th>
                <th width="100">Extras Ad.</th>
                <th width="100">Extras No Ad.</th>
                <th width="100">Tarifa bruta final</th>
                <th width="100">Descuento</th>
                <th width="100">Neto</th>
                <th width="100">Pendiente</th>
                <th width="150">Estado</th>
                <th width="150">Estado Num</th>
                <th width="50">Planilla</th>
                <th width="50">Voucher</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
