<?php
// Permitir que el frontend se comunique con este script
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Archivo donde se guardarán los registros
$archivo_log = 'registro.txt';

// Función para obtener la IP real del usuario
function obtener_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // En caso de que use proxies o balanceadores
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$ip_usuario = obtener_ip();
$fecha = date('Y-m-d H:i:s');

// Leer los datos que vienen del JavaScript
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['evento'])) {
    $evento = $input['evento']; // Puede ser 'visita' o 'clic'
    $detalles = isset($input['detalles']) ? $input['detalles'] : 'N/A';
    
    // Formatear la línea que se va a escribir en el archivo log
    $linea_registro = "[$fecha] IP: $ip_usuario | Evento: $evento | Info: $detalles" . PHP_EOL;
    
    // Escribir en el archivo (añadiendo al final sin borrar lo anterior)
    file_put_contents($archivo_log, $linea_registro, FILE_APPEND | LOCK_EX);
    
    echo json_encode(["status" => "success", "mensaje" => "Registrado correctamente"]);
    exit;
}

echo json_encode(["status" => "error", "mensaje" => "Petición no válida"]);
?>