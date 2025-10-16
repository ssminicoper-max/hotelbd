<?php
// ==========================================
//  reports_owner.php — รายงานสำหรับเจ้าของหอ
//  ใช้ร่วมกับระบบล็อกอินที่มีอยู่แล้วใน header.php
// ==========================================

$title  = 'รายงานเจ้าของ';
$active = 'owner_reports';

require __DIR__ . '/conn.php';
include __DIR__ . '/header.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// ✅ ตรวจสอบสิทธิ์เฉพาะเจ้าของหอเท่านั้น
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
  header('Location: login.php');
  exit;
}

// ดึง ID ของเจ้าของหอจาก session
$owner_id = $_SESSION['user_id'];

// ช่วงวันที่ (สำหรับกรองประกาศใหม่)
$start = $_GET['start_date'] ?? date('Y-m-01');
$end   = $_GET['end_date']   ?? date('Y-m-d');

// ดึงชื่อเจ้าของ
$stmt = $mysqli->prepare("SELECT id, name FROM users WHERE id=? LIMIT 1");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$me = $stmt->get_result()->fetch_assoc();

// การ์ดสรุปภาพรวม
$stmt = $mysqli->prepare("
  SELECT COUNT(*) total_all,
         SUM(status='active') total_active,
         ROUND(AVG(price),0) avg_price
  FROM listings
  WHERE owner_id=?
");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$overview = $stmt->get_result()->fetch_assoc();

// รายงาน 1: สรุปตามพื้นที่
$stmt = $mysqli->prepare("
  SELECT area,
         COUNT(*) total_listings,
         SUM(status='active') active_listings,
         ROUND(AVG(price),0) avg_price
  FROM listings
  WHERE owner_id=?
  GROUP BY area
  ORDER BY total_listings DESC
");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$area_rs = $stmt->get_result();

// รายงาน 2: สรุปตามช่วงราคา
$stmt = $mysqli->prepare("
  SELECT
    SUM(CASE WHEN price<=3000 THEN 1 ELSE 0 END) le_3000,
    SUM(CASE WHEN price BETWEEN 3001 AND 5000 THEN 1 ELSE 0 END) b_3001_5000,
    SUM(CASE WHEN price BETWEEN 5001 AND 8000 THEN 1 ELSE 0 END) b_5001_8000,
    SUM(CASE WHEN price>8000 THEN 1 ELSE 0 END) gt_8000
  FROM listings
  WHERE owner_id=?
");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$bk = $stmt->get_result()->fetch_assoc();

// รายงาน 3: ประกาศใหม่ของฉันในช่วงวันที่
$stmt = $mysqli->prepare("
  SELECT id, title, area, type, price, DATE(created_at) created_at, status
  FROM listings
  WHERE owner_id=? AND DATE(created_at) BETWEEN ? AND ?
  ORDER BY created_at DESC
");
$stmt->bind_param('iss', $owner_id, $start, $end);
$stmt->execute();
$new_rs = $stmt->get_result();
?>

<div class="container" style="max-width: 1000px; margin: 24px auto; padding: 0 16px;">
  <h1 style="margin: 8px 0 12px;">รายงานสำหรับเจ้าของหอ</h1>
  <p class="muted">เจ้าของ: <b><?= htmlspecialchars($me['name'] ?? 'ไม่ทราบชื่อ') ?></b></p>

  <!-- การ์ดภาพรวม -->
  <div class="grid" style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:12px 0 18px;">
    <div class="card" style="padding:12px;">
      <div class="muted">จำนวนประกาศทั้งหมด</div>
      <div style="font-size:22px;font-weight:700;"><?= number_format((int)$overview['total_all']) ?></div>
    </div>
    <div class="card" style="padding:12px;">
      <div class="muted">สถานะ Active</div>
      <div style="font-size:22px;font-weight:700;"><?= number_format((int)$overview['total_active']) ?></div>
    </div>
    <div class="card" style="padding:12px;">
      <div class="muted">ราคาเฉลี่ย (บาท/เดือน)</div>
      <div style="font-size:22px;font-weight:700;"><?= number_format((int)$overview['avg_price']) ?></div>
    </div>
  </div>

  <!-- สรุปตามพื้นที่ -->
  <div class="card" style="padding:12px;margin:12px 0;">
    <h3>สรุปตามพื้นที่</h3>
    <table class="table" style="width:100%;border-collapse:collapse;">
      <thead><tr><th>พื้นที่</th><th>จำนวน</th><th>Active</th><th>ราคาเฉลี่ย</th></tr></thead>
      <tbody>
        <?php if ($area_rs->num_rows): ?>
          <?php while ($r = $area_rs->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($r['area']) ?></td>
              <td><?= $r['total_listings'] ?></td>
              <td><?= $r['active_listings'] ?></td>
              <td><?= number_format((int)$r['avg_price']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4">ไม่มีข้อมูล</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- สรุปตามช่วงราคา -->
  <div class="card" style="padding:12px;margin:12px 0;">
    <h3>สรุปตามช่วงราคา (บาท/เดือน)</h3>
    <?php
      $b1=(int)($bk['le_3000']??0);
      $b2=(int)($bk['b_3001_5000']??0);
      $b3=(int)($bk['b_5001_8000']??0);
      $b4=(int)($bk['gt_8000']??0);
    ?>
    <table class="table" style="width:100%;border-collapse:collapse;">
      <thead><tr><th>ช่วงราคา</th><th>จำนวน</th></tr></thead>
      <tbody>
        <tr><td>≤ 3,000</td><td><?= $b1 ?></td></tr>
        <tr><td>3,001–5,000</td><td><?= $b2 ?></td></tr>
        <tr><td>5,001–8,000</td><td><?= $b3 ?></td></tr>
        <tr><td>> 8,000</td><td><?= $b4 ?></td></tr>
      </tbody>
    </table>
  </div>

  <!-- ประกาศใหม่ของฉัน -->
  <div class="card" style="padding:12px;margin:12px 0;">
    <h3>ประกาศใหม่ของฉัน (ช่วงวันที่)</h3>
    <form method="get" class="form" style="display:flex;gap:8px;align-items:center;margin:8px 0 12px;">
      <label>ตั้งแต่ <input type="date" name="start_date" value="<?= htmlspecialchars($start) ?>"></label>
      <label>ถึง <input type="date" name="end_date" value="<?= htmlspecialchars($end) ?>"></label>
      <button class="btn">กรอง</button>
      <a class="btn" href="reports_owner.php" style="margin-left:6px">ล้าง</a>
    </form>

    <table class="table" style="width:100%;border-collapse:collapse;">
      <thead><tr>
        <th>ID</th><th>ชื่อประกาศ</th><th>พื้นที่</th><th>ประเภท</th>
        <th>ราคา</th><th>วันที่ลง</th><th>สถานะ</th>
      </tr></thead>
      <tbody>
        <?php if ($new_rs->num_rows): ?>
          <?php while ($r = $new_rs->fetch_assoc()): ?>
            <tr>
              <td><?= $r['id'] ?></td>
              <td><a href="listing_view.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['title']) ?></a></td>
              <td><?= htmlspecialchars($r['area']) ?></td>
              <td><?= htmlspecialchars($r['type']) ?></td>
              <td><?= number_format((int)$r['price']) ?></td>
              <td><?= $r['created_at'] ?></td>
              <td><?= htmlspecialchars($r['status']) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="7">ไม่มีข้อมูลในช่วงวันที่ที่เลือก</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
