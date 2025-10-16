<?php
// register.php — สมัครสมาชิก (เวอร์ชันมี ยืนยันรหัส / โทรศัพท์ / ยอมรับเงื่อนไข)
$title = 'Register'; $active='register';
include __DIR__ . '/header.php';
include __DIR__ . '/conn.php';

$errors = [];
$ok = '';

$name   = trim($_POST['name'] ?? '');
$email  = trim($_POST['email'] ?? '');
$phone  = trim($_POST['phone'] ?? '');
$pass   = $_POST['password'] ?? '';
$pass2  = $_POST['password_confirm'] ?? '';
$role   = $_POST['role'] ?? 'tenant';
$agree  = isset($_POST['agree']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // validate
  if ($name === '')   { $errors[] = 'กรุณากรอกชื่อ-นามสกุล'; }
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'รูปแบบอีเมลไม่ถูกต้อง';
  }
  if ($pass === '' || strlen($pass) < 6) { $errors[] = 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'; }
  if ($pass !== $pass2) { $errors[] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน'; }

  if ($phone !== '' && !preg_match('/^[0-9+\-\s]{8,20}$/', $phone)) {
    $errors[] = 'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง';
  }

  if (!$agree) { $errors[] = 'กรุณายอมรับข้อตกลงการใช้งานและนโยบายความเป็นส่วนตัว'; }

  // role ปลอดภัยไว้ก่อน
  if (!in_array($role, ['tenant','owner'], true)) { $role = 'tenant'; }

  if (!$errors) {
    // อีเมลซ้ำ?
    $chk = $mysqli->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $chk->bind_param('s', $email);
    $chk->execute();
    if ($chk->get_result()->fetch_assoc()) {
      $errors[] = 'อีเมลนี้มีอยู่แล้ว';
    } else {
      $hash = password_hash($pass, PASSWORD_DEFAULT);

      // ตรวจว่าตาราง users มีคอลัมน์ phone ไหม
      $hasPhone = false;
      if ($res = $mysqli->prepare("
          SELECT COUNT(*) c FROM INFORMATION_SCHEMA.COLUMNS 
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='phone'
      ")) {
        $res->execute();
        $hasPhone = ((int)$res->get_result()->fetch_assoc()['c']) > 0;
      }

      if ($hasPhone) {
        $stmt = $mysqli->prepare("INSERT INTO users(name,email,password_hash,role,phone) VALUES(?,?,?,?,?)");
        $stmt->bind_param('sssss', $name, $email, $hash, $role, $phone);
      } else {
        $stmt = $mysqli->prepare("INSERT INTO users(name,email,password_hash,role) VALUES(?,?,?,?)");
        $stmt->bind_param('ssss', $name, $email, $hash, $role);
      }

      if ($stmt->execute()) {
        // login อัตโนมัติ แล้วส่งไปหน้าที่เหมาะสม
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['name']    = $name;
        $_SESSION['role']    = $role;

        if ($role === 'owner') {
          header('Location: owner_dashboard.php'); exit;
        } else {
          header('Location: index.php'); exit;
        }
      } else {
        $errors[] = 'สมัครสมาชิกไม่สำเร็จ กรุณาลองใหม่';
      }
    }
  }
}
?>
<main class="container" style="max-width:960px;margin:auto;">
  <h1 class="title">สมัครสมาชิก</h1>

  <?php if ($errors): ?>
    <div class="card" style="border-left:4px solid #ef4444">
      <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
    </div>
  <?php endif; ?>

  <form class="form card" method="post" novalidate>
    <div class="field">
      <label>ชื่อ-นามสกุล</label>
      <input name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Name" required>
    </div>

    <div class="field">
      <label>อีเมล</label>
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="Email" required>
    </div>

    <div class="row v-gap">
      <div class="field grow">
        <label>รหัสผ่าน</label>
        <div class="row center" style="gap:8px">
          <input id="pw" type="password" name="password" placeholder="Password" required class="grow">
          <button type="button" class="btn ghost" aria-label="show password" onclick="togglePw('pw', this)">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
        <div class="muted" style="font-size:12px">อย่างน้อย 6 ตัวอักษร</div>
      </div>

      <div class="field grow">
        <label>ยืนยันรหัสผ่าน</label>
        <div class="row center" style="gap:8px">
          <input id="pw2" type="password" name="password_confirm" placeholder="Password Confirmation" required class="grow">
          <button type="button" class="btn ghost" aria-label="show password" onclick="togglePw('pw2', this)">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>
      </div>
    </div>

    <div class="field">
      <label>เบอร์โทรศัพท์</label>
      <input name="phone" value="<?= htmlspecialchars($phone) ?>" placeholder="Phone">
    </div>

    <div class="field">
      <label>ประเภทผู้ใช้</label>
      <select name="role">
        <option value="tenant" <?= $role==='tenant'?'selected':'' ?>>ผู้เช่า</option>
        <option value="owner"  <?= $role==='owner'?'selected':''  ?>>เจ้าของหอ</option>
      </select>
    </div>

    <div class="field row" style="gap:10px; align-items:flex-start">
      <input type="checkbox" name="agree" id="agree" <?= $agree?'checked':'' ?> required>
      <label for="agree" class="muted">
        ยอมรับเงื่อนไข
        <a href="terms.php" target="_blank">ข้อตกลงการใช้งาน</a> และ
        <a href="privacy.php" target="_blank">นโยบายความเป็นส่วนตัว</a>
      </label>
    </div>

    <button class="btn" type="submit">สมัครสมาชิก</button>
  </form>
</main>

<script>
// toggle show/hide password
function togglePw(id, btn){
  const el = document.getElementById(id);
  const isPwd = el.type === 'password';
  el.type = isPwd ? 'text' : 'password';
  btn.innerHTML = isPwd ? '<i class="fa-regular fa-eye-slash"></i>' : '<i class="fa-regular fa-eye"></i>';
}
</script>

<?php include __DIR__ . '/footer.php'; ?>
