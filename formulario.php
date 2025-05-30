<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Incluir conexión a la base de datos
require_once 'conexion.php'; 

// Obtener número de contenedores y nombre de archivo desde el formulario
$num = $_POST['num_contenedores'] ?? 1;
$archivo = $_POST['nombre_archivo'] ?? 'docker_compose';

// Obtener imágenes desde la base de datos
$stmt = $conn->query("SELECT nombre, imagen FROM imagenes_docker ORDER BY nombre ASC");
$imagenes = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de contenedores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #0d1117;
            overflow-x: hidden;
        }
        .content-wrapper {
            width: 100%;
            max-width: 900px;
            margin: 80px auto 40px auto;
            padding: 2rem;
            background-color: rgba(13, 17, 23, 0.95);
            border-radius: 12px;
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.05);
            z-index: 1;
        }
        h1, h3, h5, label {
            color: white;
        }
        .form-control, select {
            background-color: rgba(255, 255, 255, 0.85);
            color: #000;
        }
        pre {
            background: rgba(255, 255, 255, 0.1);
            padding: 1em;
            border-radius: 8px;
            font-family: monospace;
            white-space: pre-wrap;
            color: #ffffff;
        }
        .manual-fields {
            margin-top: 10px;
        }
        .network-group {
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .network-group h6 {
            color: white;
            margin-bottom: 10px;
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


<div class="content-wrapper">
    <h1 class="mb-4 text-center">Configura tus contenedores</h1>
    <form method="POST" action="generar.php" id="dockerForm">
        <input type="hidden" name="nombre_archivo" value="<?= htmlspecialchars($archivo) ?>">
        <input type="hidden" name="num_contenedores" value="<?= $num ?>">

        <div id="networks-container">
            <div class="network-group" data-network-id="1">
                <h6>Red 1:</h6>
                <div class="mb-3">
                    <label>Subred personalizada (opcional, formato CIDR):</label>
                    <input name="redes[1][subred]" class="form-control network-subred" placeholder="Ej: 192.168.100.0/24">
                </div>
                <div class="mb-3">
                    <label>Nombre de la red (opcional, por defecto 'custom_net'):</label>
                    <input name="redes[1][nombre_red]" class="form-control network-name" placeholder="Ej: red_privada">
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary btn-sm mb-4" id="add-network">Añadir otra red</button>
        
        <?php for ($i = 1; $i <= $num; $i++): ?>
            <div class="contenedor-group mb-4" data-id="<?= $i ?>">
                <h5>Contenedor <?= $i ?>:</h5>

                <div class="mb-3">
                    <label>Imagen (selecciona o añade manualmente):</label>
                    <select name="contenedor[<?= $i ?>][imagen_seleccion]" class="form-select imagen-seleccion" data-index="<?= $i ?>" required>
                        <option value="" selected disabled>-- Selecciona una imagen --</option>
                        <?php foreach ($imagenes as $img): ?>
                            <option value="<?= htmlspecialchars($img['nombre']) ?>"
                                data-nombre="<?= htmlspecialchars($img['nombre']) ?>"
                                data-imagen="<?= htmlspecialchars($img['imagen']) ?>">
                                <?= htmlspecialchars($img['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="manual">Añadir manualmente</option>
                    </select>
                </div>

                <div class="manual-fields" id="manual-fields-<?= $i ?>" style="display:none;">
                    <div class="mb-3">
                        <label>Nombre del contenedor:</label>
                        <input type="text" name="contenedor[<?= $i ?>][nombre]" class="form-control nombre" required>
                    </div>
                    <div class="mb-3">
                        <label>Imagen (nombre completo):</label>
                        <input type="text" name="contenedor[<?= $i ?>][imagen]" class="form-control imagen" required>
                    </div>
                </div>

                <div class="mb-3 version-puertos">
                    <label>Versión de la imagen (opcional):</label>
                    <input name="contenedor[<?= $i ?>][version]" class="form-control version">
                </div>
                <div class="mb-3 version-puertos">
                    <label>Puertos (host:contenedor):</label>
                    <input name="contenedor[<?= $i ?>][puertos]" class="form-control puertos">
                </div>
                <div class="mb-3">
                    <label>Redes del contenedor (separa por comas si son varias):</label>
                    <input type="text" name="contenedor[<?= $i ?>][contenedor_redes]" class="form-control contenedor-redes" placeholder="Ej: red_privada,otra_red">
                </div>
                <hr>
            </div>
        <?php endfor; ?>

        <button type="submit" class="btn btn-success w-100">Generar Docker Compose</button>
    </form>

    <h3 class="mt-5">Vista previa:</h3>
    <pre id="vistaPrevia">version: '3'
services:
    ...
</pre>
</div>

<script>
let networkCount = 1;

function generarVistaPrevia() {
    const contenedores = document.querySelectorAll('.contenedor-group');
    const networkGroups = document.querySelectorAll('.network-group');

    let resultado = "version: '3'\nservices:\n";

    contenedores.forEach(group => {
        const index = group.getAttribute('data-id');
        const selectImagen = group.querySelector('.imagen-seleccion');
        const manualFields = group.querySelector('.manual-fields');
        let nombre, imagen;

        if(selectImagen.value === 'manual') {
            nombre = manualFields.querySelector('.nombre').value.trim();
            imagen = manualFields.querySelector('.imagen').value.trim();
        } else {
            const option = selectImagen.selectedOptions[0];
            nombre = option.getAttribute('data-nombre') || selectImagen.value.trim();
            imagen = option.getAttribute('data-imagen') || selectImagen.value.trim();
        }

        let version = group.querySelector('.version')?.value.trim();
        const puertos = group.querySelector('.puertos')?.value.trim();
        const contenedorRedes = group.querySelector('.contenedor-redes')?.value.trim();

        if (nombre && imagen) {
            version = version || "latest";
            resultado += `  ${nombre}:\n`;
            resultado += `    image: ${imagen}:${version}\n`;
            if (puertos) {
                resultado += `    ports:\n      - "${puertos}"\n`;
            }
            if (contenedorRedes) {
                const redesArray = contenedorRedes.split(',').map(net => net.trim()).filter(net => net !== '');
                if (redesArray.length > 0) {
                    resultado += `    networks:\n`;
                    redesArray.forEach(net => {
                        resultado += `      - ${net}\n`;
                    });
                }
            }
        }
    });

    let networksSection = '';
    networkGroups.forEach(networkGroup => {
        const subred = networkGroup.querySelector('.network-subred')?.value.trim();
        const nombreRed = networkGroup.querySelector('.network-name')?.value.trim() || 'custom_net';

        if (subred) {
            if (!networksSection) {
                networksSection = "\nnetworks:\n";
            }
            networksSection += `  ${nombreRed}:\n`;
            networksSection += "    driver: bridge\n";
            networksSection += "    ipam:\n";
            networksSection += "      config:\n";
            networksSection += `        - subnet: ${subred}\n`;
        } else if (nombreRed !== 'custom_net') { // Si hay un nombre de red pero no subred, solo declara la red
             if (!networksSection) {
                networksSection = "\nnetworks:\n";
            }
            networksSection += `  ${nombreRed}:\n`;
        }
    });

    resultado += networksSection;

    document.getElementById('vistaPrevia').textContent = resultado;
}

function addNetworkField() {
    networkCount++;
    const networksContainer = document.getElementById('networks-container');
    const newNetworkGroup = document.createElement('div');
    newNetworkGroup.classList.add('network-group');
    newNetworkGroup.setAttribute('data-network-id', networkCount);
    newNetworkGroup.innerHTML = `
        <h6>Red ${networkCount}: <button type="button" class="btn btn-danger btn-sm remove-network">X</button></h6>
        <div class="mb-3">
            <label>Subred personalizada (opcional, formato CIDR):</label>
            <input name="redes[${networkCount}][subred]" class="form-control network-subred" placeholder="Ej: 192.168.100.0/24">
        </div>
        <div class="mb-3">
            <label>Nombre de la red (opcional, por defecto 'custom_net'):</label>
            <input name="redes[${networkCount}][nombre_red]" class="form-control network-name" placeholder="Ej: red_privada">
        </div>
    `;
    networksContainer.appendChild(newNetworkGroup);

    newNetworkGroup.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', generarVistaPrevia);
    });

    newNetworkGroup.querySelector('.remove-network').addEventListener('click', (e) => {
        e.target.closest('.network-group').remove();
        generarVistaPrevia();
    });

    generarVistaPrevia();
}


document.getElementById('add-network').addEventListener('click', addNetworkField);

document.querySelectorAll('.imagen-seleccion').forEach(select => {
    select.addEventListener('change', (e) => {
        const index = e.target.getAttribute('data-index');
        const manualFields = document.getElementById('manual-fields-' + index);
        const nombreInput = manualFields.querySelector('.nombre');
        const imagenInput = manualFields.querySelector('.imagen');

        if (e.target.value === 'manual') {
            manualFields.style.display = 'block';
            manualFields.querySelectorAll('input').forEach(input => input.required = true);
            e.target.required = false;

            nombreInput.value = '';
            imagenInput.value = '';

        } else {
            manualFields.style.display = 'none';
            manualFields.querySelectorAll('input').forEach(input => {
                input.required = false;
            });

            const option = e.target.selectedOptions[0];
            nombreInput.value = option.getAttribute('data-nombre') || e.target.value;
            imagenInput.value = option.getAttribute('data-imagen') || e.target.value;
        }
        generarVistaPrevia();
    });
});

document.querySelectorAll('.manual-fields input, .version-puertos input, .contenedor-redes').forEach(input => {
    input.addEventListener('input', generarVistaPrevia);
});

// Event listeners para las redes iniciales
document.querySelectorAll('.network-subred, .network-name').forEach(input => {
    input.addEventListener('input', generarVistaPrevia);
});


generarVistaPrevia();
</script>

</body>
</html>