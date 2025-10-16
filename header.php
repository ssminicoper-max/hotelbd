<?php
// ===== header.php: ตั้งค่า session ต้องอยู่บนสุดเสมอ =====
session_name('HOTELSESSID'); // ตั้งชื่อ session เฉพาะโปรเจกต์

session_set_cookie_params([
  'lifetime' => 0,        // ปิดเว็บแล้วหลุดระบบ
  'path'     => '/hotel', // เว็บอยู่ในโฟลเดอร์ /hotel
  'httponly' => true,
  'samesite' => 'Lax',
  // 'secure' => true,     // เปิดถ้าใช้ https
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// ออกอัตโนมัติถ้าไม่ขยับเกิน 30 นาที
$IDLE_LIMIT = 30 * 60;
if (isset($_SESSION['LAST_ACTIVE']) && time() - $_SESSION['LAST_ACTIVE'] > $IDLE_LIMIT) {
  $_SESSION = [];
  if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'] ?? '', $p['secure'] ?? false, $p['httponly'] ?? true);
  }
  session_destroy();
  header('Location: login.php');
  exit;
}
$_SESSION['LAST_ACTIVE'] = time();

// ===== helpers =====

// base path สำหรับลิงก์ (ถ้าไฟล์คุณเคยตั้ง $base ไว้แล้ว บรรทัดนี้จะไม่ทับของเดิม)
if (!isset($base)) {
  $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
  if ($base === '.' || $base === DIRECTORY_SEPARATOR) $base = '';
}

// ฟังก์ชันเช็ค active tab (ถ้าไฟล์ตั้ง $active ไว้ เช่น $active='home')
if (!function_exists('is_active')) {
  function is_active($key, $active = null) {
    if ($active === null && isset($GLOBALS['active'])) $active = $GLOBALS['active'];
    return ($active === $key) ? 'active' : '';
  }
}
$is_active = fn($k) => is_active($k, $active ?? null);

// ตัวแปรผู้ใช้จาก session
$user_id = $_SESSION['user_id'] ?? null;
$user    = !empty($user_id);
$role    = $_SESSION['role'] ?? '';
$name    = $_SESSION['name'] ?? 'ผู้ใช้';
?>
<!doctype html>
<html lang="th">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($title ?? 'DormFinder') ?> · DormFinder</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $base ?>/style.css" />
  <script>
    function toggleNav(){
      var el = document.getElementById('main-nav');
      if(el) el.classList.toggle('open');
    }
  </script>
  <script defer src="<?= $base ?>/app.js"></script>
</head>
<body>
  <header class="topbar">
    <div class="container row between center">
      <a class="brand" href="<?= $base ?>/index.php">near RMUTT</a>

      <button class="nav-toggle" aria-label="Toggle navigation" onclick="toggleNav()">☰</button>

      <nav class="nav" id="main-nav">
        <a class="nav-link <?= $is_active('home'); ?>" href="<?= $base ?>/index.php">หน้าหลัก</a>
        <a class="nav-link <?= $is_active('listings'); ?>" href="<?= $base ?>/listings.php">ค้นหาหอ</a>

        <?php if ($user): ?>
          <?php if ($role === 'owner'): ?>
            <a class="nav-link <?= $is_active('owner_dashboard'); ?>" href="<?= $base ?>/owner_dashboard.php">จัดการหอพักของฉัน</a>
            <a class="nav-link <?= $is_active('listing_new'); ?>" href="<?= $base ?>/listing_new.php">ลงประกาศ</a>
            <a class="nav-link <?= $is_active('owner_reports'); ?>" href="<?= $base ?>/reports_owner.php">รายงาน</a>
          <?php endif; ?>
            <?php if ($role === 'tenant'): ?>
    <a class="nav-link <?= $is_active('bookings'); ?>" href="<?= $base ?>/bookings.php">การจองของฉัน</a>
  <?php endif; ?>


          <span class="nav-sep"></span>
          <a class="nav-link" href="<?= $base ?>/logout.php">ออกจากระบบ</a>
        <?php else: ?>
          <span class="nav-sep"></span>
          <a class="nav-link <?= $is_active('login'); ?>" href="<?= $base ?>/login.php">เข้าสู่ระบบ</a>
          <a class="nav-link <?= $is_active('register'); ?>" href="<?= $base ?>/register.php">สมัครสมาชิก</a>
        <?php endif; ?>
      </nav>
    </div>
  </header>
