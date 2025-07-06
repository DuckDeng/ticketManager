<?php
// check_action.php
include 'config.php';

header('Content-Type: application/json');

if(isset($_POST['ticket'])){
  $ticket = $conn->real_escape_string($_POST['ticket']);
  $sql = "SELECT * FROM tickets WHERE ticket_number = '$ticket'";
  $result = $conn->query($sql);
  if($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if($row['check_status'] == '未检票') {
      $updateSql = "UPDATE tickets SET check_status = '已检票' WHERE ticket_number = '$ticket'";
      if($conn->query($updateSql) === TRUE){
        echo json_encode(["success" => true]);
      } else {
        echo json_encode(["success" => false]);
      }
    } else {
      // 票已检
      echo json_encode(["success" => false]);
    }
  } else {
    // 找不到该票
    echo json_encode(["success" => false]);
  }
} else {
  echo json_encode(["success" => false]);
}
$conn->close();
?>
