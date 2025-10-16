<?php
$title='Login';
include __DIR__.'/header.php';
include __DIR__.'/conn.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';

  // ดึงด้วย password_hash
  $stmt = $mysqli->prepare("SELECT id, name, role, password_hash FROM users WHERE email=? LIMIT 1");
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $u = $stmt->get_result()->fetch_assoc();

  if ($u && password_verify($pass, $u['password_hash'])) {
    // ตั้ง session
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['name']    = $u['name'];
    $_SESSION['role']    = $u['role'];

    // เด้งตามบทบาท
    if ($u['role'] === 'owner') {
      header('Location: owner_dashboard.php');
    } else {
      header('Location: index.php');
    }
    exit;
  } else {
    $err = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
  }
}
?>
<main class="container">
  <h1>เข้าสู่ระบบ</h1>

  <?php if (!empty($err)): ?>
    <div class="card" style="background:#ffecec;color:#b91c1c;padding:10px;">
      <?= htmlspecialchars($err) ?>
    </div>
  <?php endif; ?>

  <form class="form card" method="post">
    <div class="field">
      <label>อีเมล</label>
      <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </div>
    <div class="field">
      <label>รหัสผ่าน</label>
      <input type="password" name="password" required>
    </div>
    <button class="btn" type="submit">เข้าสู่ระบบ</button>
  </form>
</main>
<?php include __DIR__.'/footer.php'; ?>
