<?php
// booking_create.php — ผู้เช่าส่งคำขอจอง
include __DIR__.'/conn.php';

if (session_status()!==PHP_SESSION_ACTIVE) session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'tenant') {
  header('Location: login.php'); exit;
}

$tenant_id = (int)$_SESSION['user_id'];
$listing_id = (int)($_POST['listing_id'] ?? 0);
$note = trim($_POST['note'] ?? '');

if ($listing_id <= 0) {
  header('Location: index.php'); exit;
}

// กันจองซ้ำซ้อน (optional): ถ้าเคยมี pending สำหรับ listing นี้โดยคนเดิม ให้ไม่สร้างซ้ำ
$chk = $mysqli->prepare("SELECT 1 FROM bookings WHERE listing_id=? AND tenant_id=? AND status='pending' LIMIT 1");
$chk->bind_param('ii', $listing_id, $tenant_id);
$chk->execute();
if ($chk->get_result()->fetch_row()) {
  header("Location: bookings.php?msg=already"); exit;
}

// สร้างคำขอจอง
$ins = $mysqli->prepare("INSERT INTO bookings(listing_id, tenant_id, note) VALUES (?,?,?)");
$ins->bind_param('iis', $listing_id, $tenant_id, $note);
$ins->execute();

// กลับไปหน้า “การจองของฉัน”
header('Location: bookings.php?msg=ok');
exit;
