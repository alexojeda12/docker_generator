<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'conexion.php'; // Conexión a la base de datos

$nombre = $_POST['nombre_archivo'] ?? 'docker_compose';
$contenedores = $_POST['contenedor'] ?? [];
$redes = $_POST['redes'] ?? []; // Captura el nuevo array de redes

$yml = "version: '3'\nservices:\n";

foreach ($contenedores as $c) {
    $nombreContenedor = trim($c['nombre'] ?? '');
    $imagen = trim($c['imagen'] ?? '');
    $version = trim($c['version'] ?? '');
    $puertos = trim($c['puertos'] ?? '');
    $contenedorRedes = trim($c['contenedor_redes'] ?? ''); // Nuevo campo para las redes del contenedor

    if ($nombreContenedor === '' || $imagen === '') {
        continue; // Evita errores por campos vacíos
    }

    $imagenCompleta = $imagen . ':' . ($version !== '' ? $version : 'latest');

    $yml .= "  {$nombreContenedor}:\n";
    $yml .= "    image: {$imagenCompleta}\n";

    if (!empty($puertos)) {
        $yml .= "    ports:\n      - \"{$puertos}\"\n";
    }

    // Añadir redes específicas para este contenedor
    if (!empty($contenedorRedes)) {
        $redesArray = array_map('trim', explode(',', $contenedorRedes));
        $redesArray = array_filter($redesArray); // Eliminar elementos vacíos

        if (!empty($redesArray)) {
            $yml .= "    networks:\n";
            foreach ($redesArray as $redContenedor) {
                $yml .= "      - {$redContenedor}\n";
            }
        }
    }
}

// Sección de definición de redes
$networksSection = '';
foreach ($redes as $red) {
    $subred = trim($red['subred'] ?? '');
    $nombreRed = trim($red['nombre_red'] ?? '');

    // Si el nombre de red está vacío, usa un valor por defecto si hay subred
    if ($nombreRed === '') {
        if (!empty($subred)) {
            $nombreRed = 'custom_net';
        } else {
            continue; // Si no hay ni nombre ni subred, ignora esta entrada de red
        }
    }

    if (!empty($nombreRed)) {
        if (empty($networksSection)) { // Añadir el encabezado 'networks:' solo una vez
            $networksSection = "\nnetworks:\n";
        }
        $networksSection .= "  {$nombreRed}:\n";
        if (!empty($subred)) {
            $networksSection .= "    driver: bridge\n";
            $networksSection .= "    ipam:\n";
            $networksSection .= "      config:\n";
            $networksSection .= "        - subnet: {$subred}\n";
        }
    }
}

$yml .= $networksSection; // Añade la sección de redes al final del YAML

// Guardar en la base de datos
try {
    $stmt = $conn->prepare("INSERT INTO dockers (user_id, nombre_contenedor, contenido) VALUES (:user_id, :nombre, :contenido)");
    $stmt->execute([
        ":user_id" => $_SESSION["user_id"],
        ":nombre" => $nombre,
        ":contenido" => $yml
    ]);
} catch (PDOException $e) {
    die("Error al guardar el Docker Compose: " . $e->getMessage());
}

// Descargar el archivo generado
header('Content-Type: text/plain');
header("Content-Disposition: attachment; filename=docker-compose.yaml");
echo $yml;
exit;
?>