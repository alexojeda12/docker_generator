<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: formulario.php");
    exit;
}

require_once 'conexion.php';

$mensaje = ""; // Variable para almacenar los mensajes al usuario
$mensaje_tipo = ""; // Para controlar el tipo de alerta (éxito o error)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    if ($username && $email && $password && $confirm_password) {
        // 1. Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mensaje = "El formato del correo electrónico no es válido.";
            $mensaje_tipo = "danger";
        }
        // 2. Comprobar si el nombre de usuario ya existe
        else {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE username = :username");
            $stmt->bindParam(":username", $username);
            $stmt->execute();
            if ($stmt->fetchColumn() > 0) {
                $mensaje = "El nombre de usuario ya está registrado. Por favor, elige otro.";
                $mensaje_tipo = "danger";
            }
            // 3. Comprobar si el email ya existe
            else {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE email = :email");
                $stmt->bindParam(":email", $email);
                $stmt->execute();
                if ($stmt->fetchColumn() > 0) {
                    $mensaje = "El correo electrónico ya está registrado. Si ya tienes una cuenta, inicia sesión.";
                    $mensaje_tipo = "danger";
                }
                // 4. Verificar que las contraseñas coincidan
                else if ($password !== $confirm_password) {
                    $mensaje = "Las contraseñas no coinciden. Por favor, asegúrate de escribirlas igual.";
                    $mensaje_tipo = "danger";
                }
                // 5. Si todo es válido, registrar al usuario
                else {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare("INSERT INTO usuarios (username, password, email) VALUES (?, ?, ?)");
                    if ($stmt->execute([$username, $hash, $email])) {
                        $_SESSION['registro_exitoso'] = "¡Registro exitoso! Ya puedes iniciar sesión.";
                        header("Location: login.php");
                        exit;
                    } else {
                        // En caso de un error inesperado al insertar
                        $mensaje = "Ha ocurrido un error al intentar registrar el usuario. Inténtalo de nuevo más tarde.";
                        $mensaje_tipo = "danger";
                    }
                }
            }
        }
    } else {
        $mensaje = "Por favor, completa todos los campos para registrarte.";
        $mensaje_tipo = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
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

        .alert-info {
            background-color: #2563eb;
            color: white;
        }

        /* Añadido para el mensaje de error/éxito */
        .alert-danger {
            background-color: #dc3545; /* Rojo de Bootstrap para errores */
            color: white;
        }
        .alert-success {
            background-color: #28a745; /* Verde de Bootstrap para éxito */
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

        .btn-primary {
            background-color: #2d3748;
            border-color: #2d3748;
        }

        .btn-secondary {
            background-color: #4a5568;
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
        <h2>Crear una cuenta</h2>

        <?php
        // Mostrar mensaje de éxito si viene de una redirección
        if (isset($_SESSION['registro_exitoso'])) {
            echo '<div class="alert alert-success mt-3">' . htmlspecialchars($_SESSION['registro_exitoso']) . '</div>';
            unset($_SESSION['registro_exitoso']); // Limpiar el mensaje de la sesión
        }
        // Mostrar mensaje de error/información si existe
        if (!empty($mensaje)) {
            $alert_class = "alert-" . ($mensaje_tipo === "danger" ? "danger" : "info"); // Usar alert-danger o alert-info
            echo '<div class="alert ' . $alert_class . ' mt-3">' . htmlspecialchars($mensaje) . '</div>';
        }
        ?>

        <form method="POST" class="mt-3">
            <div class="mb-3">
                <label>Usuario</label>
                <input name="username" class="form-control" required value="<?= htmlspecialchars($username ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            <div class="mb-3">
                <label>Contraseña</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Confirmar Contraseña</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button class="btn btn-primary">Registrarse</button>
        </form>

        <div class="d-flex justify-content-center mt-4">
            <a href="index.php" class="btn btn-secondary">Volver a página principal</a>
        </div>
    </div>
</div>

</body>
</html>