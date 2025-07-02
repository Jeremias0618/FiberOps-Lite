<?php
require_once('routeros_api.class.php');

// Parámetros de conexión
$host = '10.80.80.11';
$user = 'yeremi';
$password = 'yeremi08';
$port = 8728;

// Inicializar variables
$mensaje = '';
$vlan = '';

// Si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pppoe_user = trim($_POST['pppoe_user']);

    if (!empty($pppoe_user)) {
        $API = new RouterosAPI();

        if ($API->connect($host, $user, $password, $port)) {

            // Buscar al usuario por campo user
            $API->write('/interface/pppoe-server/print', false);
            $API->write('?user=' . $pppoe_user);

            $results = $API->read();

            if (!empty($results)) {
                $vlan = $results[0]['service'];
                $mensaje = "✅ La VLAN del usuario <strong>$pppoe_user</strong> es: <strong>$vlan</strong>";
            } else {
                $mensaje = "❌ No se encontró el usuario PPPoE: <strong>$pppoe_user</strong>";
            }

            $API->disconnect();
        } else {
            $mensaje = "❌ No se pudo conectar al MikroTik.";
        }
    } else {
        $mensaje = "⚠️ Debes ingresar un usuario PPPoE.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Buscar VLAN PPPoE</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 30px;
        }
        .form-container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            width: 400px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }
        input[type="submit"] {
            background: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            margin-top: 15px;
            cursor: pointer;
        }
        .mensaje {
            margin-top: 20px;
            padding: 10px;
            background: #e9ecef;
            border-left: 4px solid #007BFF;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Buscar VLAN por Usuario PPPoE</h2>
    <form method="POST">
        <label for="pppoe_user">Nombre de Usuario (ej: 00122062@33):</label>
        <input type="text" name="pppoe_user" id="pppoe_user" required>
        <input type="submit" value="Buscar VLAN">
    </form>

    <?php if (!empty($mensaje)): ?>
        <div class="mensaje"><?= $mensaje ?></div>
    <?php endif; ?>
</div>

</body>
</html>
