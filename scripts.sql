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