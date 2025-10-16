<?php
$title='สรุปการจอง'; include __DIR__.'/header.php'; include __DIR__.'/conn.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='tenant'){ header('Location: login.php'); exit; }
$uid=(int)$_SESSION['user_id'];

$sum = $mysqli->prepare("SELECT status, COUNT(*) c FROM bookings WHERE tenant_id=? GROUP BY status");
$sum->bind_param('i',$uid); $sum->execute();
$stats = [];
$res=$sum->get_result(); while($row=$res->fetch_assoc()){ $stats[$row['status']]=$row['c']; }

$approved = $mysqli->prepare("SELECT COALESCE(SUM(total_price),0) FROM bookings WHERE tenant_id=? AND status='approved'");
$approved->bind_param('i',$uid); $approved->execute(); $totalApproved=$approved->get_result()->fetch_column();
?>
<main class="container">
  <h1 class="title">รายงานสรุปการจอง</h1>
  <div class="card">
    <p>รออนุมัติ: <strong><?= (int)($stats['pending']??0) ?></strong></p>
    <p>อนุมัติแล้ว: <strong><?= (int)($stats['approved']??0) ?></strong></p>
    <p>ถูกปฏิเสธ: <strong><?= (int)($stats['rejected']??0) ?></strong></p>
    <p>ยกเลิก: <strong><?= (int)($stats['cancelled']??0) ?></strong></p>
    <hr style="border:0;border-top:1px solid var(--border);margin:12px 0">
    <p>ราคารวม (เฉพาะที่อนุมัติ): <strong>฿<?= number_format($totalApproved,0) ?></strong></p>
  </div>
</main>
<?php include __DIR__.'/footer.php'; ?>
