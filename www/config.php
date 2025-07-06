<?php
// config.php
$host = 'localhost';
$user = 'ticket_qingdai_f';
$password = 'bD85rPB69kKwtAxx'; // 根据需要修改
$dbname = 'ticket_qingdai_f';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("数据库连接失败: " . $conn->connect_error);
}
$conn->set_charset("utf8");
?>
