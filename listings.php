<?php
$title='Listings'; $active='listings';
include __DIR__ . '/header.php';
include __DIR__ . '/conn.php';

// รับค่าแบบยืดหยุ่น (เว้นว่างได้)
$q    = trim($_GET['q'] ?? '');
$min  = ($_GET['min'] ?? '') !== '' ? (float)$_GET['min'] : null;
$max  = ($_GET['max'] ?? '') !== '' ? (float)$_GET['max'] : null;

$sql   = "SELECT id,title,area,type,price FROM listings WHERE status='active'";
$conds = [];
$params= [];
$types = '';

// คีย์เวิร์ด: ค้นชื่อ/พื้นที่/ประเภท/คำอธิบาย
if ($q !== '') {
  $conds[] = "(title LIKE ? OR area LIKE ? OR type LIKE ? OR description LIKE ?)";
  $kw = "%$q%";
  $params[] = $kw; $params[] = $kw; $params[] = $kw; $params[] = $kw;
  $types .= 'ssss';
}

// ราคาเริ่ม/สูงสุด (กรองเมื่อกรอกจริงเท่านั้น)
if ($min !== null) { $conds[] = "price >= ?"; $params[] = $min; $types .= 'd'; }
if ($max !== null) { $conds[] = "price <= ?"; $params[] = $max; $types .= 'd'; }

if ($conds) $sql .= " AND ".implode(" AND ", $conds);
$sql .= " ORDER BY id DESC";

$stmt = $mysqli->prepare($sql) or die('<main class="container"><div class="card">DB error: '.$mysqli->error.'</div></main>');
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="container">
  <h1 class="page-title">ผลการค้นหา</h1>

  <form class="form row v-gap" method="get">
    <input class="grow" name="q"  placeholder="ค้นหาด้วยชื่อ/พื้นที่/ประเภท/คำอธิบาย" value="<?= htmlspecialchars($q) ?>">
    <input name="min" type="number" step="0.01" placeholder="ราคาเริ่ม (฿)" value="<?= $min !== null ? htmlspecialchars($min) : '' ?>">
    <input name="max" type="number" step="0.01" placeholder="ราคาสูงสุด (฿)" value="<?= $max !== null ? htmlspecialchars($max) : '' ?>">
    <button class="btn" type="submit">ค้นหา</button>
    <a class="btn ghost" href="listings.php">ล้าง</a>
  </form>

  <div class="card overflow mt">
    <table class="table">
      <thead>
        <tr><th>#</th><th>ชื่อหอ</th><th>พื้นที่</th><th>ประเภท</th><th class="right">ราคา/ด. (฿)</th><th></th></tr>
      </thead>
      <tbody>
        <?php if(empty($rows)): ?>
          <tr><td colspan="6" class="muted">ไม่พบข้อมูล</td></tr>
        <?php else: foreach($rows as $i=>$L): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($L['title']) ?></td>
            <td><?= htmlspecialchars($L['area']) ?></td>
            <td><?= htmlspecialchars($L['type']) ?></td>
            <td class="right">฿<?= number_format((float)$L['price'], 2) ?></td>
            <td><a class="btn small" href="listing_view.php?id=<?= (int)$L['id'] ?>">ดู</a></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__ . '/footer.php'; ?>
