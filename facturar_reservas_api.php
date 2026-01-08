<?php
//DATOS DEL USUARIO
session_start();
$usuarioId = $_SESSION['userid'];
include_once("config/db.php");
include_once("functions/util.php");
$nombreFile = 'factura_reserva_' . date('Ymd') . '_log';
$dt = date('Y-m-d G:i:s');
$logPath = "./logs/" . $nombreFile . ".log";
$logRes = $dt . " | Session: " . print_r($_SESSION, true) . "\n";
    file_put_contents($logPath, $logRes, FILE_APPEND);
// POST
$fecha = $_POST['fecha']; // formato dd/mm/yyyy
// Normalizar formato a dd/mm/yyyy
if (strpos($fecha, '-') !== false) {
    // viene como yyyy-mm-dd → convertir
    $fechaParts = explode('-', $fecha);
    if (count($fechaParts) === 3) {
        $fecha = $fechaParts[2] . '/' . $fechaParts[1] . '/' . $fechaParts[0];
    }
}

// 🔹 Validar fecha AFIP (máx. 10 días hacia atrás, no futura)
$fechaObj = DateTime::createFromFormat('d/m/Y', $fecha);
$hoy = new DateTime();

if (!$fechaObj) {
    die(json_encode(['error' => 'Ingrese una fecha válida.']));
}

if ($fechaObj > $hoy) {
    die(json_encode(['error' => 'La fecha de facturación no puede ser futura.']));
}

$diffDias = $hoy->diff($fechaObj)->days;
if ($fechaObj < (clone $hoy)->modify('-10 days')) {
    die(json_encode(['error' => 'AFIP no permite facturar servicios con más de 10 días de antigüedad.']));
}



//$conceptoId = intval($_POST['conceptoId']);
$monto = floatval($_POST['monto']);
$ids = explode(',', trim($_POST['ids'], ','));
$puntoVentaId = intval($_POST['puntoVenta']);
$columnaTransfiere = $_POST['columnaTransfiere'] ?? 0;
$columnaTC = $_POST['columnaTC'] ?? 0;
$columnaCheques = $_POST['columnaCheques'] ?? 0;
$conceptosPost = $_POST['conceptos'] ?? [];

// Punto de venta
$sqlPV = "SELECT numero, alicuota FROM punto_ventas WHERE id = $puntoVentaId";
$rsPV = mysqli_query($conn, $sqlPV);
if (!$rsPV || mysqli_num_rows($rsPV) == 0) {
    die(json_encode(['error' => 'Punto de venta no encontrado']));
}
$rowPV = mysqli_fetch_assoc($rsPV);
/*$puntoVenta = str_pad($rowPV['numero'], 5, '0', STR_PAD_LEFT);*/
$ivaCoeficiente = ($rowPV['alicuota']) ? 1 + $rowPV['alicuota'] : 1;
if (!isset($tusfacturas_tokens[$puntoVentaId])) {
    die(json_encode(['error' => "Punto de venta no configurado ($puntoVentaId)"]));
}
$tf = $tusfacturas_tokens[$puntoVentaId];
$puntoVenta = $tf['NUMERO'];
//$ivaCoeficiente=1;
//print_r($conceptosPost);

$conceptoNombre = null;
$idReserva = null;

foreach ($conceptosPost as $idCobro => $conceptoId) {

    $idCobro = (int)$idCobro;
    $conceptoId = (int)$conceptoId;

    $sql = "SELECT rc.reserva_id, cf.nombre
            FROM reserva_cobros rc
            JOIN concepto_facturacions cf ON cf.id = $conceptoId
            WHERE rc.id = $idCobro
              AND rc.tipo <> 'DESCUENTO'";



    $rs = mysqli_query($conn, $sql);
    if (!$rs || mysqli_num_rows($rs) === 0) {
        die(json_encode(['error' => "Cobro inválido ($idCobro)"]));
    }

    $row = mysqli_fetch_assoc($rs);

    // ✅ ACÁ
    $idReserva = (int)$row['reserva_id'];
    if (!$idReserva) {
        die(json_encode(['error' => "Cobro $idCobro sin reserva asociada"]));
    }

    // 🟢 Guardar el nombre del concepto
    $conceptoNombre = $row['nombre'];

    $sql = "SELECT R.*, C.nombre_apellido, C.cuit, C.dni, C.direccion, C.tipoPersona, C.razon_social, C.titular_factura, C.iva
            FROM reservas R
            JOIN clientes C ON R.cliente_id = C.id
            WHERE R.id = $idReserva";
    $rs = mysqli_query($conn, $sql);
    if (!$rs || mysqli_num_rows($rs) == 0) continue;

    $res = mysqli_fetch_assoc($rs);





    // ---------------------
    // CÁLCULO DE MONTOS
    // ---------------------
    $transferencias = $tarjetas = $cheques = 0;

    $sqlCobros = "SELECT * FROM reserva_cobros WHERE reserva_id = $idReserva";
    $rsCobros = mysqli_query($conn, $sqlCobros);
    while ($cobro = mysqli_fetch_assoc($rsCobros)) {
        if ($cobro['tipo'] == "DESCUENTO") continue;

        // Transferencias
        if ($columnaTransfiere == 1) {
            $sqlTrans = "SELECT * FROM cobro_transferencias INNER JOIN cuenta ON cobro_transferencias.cuenta_id = cuenta.id  
                         WHERE reserva_cobro_id = {$cobro['id']} AND cuenta.controla_facturacion = 1 AND cobro_transferencias.acreditado = 1";
            $rsTrans = mysqli_query($conn, $sqlTrans);
            while ($t = mysqli_fetch_assoc($rsTrans)) {
                $transferencias += $t['monto_neto'] + $t['intereses'];
            }
        }

        // Tarjetas
        if ($columnaTC == 1) {
            $sqlTC = "SELECT * FROM cobro_tarjetas WHERE reserva_cobro_id = {$cobro['id']}";
            $rsTC = mysqli_query($conn, $sqlTC);
            while ($t = mysqli_fetch_assoc($rsTC)) {
                $tarjetas += $t['monto_neto'] + $t['intereses'];
            }
        }

        // Cheques
        if ($columnaCheques == 1) {
            $sqlCheques = "SELECT * FROM cobro_cheques LEFT JOIN cuenta ON cobro_cheques.cuenta_acreditado = cuenta.id  
                           WHERE reserva_cobro_id = {$cobro['id']} AND ((acreditado = 1 AND cuenta.controla_facturacion = 1) OR (cobro_cheques.cuenta_acreditado=0))";
            $rsCheques = mysqli_query($conn, $sqlCheques);
            while ($ch = mysqli_fetch_assoc($rsCheques)) {
                $cheques += $ch['monto_neto'];
            }
        }
    }

    // Facturas previas
    $sqlFact = "SELECT * FROM reserva_facturas WHERE reserva_id = $idReserva";
    $rsFact = mysqli_query($conn, $sqlFact);
    $facturas = 0;
    while ($f = mysqli_fetch_assoc($rsFact)) {
        $facturas += $f['monto'];
    }

    // Total final
    $total = $transferencias + $tarjetas + $cheques - $facturas;
    $neto = $total / $ivaCoeficiente;
    $iva = $total - $neto;

    // ---------------------
// Datos del cliente
// ---------------------

// Determinar titular o razón social
    // Titular o razón social
    $titular = '';
    if ($res['titular_factura'] == '0' && !empty(trim($res['razon_social']))) {
        $titular = trim($res['razon_social']);
    } else {
        $titular = trim($res['nombre_apellido']);
    }
    if (empty($titular)) {
        $titular = 'Consumidor Final';
    }

// 🔹 Mapeo de IVA de la DB a la API
    $mapIvaApi = [
        'Responsable Inscripto' => 'RI',
        'Exento' => 'EX',
        'Monotributo' => 'MT'
    ];
    $condicionIvaDb = $res['iva'] ?? '';
    $condicionIvaApi = $mapIvaApi[$condicionIvaDb] ?? 'CF'; // CF = Consumidor Final


    if ($rowPV['alicuota'] == 0) {
        $condicionIvaApi = 'EX';
    }


// 🔹 Tipo y número de documento
    $documentoTipo = '';
    $documento = '';
    switch ($condicionIvaApi) {
        case 'RI': // Responsable Inscripto → Factura A
            $facturaTipo = 'A';
            if (!empty($res['cuit']) && preg_match('/^[0-9]{11}$/', $res['cuit'])) {
                $documentoTipo = 'CUIT';
                $documento = $res['cuit'];
            } else {
                // CUIT genérico para RI si no hay cargado
                $documentoTipo = 'CUIT';
                $documento = '20251748056';
            }
            break;
        case 'CF': // Consumidor Final → Factura B
        case 'MT': // Monotributo → Factura B
        case 'EX': // Exento → Factura B
            $facturaTipo = 'B';
            if (!empty($res['cuit']) && preg_match('/^[0-9]{11}$/', $res['cuit'])) {
                $documentoTipo = 'CUIT';
                $documento = $res['cuit'];
            } else {
                $documentoTipo = 'DNI';
                $documento = $res['dni'] ?? '99999999';
            }
            break;

        default: // Consumidor Final → Factura C
            $facturaTipo = 'B';
            $documentoTipo = 'DNI';
            $documento = $res['dni'] ?? '99999999';
            break;
    }


// Condición de pago según tipo de comprobante
    $condicionPago = ($facturaTipo == 'A') ? '211' : '201';

// RG5329 se usa sólo en B/C
    $rg5329 = ($facturaTipo == 'A') ? 'N' : 'N';

    $alicuota = $rowPV['alicuota'] * 100; // ejemplo: 0.21 → 21
    $precio_unitario = $neto; // sin IVA

    /*if ($facturaTipo == 'A') {
        // Factura A → separar IVA
        $alicuota = $rowPV['alicuota'] * 100; // ejemplo: 0.21 → 21
        $precio_unitario = $neto; // sin IVA
    } else {
        // Factura B → precio final, pero con la alicuota correcta
        $alicuota = $rowPV['alicuota'] * 100; // ejemplo: 21
        $precio_unitario = $total; // total con IVA incluido
    }*/


    // Domicilio fiscal obligatorio
    $domicilioFiscal = !empty($res['direccion']) ? $res['direccion'] : 'Sin domicilio fiscal';

    // Armar JSON para API v2 asincrónica
    $payload = [
        'usertoken' => $tf['USER_TOKEN'],
        'apikey' => $tf['API_KEY'],
        'apitoken' => $tf['API_TOKEN'],
        'cliente' => [
            'documento_tipo' => $documentoTipo,
            'documento_nro' => $documento,
            'razon_social' => ($titular),
            'email' => $res['email'] ?? 'no-reply@empresa.com',
            'domicilio' => ($domicilioFiscal),
            'provincia' => $res['provincia_id'] ?? '2',
            'envia_por_mail' => 'N',
            'reclama_deuda' => 'N',
            'condicion_pago' => $condicionPago,
            'condicion_iva' => $condicionIvaApi,
            'condicion_iva_operacion' => $condicionIvaApi,
            'rg5329' => $rg5329
        ],
        'comprobante'=>[
            'fecha'=>$fecha,
            'vencimiento'=>$fecha,
            'tipo'=>'FACTURA '.$facturaTipo,
            'external_reference'=>"RES-$idReserva",
            'tags'=>["reserva","web"],
            'datos_informativos'=>[
                'paga_misma_moneda'=>'N'
            ],
            'operacion'=>'V',
            'punto_venta'=>$puntoVenta,
            'numero'=>'',
            'moneda'=>'PES',
            'cotizacion'=>1,
            'periodo_facturado_desde'=>$fecha,
            'periodo_facturado_hasta'=>$fecha,
            'rubro'=>$conceptoNombre,
            'rubro_grupo_contable'=>$conceptoNombre,
            'detalle'=>[[
                'cantidad'=>1,
                'producto'=>[
                    'descripcion'=>$conceptoNombre,
                    'codigo'=>'0001',
                    'lista_precios'=>'Lista API',
                    'unidad_bulto'=>1,
                    'alicuota' => $alicuota,
                    'unidad_medida' => 7,
                    'actualiza_precio' => 'S',
                    'rg5329' => $rg5329,
                    'precio_unitario_sin_iva' => $precio_unitario
                ],
                'afecta_stock'=>'S',
                'bonificacion_porcentaje'=>0,
                'leyenda'=>''
            ]],
            'bonificacion'=>"0.00",
            'leyenda_gral'=>'',
            'tributos'=>[],
            'impuestos_internos'=>'0',
            'impuestos_internos_base'=>'0',
            'impuestos_internos_alicuota'=>'0',
            'total'=>$total,
            'pagos'=>[
                'formas_pago'=>[['descripcion'=>'Contado','importe'=>$total]],
                'total'=>$total
            ],
            'webhook'=>['token'=>$tf['WEBHOOK_TOKEN']]
        ]
    ];


    // 🔹 Log del payload que se envía
    $logPayload = $dt . " | Enviando reserva ID: $idReserva | Payload: " . json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logPath, $logPayload, FILE_APPEND);

    // Enviar request asincrónico
    // ==========================
// DRY RUN – SIMULACIÓN
// ==========================
    $respData = [
        'dry_run' => true,
        'error' => 'N',
        'msg' => 'Simulación OK'
    ];

// Log mínimo
    $logMsg = $dt . " | DRY RUN | Reserva ID: $idReserva | Cliente: " . $res['nombre_apellido'] .
        " | Total: $total\n";
    file_put_contents($logPath, $logMsg, FILE_APPEND);

// Resultado simulado
    $resultados[] = [
        'id' => $idReserva,
        'success' => true,
        'error' => null,
        'errores' => [],
        'error_details' => [],
        'raw_response' => $respData
    ];

}


header('Content-Type: application/json');
echo json_encode(['results' => $resultados]);
