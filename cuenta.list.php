<? include_once("config/db.php"); ?>
<div class="label">Cuenta</div>
<div class="content">
	<select name="cuenta_id">
	<? $sql = "SELECT banco.banco,cuenta_tipo.cuenta_tipo,cuenta.* FROM cuenta INNER JOIN cuenta_tipo ON cuenta.cuenta_tipo_id=cuenta_tipo.id INNER JOIN banco ON cuenta.banco_id=banco.id ORDER BY banco.banco";
	$rsTemp = mysql_query($sql);
	while($rs = mysql_fetch_array($rsTemp)){ ?>
	<option value="<?=$rs['id']?>" <? if($rs['id'] == $this->extra_variable){ ?> selected="selected" <? } ?> ><?=$rs['banco']?> <?=$rs['sucursal']?> <?=$rs['cuenta_tipo']?> <?=$rs['nombre']?></option>
	<? } ?>
	</select> <?=$cuenta_id?>
</div>
<div style="clear:both;"></div>