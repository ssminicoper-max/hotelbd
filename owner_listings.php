<?php
$title='ประกาศของฉัน'; include __DIR__.'/header.php'; include __DIR__.'/conn.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='owner'){ header('Location: login.php'); exit; }
$owner=(int)$_SESSION['user_id'];

$action=$_POST['action']??'';
if($action==='delete'){
  $id=(int)($_POST['id']??0);
  $d=$mysqli->prepare("DELETE FROM listings WHERE id=? AND owner_id=?");
  $d->bind_param('ii',$id,$owner); $d->execute();
}
if($action==='toggle'){
  $id=(int)($_POST['id']??0);
  $mysqli->query("UPDATE listings SET status=IF(status='active','inactive','active') WHERE id={$id} AND owner_id={$owner}");
}
if($action==='update'){
  $id=(int)($_POST['id']??0);
  $titleIn=trim($_POST['title']??''); $area=trim($_POST['area']??''); $type=trim($_POST['type']??'หอพัก'); $price=(float)($_POST['price']??0); $desc=trim($_POST['description']??'');
  $u=$mysqli->prepare("UPDATE listings SET title=?,area=?,type=?,price=?,description=? WHERE id=? AND owner_id=?");
  $u->bind_param('sssdsii',$titleIn,$area,$type,$price,$desc,$id,$owner); $u->execute();
}

$q=$mysqli->prepare("SELECT id,title,area,type,price,status FROM listings WHERE owner_id=? ORDER BY created_at DESC");
$q->bind_param('i',$owner); $q->execute();
$rows=$q->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="container">
  <h1 class="title">ประกาศของฉัน</h1>
  <a class="btn" href="listing_new.php">+ เพิ่มประกาศ</a>
  <div class="card overflow mt">
    <table class="table">
      <thead><tr><th>#</th><th>ชื่อ</th><th>พื้นที่</th><th>ประเภท</th><th class="right">ราคา</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
      <tbody>
        <?php if(!$rows): ?><tr><td colspan="7">ยังไม่มีประกาศ</td></tr><?php endif; ?>
        <?php foreach($rows as $i=>$r): ?>
          <tr>
            <td><?= $i+1 ?></td><td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['area']) ?></td><td><?= htmlspecialchars($r['type']) ?></td>
            <td class="right">฿<?= number_format($r['price'],0) ?></td>
            <td><?= $r['status']==='active'?'เปิด':'ปิด' ?></td>
            <td>
              <!-- แก้ไขแบบ inline -->
              <details>
                <summary class="nav-link">แก้ไข</summary>
                <form method="post" class="form" style="margin-top:8px">
                  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                  <div class="field"><input name="title" value="<?= htmlspecialchars($r['title']) ?>"></div>
                  <div class="field"><input name="area" value="<?= htmlspecialchars($r['area']) ?>"></div>
                  <div class="field"><input name="type" value="<?= htmlspecialchars($r['type']) ?>"></div>
                  <div class="field"><input name="price" type="number" step="0.01" value="<?= (float)$r['price'] ?>"></div>
                  <div class="field"><textarea name="description" rows="2"></textarea></div>
                  <button class="btn small" name="action" value="update">บันทึก</button>
                </form>
              </details>

              <form method="post" style="display:inline-block">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn small" name="action" value="toggle"><?= $r['status']==='active'?'ปิด':'เปิด' ?></button>
              </form>

              <form method="post" onsubmit="return confirm('ลบประกาศนี้?')" style="display:inline-block">
                <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn small" name="action" value="delete">ลบ</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</main>
<?php include __DIR__.'/footer.php'; ?>
