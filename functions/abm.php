<?php
function validar($tabla, $campos, $conn)
{
    switch ($tabla) {
        case 'empleado':
            $dni = mysqli_real_escape_string($conn, $campos['dni']);
            $sql = "SELECT id, CONCAT(nombre,' ',apellido) as empleado, 
                    CASE WHEN estado = '0' THEN 'Inactivo' ELSE 'Activo' END as estado 
                    FROM $tabla WHERE dni = '$dni'";
            $rs = mysqli_fetch_assoc(mysqli_query($conn, $sql));
            if (!empty($rs['id'])) {
                return "El DNI ya se encuentra registrado para {$rs['empleado']} - {$rs['estado']}";
            }
            break;
        default:
            break;
    }
    return 0;
}

function mysql_insert($tabla, $campos, $conn)
{
    $msg = validar($tabla, $campos, $conn);
    if (!$msg) {
        $columnas = [];
        $valores = [];

        foreach ($campos as $campo => $valor) {
            $campo = str_replace("'", "`", $campo);
            $columnas[] = "`$campo`";

            if ($valor === 'NOW()') {
                $valores[] = $valor;
            } else {
                $valores[] = "'" . mysqli_real_escape_string($conn, $valor) . "'";
            }
        }

        $query = "INSERT INTO `$tabla` (" . implode(',', $columnas) . ") VALUES (" . implode(',', $valores) . ")";
        mysqli_query($conn, $query);

        if (mysqli_errno($conn)) {
            $msg = "Error al insertar en $tabla: " . mysqli_error($conn);
        } else {
            $insert_id = mysqli_insert_id($conn);

            if ($tabla === 'empleado' && isset($_POST['fecha_alta'])) {
                $alta = fechasql($_POST['fecha_alta']);
                $sql = "INSERT INTO empleado_historico (empleado_id, alta) VALUES ($insert_id, '$alta')";
                mysqli_query($conn, $sql);
            }

            $msg = 1;
        }
    }

    return $msg;
}

function mysql_update($tabla, $campos, $id, $conn)
{
    $set = [];
    foreach ($campos as $campo => $valor) {
        $campo = str_replace("'", "`", $campo);
        $valor = mysqli_real_escape_string($conn, $valor);
        $set[] = "`$campo` = '$valor'";
    }

    $query = "UPDATE `$tabla` SET " . implode(',', $set) . " WHERE id = '" . intval($id) . "'";
    mysqli_query($conn, $query);

    if (mysqli_errno($conn)) {
        $msg = "Error al actualizar en $tabla: " . mysqli_error($conn);
    } else {
        // Lógica especial para cheques
        if ($tabla == 'cheque_consumo' && isset($_POST['vencido']) && $_POST['vencido']) {
            $numero = str_pad($_POST['numero'], 8, '0', STR_PAD_LEFT);
            $cuenta_id = intval($_POST['cuenta_id']);

            $sql = "UPDATE chequera_cheques 
                    INNER JOIN chequeras ON chequera_cheques.chequera_id = chequeras.id
                    SET chequera_cheques.estado = 2 
                    WHERE chequeras.cuenta_id = '$cuenta_id' 
                    AND chequera_cheques.numero = '$numero'";
            mysqli_query($conn, $sql);

            if (mysqli_affected_rows($conn) > 0) {
                $sql = "SELECT chequeras.id FROM chequera_cheques 
                        INNER JOIN chequeras ON chequera_cheques.chequera_id = chequeras.id 
                        WHERE chequeras.cuenta_id = '$cuenta_id' 
                        AND chequera_cheques.numero = '$numero'";
                $rsTempChequera = mysqli_query($conn, $sql);
                $rsChequera = mysqli_fetch_assoc($rsTempChequera);

                if ($rsChequera) {
                    $sql = "SELECT COUNT(*) AS restantes 
                            FROM chequera_cheques 
                            WHERE chequera_id = '{$rsChequera['id']}' 
                            AND estado = '0'";
                    $rsCount = mysqli_fetch_assoc(mysqli_query($conn, $sql));
                    $estadoChequera = ($rsCount['restantes'] > 0) ? '1' : '3';

                    $sql = "UPDATE chequeras SET estado = $estadoChequera WHERE id = '{$rsChequera['id']}'";
                    mysqli_query($conn, $sql);
                }
            }
        }

        $msg = 2;
    }

    return $msg;
}
?>
