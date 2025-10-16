<?php
$title = 'ติดต่อเรา';
$active = 'contact';
include __DIR__.'/header.php';
include __DIR__.'/conn.php';

$ok = $err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name  = trim($_POST['name']  ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $msg   = trim($_POST['message'] ?? '');

  if ($name === '' || $email === '' || $msg === '') {
    $err = 'กรุณากรอก ชื่อ อีเมล และข้อความ';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $err = 'รูปแบบอีเมลไม่ถูกต้อง';
  } else {
    $stmt = $mysqli->prepare("INSERT INTO contact_messages(name,email,phone,message) VALUES(?,?,?,?)");
    $stmt->bind_param('ssss', $name, $email, $phone, $msg);
    if ($stmt->execute()) {
      $ok = 'ส่งข้อความเรียบร้อย ขอบคุณที่ติดต่อเรา 💙';
      $_POST = [];
    } else {
      $err = 'ส่งไม่สำเร็จ กรุณาลองใหม่อีกครั้ง';
    }
  }
}
?>
<main class="container contact-page">
  <header class="contact-header">
    <h1 class="title">ติดต่อทีมงาน near RMUTT</h1>
    <p class="muted">มีข้อสงสัย ปัญหาการใช้งาน หรืออยากให้ช่วยแก้ไขประกาศ ทักหาเราได้เลย</p>
  </header>

  <?php if ($ok): ?>
    <div class="alert success"><?= htmlspecialchars($ok) ?></div>
  <?php elseif ($err): ?>
    <div class="alert danger"><?= htmlspecialchars($err) ?></div>
  <?php endif; ?>

  <section class="contact-grid">
    <!-- ซ้าย: ข้อมูลติดต่อ -->
    <article class="card info-card">
      <h2 class="card-title">ข้อมูลติดต่อ</h2>

      <dl class="info-list">
        <div>
          <dt><i class="fa-solid fa-location-dot"></i> ที่อยู่</dt>
          <dd>near RMUTT<br>39 หมู่ที่ 1 ถนนรังสิต–นครนายก ต.คลองหก อ.คลองหลวง จ.ปทุมธานี 12110</dd>
        </div>
        <div>
          <dt><i class="fa-regular fa-envelope"></i> อีเมล</dt>
          <dd><a href="mailto:nearrmutt@gmail.com">nearrmutt@gmail.com</a></dd>
        </div>
        <div>
          <dt><i class="fa-solid fa-phone"></i> โทร</dt>
          <dd>02-105-4287 <span class="muted">(จ.–ศ. 09:00–17:00 น.)</span></dd>
        </div>
        <div>
          <dt><i class="fa-brands fa-line"></i> LINE</dt>
          <dd>@kukkik2724</dd>
        </div>
      </dl>

      <div class="open-hours">
        <span class="badge">เวลาทำการ</span>
        <div>จันทร์–ศุกร์ 09:00–17:00 น.</div>
        <div>เสาร์–อาทิตย์ / นักขัตฤกษ์ : ปิดทำการ</div>
      </div>
    </article>

    <!-- ขวา: ฟอร์ม -->
    <aside class="card form-card">
      <h2 class="card-title">ส่งข้อความถึงทีมงาน</h2>
      <form method="post" class="contact-form" novalidate>
        <label>ชื่อ–นามสกุล *</label>
        <input name="name" required placeholder="เช่น กนกวรรณ ใจดี" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">

        <label>อีเมล *</label>
        <input type="email" name="email" required placeholder="name@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

        <label>เบอร์โทรศัพท์</label>
        <input name="phone" placeholder="0812345678" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">

        <label>ข้อความ *</label>
        <textarea name="message" rows="5" required placeholder="อธิบายปัญหาหรือคำขอของคุณให้ละเอียด"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>

        <button class="btn wide" type="submit">ส่งข้อความ</button>
        <p class="tiny muted">การกด “ส่งข้อความ” แสดงว่าคุณยอมรับ <a href="terms.php">ข้อตกลงการใช้งาน</a></p>
      </form>
    </aside>
  </section>

  <!-- แถว QR แยกออกมาให้ดูสะอาดตา -->
  <section class="qr-row card">
    <img src="uploads/line.jpg" alt="LINE QR" class="qr-img">
    <div>
      <div class="qr-title">Add LINE : <b>@kukkik2724</b></div>
      <div class="qr-sub muted">แอดไลน์เพื่อคุยกับทีมงานได้ทันที</div>
    </div>
  </section>
</main>

<style>
.contact-header .muted{margin-top:6px}
.alert{padding:10px 14px;border-radius:12px;margin:10px 0;font-weight:500}
.alert.success{background:#ecfdf5;color:#065f46;border:1px solid #86efac}
.alert.danger{background:#fef2f2;color:#7f1d1d;border:1px solid #fecaca}

.contact-grid{
  display:grid;grid-template-columns:1.1fr 1fr;gap:18px;margin-top:8px
}
.card{
  background:#fff;border:1px solid #e5e7eb;border-radius:14px;padding:18px 20px;
  box-shadow:0 2px 6px rgba(0,0,0,.04)
}
.card-title{margin:0 0 10px;color:#1e66ff}

.info-list{display:grid;grid-template-columns:1fr;gap:10px;margin:4px 0 12px}
.info-list dt{font-weight:700;margin-bottom:2px}
.info-list dd{margin:0}
.info-list a{color:#1e66ff;text-decoration:none}
.info-list a:hover{text-decoration:underline}

.badge{display:inline-block;background:#eef3ff;color:#1e66ff;padding:4px 8px;border-radius:999px;font-size:12px;font-weight:700}
.open-hours{display:grid;gap:4px}

.contact-form label{font-size:13px;color:#5a6b85;margin-top:8px}
.contact-form input,.contact-form textarea{
  width:100%;padding:11px 12px;border:1px solid #d1d5db;border-radius:10px;outline:none
}
.contact-form input:focus,.contact-form textarea:focus{
  border-color:#9bb8ff;box-shadow:0 0 0 3px rgba(30,102,255,.12)
}
.btn{background:#1e66ff;color:#fff;border:none;border-radius:10px;padding:10px 14px;font-weight:700;cursor:pointer}
.btn:hover{filter:brightness(1.05)}
.btn.wide{width:100%}
.tiny{font-size:12px;margin-top:6px}

.qr-row{display:flex;align-items:center;gap:14px;margin-top:16px}
.qr-img{width:96px;height:96px;border-radius:10px;background:#fff;object-fit:cover}
.qr-title{font-size:18px;font-weight:800}
.qr-sub{font-size:13px}

@media (max-width: 980px){
  .contact-grid{grid-template-columns:1fr}
  .qr-row{flex-direction:row}
}
</style>

<?php include __DIR__.'/footer.php'; ?>
