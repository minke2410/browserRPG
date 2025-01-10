<?php

session_start();

require_once 'db.php';

// セッションにusernameが設定されていない場合、エラーメッセージを表示
if (!isset($_SESSION['username'])) {
    die("ユーザー名がセッションに設定されていません");
}

$userName = $_SESSION['username'];

try {
    // Userクラスのインスタンスを作成（usernameをuserIdに変更）
    $user = new User($pdo, $userName);

    // ユーザー情報を取得
    $userName = htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8');
    $userGold = number_format($user->gold);
    $characters = $user->characters;
    $items = $user->items;
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<div style="display: flex; width: 100%; height: 100vh;">
  <!-- 左側: ユーザー情報 -->
  <div style="width: 50%; padding: 10px; box-sizing: border-box;">
    <div style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; display: flex; justify-content: space-between; align-items: center;">
      <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
        <div style="font-weight: bold;">ユーザ名：</div>
        <div><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></div>
      </div>
      <div style="display: flex; align-items: center; gap: 10px; flex: 0.8;">
        <div style="font-weight: bold;">所持金：</div>
        <div><?= number_format($user->gold) ?></div>
      </div>
    </div>
  </div>

  <!-- 右側: ナビゲーションバーとコンテンツ -->
  <div style="width: 50%; padding: 10px; border-left: 1px solid #ddd; box-sizing: border-box;">
    <!-- ナビゲーションバー -->
    <div style="display: flex; justify-content: space-around; margin-bottom: 10px; border-bottom: 1px solid #ddd; padding-bottom: 10px;">
      <button onclick="showSection('characters')" style="padding: 5px 10px;">キャラクター</button>
      <button onclick="showSection('items')" style="padding: 5px 10px;">アイテム</button>
    </div>

    <!-- コンテンツ -->
    <div id="content">
      <div id="characters" style="display: none;">
        <h3>所持キャラクター一覧</h3>
        <ul>
          <?php if (!empty($characters)): ?>
            <?php foreach ($characters as $character): ?>
              <li><?= htmlspecialchars($character, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>キャラクターがいません。</li>
          <?php endif; ?>
        </ul>
      </div>
      <div id="items" style="display: none;">
        <h3>所持アイテム一覧</h3>
        <ul>
          <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
              <li><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>アイテムがありません。</li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
  // セクション表示制御
  function showSection(sectionId) {
    // 全セクションを非表示
    document.querySelectorAll('#content > div').forEach(div => div.style.display = 'none');
    // 指定されたセクションを表示
    document.getElementById(sectionId).style.display = 'block';
  }
</script>
