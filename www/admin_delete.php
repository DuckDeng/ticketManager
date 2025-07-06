<?php
// admin_delete.php
include 'config.php';

if(isset($_GET['id'])){
  // 单个删除
  $id = intval($_GET['id']);
  $sql = "DELETE FROM tickets WHERE id = $id";
  $conn->query($sql);
  header("Location: admin.php");
  exit;
} elseif(isset($_POST['ticket_ids'])){
  // 批量删除
  $ids = $_POST['ticket_ids'];
  $ids = array_map('intval', $ids);
  $idList = implode(",", $ids);
  $sql = "DELETE FROM tickets WHERE id IN ($idList)";
  $conn->query($sql);
  header("Location: admin.php");
  exit;
} else {
  header("Location: admin.php");
  exit;
}
$conn->close();
?>
