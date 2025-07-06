<?php
// dashboard_data.php
include 'config.php';

$totalSql = "SELECT COUNT(*) as total FROM tickets";
$totalResult = $conn->query($totalSql);
$totalRow = $totalResult->fetch_assoc();
$total = $totalRow['total'];

$checkedSql = "SELECT COUNT(*) as checked FROM tickets WHERE check_status = '已检票'";
$checkedResult = $conn->query($checkedSql);
$checkedRow = $checkedResult->fetch_assoc();
$checked = $checkedRow['checked'];

$notChecked = $total - $checked;

header('Content-Type: application/json');
echo json_encode(["total" => $total, "checked" => $checked, "notChecked" => $notChecked]);
$conn->close();
?>
