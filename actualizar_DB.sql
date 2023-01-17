

ALTER TABLE `reserva_cobros`
	
	ADD INDEX `tipo` (`tipo`),
	
	ADD INDEX `finalizado` (`finalizado`);

ALTER TABLE `reserva_devoluciones`
	
	ADD INDEX `forma_pago` (`forma_pago`),
	
	ADD INDEX `motivo` (`motivo`);



ALTER TABLE `reservas`
	ADD INDEX `numero` (`numero`),
	ADD INDEX `reservado_por` (`reservado_por`),
	ADD INDEX `cliente_id` (`cliente_id`),
	ADD INDEX `apartamento_id` (`apartamento_id`),
	ADD INDEX `cargado_por` (`cargado_por`),
	ADD INDEX `estado` (`estado`);

ALTER TABLE cuenta

	ADD INDEX `sucursal` (`sucursal`),
	ADD INDEX `nombre` (`nombre`);

ALTER TABLE cuenta_tipo
	ADD INDEX `cuenta_tipo` (`cuenta_tipo`);

ALTER TABLE banco
	ADD INDEX `banco` (`banco`);

ALTER TABLE `empleado_adelanto`
	ADD INDEX `empleado_id` (`empleado_id`),
	ADD INDEX `creado_por` (`creado_por`),
	ADD INDEX `mes` (`mes`),
	ADD INDEX `ano` (`ano`);

ALTER TABLE `rel_pago_operacion`
	ADD INDEX `forma_pago` (`forma_pago`),
	ADD INDEX `forma_pago_id` (`forma_pago_id`),
	ADD INDEX `operacion_tipo` (`operacion_tipo`),
	ADD INDEX `operacion_id` (`operacion_id`);

ALTER TABLE `efectivo_consumo`
	ADD INDEX `caja_id` (`caja_id`),
	ADD INDEX `operacion_tipo` (`operacion_tipo`),
	ADD INDEX `operacion_id` (`operacion_id`);

ALTER TABLE `caja_movimiento`
	ADD INDEX `caja_id` (`caja_id`),
	ADD INDEX `origen` (`origen`),
	ADD INDEX `registro_id` (`registro_id`),
	ADD INDEX `usuario_id` (`usuario_id`);

ALTER TABLE `empleado_pago`
	ADD INDEX `empleado_id` (`empleado_id`),
	ADD INDEX `mes` (`mes`),
	ADD INDEX `ano` (`ano`),
	ADD INDEX `abonado_por` (`abonado_por`);

ALTER TABLE `cuenta_sincronizada`
	ADD INDEX `cuenta_id` (`cuenta_id`),
	ADD INDEX `usuario_id` (`usuario_id`);

ALTER TABLE `tarjeta_resumen`
	ADD INDEX `nombre` (`nombre`),
	ADD INDEX `tarjeta_id` (`tarjeta_id`),
	ADD INDEX `estado` (`estado`),
	ADD INDEX `ano` (`ano`),
	ADD INDEX `mes` (`mes`);

ALTER TABLE `caja`
	ADD INDEX `caja` (`caja`),
	ADD INDEX `visible_en_informe` (`visible_en_informe`);

ALTER TABLE `empleado`
	ADD INDEX `nombre` (`nombre`),
	ADD INDEX `apellido` (`apellido`),
	ADD INDEX `dni` (`dni`),
	ADD INDEX `cuil` (`cuil`),
	ADD INDEX `email` (`email`),
	ADD INDEX `nro_legajo` (`nro_legajo`),
	ADD INDEX `creado_por` (`creado_por`),
	ADD INDEX `baja_por` (`baja_por`),
	ADD INDEX `estado` (`estado`);

ALTER TABLE `cobro_transferencias`
	ADD INDEX `cuenta_id` (`cuenta_id`),
	ADD INDEX `quien_transfiere` (`quien_transfiere`),
	ADD INDEX `numero_operacion` (`numero_operacion`),
	ADD INDEX `reserva_cobro_id` (`reserva_cobro_id`),
	ADD INDEX `acreditado` (`acreditado`),
	ADD INDEX `acreditado_por` (`acreditado_por`);

ALTER TABLE `cobro_tarjeta_lotes`
	ADD INDEX `cobro_tarjeta_tipo_id` (`cobro_tarjeta_tipo_id`),
	ADD INDEX `numero` (`numero`),
	ADD INDEX `cerrado_por` (`cerrado_por`),
	ADD INDEX `acreditado_por` (`acreditado_por`);

ALTER TABLE `cobro_tarjeta_tipos`
	ADD INDEX `cobro_tarjeta_posnet_id` (`cobro_tarjeta_posnet_id`),
	ADD INDEX `cuenta_id` (`cuenta_id`),
	ADD INDEX `marca` (`marca`);

ALTER TABLE `cobro_tarjeta_posnets`
	ADD INDEX `posnet` (`posnet`);

ALTER TABLE `cuenta_movimiento`
	ADD INDEX `cuenta_id` (`cuenta_id`),
	ADD INDEX `origen` (`origen`),
	ADD INDEX `registro_id` (`registro_id`),
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `usuario_id` (`usuario_id`);

ALTER TABLE `cheque_consumo`
	ADD INDEX `numero` (`numero`),
	ADD INDEX `titular` (`titular`),
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `fecha_debitado` (`fecha_debitado`),
	ADD INDEX `cuenta_id` (`cuenta_id`),
	ADD INDEX `debitado` (`debitado`),
	ADD INDEX `concepto` (`concepto`),
	ADD INDEX `creado_por` (`creado_por`),
	ADD INDEX `debitado_por` (`debitado_por`),
	ADD INDEX `vencido` (`vencido`);

ALTER TABLE `ano`
	ADD INDEX `ano` (`ano`);

ALTER TABLE `apartamentos`
	ADD INDEX `apartamento` (`apartamento`),
	ADD INDEX `capacidad` (`capacidad`);

ALTER TABLE `caja_movimiento`
	ADD INDEX `fecha` (`fecha`);

ALTER TABLE `caja_sincronizada`
	ADD INDEX `fecha` (`fecha`);

ALTER TABLE `clientes`
	ADD INDEX `nombre_apellido` (`nombre_apellido`),
	ADD INDEX `dni` (`dni`),
	ADD INDEX `email` (`email`),
	ADD INDEX `cuit` (`cuit`);

ALTER TABLE `cobro_cheques`
	ADD INDEX `numero` (`numero`),
	ADD INDEX `banco` (`banco`),
	ADD INDEX `librado_por` (`librado_por`),
	ADD INDEX `tipo` (`tipo`),
	ADD INDEX `fecha_cobro` (`fecha_cobro`),
	ADD INDEX `cuit` (`cuit`),
	ADD INDEX `a_la_orden_de` (`a_la_orden_de`),
	ADD INDEX `reserva_cobro_id` (`reserva_cobro_id`),
	ADD INDEX `acreditado` (`acreditado`),
	ADD INDEX `fecha_acreditado` (`fecha_acreditado`),
	ADD INDEX `acreditado_por` (`acreditado_por`),
	ADD INDEX `asociado_a_pagos` (`asociado_a_pagos`),
	ADD INDEX `cuenta_acreditado` (`cuenta_acreditado`),
	ADD INDEX `asociado_a_pagos_fecha` (`asociado_a_pagos_fecha`);

ALTER TABLE `cobro_tarjetas`
	ADD INDEX `cobro_tarjeta_tipo_id` (`cobro_tarjeta_tipo_id`),
	ADD INDEX `tarjeta_numero` (`tarjeta_numero`),
	ADD INDEX `cuotas` (`cuotas`),
	ADD INDEX `reserva_cobro_id` (`reserva_cobro_id`),
	ADD INDEX `cobro_tarjeta_lote_id` (`cobro_tarjeta_lote_id`),
	ADD INDEX `titular` (`titular`),
	ADD INDEX `dni` (`dni`);

ALTER TABLE `cobro_tarjeta_cuotas`
	ADD INDEX `cobro_tarjeta_tipo_id` (`cobro_tarjeta_tipo_id`),
	ADD INDEX `cuota` (`cuota`);

ALTER TABLE `cobro_tarjeta_lotes`
	ADD INDEX `fecha_cierre` (`fecha_cierre`),
	ADD INDEX `fecha_acreditacion` (`fecha_acreditacion`);

ALTER TABLE `compra`
	ADD INDEX `nro_orden` (`nro_orden`),
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `fecha_vencimiento` (`fecha_vencimiento`),
	ADD INDEX `rubro_id` (`rubro_id`),
	ADD INDEX `subrubro_id` (`subrubro_id`),
	ADD INDEX `proveedor` (`proveedor`),
	ADD INDEX `factura_nro` (`factura_nro`),
	ADD INDEX `factura_tipo` (`factura_tipo`),
	ADD INDEX `factura_orden` (`factura_orden`),
	ADD INDEX `user_id` (`user_id`),
	ADD INDEX `estado` (`estado`),
	ADD INDEX `created` (`created`),
	ADD INDEX `remito_nro` (`remito_nro`),
	ADD INDEX `recibo_nro` (`recibo_nro`);

ALTER TABLE `cuenta_a_pagar`
	ADD INDEX `operacion_tipo` (`operacion_tipo`),
	ADD INDEX `operacion_id` (`operacion_id`),
	ADD INDEX `fecha_pago` (`fecha_pago`),
	ADD INDEX `estado` (`estado`);

ALTER TABLE `cuenta_sincronizada`
	ADD INDEX `fecha` (`fecha`);

ALTER TABLE `efectivo_consumo`
	ADD INDEX `fecha` (`fecha`);

ALTER TABLE `empleado_adelanto`
	ADD INDEX `creado` (`creado`);

ALTER TABLE `empleado_hora_extra`
	ADD INDEX `empleado_id` (`empleado_id`),
	ADD INDEX `hora_extra_id` (`hora_extra_id`),
	ADD INDEX `mes` (`mes`),
	ADD INDEX `ano` (`ano`),
	ADD INDEX `creado_por` (`creado_por`),
	ADD INDEX `creado` (`creado`),
	ADD INDEX `estado` (`estado`),
	ADD INDEX `aprobado_por` (`aprobado_por`),
	ADD INDEX `aprobado` (`aprobado`);

ALTER TABLE `empleado_pago`
	ADD INDEX `abonado` (`abonado`);

ALTER TABLE `empleado_sueldo`
	ADD INDEX `creado` (`creado`),
	ADD INDEX `empleado_id` (`empleado_id`),
	ADD INDEX `ano` (`ano`),
	ADD INDEX `mes` (`mes`),
	ADD INDEX `creado_por` (`creado_por`),
	ADD INDEX `sueldo_id` (`sueldo_id`);

ALTER TABLE `empleado_trabajo`
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `empleado_id` (`empleado_id`),
	ADD INDEX `espacio_trabajo_id` (`espacio_trabajo_id`),
	ADD INDEX `sector_1_id` (`sector_1_id`),
	ADD INDEX `sector_2_id` (`sector_2_id`),
	ADD INDEX `creado_por` (`creado_por`);

ALTER TABLE `espacio_trabajo`
	ADD INDEX `espacio` (`espacio`);

ALTER TABLE `extras`
	ADD INDEX `extra_rubro_id` (`extra_rubro_id`),
	ADD INDEX `extra_subrubro_id` (`extra_subrubro_id`),
	ADD INDEX `detalle` (`detalle`),
	ADD INDEX `activo` (`activo`),
	ADD INDEX `extra_variable_id` (`extra_variable_id`);

ALTER TABLE `extra_rubros`
	ADD INDEX `rubro` (`rubro`),
	ADD INDEX `extra_variables` (`extra_variables`);

ALTER TABLE `extra_subrubros`
	ADD INDEX `extra_rubro_id` (`extra_rubro_id`),
	ADD INDEX `subrubro` (`subrubro`);

ALTER TABLE `extra_variables`
	ADD INDEX `extra_rubro_id` (`extra_rubro_id`),
	ADD INDEX `detalle` (`detalle`);

ALTER TABLE `forma_pago`
	ADD INDEX `forma_pago` (`forma_pago`);

ALTER TABLE `gasto`
	ADD INDEX `nro_orden` (`nro_orden`),
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `fecha_vencimiento` (`fecha_vencimiento`),
	ADD INDEX `rubro_id` (`rubro_id`),
	ADD INDEX `subrubro_id` (`subrubro_id`),
	ADD INDEX `proveedor` (`proveedor`),
	ADD INDEX `factura_nro` (`factura_nro`),
	ADD INDEX `factura_tipo` (`factura_tipo`),
	ADD INDEX `factura_orden` (`factura_orden`),
	ADD INDEX `user_id` (`user_id`),
	ADD INDEX `estado` (`estado`),
	ADD INDEX `created` (`created`),
	ADD INDEX `remito_nro` (`remito_nro`),
	ADD INDEX `recibo_nro` (`recibo_nro`);

ALTER TABLE `mes`
	ADD INDEX `mes` (`mes`);

ALTER TABLE `motivo`
	ADD INDEX `nombre` (`nombre`);

ALTER TABLE `motivo_grupo`
	ADD INDEX `grupo` (`grupo`);

ALTER TABLE `permiso`
	ADD INDEX `permiso_grupo_id` (`permiso_grupo_id`),
	ADD INDEX `nombre` (`nombre`);

ALTER TABLE `permiso_grupo`
	ADD INDEX `nombre` (`nombre`);

ALTER TABLE `proveedor`
	ADD INDEX `nombre` (`nombre`),
	ADD INDEX `rubro_id` (`rubro_id`),
	ADD INDEX `cliente_nro` (`cliente_nro`),
	ADD INDEX `cuit` (`cuit`),
	ADD INDEX `email` (`email`);

ALTER TABLE `reservas`
	ADD INDEX `check_in` (`check_in`),
	ADD INDEX `check_out` (`check_out`),
	ADD INDEX `creado` (`creado`),
	ADD INDEX `actualizado` (`actualizado`);

ALTER TABLE `reserva_cobros`
	ADD INDEX `fecha` (`fecha`);

ALTER TABLE `reserva_descuentos`
	ADD INDEX `motivo` (`motivo`),
	ADD INDEX `reserva_cobro_id` (`reserva_cobro_id`);

ALTER TABLE `reserva_devoluciones`
	ADD INDEX `fecha` (`fecha`);

ALTER TABLE `reserva_extras`
	ADD INDEX `cantidad` (`cantidad`),
	ADD INDEX `reserva_id` (`reserva_id`),
	ADD INDEX `extra_id` (`extra_id`),
	ADD INDEX `agregada` (`agregada`),
	ADD INDEX `extra_variable_id` (`extra_variable_id`);

ALTER TABLE `reserva_facturas`
	ADD INDEX `tipo` (`tipo`),
	ADD INDEX `titular` (`titular`),
	ADD INDEX `fecha_emision` (`fecha_emision`),
	ADD INDEX `numero` (`numero`),
	ADD INDEX `reserva_id` (`reserva_id`),
	ADD INDEX `agregada_por` (`agregada_por`);

ALTER TABLE `rubro`
	ADD INDEX `rubro` (`rubro`);

ALTER TABLE `sector`
	ADD INDEX `sector` (`sector`),
	ADD INDEX `hora_extra_activa` (`hora_extra_activa`);

ALTER TABLE `sector_horas_extras`
	ADD INDEX `creado` (`creado`),
	ADD INDEX `sector_id` (`sector_id`),
	ADD INDEX `creado_por` (`creado_por`);

ALTER TABLE `subrubro`
	ADD INDEX `rubro_id` (`rubro_id`),
	ADD INDEX `subrubro` (`subrubro`);

ALTER TABLE `tarjeta`
	ADD INDEX `banco_id` (`banco_id`),
	ADD INDEX `tarjeta_marca_id` (`tarjeta_marca_id`),
	ADD INDEX `titular` (`titular`);

ALTER TABLE `tarjeta_consumo`
	ADD INDEX `tarjeta_id` (`tarjeta_id`),
	ADD INDEX `operacion_tipo` (`operacion_tipo`),
	ADD INDEX `operacion_id` (`operacion_id`),
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `comprobante_nro` (`comprobante_nro`);

ALTER TABLE `tarjeta_consumo_cuota`
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `tarjeta_consumo_id` (`tarjeta_consumo_id`),
	ADD INDEX `nro_cuota` (`nro_cuota`);

ALTER TABLE `tarjeta_marca`
	ADD INDEX `marca` (`marca`);

ALTER TABLE `tarjeta_movimiento`
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `tarjeta_resumen_id` (`tarjeta_resumen_id`),
	ADD INDEX `detalle` (`detalle`);

ALTER TABLE `tarjeta_resumen`
	ADD INDEX `inicio` (`inicio`),
	ADD INDEX `vencimiento` (`vencimiento`),
	ADD INDEX `fecha_pago` (`fecha_pago`);

ALTER TABLE `transferencia_consumo`
	ADD INDEX `cuenta_id` (`cuenta_id`),
	ADD INDEX `operacion_tipo` (`operacion_tipo`),
	ADD INDEX `operacion_id` (`operacion_id`),
	ADD INDEX `fecha` (`fecha`),
	ADD INDEX `debitado` (`debitado`),
	ADD INDEX `fecha_debitada` (`fecha_debitada`);

ALTER TABLE `usuario`
	ADD INDEX `nombre` (`nombre`),
	ADD INDEX `apellido` (`apellido`),
	ADD INDEX `email` (`email`),
	ADD INDEX `admin` (`admin`),
	ADD INDEX `espacio_trabajo_id` (`espacio_trabajo_id`);

ALTER TABLE `usuario_caja`
	ADD INDEX `usuario_id` (`usuario_id`),
	ADD INDEX `caja_id` (`caja_id`);

ALTER TABLE `usuario_cuenta`
	ADD INDEX `usuario_id` (`usuario_id`),
	ADD INDEX `cuenta_id` (`cuenta_id`);
	
##################################################11/04/2016#########################################
CREATE TABLE `documento` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`path` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `path` (`path`)
)
COLLATE='latin1_swedish_ci'
ENGINE=MyISAM;

CREATE TABLE `empleado_historico` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`empleado_id` INT(11) NOT NULL,
	`alta` DATE NOT NULL,
	`baja` DATE DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `empleado_id` (`empleado_id`),
	INDEX `alta` (`alta`),
	INDEX `baja` (`baja`)
)
COLLATE='utf8_unicode_ci'
ENGINE=MyISAM;

INSERT INTO empleado_historico (empleado_id, alta, baja)
SELECT id, fecha_alta, fecha_baja FROM empleado; 


ALTER TABLE `empleado_pago`
	ADD COLUMN `descuentos` DECIMAL(10,2) NULL AFTER `abonado`,
	ADD COLUMN `motivo_descuentos` VARCHAR(255) NULL AFTER `descuentos`;

##############################################21/04/2016#######################################################
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (11, 'Carga de documentacion en sistema');

##############################################11/05/2016#######################################################
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (7, 'Administrador de gastos');

##############################################23/05/2016#######################################################
ALTER TABLE `cuenta_a_pagar`
	CHANGE COLUMN `fecha_pago` `fecha_pago` DATE NULL AFTER `monto`;
	
##############################################05/08/2016#######################################################
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (20, 'Borrado de cobros');
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (19, 'Pagar meses atrasados');

##############################################10/10/2016#######################################################
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (21, 'Borrado de cobros');

##################################23/10/2017###########################################################
ALTER TABLE `cobro_tarjetas`
	ADD COLUMN `liquidacion` VARCHAR(8) NULL AFTER `lote`;
	
##################################26/04/2018###########################################################
ALTER TABLE `gasto`
	ADD COLUMN `iva_27` FLOAT NULL,
	ADD COLUMN `iva_21` FLOAT NULL,
	ADD COLUMN `iva_10_5` FLOAT NULL,
	ADD COLUMN `otra_alicuota` FLOAT NULL;
	
INSERT INTO `permiso` (`permiso_grupo_id`, `nombre`) VALUES (10, 'Libro IVA compras');

##################################22/08/2018###########################################################
ALTER TABLE `reservas`
	CHANGE COLUMN `reservado_por` `reservado_por` INT(11) NULL AFTER `numero`;

ALTER TABLE `clientes`
	CHANGE COLUMN `dni` `dni` VARCHAR(8) NULL COLLATE 'utf8_unicode_ci' AFTER `nombre_apellido`,
	CHANGE COLUMN `telefono` `telefono` VARCHAR(20) NULL COLLATE 'utf8_unicode_ci' AFTER `dni`,
	CHANGE COLUMN `celular` `celular` VARCHAR(20) NULL COLLATE 'utf8_unicode_ci' AFTER `telefono`,
	CHANGE COLUMN `direccion` `direccion` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `celular`,
	CHANGE COLUMN `localidad` `localidad` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `direccion`,
	CHANGE COLUMN `email` `email` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `localidad`,
	CHANGE COLUMN `nacimiento` `nacimiento` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `email`,
	CHANGE COLUMN `profesion` `profesion` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `nacimiento`,
	CHANGE COLUMN `fumador` `fumador` VARCHAR(2) NULL COLLATE 'utf8_unicode_ci' AFTER `profesion`,
	CHANGE COLUMN `1er_contacto` `1er_contacto` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `fumador`,
	CHANGE COLUMN `razones_eligio` `razones_eligio` VARCHAR(250) NULL COLLATE 'utf8_unicode_ci' AFTER `1er_contacto`,
	CHANGE COLUMN `iva` `iva` VARCHAR(150) NULL COLLATE 'utf8_unicode_ci' AFTER `razones_eligio`,
	CHANGE COLUMN `cuit` `cuit` VARCHAR(100) NULL COLLATE 'utf8_unicode_ci' AFTER `iva`;

##################################08/09/2018###########################################################	
ALTER TABLE `reservas`
	ADD COLUMN `hora_check_in` TIME NULL DEFAULT '15:00' AFTER `check_in`;
	
UPDATE reservas SET hora_check_in = '15:00';

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

##################################19/03/2020###########################################################	
UPDATE `permiso` SET `nombre`='Borrado de cobros y devoluciones' WHERE  `id`=101;

##################################26/03/2020###########################################################
ALTER TABLE `punto_ventas`
	ADD COLUMN `alicuota` DECIMAL(10,2) NULL DEFAULT NULL AFTER `ivaVentas`;
	
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
	
ALTER TABLE `cheque_consumo`
	ADD COLUMN `chquera_id` INT(11) NULL AFTER `vencido`;


