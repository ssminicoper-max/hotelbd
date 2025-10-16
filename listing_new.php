<?php
$title='เพิ่มประกาศ';
include __DIR__.'/header.php';
include __DIR__.'/conn.php';

if (empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='owner'){
  header('Location: login.php'); exit;
}
$owner=(int)$_SESSION['user_id'];

// sticky form
$old = $_POST ?? [];
$ok=$err=null;

if($_SERVER['REQUEST_METHOD']==='POST'){
  // ข้อมูลหลัก
  $titleIn = trim($_POST['title'] ?? '');
  $area    = trim($_POST['area'] ?? '');
  $type    = trim($_POST['type'] ?? 'หอพัก');
  $price   = $_POST['price'] !== '' ? (float)$_POST['price'] : 0;

  // คำอธิบายสั้น
  $desc    = trim($_POST['description'] ?? '');

  // ค่าใช้จ่าย
  $deposit          = ($_POST['deposit'] !== '' ? (float)$_POST['deposit'] : null);
  $electricity_rate = trim($_POST['electricity_rate'] ?? '');
  $water_rate       = trim($_POST['water_rate'] ?? '');
  $other_fee        = ($_POST['other_fee'] !== '' ? (float)$_POST['other_fee'] : null);
  $internet         = trim($_POST['internet'] ?? '');

  // รายละเอียดเพิ่มเติม
  $details_long     = trim($_POST['details_long'] ?? '');

  // ติดต่อ + แผนที่
  $contact_phone    = trim($_POST['contact_phone'] ?? '');
  $contact_line     = trim($_POST['contact_line'] ?? '');
  $contact_facebook = trim($_POST['contact_facebook'] ?? '');
  $contact_email    = trim($_POST['contact_email'] ?? '');
  $map_url          = trim($_POST['map_url'] ?? '');

  // validate เบื้องต้น
  if(!$titleIn || $price<=0){
    $err='กรุณากรอก "ชื่อหอ" และ "ราคา/เดือน" ให้ถูกต้อง';
  } else {

    $sql = "
      INSERT INTO listings
      (owner_id, title, area, type, price, description,
       deposit, electricity_rate, water_rate, other_fee, internet,
       details_long, contact_phone, contact_line, contact_facebook, contact_email, map_url,
       status)
      VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'active')
    ";
    $stmt = $mysqli->prepare($sql);
    if(!$stmt){
      $err = 'เตรียมคำสั่งไม่สำเร็จ: '.$mysqli->error;
    } else {
      // ชนิดข้อมูลให้ตรงกับพารามิเตอร์ 17 ตัว
      $types = 'isssdsdssdsssssss';
      $stmt->bind_param(
        $types,
        $owner, $titleIn, $area, $type, $price, $desc,
        $deposit, $electricity_rate, $water_rate, $other_fee, $internet,
        $details_long, $contact_phone, $contact_line, $contact_facebook, $contact_email, $map_url
      );

      if($stmt->execute()){
        $newId = $stmt->insert_id ?: $mysqli->insert_id;
        // ไปหน้าอัปโหลดรูปของประกาศนี้ทันที
        header('Location: upload_images.php?listing_id='.$newId);
        exit;
      } else {
        $err = 'เกิดข้อผิดพลาดในการบันทึก: '.$stmt->error;
      }
    }
  }
}
?>
<main class="container">
  <h1 class="title" style="margin-bottom:12px;">เพิ่มประกาศหอ</h1>

  <?php if($ok): ?><div class="alert success"><?= htmlspecialchars($ok) ?></div><?php endif; ?>
  <?php if($err): ?><div class="alert danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>

  <form class="card create-form" method="post" novalidate>
    <!-- ส่วนที่ 1: ข้อมูลหลัก -->
    <section class="section">
      <h3>ข้อมูลหลัก</h3>
      <div class="grid-2">
        <label class="field">
          <span>ชื่อหอ <b class="req">*</b></span>
          <input name="title" required value="<?=htmlspecialchars($old['title']??'')?>">
        </label>
        <label class="field">
          <span>พื้นที่</span>
          <input name="area" placeholder="เช่น คลองหก" value="<?=htmlspecialchars($old['area']??'')?>">
        </label>
        <label class="field">
          <span>ประเภท</span>
          <?php $sel = $old['type']??'หอพัก'; ?>
          <select name="type">
            <option<?= $sel==='หอพัก'?' selected':''; ?>>หอพัก</option>
            <option<?= $sel==='อพาร์ตเมนต์'?' selected':''; ?>>อพาร์ตเมนต์</option>
            <option<?= $sel==='แมนชั่น'?' selected':''; ?>>แมนชั่น</option>
          </select>
        </label>
        <label class="field">
          <span>ราคา/เดือน (฿) <b class="req">*</b></span>
          <input type="number" step="0.01" name="price" required placeholder="เช่น 3500" value="<?=htmlspecialchars($old['price']??'')?>">
        </label>
        <label class="field col-span-2">
          <span>คำอธิบายสั้น</span>
          <input name="description" placeholder="เช่น ห้องสตูดิโอ ใกล้ มทร.ธัญบุรี เดิน 8 นาที" value="<?=htmlspecialchars($old['description']??'')?>">
        </label>
      </div>
    </section>

    <!-- ส่วนที่ 2: ค่าใช้จ่าย -->
    <section class="section">
      <h3>ค่าใช้จ่าย</h3>
      <div class="grid-3">
        <label class="field">
          <span>เงินประกัน (฿)</span>
          <input type="number" step="0.01" name="deposit" placeholder="เช่น 5500" value="<?=htmlspecialchars($old['deposit']??'')?>">
        </label>
        <label class="field">
          <span>ค่าไฟ</span>
          <input name="electricity_rate" placeholder="เช่น 7 บาท/หน่วย หรือ ตามมิเตอร์" value="<?=htmlspecialchars($old['electricity_rate']??'')?>">
        </label>
        <label class="field">
          <span>ค่าน้ำ</span>
          <input name="water_rate" placeholder="เช่น 18 บาท/หน่วย หรือ เหมาจ่าย" value="<?=htmlspecialchars($old['water_rate']??'')?>">
        </label>
        <label class="field">
          <span>ค่าบริการอื่นๆ (฿)</span>
          <input type="number" step="0.01" name="other_fee" placeholder="เช่น 350" value="<?=htmlspecialchars($old['other_fee']??'')?>">
        </label>
        <label class="field">
          <span>อินเทอร์เน็ต</span>
          <input name="internet" placeholder="เช่น ฟรี / คิดเพิ่ม 300บ." value="<?=htmlspecialchars($old['internet']??'')?>">
        </label>
      </div>
    </section>

    <!-- ส่วนที่ 3: รายละเอียดเพิ่มเติม -->
    <section class="section">
      <h3>รายละเอียดเพิ่มเติม</h3>
      <label class="field">
        <span class="muted">ใส่รายละเอียดห้อง สิ่งอำนวยความสะดวก ข้อกำชับ ฯลฯ</span>
        <textarea name="details_long" rows="8" placeholder="- เฟอร์ครบ
- ฟรี High Speed Internet
- มัดจำ 1 เดือน, ล่วงหน้า 1 เดือน
- ห้ามสูบภายในห้อง"><?=htmlspecialchars($old['details_long']??'')?></textarea>
      </label>
    </section>

    <!-- ส่วนที่ 4: ช่องทางติดต่อ + แผนที่ -->
    <section class="section">
      <h3>ช่องทางติดต่อเจ้าของหอ</h3>
      <div class="grid-3">
        <label class="field">
          <span>โทรศัพท์</span>
          <input name="contact_phone" placeholder="0812345678" value="<?=htmlspecialchars($old['contact_phone']??'')?>">
        </label>
        <label class="field">
          <span>LINE ID</span>
          <input name="contact_line" placeholder="lineid" value="<?=htmlspecialchars($old['contact_line']??'')?>">
        </label>
        <label class="field">
          <span>Facebook URL</span>
          <input type="url" name="contact_facebook" placeholder="https://facebook.com/..." value="<?=htmlspecialchars($old['contact_facebook']??'')?>">
        </label>
        <label class="field">
          <span>อีเมล</span>
          <input type="email" name="contact_email" placeholder="name@email.com" value="<?=htmlspecialchars($old['contact_email']??'')?>">
        </label>
        <label class="field col-span-2">
          <span>ลิงก์แผนที่ (Google Maps)</span>
          <input type="url" name="map_url" placeholder="https://maps.app.goo.gl/..." value="<?=htmlspecialchars($old['map_url']??'')?>">
        </label>
      </div>
    </section>

    <!-- ปุ่ม -->
    <div class="actions">
      <button class="btn" type="submit">บันทึกประกาศ</button>
      <a class="btn ghost" href="owner_listings.php">ไปจัดการประกาศ</a>
    </div>
  </form>
</main>

<style>
.alert{padding:10px 12px;border-radius:10px;margin:10px 0;border:1px solid}
.alert.success{background:#f0fdf4;border-color:#86efac;color:#166534}
.alert.danger{background:#fef2f2;border-color:#fecaca;color:#991b1b}

.create-form{padding:16px;border:1px solid #e5e7eb;border-radius:14px}
.section{padding:12px 0;border-top:1px dashed #e5e7eb}
.section:first-of-type{border-top:none}
.section h3{margin:0 0 8px}

.field{display:flex;flex-direction:column;gap:6px}
.field > span{font-size:13px;color:#5a6b85}
.req{color:#ef4444}
input,select,textarea{
  width:100%; padding:10px 12px; border:1px solid #dbe1ea; border-radius:10px;
  background:#fff; outline:none;
}
input:focus,select:focus,textarea:focus{border-color:#9bb8ff; box-shadow:0 0 0 3px rgba(30,102,255,.12)}
textarea{min-height:120px; resize:vertical}

.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
.col-span-2{grid-column:span 2 / span 2}

.actions{display:flex;gap:10px;justify-content:flex-start;margin-top:6px}
.btn{display:inline-block;background:#1e66ff;color:#fff;border:none;border-radius:10px;padding:10px 14px;font-weight:700;text-decoration:none}
.btn:hover{filter:brightness(1.05)}
.btn.ghost{background:#eef3ff;color:#1e66ff}

@media(max-width:900px){
  .grid-2,.grid-3{grid-template-columns:1fr}
  .col-span-2{grid-column:auto}
}
</style>

<?php include __DIR__.'/footer.php'; ?>
