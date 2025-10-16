<?php
// owner_dashboard.php
$title = 'แดชบอร์ดเจ้าของหอ'; $active = 'owner_dashboard';
include __DIR__.'/header.php';
include __DIR__.'/conn.php';

// ต้องเป็นเจ้าของหอเท่านั้น
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'owner') {
  header('Location: login.php'); exit;
}
$owner_id = (int)$_SESSION['user_id'];

/* ===== สรุปประกาศของฉัน ===== */
$tot_listings = 0; 
$active_list  = 0;

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM listings WHERE owner_id = ?");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$tot_listings = (int)$stmt->get_result()->fetch_row()[0];

$stmt = $mysqli->prepare("SELECT COUNT(*) FROM listings WHERE owner_id = ? AND status = 'active'");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$active_list = (int)$stmt->get_result()->fetch_row()[0];

/* ===== สรุปคำขอจอง (JOIN กับ listings) ===== */
$pending_bk  = 0; 
$approved_bk = 0; 
$recent      = [];

// มีตาราง bookings ไหม
$hasBookings = $mysqli->query("SHOW TABLES LIKE 'bookings'")->num_rows > 0;

if ($hasBookings) {
  // pending
  $stmt = $mysqli->prepare("
    SELECT COUNT(*)
    FROM bookings b
    JOIN listings l ON l.id = b.listing_id
    WHERE l.owner_id = ? AND b.status = 'pending'
  ");
  $stmt->bind_param('i', $owner_id);
  $stmt->execute();
  $pending_bk = (int)$stmt->get_result()->fetch_row()[0];

  // approved
  $stmt = $mysqli->prepare("
    SELECT COUNT(*)
    FROM bookings b
    JOIN listings l ON l.id = b.listing_id
    WHERE l.owner_id = ? AND b.status = 'approved'
  ");
  $stmt->bind_param('i', $owner_id);
  $stmt->execute();
  $approved_bk = (int)$stmt->get_result()->fetch_row()[0];

  // รายการล่าสุด (ไม่มีคอลัมน์ checkin/checkout ในตาราง → ใส่ NULL เป็น alias)
  $stmt = $mysqli->prepare("
    SELECT 
      b.id, 
      b.created_at, 
      b.status, 
      l.title,
      NULL AS checkin_date,
      NULL AS checkout_date
    FROM bookings b
    JOIN listings l ON l.id = b.listing_id
    WHERE l.owner_id = ?
    ORDER BY b.created_at DESC
    LIMIT 6
  ");
  $stmt->bind_param('i', $owner_id);
  $stmt->execute();
  $recent = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<main class="container">

  <h1 class="title">สวัสดีครับ/ค่ะ, <?= htmlspecialchars($_SESSION['name'] ?? '') ?></h1>
  <p class="muted">นี่คือภาพรวมสำหรับเจ้าของหอ — จัดการประกาศและคำขอจองได้จากที่นี่</p>

  <!-- สรุปตัวเลข -->
  <div class="listings-grid" style="grid-template-columns:repeat(4,1fr);">
    <div class="card" style="border-left:4px solid #0ea5e9">
      <div class="muted">ประกาศทั้งหมด</div>
      <div style="font-size:28px;font-weight:800;"><?= (int)$tot_listings ?></div>
    </div>
    <div class="card" style="border-left:4px solid #22c55e">
      <div class="muted">ประกาศเปิดอยู่</div>
      <div style="font-size:28px;font-weight:800;"><?= (int)$active_list ?></div>
    </div>
    <div class="card" style="border-left:4px solid #f59e0b">
      <div class="muted">คำขอจองรอดำเนินการ</div>
      <div style="font-size:28px;font-weight:800;"><?= (int)$pending_bk ?></div>
    </div>
    <div class="card" style="border-left:4px solid #10b981">
      <div class="muted">จองที่ยืนยันแล้ว (ล่าสุด)</div>
      <div style="font-size:28px;font-weight:800;"><?= (int)$approved_bk ?></div>
    </div>
  </div>

  <!-- ปุ่มลัด -->
  <div class="row v-gap" style="margin-top:14px">
    <a class="btn" href="listing_new.php">+ ลงประกาศใหม่</a>
    <a class="btn ghost" href="owner_listings.php">จัดการประกาศของฉัน</a>
    <a class="btn ghost" href="owner_bookings.php">จัดการคำขอจอง</a>
    <a class="btn ghost" href="upload_images.php">อัปโหลดรูปเข้าประกาศ</a>
  </div>

  <!-- ล่าสุด -->
  <section class="section">
    <div class="section-head">
      <h2>คำขอจองล่าสุด</h2>
      <a class="link" href="owner_bookings.php">ดูทั้งหมด →</a>
    </div>
    <?php if(empty($recent)): ?>
      <div class="card">ยังไม่มีคำขอจอง</div>
    <?php else: ?>
      <div class="card" style="padding:0">
        <table style="width:100%;border-collapse:collapse">
          <thead>
            <tr style="background:#f7fbff">
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid #eef6ff">รายการ</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid #eef6ff">ช่วงวันที่</th>
              <th style="text-align:left;padding:10px 12px;border-bottom:1px solid #eef6ff">สถานะ</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($recent as $r): ?>
              <tr>
                <td style="padding:10px 12px;border-bottom:1px solid #f0f6ff">
                  #<?= (int)$r['id'] ?> — <?= htmlspecialchars($r['title']) ?>
                  <div class="muted" style="font-size:12px">ร้องขอเมื่อ <?= htmlspecialchars($r['created_at']) ?></div>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid #f0f6ff">
                  <?= htmlspecialchars($r['checkin_date'] ?? '-') ?> ถึง <?= htmlspecialchars($r['checkout_date'] ?? '-') ?>
                </td>
                <td style="padding:10px 12px;border-bottom:1px solid #f0f6ff">
                  <?php
                    $st = $r['status'];
                    $badge = ['pending'=>'#f59e0b','approved'=>'#10b981','rejected'=>'#ef4444'][$st] ?? '#94a3b8';
                  ?>
                  <span style="background:<?= $badge ?>1a;color:<?= $badge ?>;padding:4px 8px;border-radius:8px;font-weight:700">
                    <?= htmlspecialchars($st) ?>
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </section>

</main>
<?php include __DIR__.'/footer.php'; ?>
