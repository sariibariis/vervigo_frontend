<?php
session_start();
session_unset();      // Tüm session değişkenlerini sil
session_destroy();    // Oturumu tamamen sonlandır

header("Location: index.php"); // Giriş ekranına yönlendir
exit();
