<div class="label">Permisos</div>
<div class="content">
<?php
include_once("config/db.php");
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if(isset($dataid) and $dataid!=''){

	$sql = "SELECT usuario_permiso.permiso_id,permiso.permiso_grupo_id FROM usuario_permiso INNER JOIN permiso ON usuario_permiso.permiso_id=permiso.id WHERE usuario_permiso.usuario_id = $dataid";
	$rsTemp = mysqli_query($conn,$sql);
	while($rs = mysqli_fetch_array($rsTemp)){
		$permiso[$rs['permiso_id']] = true;
		$grupo[$rs['permiso_grupo_id']] = true;
	}

}

$sql = "SELECT permiso.id,permiso.nombre,permiso_grupo.nombre as grupo,permiso_grupo.id as grupo_id FROM permiso INNER JOIN permiso_grupo ON permiso.permiso_grupo_id=permiso_grupo.id ORDER BY permiso_grupo.id";

$rsTemp = mysqli_query($conn,$sql);

while($rs = mysqli_fetch_array($rsTemp)){
	
	if($rs['grupo_id'] != $grupo_id){?>
	<input <?php  if($grupo[$rs['grupo_id']]){ ?> checked="checked" <?php  } ?> type="checkbox" onClick="$('div[id=<?php echo $rs['grupo_id']?>]').toggle();"> <strong><?php echo $rs['grupo']?></strong><br>
	<?php  } ?>
	
	<div id="<?php echo $rs['grupo_id']?>" style=" <?php  if(!$grupo[$rs['grupo_id']]){ ?> display:none; <?php  } ?> margin-left:50px;">
	
	
<?php  	
	if ($rs['id']==108) {
		$sql = "SELECT * FROM usuario_rubro WHERE usuario_id = $dataid";
		$rsTempUsuarioRubro = mysqli_query($conn,$sql);
		while($rsUsuarioRubro = mysqli_fetch_array($rsTempUsuarioRubro)){
			$rubro[$rsUsuarioRubro['rubro_id']] = true;
			
		}
		?>
		<input <?php  if($permiso[$rs['id']]){ ?> checked="checked" <?php  } ?> name="permisos[]" type="checkbox" value="<?php echo $rs['id']?>" onClick="$('div[id=divRubros]').toggle();"><?php echo $rs['nombre']?></div>
		
		<?php 
		
		$sql = "SELECT * FROM rubro WHERE gastos=1 ORDER BY rubro";

		$rsTempRubro = mysqli_query($conn,$sql);
		
		while($rsRubro = mysqli_fetch_array($rsTempRubro)){?>
			<div id="divRubros" style=" <?php  if(!$permiso[$rs['id']]){ ?> display:none; <?php  } ?> margin-left:100px;">
			<input <?php  if($rubro[$rsRubro['id']]){ ?> checked="checked" <?php  } ?> name="rubros[]" type="checkbox" value="<?php echo $rsRubro['id']?>" ><?php echo $rsRubro['rubro']?></div>
		<?php }
	}	
	else{?>
	<input <?php  if($permiso[$rs['id']]){ ?> checked="checked" <?php  } ?> name="permisos[]" type="checkbox" value="<?php echo $rs['id']?>"><?php echo $rs['nombre']?></div>
	<?php }
	$grupo_id = $rs['grupo_id'];
}
?>
<p><strong>Cajas</strong><br>
<?php 
$sql = "SELECT DISTINCT caja.*,usuario_caja.caja_id FROM `usuario_caja` RIGHT JOIN caja ON caja.id=usuario_caja.caja_id AND usuario_caja.usuario_id = '$dataid' ORDER BY caja.caja ASC";
$rsTemp = mysqli_query($conn,$sql);
while($rs = mysqli_fetch_array($rsTemp)){ ?>

	<input <?php  if($rs['id'] == $rs['caja_id']){ ?> checked="checked" <?php  } ?> type="checkbox" name="cajas[]" value="<?php echo $rs['id']?>"> <?php echo $rs['caja']?><br>

<?php  } ?>
</p>

<p><strong>Cuenta</strong><br>
<?php 
$sql = "SELECT DISTINCT cuenta.*,usuario_cuenta.cuenta_id FROM `usuario_cuenta` RIGHT JOIN cuenta ON cuenta.id=usuario_cuenta.cuenta_id AND usuario_cuenta.usuario_id = '$dataid' ORDER BY cuenta.nombre ASC";
$rsTemp = mysqli_query($conn,$sql);
//echo mysql_error();
while($rs = mysqli_fetch_array($rsTemp)){ ?>

	<input <?php  if($rs['id'] == $rs['cuenta_id']){ ?> checked="checked" <?php  } ?> type="checkbox" name="cuentas[]" value="<?php echo $rs['id']?>"> <?php echo $rs['nombre']?><br>

<?php  } ?>
</p>
</div>
<div style="clear:both;"></div>