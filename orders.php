<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header("Location: index.php");
    exit();
}

$BASE = 'https://vervigo.loca.lt';
$token = $_SESSION['access_token'];

function api_get($url, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token",
        "Content-Type: application/json"
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

$tickets = api_get("$BASE/payments/mytickets", $token);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Siparişlerim - Vervigo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 900px;
            margin: 2em auto;
            background: white;
            padding: 2em;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        h1 {
            color: #6a11cb;
        }
        .ticket {
            background: #f5f5f5;
            margin-bottom: 1.5em;
            padding: 1em;
            border-radius: 8px;
            border-left: 5px solid #6a11cb;
        }
        .ticket.used {
            border-left-color: #dc3545;
            background: #fff5f5;
        }
        .ticket h3 {
            margin: 0 0 0.5em;
        }
        .ticket p {
            margin: 0.3em 0;
        }
        a.button {
            display: inline-block;
            margin-bottom: 1em;
            padding: 10px 20px;
            background: #6a11cb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
        }
        a.button:hover {
            background: #2575fc;
        }
    </style>
</head>
<body>

<div class="container">
    <a class="button" href="home.php">← Ana Sayfaya Dön</a>
    <h1>Siparişlerim</h1>

    <?php if (empty($tickets)): ?>
        <p>Henüz siparişiniz yok.</p>
    <?php else: ?>
        <?php foreach ($tickets as $ticket): ?>
            <div class="ticket <?php echo $ticket['used'] ? 'used' : ''; ?>">
                <h3>Sipariş #<?php echo $ticket['id']; ?></h3>
                <p><strong>Tutar:</strong> <?php echo $ticket['amount'] . ' ' . $ticket['currency']; ?></p>
                <p><strong>Açıklama:</strong> <?php echo $ticket['description']; ?></p>
                <p><strong>Durum:</strong> <?php echo $ticket['used'] ? 'Kullanıldı' : 'Kullanılmadı'; ?></p>
                <p><strong>Tarih:</strong> <?php echo $ticket['created_at']; ?></p>

                 <?php if (!empty($ticket['qrcode_path'])): ?>
                    <img src="https://vervigo.loca.lt/<?php echo str_replace('\\', '/', htmlspecialchars($ticket['qrcode_path'])); ?>"
                        alt="QR Kod"
                        style="max-width: 200px; margin-top: 1em; border: 1px solid #ccc; border-radius: 8px;">
                <?php endif; ?>

                
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>
