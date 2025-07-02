<?php
function conectarDB() {
    $host = '10.80.80.101';
    $port = 5432;
    $dbname = 'fiberprodata';
    $user = 'fiberproadmin';
    $pass = 'noc12363';
    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        echo "<div class='error'>Error de conexión: " . $e->getMessage() . "</div>";
        exit;
    }
}

function obtenerModeloONT($ip, $index) {
    $comunidad = "FiberPro2021";
    $oid = '1.3.6.1.4.1.2011.6.128.1.1.2.45.1.4.' . $index;
    $snmp = @snmpget($ip, $comunidad, $oid, 1000000, 1);
    if ($snmp === false) {
        return "No se obtuvo respuesta SNMP.";
    }
    if (preg_match('/"(.*?)"/', $snmp, $match)) {
        return $match[1];
    } else {
        return "Modelo no disponible";
    }
}

function obtenerIPporHost($host) {
    $mapa = [
        'SD-1' => '10.20.70.10',
        'SD-2' => '10.20.70.21',
        'SD-3' => '10.20.70.30',
        'SD-4' => '10.20.70.46',
        'INC-5' => '10.5.5.2',
        'SD-7' => '10.20.70.72',
        'JIC-8' => '172.16.2.2',
        'NEW_JIC-8' => '19.19.1.2',
        'NEW_JIC2-8' => '19.19.2.2',
        'ATE-9' => '172.99.99.2',
        'SMP-10' => '10.170.7.2',
        'CAMP-11' => '10.111.11.2',
        'CAMP2-11' => '10.112.25.2',
        'PTP-12' => '20.20.5.1',
        'ANC-13' => '10.13.13.2',
        'CHO-14' => '172.18.2.2',
        'LO-15' => '10.70.7.2',
        'LO2-15' => '10.70.8.2',
        'VIR-16' => '30.150.130.2',
        'PTP-17' => '10.17.7.2',
        'VENT-18' => '18.18.1.2'
    ];
    return $mapa[$host] ?? null;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Consulta Modelo ONT</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f4f8;
            color: #333;
            margin: 0; padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 40px 15px;
        }
        .container {
            background: white;
            max-width: 450px;
            width: 100%;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            margin-bottom: 24px;
            color: #0057b7;
            font-weight: 700;
        }
        form {
            margin-bottom: 20px;
        }
        input[type="text"] {
            width: 100%;
            padding: 14px 18px;
            border-radius: 8px;
            border: 2px solid #ddd;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus {
            border-color: #0057b7;
            outline: none;
        }
        button {
            margin-top: 15px;
            background-color: #0057b7;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 14px 25px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        button:hover {
            background-color: #003f8a;
        }
        .resultado {
            margin-top: 20px;
            background: #e6f0ff;
            border: 1px solid #a3c1ff;
            padding: 16px;
            border-radius: 8px;
            font-size: 18px;
            color: #003366;
        }
        .error {
            margin-top: 20px;
            background: #ffd6d6;
            border: 1px solid #ff4c4c;
            padding: 16px;
            border-radius: 8px;
            color: #990000;
            font-weight: 600;
        }
        @media (max-width: 480px) {
            .container {
                padding: 25px 20px;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Consultar Modelo ONT</h2>
    <form method="POST" autocomplete="off" novalidate>
        <input type="text" name="documento" placeholder="Ingrese DNI o RUC" required autofocus />
        <button type="submit">Consultar</button>
    </form>

<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["documento"])) {
    $documento = trim($_POST["documento"]);
    $pdo = conectarDB();

    $stmt = $pdo->prepare("SELECT snmpindexonu, host FROM onu_datos WHERE onudesc = :doc LIMIT 1");
    $stmt->execute(['doc' => $documento]);
    $row = $stmt->fetch();

    if ($row) {
        $ip = obtenerIPporHost($row['host']);
        if ($ip === null) {
            echo "<div class='error'>No se encontró IP para el host '<strong>" . htmlspecialchars($row['host']) . "</strong>'.</div>";
        } else {
            $modelo = obtenerModeloONT($ip, $row['snmpindexonu']);
            echo "<div class='resultado'><strong>Modelo ONT:</strong> " . htmlspecialchars($modelo) . "</div>";
        }
    } else {
        echo "<div class='error'>No se encontró cliente con ese DNI/RUC.</div>";
    }
}
?>
</div>
</body>
</html>
