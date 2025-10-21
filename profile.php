<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'] ?? 'Bilinmiyor';
$role = $_SESSION['role'] ?? 'user';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Vervigo - Profilim</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }
        .profile-container {
            background: white;
            padding: 2.5em;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h2 {
            color: #6a11cb;
            margin-bottom: 1em;
        }
        .info {
            margin: 1em 0;
            font-size: 1.1em;
        }
        .label {
            font-weight: bold;
            color: #6a11cb;
        }
        a {
            display: inline-block;
            margin-top: 1.5em;
            text-decoration: none;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            font-weight: bold;
        }
        a:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h2>Profil Bilgilerim</h2>
    <div class="info"><span class="label">Kullanıcı Adı:</span> <?php echo htmlspecialchars($username); ?></div>
    <div class="info"><span class="label">Rol:</span> <?php echo htmlspecialchars($role); ?></div>
    <div class="info"><span class="label">Oturum:</span> Aktif</div>

    <a href="home.php">Ana Sayfaya Dön</a>
</div>

</body>
</html>
