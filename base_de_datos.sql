CREATE DATABASE dockergen;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    rol ENUM('user', 'admin') NOT NULL DEFAULT 'user'
);


CREATE TABLE dockers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    nombre_contenedor VARCHAR(100) NOT NULL,
    contenido TEXT NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


CREATE TABLE imagenes_docker (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    imagen VARCHAR(100) NOT NULL
);


INSERT INTO imagenes_docker (nombre, imagen) VALUES
('Ubuntu', 'ubuntu'),
('Alpine', 'alpine'),
('Debian', 'debian'),
('NGINX', 'nginx'),
('MySQL', 'mysql'),
('MariaDB', 'mariadb'),
('Node.js', 'node'),
('Python', 'python'),
('PHP', 'php'),
('Redis', 'redis'),
('MongoDB', 'mongo'),
('PostgreSQL', 'postgres'),
('Java', 'openjdk'),
('Golang', 'golang'),
('Apache', 'httpd');
