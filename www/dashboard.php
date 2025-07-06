<?php
// dashboard.php
?>
<!DOCTYPE html>
<html lang="zh">

<head>
  <meta charset="UTF-8" />
  <title>检票数据</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="shortcut icon" href="/icons/dashboard.svg" type="image/x-icon" />
  <!-- Bootstrap & jQuery -->
  <link rel="stylesheet" href="https://cdn.staticfile.net/twitter-bootstrap/5.3.2/css/bootstrap.min.css" />
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <script src="https://cdn.staticfile.net/jquery/3.7.1/jquery.min.js"></script>
  <!-- Chart.js -->
  <script src="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/Chart.js/3.7.1/chart.min.js"></script>
  <style>
    body {
      background: #f0f8ff;
      /* 轻柔浅蓝 */
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding-bottom: 70px;
      /* 底栏预留 */
      color: #333;
    }

    .header-bar {
      background: #40c4ff;
      color: #fff;
      font-weight: 700;
      font-size: 2rem;
      text-align: center;
      padding: 1rem;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 10;
    }

    .container {
      max-width: 800px;
      margin: 6rem auto 0;
      /* 下边距去掉，改成0 */
      padding: 1rem 1.5rem;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }

    h1 {
      font-weight: 700;
      color: #2c3e50;
      margin-bottom: 2rem;
      text-align: center;
    }

    .stats-row {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 1.5rem;
    }

    .stat-box {
      flex: 1 1 150px;
      background: #e6f7ff;
      padding: 20px;
      border-radius: 12px;
      text-align: center;
      box-shadow: 0 3px 6px rgb(64 196 255 / 0.3);
    }

    .stat-number {
      font-size: 3rem;
      font-weight: 700;
      color: #34495e;
      margin-bottom: 0.25rem;
    }

    .stat-label {
      font-size: 1.2rem;
      color: #40c4ff;
      font-weight: 600;
    }

    .stat-percent {
      font-size: 3rem;
      font-weight: 700;
      color: #34495e;
      margin-top: 0.25rem;
    }

    /* 限制canvas大小 */
    #pieChartWrapper {
      max-width: 350px;
      max-height: 350px;
      margin: 2rem auto 0;
    }

    #pieChart {
      width: 100% !important;
      height: auto !important;
      display: block;
      box-sizing: border-box;
    }

    /* 底部tab栏 */
    .bottom-tabbar {
      position: fixed;
      bottom: 0;
      width: 100%;
      height: 60px;
      background: #ffffffdd;
      backdrop-filter: blur(8px);
      display: flex;
      justify-content: space-around;
      align-items: center;
      border-top: 1px solid #ddd;
      z-index: 15;
    }

    .bottom-tabbar a {
      color: #888;
      text-decoration: none;
      font-size: 0.8rem;
      flex-grow: 1;
      text-align: center;
    }

    .bottom-tabbar a.active {
      color: #40c4ff;
      font-weight: bold;
    }

    .bottom-tabbar a i {
      font-size: 1.4rem;
      display: block;
    }
  </style>
</head>

<body>
  <div class="header-bar">
    <i class="bi bi-bar-chart-line" style="margin-right: 8px; vertical-align: middle; font-size: 1.5rem;"></i>
    检票数据
  </div>

  <div class="container">
    <div class="stats-row">
      <div class="stat-box">
        <div id="totalTickets" class="stat-number">0</div>
        <div class="stat-label">总票数</div>
      </div>
      <div class="stat-box">
        <div id="checkedTickets" class="stat-number">0</div>
        <div class="stat-label">已检票</div>
      </div>
      <div class="stat-box">
        <div id="checkedPercent" class="stat-percent">0%</div>
        <div class="stat-label">检票百分比</div>
      </div>
    </div>

    <div id="pieChartWrapper">
      <canvas id="pieChart" aria-label="已检票与未检票比例饼图" role="img"></canvas>
    </div>
  </div>

  <div class="bottom-tabbar">
    <a href="sale.php"><i class="bi bi-ticket"></i>出票</a>
    <a href="check.php"><i class="bi bi-check2-circle"></i>检票</a>
    <a href="admin.php"><i class="bi bi-sliders"></i>后台</a>
    <a href="dashboard.php" class="active"><i class="bi bi-bar-chart-line"></i>数据</a>
  </div>

  <script src="https://cdn.staticfile.net/twitter-bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.staticfile.net/jquery/3.7.1/jquery.min.js"></script>
  <script src="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/Chart.js/3.7.1/chart.min.js"></script>
  <script>
    const totalEl = $('#totalTickets');
    const checkedEl = $('#checkedTickets');
    const percentEl = $('#checkedPercent');

    const ctx = document.getElementById('pieChart').getContext('2d');
    const pieChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['已检票', '未检票'],
        datasets: [{
          data: [0, 0],
          backgroundColor: ['#80d152', '#b5f5ec'],
          hoverBackgroundColor: ['#60C42D', '#b5f5ec'],
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              color: '#34495e',
              font: { size: 14 }
            }
          }
        }
      }
    });

    function updateData() {
      $.get('dashboard_data.php', function (data) {
        const total = data.total || 0;
        const checked = data.checked || 0;
        const notChecked = data.notChecked || 0;
        const percent = total ? Math.round((checked / total) * 100) : 0;

        totalEl.text(total);
        checkedEl.text(checked);
        percentEl.text(percent + '%');

        pieChart.data.datasets[0].data = [checked, notChecked];
        pieChart.update();
      }, 'json');
    }

    // 初始化数据和定时刷新
    updateData();
    setInterval(updateData, 5000);
  </script>
</body>

</html>