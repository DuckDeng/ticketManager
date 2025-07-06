<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8" />
    <title>检票</title>
    <link rel="shortcut icon" href="./icons/check.svg" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="https://cdn.staticfile.net/twitter-bootstrap/5.3.2/css/bootstrap.min.css" />
    <link href="https://cdn.staticfile.net/bootstrap-icons/1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <script src="https://cdn.staticfile.net/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.staticfile.net/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

    <style>
        body {
            background: #f2f2f2;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 70px;
            margin: 0;
        }

        .header-bar {
            background-color: #40c4ff;
            padding: 1rem;
            color: #fff;
            font-weight: 700;
            font-size: 2rem;
            text-align: center;
            border-bottom: 1px solid #ffffff44;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1100;
        }

        .header-bar i {
            margin-right: 0.5rem;
        }

        .container {
            padding: 0 1.4rem;
            max-width: 600px;
            margin: 7.5rem auto 0 auto;
        }

        label {
            font-weight: 600;
            font-size: 1.1rem;
        }

        #ticketInput {
            font-size: 1.1rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #ced4da;
            width: 100%;
            box-sizing: border-box;
            margin-top: 0.3rem;
        }

        #checkBtn {
            font-size: 1.2rem;
            padding: 0.6rem 1.5rem;
            width: 100%;
            margin-top: 1rem;
        }

        #resultMessage {
            margin-top: 1rem;
            font-size: 1.2rem;
            min-height: 1.6em;
            color: #333;
            text-align: center;
            font-weight: 600;
        }

        #reader {
            width: 100%;
            max-width: 320px;
            margin: 2rem auto 1rem auto;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        #successOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 255, 0, 0.8);
            color: white;
            font-size: 4rem;
            text-align: center;
            line-height: 100vh;
            font-weight: bold;
            z-index: 1200;
            animation: fadeOut 1.5s forwards;
        }

        @keyframes fadeOut {
            0% {
                opacity: 1;
            }

            100% {
                opacity: 0;
                display: none;
            }
        }

        .modal-alert {
            display: none;
            position: fixed;
            z-index: 1300;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-alert-content {
            background-color: #fefefe;
            margin: 20% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 400px;
            text-align: center;
            font-size: 1.2rem;
            border-radius: 0.5rem;
        }

        .modal-alert-content button {
            margin-top: 1rem;
            font-size: 1rem;
            padding: 0.4rem 1rem;
            border-radius: 0.3rem;
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
        <i class="bi bi-check2-circle"></i>检票
    </div>

    <div class="container" role="main">
        <h4>扫码检票</h4>
        <div id="reader" aria-label="二维码扫码区域"></div>
        <hr />
        <h4>人工输入</h4>
        <input type="text" id="ticketInput" placeholder="请输入票号或等待扫码自动填写" aria-label="票号输入框" />
        <button id="checkBtn" class="btn btn-primary">检票</button>
        <div id="resultMessage" aria-live="polite" aria-atomic="true"></div>

    </div>

    <div id="successOverlay" role="alert" aria-live="assertive" aria-atomic="true">✅ 通行 ➡️</div>

    <div id="errorModal" class="modal-alert" role="alertdialog" aria-modal="true" aria-labelledby="errorTitle"
        aria-describedby="errorDesc">
        <div class="modal-alert-content">
            <div id="errorTitle" style="font-weight: 700; font-size: 1.3rem;">检票异常</div>
            <div id="errorDesc" style="margin-top: 0.5rem;">请联系工作人员或出示付款凭证</div>
            <button id="closeModal" class="btn btn-secondary mt-3">关闭</button>
        </div>
    </div>

    <div class="bottom-tabbar">
        <a href="sale.php"><i class="bi bi-ticket-perforated"></i>出票</a>
        <a href="check.php" class="active"><i class="bi bi-check2-circle"></i>检票</a>
        <a href="admin.php"><i class="bi bi-sliders"></i>后台</a>
        <a href="dashboard.php"><i class="bi bi-bar-chart-line"></i>数据</a>
    </div>

    <script>
        const cooldownPeriod = 1000;
        let isCooldown = false;
        let lastScanned = "";
        let lastScanTime = 0;

        $("#closeModal").click(function () {
            $("#errorModal").fadeOut();
        });

        function checkTicket(ticket) {
            if (isCooldown || !ticket) {
                if (!ticket) alert("请输入票号！");
                return;
            }

            isCooldown = true;
            $.post("check_action.php", { ticket }, function (data) {
                if (data.success) {
                    $("#successOverlay").fadeIn(200).delay(700).fadeOut(500);
                    $("#resultMessage").text("✅ 票号通过").css("color", "#28a745");
                } else {
                    $("#errorModal").fadeIn();
                    $("#resultMessage").text("❌ 票号无效").css("color", "#dc3545");
                }
                $("#ticketInput").val("");
                setTimeout(() => {
                    isCooldown = false;
                    $("#resultMessage").text("");
                }, cooldownPeriod);
            }, "json");
        }

        $("#checkBtn").click(() => {
            const ticket = $("#ticketInput").val().trim();
            checkTicket(ticket);
        });

        function onScanSuccess(decodedText) {
            const now = Date.now();
            if (decodedText === lastScanned && now - lastScanTime < cooldownPeriod) return;
            lastScanned = decodedText;
            lastScanTime = now;
            $("#ticketInput").val(decodedText);
            checkTicket(decodedText);
        }

        const html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</body>

</html>