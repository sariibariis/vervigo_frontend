<?php
session_start();

if (!isset($_SESSION['access_token']) || !in_array($_SESSION['role'] ?? '', ['customer', 'user'])) {
    header("Location: index.php");
    exit();
}

$BASE = 'https://vervigo.loca.lt';
$token = $_SESSION['access_token'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $description = $_POST['description'] ?? '';
    $location = $_POST['location'] ?? '';
    $category = $_POST['category'] ?? '';

    $data = json_encode([
        'name' => $_SESSION['username'],
        'description' => $description,
        'location' => $location,
        'category' => $category
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $BASE . '/businesses/');
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
        header("Location: home.php?owner_request=success");
        exit();
    } else {
        echo "<h3 style='color:red; text-align:center;'>İstek gönderilemedi. (HTTP $httpCode)</h3>";
        echo "<p style='text-align:center;'><a href='home.php'>Ana Sayfaya Dön</a></p>";
        echo "<pre style='margin: 2em auto; max-width: 600px; background: #eee; padding: 1em; border-radius: 6px;'>" . htmlspecialchars($response) . "</pre>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Restoran Başvurusu</title>
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: Arial, sans-serif;
            padding: 2em;
            color: #333;
        }
        .form-container {
            max-width: 600px;
            background: white;
            margin: auto;
            padding: 2em;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
        }
        button {
            background: #6a11cb;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover {
            background: #2575fc;
        }
        h2 {
            color: #6a11cb;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Restoran Sahibi Olmak İçin Başvur</h2>
    <form method="POST">
        <label>Açıklama:</label>
        <textarea name="description" required>Restoran sahibi olmak istiyorum.</textarea>

        <label>Konum / Şehir:</label>
        <input type="text" name="location" placeholder="Örn: İstanbul" required>

        <label>Kategori:</label>
        <select name="category" required>
            <option value="Genel">Genel</option>
            <option value="Fast Food">Fast Food</option>
            <option value="Kafe">Kafe</option>
            <option value="Tatlıcı">Tatlıcı</option>
            <option value="Dünya Mutfağı">Dünya Mutfağı</option>
        </select>

        <button type="submit">Başvuruyu Gönder</button>
    </form>
</div>
</body>
</html>
