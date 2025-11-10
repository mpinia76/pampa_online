<?php
// functions/abm_secure.php

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

function guardar_usuario($tabla, $datos, $conn)
{
    $usuario_id = $datos['id'] ?? 0;
    $msg = null;

    // Determinar si insert o update
    if ($usuario_id > 0) {
        $msg = mysql_update($tabla, $datos, $usuario_id, $conn);
    } else {
        $msg = mysql_insert($tabla, $datos, $conn);
        $usuario_id = mysqli_insert_id($conn);
    }

    // Ahora guardamos relaciones: permisos, rubros, cajas, cuentas
    $relaciones = [
        'permisos' => 'usuario_permiso',
        'rubros' => 'usuario_rubro',
        'cajas' => 'usuario_caja',
        'cuentas' => 'usuario_cuenta'
    ];

    foreach ($relaciones as $campo_post => $tabla_rel) {
        if (isset($_POST[$campo_post])) {
            // Primero borramos lo existente si es update
            if ($usuario_id > 0) {
                $sql = "DELETE FROM $tabla_rel WHERE usuario_id=$usuario_id";
                mysqli_query($conn, $sql);
                //echo "<pre>DELETE REL: $sql</pre>";
            }

            // Insertamos los nuevos
            foreach ($_POST[$campo_post] as $valor) {
                $valor = intval($valor);
                $sql = "INSERT INTO $tabla_rel (usuario_id, {$campo_post}_id) VALUES ($usuario_id, $valor)";
                mysqli_query($conn, $sql);
                //echo "<pre>INSERT REL: $sql</pre>";
            }
        }
    }

    return $msg;
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
            $valores[] = ($valor === 'NOW()') ? $valor : "'" . mysqli_real_escape_string($conn, $valor) . "'";
        }
        $query = "INSERT INTO `$tabla` (" . implode(',', $columnas) . ") VALUES (" . implode(',', $valores) . ")";
        //echo "<pre>$query</pre>";
        mysqli_query($conn, $query);
        if (mysqli_errno($conn)) {
            return "Error al insertar en $tabla: " . mysqli_error($conn);
        }
        $insert_id = mysqli_insert_id($conn);

        if ($tabla === 'empleado' && isset($_POST['fecha_alta'])) {
            $alta = fechasql($_POST['fecha_alta']);
            $sql = "INSERT INTO empleado_historico (empleado_id, alta) VALUES ($insert_id, '$alta')";
            mysqli_query($conn, $sql);
            echo "<pre>$sql</pre>";
        }

        $msg = 1;
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

    $query = "UPDATE `$tabla` SET " . implode(',', $set) . " WHERE id=" . intval($id);
    //echo "<pre>$query</pre>";
    mysqli_query($conn, $query);

    if (mysqli_errno($conn)) {
        return "Error al actualizar en $tabla: " . mysqli_error($conn);
    }

    return 2;
}
?>
