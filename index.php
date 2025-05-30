<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: formulario.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a DockerGen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            z-index: -1;
            top: 0;
            left: 0;
        }
        .center-box {
            max-width: 500px;
            margin: 8% auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            text-align: center;
            position: relative;
            z-index: 1;
        }
        h1 {
            font-weight: bold;
            margin-bottom: 30px;
        }
        .btn-lg {
            width: 100%;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Fondo animado -->
    <div id="particles-js"></div>

    <!-- Caja central -->
    <div class="center-box">
        <h1>DockerGen</h1>
        <p class="lead">Genera tus archivos Docker Compose fácilmente</p>
        <a href="login.php" class="btn btn-primary btn-lg">Iniciar Sesión</a>
        <a href="registro.php" class="btn btn-outline-secondary btn-lg">Registrarse</a>
    </div>

    <!-- Script de partículas -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
      particlesJS("particles-js", {
        "particles": {
          "number": {
            "value": 25,
            "density": {
              "enable": true,
              "value_area": 800
            }
          },
          "shape": {
            "type": "image",
            "image": {
              "src": "assets/docker-logo.png",
              "width": 100,
              "height": 100
            }
          },
          "opacity": {
            "value": 0.5,
            "random": false
          },
          "size": {
            "value": 30,
            "random": true
          },
          "move": {
            "enable": true,
            "speed": 2,
            "direction": "bottom",
            "random": false,
            "straight": false,
            "out_mode": "out"
          }
        },
        "interactivity": {
          "detect_on": "canvas",
          "events": {
            "onhover": { "enable": false },
            "onclick": { "enable": false },
            "resize": true
          }
        },
        "retina_detect": true
      });
    </script>
</body>
</html>
