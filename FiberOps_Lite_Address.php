<?php
require_once('routeros_api.class.php'); // Asegúrate que la ruta sea correcta

// Parámetros de conexión
$host = '10.80.80.11';
$user = 'yeremi';
$password = 'yeremi08';
$port = 8728;

$mensaje = '';
$address = '';
$localAddress = '';
$interfaz = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pppoe_user = trim($_POST['pppoe_user']);

    if (!empty($pppoe_user)) {
        $API = new RouterosAPI();

        if ($API->connect($host, $user, $password, $port)) {

            // Obtener dirección remota (cliente) desde /ppp active
            $API->write('/ppp/active/print', false);
            $API->write('?name=' . $pppoe_user);
            $pppResult = $API->read();

            if (!empty($pppResult)) {
                $address = $pppResult[0]['address'];

                // Obtener el nombre real de la interfaz PPPoE
                $API->write('/interface/pppoe-server/print', false);
                $API->write('?user=' . $pppoe_user);
                $ifaceResult = $API->read();

                if (!empty($ifaceResult)) {
                    $interfaz = $ifaceResult[0]['name'];

                    // Buscar dirección local en /ip address usando el nombre completo de la interfaz
                    $API->write('/ip/address/print', false);
                    $API->write('?interface=' . $interfaz);
                    $ipResult = $API->read();

                    if (!empty($ipResult)) {
                        $localAddress = explode('/', $ipResult[0]['address'])[0]; // Quitar /32
                    } else {
                        $localAddress = 'No encontrado (sin IP asignada)';
                    }
                } else {
                    $localAddress = 'No se encontró interfaz PPPoE';
                }

                $mensaje = "✅ <strong>" . htmlspecialchars($pppoe_user) . "</strong><br>
Dirección remota (Cliente): <strong>" . htmlspecialchars($address) . "</strong><br>
Dirección local (Router): <strong>" . htmlspecialchars($localAddress) . "</strong><br>
Interfaz: <strong>" . htmlspecialchars($interfaz) . "</strong>";
            } else {
                $mensaje = "❌ No se encontró el usuario activo PPPoE: <strong>" . htmlspecialchars($pppoe_user) . "</strong>";
            }

            $API->disconnect();
        } else {
            $mensaje = "❌ No se pudo conectar al MikroTik.";
        }
    } else {
        $mensaje = "⚠️ Debes ingresar un nombre de usuario PPPoE.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Buscar IP PPPoE</title>
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
            text-align: left;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
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
        button, input[type="submit"] {
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
            display: inline-block;
        }
        button:hover, input[type="submit"]:hover {
            background-color: #003f8a;
        }
        .mensaje {
            margin-top: 20px;
            background: #e6f0ff;
            border: 1px solid #a3c1ff;
            padding: 16px;
            border-radius: 8px;
            font-size: 18px;
            color: #003366;
            text-align: left;
            white-space: pre-wrap;
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
    <h2>Obtener Adrress & Local Address</h2>
    <form method="POST" autocomplete="off" novalidate>
        <label for="pppoe_user">Nombre de Usuario (ej: 00122062@33):</label>
        <input type="text" name="pppoe_user" id="pppoe_user" required autofocus>
        <input type="submit" value="Buscar Direcciones IP">
    </form>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje"><?= $mensaje ?></div>
    <?php endif; ?>
</div>

</body>
</html>
