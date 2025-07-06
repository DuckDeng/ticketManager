<?php
// admin_action.php
include 'config.php';

if(isset($_GET['action']) && isset($_GET['id'])){
  $action = $_GET['action'];
  $id = intval($_GET['id']);
  if($action == 'check'){
    $sql = "UPDATE tickets SET check_status = '已检票' WHERE id = $id";
    $conn->query($sql);
  }
}
header("Location: admin.php");
exit;
$conn->close();
?>
