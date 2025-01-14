<?php

require_once 'db.php';

// セッションにusernameが設定されていない場合、エラーメッセージを表示
if (!isset($_SESSION['username'])) {
  die("ユーザー名がセッションに設定されていません");
}

$userName = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $characterId = intval($_POST['character']);
  $action = $_POST['action'];

  // 選択したキャラクターと敵を取得
  $character = array_filter($party, fn($char) => $char['id'] === $characterId)[0];

  if ($action === 'attack') {
      $targetEnemy = &$enemies[array_rand($enemies)]; // ランダムな敵を選択
      $targetEnemy['hp'] -= $character['attack'];

      if ($targetEnemy['hp'] <= 0) {
          $targetEnemy['hp'] = 0;
          $log[] = "{$character['name']}が{$targetEnemy['name']}を倒した！";
      } else {
          $log[] = "{$character['name']}が{$targetEnemy['name']}を攻撃し、{$character['attack']}のダメージを与えた！";
      }
  }

  // 更新された敵情報やログをフロントに送信
}
