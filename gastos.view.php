<?
session_start();

$data 	= explode(",",$_GET['dataid']);

if(is_array($data) and count($data)>1 and ($_GET['action'] == 'consultar' or $_GET['action'] == 'editar' or $_GET['action'] == 'autorizar') ){ ?>

	<p align="center">Seleccione un s&oacute;lo registro</p>

<? }elseif(is_array($data) and count($data)>1 and ($_GET['action'] == 'abonar') or isset($_POST['datos']) ){

	$dataid = $_GET['dataid'];
	include("gastos.pagar.php");

}elseif(is_array($data) and count($data)==1){

	$dataid = $data[0];
	

	include_once("functions/fechasql.php");
	include_once("functions/date.php");
	include_once("functions/getProveedor.php");
	include_once("config/db.php");
	include_once("config/user.php");
	
	if(isset($_POST['aprobar'])){ //gasto aprobado
		$sql = "SELECT nro_orden FROM gasto ORDER BY nro_orden DESC LIMIT 1";
		$rs = mysql_fetch_array(mysql_query($sql));
		$nro_orden = $rs['nro_orden'] + 1; //obtengo el numero de orden
		
		$sql = "UPDATE gasto SET nro_orden=$nro_orden WHERE id=".$_POST['gasto_id'];
		mysql_query($sql); //guardo el numero de orden
		
		$dataid = $_POST['gasto_id'];
		$result = 1;
		$_GET['action'] = 'abonar';
		
	}elseif(isset($_POST['desaprobar'])){ //gasto desaprobado
		$sql = "UPDATE gasto SET estado=2 WHERE id=".$_POST['gasto_id'];
		mysql_query($sql);
		$dataid = $_POST['gasto_id'];
		$result = 1;
		$_GET['action'] = 'consultar';
	}elseif(isset($_POST['actualizar'])){ //actualizo algunos datos
		
		$result = 1;
		$sql = "UPDATE gasto SET 
					fecha='".fechasql($_POST['fecha'])."',
					rubro_id=".$_POST['rubro'].",
					subrubro_id=".$_POST['subrubro_id'].",
					proveedor='".$_POST['proveedor']."',
					descripcion='".$_POST['descripcion']."',
					factura_nro='".$_POST['factura_nro']."',
					factura_tipo='".$_POST['factura_tipo']."',
					factura_orden='".$_POST['factura_orden']."',
					remito_nro='".$_POST['remito_nro']."',
					recibo_nro='".$_POST['recibo_nro']."',
					monto='".$_POST['monto']."'
				WHERE id=".$_POST['gasto_id'];

		mysql_query($sql);

		
		$dataid = $_POST['gasto_id'];
		$_GET['action'] = 'editar';
		
	}elseif(isset($_POST['guardar'])){ //guardo los datos extras del gasto
		$dataid = $_POST['gasto_id'];
		$_GET['action'] = 'abonar';
		
		if($_POST['forma_pago']=='n'){
		
			$result = 'Debe seleccionar al menos una forma de pago';
			$dataid = $_POST['gasto_id'];
		
		}elseif( ($_POST['factura_nro'] != '' and $_POST['factura_tipo'] != 'n') or $_POST['remito_nro'] != '' or $_POST['recibo_nro'] != '' ){
		
			$operacion_monto = $_POST['gasto_monto'];
			include("functions/comprueba_pagos.php");
			
			if($procesa){
			
				$result = 1;
				$sql = "UPDATE gasto SET 
							estado=1,
							factura_nro='".$_POST['factura_nro']."',
							factura_tipo='".$_POST['factura_tipo']."',
							factura_orden='".$_POST['factura_orden']."',
							remito_nro='".$_POST['remito_nro']."',
							recibo_nro='".$_POST['recibo_nro']."'
						WHERE id=".$_POST['gasto_id'];
				mysql_query($sql);
	
				$operacion_id[] = $dataid;
				$operacion_tipo = 'gasto';
				
				include("functions/procesa_pagos.php");	
				
			}else{
				if(($operacion_monto+$monto_interes-$monto_descuento) != $monto_pagado){
					$result = 'Verifique que monto original ('.$operacion_monto.') mas los intereses ('.$monto_interes.') menos los descuentos ('.$monto_descuento.') sea igual al valor que intenta pagar ('.$monto_pagado.')';
				}elseif($fecha_error != 0){
					$result = 'La fecha ingresada no es correcta en alguna de las formas de pago';
				}elseif($error_cheque == true){
					$result = 'Debe completar el titular del cheque';
				}elseif($error_cheque_numero == true){
					$result = 'Ya existe un cheque del banco seleccionado y el numero ingresado';				
				}elseif($fecha_hoy == false){
					$result = 'Le fecha de pago no puede ser posterior a hoy';
				}else{
					$result = 'No se pudo procesar la operacion';
				}
			}
		
		}else{
			$result = 'No se guardo, debe completar con un n&uacute;mero de recibo, remito o factura';
		}
	}
	
	
	if(isset($dataid)){
		$sql = "SELECT usuario.nombre,usuario.apellido,gasto.*,subrubro.subrubro,subrubro.id as subrubro_id,rubro.rubro,rubro.id as rubro_id FROM gasto LEFT JOIN subrubro ON gasto.subrubro_id=subrubro.id INNER JOIN usuario ON gasto.user_id=usuario.id INNER JOIN rubro ON gasto.rubro_id=rubro.id WHERE gasto.id=$dataid";
		$rs = mysql_fetch_array(mysql_query($sql));
		
		$estado = $rs['estado'];
		$operacion_id = $dataid;
		$operacion_tipo = 'gasto';
	}
	?>
	
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
	<html xmlns="http://www.w3.org/1999/xhtml"> 
	<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <link rel="STYLESHEET" type="text/css" href="styles/toolbar.css"/>
	<link href="styles/form.css" rel="stylesheet" type="text/css" />
	<script src="library/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
	<!--JQuery UI-->
	<script src="library/jquery/ui/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
	<link href="library/jquery/ui/css/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />
	<script src="js/combobox-autosuggest.js" type="text/javascript"></script>
	<!--/JQuery UI-->
	
	<!--JQuery Date Picker-->
	<script type="text/javascript" src="library/datepicker/date.js"></script>
	<!--[if IE]><script type="text/javascript" src="library/datepicker/jquery.bgiframe.js"></script><![endif]-->
	<script type="text/javascript" src="library/datepicker/jquery.datePicker.min-2.1.2.js"></script>
	<link href="library/datepicker/datePicker.css" rel="stylesheet" type="text/css" />
	<style>
	a.dp-choose-date {
		float: left;
		width: 16px;
		height: 16px;
		padding: 0;
		margin: 5px 3px 0;
		display: block;
		text-indent: -2000px;
		overflow: hidden;
		background: url(images/calendar.png) no-repeat; 
	}
	a.dp-choose-date.dp-disabled {
		background-position: 0 -20px;
		cursor: default;
	}
	/* makes the input field shorter once the date picker code
	 * has run (to allow space for the calendar icon
	 */
	input.dp-applied {
		width: 140px;
		float: left;
	}
	
	.ui-autocomplete {
		max-height: 100px;
		overflow-y: auto;
	}
	
	</style>
	<?
	$pro_sql = "SELECT nombre FROM proveedor ORDER BY nombre ASC";
	$pro_rsTemp = mysql_query($pro_sql);
	while($pro_rs = mysql_fetch_array($pro_rsTemp)){
		$proveedores[] = $pro_rs['nombre'];
	}
	$proveedores = implode('", "',$proveedores);
	?>
	<script>

	$(function()
	{
		$('.date-pick').datePicker().trigger('change');
        $('.date-pick.date-edit').datePicker({startDate:'01/01/2010'}).trigger('change');

		$("#proveedores").combobox();
		
	});
	
	function createCombo(tabla,campo_id,campo,value){

        var datos = ({
            'tabla' : tabla,
            'campo_id' : campo_id,
            'campo' : campo,
            'value' : value
         });

        $.ajax({
            beforeSend: function(){
                $('#combo_loading').show();
            },
            data: datos,
            url: 'functions/createcombo.php',
            success: function(data) {
                $('#combo_loading').hide();
                $('#subrubro_combo').html(data);
                $('#subrubro').show();
            }
        });
    }
	</script>
	<script type="text/javascript">
	function addFormaDePago(forma_pago_id){
	
		var datos = ({
			'forma_pago' : forma_pago_id
		});
		
		$.ajax({
			beforeSend: function(){
				$('#forma_pago_loading').show();
			},
			data: datos,
			url: 'functions/formadepago.php',
			success: function(data) {
				$('#forma_pago_loading').hide();
				$('#forma_de_pago').append(data);
				$('.date-pick').datePicker().trigger('change');
			}
		});
	}
	</script>
	<script language="javascript" type="text/javascript"> 
	function vacio(q) {
		//funcion que chequea que los campos no sean espacios en blanco
		for ( i = 0; i < q.length; i++ ) {
				if ( q.charAt(i) != " " ) {
						return true
				}
		}
	return false
	}
	function valida(F) {
				if(F.rubro.value == 'null') {
				alert("Rubro es obligatorio")
				F.rubro.focus();
				return false
				}
				if(vacio(F.fecha.value) == false) {
				alert("Fecha es obligatorio")
				F.fecha.focus();
				return false
				}
				if(vacio(F.monto.value) == false) {
				alert("El monto es obligatorio")
				F.monto.focus();
				return false
				}
	}
	</script>
	</head>
	
	<body>
	<? if( isset($_POST['guardar']) or isset($_POST['actualizar']) or isset($_POST['aprobar']) or isset($_POST['desaprobar']) ) { ?>
	<script>
	var dhxWins = parent.dhxWins;
	dhxWins.window('w_gasto').attachURL('/v2/gastos/index');
	</script>
	<? } ?>
	
	<? include_once("config/messages.php"); ?>
                
	<div class="formContainer">
	<form method="post" name="form" action="gastos.view.php" onSubmit="return valida(this);">
		<input type="hidden" name="gasto_id" value="<?=$operacion_id?>" />
		<input type="hidden" name="gasto_monto" value="<?=$rs['monto']?>" />
		<fieldset>
			<legend>Detalle de gasto</legend> 
			<ul class="form">
				<li><label>Estado:</label>
				<span style="background:#FFFF99;">
				<? if($rs['estado'] == 0 and $rs['nro_orden'] == 0){ ?>
					<? $subestado = 1; ?>
					Pendiente de autorizaci&oacute;n
				<? }elseif($rs['estado'] == 0 and $rs['nro_orden'] != 0){ ?>
					<? $subestado = 2; ?>
					Gasto autorizado, pendiente de pago
				<? }elseif($rs['estado'] == 1 and $rs['nro_orden'] != 0 and $rs['factura_nro'] == ''){ ?>
					<? $subestado = 3; ?>
					Gasto autorizado, abonado, falta numero de factura 
				<? }elseif($rs['estado'] == 1 and $rs['nro_orden'] != 0 and $rs['factura_nro'] != ''){ ?>
					<? $subestado = 4; ?>
					Gasto autorizado, abonado, con numero de factura
				<? }elseif($rs['estado'] == 2){ ?>
					<? $subestado = 0; ?>
					Gasto no autorizado
				<? } ?>
				</span>
				</li>
				<li><label>Responsable:</label><?=$rs['nombre']?> <?=$rs['apellido']?></li>
				<? if($_GET['action'] == 'editar'  and $subestado < 3){ ?>
					<li><label>Fecha devengado:</label><input class="date-pick dp-applied" name="fecha" value="<?=fechavista($rs['fecha'])?>" /></li>
					<li><label>Rubro:</label>
					<select name="rubro" onChange="createCombo();">
					<?
					$sql2 = "SELECT id,rubro FROM rubro ORDER BY rubro";
					$rsTemp2 = mysql_query($sql2);
					while($rs2 = mysql_fetch_array($rsTemp2)){?>
					<option <? if($rs2['id']==$rs['rubro_id']){ ?> selected="selected" <? } ?> value="<?=$rs2['id']?>"><?=$rs2['rubro']?></option>
					<? } ?>
					</select> <img id="combo_loading" src="images/loading.gif" style="display:none" />
					<li id="subrubro"><label>Subrubro:</label>
						<div id="subrubro_combo">
							<select name="subrubro_id" size="1">
							<?
							$sql2 = "SELECT id,subrubro FROM subrubro WHERE rubro_id = ".$rs['rubro_id']." ORDER BY subrubro ";
							$rsTemp2 = mysql_query($sql2);
							while($rs2 = mysql_fetch_array($rsTemp2)){?>
							<option <? if($rs2['id']==$rs['subrubro_id']){ ?> selected="selected" <? } ?> value="<?=$rs2['id']?>"><?=$rs2['subrubro']?></option>
							<? } ?>
							</select> 
						</div>
					</li>
					<li><label>Proveedor:</label>
					<select id="proveedores" name="proveedor" size="1">
					<option value="">Seleccione uno...</option>
					<?
					$sql2 = "SELECT id,nombre FROM proveedor ORDER BY nombre ASC";
					$rsTemp2 = mysql_query($sql2);
					while($rs2 = mysql_fetch_array($rsTemp2)){?>
					<option <? if($rs2['id']==$rs['proveedor']){ $selected = true; ?> selected="selected" <? } ?> value="<?=$rs2['id']?>"><?=$rs2['nombre']?></option>
					<? } ?>
					<? if(!$selected){ ?>
					<option value="<?=$rs['proveedor']?>" selected="selected"><?=$rs['proveedor']?></option>
					<? } ?>
					</select>
					</li>
					<li><label>Descripcion:</label><textarea name="descripcion"><?=$rs['descripcion']?></textarea></li>
					<li><label>Remito:</label><input type="text" name="remito_nro" value="<?=$rs['remito_nro']?>" /></li>
					<li><label>Recibo:</label><input type="text" name="recibo_nro" value="<?=$rs['recibo_nro']?>" /></li>
					<? if($subestado == 2){ ?>
						<li><label>Factura:</label>
						<select size="1" name="factura_tipo">
							<option value="n">Tipo</option>
							<option value="A">A</option>
							<option value="B">B</option>
							<option value="C">C</option>
						</select> 
						<select size="1" name="factura_orden">
							<option value="B">0001</option>
							<option value="N">0002</option>
						</select> 
						<input type="text" name="factura_nro" /></li>
					<? } ?>
					
				<? }elseif( ($_GET['action'] == 'editar' and $subestado == 3)) { ?>					
					<li><label>Fecha devengado:</label><input class="date-pick date-edit dp-applied" name="fecha" value="<?=fechavista($rs['fecha'])?>" /></li>
					<li><label>Rubro:</label>
					<select name="rubro" onChange="createCombo('subrubro','rubro_id','subrubro',form.rubro.options[form.rubro.selectedIndex].value);">
					<?
					$sql2 = "SELECT id,rubro FROM rubro ORDER BY rubro";
					$rsTemp2 = mysql_query($sql2);
					while($rs2 = mysql_fetch_array($rsTemp2)){?>
					<option <? if($rs2['id']==$rs['rubro_id']){ ?> selected="selected" <? } ?> value="<?=$rs2['id']?>"><?=$rs2['rubro']?></option>
					<? } ?>
					</select> <img id="combo_loading" src="images/loading.gif" style="display:none" />
					<li id="subrubro"><label>Subrubro:</label>
						<div id="subrubro_combo">
							<select name="subrubro_id" size="1">
							<?
							$sql2 = "SELECT id,subrubro FROM subrubro WHERE rubro_id = ".$rs['rubro_id']." ORDER BY subrubro ";
							$rsTemp2 = mysql_query($sql2);
							while($rs2 = mysql_fetch_array($rsTemp2)){?>
							<option <? if($rs2['id']==$rs['subrubro_id']){ ?> selected="selected" <? } ?> value="<?=$rs2['id']?>"><?=$rs2['subrubro']?></option>
							<? } ?>
							</select> 
						</div>
					</li>
					<li><label>Proveedor:</label><?=getProveedor($rs['proveedor'])?></li>
					<input type="hidden" name="proveedor" value="<?=getProveedor($rs['proveedor'])?>" />
					<li><label>Descripcion:</label><?=$rs['descripcion']?></li>
					<input type="hidden" name="descripcion" value="<?=$rs['descripcion']?>" />
					<li><label>Remito:</label><input type="text" name="remito_nro" value="<?=$rs['remito_nro']?>" /></li>
					<li><label>Recibo:</label><input type="text" name="recibo_nro" value="<?=$rs['recibo_nro']?>" /></li>
					<li><label>Factura:</label>
					<select size="1" name="factura_tipo">
						<option value="n">Tipo</option>
						<option value="A">A</option>
						<option value="B">B</option>
						<option value="C">C</option>
					</select> 
					<select size="1" name="factura_orden">
						<option value="B">0001</option>
						<option value="N">0002</option>
					</select> 
					<input type="text" name="factura_nro" /></li>
				<? }elseif($_GET['action'] == 'abonar' and $subestado == 2){ ?>
					<li><label>Fecha devengado:</label><?=fechavista($rs['fecha'])?></li>
					<input type="hidden" name="fecha" value="<?=fechavista($rs['fecha'])?>" />
					<li><label>Rubro:</label><?=$rs['rubro']?></li>
					<input type="hidden" name="rubro" value="<?=$rs['rubro_id']?>" />
					<li><label>Sububro:</label><?=$rs['subrubro']?></li>
					<input type="hidden" name="subrubro_id" value="<?=$rs['subrubro_id']?>" />
					<li><label>Proveedor:</label><?=getProveedor($rs['proveedor'])?></li>
					<input type="hidden" name="proveedor" value="<?=getProveedor($rs['proveedor'])?>" />
					<li><label>Descripcion:</label><?=$rs['descripcion']?></li>
					<input type="hidden" name="descripcion" value="<?=$rs['descripcion']?>" />
					<li><label>Remito:</label><input type="text" name="remito_nro" value="<?=$rs['remito_nro']?>" /></li>
					<li><label>Recibo:</label><input type="text" name="recibo_nro" value="<?=$rs['recibo_nro']?>" /></li>
					<li><label>Factura:</label>
					<select size="1" name="factura_tipo">
						<option value="n">Tipo</option>
						<option value="A">A</option>
						<option value="B">B</option>
						<option value="C">C</option>
					</select> 
					<select size="1" name="factura_orden">
						<option value="B">0001</option>
						<option value="N">0002</option>
					</select> 
					<input type="text" name="factura_nro" /></li>
				<? }elseif($subestado == 4){ ?>
					<li><label>Fecha devengado:</label><input class="date-pick date-edit dp-applied" name="fecha" value="<?=fechavista($rs['fecha'])?>" /></li>
					<li><label>Rubro:</label>
					<select name="rubro" onchange="createCombo('subrubro','rubro_id','subrubro',form.rubro.options[form.rubro.selectedIndex].value);">
					<?
					$sql2 = "SELECT id,rubro FROM rubro ORDER BY rubro";
					$rsTemp2 = mysql_query($sql2);
					while($rs2 = mysql_fetch_array($rsTemp2)){?>
					<option <? if($rs2['id']==$rs['rubro_id']){ ?> selected="selected" <? } ?> value="<?=$rs2['id']?>"><?=$rs2['rubro']?></option>
					<? } ?>
					</select> <img id="combo_loading" src="images/loading.gif" style="display:none" />
					<li id="subrubro"><label>Subrubro:</label>
						<div id="subrubro_combo">
							<select name="subrubro_id" size="1">
							<?
							$sql2 = "SELECT id,subrubro FROM subrubro WHERE rubro_id = ".$rs['rubro_id']." ORDER BY subrubro ";
							$rsTemp2 = mysql_query($sql2);
							while($rs2 = mysql_fetch_array($rsTemp2)){?>
							<option <? if($rs2['id']==$rs['subrubro_id']){ ?> selected="selected" <? } ?> value="<?=$rs2['id']?>"><?=$rs2['subrubro']?></option>
							<? } ?>
							</select> 
						</div>
					</li>



					<li><label>Proveedor:</label><?=getProveedor($rs['proveedor'])?></li>
					<input type="hidden" name="proveedor" value="<?=getProveedor($rs['proveedor'])?>" />
					<li><label>Descripcion:</label><?=$rs['descripcion']?></li>
					<input type="hidden" name="descripcion" value="<?=$rs['descripcion']?>" />
					<li><label>Remito:</label><?=$rs['remito_nro']?></li>
					<input type="hidden" name="remito_nro" value="<?=$rs['remito_nro']?>" />
					<li><label>Recibo::</label><?=$rs['recibo_nro']?></li>
					<input type="hidden" name="recibo_nro" value="<?=$rs['recibo_nro']?>" />
					<li><label>Factura:</label>Tipo: <?=$rs['factura_tipo']?> Numero: <?=$rs['factura_nro']?></li>
					<input type="hidden" name="factura_orden" value="<?=$rs['factura_orden']?>" />
					<input type="hidden" name="factura_tipo" value="<?=$rs['factura_tipo']?>" />
					<input type="hidden" name="factura_nro" value="<?=$rs['factura_nro']?>" />
				<? }elseif($subestado > 0){ ?>
					<li><label>Fecha devengado:</label><?=fechavista($rs['fecha'])?></li>
					<input type="hidden" name="fecha" value="<?=fechavista($rs['fecha'])?>" />
					<li><label>Rubro:</label><?=$rs['rubro']?></li>
					<input type="hidden" name="rubro" value="<?=$rs['rubro_id']?>" />
					<li><label>Sububro:</label><?=$rs['subrubro']?></li>
					<input type="hidden" name="subrubro_id" value="<?=$rs['subrubro_id']?>" />
					<li><label>Proveedor:</label><?=getProveedor($rs['proveedor'])?></li>
					<input type="hidden" name="proveedor" value="<?=getProveedor($rs['proveedor'])?>" />
					<li><label>Descripcion:</label><?=$rs['descripcion']?></li>
					<input type="hidden" name="descripcion" value="<?=$rs['descripcion']?>" />
					<li><label>Remito:</label><?=$rs['remito_nro']?></li>
					<input type="hidden" name="remito_nro" value="<?=$rs['remito_nro']?>" />
					<li><label>Recibo::</label><?=$rs['recibo_nro']?></li>
					<input type="hidden" name="recibo_nro" value="<?=$rs['recibo_nro']?>" />
					<? if($subestado > 1){ ?>
					<li><label>Factura:</label>Tipo: <?=$rs['factura_tipo']?> Numero: <?=$rs['factura_nro']?></li>
					<input type="hidden" name="factura_orden" value="<?=$rs['factura_orden']?>" />
					<input type="hidden" name="factura_tipo" value="<?=$rs['factura_tipo']?>" />
					<input type="hidden" name="factura_nro" value="<?=$rs['factura_nro']?>" />
					<? } ?>
				<? } ?>
				<? if($_GET['action'] == 'editar' and $subestado < 3 and ACCION_39){ ?>
					<li><label>Monto neto:</label><input type="text" name="monto" value="<?=$rs['monto']?>" /></li>
				<? }else{ ?>
					<li><label>Monto neto:</label>$<?=$rs['monto']?></li>
                    <input type="hidden" name="monto" value="<?=$rs['monto']?>" />
				<? } ?>
				
				
	<? if($rs['nro_orden']==0 and $rs['estado']==0 and $_GET['action'] == 'autorizar'){ ?>
				
			</ul>
		</fieldset>
		<p align="center"><input type="submit" value="Aprobar gasto" name="aprobar" /> <input type="submit" value="Desaprobar gasto" name="desaprobar" /></p> 
	</form>
	
	<? }elseif($rs['estado']==0 and $_GET['action'] == 'autorizar' and $rs['nro_orden']!=0){ ?>
			</ul>
		</fieldset>
		<input type="hidden" name="gasto_id" value="<?=$operacion_id?>" />
	</form>
	
	<? }elseif($_GET['action'] == 'editar'){ ?>
			<? if($subestado >= 3) { include("pagos.view.php"); } ?>
			</ul>
		</fieldset>
		<p align="center"><input type="submit" value="Actualizar datos" name="actualizar" /></p> 
	</form>
	
	<? }elseif($rs['nro_orden']!=0 and $rs['estado']==0 and $_GET['action'] == 'abonar'){ ?>
	
				<li><label>Forma de pago:</label>
				<select name="forma_pago">
				<option value="n">Seleccionar...</option>
				<?
				$sql = "SELECT id,forma_pago FROM forma_pago ORDER BY forma_pago";
				$rsTemp = mysql_query($sql);
				while($rs = mysql_fetch_array($rsTemp)){?>
				<option value="<?=$rs['id']?>"><?=$rs['forma_pago']?></option>
				<? } ?>
				</select> &nbsp; <a style="cursor:pointer; color:#0000FF; text-decoration:underline;" onClick="addFormaDePago(form.forma_pago.options[form.forma_pago.selectedIndex].value)">agregar</a> <img id="forma_pago_loading" src="images/loading.gif" style="display:none" /></li>
				<div id="forma_de_pago"></div>
			</ul>
		</fieldset> 
		<p align="center"><input type="submit" value="Abonar" name="guardar" /></p> 
	</form>
				
	<? }elseif($rs['nro_orden']!=0 and $rs['estado']==1 and ($_GET['action'] == 'consultar' or $_GET['action'] == 'autorizar' or $_GET['action'] == 'abonar') ){ ?>
				<? include("pagos.view.php") ?>
			</ul>
		</fieldset>
	</form>
	
	<? }elseif($rs['estado']==2){ ?>
			</ul>
		</fieldset>
		<input type="hidden" name="gasto_id" value="<?=$operacion_id?>" />
		<p align="center">Este gasto fue desaprobado por administraci&oacute;n</p> 
	</form>
	
	<? } ?>
	</div> 
	</body>
	</html>
<? } ?>
