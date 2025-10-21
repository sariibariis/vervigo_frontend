<?php
session_start();

if (!isset($_SESSION['access_token']) || ($_SESSION['role'] ?? '') !== 'owner') {
    header("Location: index.php");
    exit();
}

$BASE = 'https://vervigo.loca.lt';
$token = $_SESSION['access_token'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $date = $_POST['date'];
    $location = $_POST['location'];
    $image = $_FILES['image'];

    $postFields = [
        'title' => $title,
        'description' => $desc,
        'date' => $date,
        'location' => $location,
    ];

    // Eğer görsel yüklendiyse ekle
    if (isset($image) && $image['error'] === UPLOAD_ERR_OK) {
        $postFields['image'] = new CURLFile(
            $image['tmp_name'],
            mime_content_type($image['tmp_name']),
            $image['name']
        );
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $BASE . '/events/');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $success = "Reklam başarıyla gönderildi. Onay bekliyor.";
    } else {
        $error = "Hata: Reklam gönderilemedi. (HTTP $httpCode)";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yeni Reklam Ekle - Vervigo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            padding: 2em;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 2em;
            border-radius: 12px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        input, textarea {
            width: 100%;
            padding: 0.8em;
            margin-bottom: 1em;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            background-color: #6a11cb;
            color: white;
            padding: 0.8em 1.5em;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background-color: #2575fc;
        }
        .msg { color: green; margin-bottom: 1em; }
        .err { color: red; margin-bottom: 1em; }
        h2 {
            color: #6a11cb;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Yeni Reklam Ekle</h2>

    <?php if (!empty($success)): ?>
        <p class="msg"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="err"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Reklam Başlığı" required>
        <textarea name="description" placeholder="Açıklama" required></textarea>
        <input type="datetime-local" name="date" required>
        <input type="text" name="location" placeholder="Konum" required>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Gönder</button>
    </form>
</div>

</body>
</html>
