<?php
session_start();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

            if(confirm('Â¿Seguro desea enviar la encuesta?')){
                createWindow('w_enviar_encuesta','Enviar encuesta','cron-encuesta.php?id='+id,'300','200'); //botones
                setTimeout('dhxWins.window("w_enviar_encuesta").close()', 2000);

            }

        }
        function modificar(id){


            createWindow('w_modificar_encuesta','Modificar encuesta','modificar_encuesta.php?id='+id,'900','700'); //botones


        }
        function mostrarGraficos(){

            $('#graficos').show();
            $('#noGraficos').hide();
            $('#divHabilitarPreguntas').hide();
            $('#hiddenHabilitarPreguntas').val(0);
        }
        function noMostrarGraficos(){

            $('#graficos').hide();
            $('#noGraficos').show();
            $('#divHabilitarPreguntas').hide();
            $('#hiddenHabilitarPreguntas').val(0);

        }
        function habilitarPreguntas(){

            $('#graficos').hide();
            $('#noGraficos').hide();
            $('#divHabilitarPreguntas').show();
            $('#hiddenHabilitarPreguntas').val(1);

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
            width:200px;
            height:120px;
            padding:5px;
            text-align:center;
            font-size:10px;
            /*margin-top: -129px;*/
        }
    </style>
    <link rel="STYLESHEET" type="text/css" href="styles/toolbar.css">
    <!--/JQuery Date Picker-->
    <script>
        function findApartamentos (){
            $.ajax({
                beforeSend: function(){
                    $('#loading').show();
                },
                data: { 'categoria' : $('#categoria').val() },
                url: 'apartamentos.get.php',
                success: function(data) {
                    $('#loading').hide();
                    $('#spanApartamentos').html(data);
                }
            });

        }


    </script>
</head>

<body>
<ul id="menu">

    <li onclick="mostrarGraficos()" class="item"><img src="images/if_Cake_206472.png" align="absmiddle" />&iquest;Que piensan nuestros clientes?</li>
    <li onclick="noMostrarGraficos()" class="item"><img src="images/ico_users.png" align="absmiddle" />&iquest;Vamos a consultarles?</li>
    <?php
    include("preguntas.inc.php");
    date_default_timezone_set('America/Argentina/Buenos_Aires');

    include_once("config/db.php");
    include_once("functions/util.php");
    auditarUsuarios('Informes de satisfaccion');

    include_once("config/user.php");

    if (ACCION_118) {

        ?>
        <li onclick="habilitarPreguntas()" class="item"><img src="images/pregunta.png" align="absmiddle" />&nbsp;&nbsp;Habilitar preguntas</li>
    <?php }?>

</ul>
<?php
$displayGraficos = (isset($_POST['buscar'])||isset($_POST['todos']))?'block':'none';
$displayNoGraficos = (isset($_POST['ver']))?'block':'none';
$displayHabilitarPreguntas = (isset($_POST['ver'])||isset($_POST['buscar'])||isset($_POST['todos']))?'none':'block';
if (($displayGraficos=='none')&&($displayNoGraficos=='none')) {
    $displayGraficos='block';
    $_POST['todos']=1;
    $displayHabilitarPreguntas='none';
}
?>
<div id="graficos" style="display:<?php echo $displayGraficos;?>">
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
        <input name="verGraficos" type="hidden" value="1"/>
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center"><div class="titulo_secundario" style="float:left; margin-top:3px;">Desde</div></td><td align="center"><div class="titulo_secundario" style="float:left; margin-top:3px;"> hasta </div></td><td align="center"><div class="titulo_secundario" style="float:left; margin-top:3px;"> E-mail </div></td><td align="center"><div class="titulo_secundario" style="float:left; margin-top:3px;">Categor&iacute;as</div></td><td align="center"><div class="titulo_secundario" style="float:left; margin-top:3px;">Apartamentos</div></td><td></td>
            </tr>
            <tr>
                <td><input name="checkin" size="5" id="start-date" class="date-pick" value="<?php echo $_POST['checkin']?>" /></td><td><input name="checkout" size="5" id="end-date" class="date-pick" value="<?php echo $_POST['checkout']?>" /> </td><td><input name="email"  id="email" style="float:left" value="<?php echo $_POST['email']?>" /> </td><td><select style="float:left" id="categoria" name="categoria" onChange="findApartamentos();">
                        <option value="Seleccionar..." selected="selected">Seleccionar...</option>

                        <?php


                        $sql = "SELECT id,categoria FROM categorias ORDER BY categoria ASC";
                        $rsTemp = mysqli_query($conn,$sql);

                        while($rs = mysqli_fetch_array($rsTemp)){

                            ?>

                            <option value="<?php echo $rs['id']?>" <?php if($_POST['categoria'] == $rs['id']){?> selected="selected" <?php } ?>><?php echo $rs['categoria']?> </option>

                        <?php } ?>
                    </select> </td><td><span id="spanApartamentos">
<?php
$sql = "SELECT * FROM apartamentos WHERE categoria_id=".$_POST['categoria']." ORDER BY orden";

$rsTemp = mysqli_query($conn,$sql);

?>
<select name="ApartamentoId[]" multiple="multiple" style="float:left;height:40px; width:200px; margin:2px 0px" id="ApartamentoId">
	<?php
    while($rs = mysqli_fetch_array($rsTemp)){

        ?>

        <option value="<?php echo $rs['id']?>" <?php if(in_array($rs['id'], $_POST['ApartamentoId'])){?> selected="selected" <?php } ?>><?php echo $rs['apartamento']?> </option>

    <?php } ?>

</select>



</span></td>
                <td>
                    <div class="titulo_secundario" style="float:left; margin-top:3px;">
                        <input type="submit" name="buscar" value="Buscar" /> <input type="submit" name="todos" value="Todas las encuestas" />
                    </div></td>
            </tr>
        </table>
        <div style="clear:both;"></div>
    </form>
    <?php

    //$conn = mysqli_connect($host, $db_user, $db_pass, $db);
    //mysql_select_db($db);
    ?>
    <?php

    if(isset($_POST['buscar']) and (($_POST['checkin']!="" and $_POST['checkout']!="")or($_POST['email']!="")or($_POST['categoria']!=""))){
        $condicionEmail = ($_POST['email']!="")?" AND clientes.email LIKE '%".$_POST['email']."%'":"";
        $condicionFechas = (($_POST['checkin']!="" and $_POST['checkout']!=""))?" AND reservas.check_out>='".fechasql($_POST['checkin'])."' AND reservas.check_out<='".fechasql($_POST['checkout'])."'":"";
        if ($_POST['ApartamentoId']) {
            $arrayApartamento = implode(",", $_POST['ApartamentoId']);

            $condicionApartamento = " AND apartamentos.id IN (".$arrayApartamento.")";
        }
        elseif ($_POST['categoria']!='Seleccionar...'){
            $condicionApartamento = " AND apartamentos.categoria_id = '".$_POST['categoria']."'";
        }
        $sql = "SELECT encuesta_respuestas.*,encuesta.id, clientes.email 
	FROM encuesta INNER JOIN encuesta_respuestas ON encuesta.id=encuesta_respuestas.encuesta_id 
	INNER JOIN reservas ON reservas.id=encuesta.reserva_id 
	INNER JOIN apartamentos ON reservas.apartamento_id=apartamentos.id
	INNER JOIN clientes ON reservas.cliente_id=clientes.id
	WHERE 1=1 
			".$condicionFechas.$condicionEmail.$condicionApartamento;
        echo $sql;
    }elseif(isset($_POST['todos'])){
        $sql = "SELECT encuesta_respuestas.*,encuesta.id 
	FROM encuesta INNER JOIN encuesta_respuestas ON encuesta.id=encuesta_respuestas.encuesta_id";
        $sql1 = "SELECT reservas.*,clientes.nombre_apellido 
	FROM reservas INNER JOIN clientes ON clientes.id=reservas.cliente_id 
	WHERE (reservas.estado != 2 AND reservas.estado != 3) OR reservas.estado is null";
    }
    if(isset($_POST)){
        if ($sql) {
            $rsTemp = mysqli_query($conn,$sql);
            if(mysqli_affected_rows($conn)>0){
                while($rs = mysqli_fetch_array($rsTemp)){
                    $encuestas[$rs['id']]=1;
                    if(!isset($respuestas[$rs['pregunta_id']][$rs['valor']])){
                        $respuestas[$rs['pregunta_id']][$rs['valor']] = 1;
                    }else{
                        $respuestas[$rs['pregunta_id']][$rs['valor']]++;
                    }
                }
                foreach($respuestas as $preg=>$resp){
                    foreach($resp as $valor=>$cant){
                        if($preg=="3a" or $preg=="3b" or $preg=="3c"){
                            $ventas[$valor]=$ventas[$valor] + $cant;
                        }elseif($preg=="3d" or $preg=="3e"or $preg=="3f"){
                            $recepcion[$valor]=$recepcion[$valor] + $cant;
                        }elseif($preg=="3g" or $preg=="3h" or $preg=="3i" or $preg=="3j"){
                            $desayuno[$valor]=$desayuno[$valor] + $cant;
                        }elseif($preg=="3u" or $preg=="3v" or $preg=="3w"){
                            $alimentos[$valor]=$alimentos[$valor] + $cant;
                        }elseif($preg=="3k" or $preg=="3l" or $preg=="3m"){
                            $limpieza[$valor]=$limpieza[$valor] + $cant;
                        }elseif($preg=="3n" or $preg=="3o"or $preg=="3t"){
                            $mantenimiento[$valor]=$mantenimiento[$valor] + $cant;
                        }elseif($preg=="3p" or $preg=="3q"){
                            $relax[$valor]=$relax[$valor] + $cant;
                        }elseif($preg=="3r" or $preg=="3s"){
                            $sustentabilidad[$valor]=$sustentabilidad[$valor] + $cant;
                        }
                    }
                }
                ?>
                <br>
                <div style="text-align:center; font-family: Arial, Helvetica, sans-serif;font-size: 25px;font-weight: bold;">Puntaje <span id="puntaje" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div>
                <?php

                $preguntasContestadas =0;
                $preguntasContestadasVentas =0;
                $preguntasContestadasRecepcion =0;
                $preguntasContestadasDesayuno =0;
                $preguntasContestadasLimpieza =0;
                $preguntasContestadasMantenimiento =0;
                $preguntasContestadasRelax =0;
                $preguntasContestadasSustentabilidad =0;
                $promedios=array();
                foreach($respuestas as $preg=>$resp){
                    $etiquetas=array();
                    $valores=array();
                    $colores=array();

                    $total = array_sum($resp);
                    //print_r($resp);
                    if($preg!=3){
                        ?>
                        <div id="titulo_pregunta">
                            <?php echo $pregunta[$preg]?>
                        </div>
                        <div id="respuestas">
                            <?php
                            foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                //echo $valor."=>".$cant."<br>";
                                if($preg!=3){
                                    //echo $preg.'-'.$respuesta[$preg][$valor];
                                    $etiquetas[]=utf8_encode($respuesta[$preg][$valor]);
                                    switch ($valor) {
                                        case "a":
                                            $color="071dfc";
                                            break;

                                        case "b":
                                            $color="c407fc";
                                            break;

                                        case "c":
                                            $color="07d7fc";
                                            break;

                                        case "d":
                                            $color="52c7fa";
                                            break;
                                        case "e":
                                            $color="8a52fa";
                                            break;
                                    }
                                    $colores[]=$color;
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
                            <div style="max-width: 500px; min-height: 400px; margin: 0 auto;">
                                <canvas id="graficoPregunta<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                            </div>

                            <script>
                                var ctx = document.getElementById('graficoPregunta<?php echo $preg;?>').getContext('2d');
                                var grafico<?php echo $preg;?> = new Chart(ctx, {
                                    type: 'pie', // Puedes cambiar el tipo de gráfico aquí, p.ej. 'bar', 'line', etc.
                                    data: {
                                        labels: <?php echo json_encode($etiquetas); ?>,
                                        datasets: [{
                                            label: 'Respuestas',
                                            data: <?php echo json_encode($valores); ?>,
                                            backgroundColor: <?php echo json_encode(array_map(function($color) { return "#".$color; }, $colores)); ?>
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                        plugins: {
                                            legend: {
                                                position: 'top',
                                            },
                                            tooltip: {
                                                callbacks: {
                                                    label: function(tooltipItem) {
                                                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });
                            </script>

                            <!--<img src="
				http://chart.apis.google.com/chart?
				chs=800x120
				&chf=bg,s,65432100
				&chd=t:<?php echo implode(",",$valores)?>
				&cht=p3
				&chco=<?php echo implode(",",$colores)?>
				&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
				/>-->
                            <br />Esta pregunta obtuvo <?php echo $total?> respuestas en <?php echo count($encuestas)?> encuestas encontradas
                        </div>
                        <?php

                    }
                } //foreach que recorre las respuesats
                ?>
                <!--Ventas-->
                <div id="titulo_pregunta">
                    Ventas
                </div>
                <div id="respuestas">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="8%"><div style="text-align:right; font-family: Arial, Helvetica, sans-serif;font-size: 20px;font-weight: bold;">Punt. <span id="puntajeVentas" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div></td>
                            <td width="42%">
                                <?php
                                $etiquetas=array();
                                $valores=array();
                                $colores=array();
                                $total = array_sum($ventas);
                                foreach($ventas as $valor=>$cant){
                                    $etiquetas[]=$valor;
                                    $valores[]=round($cant/$total*100,2);
                                    switch ($valor) {

                                        case 1:
                                            $color="#ec0617";
                                            break;
                                        case 2:
                                            $color="#faab52";
                                            break;
                                        case 3:
                                            $color="#fafa04";
                                            break;
                                        case 4:
                                            $color="#2dfa04";
                                            break;
                                        case 5:
                                            $color="#136204";
                                            break;
                                    }
                                    $colores[]=$color;

                                }
                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="graficoVentas" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('graficoVentas').getContext('2d');
                                    var graficoVentas = new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: <?php echo json_encode($etiquetas); ?>,
                                            datasets: [{
                                                data: <?php echo json_encode($valores); ?>,
                                                backgroundColor: <?php echo json_encode($colores); ?>
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>

                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=<?php echo implode(",",$colores)?>
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
					/>
					<br /><br />-->
                                <?php

                                $promedio=($ventas['5']*100+$ventas['4']*75+$ventas['3']*50+$ventas['2']*25+$ventas['1']*10)/$total;

                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="gaugePromedioBar" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('gaugePromedioBar').getContext('2d');
                                    var gaugePromedioBar = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Promedio'],
                                            datasets: [{
                                                label: 'Nivel de Promedio',
                                                data: [<?php echo $promedio; ?>],
                                                backgroundColor: '#ec0617'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            indexAxis: 'y', // Coloca el gráfico en formato horizontal
                                            scales: {
                                                x: {
                                                    min: 0,
                                                    max: 100,
                                                    ticks: {
                                                        callback: function(value) {
                                                            return value + '%'; // Muestra el porcentaje
                                                        }
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>



                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&cht=gom
					&chd=t:<?php echo $promedio?>

					&chl=Promedio"
					/>-->
                            </td>
                            <td>
                                <?php
                                foreach($respuestas as $preg=>$resp){
                                    $etiquetas=array();
                                    $valores=array();
                                    $colores=array();
                                    $total = array_sum($resp);
                                    if($preg=="3a" or $preg=="3b" or $preg=="3c"){
                                        $preguntasContestadas++;
                                        $preguntasContestadasVentas++;
                                        foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                            if($preg!=3){
                                                $etiquetas[]=$respuesta[$preg][$valor];
                                            }else{
                                                $etiquetas[]=$valor;
                                            }
                                            switch ($valor) {

                                                case 1:
                                                    $color="ec0617";
                                                    break;
                                                case 2:
                                                    $color="faab52";
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
                                            $colores[]=$color;


                                            $valores[]=round($cant/$total*100,2);
                                            $promedios[$preg] +=$cant*$valor;
                                        }
                                        $sumaPromedios +=$promedios[$preg]/$total;
                                        $sumaPromediosVentas +=$promedios[$preg]/$total;


                                        ?>


                                        <div id="tabla">
                                            <strong><?php echo $pregunta[$preg]?></strong>
                                            <div style="max-width: 300px; margin: 0 auto;">
                                                <canvas id="graficoPregunta1<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                                            </div>


                                            <script>
                                                var ctx = document.getElementById('graficoPregunta1<?php echo $preg;?>').getContext('2d');
                                                var graficoPregunta = new Chart(ctx, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: <?php echo json_encode($etiquetas); ?>,
                                                        datasets: [{
                                                            label: 'Distribución de Respuestas',
                                                            data: <?php echo json_encode($valores); ?>,
                                                            backgroundColor: <?php echo json_encode(array_map(function($color) { return "#" . $color; }, $colores)); ?>
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                        plugins: {
                                                            legend: {
                                                                position: 'top',
                                                            },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: function(tooltipItem) {
                                                                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>
                                            <!--<img src="
						http://chart.apis.google.com/chart?
						chs=185x70
						&chf=bg,s,65432100
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=<?php echo implode(",",$colores)?>
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
						/>-->
                                            <?php echo $total?> / <?php echo count($encuestas)?>
                                        </div>
                                        <?php

                                    }
                                } //foreach que recorre las respuesats

                                echo "<script> $('#puntajeVentas').html(".round($sumaPromediosVentas/$preguntasContestadasVentas,2).");</script>";
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--/Ventas-->

                <!--Recepcion-->
                <div id="titulo_pregunta">
                    Recepci&oacute;n
                </div>
                <div id="respuestas">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="8%"><div style="text-align:right; font-family: Arial, Helvetica, sans-serif;font-size: 20px;font-weight: bold;">Punt. <span id="puntajeRecepcion" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div></td>
                            <td width="42%">
                                <?php
                                $etiquetas=array();
                                $valores=array();
                                $colores=array();
                                $total = array_sum($recepcion);
                                foreach($recepcion as $valor=>$cant){
                                    $etiquetas[]=$valor;
                                    $valores[]=round($cant/$total*100,2);
                                    switch ($valor) {

                                        case 1:
                                            $color="#ec0617";
                                            break;
                                        case 2:
                                            $color="#faab52";
                                            break;
                                        case 3:
                                            $color="#fafa04";
                                            break;
                                        case 4:
                                            $color="#2dfa04";
                                            break;
                                        case 5:
                                            $color="#136204";
                                            break;
                                    }
                                    $colores[]=$color;
                                }
                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="graficoRecepcion" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('graficoRecepcion').getContext('2d');
                                    var graficoVentas = new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: <?php echo json_encode($etiquetas); ?>,
                                            datasets: [{
                                                data: <?php echo json_encode($valores); ?>,
                                                backgroundColor: <?php echo json_encode($colores); ?>
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=<?php echo implode(",",$colores)?>
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
					/>
					<br /><br />-->
                                <?php

                                $promedio=($recepcion['5']*100+$recepcion['4']*75+$recepcion['3']*50+$recepcion['2']*25+$recepcion['1']*10)/$total;

                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="gaugePromedioBarRecepcion" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('gaugePromedioBarRecepcion').getContext('2d');
                                    var gaugePromedioBar = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Promedio'],
                                            datasets: [{
                                                label: 'Nivel de Promedio',
                                                data: [<?php echo $promedio; ?>],
                                                backgroundColor: '#ec0617'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            indexAxis: 'y', // Coloca el gráfico en formato horizontal
                                            scales: {
                                                x: {
                                                    min: 0,
                                                    max: 100,
                                                    ticks: {
                                                        callback: function(value) {
                                                            return value + '%'; // Muestra el porcentaje
                                                        }
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&cht=gom
					&chd=t:<?php echo $promedio?>

					&chl=Promedio"
					/>-->
                            </td>
                            <td>
                                <?php
                                foreach($respuestas as $preg=>$resp){
                                    $etiquetas=array();
                                    $valores=array();
                                    $colores=array();
                                    $total = array_sum($resp);
                                    if($preg=="3d" or $preg=="3e"or $preg=="3f"){
                                        $preguntasContestadas++;
                                        $preguntasContestadasRecepcion++;
                                        foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                            if($preg!=3){
                                                $etiquetas[]=$respuesta[$preg][$valor];
                                            }else{
                                                $etiquetas[]=$valor;
                                            }
                                            $valores[]=round($cant/$total*100,2);
                                            $promedios[$preg] +=$cant*$valor;
                                            switch ($valor) {

                                                case 1:
                                                    $color="ec0617";
                                                    break;
                                                case 2:
                                                    $color="faab52";
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
                                            $colores[]=$color;
                                        }
                                        $sumaPromedios +=$promedios[$preg]/$total;
                                        $sumaPromediosRecepcion +=$promedios[$preg]/$total;
                                        ?>
                                        <div id="tabla">
                                            <strong><?php echo $pregunta[$preg]?></strong><br />
                                            <div style="max-width: 300px; margin: 0 auto;">
                                                <canvas id="graficoPreguntaRecepcion<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                                            </div>


                                            <script>
                                                var ctx = document.getElementById('graficoPreguntaRecepcion<?php echo $preg;?>').getContext('2d');
                                                var graficoPregunta = new Chart(ctx, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: <?php echo json_encode($etiquetas); ?>,
                                                        datasets: [{
                                                            label: 'Distribución de Respuestas',
                                                            data: <?php echo json_encode($valores); ?>,
                                                            backgroundColor: <?php echo json_encode(array_map(function($color) { return "#" . $color; }, $colores)); ?>
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                        plugins: {
                                                            legend: {
                                                                position: 'top',
                                                            },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: function(tooltipItem) {
                                                                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>
                                            <!--<img src="
						http://chart.apis.google.com/chart?
						chs=185x70
						&chf=bg,s,65432100
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=<?php echo implode(",",$colores)?>
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
						/>
						<br />--><?php echo $total?> / <?php echo count($encuestas)?>
                                        </div>
                                        <?php

                                    }
                                } //foreach que recorre las respuesats
                                echo "<script> $('#puntajeRecepcion').html(".round($sumaPromediosRecepcion/$preguntasContestadasRecepcion,2).");</script>";
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--/Recepcion-->

                <!--Desayuno-->
                <div id="titulo_pregunta">
                    Desayuno
                </div>
                <div id="respuestas">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="8%"><div style="text-align:right; font-family: Arial, Helvetica, sans-serif;font-size: 20px;font-weight: bold;">Punt. <span id="puntajeDesayuno" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div></td>
                            <td width="42%">
                                <?php
                                $etiquetas=array();
                                $valores=array();
                                $colores=array();
                                $total = array_sum($desayuno);
                                foreach($desayuno as $valor=>$cant){
                                    $etiquetas[]=$valor;
                                    $valores[]=round($cant/$total*100,2);
                                    switch ($valor) {

                                        case 1:
                                            $color="#ec0617";
                                            break;
                                        case 2:
                                            $color="#faab52";
                                            break;
                                        case 3:
                                            $color="#fafa04";
                                            break;
                                        case 4:
                                            $color="#2dfa04";
                                            break;
                                        case 5:
                                            $color="#136204";
                                            break;
                                    }
                                    $colores[]=$color;
                                }
                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="graficoDesayuno" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('graficoDesayuno').getContext('2d');
                                    var graficoVentas = new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: <?php echo json_encode($etiquetas); ?>,
                                            datasets: [{
                                                data: <?php echo json_encode($valores); ?>,
                                                backgroundColor: <?php echo json_encode($colores); ?>
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=<?php echo implode(",",$colores)?>
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
					/>
					<br /><br />-->
                                <?php

                                $promedio=($desayuno['5']*100+$desayuno['4']*75+$desayuno['3']*50+$desayuno['2']*25+$desayuno['1']*10)/$total;

                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="gaugePromedioBarDesayuno" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('gaugePromedioBarDesayuno').getContext('2d');
                                    var gaugePromedioBar = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Promedio'],
                                            datasets: [{
                                                label: 'Nivel de Promedio',
                                                data: [<?php echo $promedio; ?>],
                                                backgroundColor: '#ec0617'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            indexAxis: 'y', // Coloca el gráfico en formato horizontal
                                            scales: {
                                                x: {
                                                    min: 0,
                                                    max: 100,
                                                    ticks: {
                                                        callback: function(value) {
                                                            return value + '%'; // Muestra el porcentaje
                                                        }
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>-->
                            </td>
                            <td>
                                <?php
                                foreach($respuestas as $preg=>$resp){
                                    $etiquetas=array();
                                    $valores=array();
                                    $colores=array();
                                    $total = array_sum($resp);
                                    if($preg=="3g" or $preg=="3h" or $preg=="3i" or $preg=="3j"){
                                        $preguntasContestadas++;
                                        $preguntasContestadasDesayuno++;
                                        foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                            if($preg!=3){
                                                $etiquetas[]=$respuesta[$preg][$valor];
                                            }else{
                                                $etiquetas[]=$valor;
                                            }
                                            $valores[]=round($cant/$total*100,2);
                                            $promedios[$preg] +=$cant*$valor;
                                            switch ($valor) {

                                                case 1:
                                                    $color="ec0617";
                                                    break;
                                                case 2:
                                                    $color="faab52";
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
                                            $colores[]=$color;
                                        }
                                        $sumaPromedios +=$promedios[$preg]/$total;
                                        $sumaPromediosDesayuno +=$promedios[$preg]/$total;
                                        ?>
                                        <div id="tabla">
                                            <strong><?php echo $pregunta[$preg]?></strong>
                                            <div style="max-width: 300px; margin: 0 auto;">
                                                <canvas id="graficoPreguntaDesayuno<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                                            </div>


                                            <script>
                                                var ctx = document.getElementById('graficoPreguntaDesayuno<?php echo $preg;?>').getContext('2d');
                                                var graficoPregunta = new Chart(ctx, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: <?php echo json_encode($etiquetas); ?>,
                                                        datasets: [{
                                                            label: 'Distribución de Respuestas',
                                                            data: <?php echo json_encode($valores); ?>,
                                                            backgroundColor: <?php echo json_encode(array_map(function($color) { return "#" . $color; }, $colores)); ?>
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                        plugins: {
                                                            legend: {
                                                                position: 'top',
                                                            },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: function(tooltipItem) {
                                                                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>
                                            <!--<img src="
						http://chart.apis.google.com/chart?
						chs=185x70
						&chf=bg,s,65432100
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=<?php echo implode(",",$colores)?>
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
						/>
						<br />--><?php echo $total?> / <?php echo count($encuestas)?>
                                        </div>
                                        <?php

                                    }
                                } //foreach que recorre las respuesats
                                echo "<script> $('#puntajeDesayuno').html(".round($sumaPromediosDesayuno/$preguntasContestadasDesayuno,2).");</script>";

                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--/Desayuno-->
                <?php
                if ($alimentos) {

                    ?>
                    <!--Alimentos-->
                    <div id="titulo_pregunta">
                        Servicio de Alimentos y bebidas
                    </div>
                    <div id="respuestas">
                        <table cellpadding="0" cellspacing="0" width="100%">
                            <tr>
                                <td width="8%"><div style="text-align:right; font-family: Arial, Helvetica, sans-serif;font-size: 20px;font-weight: bold;">Punt. <span id="puntajeAlimentos" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div></td>
                                <td width="42%">
                                    <?php
                                    $etiquetas=array();
                                    $valores=array();
                                    $colores=array();
                                    $total = array_sum($alimentos);
                                    foreach($alimentos as $valor=>$cant){
                                        $etiquetas[]=$valor;
                                        $valores[]=round($cant/$total*100,2);
                                        switch ($valor) {

                                            case 1:
                                                $color="#ec0617";
                                                break;
                                            case 2:
                                                $color="#faab52";
                                                break;
                                            case 3:
                                                $color="#fafa04";
                                                break;
                                            case 4:
                                                $color="#2dfa04";
                                                break;
                                            case 5:
                                                $color="#136204";
                                                break;
                                        }
                                        $colores[]=$color;
                                    }
                                    ?>
                                    <div style="max-width: 300px; margin: 0 auto;">
                                        <canvas id="graficoAlimentos" style="width: 100%; height: auto;"></canvas>
                                    </div>


                                    <script>
                                        var ctx = document.getElementById('graficoAlimentos').getContext('2d');
                                        var graficoVentas = new Chart(ctx, {
                                            type: 'pie',
                                            data: {
                                                labels: <?php echo json_encode($etiquetas); ?>,
                                                datasets: [{
                                                    data: <?php echo json_encode($valores); ?>,
                                                    backgroundColor: <?php echo json_encode($colores); ?>
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                plugins: {
                                                    legend: {
                                                        position: 'top',
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(tooltipItem) {
                                                                return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    </script>
                                    <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=<?php echo implode(",",$colores)?>
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
					/>
					<br /><br />-->
                                    <?php

                                    $promedio=($alimentos['5']*100+$alimentos['4']*75+$alimentos['3']*50+$alimentos['2']*25+$alimentos['1']*10)/$total;

                                    ?>
                                    <div style="max-width: 300px; margin: 0 auto;">
                                        <canvas id="gaugePromedioBarAlimentos" style="width: 100%; height: auto;"></canvas>
                                    </div>


                                    <script>
                                        var ctx = document.getElementById('gaugePromedioBarAlimentos').getContext('2d');
                                        var gaugePromedioBar = new Chart(ctx, {
                                            type: 'bar',
                                            data: {
                                                labels: ['Promedio'],
                                                datasets: [{
                                                    label: 'Nivel de Promedio',
                                                    data: [<?php echo $promedio; ?>],
                                                    backgroundColor: '#ec0617'
                                                }]
                                            },
                                            options: {
                                                responsive: true,
                                                maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                indexAxis: 'y', // Coloca el gráfico en formato horizontal
                                                scales: {
                                                    x: {
                                                        min: 0,
                                                        max: 100,
                                                        ticks: {
                                                            callback: function(value) {
                                                                return value + '%'; // Muestra el porcentaje
                                                            }
                                                        }
                                                    }
                                                },
                                                plugins: {
                                                    legend: {
                                                        display: false
                                                    },
                                                    tooltip: {
                                                        callbacks: {
                                                            label: function(tooltipItem) {
                                                                return tooltipItem.dataset.label + ': ' + tooltipItem.raw + '%';
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                    </script>


                                    <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>-->
                                </td>
                                <td>
                                    <?php
                                    foreach($respuestas as $preg=>$resp){
                                        $etiquetas=array();
                                        $valores=array();
                                        $colores=array();
                                        $total = array_sum($resp);
                                        if($preg=="3u" or $preg=="3v" or $preg=="3w"){
                                            $preguntasContestadas++;
                                            $preguntasContestadasAlimentos++;
                                            foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                                if($preg!=3){
                                                    $etiquetas[]=$respuesta[$preg][$valor];
                                                }else{
                                                    $etiquetas[]=$valor;
                                                }
                                                $valores[]=round($cant/$total*100,2);
                                                $promedios[$preg] +=$cant*$valor;
                                                switch ($valor) {

                                                    case 1:
                                                        $color="ec0617";
                                                        break;
                                                    case 2:
                                                        $color="faab52";
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
                                                $colores[]=$color;
                                            }
                                            $sumaPromedios +=$promedios[$preg]/$total;
                                            $sumaPromediosAlimentos +=$promedios[$preg]/$total;
                                            ?>
                                            <div id="tabla">
                                                <strong><?php echo $pregunta[$preg]?></strong>
                                                <div style="max-width: 300px; margin: 0 auto;">
                                                    <canvas id="graficoPreguntaAlimentos<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                                                </div>


                                                <script>
                                                    var ctx = document.getElementById('graficoPreguntaAlimentos<?php echo $preg;?>').getContext('2d');
                                                    var graficoPregunta = new Chart(ctx, {
                                                        type: 'pie',
                                                        data: {
                                                            labels: <?php echo json_encode($etiquetas); ?>,
                                                            datasets: [{
                                                                label: 'Distribución de Respuestas',
                                                                data: <?php echo json_encode($valores); ?>,
                                                                backgroundColor: <?php echo json_encode(array_map(function($color) { return "#" . $color; }, $colores)); ?>
                                                            }]
                                                        },
                                                        options: {
                                                            responsive: true,
                                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                            plugins: {
                                                                legend: {
                                                                    position: 'top',
                                                                },
                                                                tooltip: {
                                                                    callbacks: {
                                                                        label: function(tooltipItem) {
                                                                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    });
                                                </script>
                                                <!--<br />
						<img src="
						http://chart.apis.google.com/chart?
						chs=185x70
						&chf=bg,s,65432100
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=<?php echo implode(",",$colores)?>
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
						/>
						<br />--><?php echo $total?> / <?php echo count($encuestas)?>
                                            </div>
                                            <?php

                                        }
                                    } //foreach que recorre las respuesats
                                    echo "<script> $('#puntajeAlimentos').html(".round($sumaPromediosAlimentos/$preguntasContestadasAlimentos,2).");</script>";

                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <!--/Alimentos-->
                    <?php

                }
                ?>
                <!--Limpieza-->
                <div id="titulo_pregunta">
                    Servicio de Mucamas y Limpieza
                </div>
                <div id="respuestas">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="8%"><div style="text-align:right; font-family: Arial, Helvetica, sans-serif;font-size: 20px;font-weight: bold;">Punt. <span id="puntajeLimpieza" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div></td>
                            <td width="42%">
                                <?php
                                $etiquetas=array();
                                $valores=array();
                                $colores=array();
                                $total = array_sum($limpieza);
                                foreach($limpieza as $valor=>$cant){
                                    $etiquetas[]=$valor;
                                    $valores[]=round($cant/$total*100,2);
                                    switch ($valor) {

                                        case 1:
                                            $color="#ec0617";
                                            break;
                                        case 2:
                                            $color="#faab52";
                                            break;
                                        case 3:
                                            $color="#fafa04";
                                            break;
                                        case 4:
                                            $color="#2dfa04";
                                            break;
                                        case 5:
                                            $color="#136204";
                                            break;
                                    }
                                    $colores[]=$color;
                                }
                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="graficoMucamas" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('graficoMucamas').getContext('2d');
                                    var graficoVentas = new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: <?php echo json_encode($etiquetas); ?>,
                                            datasets: [{
                                                data: <?php echo json_encode($valores); ?>,
                                                backgroundColor: <?php echo json_encode($colores); ?>
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=<?php echo implode(",",$colores)?>
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
					/>
					<br /><br />-->
                                <?php

                                $promedio=($limpieza['5']*100+$limpieza['4']*75+$limpieza['3']*50+$limpieza['2']*25+$limpieza['1']*10)/$total;

                                ?>

                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="gaugePromedioBarMucamas" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('gaugePromedioBarMucamas').getContext('2d');
                                    var gaugePromedioBar = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Promedio'],
                                            datasets: [{
                                                label: 'Nivel de Promedio',
                                                data: [<?php echo $promedio; ?>],
                                                backgroundColor: '#ec0617'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            indexAxis: 'y', // Coloca el gráfico en formato horizontal
                                            scales: {
                                                x: {
                                                    min: 0,
                                                    max: 100,
                                                    ticks: {
                                                        callback: function(value) {
                                                            return value + '%'; // Muestra el porcentaje
                                                        }
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>-->
                            </td>
                            <td>
                                <?php
                                foreach($respuestas as $preg=>$resp){
                                    $etiquetas=array();
                                    $valores=array();
                                    $colores=array();
                                    $total = array_sum($resp);
                                    if($preg=="3k" or $preg=="3l" or $preg=="3m"){
                                        $preguntasContestadas++;
                                        $preguntasContestadasLimpieza++;
                                        foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                            if($preg!=3){
                                                $etiquetas[]=$respuesta[$preg][$valor];
                                            }else{
                                                $etiquetas[]=$valor;
                                            }
                                            $valores[]=round($cant/$total*100,2);
                                            $promedios[$preg] +=$cant*$valor;
                                            switch ($valor) {

                                                case 1:
                                                    $color="ec0617";
                                                    break;
                                                case 2:
                                                    $color="faab52";
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
                                            $colores[]=$color;
                                        }
                                        $sumaPromedios +=$promedios[$preg]/$total;
                                        $sumaPromediosLimpieza +=$promedios[$preg]/$total;
                                        ?>
                                        <div id="tabla">
                                            <strong><?php echo $pregunta[$preg]?></strong>
                                            <div style="max-width: 300px; margin: 0 auto;">
                                                <canvas id="graficoPreguntaMucamas<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                                            </div>


                                            <script>
                                                var ctx = document.getElementById('graficoPreguntaMucamas<?php echo $preg;?>').getContext('2d');
                                                var graficoPregunta = new Chart(ctx, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: <?php echo json_encode($etiquetas); ?>,
                                                        datasets: [{
                                                            label: 'Distribución de Respuestas',
                                                            data: <?php echo json_encode($valores); ?>,
                                                            backgroundColor: <?php echo json_encode(array_map(function($color) { return "#" . $color; }, $colores)); ?>
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                        plugins: {
                                                            legend: {
                                                                position: 'top',
                                                            },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: function(tooltipItem) {
                                                                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>
                                            <!--<br />
						<img src="
						http://chart.apis.google.com/chart?
						chs=185x70
						&chf=bg,s,65432100
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=<?php echo implode(",",$colores)?>
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
						/>
						<br />--><?php echo $total?> / <?php echo count($encuestas)?>
                                        </div>
                                        <?php

                                    }
                                } //foreach que recorre las respuesats
                                echo "<script> $('#puntajeLimpieza').html(".round($sumaPromediosLimpieza/$preguntasContestadasLimpieza,2).");</script>";
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--/Limpieza-->
                <!--Mantenimiento-->
                <div id="titulo_pregunta">
                    Mantenimiento
                </div>
                <div id="respuestas">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="8%"><div style="text-align:right; font-family: Arial, Helvetica, sans-serif;font-size: 20px;font-weight: bold;">Punt. <span id="puntajeMantenimiento" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div></td>
                            <td width="42%">
                                <?php
                                $etiquetas=array();
                                $valores=array();
                                $colores=array();
                                $total = array_sum($mantenimiento);
                                foreach($mantenimiento as $valor=>$cant){
                                    $etiquetas[]=$valor;
                                    $valores[]=round($cant/$total*100,2);
                                    switch ($valor) {

                                        case 1:
                                            $color="#ec0617";
                                            break;
                                        case 2:
                                            $color="#faab52";
                                            break;
                                        case 3:
                                            $color="#fafa04";
                                            break;
                                        case 4:
                                            $color="#2dfa04";
                                            break;
                                        case 5:
                                            $color="#136204";
                                            break;
                                    }
                                    $colores[]=$color;
                                }
                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="graficoMantenimiento" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('graficoMantenimiento').getContext('2d');
                                    var graficoVentas = new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: <?php echo json_encode($etiquetas); ?>,
                                            datasets: [{
                                                data: <?php echo json_encode($valores); ?>,
                                                backgroundColor: <?php echo json_encode($colores); ?>
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>

                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=<?php echo implode(",",$colores)?>
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
					/>
					<br /><br />-->
                                <?php

                                $promedio=($mantenimiento['5']*100+$mantenimiento['4']*75+$mantenimiento['3']*50+$mantenimiento['2']*25+$mantenimiento['1']*10)/$total;

                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="gaugePromedioBarMantenimiento" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('gaugePromedioBarMantenimiento').getContext('2d');
                                    var gaugePromedioBar = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Promedio'],
                                            datasets: [{
                                                label: 'Nivel de Promedio',
                                                data: [<?php echo $promedio; ?>],
                                                backgroundColor: '#ec0617'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            indexAxis: 'y', // Coloca el gráfico en formato horizontal
                                            scales: {
                                                x: {
                                                    min: 0,
                                                    max: 100,
                                                    ticks: {
                                                        callback: function(value) {
                                                            return value + '%'; // Muestra el porcentaje
                                                        }
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>

                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>-->
                            </td>
                            <td>
                                <?php
                                foreach($respuestas as $preg=>$resp){
                                    $etiquetas=array();
                                    $valores=array();
                                    $colores=array();
                                    $total = array_sum($resp);
                                    if($preg=="3n" or $preg=="3o"or $preg=="3t"){
                                        $preguntasContestadas++;
                                        $preguntasContestadasMantenimiento++;
                                        foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                            if($preg!=3){
                                                $etiquetas[]=$respuesta[$preg][$valor];
                                            }else{
                                                $etiquetas[]=$valor;
                                            }
                                            $valores[]=round($cant/$total*100,2);
                                            $promedios[$preg] +=$cant*$valor;
                                            switch ($valor) {

                                                case 1:
                                                    $color="ec0617";
                                                    break;
                                                case 2:
                                                    $color="faab52";
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
                                            $colores[]=$color;
                                        }
                                        $sumaPromedios +=$promedios[$preg]/$total;
                                        $sumaPromediosMantenimiento +=$promedios[$preg]/$total;
                                        ?>
                                        <div id="tabla">
                                            <strong><?php echo $pregunta[$preg]?></strong>
                                            <div style="max-width: 300px; margin: 0 auto;">
                                                <canvas id="graficoPreguntaMantenimiento<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                                            </div>


                                            <script>
                                                var ctx = document.getElementById('graficoPreguntaMantenimiento<?php echo $preg;?>').getContext('2d');
                                                var graficoPregunta = new Chart(ctx, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: <?php echo json_encode($etiquetas); ?>,
                                                        datasets: [{
                                                            label: 'Distribución de Respuestas',
                                                            data: <?php echo json_encode($valores); ?>,
                                                            backgroundColor: <?php echo json_encode(array_map(function($color) { return "#" . $color; }, $colores)); ?>
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                        plugins: {
                                                            legend: {
                                                                position: 'top',
                                                            },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: function(tooltipItem) {
                                                                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>
                                            <!--<br />
						<img src="
						http://chart.apis.google.com/chart?
						chs=185x70
						&chf=bg,s,65432100
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=<?php echo implode(",",$colores)?>
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
						/>
						<br />--><?php echo $total?> / <?php echo count($encuestas)?>
                                        </div>
                                        <?php

                                    }
                                } //foreach que recorre las respuesats
                                echo "<script> $('#puntajeMantenimiento').html(".round($sumaPromediosMantenimiento/$preguntasContestadasMantenimiento,2).");</script>";
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--/Mantenimiento-->
                <!--Relax-->
                <div id="titulo_pregunta">
                    Sala de Relax
                </div>
                <div id="respuestas">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="8%"><div style="text-align:right; font-family: Arial, Helvetica, sans-serif;font-size: 20px;font-weight: bold;">Punt. <span id="puntajeRelax" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div></td>
                            <td width="42%">
                                <?php
                                $etiquetas=array();
                                $valores=array();
                                $colores=array();
                                $total = array_sum($relax);
                                //echo $total;
                                foreach($relax as $valor=>$cant){
                                    $etiquetas[]=$valor;
                                    $valores[]=round($cant/$total*100,2);
                                    switch ($valor) {

                                        case 1:
                                            $color="#ec0617";
                                            break;
                                        case 2:
                                            $color="#faab52";
                                            break;
                                        case 3:
                                            $color="#fafa04";
                                            break;
                                        case 4:
                                            $color="#2dfa04";
                                            break;
                                        case 5:
                                            $color="#136204";
                                            break;
                                    }
                                    $colores[]=$color;
                                }
                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="graficoRelax" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('graficoRelax').getContext('2d');
                                    var graficoVentas = new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: <?php echo json_encode($etiquetas); ?>,
                                            datasets: [{
                                                data: <?php echo json_encode($valores); ?>,
                                                backgroundColor: <?php echo json_encode($colores); ?>
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=<?php echo implode(",",$colores)?>
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
					/>
					<br /><br />-->
                                <?php

                                $promedio=($relax['5']*100+$relax['4']*75+$relax['3']*50+$relax['2']*25+$relax['1']*10)/$total;

                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="gaugePromedioBarRelax" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('gaugePromedioBarRelax').getContext('2d');
                                    var gaugePromedioBar = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Promedio'],
                                            datasets: [{
                                                label: 'Nivel de Promedio',
                                                data: [<?php echo $promedio; ?>],
                                                backgroundColor: '#ec0617'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            indexAxis: 'y', // Coloca el gráfico en formato horizontal
                                            scales: {
                                                x: {
                                                    min: 0,
                                                    max: 100,
                                                    ticks: {
                                                        callback: function(value) {
                                                            return value + '%'; // Muestra el porcentaje
                                                        }
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>-->
                            </td>
                            <td>
                                <?php
                                foreach($respuestas as $preg=>$resp){
                                    $etiquetas=array();
                                    $valores=array();
                                    $colores=array();
                                    $total = array_sum($resp);
                                    if($preg=="3p" or $preg=="3q"){
                                        $preguntasContestadas++;
                                        $preguntasContestadasRelax++;
                                        foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                            if($preg!=3){
                                                $etiquetas[]=$respuesta[$preg][$valor];
                                            }else{
                                                $etiquetas[]=$valor;
                                            }
                                            $valores[]=round($cant/$total*100,2);
                                            $promedios[$preg] +=$cant*$valor;
                                            switch ($valor) {

                                                case 1:
                                                    $color="ec0617";
                                                    break;
                                                case 2:
                                                    $color="faab52";
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
                                            $colores[]=$color;
                                        }
                                        $sumaPromedios +=$promedios[$preg]/$total;
                                        $sumaPromediosRelax +=$promedios[$preg]/$total;

                                        ?>
                                        <div id="tabla">
                                            <strong><?php echo $pregunta[$preg]?></strong>
                                            <div style="max-width: 300px; margin: 0 auto;">
                                                <canvas id="graficoPreguntaRelax<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                                            </div>


                                            <script>
                                                var ctx = document.getElementById('graficoPreguntaRelax<?php echo $preg;?>').getContext('2d');
                                                var graficoPregunta = new Chart(ctx, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: <?php echo json_encode($etiquetas); ?>,
                                                        datasets: [{
                                                            label: 'Distribución de Respuestas',
                                                            data: <?php echo json_encode($valores); ?>,
                                                            backgroundColor: <?php echo json_encode(array_map(function($color) { return "#" . $color; }, $colores)); ?>
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                        plugins: {
                                                            legend: {
                                                                position: 'top',
                                                            },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: function(tooltipItem) {
                                                                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>
                                            <!--<br />
						<img src="
						http://chart.apis.google.com/chart?
						chs=185x70
						&chf=bg,s,65432100
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=<?php echo implode(",",$colores)?>
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
						/>
						<br />--><?php echo $total?> / <?php echo count($encuestas)?>
                                        </div>
                                        <?php

                                    }
                                } //foreach que recorre las respuesats
                                echo "<script> $('#puntajeRelax').html(".round($sumaPromediosRelax/$preguntasContestadasRelax,2).");</script>";
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--/Relax-->
                <!--Sustentabilidad-->
                <div id="titulo_pregunta">
                    Gesti&oacute;n de sustentabilidad
                </div>
                <div id="respuestas">
                    <table cellpadding="0" cellspacing="0" width="100%">
                        <tr>
                            <td width="8%"><div style="text-align:right; font-family: Arial, Helvetica, sans-serif;font-size: 20px;font-weight: bold;">Punt. <span id="puntajeSustentabilidad" style="border: 1px solid #1E679A; width: 100px; height:100px;"></span></div></td>
                            <td width="42%">
                                <?php
                                $etiquetas=array();
                                $valores=array();
                                $colores=array();
                                $total = array_sum($sustentabilidad);
                                foreach($sustentabilidad as $valor=>$cant){
                                    $etiquetas[]=$valor;
                                    $valores[]=round($cant/$total*100,2);
                                    switch ($valor) {

                                        case 1:
                                            $color="#ec0617";
                                            break;
                                        case 2:
                                            $color="#faab52";
                                            break;
                                        case 3:
                                            $color="#fafa04";
                                            break;
                                        case 4:
                                            $color="#2dfa04";
                                            break;
                                        case 5:
                                            $color="#136204";
                                            break;
                                    }
                                    $colores[]=$color;
                                }
                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="graficoSustentabilidad" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('graficoSustentabilidad').getContext('2d');
                                    var graficoVentas = new Chart(ctx, {
                                        type: 'pie',
                                        data: {
                                            labels: <?php echo json_encode($etiquetas); ?>,
                                            datasets: [{
                                                data: <?php echo json_encode($valores); ?>,
                                                backgroundColor: <?php echo json_encode($colores); ?>
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            plugins: {
                                                legend: {
                                                    position: 'top',
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>
                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&chd=t:<?php echo implode(",",$valores)?>
					&cht=p3
					&chco=<?php echo implode(",",$colores)?>
					&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
					/>
					<br /><br />-->
                                <?php

                                $promedio=($sustentabilidad['5']*100+$sustentabilidad['4']*75+$sustentabilidad['3']*50+$sustentabilidad['2']*25+$sustentabilidad['1']*10)/$total;

                                ?>
                                <div style="max-width: 300px; margin: 0 auto;">
                                    <canvas id="gaugePromedioBarSustentabilidad" style="width: 100%; height: auto;"></canvas>
                                </div>


                                <script>
                                    var ctx = document.getElementById('gaugePromedioBarSustentabilidad').getContext('2d');
                                    var gaugePromedioBar = new Chart(ctx, {
                                        type: 'bar',
                                        data: {
                                            labels: ['Promedio'],
                                            datasets: [{
                                                label: 'Nivel de Promedio',
                                                data: [<?php echo $promedio; ?>],
                                                backgroundColor: '#ec0617'
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                            indexAxis: 'y', // Coloca el gráfico en formato horizontal
                                            scales: {
                                                x: {
                                                    min: 0,
                                                    max: 100,
                                                    ticks: {
                                                        callback: function(value) {
                                                            return value + '%'; // Muestra el porcentaje
                                                        }
                                                    }
                                                }
                                            },
                                            plugins: {
                                                legend: {
                                                    display: false
                                                },
                                                tooltip: {
                                                    callbacks: {
                                                        label: function(tooltipItem) {
                                                            return tooltipItem.dataset.label + ': ' + tooltipItem.raw + '%';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    });
                                </script>

                                <!--<img src="
					http://chart.apis.google.com/chart?
					chs=300x120
					&chf=bg,s,65432100
					&cht=gom
					&chd=t:<?php echo $promedio?>
					&chl=Promedio"
					/>-->
                            </td>
                            <td>
                                <?php
                                foreach($respuestas as $preg=>$resp){
                                    $etiquetas=array();
                                    $valores=array();
                                    $colores=array();
                                    $total = array_sum($resp);
                                    if($preg=="3r" or $preg=="3s"){
                                        $preguntasContestadas++;
                                        $preguntasContestadasSustentabilidad++;
                                        foreach($resp as $valor=>$cant){ //genero los valores para los graficos
                                            if($preg!=3){
                                                $etiquetas[]=$respuesta[$preg][$valor];
                                            }else{
                                                $etiquetas[]=$valor;
                                            }
                                            $valores[]=round($cant/$total*100,2);
                                            $promedios[$preg] +=$cant*$valor;
                                            switch ($valor) {

                                                case 1:
                                                    $color="ec0617";
                                                    break;
                                                case 2:
                                                    $color="faab52";
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
                                            $colores[]=$color;
                                        }
                                        $sumaPromedios +=$promedios[$preg]/$total;
                                        $sumaPromediosSustentabilidad +=$promedios[$preg]/$total;
                                        ?>
                                        <div id="tabla">
                                            <strong><?php echo $pregunta[$preg]?></strong>
                                            <div style="max-width: 300px; margin: 0 auto;">
                                                <canvas id="graficoPreguntaSustentabilidad<?php echo $preg;?>" style="width: 100%; height: auto;"></canvas>
                                            </div>


                                            <script>
                                                var ctx = document.getElementById('graficoPreguntaSustentabilidad<?php echo $preg;?>').getContext('2d');
                                                var graficoPregunta = new Chart(ctx, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: <?php echo json_encode($etiquetas); ?>,
                                                        datasets: [{
                                                            label: 'Distribución de Respuestas',
                                                            data: <?php echo json_encode($valores); ?>,
                                                            backgroundColor: <?php echo json_encode(array_map(function($color) { return "#" . $color; }, $colores)); ?>
                                                        }]
                                                    },
                                                    options: {
                                                        responsive: true,
                                                        maintainAspectRatio: false, // Permite que el gráfico se adapte al tamaño CSS
                                                        plugins: {
                                                            legend: {
                                                                position: 'top',
                                                            },
                                                            tooltip: {
                                                                callbacks: {
                                                                    label: function(tooltipItem) {
                                                                        return tooltipItem.label + ': ' + tooltipItem.raw + '%';
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                });
                                            </script>

                                            <!--<br />
						<img src="
						http://chart.apis.google.com/chart?
						chs=185x70
						&chf=bg,s,65432100
						&chd=t:<?php echo implode(",",$valores)?>
						&cht=p3
						&chco=<?php echo implode(",",$colores)?>
						&chl=<?php echo implode("|",$etiquetas)?>&chdl=<?php echo implode("|",$valores)?>"
						/>
						<br />--><?php echo $total?> / <?php echo count($encuestas)?>
                                        </div>
                                        <?php

                                    }
                                } //foreach que recorre las respuesats
                                echo "<script> $('#puntajeSustentabilidad').html(".round($sumaPromediosSustentabilidad/$preguntasContestadasSustentabilidad,2).");</script>";
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--/Sustentabilidad-->
                <?php
                //print_r($promedios);
                //$sumaPromedios +=$promedios[$preg]/count($encuestas);
                //echo $promedios[$preg]."/".count($encuestas);

                echo "<script> $('#puntaje').html(".round($sumaPromedios/$preguntasContestadas,2).");</script>";
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
        <?php
        $currentYear = date("Y"); // Año actual
        $startYear = 2011; // Año de inicio del rango


        ?><select id="ano" name="ano">
                <?php for ($year = $startYear; $year <= $currentYear; $year++): ?>
                    <option value="<?php echo $year; ?>" <?php if ($ano == $year) echo 'selected="selected"'; ?>>
                        <?php echo $year; ?>
                    </option>
                <?php endfor; ?>
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
            <th width="20">Modificar</th>
        </tr>
        </thead>
        <tbody>
        <?php
        function encodeURIComponent($str) {
            $revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
            return strtr(rawurlencode($str), $revert);
        }
        if(isset($_POST['ver'])){
            $sql1 = "SELECT reservas.*,clientes.nombre_apellido,clientes.email,clientes.codArea, clientes.telefono  
	FROM reservas INNER JOIN clientes ON clientes.id=reservas.cliente_id 
	
	WHERE
			YEAR(reservas.check_out)='".$_POST['ano']."' AND  MONTH(reservas.check_out)='".$_POST['mes']."'
			AND ((reservas.estado != 2 AND reservas.estado != 3) OR reservas.estado is null)
			ORDER BY reservas.check_out DESC";

            if ($sql1) {
                $rsTemp1 = mysqli_query($conn,$sql1);

                if(mysqli_affected_rows($conn)>0){
                    while($rs1 = mysqli_fetch_array($rsTemp1)){
                        $enviada=0;
                        $sql2 = "SELECT respondida,enviada 
			FROM encuesta where reserva_id = ".$rs1['id'];

                        $rsTemp2 = mysqli_query($conn,$sql2);
                        if(mysqli_affected_rows($conn)>0){
                            $imgEnviada = "ok.gif";
                            if($rs2 = mysqli_fetch_array($rsTemp2)){
                                $imgRespuesta = ($rs2['respondida'])?"ok.gif":"bt_delete.png";
                                $enviada=$rs2['enviada'];
                            }
                            //$enviarEncuesta ="";
                        }
                        else{
                            $imgEnviada = "bt_delete.png";
                            $imgRespuesta = "bt_delete.png";

                        }
                        $phone = $rs1['codArea'].$rs1['telefono']; // Dejar vacio si quieres que el usuario elija a quien enviar el mensaje
                        $actual_link = 'https://villagedelaspampas.com.ar/encuesta.php?id='.$rs1['id'];
                        $message = "Estimado ".$rs1['nombre_apellido']." aprovechamos la oportunidad para invitarlos a participar de nuestra encuesta de satisfaccion. Ingresando al siguiente link";
                        //$message = str_replace(" ", "%20", $message); // Remplazamos los espacios por su equivalente
                        $mensaje = $message.' '.$actual_link;

                        $wa_link = "https://wa.me/$phone?text=".encodeURIComponent($mensaje);
                        $enviarEncuesta = '<a href="#" onclick="enviar('.$rs1['id'].')" class="item"><img src="images/mail.png" align="absmiddle" />('.$enviada.') </a><a href="'.$wa_link.'" target="_blank"  class="item"><img width="16px;" src="images/whatsapp.png" align="absmiddle" /></a>';
                        $modificarEncuesta ="";
                        if(($imgRespuesta != "bt_delete.png")&&(ACCION_115)) {
                            $modificarEncuesta = '<a href="#" onclick="modificar('.$rs1['id'].')" class="item"><img src="images/ico_users.png" align="absmiddle" /></a>';
                        }
                        ?>



                        <tr>
                            <td><?php echo $rs1['numero']?></td>
                            <td><?php echo $rs1['nombre_apellido']?></td>
                            <td><?php echo fechavista($rs1['check_out'])?></td>
                            <td><?php echo $rs1['email']?></td>
                            <td style="text-align: center;"><img src="images/<?php echo $imgEnviada?>"></img></td>
                            <td style="text-align: center;"><img src="images/<?php echo $imgRespuesta?>"></img></td>
                            <td style="text-align: center;"><?php echo $enviarEncuesta?></td>
                            <td style="text-align: center;"><?php echo $modificarEncuesta?></td>
                        </tr>
                    <?php } ?>
                <?php }
            }
        }?>

        </tbody>
    </table>
</div>


<div id="divHabilitarPreguntas" style="display:<?php echo $displayHabilitarPreguntas;?>">
    <table width="50%" cellspacing="0" border="1" style="table-layout:fixed;">
        <thead>
        <tr>

            <th width="80">Pregunta</th>
            <th width="20">Habilitar</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT *
	FROM encuesta_preguntas ";
        //echo $sql;
        $rsTemp1 = mysqli_query($conn,$sql);
        //print_r($rsTemp1);

        while ($rs = mysqli_fetch_array($rsTemp1)){
            $checked = ($rs['activa'])?'checked="checked"':'';
            echo '<tr>';
            echo '<td>'.$rs['nombre'].'</td>';
            echo '<td style="text-align: center;"><input type="checkbox" id="'.$rs['id'].'" name="inputActiva'.$rs['id'].'" '.$checked.'></td>';
            echo '</tr>';
        }
        ?>
        <tbody>
    </table>
    <input type="hidden" id="hiddenHabilitarPreguntas" name="hiddenHabilitarPreguntas" value="0"/>
</div>

</body>
<script>
    $('input:checkbox').change(function(e) {
        e.preventDefault();
        var isChecked = $("input:checkbox").is(":checked") ? 1:0;
//alert($("input:checkbox").attr("id"));
        $.ajax({
            type: 'POST',
            url: 'functions/editarPregunta.php',
            data: { id:$("input:checkbox").attr("id"), valor:isChecked }
        });
    });

</script>
</html>
