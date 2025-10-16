<?php
$title='Home'; $active='home';
include __DIR__ . '/header.php';
include __DIR__ . '/conn.php';

$q   = trim($_GET['q'] ?? '');
$min = (float)($_GET['min'] ?? 0);
$max = (float)($_GET['max'] ?? 0);

$sql = "SELECT id,title,area,type,price FROM listings WHERE status='active'";
$params = []; $types = '';

if ($q !== '') {
  $sql .= " AND (title LIKE CONCAT('%',?,'%') OR area LIKE CONCAT('%',?,'%') OR type LIKE CONCAT('%',?,'%'))";
  $types.='sss'; array_push($params,$q,$q,$q);
}
if ($min>0) { $sql .= " AND price >= ?"; $types.='d'; $params[]=$min; }
if ($max>0) { $sql .= " AND price <= ?"; $types.='d'; $params[]=$max; }

$sql .= " ORDER BY id DESC LIMIT 6";
$stmt=$mysqli->prepare($sql);
if($types!==''){ $stmt->bind_param($types, ...$params); }
$stmt->execute(); $res=$stmt->get_result(); $latest = $res->fetch_all(MYSQLI_ASSOC);
?>
<main class="container">

  <!-- กล่องค้นหา -->
  <section class="card">
    <h1 class="title">ค้นหาหอพักใกล้ RMUTT</h1>
    <form class="form row v-gap" method="get" action="listings.php">
      <input class="grow" name="q"  placeholder="ค้นหาด้วยชื่อ/พื้นที่/ประเภท" value="<?= htmlspecialchars($q) ?>">
      <input name="min" type="number" step="0.01" placeholder="ราคาเริ่ม (฿)"  value="<?= $min ?: '' ?>">
      <input name="max" type="number" step="0.01" placeholder="ราคาสูงสุด (฿)" value="<?= $max ?: '' ?>">
      <button class="btn" type="submit">ค้นหา</button>
    </form>
  </section>

  
    <!-- รายการล่าสุด -->
  <h2 class="mt">หอพักแนะนำ</h2>
  <div class="listings-grid">
    <?php foreach ($latest as $L): ?>
      <?php
        // รูปแรกของประกาศ (ถ้าไม่มี ใช้รูปสำรอง)
        $imgStmt = $mysqli->prepare("SELECT filename FROM images WHERE listing_id=? ORDER BY id ASC LIMIT 1");
        $imgStmt->bind_param('i', $L['id']);
        $imgStmt->execute();
        $thumb = $imgStmt->get_result()->fetch_column();
        $thumb = $thumb ? str_replace('\\','/',$thumb) : 'images/default_dorm.jpg';
        $abs = __DIR__ . '/' . $thumb;
        if (!is_file($abs)) $thumb = 'images/default_dorm.jpg';
      ?>
      <article class="card listing-card">
        <img class="listing-thumb" src="<?= htmlspecialchars($thumb) ?>" alt="">
        <div class="listing-body">
          <h3 class="listing-title"><?= htmlspecialchars($L['title']) ?></h3>
          <div class="listing-meta">พื้นที่: <?= htmlspecialchars($L['area']) ?> · ประเภท: <?= htmlspecialchars($L['type']) ?></div>
        </div>
        <div class="listing-footer">
          <span class="price-chip">฿<?= number_format($L['price'],0) ?>/ด.</span>
          <a class="btn small" href="listing_view.php?id=<?= (int)$L['id'] ?>">ดูรายละเอียด</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>


</main>
<?php include __DIR__ . '/footer.php'; ?>
