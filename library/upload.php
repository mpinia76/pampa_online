<?php
// Ruta de destino para guardar los archivos subidos
$targetDirectory = '../'.$_POST['folder'].'/';

// Nombre del campo de archivo en el formulario
$fileFieldName = $_POST['name'];

// Comprobar si se recibi� el archivo
if (!empty($_FILES[$fileFieldName]['name'])) {
    $fileName = $_FILES[$fileFieldName]['name'];
    $filePath = $targetDirectory . $fileName;

    // Mover el archivo al directorio de destino
    if (move_uploaded_file($_FILES[$fileFieldName]['tmp_name'], $filePath)) {
        // �xito en la carga del archivo
        $response = array(
            'success' => true,
            'filename' => $fileName
        );
    } else {
        // Error al mover el archivo
        $response = array(
            'success' => false,
            'message' => 'Error al mover el archivo al directorio de destino.'
        );
    }
} else {
    // No se recibi� ning�n archivo
    $response = array(
        'success' => false,
        'message' => 'No se recibio ningun archivo.'
    );
}

// Devolver la respuesta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
