
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php echo $this->Html->charset(); ?>
    <title><?php echo $title_for_layout; ?></title>
    <?php echo $this->Html->css(array('bootstrap.min','dhtmlx/dhtmlxscheduler.css','bootstrap-theme.min.css','dhtmlx/dhtmlxmenu.css')); ?>
    <?php echo $this->Html->script(array('jquery','jquery-ui','dhtmlx/dhtmlxscheduler.js','dhtmlx/dhtmlxmenu.js','jquery.min','dhtmlx/ext/dhtmlxscheduler_readonly.js','dhtmlx/ext/dhtmlxscheduler_collision.js','dhtmlx/ext/dhtmlxscheduler_recurring.js','dhtmlx/ext/dhtmlxscheduler_serialize.js','dhtmlx/ext/dhtmlxscheduler_tooltip.js','dhtmlx/ext/dhtmlxscheduler_timeline.js','dhtmlx/dhtmlxcommon.js','dhtmlx/locale/locale_es.js','bootstrap.min.js')); ?>
    <?php echo $this->fetch('extra_scripts'); ?>
    <?php echo $this->Js->writeBuffer(); ?>

    <style>
        html, body{
            height:100%;
            overflow:hidden;
        }
        .checkin_event{
            border: 4px solid #000;
        }
        .checkout_event{
            border: 4px solid grey;
        }
		.white_cell{
		background-color:white;
		}
		.green_cell{
			background-color:#8D6190;
		}
        .orange_cell{
            background-color:#D3D3D3;
        }
		.yellow_cell{
			background-color:#c4accd;
		}
		.red_cell{
			background-color:#FF5353;
		}
		.grey_cell{
			background-color:#999;
		}
		.blue_cell{
			background-color:#00F;
		}
			#sedes{
		vertical-align:bottom;

		}
		.blink {
			font-weight: bold;
  			background-color: #000;
		  animation: blink-animation 1s steps(5, start) infinite;
		  -webkit-animation: blink-animation 1s steps(5, start) infinite;
		}
		@keyframes blink-animation {
		  to {
		    visibility: hidden;
		  }
		}
		@-webkit-keyframes blink-animation {
		  to {
		    visibility: hidden;
		  }
		}
    </style>


    <script type="text/javascript" charset="utf-8">
    	var xpos, ypos, dhxWins, position, oTable;
        function createWindow(id,titulo,url,w,h) {
            xpos = xpos+20;
            ypos = ypos+20;

            if(ypos>200){ ypos = 5; }
            if(xpos>300){ xpos = 50; }

            w1 = dhxWins.createWindow(id, xpos, ypos, w, h);
            w1.setText(titulo);
            w1.attachURL(url);
        }
		function show(){
			alert(scheduler.toXML());
		}

		function pad(input, length, padding) {
		  var str = input + "";
		  return (length <= str.length) ? str : pad(str+padding, length, padding);
		}


		function direccionaJS(url, parametro, id){
			var form = document.createElement("form");
		    input = document.createElement("input");

			form.action = url;
			form.method = "post"

			input.type = "hidden";
			input.name = parametro;
			input.value = id;
			form.appendChild(input);

			document.body.appendChild(form);
			form.submit();
		}

		function direccionaJS2parametros(url, parametro, id, parametro2, id2){
			var form = document.createElement("form");
		    input = document.createElement("input");
		    input2 = document.createElement("input");

			form.action = url;
			form.method = "post"

			input.type = "hidden";
			input.name = parametro;
			input.value = id;
			form.appendChild(input);

			input2.type = "hidden";
			input2.name = parametro2;
			input2.value = id2;
			form.appendChild(input2);

			document.body.appendChild(form);
			form.submit();
		}

		function cancelarReserva(idAlquiler){
			if (confirm('�Seguro que desea cancelar esta reserva?'))
				window.location.assign("Comercial/Reservas/procesarReserva.php?accion=5&idAlquileres="+idAlquiler);
		}

		// #88 para eventos q no son de la sede actual
		function block_readonly(id){
			if (!id) return true;
			return !this.getEvent(id).readonly;
		}

        function init() {
            scheduler.config.multi_day = true;
			brief_mode = true;
            scheduler.locale.labels.timeline_tab = "Timeline";
            scheduler.locale.labels.section_custom="Vehiculo";
            scheduler.config.xml_date="%Y-%m-%d %H:%i";

            scheduler.config.details_on_create=true; // 'says' to use the extended form while creating new events by drag or double click
            scheduler.config.details_on_dblclick=false;
            scheduler.config.drag_create = true;

			scheduler.config.drag_move = true; // No permite mover los eventos
			scheduler.config.drag_resize= false;
			scheduler.config.readonly = false;
			scheduler.config.dblclick_create = false;




			// Para evitar que se cambie la fecha cuando hace drag and drop
			/*scheduler.attachEvent("onBeforeEventChanged", function(ev, e, flag, ev_old){
				if (!flag) { // only for existing events
//				alert("No se puede modificar la fecha y dia");
					ev.start_date = ev_old.start_date;
					ev.end_date = ev_old.end_date;
				}
			 return true;
			 });*/

			 // Para que con doble clic vaya a Modificar Reserva
			 // Sacado para que con doble clic se abra el menu
			/*
			 scheduler.attachEvent("onDblClick", function(id){
			   // redirect on double click on event, pass event id
			   var eventObj = scheduler.getEvent(id);
			   if (eventObj['idReserva'] != 0) // Para evitar que se vaya de la pantalla al hacer doble clic en una reserva de Categoria Deshabilitada
			   		direccionaJS("Comercial/Reservas/modificarReserva.php", "idAlquiler", eventObj['idReserva']);
			   		// location.href = "Comercial/Reservas/modificarReserva.php?idAlquiler=" + eventObj['idReserva'];
			});
			*/

			//===============
			// Salvar
			//===============
			//scheduler.config.xml_date="%Y-%m-%d %H:%i";
			scheduler.config.prevent_cache = true;
			// Ver si son necesarios
			scheduler.config.include_end_by = true;
      		scheduler.config.repeat_precise = true;

			//===============
			// Tooltip related code
			//===============

			// we want to save "dhx_cal_data" div in a variable to limit look ups
			var scheduler_container = document.getElementById("scheduler_here");
			var scheduler_container_divs = scheduler_container.getElementsByTagName("div");
			var dhx_cal_data = scheduler_container_divs[scheduler_container_divs.length-1];

			// while target has parent node and we haven't reached dhx_cal_data
			// we can keep checking if it is timeline section
			scheduler.dhtmlXTooltip.isTooltipTarget = function(target) {
				while (target.parentNode && target != dhx_cal_data) {
					var css = target.className.split(" ")[0];
					// if we are over matrix cell or tooltip itself
					if (css == "dhx_matrix_scell" || css == "dhtmlXTooltip") {
						return { classname: css };
					}
					target = target.parentNode;
				}
				return false;
			};

			// Para la posicion del Tooltip.  Se establecio este valor para que no se pise con el menu contextual
			dhtmlXTooltip.config.delta_x = -350;
            // dhtmlXTooltip.config.delta_y = -20;

			scheduler.attachEvent("onMouseMove", function(id, e) {

				var timeline_view = scheduler.matrix[scheduler.getState().mode];

				// if we are over event then we can immediately return or if we are not on timeline view
				if (id || !timeline_view) {
					return;
				}

				// native mouse event
				e = e||window.event;
				var target = e.target||e.srcElement;

				var tooltip = scheduler.dhtmlXTooltip;
				var tooltipTarget = tooltip.isTooltipTarget(target);
				if (tooltipTarget) {

					if (tooltipTarget.classname == "dhx_matrix_scell") {
						// we are over cell, need to get what cell it is and display tooltip
						var section_id = scheduler.getActionData(e).section;
						var section = timeline_view.y_unit[timeline_view.order[section_id]];

						// showing tooltip itself
						var text = "Datos de apartamento: <b>"+section.label+"</b>";
						tooltip.delay(tooltip.show, tooltip, [e, text]);
					}
					if (tooltipTarget.classname == "dhtmlXTooltip") {
						dhtmlxTooltip.delay(tooltip.show, tooltip, [e, tooltip.tooltip.innerHTML]);
					}
				}
			});

			//===============
			// Menu Contextual
			//===============

			var eventoSeleccionado = null;
			var eventoClienteSeleccionado = null;
			// Para el clic derecho
			var menu = new dhtmlXMenuObject();
			menu.setSkin("dhx_terrace");
		//	menu.setIconsPath("./data/imgs/");
			menu.renderAsContextMenu();
			menu.loadStruct("../app/webroot/js/dhtmlx/dhxmenu.xml");


			menu.attachEvent("onClick", function(id, zoneId, cas){
				// zoneId used for context menu
				// ctrl, alt, shift state
				/*
				var casText = "";
				for (var a in {ctrl:1,alt:1,shift:1}) if (cas[a] == true) casText += " "+a+"=true";
				console.log("<b>onClick</b> id="+id+ " texto:"+ casText+" id " + eventoSeleccionado + "<br>");
				*/
				switch(id) {
				    case "modificarReserva":
				    	//direccionaJS("Comercial/Reservas/modificarReserva.php", "idAlquiler", eventoSeleccionado);
				    	direccionaJS("<?php echo $this->Html->url('/reservas/editar', true);?>/"+ eventoSeleccionado+"/1");
				        break;
				    case "cargarCobranza":
				    	//direccionaJS("Comercial/Cobranzas/altaCobranza.php", "idAlquiler", eventoSeleccionado);
				    	if((eventoColorSeleccionado!='#f1fa52')){
				    		direccionaJS("<?php echo $this->Html->url('/reserva_cobros/agregar', true);?>/"+ eventoSeleccionado+"/1");
				    	}
				    	else{
				    		alert('Apartamento Bloqueado');
				    	}
				        break;
                    case "modificarCheckIn":
                        //direccionaJS("Comercial/Cobranzas/altaCobranza.php", "idAlquiler", eventoSeleccionado);
                        if((eventoColorSeleccionado!='#f1fa52')){
                            if(confirm("Esta seguro que desea hacer el Check In?")){


                                $.ajax({
                                    url: "<?php echo $this->Html->url('/reservas/check_in', true);?>",
                                    data: {'reserva_id' : eventoSeleccionado},
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(data){

                                        if(data.resultado == 'ERROR'){
                                            alert(data.mensaje);
                                        }else{
                                            location.reload();
                                        }
                                    }
                                })

                            }
                        }
                        else{
                            alert('Apartamento Bloqueado');
                        }
                        break;
                    case "modificarCheckOut":
                        //direccionaJS("Comercial/Cobranzas/altaCobranza.php", "idAlquiler", eventoSeleccionado);
                        if((eventoColorSeleccionado!='#f1fa52')){
                            if(confirm("Esta seguro que desea hacer el Check Out?")){


                                $.ajax({
                                    url: "<?php echo $this->Html->url('/reservas/check_out', true);?>",
                                    data: {'reserva_id' : eventoSeleccionado},
                                    type: 'POST',
                                    dataType: 'json',
                                    success: function(data){

                                        if(data.resultado == 'ERROR'){
                                            alert(data.mensaje);
                                        }else{
                                            location.reload();
                                        }
                                    }
                                })

                            }
                        }
                        else{
                            alert('Apartamento Bloqueado');
                        }
                        break;
				    case "cancelarReserva":
				    	if((eventoColorSeleccionado=='#ff0033')||(eventoColorSeleccionado=='#f1fa52')){
				    		if(confirm("Esta seguro que desea cancelar la reserva?")){


					            $.ajax({
					                url: "<?php echo $this->Html->url('/reservas/cancelar', true);?>",
					                data: {'reserva_id' : eventoSeleccionado},
					                type: 'POST',
					                dataType: 'json',
					                success: function(data){

					                    if(data.resultado == 'ERROR'){
					                        alert(data.mensaje);
					                    }else{
					                        location.reload();
					                    }
					                }
					            })

						    }
				    	}
				    	else{
				    		alert('No se puede cancelar la reserva');
				    	}

				   		//cancelarReserva(eventoSeleccionado);
				        break;
			        case "entrega":
			        	direccionaJS2parametros("Comercial/Reservas/procesarReserva.php", "idAlquileres", eventoSeleccionado, "accion", 16);
				        break;
					case "devolucion":
						direccionaJS2parametros("Comercial/Reservas/procesarReserva.php", "idAlquileres", eventoSeleccionado, "accion", 17);
				        break;
		        	case "listadoCobranza":
		        		direccionaJS("Comercial/Cobranzas/listadoCobranzas.php", "idAlquiler", eventoSeleccionado);
				        break;
					case "modificarCliente":
		        		direccionaJS("Comercial/Cliente/altaCliente.php", "idCliente", eventoClienteSeleccionado);
				        break;
			        case "voucher":
				    	direccionaJS("Comercial/Reservas/voucher.php", "idAlquileres", eventoSeleccionado);
				        break;
				    default:
				       break;
				}

			});


		scheduler.attachEvent("onBeforeDrag",block_readonly);
		// scheduler.attachEvent("onClick",block_readonly);

			/* Menu con clic derecho
			Esto es para cuando estoy parado en la grilla pero no sobre un evento
			*/
			scheduler.attachEvent("onContextMenu", function (id, e){
			    var action_data = scheduler.getActionData(e);
				var eventObj = scheduler.getEvent(id);

				if (eventObj != null){

					eventoSeleccionado = eventObj.idReserva;
					eventoColorSeleccionado = eventObj.color;
					eventoClienteSeleccionado = eventObj.idCliente;
                    eventoCheckInSeleccionado = eventObj.checkIn;
				    //console.log(eventoCheckInSeleccionado);
				}

				return false; // Con esto evito el menu desplegable del navegador
			});



			scheduler.attachEvent("onContextMenu", function(event_id, native_event_object) {

				if (event_id) {

					// #88 Esto es para evitar que se despliegue el menu en eventos readonly (cuando son reservas de otra sede)
					var eventObj = scheduler.getEvent(event_id);
					if (eventObj.readonly)
						return false;

					var posx = 0;
					var posy = 0;
					if (native_event_object.pageX || native_event_object.pageY) {
						posx = native_event_object.pageX;
						posy = native_event_object.pageY;
					} else if (native_event_object.clientX || native_event_object.clientY) {
						posx = native_event_object.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
						posy = native_event_object.clientY + document.body.scrollTop + document.documentElement.scrollTop;
					}
					menu.showContextMenu(posx, posy);

					return false; // prevent default action and propagation
				}
				return true;
			});


			/* Menu con doble clic */
			scheduler.attachEvent("onDblClick", function (id, e){
			    var action_data = scheduler.getActionData(e);

				var eventObj = scheduler.getEvent(id);
				if (eventObj != null){
					eventoSeleccionado = eventObj.idReserva;
					eventoClienteSeleccionado = eventObj.idCliente;
				//	console.log(eventoClienteSeleccionado);
				}
				return false; // Con esto evito el menu desplegable del navegador
			});

			scheduler.attachEvent("onDblClick", function(event_id, native_event_object) {

				if (event_id) {

					// #88 Esto es para evitar que se despliegue el menu en eventos readonly (cuando son reservas de otra sede)
					var eventObj = scheduler.getEvent(event_id);
					if (eventObj.readonly)
						return false;

					var posx = 0;
					var posy = 0;
					if (native_event_object.pageX || native_event_object.pageY) {
						posx = native_event_object.pageX;
						posy = native_event_object.pageY;
					} else if (native_event_object.clientX || native_event_object.clientY) {
						posx = native_event_object.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
						posy = native_event_object.clientY + document.body.scrollTop + document.documentElement.scrollTop;
					}
					menu.showContextMenu(posx, posy);

					return false; // prevent default action and propagation
				}
				return true;
			});

		/*
		scheduler.attachEvent("ondrag_create", function (id, e){

					    var action_data = scheduler.getActionData(e);

						var eventObj = scheduler.getEvent(id);
						if (eventObj != null){
							eventoSeleccionado = eventObj.idReserva;
							eventoClienteSeleccionado = eventObj.idCliente;
						//	console.log(eventoClienteSeleccionado);
						}
						return false; // Con esto evito el menu desplegable del navegador
					});
		*/
		scheduler.attachEvent("onBeforeViewChange", function(old_mode,old_date,mode,date){
		    //alert(date.getFullYear()+'-'+(date.getMonth()+1)+'-'+date.getDate());


		   scheduler.clearAll();

		    scheduler.load('<?php echo $this->Html->url('/informes/ventas_grilla', true);?>/'+date.getFullYear()+'-'+pad((date.getMonth()+1),2,0)+'-'+pad(date.getDate(),2,0), 'json' );
		    //scheduler.enableAutoHeight(true,100);
		    return true;
		});
            scheduler.templates.event_class = function(start, end, ev){
                //console.log(ev.type);
                return ev.type;
            }
/*

scheduler.attachEvent("onDragIn",function(sid,tid,sgrid,tgrid){
	alert("dragin");
    if (tid)    // tid may be null if dropping is in the grid body
        scheduler.setRowTextStyle(tid,"background-color:red;");// marks current dropping
    return true;
})

scheduler.attachEvent("onDragOut",function(tid){
	alert("dragout");
    if (tid)
        scheduler.setRowTextStyle(tid,""); // clears styles set on the previous step
})


scheduler.attachEvent("onEventCreated", function(id,e){
     alert("dale");
     return true;
});

if (scheduler.getState().lightbox_id){
    alert("no se");
} else {
        alert("se se");
}


scheduler.attachEvent("onEventDrag", function (id, mode, e){
	alert("dale");
    //any custom logic here
    return true;
});
*/



scheduler.attachEvent("onBeforeEventChanged", function(ev, e, is_new, x_ind, y_ind, x_val, y_val){

    if (is_new){
    	var today = new Date();
    	// Para que tampoco se pueda crear del mismo dia, si queremos q se pueda poner en 0 ambos
    	today.setHours(0);
    	today.setMinutes(0);
    	if (today< ev.start_date && today< ev.end_date){
	       // ev.start_date.setHours(0);
			ev.start_date.setMinutes(0);
			ev.end_date.setMinutes(0);

		//	console.log("desde: " + ev.start_date) ;
		//	console.log("fin + " + ev.end_date );

			var date = new Date( ev.start_date);
			var mesDesde = pad( (date.getMonth() + 1), 2);
			var diaDesde = pad( (date.getDate() ), 2);



	       var fechaDesde =  date.getFullYear() +"-" + mesDesde +"-" + diaDesde;
	       // alert((date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear());

			var dateHasta = new Date( ev.end_date);
			var mesHasta = pad( (dateHasta.getMonth() + 1), 2);
			var diaHasta = pad( (dateHasta.getDate() ), 2);

			var fechaHasta =  dateHasta.getFullYear() +"-" + mesHasta +"-" + diaHasta;
	       // http://localhost/rentACarGestion/Comercial/Reservas/altaReserva.php?cbSede=1&fechaDesde=2016-08-05&horaDesde=10:00&fechaHasta=2016-08-09&horaHasta=10:00

	// x_ind.toSource()


		/*var url = "Comercial/Reservas/altaReserva.php?fechaDesde=" + fechaDesde + "&horaDesde=10:00&fechaHasta=" + fechaHasta + "&horaHasta=10:00&idVehiculo=" + x_ind._orig_section;*/

		direccionaJS("<?php echo $this->Html->url('/reservas/crear', true);?>/1/"+ x_ind._orig_section+"/"+fechaDesde+"/"+fechaHasta);



	/*
	// x_ind, y_ind, x_val, y_val, e){

		var action_data = scheduler.getActionData(e);
		alert(action_data.getEvent());
	    alert(" x-ind: " + x_ind +  " y-ind: " + y_ind +  " x-val: " + x_val +  " y-val: " + y_val );
	*/

	// alert(" x-ind: " + x_ind.toSource() +  " y-ind: " + y_ind +  " x-val: " + x_val +  " y-val: " + y_val );

	/*
				var action_data = scheduler.getActionData(e);

					var eventObj = scheduler.getEvent(id);
				if (eventObj != null){
					//eventObj.

				}

*/
			return true;
		}else{
			alert("No se puede crear una reserva con fecha desde o hasta anteriores al dia de hoy");
			return false;
			}

    }else{
    	//alert(ev.section_id+' '+x_ind._orig_section)
    	/*if(x_ind._orig_section==ev.section_id){
    		return false;
    	}
    	else{*/
	    	if(confirm('Seguro desea cambiar la reserva?')){
		    	//alert(ev.section_id+' '+ev.idReserva);
		    	var date = new Date( ev.start_date);
				var mesDesde = pad( (date.getMonth() + 1), 2);
				var diaDesde = pad( (date.getDate() ), 2);



		       var fechaDesde =  date.getFullYear() +"-" + mesDesde +"-" + diaDesde;
		       // alert((date.getMonth() + 1) + '/' + date.getDate() + '/' + date.getFullYear());

				var dateHasta = new Date( ev.end_date);
				var mesHasta = pad( (dateHasta.getMonth() + 1), 2);
				var diaHasta = pad( (dateHasta.getDate() ), 2);

				var fechaHasta =  dateHasta.getFullYear() +"-" + mesHasta +"-" + diaHasta;

				//alert(fechaDesde+' '+fechaHasta);
		    	$.ajax({
				        url : '<?php echo $this->Html->url('/reservas/modificarApartamento', true);?>',
				        data: {'data' : {'apartamento_id' : ev.section_id,'reserva_id' : ev.idReserva,'checkIn' : fechaDesde,'checkOut' : fechaHasta}},
				        type: 'POST',
				        dataType: 'json',
				        success: function(data){

				            if(data.resultado == 'ERROR'){
				                alert(data.mensaje);
				                location.reload();

				            }
				            //location.reload();
				        }
				    })
				    return true;
			}
			return false;
		//}

    }


});

			//===============
			// Clic zona libre - Nuevo Alquiler
			//===============
/*
scheduler.attachEvent("onCellClick", function (x_ind, y_ind, x_val, y_val, e){
	var action_data = scheduler.getActionData(e);
	alert(action_data.getEvent());
    alert(" x-ind: " + x_ind +  " y-ind: " + y_ind +  " x-val: " + x_val +  " y-val: " + y_val );
    //any custom logic here
});

			scheduler.attachEvent("onClick", function (id, e){
			    var action_data = scheduler.getActionData(e);
				var eventObj = scheduler.getEvent(id);
				alert("clic");
				if (eventObj != null){
					console.log(event);
					eventoSeleccionado = eventObj.idReserva;
					eventoClienteSeleccionado = eventObj.idCliente;
				//	console.log(eventoClienteSeleccionado);
				}

				return false; // Con esto evito el menu desplegable del navegador
			});



			scheduler.attachEvent("onClick", function(event_id, native_event_object) {
				if (event_id) {
					var posx = 0;
					var posy = 0;
					if (native_event_object.pageX || native_event_object.pageY) {
						posx = native_event_object.pageX;
						posy = native_event_object.pageY;
					} else if (native_event_object.clientX || native_event_object.clientY) {
						posx = native_event_object.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
						posy = native_event_object.clientY + document.body.scrollTop + document.documentElement.scrollTop;
					}
					// menu.showContextMenu(posx, posy);

					return false; // prevent default action and propagation
				}
				return true;
			});

*/

			//===============
            //Configuration
            //===============



            var sections=[
            <?php

            foreach($unidads as $unidad){


            		echo '{key:'.$unidad['Apartamento']['id'].', label: "'.$unidad['Apartamento']['apartamento'].'"},';
			}

            ?>];

            var feriados=[
                <?php

                foreach($feriados as $feriado){

                    $comienzo = new DateTime(str_replace('/','-',$feriado['GrillaFeriado']['desde']));
                    $final = new DateTime(str_replace('/','-',$feriado['GrillaFeriado']['hasta']));

                    for($i = $comienzo; $i <= $final; $i->modify('+1 day')){

                        echo '{fecha:"'.$i->format("Y-m-d").'"},';
                    }



                }

                ?>];

            //console.log(feriados);
			var $heightTest = $('#divGrillaData');
            //console.log($heightTest.height());
            var cantidadFilas = $heightTest.height() / 8;

            // scheduler.matrix["timeline"].x_size = 12;
// scheduler.setCurrentView(); // redraws scheduler

            scheduler.createTimelineView({
                name: "matrix",
			    x_unit:	"day",
                x_date:	"%j",
				section_autoheight:"false", // (if the section_autoheight property has value false, the height of cells will be equal to dy, otherwise the height of cells will be increased to fill all free space
				fit_events: "false", //(boolean) specifies whether the section's height should be increased, to fit all events in this section, or should be fixed (the dy parameter). Available from version 3.0. By default, true
				// dy: 25, // Altura minima de las filas //25
				dy: 33, // Altura minima de las filas //25
				dx: 250,
				y_size: 1,
				// event_dy : cantidadFilas,

				event_dy : 33, //45, //25


                x_step:	1, // the X-Axis step in 'x_unit's
                x_size: 33, // Cantidad de dias a mostrar
                x_start: 0,
                x_length: 15 , // The number of 'x_step's that will be scrolled at a time, when the user clicks on the 'next' button in the scheduler's header
                y_unit:	sections,
                y_property:	"section_id",
                render:"bar",
                second_scale:{
                    x_unit: "month", // unit which should be used for second scale
                    x_date: "%M" // date format which should be used for second scale, "July 01"
                }
            });


			//===============
			//Customization
			//===============
			scheduler.templates.matrix_cell_class = function(evs,x,y){
				var day = x.getDay();

                for (i = 0; i < feriados.length; i++) {

                    /*var desde = feriados[i]['desde'].split('/');
                    var dDesde = new Date(desde[2],desde[1]-1,desde[0]);
                    var hasta = feriados[i]['desde'].split('/');
                    var dHasta = new Date(hasta[2],hasta[1]-1,hasta[0]);
                    const UN_DIA_EN_MILISEGUNDOS = 1000 * 60 * 60 * 24;
                    const INTERVALO = UN_DIA_EN_MILISEGUNDOS * 7; // Cada semana
                    const formateadorFecha = new Intl.DateTimeFormat('es-MX', { dateStyle: 'medium', });*/

                    //for (let i = dDesde; i <= dHasta; i = new Date(i.getTime() + INTERVALO)) {
                    var d = new Date(feriados[i]['fecha']);
                    d.setDate(d.getDate() + 1);

                    if (x.getDate() == (d.getDate()) && (x.getMonth()) == (d.getMonth()) ){

                            return "orange_cell";
                        }
                    //}


                }

                // -------- Para el d�a de hoy
				var d = new Date();

				if (x.getDate() == (d.getDate()) && (x.getMonth()) == (d.getMonth()) )
					return "blue_cell";

				// --------- Para los fines de semana
				if (day == 0)
					return "grey_cell";
				else if (day == 6)
					return "yellow_cell";
				else
					return "white_cell";




			};
			scheduler.templates.matrix_scalex_class = function(date){
                for (i = 0; i < feriados.length; i++) {


                    var d = new Date(feriados[i]['fecha']);

                    d.setDate(d.getDate() + 1);
                    console.log(d);
                    if (date.getDate() == (d.getDate()) && (date.getMonth()) == (d.getMonth()) ){

                        return "orange_cell";
                    }



                }
				if (date.getDay()==0 || date.getDay()==6)  return "yellow_cell";

				return "";
			}



            //===============
            //Data loading
            //===============
             scheduler.config.lightbox.sections=[
                {name:"description", height:130, map_to:"text", type:"textarea" , focus:true},
                {name:"custom", height:23, type:"select", options:sections, map_to:"section_id" },
                {name:"time", height:72, type:"time", map_to:"auto"}
            ];

			var oneWeekAgo = new Date();
						oneWeekAgo.setDate(oneWeekAgo.getDate() - 7);
			            scheduler.init('scheduler_here',oneWeekAgo,"matrix"); // Poner fecha de hace 7 dias siempre. EL mes tiene un mes menos

            // scheduler.getEvent(id).readonly = true;





             scheduler.parse([

             <?php /*foreach($reservas as $reserva){


            		echo '{ start_date: "'.$reserva['retiro'].'", end_date: "'.$reserva['devolucion'].'", text:"<b>Titular:</b>'.$reserva['nombre'].'<br /> <b>Reserva Nro.:</b> '.$reserva['numero'].'<br /><b>Check IN:</b> '.$reserva['fecha_retiro'].' 10:00<br /><b>Check OUT:</b>'.$reserva['fecha_devolucion'].' 10:00<br />  <b>Total:</b>  '.$reserva['total'].'<b> Cobrado:</b>  '.$reserva['pagado'].'<b> A Cobrar:</b>  '.$reserva['pendiente'].'<br /><b> Comentarios:</b>  '.$reserva['comentarios'].'", idReserva:"'.$reserva['reserva_id'].'", section_id:'.$reserva['unidad_id'].' ,color:"'.$reserva['color'].'", idCliente:'.$reserva['cliente_id'].', readonly:false },';

			}*/
			?>
			],"json");


        }
	</script>
</head>
<body onload="init();">



            <form method="post" accept-charset="utf-8" class="form-inline">

            <div class="form-group">
            <!-- <input type="button" name="save" value="Guardar Cambios" onclick="salvar()" class="btn btn-success"><img src="<?php echo $this->webroot; ?>img/loading_save.gif" class="loading" id="loading_save" style="display:none"/>-->
            </div>
        </form>

        <form id="xml_form" method="post" accept-charset="utf-8">
            <input type="hidden" name="reservasModificadas" id="reservasModificadas">
        </form>

		<div id="scheduler_here" class="dhx_cal_container" style='width:100%; height:100%;'>
		<div class="dhx_cal_navline">
			<div class="dhx_cal_prev_button">&nbsp;</div>
			<div class="dhx_cal_next_button">&nbsp;</div>
			<div class="dhx_cal_today_button"></div>
			<div class="dhx_cal_date"></div>
        </div>
		<div class="dhx_cal_header">
		</div>
		<div class="dhx_cal_data" id="divGrillaData">
		</div>
	</div>

	<div id="menuData" style="display: none;">
		<div id="file" text="File">
			<div id="new" text="New" img="new.gif"></div>
			<div id="file_sep_1" type="separator"></div>
			<div id="open" text="Open" img="open.gif"></div>
			<div id="save" text="Save" img="save.gif"></div>
			<div id="saveAs" text="Save As..." imgdis="save_as_dis.gif" enabled="false"></div>
			<div id="file_sep_2" type="separator"></div>
			<div id="print" text="Print" img="print.gif"></div>
			<div id="pageSetup" text="Page Setup" imgdis="page_setup_dis.gif" enabled="false"></div>
			<div id="file_sep_3" type="separator"></div>
			<div id="close" text="Close" img="close.gif"></div>
		</div>
		<div id="m2" text="Edit">
			<div id="undo" text="Undo" img="undo.gif"></div>
			<div id="redo" text="Redo" img="redo.gif"></div>
			<div id="edit_sep_1" type="separator"></div>
			<div id="selectAll" text="Select All" img="select_all.gif"></div>
			<div id="edit_sep_2" type="separator"></div>
			<div id="cut" text="Cut" img="cut.gif"></div>
			<div id="cpoy" text="Copy" img="copy.gif"></div>
			<div id="paste" text="Paste" img="paste.gif"></div>
		</div>
		<div id="m3" text="Help">
			<div id="about" text="About..." img="about.gif"></div>
			<div id="help" text="Help" img="help.gif"></div>
			<div id="bugReporting" text="Bug Reporting" img="bug_reporting.gif"></div>
		</div>
	</div>

<script type="text/javascript">
function pad(n, width, z) {
  z = z || '0';
  n = n + '';
  return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

</script>

<script>//setTimeout('document.location.reload()',300000); </script>

</body>
</html>
