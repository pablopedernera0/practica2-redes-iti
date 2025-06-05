<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Conexiones - Docker Compose</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 30px;
        }
        .service-card {
            background: #ecf0f1;
            padding: 20px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 5px solid #3498db;
        }
        .success {
            border-left-color: #2ecc71;
            background: #d5f4e6;
        }
        .error {
            border-left-color: #e74c3c;
            background: #fadbd8;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .info-table th, .info-table td {
            padding: 8px 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .info-table th {
            background-color: #34495e;
            color: white;
        }
        .status {
            padding: 4px 8px;
            border-radius: 4px;
            color: white;
            font-weight: bold;
        }
        .status.online {
            background-color: #2ecc71;
        }
        .status.offline {
            background-color: #e74c3c;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🐳 Panel de Monitoreo - Docker Compose</h1>
        <p>Infraestructura: Nginx + PHP + MySQL</p>
    </div>

    <?php
    // Función para probar conexión a MySQL
    function testMySQLConnection() {
        $host = 'db';  // Nombre del servicio en docker-compose
        $username = 'usuario';
        $password = 'password123';
        $database = 'mi_aplicacion';

        try {
            $connection = new PDO("mysql:host=$host;dbname=$database", $username, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Probar consulta
            $stmt = $connection->query("SELECT COUNT(*) as total FROM usuarios");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'status' => 'success',
                'message' => 'Conexión exitosa',
                'data' => $result
            ];
        } catch(PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    // Obtener información del sistema
    $dbTest = testMySQLConnection();
    $phpVersion = phpversion();
    $serverInfo = $_SERVER;
    ?>

    <!-- Información de PHP -->
    <div class="service-card success">
        <h3>🐘 Servicio PHP</h3>
        <p><strong>Estado:</strong> <span class="status online">ONLINE</span></p>
        <p><strong>Versión:</strong> <?php echo $phpVersion; ?></p>
        <p><strong>Servidor:</strong> <?php echo $serverInfo['SERVER_SOFTWARE'] ?? 'N/A'; ?></p>
        <p><strong>Host:</strong> <?php echo gethostname(); ?></p>
    </div>

    <!-- Información de Nginx -->
    <div class="service-card success">
        <h3>🌐 Servicio Nginx</h3>
        <p><strong>Estado:</strong> <span class="status online">ONLINE</span></p>
        <p><strong>Puerto:</strong> 8080 (externo) → 80 (interno)</p>
        <p><strong>Función:</strong> Proxy reverso hacia PHP-FPM</p>
        <p><strong>IP Cliente:</strong> <?php echo $_SERVER['REMOTE_ADDR'] ?? 'N/A'; ?></p>
    </div>

    <!-- Información de MySQL -->
    <div class="service-card <?php echo $dbTest['status'] === 'success' ? 'success' : 'error'; ?>">
        <h3>🗄️ Servicio MySQL</h3>
        <p><strong>Estado:</strong>
            <span class="status <?php echo $dbTest['status'] === 'success' ? 'online' : 'offline'; ?>">
                    <?php echo $dbTest['status'] === 'success' ? 'ONLINE' : 'OFFLINE'; ?>
                </span>
        </p>
        <p><strong>Host:</strong> db (nombre del servicio)</p>
        <p><strong>Puerto:</strong> 3306</p>
        <p><strong>Base de datos:</strong> mi_aplicacion</p>
        <p><strong>Mensaje:</strong> <?php echo $dbTest['message']; ?></p>

        <?php if ($dbTest['status'] === 'success'): ?>
            <p><strong>Usuarios en BD:</strong> <?php echo $dbTest['data']['total']; ?></p>
        <?php endif; ?>
    </div>

    <!-- Tabla de información técnica -->
    <div class="service-card">
        <h3>📊 Información Técnica</h3>
        <table class="info-table">
            <tr>
                <th>Parámetro</th>
                <th>Valor</th>
            </tr>
            <tr>
                <td>Método de solicitud</td>
                <td><?php echo $_SERVER['REQUEST_METHOD']; ?></td>
            </tr>
            <tr>
                <td>URI solicitada</td>
                <td><?php echo $_SERVER['REQUEST_URI']; ?></td>
            </tr>
            <tr>
                <td>Protocolo</td>
                <td><?php echo $_SERVER['SERVER_PROTOCOL']; ?></td>
            </tr>
            <tr>
                <td>User Agent</td>
                <td><?php echo substr($_SERVER['HTTP_USER_AGENT'], 0, 100) . '...'; ?></td>
            </tr>
            <tr>
                <td>Fecha/Hora</td>
                <td><?php echo date('Y-m-d H:i:s'); ?></td>
            </tr>
        </table>
    </div>

    <!-- Arquitectura del sistema -->
    <div class="service-card">
        <h3>🏗️ Arquitectura del Sistema</h3>
        <p><strong>Red Docker:</strong> app_network (bridge)</p>
        <p><strong>Flujo de datos:</strong></p>
        <ol>
            <li>Cliente solicita página en puerto 8080</li>
            <li>Nginx recibe solicitud y la envía a PHP-FPM (puerto 9000)</li>
            <li>PHP procesa la solicitud y se conecta a MySQL (puerto 3306)</li>
            <li>MySQL devuelve datos a PHP</li>
            <li>PHP genera HTML y lo envía a Nginx</li>
            <li>Nginx devuelve respuesta final al cliente</li>
        </ol>
    </div>
</div>
</body>
</html>