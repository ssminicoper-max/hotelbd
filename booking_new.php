<?php
$title='จองหอ'; include __DIR__.'/header.php'; include __DIR__.'/conn.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='tenant'){ header('Location: login.php'); exit; }
$tenant=(int)$_SESSION['user_id'];
$listing_id=(int)($_GET['listing_id'] ?? $_POST['listing_id'] ?? 0);
if($listing_id<=0){ header('Location: index.php'); exit; }

$L = $mysqli->query("SELECT id,title,price FROM listings WHERE id={$listing_id} AND status='active'")->fetch_assoc();
if(!$L){ echo '<main class="container"><div class="card">ไม่พบประกาศนี้</div></main>'; include __DIR__.'/footer.php'; exit; }

$ok=null; $err=null;
if($_SERVER['REQUEST_METHOD']==='POST'){
  $start=$_POST['start_date']??''; $end=$_POST['end_date']??'';
  if(!$start||!$end){ $err='กรุณาเลือกวันที่'; }
  elseif (strtotime($end) < strtotime($start)) { $err='ช่วงวันที่ไม่ถูกต้อง'; }
  else {
    // (เดโม่) คิดราคารวมแบบรายเดือนคร่าว ๆ
    $months = max(1, (int)ceil((strtotime($end)-strtotime($start))/(30*24*3600)));
    $total = $months * (float)$L['price'];

    $stmt=$mysqli->prepare("INSERT INTO bookings(listing_id,tenant_id,start_date,end_date,total_price,status) VALUES(?,?,?,?,?,'pending')");
    $stmt->bind_param('iissd',$listing_id,$tenant,$start,$end,$total);
    if($stmt->execute()){ $ok='ส่งคำขอจองแล้ว'; }
    else { $err='บันทึกไม่สำเร็จ'; }
  }
}
?>
<main class="container">
  <div class="card">
    <h1>จอง: <?= htmlspecialchars($L['title']) ?></h1>
    <?php if($ok): ?><div class="card" style="margin-top:10px"><?= htmlspecialchars($ok) ?></div><?php endif; ?>
    <?php if($err): ?><div class="card" style="margin-top:10px"><?= htmlspecialchars($err) ?></div><?php endif; ?>

    <form class="form" method="post">
      <input type="hidden" name="listing_id" value="<?= (int)$listing_id ?>">
      <div class="field"><label>วันที่เริ่ม</label><input type="date" name="start_date" required></div>
      <div class="field"><label>วันที่สิ้นสุด</label><input type="date" name="end_date" required></div>
      <button class="btn" type="submit">ส่งคำขอจอง</button>
      <a class="btn ghost" href="bookings.php" style="margin-left:8px">ไปหน้าการจองของฉัน</a>
    </form>
  </div>
</main>
<?php include __DIR__.'/footer.php'; ?>
