<?php

//abrir ventanas
$this->Js->buffer('
    dhxWins = parent.dhxWins;
    position = dhxWins.window("w_proveedors_add").getPosition();
    xpos = position[0];
    ypos = position[1];
');


//formulario
echo $this->Form->create(null, array('url' => '/proveedors/crear','inputDefaults' => (array('div' => 'ym-gbox'))));

?>
  <div class="ym-grid">
  	<div class="ym-g50 ym-gl">
        <span style="margin-top: 15px;" class="boton agregar" onclick="condicionImpositivaABM()">&nbsp;ABM Condicion Impositiva</span>
    </div>
    <div class="ym-g50 ym-gl">
        <span style="margin-top: 15px;" class="boton agregar" onclick="jurisdiccionInscripcionABM();">ABM Jurisdiccion Inscripcion</span>
    </div>
  </div>
<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Proveedor.nombre',array('label' => 'Nombre'));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Proveedor.razon',array('label' => 'Razon Social'));?></div>
	
</div>

<div class="ym-grid">
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Proveedor.cuit',array('label' => 'CUIT'));?></div>
	<div class="ym-g50 ym-gl"><?php echo $this->Form->input('Proveedor.condicion_impositiva_id',array('label' => 'Condicion Impositiva','empty' => 'Seleccionar', 'type'=>'select'));?></div>
</div>

<div class="ym-grid">
	
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Proveedor.jurisdiccion_inscripcion_id',array('label' => 'Jurisdiccion Inscripcion','empty' => 'Seleccionar', 'type'=>'select'));?></div>
    <div class="ym-g50 ym-gl"><?php echo $this->Form->input('Proveedor.rubro_id',array('label' => 'Rubro','empty' => 'Seleccionar', 'type'=>'select'));?></div>
</div>




<span onclick="guardar('guardar.json',$('form').serialize(),{id:'w_proveedors',url:'v2/proveedors/index'});" class="boton guardar">Guardar <img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" /></span>
<?php echo $this->Form->end(); ?>

<script>
function refreshOnClose(id){
            dhxWins.window(id).attachEvent("onClose", function(win){
                location.reload();
                return true;
            });
        }
function condicionImpositivaABM(){

    
    	
		createWindow('w_condicion_impositivas','ABM Condicion Impositiva','<?php echo $this->Html->url('/condicion_impositivas/index', true);?>','450','300'); //nombre de los divs
		refreshOnClose('w_condicion_impositivas');    	

}

function jurisdiccionInscripcionABM(){

    
    	
		createWindow('w_jurisdiccion_inscripcions','ABM Jurisdiccion Inscripcion','<?php echo $this->Html->url('/jurisdiccion_inscripcions/index', true);?>','450','300'); //nombre de los divs
		refreshOnClose('w_jurisdiccion_inscripcions');    	

}

</script>