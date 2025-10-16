<?php
include 'header.php';
include 'conn.php';
?>

<main class="container">
  <h1 class="title">🏆 หอพักแนะนำสำหรับคุณ</h1>
  <p class="muted">รวมสุดยอดหอพักใกล้ RMUTT ที่ได้รับคะแนนรีวิวสูงสุด</p>

  <div class="recommend-grid">
    <?php
    $q = "SELECT L.*, U.name AS owner_name 
          FROM listings L 
          JOIN users U ON U.id = L.owner_id 
          WHERE L.status='active'
          ORDER BY L.rating DESC, L.price ASC 
          LIMIT 6";
    $res = $mysqli->query($q);
    if($res->num_rows == 0){
      echo "<p>ยังไม่มีข้อมูลหอแนะนำ</p>";
    }
    while($row = $res->fetch_assoc()):
      $imgQ = $mysqli->prepare("SELECT filename FROM images WHERE listing_id=? LIMIT 1");
      $imgQ->bind_param('i', $row['id']);
      $imgQ->execute();
      $imgR = $imgQ->get_result()->fetch_assoc();
      $img = $imgR['filename'] ?? 'images/default_dorm.jpg';
    ?>
    <div class="recommend-card">
      <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($row['title']) ?>">
      <div class="recommend-info">
        <h3><?= htmlspecialchars($row['title']) ?></h3>
        <p class="muted">พื้นที่: <?= htmlspecialchars($row['area']) ?> · ประเภท: <?= htmlspecialchars($row['type']) ?></p>
        <div class="rating">
          ⭐ <?= number_format($row['rating'], 1) ?> / 5.0
        </div>
        <div class="price">฿<?= number_format($row['price'],0) ?>/ด.</div>
        <a class="btn small" href="listing_view.php?id=<?= (int)$row['id'] ?>">ดูรายละเอียด</a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</main>

<?php include 'footer.php'; ?>
