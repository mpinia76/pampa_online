<?php
//DATOS DEL USUARIO
session_start();
$usuarioId = $_SESSION['userid'];
include_once("config/db.php");
include_once("functions/util.php");

// POST
$fecha = $_POST['fecha']; // formato dd/mm/yyyy
$conceptoId = intval($_POST['conceptoId']);
$monto = floatval($_POST['monto']);
$ids = explode(',', trim($_POST['ids'], ','));
$puntoVentaId = intval($_POST['puntoVenta']);


// Punto de venta
$sqlPV = "SELECT numero FROM punto_ventas WHERE id = $puntoVentaId";
$rsPV = mysqli_query($conn, $sqlPV);
if (!$rsPV || mysqli_num_rows($rsPV) == 0) {
    die(json_encode(['error' => 'Punto de venta no encontrado']));
}
$rowPV = mysqli_fetch_assoc($rsPV);
//$puntoVenta = str_pad($rowPV['numero'], 4, '0', STR_PAD_LEFT);
$puntoVenta='00001';

$resultados = [];

$nombreFile = 'factura_reserva_' . date('Ymd') . '_log';
$dt = date('Y-m-d G:i:s');
$logPath = "./logs/" . $nombreFile . ".log";

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

    // Datos del cliente
    $documento = preg_replace("/[^0-9]/", "", $res['cuit'] ?: $res['dni']);
    $titular = ($res['titular_factura'] == '0') ? $res['razon_social'] : $res['nombre_apellido'];
    $condicionIva = $res['iva'] ?? "RI";

    // Concepto de la factura
    $sqlConcepto = "SELECT nombre FROM concepto_facturacions WHERE id = $conceptoId";
    $rsC = mysqli_query($conn, $sqlConcepto);
    $cRow = mysqli_fetch_assoc($rsC);
    $conceptoNombre = $cRow['nombre'] ?? "Alquiler de Departamento";

    // Armar JSON para API v2 asincrónica
    $payload = [
        'usertoken' => USER_TOKEN,
        'apikey' => API_KEY,
        'apitoken' => API_TOKEN,
        'cliente' => [
            'documento_tipo' => 'CUIT',
            'documento_nro' => $documento,
            'razon_social' => $titular,
            'email' => $res['email'] ?? 'no-reply@empresa.com',
            'domicilio' => $res['direccion'] ?? '',
            'provincia' => $res['provincia_id'] ?? '2',
            'reclama_deuda' => 'N',
            'envia_por_mail' => 'N',
            'condicion_pago' => '211',
            'condicion_iva' => $condicionIva,
            'condicion_iva_operacion' => $condicionIva
        ],
        'comprobante' => [
            'fecha' => $fecha,
            'vencimiento' => $fecha,
            'tipo' => 'FACTURA C',
            'external_reference' => "RES-$idReserva",
            'operacion' => 'V',
            'punto_venta' => $puntoVenta,
            'numero' => '', // la API asigna
            'moneda' => 'PES',
            'cotizacion' => 1,
            'rubro' => $conceptoNombre,
            'rubro_grupo_contable' => $conceptoNombre,
            'detalle' => [
                [
                    'cantidad' => 1,
                    'producto' => [
                        'descripcion' => $conceptoNombre . " - Reserva #" . $res['numero'],
                        'unidad_bulto' => 1,
                        'lista_precios' => 'Lista API',
                        'codigo' => '0001',
                        'precio_unitario_sin_iva' => $monto,
                        'unidad_medida' => 7,
                        'actualiza_precio' => 'S',
                        'alicuota' => 0
                    ],
                    'afecta_stock' => 'S',
                    'bonificacion_porcentaje' => 0,
                    'leyenda' => ''
                ]
            ],
            'bonificacion' => "0.00",
            'leyenda_gral' => '',
            'total' => $monto,
            'pagos' => [
                'formas_pago' => [
                    ['descripcion' => 'Efectivo', 'importe' => $monto]
                ],
                'total' => $monto
            ],
            // ✅ Token del webhook
            'webhook' => [
                'token' => WEBHOOK_TOKEN
            ]
        ]
    ];

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


    $resultados[] = [
        'id' => $idReserva,
        'success' => empty($respData['error']),
        'raw_response' => $respData
    ];
}


header('Content-Type: application/json');
echo json_encode(['results' => $resultados]);
