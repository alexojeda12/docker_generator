    <?php
    $host = 'localhost';
    $db = 'dockergen';
    $user = 'root';
    $pass = ''; // cambia por tu contraseña

    try {
        $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
    ?>
