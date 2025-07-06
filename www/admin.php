<?php
// admin.php
include 'config.php';

// 分页逻辑
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

$totalSql = "SELECT COUNT(*) as total FROM tickets";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$totalTickets = $totalRow['total'];
$totalPages = ceil($totalTickets / $limit);

$sql = "SELECT * FROM tickets ORDER BY sale_time DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="UTF-8">
  <title>后台管理</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="./icons/admin.svg" type="image/x-icon">
  <!-- Bootstrap 和 jQuery -->
  <link rel="stylesheet" href="https://cdn.staticfile.net/twitter-bootstrap/5.3.2/css/bootstrap.min.css">
  <link href="https://cdn.staticfile.net/bootstrap-icons/1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.staticfile.net/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/qrcodejs/1.0.0/qrcode.min.js"></script>
  <style>
    body {
      background: #f2f2f2;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding-bottom: 70px;
    }

    .header-bar {
      background-color: #40c4ff;
      padding: 1.5rem 1rem;
      color: #fff;
      font-weight: 700;
      font-size: 2rem;
      text-align: center;
      border-bottom: 1px solid #ffffff44;
    }

    .header-bar i {
      margin-right: 0.5rem;
    }

    .container {
      padding: 1rem;
      max-width: 900px;
      margin: 0 auto;
    }

    .bottom-tabbar {
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 60px;
      background: #ffffffdd;
      backdrop-filter: blur(8px);
      display: flex;
      justify-content: space-around;
      align-items: center;
      border-top: 1px solid #ddd;
      z-index: 1000;
    }

    .bottom-tabbar a {
      color: #888;
      text-decoration: none;
      text-align: center;
      font-size: 0.8rem;
      flex-grow: 1;
    }

    .bottom-tabbar a.active {
      color: #40c4ff;
      font-weight: bold;
    }

    .bottom-tabbar a i {
      display: block;
      font-size: 1.4rem;
    }
  </style>
</head>

<body>
  <div class="header-bar">
    <i class="bi bi-sliders"></i>后台管理
  </div>

  <div class="container">
    <a href="admin_add.php" class="btn btn-primary mb-3">手动增加票</a>
    <form id="batchForm" method="post" action="admin_delete.php">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>票号</th>
            <th>售票时间</th>
            <th>检票状态</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><input type="checkbox" name="ticket_ids[]" value="<?php echo $row['id']; ?>"></td>
                <td><?php echo $row['ticket_number']; ?></td>
                <td><?php echo $row['sale_time']; ?></td>
                <td><?php echo $row['check_status']; ?></td>
                <td>
                  <a href="admin_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">删除</a>
                  <?php if ($row['check_status'] == '未检票'): ?>
                    <a href="admin_action.php?action=check&id=<?php echo $row['id']; ?>"
                      class="btn btn-success btn-sm">手动检票</a>
                  <?php endif; ?>
                  <button type="button" class="btn btn-info btn-sm showQRBtn"
                    data-ticket="<?php echo $row['ticket_number']; ?>">显示二维码</button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5">暂无票据</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
      <button type="submit" class="btn btn-danger">批量删除</button>
    </form>

    <!-- 分页器 -->
    <nav class="mt-4">
      <ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <li class="page-item <?php if ($i == $page)
            echo 'active'; ?>">
            <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </div>

  <div class="bottom-tabbar">
    <a href="sale.php"><i class="bi bi-ticket-perforated"></i>出票</a>
    <a href="check.php"><i class="bi bi-check2-circle"></i>检票</a>
    <a href="admin.php" class="active"><i class="bi bi-sliders"></i>后台</a>
    <a href="dashboard.php"><i class="bi bi-bar-chart-line"></i>数据</a>
  </div>

  <!-- 二维码显示 Modal -->
  <div class="modal fade" id="qrModal" tabindex="-1" role="dialog" aria-labelledby="qrModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">票二维码</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="关闭">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body text-center">
          <div id="modalQR"></div>
          <p id="modalTicket"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.staticfile.net/twitter-bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script>
    $("#selectAll").click(function () {
      $("input[name='ticket_ids[]']").prop('checked', this.checked);
    });

    $(".showQRBtn").click(function () {
      var ticket = String($(this).data("ticket"));
      $("#modalTicket").text("票号：" + ticket);
      $("#modalQR").empty();
      var modal = new bootstrap.Modal(document.getElementById('qrModal'));
      modal.show();
      setTimeout(function () {
        new QRCode(document.getElementById("modalQR"), {
          text: ticket,
          width: 200,
          height: 200,
          colorDark: "#000000",
          colorLight: "#ffffff",
          correctLevel: QRCode.CorrectLevel.H
        });
      }, 300);
    });
  </script>
</body>

</html>
<?php $conn->close(); ?>