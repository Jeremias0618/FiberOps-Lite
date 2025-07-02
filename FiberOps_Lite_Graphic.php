<?php
require_once('routeros_api.class.php');

$host = '10.80.80.11';
$user = 'yeremi';
$password = 'yeremi08';
$port = 8728;

// Función para convertir bits por segundo a una unidad legible
function formatSpeed($bps) {
    $units = ['bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps'];
    $i = 0;
    while ($bps >= 1000 && $i < count($units) - 1) {
        $bps /= 1000;
        $i++;
    }
    return round($bps, 2) . ' ' . $units[$i];
}

// Respuesta AJAX
if (isset($_GET['action']) && $_GET['action'] === 'getTraffic' && isset($_GET['interfaz'])) {
    $interfaceName = $_GET['interfaz'];

    $API = new RouterosAPI();
    if ($API->connect($host, $user, $password, $port)) {
        $res = $API->comm('/interface/monitor-traffic', [
            'interface' => $interfaceName,
            'once' => null
        ]);
        $API->disconnect();

        $rx = isset($res[0]['rx-bits-per-second']) ? (int) filter_var($res[0]['rx-bits-per-second'], FILTER_SANITIZE_NUMBER_INT) : 0;
        $tx = isset($res[0]['tx-bits-per-second']) ? (int) filter_var($res[0]['tx-bits-per-second'], FILTER_SANITIZE_NUMBER_INT) : 0;

        header('Content-Type: application/json');
        echo json_encode([
            'rx' => $rx,
            'tx' => $tx,
            'rx_formatted' => formatSpeed($rx),
            'tx_formatted' => formatSpeed($tx)
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No se pudo conectar al MikroTik']);
    }
    exit;
}

$mensaje = '';
$interfaz = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pppoe_user = trim($_POST['pppoe_user']);
    if (!empty($pppoe_user)) {
        $API = new RouterosAPI();
        if ($API->connect($host, $user, $password, $port)) {
            $API->write('/interface/pppoe-server/print', false);
            $API->write('?user=' . $pppoe_user);
            $ifaceResult = $API->read();
            $API->disconnect();

            if (!empty($ifaceResult) && isset($ifaceResult[0]['name'])) {
                $interfaz = $ifaceResult[0]['name'];
                $mensaje = "✅ Interfaz detectada: <strong>$interfaz</strong>";
            } else {
                $mensaje = "❌ No se encontró interfaz para el usuario PPPoE <strong>$pppoe_user</strong>";
            }
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
    <title>Monitor de Consumo TX/RX PPPoE</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f9f9f9;
            margin: 0;
            padding: 30px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            text-align: center;
            margin-bottom: 30px;
        }

        input[type="text"] {
            padding: 10px;
            width: 250px;
            font-size: 16px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 20px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-left: 10px;
        }

        button:hover {
            background: #0056b3;
        }

        .resultado {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
        }

        .cuadro {
            display: inline-block;
            width: 180px;
            padding: 15px;
            margin: 15px 10px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }

        #grafica-container {
            max-width: 800px;
            margin: 30px auto;
        }
    </style>
</head>
<body>

<h2>Monitor en Tiempo Real de Consumo TX/RX PPPoE</h2>

<form method="POST">
    <input type="text" name="pppoe_user" placeholder="Ej: 00122062@33" required />
    <button type="submit">Buscar</button>
</form>

<div class="resultado">
    <?php if ($mensaje): ?>
        <p><?= $mensaje ?></p>
    <?php endif; ?>
</div>

<?php if ($interfaz): ?>
    <div id="grafica-container">
        <canvas id="grafica"></canvas>
    </div>

    <div style="text-align:center;">
        <div class="cuadro" id="tx_value">TX: --</div>
        <div class="cuadro" id="rx_value">RX: --</div>
    </div>

    <script>
        const ctx = document.getElementById('grafica').getContext('2d');

        const data = {
            labels: [],
            datasets: [
                {
                    label: 'TX (bps)',
                    data: [],
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255,99,132,0.2)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'RX (bps)',
                    data: [],
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54,162,235,0.2)',
                    fill: true,
                    tension: 0.3
                }
            ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                animation: false,
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Consumo de Interfaz <?= htmlspecialchars($interfaz) ?>',
                        font: { size: 18 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1e9) return (value/1e9).toFixed(1) + ' Gbps';
                                if (value >= 1e6) return (value/1e6).toFixed(1) + ' Mbps';
                                if (value >= 1e3) return (value/1e3).toFixed(1) + ' Kbps';
                                return value + ' bps';
                            }
                        }
                    }
                }
            }
        };

        const trafficChart = new Chart(ctx, config);

        function agregarDato(chart, label, tx, rx) {
            chart.data.labels.push(label);
            chart.data.datasets[0].data.push(tx);
            chart.data.datasets[1].data.push(rx);
            if (chart.data.labels.length > 30) {
                chart.data.labels.shift();
                chart.data.datasets[0].data.shift();
                chart.data.datasets[1].data.shift();
            }
            chart.update();
        }

        async function fetchTraffic() {
            try {
                const response = await fetch('?action=getTraffic&interfaz=<?= urlencode($interfaz) ?>');
                const json = await response.json();
                if (json.error) return;

                const now = new Date();
                const label = now.toLocaleTimeString();

                agregarDato(trafficChart, label, json.tx, json.rx);
                document.getElementById('tx_value').textContent = 'TX: ' + json.tx_formatted;
                document.getElementById('rx_value').textContent = 'RX: ' + json.rx_formatted;
            } catch (error) {
                console.error('Error al obtener tráfico:', error);
            }
        }

        setInterval(fetchTraffic, 1000);
        fetchTraffic();
    </script>
<?php endif; ?>

</body>
</html>
