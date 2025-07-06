<?php
// admin_add.php
include 'config.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
  function generateTicketNumber($conn) {
    $attempt = 0;
    do {
      $ticketNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
      $sql = "SELECT * FROM tickets WHERE ticket_number = '$ticketNumber'";
      $result = $conn->query($sql);
      $attempt++;
      if ($attempt > 10) { 
        break;
      }
    } while($result && $result->num_rows > 0);
    return $ticketNumber;
  }
  $ticketNumber = generateTicketNumber($conn);
  $saleTime = date('Y-m-d H:i:s');
  $checkStatus = '未检票';
  $sql = "INSERT INTO tickets (ticket_number, sale_time, check_status) VALUES ('$ticketNumber', '$saleTime', '$checkStatus')";
  if($conn->query($sql) === TRUE){
    $message = "手动增加票成功！票号：" . $ticketNumber;
  } else {
    $message = "添加票失败: " . $conn->error;
  }
}
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <title>手动增加票 - 漫展票务系统</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 和 jQuery -->
  <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
  <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
  <div class="container">
    <h2 class="mt-5">手动增加票</h2>
    <?php if(isset($message)): ?>
      <div class="alert alert-info"><?php echo $message; ?></div>
    <?php endif; ?>
    <form method="post">
      <button type="submit" class="btn btn-primary">生成票</button>
    </form>
    <a href="admin.php" class="btn btn-secondary mt-3">返回后台管理</a>
  </div>
</body>
</html>
