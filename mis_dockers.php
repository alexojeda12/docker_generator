<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

$stmt = $conn->prepare("SELECT id, nombre_contenedor, contenido, fecha_creacion FROM dockers WHERE user_id = :user_id ORDER BY fecha_creacion DESC");
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$dockers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Dockers</title>
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

        .accordion-button {
            background-color: #1a202c;
            color: white;
        }

        .accordion-button:focus {
            box-shadow: none;
        }

        .accordion-body {
            background-color: #1a202c;
            color: white;
        }

        pre {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 1em;
            border-radius: 8px;
            font-family: monospace;
            color: #ffffff;
        }

        .btn-primary, .btn-danger {
            background-color: #2d3748;
            border-color: #2d3748;
        }

        .alert-info {
            background-color: #4a5568;
            border-color: #2d3748;
        }

        .form-control, .form-control-sm {
            background-color: rgba(255, 255, 255, 0.2);
            border-color: #4a5568;
            color: white;
        }
    </style>
</head>
<body>
    
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
        <h1 class="mb-4">Tus Docker Compose Generados</h1>

        <?php if (count($dockers) > 0): ?>
            <div class="accordion" id="dockersAccordion">
                <?php foreach ($dockers as $i => $docker): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $i ?>">
                            <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $i ?>" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $i ?>">
                                <?= htmlspecialchars($docker['nombre_contenedor']) ?> - <?= $docker['fecha_creacion'] ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $i ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $i ?>" data-bs-parent="#dockersAccordion">
                            <div class="accordion-body">
                                <pre><?= htmlspecialchars($docker['contenido']) ?></pre>

                                <form action="renombrar_docker.php" method="POST" class="d-inline-block mt-2 me-2">
                                    <input type="hidden" name="docker_id" value="<?= $docker['id'] ?>">
                                    <div class="input-group">
                                        <input type="text" name="nuevo_nombre" class="form-control form-control-sm" placeholder="Nuevo nombre" required>
                                        <button type="submit" class="btn btn-primary btn-sm">Renombrar</button>
                                    </div>
                                </form>

                                <form action="eliminar_docker.php" method="POST" class="d-inline-block mt-2" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este Docker Compose?');">
                                    <input type="hidden" name="docker_id" value="<?= $docker['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">Aún no has generado ningún archivo Docker Compose.</div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
