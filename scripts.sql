CREATE TABLE IF NOT EXISTS `control_reservas` (
  `id` int(11) NOT NULL,
  `numero` int(11) NOT NULL DEFAULT '0',

  `total_estadia` float NOT NULL DEFAULT '0',

  `total` float NOT NULL DEFAULT '0',
  monto_cobrado float NOT NULL DEFAULT '0',
  monto_devoluciones float NOT NULL DEFAULT '0',
  monto_descuentos float NOT NULL DEFAULT '0',
  monto_extras float NOT NULL DEFAULT '0'

) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO control_reservas ( id, numero,total_estadia,total)
SELECT id, numero,total_estadia,total
FROM reservas

update
    control_reservas,
    (select
        reserva_id, sum(reserva_cobros.monto_neto) as sumAttr
     from  reserva_cobros WHERE tipo != 'DESCUENTO'
     group by reserva_id) as a
set
    monto_cobrado = a.sumAttr
where
    control_reservas.id = a.reserva_id

update
    control_reservas,
    (select
        reserva_id, sum(reserva_cobros.monto_neto) as sumAttr
     from  reserva_cobros WHERE tipo = 'DESCUENTO'
     group by reserva_id) as a
set
    monto_descuentos = a.sumAttr
where
    control_reservas.id = a.reserva_id

update
    control_reservas,
    (select
        reserva_id, sum(reserva_extras.cantidad*reserva_extras.precio) as sumAttr
     from  reserva_extras
     group by reserva_id) as a
set
    monto_extras = a.sumAttr
where
    control_reservas.id = a.reserva_id

update
    control_reservas,
    (select
        reserva_id, sum(reserva_devoluciones.monto) as sumAttr
     from  reserva_devoluciones
     group by reserva_id) as a
set
    monto_devoluciones = a.sumAttr
where
    control_reservas.id = a.reserva_id

SELECT *, (total+monto_extras+monto_devoluciones-monto_descuentos) as monto_a_cobrar,monto_cobrado
FROM control_reservas
WHERE ROUND((total+monto_extras+monto_devoluciones-monto_descuentos),0)<ROUND(monto_cobrado,0)

###################################################14/11/2017#########################################################################
ALTER TABLE `cobro_tarjeta_tipos`
	ADD COLUMN `nro_comercio` INT(11) NULL DEFAULT '0';

ALTER TABLE `cobro_tarjetas`
	ADD COLUMN `lote_nuevo` VARCHAR(8) NULL AFTER `lote`;

ALTER TABLE `cobro_tarjetas`
	ADD COLUMN `fecha_pago` DATE NULL DEFAULT NULL AFTER `descuento_lote`;


ALTER TABLE `cobro_tarjeta_lotes`
	CHANGE COLUMN `fecha_cierre` `fecha_cierre` DATE NULL AFTER `numero`,
	CHANGE COLUMN `fecha_acreditacion` `fecha_acreditacion` DATE NULL AFTER `fecha_cierre`,
	CHANGE COLUMN `cerrado_por` `cerrado_por` INT(11) NULL AFTER `fecha_acreditacion`,
	CHANGE COLUMN `acreditado_por` `acreditado_por` INT(11) NULL AFTER `cerrado_por`,
	CHANGE COLUMN `monto_total` `monto_total` DECIMAL(10,2) NULL AFTER `acreditado_por`,
	CHANGE COLUMN `descuentos` `descuentos` DECIMAL(10,2) NULL AFTER `monto_total`;

ALTER TABLE `cuenta_movimiento`
	CHANGE COLUMN `registro_id` `registro_id` INT(11) NULL AFTER `origen`;
ALTER TABLE `cuenta_movimiento`
	CHANGE COLUMN `usuario_id` `usuario_id` INT(11) NULL AFTER `fecha`;

ALTER TABLE `cobro_tarjetas`
	CHANGE COLUMN `cobro_tarjeta_lote_id` `cobro_tarjeta_lote_id` INT(11) NULL AFTER `nacimiento`;

##################################16/11/2017###########################################################
INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Facturacion electronica');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('35', 'Descargar');

##################################07/08/2018###########################################################

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('20', 'Grilla de reservas');

ALTER TABLE `reserva_cobros`
	CHANGE COLUMN `finalizado` `finalizado` INT(11) NULL AFTER `monto_pendiente`;

ALTER TABLE `caja_movimiento`
	CHANGE COLUMN `registro_id` `registro_id` INT(11) NULL AFTER `origen`;

ALTER TABLE `caja_movimiento`
	CHANGE COLUMN `usuario_id` `usuario_id` INT(11) NULL AFTER `fecha`;

##################################03/09/2018############################################################
ALTER TABLE `apartamentos`
	ADD COLUMN `orden` INT NULL;

##################################12/09/2018############################################################
ALTER TABLE `clientes`
	ADD COLUMN `email2` VARCHAR(255) NULL AFTER `email`;

UPDATE clientes SET email2 = email;

ALTER TABLE `reservas`
	ADD COLUMN `voucher` INT(11) NOT NULL DEFAULT '0',
	ADD COLUMN `planilla` INT(11) NOT NULL DEFAULT '0';


##################################18/09/2018############################################################
CREATE TABLE `encuesta` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`reserva_id` INT(11) NOT NULL,
	`comentarios` TEXT NULL,
	`respondida` TINYINT(1) NOT NULL DEFAULT '0',
	`enviada` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=INNODB
AUTO_INCREMENT=1
;

CREATE TABLE `encuesta_respuestas` (
	`encuesta_id` INT(11) NOT NULL,
	`pregunta_id` VARCHAR(2) NOT NULL,
	`valor` VARCHAR(1) NOT NULL,
	`extra` VARCHAR(100) NOT NULL
)
COLLATE='latin1_swedish_ci'
ENGINE=INNODB
;

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Informes de satisfacción');

##################################17/10/2018############################################################
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Modificar Puntajes - Informes de satisfacción');

##################################29/10/2018############################################################
ALTER TABLE `cuenta`
	ADD COLUMN `controla_facturacion` TINYINT NOT NULL DEFAULT '0' AFTER `visible_en_informe`,
	ADD COLUMN `CUIT` VARCHAR(50) NULL AFTER `controla_facturacion`;

CREATE TABLE `punto_ventas` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`cuit` VARCHAR(50) NULL,
	`numero` VARCHAR(50) NULL,
	`descripcion` VARCHAR(255)NULL,
	`direccion` VARCHAR(255)NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=INNODB
;
INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Puntos de venta');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (36, 'Operar');

UPDATE `permiso_grupo` SET `nombre`='Carga de extras y facturas' WHERE  `id`=21;

ALTER TABLE `reserva_facturas`
	ADD COLUMN `punto_venta_id` INT(11) NULL DEFAULT '0' AFTER `id`;

CREATE TABLE `reserva_factura_importacions` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`fecha` DATETIME NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE `reserva_factura_importacion_items` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`reserva_factura_importacion_id` INT(11) NOT NULL,
	`nro` VARCHAR(50) NULL DEFAULT NULL,
	`tipo` VARCHAR(50) NULL DEFAULT '0',
	`fecha` DATE NULL DEFAULT NULL,
	`CAE` VARCHAR(50) NULL DEFAULT NULL,
	`nombre` VARCHAR(255) NULL DEFAULT NULL,
	`documento` VARCHAR(50) NULL DEFAULT NULL,
	`direccion` VARCHAR(255) NULL DEFAULT NULL,
	`moneda` VARCHAR(50) NULL DEFAULT NULL,
	`cotizacion` DECIMAL(10,2) NULL DEFAULT 0,
	`neto_gravado` DECIMAL(10,2) NULL DEFAULT 0,
	`base_21` DECIMAL(10,2) NULL DEFAULT 0,
	`iva_21` DECIMAL(10,2) NULL DEFAULT 0,
	`base_imponible` DECIMAL(10,2) NULL DEFAULT 0,
	`total_iva` DECIMAL(10,2) NULL DEFAULT 0,
	`total` DECIMAL(10,2) NULL DEFAULT 0,
	`exento` DECIMAL(10,2) NULL DEFAULT 0,
	`neto_no_gravado` DECIMAL(10,2) NULL DEFAULT 0,
	`base_0` DECIMAL(10,2) NULL DEFAULT 0,
	`iva_0` DECIMAL(10,2) NULL DEFAULT 0,
	base_2_5 DECIMAL(10,2) NULL DEFAULT 0,
	`iva_2_5` DECIMAL(10,2) NULL DEFAULT 0,
	`base_5` DECIMAL(10,2) NULL DEFAULT 0,
	`iva_5` DECIMAL(10,2) NULL DEFAULT 0,
	`base_10_5` DECIMAL(10,2) NULL DEFAULT 0,
	`iva_10_5` DECIMAL(10,2) NULL DEFAULT 0,
	`iva_19` DECIMAL(10,2) NULL DEFAULT 0,
	`base_27` DECIMAL(10,2) NULL DEFAULT 0,
	`iva_27` DECIMAL(10,2) NULL DEFAULT 0,
	`otros_tributos` DECIMAL(10,2) NULL DEFAULT 0,
	`provincia` VARCHAR(50) NULL DEFAULT '0',
	`condicion_iva` VARCHAR(50) NULL DEFAULT NULL,
	`exito` TINYINT(1) NULL DEFAULT NULL,
	`observaciones` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE `reserva_factura_procesada` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`reserva_id` INT(11) NOT NULL,
	`fecha` DATETIME NULL,
	`cliente` VARCHAR(255) NULL DEFAULT NULL,
	`dni` VARCHAR(50) NULL DEFAULT NULL,
	`total` DECIMAL(10,2) NULL DEFAULT 0,
	`neto` DECIMAL(10,2) NULL DEFAULT 0,
	`diferencia` DECIMAL(10,2) NULL DEFAULT 0,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

##################################07/12/2018############################################################
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (21, 'Editar y eliminar');

##################################20/12/2018############################################################
CREATE TABLE `encuesta_preguntas` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nombre` VARCHAR(255) NULL DEFAULT NULL,
	`activa` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB;

CREATE TABLE `encuesta_preguntas_items` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`encuesta_pregunta_id` INT(11) NOT NULL,
	`pregunta_id` VARCHAR(3) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB;


INSERT INTO `encuesta_preguntas` (`id`, `nombre`, `activa`) VALUES
	(1, 'Servicio de Alimentos y bebidas', 1);


INSERT INTO `encuesta_preguntas_items` (`id`, `encuesta_pregunta_id`, `pregunta_id`) VALUES
	(1, 1, '3u'),
	(2, 1, '3v'),
	(3, 1, '3w');

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Habilitar Preguntas - Informes de satisfaccion');

##################################21/12/2018############################################################
CREATE TABLE `usuario_log` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`usuario_id` INT(11) NOT NULL,
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	`nombre` VARCHAR(255) NULL DEFAULT NULL,
	`accion` VARCHAR(255) NULL DEFAULT NULL,
	`ip` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB;

INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Centros de Costos');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('37', 'Operar');

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('10', 'Informe de Impuestos, tasas y cargas sociales');

CREATE TABLE `usuario_rubro` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`usuario_id` INT(11) NOT NULL,
	`rubro_id` INT(11) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB;

UPDATE `permiso_grupo` SET `nombre`='Gastos y Compras' WHERE  `id`=7;
UPDATE `permiso_grupo` SET `nombre`='Impuestos, tasas y cargas sociales' WHERE  `id`=13;

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (13, 'Administrador de impuestos, tasas y cargas sociales');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Informe de Extras');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Informe de Extras Adelantados');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Informe de Extras No Adelantados');

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('8', 'Gastos y compras');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('8', 'Impuestos,tasas y Cargas sociales');

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Auditoria de Usuarios');

##################################09/01/2019############################################################
UPDATE `permiso` SET `nombre`='Pagar meses atrasados (Sueldos y Horas Extras)' WHERE  `id`=102;

UPDATE `configuracion` SET `descripcion`='Monto de gastos y compras que se aprueba automaticamente' WHERE  `id`='gasto_aprobado';
UPDATE `configuracion` SET `descripcion`='Monto de Impuestos,tasas y Cargas sociales que se aprueba automaticamente' WHERE  `id`='compra_aprobada';

##################################04/02/2019############################################################
INSERT INTO `ano` (`id`, `ano`) VALUES ('2019', '2019');
INSERT INTO `ano` (`id`, `ano`) VALUES ('2020', '2020');
INSERT INTO `ano` (`id`, `ano`) VALUES ('2021', '2021');
INSERT INTO `ano` (`id`, `ano`) VALUES ('2022', '2022');
INSERT INTO `ano` (`id`, `ano`) VALUES ('2023', '2023');
INSERT INTO `ano` (`id`, `ano`) VALUES ('2024', '2024');
INSERT INTO `ano` (`id`, `ano`) VALUES ('2025', '2025');
INSERT INTO `ano` (`id`, `ano`) VALUES ('2026', '2026');

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('13', 'Anular pagos y eliminar');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('7', 'Anular pagos y eliminar');


ALTER TABLE `reserva_facturas`
	ADD COLUMN `tipoDoc` INT(11) NULL DEFAULT '1' AFTER `punto_venta_id`;

##################################27/02/2019############################################################
ALTER TABLE `rubro`
	ADD COLUMN `gastos` INT(1) NOT NULL DEFAULT '1' AFTER `rubro`,
	ADD COLUMN `impuestos` INT(1) NOT NULL DEFAULT '1' AFTER `gastos`,
	ADD COLUMN `activo` INT(1) NOT NULL DEFAULT '1' AFTER `impuestos`;

ALTER TABLE `subrubro`
	ADD COLUMN `activo` INT(1) NOT NULL DEFAULT '1';

##################################01/03/2019############################################################
ALTER TABLE `punto_ventas`
	ADD COLUMN `ivaVentas` TINYINT(1) NOT NULL DEFAULT '0' AFTER `direccion`;

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Libro IVA ventas');

##################################11/03/2019############################################################
ALTER TABLE `cobro_cheques`
	ADD COLUMN `caja_acreditado` INT(11) NOT NULL AFTER `asociado_a_pagos_fecha`;

##################################24/04/2019############################################################

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (21, 'Permitir descuentos');

##################################06/05/2019############################################################
ALTER TABLE `cobro_tarjeta_tipos`
	ADD COLUMN `mostrar` TINYINT(1) NULL DEFAULT '0';

##################################18/06/2019###########################################################
CREATE TABLE `categorias` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`categoria` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `id` (`id`),
	INDEX `categoria` (`categoria`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Categorias');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (38, 'Operar');

ALTER TABLE `apartamentos`
	ADD COLUMN `categoria_id` INT(11) NULL AFTER `id`;

ALTER TABLE `apartamentos`
	ADD COLUMN `excluir` TINYINT(1) NOT NULL DEFAULT '0';

##################################27/06/2019###########################################################
ALTER TABLE `caja`
	ADD COLUMN `descubierto` INT(11) NOT NULL AFTER `visible_en_informe`,
	ADD COLUMN `sincronizacion` INT(11) NOT NULL AFTER `descubierto`,
	ADD COLUMN `dias_sincronizacion` INT(11) NOT NULL AFTER `sincronizacion`;


INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (16, 'Permitir mov. anteriores a la sincronizacion');

ALTER TABLE `reserva_cobros`
	ADD COLUMN `moneda_id` INT(11) NULL DEFAULT '1' AFTER `finalizado`,
	ADD COLUMN `monto_moneda` DECIMAL(10,2) NULL AFTER `moneda_id`,
	ADD COLUMN `cambio` DECIMAL(10,2) NULL AFTER `monto_moneda`;

CREATE TABLE `moneda` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`moneda` VARCHAR(100) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `id` (`id`),
	INDEX `moneda` (`moneda`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

INSERT INTO `moneda` (`moneda`) VALUES ('$');
INSERT INTO `moneda` (`moneda`) VALUES ('U$D');
INSERT INTO `moneda` (`moneda`) VALUES ('€');

ALTER TABLE `cobro_efectivos`
	ADD COLUMN `moneda_id` INT(11) NULL DEFAULT '1',
	ADD COLUMN `monto_moneda` DECIMAL(10,2) NULL AFTER `moneda_id`,
	ADD COLUMN `cambio` DECIMAL(10,2) NULL AFTER `monto_moneda`;

ALTER TABLE `caja_movimiento`
	ADD COLUMN `moneda_id` INT(11) NULL DEFAULT '1',
	ADD COLUMN `monto_moneda` DECIMAL(10,2) NULL AFTER `moneda_id`,
	ADD COLUMN `cambio` DECIMAL(10,2) NULL AFTER `monto_moneda`,
	ADD COLUMN `usados` DECIMAL(10,2) NULL DEFAULT '0' AFTER `cambio`;

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (16, 'Cambio de moneda extranjera');

################################### 08/06/2019 ###########################################
CREATE TABLE `canals` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`canal` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `id` (`id`),
	INDEX `canal` (`canal`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE `subcanals` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`subcanal` VARCHAR(250) NOT NULL,

	`canal_id` INT(11) NULL DEFAULT NULL,

	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Canales');
INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Subcanales');

ALTER TABLE `reservas`
	ADD COLUMN `subcanal_id` INT(11) NULL DEFAULT NULL AFTER `estado`;

################################### 24/09/2019 ###########################################
CREATE TABLE `plans` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`plan` VARCHAR(255) NOT NULL,
	`tipo` VARCHAR(255) NOT NULL,
	`monto` DECIMAL(10,2) NULL DEFAULT 0,
	`intereses` DECIMAL(10,2) NULL DEFAULT 0,
	`cuotas` INT(11) NOT NULL,
	`vencimiento1` DATE NULL,
	`vencimiento2` DATE NULL,
	`rubro_id` INT(11)  NULL,
	`subrubro_id` INT(11) NULL,
	`proveedor` VARCHAR(100) NULL,
	`user_id` INT(11)  NULL,

	PRIMARY KEY (`id`),
	INDEX `id` (`id`),
	INDEX `plan` (`plan`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE `cuota_plans` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,

	`plan_id` INT(11) NULL DEFAULT NULL,
	`vencimiento` DATE NULL,
	`monto` DECIMAL(10,2) NULL DEFAULT 0,
	`fecha_pago` DATE NULL,
	`estado` INT(11) NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;


INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (13, 'Planes de pagos');

ALTER TABLE `cuenta_a_pagar`
	ADD COLUMN `plan_id` INT(11) NULL;

ALTER TABLE `gasto`
	ADD COLUMN `plan_id` INT(11) NULL;

ALTER TABLE `compra`
	ADD COLUMN `plan_id` INT(11) NULL;


ALTER TABLE `proveedor`
	ADD COLUMN `razon` VARCHAR(250) NULL DEFAULT NULL AFTER `contacto`;

ALTER TABLE `gasto`
	ADD COLUMN `orden_pago` INT(11) NULL;

UPDATE cuenta_a_pagar
INNER JOIN gasto
			ON cuenta_a_pagar.operacion_id=gasto.id AND cuenta_a_pagar.operacion_tipo='gasto'
SET gasto.orden_pago = gasto.nro_orden
WHERE cuenta_a_pagar.estado = 1;

ALTER TABLE `compra`
	ADD COLUMN `orden_pago` INT(11) NULL;

UPDATE cuenta_a_pagar
INNER JOIN compra
			ON cuenta_a_pagar.operacion_id=compra.id AND cuenta_a_pagar.operacion_tipo='compra'
SET compra.orden_pago = compra.nro_orden
WHERE cuenta_a_pagar.estado = 1;

################################### 31/10/2019 ###########################################
ALTER TABLE `caja_movimiento`
	ALTER `fecha` DROP DEFAULT;
ALTER TABLE `caja_movimiento`
	CHANGE COLUMN `fecha` `fecha` DATETIME NOT NULL AFTER `monto`;


################################### 12/12/2019 ###########################################
CREATE TABLE `condicion_impositiva` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,

	`nombre` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

CREATE TABLE `jurisdiccion_inscripcion` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,

	`nombre` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

ALTER TABLE `proveedor`
	ADD COLUMN `condicion_impositiva_id` INT(11) NULL AFTER `razon`,
	ADD COLUMN `jurisdiccion_inscripcion_id` INT(11) NULL AFTER `condicion_impositiva_id`;

ALTER TABLE `proveedor`
	ALTER `telefono` DROP DEFAULT,
	ALTER `direccion` DROP DEFAULT,
	ALTER `cliente_nro` DROP DEFAULT,
	ALTER `email` DROP DEFAULT,
	ALTER `contacto` DROP DEFAULT;
ALTER TABLE `proveedor`
	CHANGE COLUMN `telefono` `telefono` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `nombre`,
	CHANGE COLUMN `direccion` `direccion` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `telefono`,
	CHANGE COLUMN `cliente_nro` `cliente_nro` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `rubro_id`,
	CHANGE COLUMN `email` `email` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `cuit`,
	CHANGE COLUMN `contacto` `contacto` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `email`;

ALTER TABLE `gasto`
	ADD COLUMN `origen` VARCHAR(50) NULL DEFAULT NULL AFTER `orden_pago`,
	ADD COLUMN `razon` VARCHAR(255) NULL DEFAULT NULL AFTER `origen`,
	ADD COLUMN `cuit` VARCHAR(13) NULL DEFAULT NULL AFTER `razon`,
	ADD COLUMN `factura_punto_venta` VARCHAR(6) NULL DEFAULT NULL AFTER `cuit`,
	ADD COLUMN `perc_iva` FLOAT NULL DEFAULT NULL AFTER `factura_punto_venta`,
	ADD COLUMN `perc_iibb_bsas` FLOAT NULL DEFAULT NULL AFTER `perc_iva`,
	ADD COLUMN `perc_iibb_caba` FLOAT NULL DEFAULT NULL AFTER `perc_iibb_bsas`,
	ADD COLUMN `exento` FLOAT NULL DEFAULT NULL AFTER `perc_iibb_caba`;


INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (7, 'Tipo de operacion');

ALTER TABLE `gasto`
	CHANGE COLUMN `factura_tipo` `factura_tipo` VARCHAR(6) NOT NULL AFTER `factura_nro`;


##################################02/07/2020###########################################################
INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Carga y asignacion de chequeras');

ALTER TABLE `cuenta`
	ADD COLUMN `emite_cheques` TINYINT NOT NULL DEFAULT '0' AFTER `controla_facturacion`;

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (4, 'Firma cheques');

CREATE TABLE `chequeras` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`cuenta_id` INT(11) NOT NULL,
	`numero` INT(11) NOT NULL,
	`tipo` VARCHAR(255) NOT NULL,
	`cantidad` INT(11) NOT NULL,
	`inicio` VARCHAR(255) NOT NULL,
	`final` VARCHAR(255) NOT NULL,
	`usuario_id` INT(11) NOT NULL,
	`estado` INT(11) NOT NULL,
	`ultimo` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `id` (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
;

CREATE TABLE `chequera_cheques` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`chequera_id` INT(11) NOT NULL,
	`numero` VARCHAR(255) NOT NULL,
	`estado` INT(11) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `id` (`id`),
	INDEX `chequera_id` (`chequera_id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB;


ALTER TABLE `rel_pago_operacion`
	ADD INDEX `forma_pago_id` (`forma_pago_id`);

ALTER TABLE `cheque_consumo`
	ADD INDEX `cuenta_id` (`cuenta_id`);



################################### 25/07/2020 ###########################################
UPDATE `permiso` SET `nombre`='Agregar extraviados y anulados' WHERE  `id`=49;
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('14', 'Reemplazar');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES ('14', 'Debitar y anular débito');

ALTER TABLE `cheque_consumo`
	CHANGE COLUMN `concepto` `concepto` VARCHAR(255) NULL DEFAULT NULL AFTER `debitado`;


ALTER TABLE `cheque_consumo`
	ADD COLUMN `chequera_id` INT(11) NULL AFTER `vencido`;

################################### 12/11/2020 ###########################################
ALTER TABLE `gasto`
	ADD COLUMN `quitar_egresos` INT(1) NOT NULL DEFAULT '0';

################################### 26/04/2021 ###########################################
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Base de datos');

################################### 15/06/2021 ###########################################
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (39, 'Operar');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (40, 'Operar');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (41, 'Operar');

INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Grilla de reservas');
UPDATE `permiso` SET `permiso_grupo_id`='42' WHERE  `id`=113;

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (42, 'Ocultar tarifas');

################################### 15/07/2021 ###########################################

ALTER TABLE `clientes`
	ADD COLUMN `tipoDocumento` ENUM('DNI','Pasaporte') NULL DEFAULT 'DNI' AFTER `nombre_apellido`;

ALTER TABLE `clientes`
	ADD COLUMN `tipoTelefono` ENUM('Fijo','Celular') NULL DEFAULT 'Celular' AFTER `dni`;

ALTER TABLE `clientes`
	ADD COLUMN `codPais` VARCHAR(10) NULL DEFAULT NULL AFTER `tipoTelefono`,
	ADD COLUMN `codArea` VARCHAR(10) NULL DEFAULT NULL AFTER `codPais`;

CREATE TABLE `pais_telefono` (
	`nombre` VARCHAR(50) NULL DEFAULT NULL,
	`name` VARCHAR(50) NULL DEFAULT NULL,
	`nom` VARCHAR(50) NULL DEFAULT NULL,
	`iso2` VARCHAR(50) NULL DEFAULT NULL,
	`iso3` VARCHAR(50) NULL DEFAULT NULL,
	`phone_code` VARCHAR(50) NULL DEFAULT NULL
)
COLLATE='utf8_general_ci'
;



ALTER TABLE `clientes`
	ADD COLUMN `razon_social` VARCHAR(250) NULL DEFAULT NULL AFTER `cuit`,
	ADD COLUMN `tipoPersona` ENUM('Fisica','Juridica') NULL DEFAULT 'Fisica' AFTER `razon_social`;

ALTER TABLE `clientes`
	ADD COLUMN `titular_factura` TINYINT(1) NULL DEFAULT '0' AFTER `razones_eligio`;

ALTER TABLE `clientes`
	ADD COLUMN `sexo` TINYINT(1) NULL DEFAULT '0' AFTER `titular_factura`;

CREATE TABLE `concepto_facturacions` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`nombre` VARCHAR(255) NOT NULL,
	`activo` TINYINT(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `id` (`id`)
)
COLLATE='latin1_swedish_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1;

INSERT INTO `permiso_grupo` (`nombre`) VALUES ('Concepto de facturacion');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (43, 'Operar');

ALTER TABLE `reserva_cobros`
	ADD COLUMN `concepto_facturacion_id` INT(11) NULL AFTER `cambio`;

################################### 10/11/2021 ###########################################
ALTER TABLE `reservas`
    ADD COLUMN `checkIn` INT(11) NOT NULL DEFAULT '0',
	ADD COLUMN `checkOut` INT(11) NOT NULL DEFAULT '0';

INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (42, 'Check In');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (42, 'Check Out');
