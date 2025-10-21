<?php
session_start();

$error = '';
$success = '';
$BASE = 'https://vervigo.loca.lt';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $location = $_POST['location'];

    $api_url = $BASE . '/auth/register';

    $json_data = json_encode([
        'name' => $name,
        'email' => $email,
        'password' => $password,
        'location' => $location
    ]);
 

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json_data)
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $success = "Kayıt başarılı! <a href='index.php'>Giriş yap</a>";
    } else {
        $data = json_decode($response, true);
        $error = $data['message'] ?? "Kayıt başarısız: HTTP $httpCode";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Vervigo - Kayıt Ol</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            font-family: Arial, sans-serif;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            background: #fff;
            padding: 2em;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 1em;
        }
        button {
            background-color: #6a11cb;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            font-size: 1em;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #2575fc;
        }
        a {
            color: #6a11cb;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .msg { color: green; margin-bottom: 1em; }
        .err { color: red; margin-bottom: 1em; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Vervigo - Kayıt Ol</h2>

    <?php if (!empty($success)): ?>
        <p class="msg"><?php echo $success; ?></p>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <p class="err"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Ad Soyad" required>
        <input type="email" name="email" placeholder="E-posta" required>
        <input type="password" name="password" placeholder="Şifre" required>
        <input type="text" name="location" placeholder="Konum / Şehir" required>
        <button type="submit">Kayıt Ol</button>
    </form>

    <p>Zaten hesabın var mı? <a href="index.php">Giriş Yap</a></p>
</div>

</body>
</html>
