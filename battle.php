<?php

require_once 'db.php';

// セッションにusernameが設定されていない場合、エラーメッセージを表示
if (!isset($_SESSION['username'])) {
  die("ユーザー名がセッションに設定されていません");
}

$userName = $_SESSION['username'];
