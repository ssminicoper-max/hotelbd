<?php
$title='ที่สนใจ'; include __DIR__.'/header.php'; include __DIR__.'/conn.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='tenant'){ header('Location: login.php'); exit; }
$uid=(int)$_SESSION['user_id'];

$stmt=$mysqli->prepare("
  SELECT L.id,L.title,L.area,L.type,L.price
  FROM favorites F JOIN listings L ON L.id=F.listing_id
  WHERE F.user_id=? ORDER BY F.created_at DESC
");
$stmt->bind_param('i',$uid); $stmt->execute();
$rows=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="container">
  <h1 class="title">หอที่บันทึกไว้</h1>
  <div class="grid listings">
    <?php if(!$rows): ?><div class="card">ยังไม่มีรายการ</div><?php endif; ?>
    <?php foreach($rows as $L): ?>
      <article class="card listing">
        <div class="listing-title"><?= htmlspecialchars($L['title']) ?></div>
        <div class="listing-meta">พื้นที่: <?= htmlspecialchars($L['area']) ?> · ประเภท: <?= htmlspecialchars($L['type']) ?></div>
        <div class="listing-row">
          <span class="price-chip">฿<?= number_format($L['price'],0) ?>/ด.</span>
          <a class="btn small" href="listing_view.php?id=<?= (int)$L['id'] ?>">ดู</a>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</main>
<?php include __DIR__.'/footer.php'; ?>
