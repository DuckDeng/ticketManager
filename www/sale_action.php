<?php
// sale_action.php
include 'config.php';

// 随机生成不重复的8位票号
function generateTicketNumber($conn) {
    $attempt = 0;
    do {
        $ticketNumber = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
        $sql = "SELECT * FROM tickets WHERE ticket_number = '$ticketNumber'";
        $result = $conn->query($sql);
        $attempt++;
        if ($attempt > 10) { // 避免无限循环
            break;
        }
    } while($result && $result->num_rows > 0);
    return $ticketNumber;
}

$ticketNumber = generateTicketNumber($conn);
$saleTime = date('Y-m-d H:i:s');
$checkStatus = '未检票';

$sql = "INSERT INTO tickets (ticket_number, sale_time, check_status) VALUES ('$ticketNumber', '$saleTime', '$checkStatus')";
if ($conn->query($sql) === TRUE) {
?>
<!DOCTYPE html>
<html lang="zh">
<head>
  <meta charset="UTF-8">
  <title>出票成功 - 漫展票务系统</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap、jQuery 和二维码生成库 -->
  <link rel="stylesheet" href="https://cdn.bootcdn.net/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
  <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- QRCode.js (前端生成二维码) -->
  <script src="https://cdn.bootcdn.net/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
  <div class="container text-center">
    <h2 class="mt-5">出票成功！</h2>
    <p>票号：<strong id="ticketNumber"><?php echo $ticketNumber; ?></strong></p>
    <!-- 显示二维码的容器 -->
    <div id="qrcode"></div>
    <p class="mt-3">务必妥善保留二维码和票号，以免耽误您的入场</p>
    <button id="downloadBtn" class="btn btn-success mt-3">下载票据图片</button>
  </div>
  <script>
    // 生成二维码
    var ticketNumber = "<?php echo $ticketNumber; ?>";
    var qrcode = new QRCode(document.getElementById("qrcode"), {
      text: ticketNumber,
      width: 200,
      height: 200
    });

    // 使用 canvas 合成成品票据图片
    function composeTicketImage() {
      var canvas = document.createElement("canvas");
      canvas.width = 250;
      canvas.height = 350;
      var ctx = canvas.getContext("2d");
      
      // 绘制白色背景
      ctx.fillStyle = "#fff";
      ctx.fillRect(0, 0, canvas.width, canvas.height);

      // 绘制二维码（取页面上生成的二维码图片）
      var qrImg = document.querySelector("#qrcode img");
      if(qrImg) {
        ctx.drawImage(qrImg, 25, 20, 200, 200);
      }

      // 绘制票号
      ctx.fillStyle = "#000";
      ctx.font = "20px Arial";
      ctx.textAlign = "center";
      ctx.fillText("票号: " + ticketNumber, canvas.width/2, 250);

      // 绘制须知文字
      ctx.font = "16px Arial";
      ctx.fillText("请妥善保留二维码和票号", canvas.width/2, 280);
      ctx.fillText("以免耽误入场", canvas.width/2, 310);

      return canvas;
    }

    $('#downloadBtn').click(function(){
      var canvas = composeTicketImage();
      var link = document.createElement("a");
      link.download = "ticket_<?php echo $ticketNumber; ?>.png";
      link.href = canvas.toDataURL();
      link.click();
    });
  </script>
</body>
</html>
<?php
} else {
    echo "Error: " . $conn->error;
}
$conn->close();
?>
