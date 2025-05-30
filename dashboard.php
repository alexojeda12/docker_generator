<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - DockerGen</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #0d1117;
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">DockerGen</span>
            <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
        </div>
    </nav>

    <div class="container text-center mt-5">
        <h2 class="mb-4">Bienvenido, <?= htmlspecialchars($_SESSION['username']) ?> 👋</h2>
        <div class="d-grid gap-3 col-6 mx-auto">
            <a href="inicio.php" class="btn btn-primary btn-lg">Crear nuevo docker-compose</a>
            <a href="mis_dockers.php" class="btn btn-secondary btn-lg">Ver docker-compose creados</a>
            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                <a href="panel_admin.php" class="btn btn-secondary btn-lg">Modificar imágenes</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
