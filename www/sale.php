<!DOCTYPE html>
<html lang="zh">

<head>
    <meta charset="UTF-8" />
    <title>出票</title>
    <link rel="shortcut icon" href="./icons/sale.svg" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.staticfile.net/twitter-bootstrap/5.3.2/css/bootstrap.min.css" />
    <!-- Bootstrap Icons -->
    <link href="https://cdn.staticfile.net/bootstrap-icons/1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- jQuery CDN -->
    <script src="https://cdn.staticfile.net/jquery/3.7.1/jquery.min.js"></script>
    <!-- QRCode.js CDN -->
    <script src="https://lf9-cdn-tos.bytecdntp.com/cdn/expire-1-M/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
        body {
            background: #f2f2f2;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 70px;
        }

        .header-bar {
            background-color: #40c4ff;
            padding: 1rem;
            color: #fff;
            font-weight: 700;
            font-size: 2rem;
            text-align: center;
            border-bottom: 1px solid #ffffff44;
        }

        .header-bar i {
            margin-right: 0.5rem;
        }

        .header-sub {
            font-size: 1rem;
            font-weight: 500;
            margin-top: 0.3rem;
        }

        .container {
            padding: 1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .btn-lg {
            font-size: 1.2rem;
            padding: 0.8rem 1.5rem;
            min-width: 200px;
        }

        #ticketInfo {
            background-color: #fff;
            border-radius: 1rem;
            padding: 2rem 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            margin-top: 1rem !important;
        }

        #ticketInfo h3 {
            font-size: 1.75rem;
            color: #28a745;
            font-weight: 700;
        }

        .ticket-details {
            margin-top: 1.5rem;
            font-size: 1.1rem;
        }

        .ticket-warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
            padding: 0.75rem 1rem;
            margin-top: 1.2rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        /* 新增折叠隐藏样式，不破坏布局 */
        .collapse-hide {
            visibility: hidden;
            height: 0;
            overflow: hidden;
            transition: visibility 0.3s ease, height 0.3s ease;
        }

        #qrcode {
            margin-top: 0.6rem;
            display: flex;
            justify-content: center;
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
        <i class="bi bi-ticket-perforated"></i>售票
        <div class="header-sub">
            当前阶段：
            <span id="currentPhase">加载中...</span> ｜ 已出票：
            <span id="totalCount">加载中...</span> 张
        </div>
    </div>

    <div class="container">
        <!-- 按钮容器，方便隐藏 -->
        <div id="buttonsContainer" class="text-center">
            <button id="saleBtn" class="btn btn-primary btn-lg mt-4">普通票</button>
            <br>
            <button id="presaleBtn" class="btn btn-danger btn-lg mt-3">预售票</button>
        </div>

        <div id="ticketInfo" class="mt-5 text-center" style="display: none;" aria-live="polite" aria-atomic="true">
            <h3>
                <i class="bi bi-check-circle-fill me-1"></i>出票成功
            </h3>
            <div class="ticket-details">
                <div style="display: flex; justify-content: center; align-items: center;">
                    <span>票号：</span>
                    <strong style="font-size: 1.2rem" id="ticketNumber"></strong>
                </div>
            </div>
            <div id="qrcode"></div>
            <div class="ticket-warning">
                ⚠️请妥善保留二维码与票号，<br>入场请出示。
            </div>
            <button id="newTicketBtn" class="btn btn-primary mt-3">完成</button>
        </div>
    </div>

    <div class="bottom-tabbar">
        <a href="sale.php" class="active"><i class="bi bi-ticket-perforated"></i>出票</a>
        <a href="check.php"><i class="bi bi-check2-circle"></i>检票</a>
        <a href="admin.php"><i class="bi bi-sliders"></i>后台</a>
        <a href="dashboard.php"><i class="bi bi-bar-chart-line"></i>数据</a>
    </div>

    <script>
        let currentTotal = 0;
        let qrcodeInstance = null;
        let isIssuing = false;

        function checkTicketAvailability(callback) {
            $.get("dashboard_data.php", function (data) {
                const total = parseInt(data.total);
                if (!isNaN(total)) {
                    currentTotal = total;
                    $("#totalCount").text(total);
                    const phase = total < 100 ? "预售阶段" : "正式阶段";
                    $("#currentPhase").text(phase);
                    callback(total);
                } else {
                    alert("获取票数失败，请稍后重试");
                }
            }, "json").fail(function () {
                alert("无法连接到服务器，请检查网络");
            });
        }

        function updateButtonStates() {
            checkTicketAvailability(function (total) {
                const isPresale = total < 100;
                $("#presaleBtn").prop("disabled", !isPresale);
                $("#saleBtn").prop("disabled", isPresale);
            });
        }

        $(document).ready(function () {
            updateButtonStates();
        });

        function attachClickWithDisabledCheck(
            buttonId,
            conditionCheck,
            errorMessage,
            successCallback
        ) {
            $(document).on("click", buttonId, function (e) {
                e.preventDefault();
                if ($(this).prop("disabled")) {
                    alert("请先点击完成");
                    return;
                }
                if (isIssuing) {
                    alert("出票请求进行中，请稍候");
                    return;
                }
                checkTicketAvailability(function (total) {
                    if (!conditionCheck(total)) {
                        alert(errorMessage);
                    } else {
                        isIssuing = true;
                        successCallback();
                    }
                });
            });
        }

        attachClickWithDisabledCheck(
            "#saleBtn",
            (total) => total >= 100,
            "当前为预售阶段，请使用红色的预售出票按钮。",
            function () {
                issueTicket("sale_ajax.php");
            }
        );

        attachClickWithDisabledCheck(
            "#presaleBtn",
            (total) => total < 100,
            "当前已进入正式出票阶段，请使用普通出票按钮。",
            function () {
                issueTicket("sale_ajax.php");
            }
        );

        function issueTicket(apiUrl) {
            $.ajax({
                url: apiUrl,
                method: "POST",
                dataType: "json",
                success: function (data) {
                    if (data.success && data.ticket_number && data.ticket_number.trim() !== "") {
                        $("#ticketNumber").text(data.ticket_number);
                        if (qrcodeInstance) {
                            $("#qrcode").empty();
                        }
                        qrcodeInstance = new QRCode(document.getElementById("qrcode"), {
                            text: data.ticket_number,
                            width: 200,
                            height: 200,
                        });
                        $("#ticketInfo").show();
                        // 隐藏按钮区，但不破坏布局
                        $("#buttonsContainer").addClass("collapse-hide");
                    } else {
                        alert("出票失败，请重试");
                    }
                },
                error: function () {
                    alert("出票请求出错");
                },
                complete: function () {
                    isIssuing = false;
                },
            });
        }

        $("#newTicketBtn").click(function () {
            $("#ticketInfo").hide();
            // 恢复显示按钮区
            $("#buttonsContainer").removeClass("collapse-hide");
            updateButtonStates();
        });
    </script>
</body>

</html>