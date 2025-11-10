<?php
if(isset($_GET['dataid'])){
    $dataid = intval($_GET['dataid']);
}

if(isset($_POST['agregar'])){
    $datos = [];

    foreach($campos as $key=>$atr){
        $tipo = is_array($atr) ? ($atr['type'] ?? $atr[0]) : '';

        if($tipo == 'date'){
            $datos[$key] = fechasql($_POST[$key] ?? '');
        } elseif($tipo == 'checkbox'){
            $datos[$key] = !empty($_POST[$key]) ? 1 : 0;
        } elseif($tipo == 'textarea'){
            $datos[$key] = addslashes($_POST[$key] ?? '');
        } elseif($tipo == 'text_info'){
            $datos[$key] = null;
        } else{
            $datos[$key] = $_POST[$key] ?? '';
        }
    }

    if(empty($datos['id'])) {
        $datos['id'] = 0;
    }

    $result = mysql_insert($tabla, $datos, $conn);
    $usuario_id = mysqli_insert_id($conn);

    if($tabla == 'empleado' && !empty($_POST['fecha_alta'])){
        $alta = fechasql($_POST['fecha_alta']);
        $sql = "INSERT INTO empleado_historico (empleado_id, alta) VALUES ($usuario_id, '$alta')";
        mysqli_query($conn, $sql);
    }

    if(!empty($_POST['permisos'])){
        foreach($_POST['permisos'] as $permiso_id){
            $permiso_id = intval($permiso_id);
            mysqli_query($conn, "INSERT INTO usuario_permiso (usuario_id, permiso_id) VALUES ($usuario_id, $permiso_id)");
        }
    }

    if(!empty($_POST['rubros'])){
        foreach($_POST['rubros'] as $rubro_id){
            $rubro_id = intval($rubro_id);
            mysqli_query($conn, "INSERT INTO usuario_rubro (usuario_id, rubro_id) VALUES ($usuario_id, $rubro_id)");
        }
    }

    if(!empty($_POST['cajas'])){
        foreach($_POST['cajas'] as $caja_id){
            $caja_id = intval($caja_id);
            mysqli_query($conn, "INSERT INTO usuario_caja (usuario_id, caja_id) VALUES ($usuario_id, $caja_id)");
        }
    }

    if(!empty($_POST['cuentas'])){
        foreach($_POST['cuentas'] as $cuenta_id){
            $cuenta_id = intval($cuenta_id);
            mysqli_query($conn, "INSERT INTO usuario_cuenta (usuario_id, cuenta_id) VALUES ($usuario_id, $cuenta_id)");
        }
    }
}

if(isset($_POST['editar'])){
    $datos = [];

    foreach($campos as $key=>$atr){
        $tipo = is_array($atr) ? ($atr['type'] ?? $atr[0]) : '';

        if($tipo == 'date'){
            $datos[$key] = fechasql($_POST[$key] ?? '');
        } elseif($tipo == 'checkbox'){
            $datos[$key] = !empty($_POST[$key]) ? 1 : 0;
        } elseif($tipo == 'textarea'){
            $datos[$key] = addslashes($_POST[$key] ?? '');
        } elseif($tipo == 'text_info'){
            $datos[$key] = null;
        } else{
            $datos[$key] = $_POST[$key] ?? '';
        }
    }

    $result = mysql_update($tabla, $datos, $datos['id'], $conn);
    $usuario_id = intval($datos['id']);
    $dataid = $usuario_id;

    // Borrar y volver a insertar relaciones
    $relaciones = ['cajas' => 'usuario_caja', 'permisos' => 'usuario_permiso', 'rubros' => 'usuario_rubro', 'cuentas' => 'usuario_cuenta'];

    foreach($relaciones as $postKey => $tablaRel){
        mysqli_query($conn, "DELETE FROM $tablaRel WHERE usuario_id=$usuario_id");
        if(!empty($_POST[$postKey])){
            foreach($_POST[$postKey] as $id){
                $id = intval($id);
                mysqli_query($conn, "INSERT INTO $tablaRel (usuario_id, {$postKey}_id) VALUES ($usuario_id, $id)");
            }
        }
    }
}
?>
