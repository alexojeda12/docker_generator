<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'conexion.php';

// Inicializamos la variable $error para los mensajes de error
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Ahora también obtenemos el campo 'rol'
    $stmt = $conn->prepare("SELECT id, username, password, rol FROM usuarios WHERE username = :username");
    $stmt->bindParam(":username", $username);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verificamos si el usuario fue encontrado
    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION["user_id"] = $user['id'];
            $_SESSION["username"] = $user['username'];
            $_SESSION["rol"] = $user['rol']; // Guardamos el rol en la sesión
            header("Location: dashboard.php");
            exit;
        } else {
            // Contraseña incorrecta para un usuario existente
            $error = "Usuario o contraseña incorrectos.";
        }
    } else {
        // Usuario no encontrado
        $error = "El usuario no existe. Por favor, comprueba tus credenciales o regístrate.";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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

        .alert-danger {
            background-color: #e53e3e;
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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">DockerGen</a>
    </div>
</nav>

<div class="container">
    <div class="content-wrapper">
        <h2>Iniciar sesión</h2>

        <?php
        // Mostramos el mensaje de error si existe
        if (!empty($error)) {
            echo "<div class='alert alert-danger'>{$error}</div>";
        }
        ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username">Usuario</label>
                <input name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button class="btn btn-primary">Entrar</button>

        </form>
    </div>
<div class="d-flex justify-content-center mt-4">
    <a href="index.php" class="btn btn-secondary">Volver a página principal</a>
</div>

</div>

</body>
</html>