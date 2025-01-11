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
<head>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<div class="content-wrapper">
  <!-- 左側: ユーザー情報 -->
  <div class="left-panel">
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
  <div class="right-panel">
    <!-- ナビゲーションバー -->
    <div class="nav-bar">
      <button onclick="showSection('characters')" style="padding: 5px 10px;">キャラクター</button>
      <button onclick="showSection('items')" style="padding: 5px 10px;">アイテム</button>
    </div>

    <!-- コンテンツ -->
    <div id="content">
      <!-- キャラクター情報の表示 -->
      <div id="characters">
        <h3>所持キャラクター一覧</h3>
        <ul>
          <?php if (!empty($characters)): ?>
            <?php foreach ($characters as $character): ?>
              <li>
                <strong class="character-name" onclick="toggleCharacterDetails(<?= intval($character['id']) ?>)">
                  <?= htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8') ?>
                </strong>
                <div id="details-<?= $character['id'] ?>" class="character-details hidden" style="padding-left: 20px;">
                  <strong>レベル:</strong> <?= htmlspecialchars($character['level'], ENT_QUOTES, 'UTF-8') ?><br>
                  <strong>HP:</strong> <?= htmlspecialchars($character['hp'], ENT_QUOTES, 'UTF-8') ?><br>
                  <strong>攻撃力:</strong> <?= htmlspecialchars($character['attack'], ENT_QUOTES, 'UTF-8') ?><br>
                  <strong>経験値:</strong> <?= htmlspecialchars($character['xp'], ENT_QUOTES, 'UTF-8') ?><br>
                </div>
              </li>
            <?php endforeach; ?>
          <?php else: ?>
            <li>キャラクターがいません。</li>
          <?php endif; ?>
        </ul>
      </div>


      <!-- アイテム情報の表示 -->
      <div id="items">
        <h3>所持アイテム一覧</h3>
        <ul>
          <?php if (!empty($items)): ?>
            <?php foreach ($items as $item): ?>
              <li>
                <div class="item-info">
                  <strong><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                  <span><?= htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8') ?>個</span>
                </div>
              </li>
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
    // URLパラメータを更新
    const url = new URL(window.location.href);
    url.searchParams.set('section', sectionId);
    history.replaceState(null, '', url);
  }

  // ページロード時にURLパラメータからセクションを選択
  window.onload = function() {
    const url = new URL(window.location.href);
    const section = url.searchParams.get('section') || 'characters'; // デフォルトは'characters'
    showSection(section);
  };

  // キャラクター詳細を表示/非表示にする関数
  function toggleCharacterDetails(characterId) {
    // クリックされたキャラクターの詳細の要素を取得
    const details = document.getElementById('details-' + characterId);

    // すべての詳細を非表示にする
    const allDetails = document.querySelectorAll('.character-details');
    allDetails.forEach(detail => {
      detail.classList.add('hidden');  // hiddenクラスを追加して非表示にする
    });

    // クリックされたキャラクターの詳細をトグル（表示/非表示）
    if (details.style.display === 'none' || details.style.display === '') {
      details.style.display = 'block';  // 詳細を表示
    } else {
      details.style.display = 'none';   // 詳細を隠す
    }
  }

</script>
