<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require __DIR__.'/conn.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role']??'')!=='tenant'){ header('Location: login.php'); exit; }

$uid=(int)$_SESSION['user_id'];
$listing_id=(int)($_POST['listing_id']??0);
$action=$_POST['action']??'';

if($listing_id>0){
  if($action==='fav'){
    $stmt=$mysqli->prepare("INSERT IGNORE INTO favorites(user_id,listing_id) VALUES(?,?)");
    $stmt->bind_param('ii',$uid,$listing_id); $stmt->execute();
  }elseif($action==='unfav'){
    $stmt=$mysqli->prepare("DELETE FROM favorites WHERE user_id=? AND listing_id=?");
    $stmt->bind_param('ii',$uid,$listing_id); $stmt->execute();
  }
}
header('Location: listing_view.php?id='.$listing_id);
