<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dockerId = $_POST['docker_id'] ?? null;

    if ($dockerId) {
        $stmt = $conn->prepare("DELETE FROM dockers WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ':id' => $dockerId,
            ':user_id' => $_SESSION['user_id']
        ]);
    }
}

header("Location: mis_dockers.php");
exit;
