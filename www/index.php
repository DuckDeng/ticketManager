<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="UTF-8">
  <title>萌格式票务系统-首页</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="./icons/icon.svg" type="image/x-icon">

  <!-- Bootstrap + Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.staticfile.net/twitter-bootstrap/5.3.2/css/bootstrap.min.css">
  <link href="https://cdn.staticfile.net/bootstrap-icons/1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    html, body {
      height: 100%;
      margin: 0;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background: #f2f2f2;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
      background-color: #40c4ff;
    }

    .navbar-brand {
      color: #fff;
      font-weight: heavy;
      font-size: 2rem;
    }

    .navbar-brand:hover {
      color: #ff80ab;
    }

    .grid-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: 1.5rem;
      margin-top: 2rem;
    }

    .card {
      text-align: center;
      padding: 2rem 1rem;
      border: none;
      border-radius: 12px;
      background: #ffffffcc;
      backdrop-filter: blur(4px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.2s ease-in-out, background-color 0.25s ease, box-shadow 0.25s ease;
      user-select: none;
    }

    .card:hover {
      transform: translateY(-5px);
    }

    .card:active {
      background-color: #007bbd;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
      transform: scale(0.97);
    }

    .card i {
      font-size: 2.5rem;
      margin-bottom: 0.75rem;
      color: #40c4ff;
      transition: color 0.25s ease;
    }

    .card:active i {
      color: #ffffff;
    }

    .card div {
      color: #3a3a3a;
      font-weight: 600;
      font-size: 1.2rem;
    }

    /* 页脚样式 */
    .footer {
      background: #f8f9fa;
      font-size: 0.9rem;
    }

    .main-content {
      flex: 1;
    }
  </style>
</head>

<body>
  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="#">萌格式票务系统</a>
    </div>
  </nav>

  <div class="container main-content">
    <div class="grid-container">
      <a href="sale.php" class="card text-decoration-none">
        <i class="bi bi-ticket-perforated"></i>
        <div class="mt-2">售票</div>
      </a>
      <a href="check.php" class="card text-decoration-none">
        <i class="bi bi-check2-circle"></i>
        <div class="mt-2">检票</div>
      </a>
      <a href="admin.php" class="card text-decoration-none">
        <i class="bi bi-sliders"></i>
        <div class="mt-2">后台管理</div>
      </a>
      <a href="dashboard.php" class="card text-decoration-none">
        <i class="bi bi-bar-chart-line"></i>
        <div class="mt-2">实时检票数据</div>
      </a>
    </div>
  </div>

  <footer class="footer text-center text-muted py-4 mt-4">
    <hr style="max-width: 300px; margin: 20px auto;">
    <div>本网站由 AI 辅助制作。</div>
    <div>Copyright © 2025 JunjianDeng. All rights reserved.</div>
  </footer>
</body>

</html>
