<?php
// sale_ajax.php
include 'config.php';

function generateTicketNumber($conn) {
    $attempt = 0;
    do {
        // 生成8位数字票号（前置0补齐）
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
    echo json_encode(["success" => true, "ticket_number" => $ticketNumber]);
} else {
    echo json_encode(["success" => false]);
}
$conn->close();
?>
