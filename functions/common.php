<?php
$dataid = $_GET['dataid'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datos = [];
    foreach ($campos as $key => $atr) {
        if (($atr['type'] ?? '') === 'date' || ($atr[0] ?? '') === 'date') {
            $datos[$key] = fechasql($_POST[$key] ?? null);
        } elseif (($atr['type'] ?? '') === 'checkbox' || ($atr[0] ?? '') === 'checkbox') {
            $datos[$key] = isset($_POST[$key]) ? 1 : 0;
        } elseif (($atr['type'] ?? '') === 'textarea' || ($atr[0] ?? '') === 'textarea') {
            $datos[$key] = addslashes($_POST[$key] ?? '');
        } elseif (($atr['type'] ?? '') === 'text_info' || ($atr[0] ?? '') === 'text_info') {
            // Ignorar
        } else {
            $datos[$key] = $_POST[$key] ?? '';
        }
    }

    $usuario_id = $datos['id'] ?? 0;

    if ($usuario_id > 0) {
        // Si existe id → actualizar
        $result = mysql_update($tabla, $datos, $usuario_id, $conn);
    } else {
        // Si no existe → insertar
        $result = mysql_insert($tabla, $datos, $conn);
        $usuario_id = mysqli_insert_id($conn);
    }

    // Guardar permisos, rubros, cajas y cuentas (igual que antes)
    if (isset($_POST['permisos'])) {
        $delete_sql = "DELETE FROM usuario_permiso WHERE usuario_id = '$usuario_id'";
        echo "<pre>DELETE QUERY: $delete_sql</pre>";
        mysqli_query($conn, $delete_sql);

        foreach ($_POST['permisos'] as $permiso_id) {
            $permiso_id = intval($permiso_id);
            $insert_sql = "INSERT INTO usuario_permiso (usuario_id, permiso_id) VALUES ('$usuario_id', '$permiso_id')";
            echo "<pre>INSERT QUERY: $insert_sql</pre>";
            mysqli_query($conn, $insert_sql);
        }
    }

    if (isset($_POST['rubros'])) {
        mysqli_query($conn, "DELETE FROM usuario_rubro WHERE usuario_id = '$usuario_id'");
        foreach ($_POST['rubros'] as $rubro_id) {
            $rubro_id = intval($rubro_id);
            mysqli_query($conn, "INSERT INTO usuario_rubro (usuario_id, rubro_id) VALUES ('$usuario_id', '$rubro_id')");
        }
    }

    if (isset($_POST['cajas'])) {
        mysqli_query($conn, "DELETE FROM usuario_caja WHERE usuario_id = '$usuario_id'");
        foreach ($_POST['cajas'] as $caja_id) {
            $caja_id = intval($caja_id);
            mysqli_query($conn, "INSERT INTO usuario_caja (usuario_id, caja_id) VALUES ('$usuario_id', '$caja_id')");
        }
    }

    if (isset($_POST['cuentas'])) {
        mysqli_query($conn, "DELETE FROM usuario_cuenta WHERE usuario_id = '$usuario_id'");
        foreach ($_POST['cuentas'] as $cuenta_id) {
            $cuenta_id = intval($cuenta_id);
            mysqli_query($conn, "INSERT INTO usuario_cuenta (usuario_id, cuenta_id) VALUES ('$usuario_id', '$cuenta_id')");
        }
    }
}
?>
