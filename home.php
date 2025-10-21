<?php
session_start();

if (!isset($_SESSION['access_token'])) {
    header("Location: index.php");
    exit();
}

$BASE = 'https://vervigo.loca.lt';
$token = $_SESSION['access_token'];
$role = $_SESSION['role'] ?? 'user';

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

$events = api_get("$BASE/events/", $token);
$queue = $role === 'admin' ? api_get("$BASE/admin/queue", $token) : null;
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Vervigo - Ana Sayfa</title>
    <style>
         img.uploaded-image {
            width: 275px;
            height: 183px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #ccc;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: #333;
        }
        header {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            text-align: center;
            padding: 1.5em;
        }
        header h1 {
            margin: 0 0 0.3em;
        }
        header p a {
            color: #ffc107;
            text-decoration: none;
        }
        .container {
            max-width: 900px;
            margin: 2em auto;
            background: white;
            padding: 2em;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .button {
            display: inline-block;
            background-color: #6a11cb;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            border: none;
            margin-bottom: 1em;
            cursor: pointer;
        }
        .button:hover {
            background-color: #2575fc;
        }
        .card {
            position: relative; /* Eklendi */
            background: #f9f9f9;
            padding: 1.5em;
            margin-bottom: 1.5em;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease; /* Eklendi: yumuşak geçiş için */
        }

        .card::after {
            content: "Satın Al";
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2em;
            font-weight: bold;
            color: green;
            opacity: 0;
            transition: opacity 0.2s;
        }
        .card.swipe-active::after {
            opacity: 1;
        }
        .swipeable-wrapper {
            position: relative;
            overflow: hidden; 
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .swipe-bg {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #28a745;
            display: flex;
            align-items: center;
            justify-content: left;
            padding-left: 20px;
            color: white;
            font-weight: bold;
            font-size: 20px;
            z-index: 0;
        }

        .swipeable {
            position: relative;
            background: #f9f9f9;
            padding: 1.5em;
            border-radius: 10px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
            z-index: 1;
            transition: transform 0.3s ease;
        }
        .card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1em;
        }

        h2 {
            color: #6a11cb;
        }
        form {
            margin-top: 1em;
        }
        .new-ad-btn {
            display: inline-block;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            margin-bottom: 1.5em;
            transition: background 0.3s ease;
        }
        .new-ad-btn:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<header>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 style="margin: 0;">Hoş Geldin, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <div>
            <a href="profile.php" style="margin-right: 10px; background: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; color: #6a11cb;">Profilim</a>
            <a href="orders.php" style="margin-right: 10px; background: #28a745; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; color: white;">Siparişlerim</a>
            <a href="logout.php" style="background: #ffc107; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; color: black;">Çıkış Yap</a>


        </div>
    </div>
    <p>Rol: <?php echo htmlspecialchars($role); ?></p>
</header>

<div class="container">
    <?php if ($role === 'owner' || $role === 'restaurant'): ?>
        <a href="create_ad.php" class="new-ad-btn">+ Yeni Reklam Ekle</a>
        <a href="orders.php" class="new-ad-btn">Siparişlerim</a>

    <?php elseif ($role === 'customer'): ?>
        <a href="request_owner.php" class="new-ad-btn">İşletme Sahibi Olmak İstiyorum</a>
        <a href="orders.php" class="new-ad-btn">Siparişlerim</a>


    <?php endif; ?>

    <h2>Reklamlar</h2>
    <?php if (!empty($events)): ?>
        <?php foreach ($events as $event): ?>
             <?php
                    $image_url = isset($event['image']) && $event['image']
                        ? $BASE . '/' . ltrim($event['image'], '/')
                        : 'uploads/default.jpg';
            ?>

         <div class="swipeable-wrapper" data-event-id="<?php echo $event['id']; ?>">
            <div class="swipe-bg">Satın Al ✅</div> <!-- SADECE 1 ADET -->
            <div class="swipeable">
                <img src="https://vervigo.loca.lt/<?php echo htmlspecialchars($event['image']); ?>" alt="Reklam Görseli" class="uploaded-image">
                <h3><?php echo htmlspecialchars($event['title']); ?></h3>
                <p><?php echo htmlspecialchars($event['description']); ?></p>
                <p><strong>Konum:</strong> <?php echo htmlspecialchars($event['location']); ?></p>
                <p><small><?php echo htmlspecialchars($event['date']); ?></small></p>

                <?php if (
                    ($role === 'owner' && isset($event['owned_by_me']) && $event['owned_by_me']) ||
                    $role === 'admin'
                ): ?>
                    <form method="post" action="delete_ad.php" style="margin-top: 10px;">
                        <input type="hidden" name="id" value="<?php echo $event['id']; ?>">
                        <button class="button" type="submit" style="background-color:#dc3545;">Reklamı Sil</button>
                    </form>
                <?php endif; ?>
            </div>

        </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p>Henüz reklam yok.</p>
    <?php endif; ?>

    <?php if ($role === 'admin' && $queue): ?>
        <h2>Onay Bekleyen Reklam ve Restoranlar</h2>

        <h3>Reklamlar</h3>
        <?php foreach ($queue['events'] as $e): ?>
            <div class="card">
                <strong><?php echo htmlspecialchars($e['title']); ?></strong>
                <form method="post" action="admin_approve.php">
                    <input type="hidden" name="type" value="event">
                    <input type="hidden" name="id" value="<?php echo $e['id']; ?>">
                    <button class="button" name="action" value="approve">Onayla</button>
                    <button class="button" name="action" value="reject">Reddet</button>
                </form>
            </div>
        <?php endforeach; ?>

        <h3>Restoranlar</h3>
        <?php foreach ($queue['businesses'] as $b): ?>
            <div class="card">
                <strong><?php echo htmlspecialchars($b['name']); ?></strong>
                <form method="post" action="admin_approve.php">
                    <input type="hidden" name="type" value="business">
                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                    <button class="button" name="action" value="approve">Onayla</button>
                    <button class="button" name="action" value="reject">Reddet</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<script>
document.querySelectorAll('.swipeable-wrapper').forEach(wrapper => {
    const card = wrapper.querySelector('.swipeable');
    let startX = 0;
    let isDown = false;

    wrapper.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.clientX;
        card.style.transition = "none";
    });

    wrapper.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        const deltaX = e.clientX - startX;
        if (deltaX > 0) {
            card.style.transform = `translateX(${deltaX}px)`;
        }
    });

    wrapper.addEventListener('mouseup', (e) => {
        isDown = false;
        const deltaX = e.clientX - startX;
        card.style.transition = "transform 0.3s ease";

        if (deltaX > 100) {
            const eventId = wrapper.getAttribute('data-event-id');
            window.location.href = `satinal.php?id=${eventId}`;
        } else {
            card.style.transform = "translateX(0)";
        }
    });

    wrapper.addEventListener('mouseleave', () => {
        isDown = false;
        card.style.transform = "translateX(0)";
    });
});
</script>
</body>
</html> 