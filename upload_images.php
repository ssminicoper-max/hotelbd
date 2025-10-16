<?php
// upload_images.php — อัปโหลดรูปเข้าประกาศ (เฉพาะเจ้าของหอ)
$title = 'อัปโหลดรูป'; $active = 'owner';
include __DIR__.'/header.php';
include __DIR__.'/conn.php';

// อนุญาตเฉพาะเจ้าของหอ
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'owner') {
  header('Location: login.php'); exit;
}
$owner_id = (int)$_SESSION['user_id'];

// ดึงประกาศของฉันมาสร้างตัวเลือก
$stmt = $mysqli->prepare("SELECT id, title FROM listings WHERE owner_id=? ORDER BY created_at DESC");
$stmt->bind_param('i', $owner_id);
$stmt->execute();
$my_listings = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$ok = ''; $errs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $listing_id = (int)($_POST['listing_id'] ?? 0);

  // ป้องกันอัปโหลดเข้า listing คนอื่น
  $chk = $mysqli->prepare("SELECT 1 FROM listings WHERE id=? AND owner_id=?");
  $chk->bind_param('ii', $listing_id, $owner_id);
  $chk->execute();
  if (!$chk->get_result()->fetch_row()) {
    $errs[] = 'ประกาศไม่ถูกต้อง หรือไม่ใช่ของคุณ';
  } else {
    // โฟลเดอร์ปลายทาง
    $uploadDir = __DIR__.'/uploads';
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0777, true);

    // เงื่อนไขไฟล์
    $allow = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif'];
    $max   = 5 * 1024 * 1024; // 5MB ต่อไฟล์

    $okCount = 0;

    if (!empty($_FILES['images']) && is_array($_FILES['images']['name'])) {
      $n = count($_FILES['images']['name']);
      for ($i=0; $i<$n; $i++) {
        $tmp  = $_FILES['images']['tmp_name'][$i] ?? '';
        $name = $_FILES['images']['name'][$i] ?? '';
        $size = $_FILES['images']['size'][$i] ?? 0;
        if (!$tmp || !is_uploaded_file($tmp)) continue;

        $mime = @mime_content_type($tmp) ?: ($_FILES['images']['type'][$i] ?? '');
        if (!isset($allow[$mime])) { $errs[] = "$name: ไฟล์ไม่รองรับ"; continue; }
        if ($size > $max)          { $errs[] = "$name: ขนาดเกิน 5MB"; continue; }

        $ext  = $allow[$mime];
        $base = preg_replace('/[^a-zA-Z0-9_\-]/','', pathinfo($name, PATHINFO_FILENAME)) ?: 'img';
        $new  = "L{$listing_id}_".date('Ymd_His').'_'.bin2hex(random_bytes(3))."_{$base}.{$ext}";
        $abs  = $uploadDir.'/'.$new;
        $rel  = 'uploads/'.$new; // เก็บพาธสัมพัทธ์ใน DB

        if (move_uploaded_file($tmp, $abs)) {
          $ins = $mysqli->prepare("INSERT INTO images (listing_id, filename) VALUES (?, ?)");
          $ins->bind_param('is', $listing_id, $rel);
          $ins->execute();
          $okCount++;
        } else {
          $errs[] = "$name: อัปโหลดไม่สำเร็จ";
        }
      }
      if ($okCount > 0) $ok = "อัปโหลดสำเร็จ {$okCount} ไฟล์";
    } else {
      $errs[] = 'ยังไม่ได้เลือกไฟล์รูป';
    }
  }
}
?>
<main class="container">
  <h1 class="title">อัปโหลดรูปเข้าประกาศ</h1>

  <?php if($ok): ?>
    <div class="card" style="border-left:4px solid #16a34a"><?= htmlspecialchars($ok) ?></div>
  <?php endif; ?>
  <?php if($errs): ?>
    <div class="card" style="border-left:4px solid #dc2626">
      <?= htmlspecialchars(implode(' | ', $errs)) ?>
    </div>
  <?php endif; ?>

  <form class="form card" method="post" enctype="multipart/form-data">
    <div class="field">
      <label>เลือกประกาศของฉัน</label>
      <select name="listing_id" required>
        <option value="">-- เลือกประกาศ --</option>
        <?php foreach($my_listings as $L): ?>
          <option value="<?= (int)$L['id'] ?>">#<?= (int)$L['id'] ?> — <?= htmlspecialchars($L['title']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="field">
      <label>เลือกรูป (อัปโหลดได้หลายไฟล์)</label>
      <input type="file" name="images[]" multiple accept="image/*" id="images">
      <small class="muted">รองรับ JPG / PNG / WEBP / GIF — ขนาด ≤ 5MB ต่อไฟล์</small>
    </div>

    <div id="preview" class="row v-gap" style="flex-wrap:wrap;gap:10px;"></div>

    <button class="btn" type="submit">อัปโหลดรูป</button>
    <a class="btn ghost" href="owner_listings.php" style="margin-left:8px">กลับไปจัดการประกาศ</a>
  </form>
</main>

<script>
  // พรีวิวรูปก่อนอัปโหลด
  const inp = document.getElementById('images');
  const preview = document.getElementById('preview');
  inp?.addEventListener('change', () => {
    preview.innerHTML = '';
    [...inp.files].forEach(f => {
      const url = URL.createObjectURL(f);
      const img = new Image();
      img.src = url;
      img.style = 'width:120px;height:80px;object-fit:cover;border-radius:8px;border:1px solid #e6eef8';
      preview.appendChild(img);
    });
  });
</script>

<?php include __DIR__.'/footer.php'; ?>
