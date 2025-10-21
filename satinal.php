<?php
session_start();
if (!isset($_SESSION['access_token'])) {
    header("Location: index.php");
    exit();
}

$eventId = $_GET['id'] ?? '';
$success = null;
$qrcodePath = null;
$baseUrl = "https://vervigo.loca.lt"; // Sunucunun dış URL’si

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = 100.0;
    $currency = 'TRY';
    $description = "Reklam ID: $eventId için demo ödeme";

    $data = json_encode([
        "amount" => $amount,
        "currency" => $currency,
        "description" => $description,
        "event_id" => (int)$eventId,
        "generate_qr" => true // ✅ QR talebi açıkça belirtildi
    ]);

    $ch = curl_init("$baseUrl/payments/");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $_SESSION['access_token']
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $success = true;
        $json = json_decode($response, true);
        if (!empty($json['qrcode_path'])) {
            $relativePath = str_replace("\\", "/", $json['qrcode_path']); // \ yerine /
            $qrcodePath = $baseUrl . '/' . ltrim($relativePath, '/'); // ✅ Tam URL
        }
    } else {
        $success = false;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Reklam Satın Al</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            text-align: center;
            padding: 3em;
        }
        .form-container {
            background: white;
            color: black;
            border-radius: 12px;
            padding: 2em;
            max-width: 500px;
            margin: auto;
        }
        input {
            padding: 10px;
            margin: 0.5em 0;
            width: 100%;
        }
        button {
            padding: 10px 20px;
            background-color: #2575fc;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            margin-top: 1em;
        }
        .success { color: green; }
        .fail { color: red; }
        img.qr {
            margin-top: 1em;
            max-width: 300px;
            border: 2px solid #ccc;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Reklam Satın Al</h2>
        <p>Reklam ID: <?php echo htmlspecialchars($eventId); ?></p>

        <?php if ($success === true): ?>
            <p class="success">✔️ Ödeme başarılı!</p>

            <?php if ($qrcodePath): ?>
                <p>QR Kodunuz:</p>
                <img src="<?php echo htmlspecialchars($qrcodePath); ?>" alt="QR Kod" class="qr" />
            <?php else: ?>
                <p><em>QR kod alınamadı.</em></p>
            <?php endif; ?>

            <form action="home.php" method="get">
                <button type="submit">Ana Sayfaya Dön</button>
            </form>

        <?php elseif ($success === false): ?>
            <p class="fail">❌ Ödeme başarısız. Lütfen tekrar deneyin.</p>
        <?php else: ?>
            <form method="post">
                <input type="text" name="card_number" placeholder="Kart Numarası" required />
                <input type="text" name="expiry" placeholder="MM/YY" required />
                <input type="text" name="cvv" placeholder="CVV" required />
                <input type="text" name="name" placeholder="Kart Sahibi" required />
                <button type="submit">Satın Al</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
