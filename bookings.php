<?php
$title='การจองของฉัน'; include __DIR__.'/header.php'; include __DIR__.'/conn.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='tenant'){ header('Location: login.php'); exit; }
$uid=(int)$_SESSION['user_id'];

$action=$_POST['action']??'';
if($action==='cancel'){
  $id=(int)($_POST['id']??0);
  $stmt=$mysqli->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND tenant_id=? AND status IN ('pending','approved')");
  $stmt->bind_param('ii',$id,$uid); $stmt->execute();
}
if($action==='update'){
  $id=(int)($_POST['id']??0); $start=$_POST['start_date']??''; $end=$_POST['end_date']??'';
  if($start && $end){
    $stmt=$mysqli->prepare("SELECT L.price FROM bookings B JOIN listings L ON L.id=B.listing_id WHERE B.id=? AND B.tenant_id=?");
    $stmt->bind_param('ii',$id,$uid); $stmt->execute();
    $price = $stmt->get_result()->fetch_column();
    if($price){
      $months=max(1,(int)ceil((strtotime($end)-strtotime($start))/(30*24*3600)));
      $total=(float)$price*$months;
      $up=$mysqli->prepare("UPDATE bookings SET start_date=?, end_date=?, total_price=?, status='pending' WHERE id=? AND tenant_id=?");
      $up->bind_param('ssdii',$start,$end,$total,$id,$uid); $up->execute();
    }
  }
}

$q=$mysqli->prepare("
  SELECT B.id,B.start_date,B.end_date,B.status,B.total_price,L.title
  FROM bookings B JOIN listings L ON L.id=B.listing_id
  WHERE B.tenant_id=? ORDER BY B.created_at DESC
");
$q->bind_param('i',$uid); $q->execute();
$rows=$q->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="container">
  <h1 class="title">การจองของฉัน</h1>
  <div class="card overflow">
    <table class="table">
      <thead><tr><th>#</th><th>หอ</th><th>ช่วง</th><th>สถานะ</th><th class="right">ราคารวม</th><th>จัดการ</th></tr></thead>
      <tbody>
        <?php if(!$rows): ?><tr><td colspan="6">ยังไม่มีการจอง</td></tr><?php endif; ?>
        <?php foreach($rows as $i=>$r): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['start_date']) ?> → <?= htmlspecialchars($r['end_date']) ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
            <td class="right">฿<?= number_format($r['total_price'],0) ?></td>
            <td>
              <?php if(in_array($r['status'],['pending','approved'])): ?>
                <form method="post" style="display:inline-block">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <input type="date" name="start_date" required>
                  <input type="date" name="end_date" required>
                  <button class="btn small" name="action" value="update">แก้ไข</button>
                </form>
                <form method="post" style="display:inline-block">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <button class="btn small" name="action" value="cancel">ยกเลิก</button>
                </form>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__.'/footer.php'; ?>
