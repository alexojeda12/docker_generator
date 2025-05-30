<?php
session_start();
require 'conexion.php';

// Solo permitir acceso si el usuario ha iniciado sesión y es admin por rol
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Procesar acciones
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['accion'])) {
        if ($_POST['accion'] === 'agregar') {
            $nombre = $_POST['nombre'];
            $imagen = $_POST['imagen'];
            $stmt = $conn->prepare("INSERT INTO imagenes_docker (nombre, imagen) VALUES (:nombre, :imagen)");
            $stmt->execute([':nombre' => $nombre, ':imagen' => $imagen]);
        }

        if ($_POST['accion'] === 'editar') {
            $id = $_POST['id'];
            $nombre = $_POST['nombre'];
            $imagen = $_POST['imagen'];
            $stmt = $conn->prepare("UPDATE imagenes_docker SET nombre = :nombre, imagen = :imagen WHERE id = :id");
            $stmt->execute([':nombre' => $nombre, ':imagen' => $imagen, ':id' => $id]);
        }

        if ($_POST['accion'] === 'eliminar') {
            $id = $_POST['id'];
            $stmt = $conn->prepare("DELETE FROM imagenes_docker WHERE id = :id");
            $stmt->execute([':id' => $id]);
        }
    }
}

// Obtener imágenes existentes
$stmt = $conn->query("SELECT * FROM imagenes_docker");
$imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #0d1117;
            color: white;
        }

        .form-control, .form-select {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid #4a5568;
            color: white;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .btn {
            min-width: 120px;
        }

        table tr td, table tr th {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">DockerGen - Panel Admin</span>
        <a href="dashboard.php" class="btn btn-outline-light">Volver al panel</a>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="mb-4">Gestión de Imágenes Docker</h2>

    <!-- Agregar nueva imagen -->
    <form method="POST" class="row g-3 mb-4">
        <input type="hidden" name="accion" value="agregar">
        <div class="col-md-5">
            <input type="text" name="nombre" class="form-control" placeholder="Nombre descriptivo" required>
        </div>
        <div class="col-md-5">
            <input type="text" name="imagen" class="form-control" placeholder="Nombre de la imagen (e.g. nginx:latest)" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Añadir</button>
        </div>
    </form>

    <!-- Tabla de imágenes existentes -->
    <div class="table-responsive">
        <table class="table table-dark table-bordered table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Imagen</th>
                    <th style="width: 200px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($imagenes as $img): ?>
                    <tr>
                        <form method="POST">
                            <input type="hidden" name="accion" value="editar">
                            <input type="hidden" name="id" value="<?= $img['id'] ?>">
                            <td><?= $img['id'] ?></td>
                            <td><input type="text" name="nombre" value="<?= htmlspecialchars($img['nombre']) ?>" class="form-control" required></td>
                            <td><input type="text" name="imagen" value="<?= htmlspecialchars($img['imagen']) ?>" class="form-control" required></td>
                            <td>
                                <button type="submit" class="btn btn-primary btn-sm">Guardar</button>
                                <a href="#" onclick="eliminar(<?= $img['id'] ?>)" class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="formEliminar" method="POST" style="display: none;">
    <input type="hidden" name="accion" value="eliminar">
    <input type="hidden" name="id" id="eliminarId">
</form>

<script>
    function eliminar(id) {
        if (confirm("¿Estás seguro de que quieres eliminar esta imagen?")) {
            document.getElementById("eliminarId").value = id;
            document.getElementById("formEliminar").submit();
        }
    }
</script>
</body>
</html>
