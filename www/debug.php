<?php
// debug.php
include 'config.php';

// 处理后台操作
$response = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'generate') {
    $count = intval($_POST['count'] ?? 0);
    if ($count > 0 && $count <= 10000) {
      $inserted = 0;
      for ($i = 0; $i < $count; $i++) {
        $ticketNumber = generateTicketNumber($conn);
        $saleTime = date('Y-m-d H:i:s');
        $checkStatus = '未检票';
        $sql = "INSERT INTO tickets (ticket_number, sale_time, check_status) VALUES ('$ticketNumber', '$saleTime', '$checkStatus')";
        if ($conn->query($sql) === TRUE) {
          $inserted++;
        }
      }
      $response = "成功生成 {$inserted} 张票。";
    } else {
      $response = "生成数量无效，必须为 1 到 10000。";
    }
  } elseif ($action === 'clear') {
    $sql = "DELETE FROM tickets";
    if ($conn->query($sql) === TRUE) {
      $response = "已清空所有票据。";
    } else {
      $response = "清空失败：" . $conn->error;
    }
  }
}

function generateTicketNumber($conn) {
  $attempt = 0;
  do {
    $ticketNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
    $sql = "SELECT 1 FROM tickets WHERE ticket_number = '$ticketNumber'";
    $result = $conn->query($sql);
    $attempt++;
    if ($attempt > 10) break;
  } while($result && $result->num_rows > 0);
  return $ticketNumber;
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <title>Debug 工具页</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.staticfile.net/twitter-bootstrap/5.3.2/css/bootstrap.min.css">
  <script src="https://cdn.staticfile.net/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
  <div class="container mt-5">
    <h2>🎛️ 调试工具面板</h2>

    <?php if ($response): ?>
      <div class="alert alert-info mt-3">操作反馈：<?php echo htmlspecialchars($response); ?></div>
    <?php endif; ?>

<form method="POST" class="mt-4" onsubmit="return confirm('你确定要生成票据吗？') && confirm('请再次确认：你要生成的是 <?php echo isset($_POST['count']) ? intval($_POST['count']) : '指定数量'; ?> 张票吗？');">
      <h5>生成指定数量票据</h5>
      <div class="input-group mb-3" style="max-width: 300px;">
        <input type="number" name="count" class="form-control" placeholder="请输入票数">
        <input type="hidden" name="action" value="generate">
        <button class="btn btn-success" type="submit">生成票据</button>
      </div>
    </form>

    <form method="POST" class="mt-4" onsubmit="return confirm('你确定要清空所有票据吗？此操作不可恢复！') && confirm('请再次确认：这将永久删除所有票据！');">
      <h5>⚠️ 清空全部票据</h5>
      <input type="hidden" name="action" value="clear">
      <button class="btn btn-danger" type="submit">清空所有票据</button>
    </form>
  </div>
</body>
</html>
