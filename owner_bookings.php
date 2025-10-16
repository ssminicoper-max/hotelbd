<?php
$title='คำขอจอง'; include __DIR__.'/header.php'; include __DIR__.'/conn.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='owner'){ header('Location: login.php'); exit; }
$owner=(int)$_SESSION['user_id'];

$action=$_POST['action']??'';
$id=(int)($_POST['id']??0);
if($id && in_array($action,['approve','reject','cancel'],true)){
  $new = $action==='approve'?'approved':($action==='reject'?'rejected':'cancelled');
  $stmt=$mysqli->prepare("
    UPDATE bookings B
    JOIN listings L ON L.id=B.listing_id
    SET B.status=?
    WHERE B.id=? AND L.owner_id=? AND B.status IN ('pending','approved')
  ");
  $stmt->bind_param('sii',$new,$id,$owner); $stmt->execute();
}

$q=$mysqli->prepare("
  SELECT B.id,B.start_date,B.end_date,B.status,B.total_price,U.name AS tenant_name,L.title
  FROM bookings B
  JOIN listings L ON L.id=B.listing_id
  JOIN users U ON U.id=B.tenant_id
  WHERE L.owner_id=? ORDER BY B.created_at DESC
");
$q->bind_param('i',$owner); $q->execute();
$rows=$q->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="container">
  <h1 class="title">คำขอจองในประกาศของฉัน</h1>
  <div class="card overflow">
    <table class="table">
      <thead><tr><th>#</th><th>หอ</th><th>ผู้เช่า</th><th>ช่วง</th><th>สถานะ</th><th class="right">ยอด</th><th>จัดการ</th></tr></thead>
      <tbody>
        <?php if(!$rows): ?><tr><td colspan="7">ยังไม่มีคำขอ</td></tr><?php endif; ?>
        <?php foreach($rows as $i=>$r): ?>
          <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['tenant_name']) ?></td>
            <td><?= htmlspecialchars($r['start_date']) ?> → <?= htmlspecialchars($r['end_date']) ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
            <td class="right">฿<?= number_format($r['total_price'],0) ?></td>
            <td>
              <?php if($r['status']==='pending'): ?>
                <form method="post" style="display:inline-block">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <button class="btn small" name="action" value="approve">อนุมัติ</button>
                </form>
                <form method="post" style="display:inline-block">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <button class="btn small" name="action" value="reject">ปฏิเสธ</button>
                </form>
              <?php elseif($r['status']==='approved'): ?>
                <form method="post" style="display:inline-block">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <button class="btn small" name="action" value="cancel">ยกเลิก</button>
                </form>
              <?php else: ?>-<?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__.'/footer.php'; ?>
