<?php
// listing_view.php (ปรับปรุง: รายละเอียดเพิ่มเติม + ช่องทางติดต่อ + แผนที่)
$title='รายละเอียดหอ';
include __DIR__.'/header.php';
include __DIR__.'/conn.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: index.php'); exit; }

// ดึงข้อมูลประกาศ (รวมฟิลด์ติดต่อ/รายละเอียดเพิ่มเติม/แผนที่)
$stmt = $mysqli->prepare("
  SELECT
    L.id, L.title, L.area, L.type, L.price, L.description, L.owner_id,
    L.deposit, L.electricity_rate, L.water_rate, L.other_fee, L.internet,
    L.details_long,              -- รายละเอียดยาว (ใหม่)
    L.contact_phone, L.contact_line, L.contact_facebook, L.contact_email, -- ช่องทางติดต่อ (ใหม่)
    L.map_url,                   -- ลิงก์แผนที่ (ใหม่)
    U.name AS owner_name
  FROM listings L
  JOIN users U ON U.id=L.owner_id
  WHERE L.id=? AND L.status='active'
  LIMIT 1
");
$stmt->bind_param('i',$id);
$stmt->execute();
$listing = $stmt->get_result()->fetch_assoc();
if(!$listing){
  echo '<main class="container"><div class="card">ไม่พบประกาศนี้</div></main>';
  include __DIR__.'/footer.php'; exit;
}

// รูปภาพ
$imgs = [];
$imgq = $mysqli->prepare("SELECT filename FROM images WHERE listing_id=? ORDER BY id ASC");
$imgq->bind_param('i',$id); $imgq->execute();
$imgs = $imgq->get_result()->fetch_all(MYSQLI_ASSOC);

// ผู้เช่า favorites
$isFav = false;
if (!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '')==='tenant') {
  $chk=$mysqli->prepare("SELECT 1 FROM favorites WHERE user_id=? AND listing_id=?");
  $chk->bind_param('ii', $_SESSION['user_id'], $id); $chk->execute();
  $isFav = (bool)$chk->get_result()->fetch_row();
}

// สิ่งอำนวยความสะดวก (ถ้ามี)
$amenityCodes = [];
if ($mysqli->query("SHOW TABLES LIKE 'listing_amenities'")->num_rows) {
  if ($aq = $mysqli->prepare("SELECT code FROM listing_amenities WHERE listing_id=?")) {
    $aq->bind_param('i',$id); $aq->execute();
    $amenityCodes = array_column($aq->get_result()->fetch_all(MYSQLI_ASSOC),'code');
  }
}

// helpers
function showv($v, $unit = '') {
  if ($v === null || $v === '') return '—';
  if (is_numeric($v) && $unit === 'บาท') return number_format((float)$v,2).' บาท';
  return htmlspecialchars((string)$v);
}
function image_url_safe($rel){
  $rel = ltrim($rel ?? '', '/');
  $abs = __DIR__ . '/' . $rel;
  return ($rel && is_file($abs)) ? $rel : 'images/default_dorm.jpg';
}
function auto_link_nl2br($text){
  $safe = htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
  $safe = preg_replace('~(https?://[^\s<]+)~i', '<a href="$1" target="_blank" rel="noopener">$1</a>', $safe);
  return nl2br($safe);
}
?>
<main class="container">
  <div class="row v-gap" style="align-items:flex-start">
    <div class="grow">
      <h1 class="title" style="margin-bottom:6px;"><?= htmlspecialchars($listing['title']) ?></h1>
      <div class="muted">พื้นที่: <?= htmlspecialchars($listing['area']) ?> · ประเภท: <?= htmlspecialchars($listing['type']) ?></div>

      <?php if($imgs): ?>
  <!-- รูปหลัก -->
  <img id="main-img"
       src="<?= htmlspecialchars(image_url_safe($imgs[0]['filename'])) ?>"
       alt=""
       style="width:100%;max-height:450px;object-fit:cover;border-radius:14px;border:1px solid var(--border);margin-bottom:10px;transition:opacity .2s ease;">

  <!-- รูปย่อย -->
  <?php if(count($imgs)>1): ?>
  <div id="thumb-row" style="display:flex;gap:8px;overflow-x:auto;">
    <?php foreach (array_slice($imgs,1) as $im): $url=image_url_safe($im['filename']); ?>
      <img src="<?= htmlspecialchars($url) ?>" alt=""
           class="thumb"
           style="flex:0 0 auto;width:160px;height:100px;object-fit:cover;border-radius:10px;border:2px solid transparent;cursor:pointer;">
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
<?php endif; ?>



      <div class="row between center" style="margin:12px 0">
        <strong class="price-chip" style="font-size:20px">฿<?= number_format($listing['price'],0) ?>/ด.</strong>
        <span class="muted">โดย เจ้าของ – <?= htmlspecialchars($listing['owner_name']) ?></span>
      </div>

      <!-- สรุป/คำอธิบายสั้น (เดิม) -->
      <?php if($listing['description']): ?>
        <div class="card" style="white-space:pre-wrap"><?= nl2br(htmlspecialchars($listing['description'])) ?></div>
      <?php endif; ?>

      <!-- รายละเอียดเพิ่มเติม (ใหม่ แบบยาวเหมือนภาพ 2) -->
      <?php if(!empty($listing['details_long'])): ?>
        <div class="card" style="margin-top:14px; line-height:1.7">
          <h3 style="margin:0 0 8px;">รายละเอียดเพิ่มเติม</h3>
          <div class="richtext"><?= auto_link_nl2br($listing['details_long']) ?></div>
        </div>
      <?php endif; ?>

      <!-- ปุ่ม -->
      <div class="row" style="gap:10px; margin-top:16px;">
        <?php if(!empty($_SESSION['user_id']) && ($_SESSION['role'] ?? '')==='tenant'): ?>
          <a class="btn" href="booking_new.php?listing_id=<?= (int)$listing['id'] ?>">จอง/นัดดูห้อง</a>
        <?php else: ?>
          <a class="btn" href="login.php">เข้าสู่ระบบเพื่อบันทึก/จอง</a>
        <?php endif; ?>
      </div>

      <!-- แผนที่ (ใหม่) -->
      <?php if(!empty($listing['map_url'])): ?>
        <div class="card" style="margin-top:14px;">
          <h3 style="margin:0 0 8px;">แผนที่</h3>
          <a class="btn" href="<?= htmlspecialchars($listing['map_url']) ?>" target="_blank" rel="noopener">ดูบน Google Maps</a>
        </div>
      <?php endif; ?>
    </div>

    <!-- กล่องขวา: ค่าใช้จ่าย + ติดต่อ -->
    <aside class="card" style="width: 800px;px;max-width:100%;">
      <div class="muted">ค่าเช่า :</div>
      <div style="font-size:28px;font-weight:800;">
        ฿<?= number_format($listing['price'],0) ?><span class="muted" style="font-size:14px;"> /เดือน</span>
      </div>

      <div class="card" style="margin-top:12px">
        <table style="width:100%;border-collapse:collapse">
          <tbody>
            <tr><td class="muted" style="padding:6px 0">พื้นที่</td>
                <td style="text-align:right;padding:6px 0"><?= htmlspecialchars($listing['area']) ?></td></tr>
            <tr><td class="muted" style="padding:6px 0">ประเภท</td>
                <td style="text-align:right;padding:6px 0"><?= htmlspecialchars($listing['type']) ?></td></tr>
          </tbody>
        </table>
      </div>

      <!-- ตารางค่าใช้จ่าย -->
      <div class="card" style="margin: top 20px;px">
        <table style="width:100%;border-collapse:collapse">
          <tbody>
            <tr>
              <td class="muted" style="padding:6px 0">เงินประกัน</td>
              <td style="text-align:right;padding:6px 0"><?= showv($listing['deposit'], 'บาท') ?></td>
            </tr>
            <tr>
              <td class="muted" style="padding:6px 0">ค่าไฟ</td>
              <td style="text-align:right;padding:6px 0"><?= showv($listing['electricity_rate']) ?></td>
            </tr>
            <tr>
              <td class="muted" style="padding:6px 0">ค่าน้ำ</td>
              <td style="text-align:right;padding:6px 0"><?= showv($listing['water_rate']) ?></td>
            </tr>
            <tr>
              <td class="muted" style="padding:6px 0">ค่าบริการอื่นๆ</td>
              <td style="text-align:right;padding:6px 0"><?= showv($listing['other_fee'], 'บาท') ?></td>
            </tr>
            <tr>
              <td class="muted" style="padding:6px 0">อินเทอร์เน็ต</td>
              <td style="text-align:right;padding:6px 0"><?= showv($listing['internet']) ?></td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- ช่องทางติดต่อเจ้าของหอ (ใหม่) -->
      <div class="card" style="margin-top:12px">
        <h3 style="margin:0 0 8px;">ช่องทางติดต่อเจ้าของหอ</h3>
        <ul style="list-style:none;padding:0;margin:0;display:grid;gap:8px;">
          <?php if(!empty($listing['contact_phone'])): ?>
            <li><a class="btn full" href="tel:<?=preg_replace('/\D+/','',$listing['contact_phone'])?>">โทร: <?=htmlspecialchars($listing['contact_phone'])?></a></li>
          <?php endif; ?>
          <?php if(!empty($listing['contact_line'])): ?>
            <li><a class="btn full ghost" href="https://line.me/ti/p/~<?=urlencode($listing['contact_line'])?>" target="_blank">LINE: <?=htmlspecialchars($listing['contact_line'])?></a></li>
          <?php endif; ?>
          <?php if(!empty($listing['contact_facebook'])): ?>
            <li><a class="btn full ghost" href="<?=htmlspecialchars($listing['contact_facebook'])?>" target="_blank">Facebook</a></li>
          <?php endif; ?>
          <?php if(!empty($listing['contact_email'])): ?>
            <li><a class="btn full ghost" href="mailto:<?=htmlspecialchars($listing['contact_email'])?>">อีเมล: <?=htmlspecialchars($listing['contact_email'])?></a></li>
          <?php endif; ?>
          <?php if(empty($listing['contact_phone']) && empty($listing['contact_line']) && empty($listing['contact_facebook']) && empty($listing['contact_email'])): ?>
            <li class="muted">ยังไม่มีข้อมูลช่องทางติดต่อ</li>
          <?php endif; ?>
        </ul>
      </div>
    </aside>
  </div>
  <script>
document.addEventListener('DOMContentLoaded', function() {
  const mainImg = document.getElementById('main-img');
  const thumbs = document.querySelectorAll('.thumb');

  thumbs.forEach(img => {
    img.addEventListener('click', () => {
      if (!mainImg) return;
      // เอฟเฟกต์ fade สั้น ๆ
      mainImg.style.opacity = 0;
      const newSrc = img.getAttribute('src');
      setTimeout(() => {
        mainImg.src = newSrc;
        mainImg.onload = () => { mainImg.style.opacity = 1; };
      }, 120);

      // ไฮไลต์รูปที่เลือก
      thumbs.forEach(t => t.style.borderColor = 'transparent');
      img.style.borderColor = '#1e66ff';
    });
  });
});
</script>

</main>
<?php include __DIR__.'/footer.php'; ?>
