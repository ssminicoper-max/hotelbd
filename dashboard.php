<?php
$title='Dashboard'; $active='dashboard';
include __DIR__ . '/header.php';
if(!isset($_SESSION['user'])){ header('Location: login.php'); exit; }
$user=$_SESSION['user'];
?>
<main class="container">
  <div class="card">
    <h1>สวัสดี <?= htmlspecialchars($user['name']) ?></h1>
    <p>ประเภทผู้ใช้: <?= $user['role']==='owner'?'เจ้าของหอพัก':'ผู้เช่า' ?></p>
    <?php if($user['role']==='owner'): ?>
      <a class="btn" href="listing_new.php">+ เพิ่มประกาศใหม่</a>
    <?php endif; ?>
  </div>

  <?php if($user['role']==='owner'):
    $listings=$_SESSION['listings']??[];
    $mine=array_filter($listings, fn($L)=>$L['owner']===$user['name']);
  ?>
    <h2 class="mt">ประกาศของคุณ</h2>
    <div class="card overflow">
      <table class="table">
        <thead><tr><th>#</th><th>ชื่อหอ</th><th>พื้นที่</th><th>ประเภท</th><th class="right">ราคา/ด.</th></tr></thead>
        <tbody>
          <?php if(empty($mine)): ?><tr><td colspan="5" class="muted">ยังไม่มีประกาศของคุณ</td></tr>
          <?php else: $i=1; foreach($mine as $L): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($L['title']) ?></td>
              <td><?= htmlspecialchars($L['area']) ?></td>
              <td><?= htmlspecialchars($L['type']) ?></td>
              <td class="right">฿<?= number_format($L['price'],0) ?></td>
            </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</main>
<?php include __DIR__ . '/footer.php'; ?>
