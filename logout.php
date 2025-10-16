<?php
// logout.php — ออกจากระบบแล้วกลับหน้าแรก

// ใช้ชื่อ session ให้ตรงกับที่ตั้งใน header.php
session_name('HOTELSESSID');

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// ล้างตัวแปรใน session
$_SESSION = [];

// ลบคุกกี้ของ session
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $p['path'] ?? '/',
    $p['domain'] ?? '',
    $p['secure'] ?? false,
    $p['httponly'] ?? true
  );
}

// ทำลาย session ฝั่งเซิร์ฟเวอร์
session_destroy();

// กลับหน้าแรก
header('Location: index.php');
exit;
