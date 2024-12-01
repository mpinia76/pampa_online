
<div class="ym-gbox" style="width: 25%; float: left;">
    <!-- Combo de Subrubros -->
    <?php echo $this->Form->input('Extra.extra_subrubro_id', array(
        'options' => $subrubros,
        'empty' => 'Seleccionar Subrubro',
        'type' => 'select',
        'id' => 'subrubroId',
        'label' => 'Subrubro'
    )); ?>
</div>

<div class="ym-gbox" style="width: 25%; float: left;">
    <!-- Combo de Detalles -->
    <?php echo $this->Form->input('Extra.detalle_id', array(
        'empty' => 'Seleccionar Detalle',
        'type' => 'select',
        'id' => 'ExtraId'
    )); ?>
</div>
<div class="ym-gbox" style="width: 10%; float: left;">
    <?php echo $this->Form->input('ReservaExtra.cantidad',array('value' => '1', 'type'=>'text', 'size' => '2', 'label' => 'Cant.')); ?>
</div>
<div class="ym-gbox" style="width: 10%; float: left;">
    <!-- Campo de Precio -->
    <?php echo $this->Form->input('Extra.precio', array(
        'type' => 'text',
        'readonly' => true,
        'id' => 'precioId'
    )); ?>
</div>

<div class="ym-gbox" style="width: 20%; float: right; margin-top:5px;"><span onclick="addExtra();" class="boton agregar">+ agregar</span></div>
<script>
    $(document).ready(function() {
        // Cargar Subrubros al seleccionar un Rubro


        // Cargar Detalles al seleccionar un Subrubro
        $('#subrubroId').change(function() {
            var subrubroId = $(this).val();

            if (subrubroId) {
                $.ajax({
                    url: '<?php echo $this->Html->url(array('controller' => 'extra_rubros', 'action' => 'obtenerDetalles')); ?>/' + subrubroId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        var detalleCombo = $('#ExtraId');
                        detalleCombo.empty();
                        detalleCombo.append('<option value="">Seleccionar Detalle</option>');

                        $.each(data.detalles, function(key, value) {
                            detalleCombo.append('<option value="' + key + '">' + value + '</option>');
                        });

                        // Limpiar campo de precio
                        $('#precioId').val('');
                    },
                    error: function() {
                        alert('Error al cargar detalles.');
                    }
                });
            }
        });

        // Mostrar Precio al seleccionar un Detalle
        $('#ExtraId').change(function() {
            var ExtraId = $(this).val();

            if (ExtraId) {
                $.ajax({
                    url: '<?php echo $this->Html->url(array('controller' => 'extra_rubros', 'action' => 'obtenerPrecio')); ?>/' + ExtraId,
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        $('#precioId').val(data.precio);
                    },
                    error: function() {
                        alert('Error al cargar el precio.');
                    }
                });
            } else {
                $('#precioId').val('');
            }
        });
    });
</script>


