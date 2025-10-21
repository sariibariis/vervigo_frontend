<?php
session_start();

if (!isset($_SESSION['access_token']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: index.php");
    exit();
}

$BASE = 'https://vervigo.loca.lt';
$token = $_SESSION['access_token'];

$feedback = '';
$status = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST['type'] ?? '';
    $id = $_POST['id'] ?? '';
    $action = $_POST['action'] ?? '';

    $data = json_encode([
        'type' => $type,
        'id' => (int)$id
    ]);

    if (in_array($action, ['approve', 'reject'])) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $BASE . '/admin/' . $action); // now: /admin/approve or /admin/reject
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
            "Content-Length: " . strlen($data)
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            $feedback = ucfirst($action) . " işlemi başarıyla tamamlandı.";
            $status = "success";
        } else {
            $feedback = ucfirst($action) . " işlemi başarısız oldu. (HTTP $httpCode)";
            $status = "error";
        }
    } else {
        $feedback = "Geçersiz işlem.";
        $status = "error";
    }
} else {
    header("Location: home.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin İşlem Sonucu - Vervigo</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .message-box {
            background: white;
            padding: 2em 3em;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            text-align: center;
            max-width: 500px;
        }
        .message-box h2 {
            color: #6a11cb;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        a {
            display: inline-block;
            margin-top: 1.5em;
            text-decoration: none;
            padding: 10px 20px;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            border-radius: 6px;
            font-weight: bold;
        }
        a:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
        }
    </style>
</head>
<body>

<div class="message-box">
    <h2>İşlem Sonucu</h2>
    <p class="<?php echo $status; ?>"><?php echo $feedback; ?></p>
    <a href="home.php">Ana Sayfaya Dön</a>
</div>

</body>
</html>
