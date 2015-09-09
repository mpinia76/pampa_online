<div class="label">Permisos</div>
<div class="content">
<?
include_once("config/db.php");
if(isset($dataid) and $dataid!=''){

	$sql = "SELECT usuario_permiso.permiso_id,permiso.permiso_grupo_id FROM usuario_permiso INNER JOIN permiso ON usuario_permiso.permiso_id=permiso.id WHERE usuario_permiso.usuario_id = $dataid";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){
		$permiso[$rs['permiso_id']] = true;
		$grupo[$rs['permiso_grupo_id']] = true;
	}

}

$sql = "SELECT permiso.id,permiso.nombre,permiso_grupo.nombre as grupo,permiso_grupo.id as grupo_id FROM permiso INNER JOIN permiso_grupo ON permiso.permiso_grupo_id=permiso_grupo.id ORDER BY permiso_grupo.id";

$rsTemp = mysql_query($sql);

while($rs = mysql_fetch_array($rsTemp)){

	if($rs['grupo_id'] != $grupo_id){?>
	<input <? if($grupo[$rs['grupo_id']]){ ?> checked="checked" <? } ?> type="checkbox" onClick="$('div[id=<?=$rs['grupo_id']?>]').toggle();"> <strong><?=$rs['grupo']?></strong><br>
	<? } ?>
	
	<div id="<?=$rs['grupo_id']?>" style=" <? if(!$grupo[$rs['grupo_id']]){ ?> display:none; <? } ?> margin-left:50px;">
	<input <? if($permiso[$rs['id']]){ ?> checked="checked" <? } ?> name="permisos[]" type="checkbox" value="<?=$rs['id']?>"><?=$rs['nombre']?></div>
	
<? 		
	$grupo_id = $rs['grupo_id'];
}
?>
<p><strong>Cajas</strong><br>
<?
$sql = "SELECT caja.*,usuario_caja.caja_id FROM `usuario_caja` RIGHT JOIN caja ON caja.id=usuario_caja.caja_id AND usuario_caja.usuario_id = '$dataid' ORDER BY caja.caja ASC";
$rsTemp = mysql_query($sql);
while($rs = mysql_fetch_array($rsTemp)){ ?>

	<input <? if($rs['id'] == $rs['caja_id']){ ?> checked="checked" <? } ?> type="checkbox" name="cajas[]" value="<?=$rs['id']?>"> <?=$rs['caja']?><br>

<? } ?>
</p>

<p><strong>Cuenta</strong><br>
<?
$sql = "SELECT cuenta.*,usuario_cuenta.cuenta_id FROM `usuario_cuenta` RIGHT JOIN cuenta ON cuenta.id=usuario_cuenta.cuenta_id AND usuario_cuenta.usuario_id = '$dataid' ORDER BY cuenta.nombre ASC";
$rsTemp = mysql_query($sql);
echo mysql_error();
while($rs = mysql_fetch_array($rsTemp)){ ?>

	<input <? if($rs['id'] == $rs['cuenta_id']){ ?> checked="checked" <? } ?> type="checkbox" name="cuentas[]" value="<?=$rs['id']?>"> <?=$rs['nombre']?><br>

<? } ?>
</p>
</div>
<div style="clear:both;"></div>