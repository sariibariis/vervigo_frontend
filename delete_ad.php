<?php
session_start();

// Sadece giriş yapılmış ve owner veya admin ise işlem yapılabilir
if (!isset($_SESSION['access_token']) || !in_array($_SESSION['role'] ?? '', ['owner', 'admin'])) {
    header("Location: index.php");
    exit();
}

$BASE = 'https://vervigo.loca.lt';  // Güncel sunucu adresin
$token = $_SESSION['access_token'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "$BASE/events/$id");  // Silinecek reklamın ID'si
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $token"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 204) {
        // Başarıyla silindiyse ana sayfaya dön
        header("Location: home.php");
        exit();
    } else {
        // Silinemediyse hata mesajı
        echo "<h3 style='color:red; text-align:center; margin-top:2em;'>Silme işlemi başarısız oldu (HTTP $httpCode)</h3>";
        echo "<p style='text-align:center;'><a href='home.php'>Ana Sayfaya Dön</a></p>";
    }
} else {
    header("Location: home.php");
    exit();
}
