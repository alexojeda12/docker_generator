<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Generador Docker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #0d1117;
            color: white;
        }

        .container {
            padding-top: 80px;
        }

        .content-wrapper {
            background-color: rgba(13, 17, 23, 0.95);
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.05);
            margin-bottom: 40px;
            padding: 2rem;
        }

        .navbar {
            background-color: #1a202c;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
        }

        .navbar-brand {
            font-weight: bold;
        }

        .navbar-nav .nav-link {
            color: white;
        }

        .navbar-nav .nav-link:hover {
            color: #4a90e2;
        }

        .btn-primary {
            background-color: #2d3748;
            border-color: #2d3748;
        }

        .alert-warning {
            background-color: #4a5568;
            color: white;
        }

        .form-control {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #4a5568;
            color: white;
        }

        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.3);
            border-color: #4a5568;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow" style="background-color: #1a202c;">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">DockerGen</a>
        <div class="collapse navbar-collapse justify-content-end">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-outline-light">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


<div class="container">
    <div class="content-wrapper">
        <h1 class="mb-4">Generador de docker-compose</h1>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <div class="alert alert-warning">Debes iniciar sesión para crear contenedores.</div>
        <?php else: ?>
            <form method="POST" action="formulario.php" class="mb-3">
                <div class="mb-3">
                    <label for="nombre_archivo" class="form-label">Nombre del archivo (sin extensión):</label>
                    <input type="text" class="form-control" id="nombre_archivo" name="nombre_archivo" required>
                </div>
                <div class="mb-3">
                    <label for="num_contenedores" class="form-label">¿Cuántos contenedores quieres crear?</label>
                    <input type="number" min="1" class="form-control" id="num_contenedores" name="num_contenedores" required>
                </div>
                <button type="submit" class="btn btn-primary">Siguiente</button>
            </form>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
