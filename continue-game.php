<?php

session_start();

require_once 'db.php';

$userName = $_SESSION['username'];

$sql = "SELECT gold FROM users WHERE username = :username";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':username', $userName, PDO::PARAM_STR);
$stmt->execute();

$status = $stmt->fetch(PDO::FETCH_ASSOC);

echo '
  <div style="position: absolute; top: 0px; left: 10px; display: flex; justify-content: space-between; align-items: center; width: 50%; margin: 10px auto; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9;">
    <div style="flex: 1; font-weight: bold;">ユーザ名：</div>
    <div style="flex: 2; text-align: left;">' . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') . '</div>
    <div style="flex: 1; font-weight: bold;">所持金：</div>
    <div style="flex: 2; text-align: left;">' . number_format($status['gold']) . '</div>
  </div>
';

?>