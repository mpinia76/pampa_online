<div id="menu" style="display:none; width: 320px; background: #fff; padding: 10px;">
    <div class="title" onclick="$('.operaciones').toggle();"><img width="30" src="images/ico_operaciones.png" align="absmiddle" /> Operaciones</div>
        <?php if(MENU_20){?>
        <div class="operaciones item" onclick="createWindow('w_reservas','Reservas','v2/reservas/index','1230','600'); $('#menu').hide();">
            Ventas
        </div>
        <?php } ?>
    <?php if(MENU_21){?>
    <div class="operaciones item" onclick="createWindow('w_reservas','Carga de extras y facturas','v2/reservas/index_restringido','1230','600'); $('#menu').hide();">
            Carga de extras y facturas
        </div>
    <?php } ?>
        <?php if(MENU_7){?>
        <div class="operaciones item" onclick="createWindow('w_gasto','Administrador de gastos y compras','v2/gastos/index','1230','650'); $('#menu').hide();">
            Gastos y compras
        </div>
        <?php } ?>
        <?php if(MENU_13){?>
        <div class="operaciones item" onclick="createWindow('w_compra','Administrador de impuestos, tasas y cargas sociales','compras.php','1230','600'); $('#menu').hide();">
            Impuestos, tasas y cargas sociales
        </div>
        <?php } ?>
        <?php if(MENU_13){?>
        <div class="operaciones item" onclick="createWindow('w_compra','Administrador de impuestos, tasas y cargas sociales','v2/compras/index','1230','600'); $('#menu').hide();">
            Impuestos, tasas y cargas sociales 2
        </div>
        <?php } ?>
        <?php if(MENU_8){?>
        <div class="operaciones item" onclick="createWindow('w_cuenta_a_pagar','Cuentas a pagar','cuentas_pagar.php','1230','600'); $('#menu').hide();">
            Cuentas a pagar
        </div>
        <?php } ?>
        <?php if(ACCION_116){ ?>
        <div class="operaciones item" onclick="createWindow('w_planes_pagos','Administrador de planes de pagos','v2/cuota_plans/index','1230','650'); $('#menu').hide();">
            Planes de pagos
        </div>
        <?php } ?>
         <?php if(ACCION_113){ ?>
      	<div class="operaciones item" onclick="createWindow('w_ventas_grilla','Grilla de reservas','v2/informes/index_ventas_grilla','1230','600'); $('#menu').hide();">
            Grilla de reservas
        </div>
        <?php } ?>
        <div class="title" onclick="$('.administracion').toggle();"><img width="30" src="images/ico_admin.png" align="absmiddle" /> Administracion</div>
        <?php if(MENU_4){?>
        <div class="administracion item" onclick="createWindow('w_cuenta','Administrador de cuentas','cuentas.php','1230','400'); $('#menu').hide();">
            Creaci&oacute;n y Conciliaci&oacute;n de cuentas Bancarias
        </div>
        <?php } ?>
        <?php if(MENU_16){?>
        <div class="administracion item" onclick="createWindow('w_caja','Administrador de cajas','cajas.php','1230','400'); $('#menu').hide();">
            Creaci&oacute;n y Conciliaci&oacute;n de Cajas
        </div>
        <?php } ?>
        <?php if(MENU_6){?>
        <div class="administracion item" onclick="createWindow('w_empleado','Administrador de empleados','empleados.php','900','400'); $('#menu').hide();">
            Administrador de Empleados
        </div>
        <?php } ?>
        <?php if(MENU_19){?>
        <div class="administracion item" onclick="createWindow('w_empleados_sueldos','Administrador de sueldos','empleados_sueldos.php','1230','435'); $('#menu').hide();">
            Pago de haberes
        </div>
        <?php } ?>

        <?php if(MENU_12){?>
        <div class="administracion item" onclick="createWindow('w_proveedors','Proveedores','v2/proveedors/index','1230','400'); $('#menu').hide();">
            Alta de Proveedores
        </div>
        <?php } ?>
         <?php if(MENU_41){?>
        <div class="administracion item" onclick="createWindow('w_chequeras','Carga y asignacion de chequeras','v2/chequeras/index','1230','500'); $('#menu').hide();">
            Carga y asignacion de chequeras
        </div>
        <?php } ?>
        <?php if(MENU_14){?>
        <div class="administracion item" onclick="createWindow('w_cheque_consumo','Movimientos de cheques','v2/cheque_consumos/index','1230','500'); $('#menu').hide();">
            Cheques a debitar
        </div>

        <?php } ?>
        <?php if(MENU_15){?>
        <div class="administracion item" onclick="createWindow('w_transferencia_consumo','Transferencias','transferencias_movimientos.php','1230','500'); $('#menu').hide();">
            Transferencias a debitar
        </div>
        <?php } ?>

        <?php if(MENU_22){?>
        <div class="administracion item" onclick="createWindow('w_cobro_cheques','Cheques a acreditar','v2/cobro_cheques/index','1230','500'); $('#menu').hide();">
            Cheques de 3ros en cartera
        </div>
        <?php } ?>
        <?php if(MENU_23){?>
        <div class="administracion item" onclick="createWindow('w_cobro_transferencias','Transferencias a acreditar','v2/cobro_transferencias/index','1230','500'); $('#menu').hide();">
            Transferencias a acreditar
        </div>
        <?php } ?>
        <?php if(MENU_24){?>
        <div class="administracion item" onclick="createWindow('w_cobro_tarjetas','Transacciones con tarjeta','v2/cobro_tarjetas/index','1230','500'); $('#menu').hide();">
            Transacciones con tarjeta
        </div>
        <?php } ?>
        <?php if(MENU_25){?>
        <div class="administracion item" onclick="createWindow('w_cobro_tarjeta_lotes','Liquidaciones y lotes','v2/cobro_tarjeta_lotes/index','1230','500'); $('#menu').hide();">
            Liquidaciones y lotes
        </div>
        <?php } ?>
        <?php if(ACCION_68){ ?>
         <div class="administracion item" onclick="createWindow('w_configuracion_borrar','Borrar ordenes','borrar_orden.php','600','400'); $('#menu').hide();">
            Borrado de ordenes
         </div>
        <?php } ?>
        <?php if(MENU_26){?>
        <div class="administracion item" onclick="createWindow('w_tarjeta_resumen','Resumen de tarjetas','tarjeta_resumen.php','1230','420'); $('#menu').hide();">
            Resumen de Tarjetas Corporativas
        </div>
        <?php } ?>
         <?php if(MENU_35){?>
        <div class="administracion item" onclick="createWindow('w_facturacion_electronica','Facturacion electronica','facturacion_electronica.php','1230','400'); $('#menu').hide();">
            Facturacion electronica
        </div>
        <?php } ?>

        <div class="title" onclick="$('.configuracion').toggle();"><img width="30" src="images/ico_configure.png" align="absmiddle" /> Configuracion</div>
        <?php if(MENU_1){?>
        <div class="configuracion item" onclick="createWindow('w_usuario','Administrador de usuarios','usuarios.php','600','400'); $('#menu').hide();">
            Administracion de Usuarios
        </div>
        <?php } ?>
        <?php if(MENU_2){?>
        <div class="configuracion item" onclick="createWindow('w_rubro','Administrador de rubros de plan de cuentas','rubros.php','600','400'); $('#menu').hide();">
            Rubros de plan de cuentas
        </div>
        <?php } ?>
        <?php if(MENU_3){?>
        <div class="configuracion item" onclick="createWindow('w_subrubro','Administrador de subrubros de plan de cuentas','subrubros.php','600','400'); $('#menu').hide();">
            Subrubros de plan de cuentas
        </div>
        <?php } ?>
        <?php if(MENU_5){?>
        <div class="configuracion item" onclick="createWindow('w_tarjeta','Administrador de tarjetas','tarjetas.php','650','400'); $('#menu').hide();">
            Tarjetas Corporativas
        </div>
        <?php } ?>
        <?php if(MENU_27){?>
        <div class="configuracion item" onclick="createWindow('w_motivo','Motivos de ajuste','motivos.php','600','400'); $('#menu').hide();">
            Conceptos de Ajuste
        </div>
        <?php } ?>
        <?php if(MENU_18){?>
        <div class="configuracion item" onclick="createWindow('w_sector','Sectores','sectores.php','600','400'); $('#menu').hide();">
            Sectores y Valorizaci&oacute;n de Horas Extras
        </div>
        <?php } ?>
        <?php if(MENU_39){?>
        <div class="configuracion item" onclick="createWindow('w_canals','Canales de venta','v2/canals/index','600','400'); $('#menu').hide();">
            Canales de venta
        </div>
        <?php } ?>
         <?php if(MENU_40){?>
        <div class="configuracion item" onclick="createWindow('w_subcanals','Subcanales','v2/subcanals/index','600','400'); $('#menu').hide();">
            Subcanales de venta
        </div>
        <?php } ?>
        <?php if(MENU_38){?>
        <div class="configuracion item" onclick="createWindow('w_categorias','Categorias','v2/categorias/index','600','400'); $('#menu').hide();">
            Categor&iacute;as
        </div>
        <?php } ?>
        <?php if(MENU_28){?>
        <div class="configuracion item" onclick="createWindow('w_apartamentos','Apartamentos','v2/apartamentos/index','600','500'); $('#menu').hide();">
            Apartamentos y Capacidad
        </div>
        <?php } ?>
        <?php if(MENU_29){?>
        <div class="configuracion item" onclick="createWindow('w_extra_rubros','Extra Rubros','v2/extra_rubros/index','600','500'); $('#menu').hide();">
            Rubros de Extras
        </div>
        <?php } ?>
        <?php if(MENU_30){?>
        <div class="configuracion item" onclick="createWindow('w_extra_subrubros','Extra Subrubros','v2/extra_subrubros/index','600','500'); $('#menu').hide();">
            Subrubros de Extras
        </div>
        <?php } ?>
        <?php if(MENU_31){?>
        <div class="configuracion item" onclick="createWindow('w_extras','Extras','v2/extras/index','600','500'); $('#menu').hide();">
            Menu de Extras y Valorizaci&oacute;n
        </div>
        <?php } ?>
        <?php if(MENU_32){?>
        <div class="configuracion item" onclick="createWindow('w_cobro_tarjeta_posnets','Cobro Tarjeta Locaciones','v2/cobro_tarjeta_posnets/index','600','500'); $('#menu').hide();">
            Terminales de Cobro con Tarjeta
        </div>
        <?php } ?>
        <?php if(MENU_33){?>
        <div class="configuracion item" onclick="createWindow('w_cobro_tarjeta_tipos','Tarjetas: Asociacion cuenta y numero de comercio','v2/cobro_tarjeta_tipos/index','600','500'); $('#menu').hide();">
            Tarjetas: Asociacion cuenta y numero de comercio
        </div>
        <?php } ?>
        <?php if(MENU_34){?>
        <div class="configuracion item" onclick="createWindow('w_cobro_tarjeta_cuotas','Cobro Tarjeta Cuotas','v2/cobro_tarjeta_cuotas/index','600','500'); $('#menu').hide();">
            Cuotas y Coheficientes
        </div>
        <?php } ?>

        <?php if(ACCION_26){ ?>
        <div class="configuracion item" onclick="createWindow('w_configuracion_monto','Configuracion del sistema','configuracion.am.php','600','400'); $('#menu').hide();">
            Gastos y Compras: Montos Aprobaci&oacute;n
        </div>
        <?php } ?>
        <?php if(ACCION_97){?>
        <div class="configuracion item" onclick="createWindow('w_documentos','Carga de documentacion de sistema','documentos.php','600','400'); $('#menu').hide();">
            Carga de documentacion de sistema
        </div>
        <?php } ?>
         <?php if(MENU_36){?>
        <div class="configuracion item" onclick="createWindow('w_puntoVentas','Puntos de venta','v2/punto_ventas/index','600','500'); $('#menu').hide();">
            Puntos de venta
        </div>
        <?php } ?>
         <?php if(MENU_37){?>
        <div class="configuracion item" onclick="createWindow('w_espacio_trabajos','Centros de costos','v2/espacio_trabajos/index','600','500'); $('#menu').hide();">
            Centros de costos
        </div>
        <?php } ?>
    <?php if(MENU_43){?>
        <div class="configuracion item" onclick="createWindow('w_concepto_facturacions','Conceptos de Facturacion','v2/concepto_facturacions/index','600','400'); $('#menu').hide();">
            Conceptos de Facturacion
        </div>
    <?php } ?>
    <?php if(MENU_44){?>
        <div class="configuracion item" onclick="createWindow('w_grilla_feriados','Carga de feriados en grilla','v2/grilla_feriados/index','600','400'); $('#menu').hide();">
            Carga de feriados en grilla
        </div>
    <?php } ?>
        <div class="title" onclick="$('.informes').toggle();"><img width="30" src="images/ico_informes.png" align="absmiddle" /> Informes</div>
        <?php if(ACCION_108){ ?>
        <div class="informes item" onclick="createWindow('w_informe','Informe economico','informe.economico.php','1230','400'); $('#menu').hide();">
            Egresos
        </div>
        <?php } ?>
        <?php if(ACCION_120){ ?>
        <div class="informes item" onclick="createWindow('w_informe_impuestos','Impuestos, tasas y cargas sociales','informe.impuestos.php','1230','400'); $('#menu').hide();">
            Impuestos, tasas y cargas sociales
        </div>
        <?php } ?>
        <?php if(ACCION_109){ ?>
        <div class="informes item" onclick="createWindow('w_informe','Informe financiero','informe.financiero.php','1230','400'); $('#menu').hide();">
            Financiero de Egresos
        </div>
        <?php } ?>
        <?php if(ACCION_24){ ?>
        <div class="informes item" onclick="createWindow('w_ventas_informe_economico','Informe economico de ventas','v2/informes/index_ventas_economico','1230','600'); $('#menu').hide();">
            Ventas
        </div>
        <?php } ?>
        <?php if(ACCION_122){ ?>
        <div class="informes item" onclick="createWindow('w_ventas_informe_extras','Informe de extras','v2/informes/index_ventas_extras','1230','600'); $('#menu').hide();">
            Informe de extras
        </div>
        <?php } ?>
        <?php if(ACCION_25){ ?>
        <div class="informes item" onclick="createWindow('w_ventas_informe_financiero','Informe financiero de ventas','v2/informes/index_ventas_financiero','1230','600'); $('#menu').hide();">
            Financiero de Ventas
        </div>
        <?php } ?>

        <?php if(ACCION_107){ ?>
        <div class="informes item" onclick="createWindow('w_informe_cheques','Informe de cheques','informe.cheque.php','1230','400'); $('#menu').hide();">
            Movimiento de Cheques librados
        </div>
        <?php } ?>
        <?php if(ACCION_110){ ?>
      	<div class="informes item" onclick="createWindow('w_ventas_informe_ocupacion','Informe de ocupacion','v2/informes/index_ventas_ocupacion','1230','600'); $('#menu').hide();">
            Informe de ocupacion
        </div>
        <?php } ?>
        <?php if(ACCION_112){ ?>
        <div class="informes item" onclick="createWindow('w_libro_iva_compras','Libro IVA Compras','v2/informes/index_iva_compras','1230','600'); $('#menu').hide();">
            Libro IVA Compras
        </div>
        <?php } ?>
        <?php if(ACCION_131){ ?>
        <div class="informes item" onclick="createWindow('w_libro_iva_ventas','Libro IVA Ventas','v2/informes/index_iva_ventas','1230','600'); $('#menu').hide();">
            Libro IVA Ventas
        </div>
        <?php } ?>
         <?php if(ACCION_114){ ?>
        <div class="informes item" onclick="createWindow('w_informe_satisfaccion','Informes de satisfaccion','informe_satisfaccion.php','1230','400'); $('#menu').hide();">
            Informes de satisfacci&oacute;n
        </div>
        <?php } ?>
        <?php if(ACCION_127){ ?>
        <div class="informes item" onclick="createWindow('w_audiroria_usuario','Auditoria de Usuarios','v2/usuario_logs/index_auditoria','1230','400'); $('#menu').hide();">
            Auditor&iacute;a de Usuarios
        </div>
        <?php } ?>

    <?php if(ACCION_141){ ?>
        <div class="informes item" onclick="createWindow('w_base_datos','Base de datos','v2/informes/index_base_datos','1230','600'); $('#menu').hide();">
            Base de datos
        </div>
    <?php } ?>
        <!--
        <div class="item" onclick="createWindow('w_operaciones_xpago','Operaciones por forma de pago','operaciones_xpago.php','600','420'); $('#menu').hide();">
            Operaciones por forma de pago
        </div>
        <div class="item" onclick="createWindow('w_operaciones_xtarjeta','Detalle de operaciones con tarjeta','operaciones_xtarjeta.php','800','420'); $('#menu').hide();">
            Detalle de operaciones con tarjeta
        </div>
        -->
</div>
