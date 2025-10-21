<?php
session_start();

$error = '';
$BASE = 'https://vervigo.loca.lt';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pw = $_POST['password'];

    $api_url = $BASE . '/auth/login';

    $post_fields = http_build_query([
        'username' => $email,
        'password' => $pw
    ]);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/x-www-form-urlencoded',
        'Content-Length: ' . strlen($post_fields)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if ($data === null) {
        $error = "API cevabı ayrıştırılamadı: " . json_last_error_msg();
    } elseif (isset($data['access_token'])) {
        $_SESSION['access_token'] = $data['access_token'];

        // Kullanıcının rolünü öğren
        $me_ch = curl_init();
        curl_setopt($me_ch, CURLOPT_URL, $BASE . '/auth/me');
        curl_setopt($me_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($me_ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $_SESSION['access_token'],
            'Content-Type: application/json'
        ]);
        $me_response = curl_exec($me_ch);
        $me_data = json_decode($me_response, true);
        curl_close($me_ch);

        if (isset($me_data['role'])) {
            $_SESSION['role'] = $me_data['role'];
            $_SESSION['username'] = $me_data['name'] ?? $email;

            if ($_SESSION['role'] === 'admin') {
                header("Location: home.php");
            } elseif ($_SESSION['role'] === 'restaurant') {
                header("Location: restaurant.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $error = "Kullanıcı rolü alınamadı.";
        }
    } else {
        $error = $data['message'] ?? "Giriş başarısız.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vervigo - Giriş Yap</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(to right, #667eea, #764ba2);
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }
        .login-box h1 {
            margin-bottom: 1em;
            color: #333;
        }
        .login-box input {
            width: 100%;
            padding: 0.8em;
            margin-bottom: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .login-box button {
            width: 100%;
            padding: 0.8em;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1em;
            cursor: pointer;
        }
        .login-box button:hover {
            background-color: #5a67d8;
        }
        .login-box .error {
            color: red;
            margin-bottom: 1em;
        }
        .login-box a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h1>Vervigo'ya Giriş Yap</h1>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="E-posta" required>
        <input type="password" name="password" placeholder="Şifre" required>
        <button type="submit">Giriş Yap</button>
    </form>

    <p>Hesabın yok mu? <a href="register.php">Kayıt Ol</a></p>
</div>
</body>
</html>
