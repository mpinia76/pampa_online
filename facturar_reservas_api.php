<?php
//DATOS DEL USUARIO
session_start();
$usuarioId = $_SESSION['userid'];
include_once("config/db.php");
include_once("functions/util.php");
$nombreFile = 'factura_reserva_' . date('Ymd') . '_log';
$dt = date('Y-m-d G:i:s');
$logPath = "./logs/" . $nombreFile . ".log";
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



$conceptoGral='';
/*if($conceptoId){
    // Concepto de la factura
    $sqlConcepto = "SELECT nombre FROM concepto_facturacions WHERE id = $conceptoId";
    $rsC = mysqli_query($conn, $sqlConcepto);
    $cRow = mysqli_fetch_assoc($rsC);
    $conceptoGral    = $cRow['nombre'] ?? "";
}*/



foreach ($ids as $idReserva) {
    $idReserva = intval($idReserva);
    if (!$idReserva) continue;

    $sql = "SELECT R.*, C.nombre_apellido, C.cuit, C.dni, C.direccion, C.tipoPersona, C.razon_social, C.titular_factura, C.iva
            FROM reservas R
            JOIN clientes C ON R.cliente_id = C.id
            WHERE R.id = $idReserva";
    $rs = mysqli_query($conn, $sql);
    if (!$rs || mysqli_num_rows($rs) == 0) continue;

    $res = mysqli_fetch_assoc($rs);


// 🔹 Log del resultado de la consulta
    /*$logRes = $dt . " | Reserva ID: $idReserva | Resultado consulta CUIT: " . print_r($res, true) . "\n";
    file_put_contents($logPath, $logRes, FILE_APPEND);*/
    $detalle = '';
    if ($conceptoGral){
        $sql = "SELECT reserva_cobros.*, concepto_facturacions.nombre as concepto_facturacion FROM reserva_cobros LEFT JOIN concepto_facturacions ON reserva_cobros.concepto_facturacion_id = concepto_facturacions.id  WHERE fecha LIKE '".$_POST["ano"]."-".$_POST["mes"]."%' AND reserva_id = ". $idReserva." AND reserva_cobros.tipo <> 'DESCUENTO' ORDER BY reserva_cobros.id";

        $rsTempCobros = mysqli_query($conn,$sql);

        while($rsCobros = mysqli_fetch_array($rsTempCobros)){
            $detalle = $rsCobros['concepto_facturacion'];
        }
        $conceptoNombre=($detalle)?$detalle:'Alquiler de Departamento';
    }
    else{
        $conceptoNombre=$conceptoGral;
    }


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
            $facturaTipo = 'C';
            $documentoTipo = 'DNI';
            $documento = $res['dni'] ?? '99999999';
            break;
    }


// Condición de pago según tipo de comprobante
    $condicionPago = ($facturaTipo == 'A') ? '211' : '201';

// RG5329 se usa sólo en B/C
    $rg5329 = ($facturaTipo == 'A') ? 'N' : 'N';

    // 🔹 Calcular valores según tipo de comprobante
    if ($facturaTipo == 'A') {
        $alicuota = 21;
        $precio_unitario = $neto; // sin IVA
    } else {
        $alicuota = 0;
        $precio_unitario = $total; // total con IVA incluido
    }

    // Domicilio fiscal obligatorio
    $domicilioFiscal = !empty($res['direccion']) ? $res['direccion'] : 'Sin domicilio fiscal';

    // Armar JSON para API v2 asincrónica
    $payload = [
        'usertoken' => $tf['USER_TOKEN'],
        'apikey' => API_KEY,
        'apitoken' => API_TOKEN,
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
            'rubro'=>($conceptoNombre),
            'rubro_grupo_contable'=>($conceptoNombre),
            'detalle'=>[[
                'cantidad'=>1,
                'producto'=>[
                    'descripcion'=>($conceptoNombre) . " - Reserva #" . $res['numero'],
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
            'webhook'=>['token'=>WEBHOOK_TOKEN]
        ]
    ];


    // 🔹 Log del payload que se envía
    $logPayload = $dt . " | Enviando reserva ID: $idReserva | Payload: " . json_encode($payload, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logPath, $logPayload, FILE_APPEND);

    // Enviar request asincrónico
    $ch = curl_init(API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    $response = curl_exec($ch);
    curl_close($ch);

    $respData = json_decode($response, true);

    // Preparar datos para log
    $logMsg = $dt . " | Reserva ID: $idReserva | Cliente: " . $res['nombre_apellido'] .
        " | Response: " . json_encode($respData) . "\n";

    // Guardar log
    file_put_contents($logPath, $logMsg, FILE_APPEND);
// Guardar factura procesada solo si se envió bien
    if (empty($respData['error']) || $respData['error'] === 'N') {

        $cliente = mysqli_real_escape_string($conn, $res['nombre_apellido']);
        $dni = mysqli_real_escape_string($conn, trim($res['dni']));

        mysqli_query($conn, "DELETE FROM reserva_factura_procesada 
            WHERE reserva_id = ".$res['id']."
            AND cliente = '".$cliente."'
            AND dni = '".$dni."'
            AND total = '".$total."'"
                );

                $insert = "INSERT INTO reserva_factura_procesada 
            (reserva_id, fecha, cliente, dni, total, neto, diferencia, usuario_id) VALUES 
            (".$res['id'].",'".date('Y-m-d H:i:s')."','".$cliente."','".$dni."','".$total."','".$neto."','".($total-$neto)."','".($usuarioId)."')";

        mysqli_query($conn, $insert);

    }

    $resultados[] = [
        'id' => $idReserva,
        'success' => empty($respData['error']),
        'error' => $respData['error'] ?? null,
        'errores' => $respData['errores'] ?? [],
        'error_details' => $respData['error_details'] ?? [],
        'raw_response' => $respData // por si queremos ver todo después
    ];

}


header('Content-Type: application/json');
echo json_encode(['results' => $resultados]);
