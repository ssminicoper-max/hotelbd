<?php
// auth.php — mock system สำหรับเช็คการล็อกอินเบื้องต้น

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// ถ้าไม่มี user_id ใน session ให้จำลองเป็นเจ้าของ (owner)
if (!isset($_SESSION['user_id'])) {
  $_SESSION['user_id'] = 1;        // สมมติ id เจ้าของ = 1
  $_SESSION['role'] = 'owner';     // กำหนดบทบาทเป็นเจ้าของ
  $_SESSION['name'] = 'เจ้าของตัวอย่าง'; // ชื่อผู้ใช้จำลอง
}

// ฟังก์ชันตรวจล็อกอิน
function require_login() {
  if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
  }
}

// ฟังก์ชันดึง id ผู้ใช้ปัจจุบัน
function current_user_id() {
  return $_SESSION['user_id'] ?? null;
}
?>
