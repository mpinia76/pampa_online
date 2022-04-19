<?php
function fechasql($fecha){
	$part=explode("/",$fecha);
	$mysql=$part[2]."-".$part[1]."-".$part[0];
	return $mysql;
}
function fechavista($fecha){
	$part=explode("-",$fecha);
	$mysql=$part[2]."/".$part[1]."/".$part[0];
	return $mysql;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Documento sin t&iacute;tulo</title>
<script type="text/javascript" src="library/jquery/jquery-1.4.2.min.js"></script>
<!--JQuery Date Picker-->
<script type="text/javascript" src="library/datepicker/date.js"></script>
<!--[if IE]><script type="text/javascript" src="library/datepicker/jquery.bgiframe.js"></script><![endif]-->
<script type="text/javascript" src="library/datepicker/jquery.datePicker.min-2.1.2.js"></script>
<link href="library/datepicker/datePicker.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" charset="utf-8">
var dhxWins = parent.dhxWins;

var position = dhxWins.window('w_informe_satisfaccion').getPosition(); //id de la ventana

var xpos = position[0];
var ypos = position[1];
$(function()
{
	$('.date-pick').datePicker({startDate:'01/01/2009'})
	$('#start-date').bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('#end-date').dpSetStartDate(d.addDays(1).asString());
			}
		}
	);
	$('#end-date').bind(
		'dpClosed',
		function(e, selectedDates)
		{
			var d = selectedDates[0];
			if (d) {
				d = new Date(d);
				$('#start-date').dpSetEndDate(d.addDays(-1).asString());
			}
		}
	);
});
function enviar(id){
	
		if(confirm('¿Seguro desea enviar la encuesta?')){
			createWindow('w_enviar_encuesta','Enviar encuesta','cron-encuesta.php?id='+id,'300','200'); //botones
			setTimeout('dhxWins.window("w_enviar_encuesta").close()', 2000);
			
		}
	
}
function mostrarGraficos(){
	
	$('#graficos').show();
	$('#noGraficos').hide();

}
function noMostrarGraficos(){
	
	$('#graficos').hide();
	$('#noGraficos').show();

}
</script>
<script src="js/createWindow.js"></script>
<style type="text/css">
a.dp-choose-date {
	float: left;
	width: 16px;
	height: 16px;
	padding: 0;
	margin: 5px 3px 0;
	display: block;
	text-indent: -2000px;
	overflow: hidden;
	background: url(http://www.villagedelaspampas.com.ar/scripts/calendar.png) no-repeat; 
}
a.dp-choose-date.dp-disabled {
	background-position: 0 -20px;
	cursor: default;
}
input.dp-applied {
	width: 70px;
	float: left;
}
.titulo_secundario {
	font-family: Arial, Helvetica, sans-serif;
	font-size:small;	
}
#titulo_pregunta{
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;
	font-weight:bold;
	border-bottom:#CCCCCC solid 1px;
	padding:3px;
	margin-top:10px;
}
#respuestas{
	font-family:Arial, Helvetica, sans-serif;
	font-size:10px;
	text-align:center;
	padding:5px;
}
#tabla{
	float:left;
	width:160px; 
	height:120px; 
	padding:5px; 
	text-align:center; 
	font-size:10px;
}
</style>
<link rel="STYLESHEET" type="text/css" href="styles/toolbar.css">
<!--/JQuery Date Picker-->

</head>

<body>
<ul id="menu">
	
	<li onclick="mostrarGraficos()" class="item"><img src="images/if_Cake_206472.png" align="absmiddle" />¿Que piensan nuestros clientes?</li>
	<li onclick="noMostrarGraficos()" class="item"><img src="images/ico_users.png" align="absmiddle" />¿Vamos a consultarles?</li>
	
</ul>
<?php 
$displayGraficos = (isset($_POST['buscar'])||isset($_POST['todos']))?'block':'none';
$displayNoGraficos = (isset($_POST['ver']))?'block':'none';
if (($displayGraficos=='none')&&($displayNoGraficos=='none')) {
	$displayGraficos='block';
	$_POST['todos']=1;
}
?>
<div id="graficos" style="display:<?php echo $displayGraficos;?>">
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<input name="verGraficos" type="hidden" value="1"/>
<p><div class="titulo_secundario" style="float:left; margin-top:3px;">Desde &nbsp;</div> 
<input name="checkin" size="5" id="start-date" class="date-pick" value="<?php echo $_POST['checkin']?>" />
<div class="titulo_secundario" style="float:left; margin-top:3px;"> &nbsp; hasta &nbsp; </div>
<input name="checkout" size="5" id="end-date" class="date-pick" value="<?php echo $_POST['checkout']?>" /> 
<div class="titulo_secundario" style="float:left; margin-top:3px;"> &nbsp; E-mail &nbsp; </div>
<input name="email"  id="email" style="float:left" value="<?php echo $_POST['email']?>" /> 
<div class="titulo_secundario" style="float:left; margin-top:3px;"> 
&nbsp; <input type="submit" name="buscar" value="Buscar" /> <input type="submit" name="todos" value="Todas las encuestas" /> 
</div></p>
<div style="clear:both;"></div>
</form>
<?php
include("preguntas.inc.php");
include_once("config/db.php"); 
//$conn = mysqli_connect($host, $db_user, $db_pass, $db);
//mysql_select_db($db);
?>
<?php

if(isset($_POST['buscar']) and (($_POST['checkin']!="" and $_POST['checkout']!="")or($_POST['email']!=""))){
	$condicionEmail = ($_POST['email']!="")?" AND clientes.email LIKE '%".$_POST['email']."%'":"";
	$condicionFechas = (($_POST['checkin']!="" and $_POST['checkout']!=""))?" AND reservas.check_out>='".fechasql($_POST['checkin'])."' AND reservas.check_out<='".fechasql($_POST['checkout'])."'":"";
	$sql = "SELECT encuesta_respuestas.*,encuesta.id, clientes.email 
	FROM encuesta INNER JOIN encuesta_respuestas ON encuesta.id=encuesta_respuestas.encuesta_id 
	INNER JOIN reservas ON reservas.id=encuesta.reserva_id 
	INNER JOIN clientes ON reservas.cliente_id=clientes.id
	WHERE 1=1 
			".$condicionFechas.$condicionEmail;
	//echo $sql;
}elseif(isset($_POST['todos'])){
	$sql = "SELECT encuesta_respuestas.*,encuesta.id 
	FROM encuesta INNER JOIN encuesta_respuestas ON encuesta.id=encuesta_respuestas.encuesta_id";
	$sql1 = "SELECT reservas.*,clientes.nombre_apellido 
	FROM reservas INNER JOIN clientes ON clientes.id=reservas.cliente_id 
	WHERE (reservas.estado != 2 AND reservas.estado != 3) OR reservas.estado is null";
}
if(isset($_POST)){
	if ($sql) {
	$rsTemp = mysql_query($sql);
	if(mysql_affected_rows()>0){
		while($rs = mysql_fetch_array($rsTemp)){
			$encuestas[$rs['id']]=1;
			if(!isset($respuestas[$rs['pregunta_id']][$rs['valor']])){
				$respuestas[$rs['pregunta_id']][$rs['valor']] = 1;
			}else{
				$respuestas[$rs['pregunta_id']][$rs['valor']]++;
			}
		}
		foreach($respuestas as $preg=>$resp){
			foreach($resp as $valor=>$cant){
				if($preg=="7a" or $preg=="7b" or $preg=="7c" or $preg=="7d" or $preg=="7e" or $preg=="7f" or $preg=="7g" or $preg=="7h" or $preg=="7i" or $preg=="7j"){
					$servicios[$valor]=$servicios[$valor] + 1; 
				}elseif($preg=="7k" or $preg=="7l"){
					$limpieza[$valor]=$limpieza[$valor] + 1;
				}elseif($preg=="7m" or $preg=="7n" or $preg=="7o" or $preg=="7p" or $preg=="7q"){
					$restaurante[$valor]=$restaurante[$valor] + 1;
				}
			}
		}
		?>
		<!--  <br>
		<div style="text-align:center; font-family: Arial, Helvetica, sans-serif;font-size: 25px;font-weight: bold;">Puntaje <span id="puntaje" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div>-->
		<?php 
		
		$sumaPromedios =0;
		$promedios=array();
		foreach($respuestas as $preg=>$resp){
			$etiquetas=array();		
			$valores=array();
			$colores=array();
			
			$total = array_sum($resp);
			//print_r($resp);
			if($preg!=7){
?>
				<div id="titulo_pregunta">
				<?php echo utf8_encode($pregunta[$preg])?>
				</div>
				<div id="respuestas">
<?php
				foreach($resp as $valor=>$cant){ //genero los valores para los graficos
					//echo $valor."=>".$cant."<br>";
					if($preg!=7){
						//echo $preg.'-'.$respuesta[$preg][$valor];
						$etiquetas[]=$respuesta[$preg][$valor];
						/*switch ($respuesta[$preg][$valor]) {
							case "Familia":
							 	$color="071dfc";
							break;
							case "Pareja":
							 	$color="c407fc";
							break;
							case "Solo":
							 	$color="07d7fc";
							break;
							case "SI":
							 	$color="136204";
							break;
							case "NO":
							 	$color="ec0617";
							break;
							case 1:
							 	$color="ec0617";
							break;
							case 2:
							 	$color="ec0617";
							break;
							case 3:
							 	$color="fafa04";
							break;
							case 4:
							 	$color="2dfa04";
							break;
							case 5:
							 	$color="136204";
							break;
						}
						$colores[]=$color;*/
					}else{
						$etiquetas[]=$valor;
					}
					
					$valores[]=round($cant/$total*100,2);
					//echo $promedios[$preg]."/".$valor."<br>";
					//$promedios[$preg] +=$cant*$valor;
				}
				/*print_r($etiquetas);
				print_r($valores);*/
				//print_r($promedios);
				//if(($preg!=1)&&($preg!=10)){
					//$sumaPromedios +=$promedios[$preg]/count($encuestas);
					//echo $promedios[$preg]."/".count($encuestas);
				//}
?>
				<img src="
				http://chart.apis.google.com/chart?
				chs=600x120
				&chf=bg,s,65432100
				&chd=t:<?php echo implode(",",$valores)?>
				&cht=p3
				&chco=00FF00
				&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>" 
				/>
				<br />Esta pregunta obtuvo <?php echo $total?> respuestas en <?php echo count($encuestas)?> encuestas encontradas
				</div>
<?php
				
			}
		} //foreach que recorre las respuesats
		?>
		<!--Servicios-->
		<div id="titulo_pregunta">
		Servicios
		</div>
		<div id="respuestas">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="50%">
<?php	
				$etiquetas=array();		
				$valores=array();
				$total = array_sum($servicios);
				foreach($servicios as $valor=>$cant){
					$etiquetas[]=$valor;
					$valores[]=round($cant/$total*100,2);
				}
?>
					<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=00FF00
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>" 
					/>
					<br /><br />
<?php
				
				$promedio=($servicios['E']*100+$servicios['B']*70+$servicios['P']*40+$servicios['M']*10)/$total;
?>
					<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>
				</td>
				<td>
<?php
				foreach($respuestas as $preg=>$resp){
					$etiquetas=array();		
					$valores=array();
					$total = array_sum($resp);
					if($preg=="7a" or $preg=="7b" or $preg=="7c" or $preg=="7d" or $preg=="7e" or $preg=="7f"  or $preg=="7r" or $preg=="7g" or $preg=="7h" or $preg=="7i" or $preg=="7j"){
						foreach($resp as $valor=>$cant){ //genero los valores para los graficos
							if($preg!=7){
								$etiquetas[]=$respuesta[$preg][$valor];
							}else{
								$etiquetas[]=$valor;
							}
							$valores[]=round($cant/$total*100,2);
						}
?>
						<div id="tabla">
						<strong><?php echo $pregunta[$preg]?></strong><br />
						<img src="
						http://chart.apis.google.com/chart?
						chs=150x70
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=00FF00
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"  
						/>
						<br /><?php echo $total?> / <?php echo count($encuestas)?>
						</div>
<?php
						
					}
				} //foreach que recorre las respuesats
?>
				</td>
			</tr>
		</table>
		</div>
		<!--/Servicios-->
		
		<!--Limpieza-->
		<div id="titulo_pregunta">
		Limpieza y Mantenimiento
		</div>
		<div id="respuestas">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="50%">
<?php
				$etiquetas=array();		
				$valores=array();
				$total = array_sum($limpieza);
				foreach($limpieza as $valor=>$cant){
					$etiquetas[]=$valor;
					$valores[]=round($cant/$total*100,2);
				}
?>
					<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=00FF00
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>" 
					/>
					<br /><br />
<?php
				
				$promedio=($limpieza['E']*100+$limpieza['B']*70+$limpieza['P']*40+$limpieza['M']*10)/$total;
?>
					<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>
				</td>
				<td>
<?php
				foreach($respuestas as $preg=>$resp){
					$etiquetas=array();		
					$valores=array();
					$total = array_sum($resp);
					if($preg=="7k" or $preg=="7l"){
						foreach($resp as $valor=>$cant){ //genero los valores para los graficos
							if($preg!=7){
								$etiquetas[]=$respuesta[$preg][$valor];
							}else{
								$etiquetas[]=$valor;
							}
							$valores[]=round($cant/$total*100,2);
						}
?>
						<div id="tabla">
						<strong><?php echo $pregunta[$preg]?></strong><br />
						<img src="
						http://chart.apis.google.com/chart?
						chs=150x70
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=00FF00
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"  
						/>
						<br /><?php echo $total?> / <?php echo count($encuestas)?>
						</div>
<?php
						
					}
				} //foreach que recorre las respuesats
?>
				</td>
			</tr>
		</table>
		</div>
		<!--/Limpieza-->
		
		<!--Restaurante-->
		<div id="titulo_pregunta">
		Restaurante
		</div>
		<div id="respuestas">
		<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td width="50%">
<?php
				$etiquetas=array();		
				$valores=array();
				$total = array_sum($restaurante);
				foreach($restaurante as $valor=>$cant){
					$etiquetas[]=$valor;
					$valores[]=round($cant/$total*100,2);
				}
?>
					<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=00FF00
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>" 
					/>
					<br /><br />
<?php
				
				$promedio=($restaurante['E']*100+$restaurante['B']*70+$restaurante['P']*40+$restaurante['M']*10)/$total;
?>
					<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>
				</td>
				<td>
<?php
				foreach($respuestas as $preg=>$resp){
					$etiquetas=array();		
					$valores=array();
					$total = array_sum($resp);
					if($preg=="7m" or $preg=="7n" or $preg=="7o" or $preg=="7p" or $preg=="7q"){
						foreach($resp as $valor=>$cant){ //genero los valores para los graficos
							if($preg!=7){
								$etiquetas[]=$respuesta[$preg][$valor];
							}else{
								$etiquetas[]=$valor;
							}
							$valores[]=round($cant/$total*100,2);
						}
?>
						<div id="tabla">
						<strong><?php echo $pregunta[$preg]?></strong><br />
						<img src="
						http://chart.apis.google.com/chart?
						chs=150x70
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=00FF00
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"  
						/>
						<br /><?php echo $total?> / <?php echo count($encuestas)?>
						</div>
<?php
						
					}
				} //foreach que recorre las respuesats
?>
				</td>
			</tr>
		</table>
		</div>
		<!--/Restaurante-->
		<?php 
	echo "<script> $('#puntaje').html(".round($sumaPromedios/8,2).");</script>";	
?>
		
<?php	
	}else{ //sino hay resultados
?>
	<p class="titulo_secundario" align="center">No se encontraron encuestas en las fechas seleccionadas</p>
<?php
	}
}
	}
	
 $ano= (isset($_POST['ano']))?$_POST['ano']:date('Y'); 
	  $mes= (isset($_POST['mes']))?$_POST['mes']:date('m'); 	
?>
</div>
<div id="noGraficos" style="display:<?php echo $displayNoGraficos;?>">
<form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
<input name="noVerGraficos" type="hidden" value="1"/>
<select id="mes" name="mes">
    <option value="01" <?php if($mes == '01'){?> selected="selected" <?php } ?>>Enero</option>
    <option value="02" <?php if($mes == '02'){?> selected="selected" <?php } ?>>Febrero</option>
    <option value="03" <?php if($mes == '03'){?> selected="selected" <?php } ?>>Marzo</option>
    <option value="04" <?php if($mes == '04'){?> selected="selected" <?php } ?>>Abril</option>
    <option value="05" <?php if($mes == '05'){?> selected="selected" <?php } ?>>Mayo</option>
    <option value="06" <?php if($mes == '06'){?> selected="selected" <?php } ?>>Junio</option>
    <option value="07" <?php if($mes == '07'){?> selected="selected" <?php } ?>>Julio</option>
    <option value="08" <?php if($mes == '08'){?> selected="selected" <?php } ?>>Agosto</option>
    <option value="09" <?php if($mes == '09'){?> selected="selected" <?php } ?>>Septiembre</option>
    <option value="10" <?php if($mes == '10'){?> selected="selected" <?php } ?>>Octubre</option>
    <option value="11" <?php if($mes == '11'){?> selected="selected" <?php } ?>>Noviembre</option>
    <option value="12" <?php if($mes == '12'){?> selected="selected" <?php } ?>>Diciembre</option>
</select> 
<select id="ano" name="ano">
    <option <?php if($ano == '2012'){?> selected="selected" <?php } ?>>2012</option>
    <option <?php if($ano == '2013'){?> selected="selected" <?php } ?>>2013</option>
    <option <?php if($ano == '2014'){?> selected="selected" <?php } ?>>2014</option>
    <option <?php if($ano == '2015'){?> selected="selected" <?php } ?>>2015</option>
    <option <?php if($ano == '2016'){?> selected="selected" <?php } ?>>2016</option>
    <option <?php if($ano == '2017'){?> selected="selected" <?php } ?>>2017</option>
    <option <?php if($ano == '2018'){?> selected="selected" <?php } ?>>2018</option>
    <option <?php if($ano == '2019'){?> selected="selected" <?php } ?>>2019</option>
</select>
<input type="submit" name="ver" id="ver" value="Ver" />
<div style="clear:both;"></div>
</form>
<table width="100%" cellspacing="0" border="1" style="table-layout:fixed;"> 
<thead>
	<tr>
		<th width="20">Nro. Reserva</th>
		<th width="80">Titular</th>
		<th width="20">Check Out</th>
		<th width="50">E-mail</th>
		<th width="20">Enviado</th>
		<th width="20">Respuesta</th>
		<th width="20">Enviar</th>
	</tr>
</thead>
<tbody>
<?php 
	if(isset($_POST['ver'])){
	$sql1 = "SELECT reservas.*,clientes.nombre_apellido,clientes.email  
	FROM reservas INNER JOIN clientes ON clientes.id=reservas.cliente_id 
	
	WHERE
			YEAR(reservas.check_out)='".$_POST['ano']."' AND  MONTH(reservas.check_out)='".$_POST['mes']."'
			AND ((reservas.estado != 2 AND reservas.estado != 3) OR reservas.estado is null)
			ORDER BY reservas.check_out DESC";
	
	if ($sql1) {
	$rsTemp1 = mysql_query($sql1);
	
	if(mysql_affected_rows()>0){
		while($rs1 = mysql_fetch_array($rsTemp1)){
			$enviada=0;
			$sql2 = "SELECT respondida,enviada 
			FROM encuesta where reserva_id = ".$rs1['id'];

			$rsTemp2 = mysql_query($sql2);
			if(mysql_affected_rows()>0){
				$imgEnviada = "ok.gif";
				if($rs2 = mysql_fetch_array($rsTemp2)){
					$imgRespuesta = ($rs2['respondida'])?"ok.gif":"bt_delete.png";
					$enviada=$rs2['enviada'];
				}
				//$enviarEncuesta ="";
			}
			else{
				$imgEnviada = "bt_delete.png";
				$imgRespuesta = "bt_delete.png";
				
			}
			$enviarEncuesta = '<a href="#" onclick="enviar('.$rs1['id'].')" class="item"><img src="images/mail.png" align="absmiddle" />('.$enviada.') </a>';
			?>
			
			
			
			<tr>
				<td><?php echo $rs1['numero']?></td>
				<td><?php echo $rs1['nombre_apellido']?></td>
				<td><?php echo fechavista($rs1['check_out'])?></td>
				<td><?php echo $rs1['email']?></td>
				<td style="text-align: center;"><img src="images/<?php echo $imgEnviada?>"></img></td>
				<td style="text-align: center;"><img src="images/<?php echo $imgRespuesta?>"></img></td>
				<td style="text-align: center;"><?php echo $enviarEncuesta?></td>
			</tr>
			<?php } ?>
		<?php } 
	}
	}?>
	
</tbody>
</table>
</div>		
		

</body>
</html>
