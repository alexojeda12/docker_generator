<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dockerId = $_POST['docker_id'] ?? null;
    $nuevoNombre = trim($_POST['nuevo_nombre'] ?? '');

    if ($dockerId && $nuevoNombre !== '') {
        $stmt = $conn->prepare("UPDATE dockers SET nombre_contenedor = :nuevo_nombre WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':nuevo_nombre' => $nuevoNombre,
            ':id' => $dockerId,
            ':user_id' => $_SESSION['user_id']
        ]);
    }
}

header("Location: mis_dockers.php");
exit;
