<?php
session_start();

require_once 'db.php';         // Databaseクラスを読み込み
require_once 'User.php';       // Userクラスを読み込み
require_once 'Character.php';  // Characterクラスを読み込み
require_once 'Item.php';       // Itemクラスを読み込み
require_once 'Enemy.php';       // Enemyクラスを読み込み
require_once 'Party.php';       // Partyクラスを読み込み

// セッションにユーザーIDが設定されていない場合、エラーメッセージを表示
if (!isset($_SESSION['user_id'])) {
    die("ユーザーIDがセッションに設定されていません");
}

$userId = $_SESSION['user_id'];

// セクションがURLパラメータで指定された場合、セッションに保存
if (isset($_GET['section'])) {
    $_SESSION['section'] = $_GET['section'];
}

try {
  // データベース接続を取得
  $db = new Database();
  $pdo = $db->getConnection();

  // Userクラスのインスタンスを作成してユーザー情報を取得
  $user = new User($pdo, $userId);
  $userName = htmlspecialchars($user->username, ENT_QUOTES, 'UTF-8');
  $userGold = number_format($user->gold);

  // Characterクラスのインスタンスを作成してキャラクター情報を取得
  $character = new Character($pdo, $userId);
  $characters = $character->getCharacters();

  // Itemクラスのインスタンスを作成してアイテム情報を取得
  $item = new Item($pdo, $userId);
  $items = $item->getItems();

  // Enemyクラスのインスタンスを作成して敵情報を取得
  $enemy = new Enemy($pdo);
  $enemies = $enemy->getEnemies();

  // Partyクラスのインスタンスを作成してパーティー情報を取得
  $party = new Party($pdo, $userId);
  $parties = $party->getParties();

} catch (Exception $e) {
  die("エラー: " . $e->getMessage());
}

// デフォルトセクションを設定（セッションが空の場合）
$currentSection = isset($_SESSION['section']) ? $_SESSION['section'] : 'characters';

?>

<head>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<div class="content-wrapper">
  <!-- 左側: ユーザー情報･戦闘画面 -->
  <div class="left-panel">
    <!-- 上部:ユーザー情報 -->
    <div style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; display: flex; justify-content: space-between; align-items: center;">
      <div style="display: flex; align-items: center; gap: 10px; flex: 1;">
        <div style="font-weight: bold;">ユーザー名：</div>
        <div><?= htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') ?></div>
      </div>
      <div style="display: flex; align-items: center; gap: 10px; flex: 0.8;">
        <div style="font-weight: bold;">所持金：</div>
        <div><?= $userGold ?></div>
      </div>
    </div>

    <!-- 下部:キャラクターステータスボックス -->
    <div class="status-boxes">
      <?php foreach ($characters as $character): ?>
        <div class="status-box">
          <h4><?= htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8') ?></h4>
          <p>レベル: <?= htmlspecialchars($character['level'], ENT_QUOTES, 'UTF-8') ?></p>
          <p>HP: <?= htmlspecialchars($character['hp'], ENT_QUOTES, 'UTF-8') ?></p>
          <p>攻撃力: <?= htmlspecialchars($character['attack'], ENT_QUOTES, 'UTF-8') ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>


  <!-- 右側: ナビゲーションバーとコンテンツ -->
  <div class="right-panel">
    <!-- ナビゲーションバー -->
    <div class="nav-bar">
      <button onclick="showSection('characters')" style="padding: 5px 10px;">キャラクター</button>
      <button onclick="showSection('parties')" style="padding: 5px 10px;">編成</button>
      <button onclick="showSection('items')" style="padding: 5px 10px;">アイテム</button>
    </div>
  
    <!-- コンテンツ -->
    <div id="content">
      <!-- キャラクター情報の表示 -->
      <div id="characters" style="<?= $currentSection === 'characters' ? 'display:block;' : 'display:none;' ?>">
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

      <!-- 編成情報の表示 -->
      <div id="parties" style="<?= $currentSection === 'parties' ? 'display:block;' : 'display:none;' ?>">
        <h3>パーティー一覧</h3>
        <ul>
            <?php if (!empty($parties)): ?>
                <?php foreach ($parties as $party): ?>
                    <li class="party-item">
                        <strong class="party-name"><?= htmlspecialchars($party['party_name'], ENT_QUOTES, 'UTF-8') ?></strong><br>
                        <span class="party-member">メンバー1: <?= htmlspecialchars($party['member1_name'] ?? '不明', ENT_QUOTES, 'UTF-8') ?></span><br>
                        <span class="party-member">メンバー2: <?= htmlspecialchars($party['member2_name'] ?? '不明', ENT_QUOTES, 'UTF-8') ?></span><br>
                        <span class="party-member">メンバー3: <?= htmlspecialchars($party['member3_name'] ?? '不明', ENT_QUOTES, 'UTF-8') ?></span><br>
                        <span class="party-member">メンバー4: <?= htmlspecialchars($party['member4_name'] ?? '不明', ENT_QUOTES, 'UTF-8') ?></span><br>
                        <button class="toggle-active-btn" data-party-id="<?= $party['id'] ?>" 
                          data-is-active="<?= isset($party['is_active']) ? $party['is_active'] : 0 ?>"> 
                          <?= isset($party['is_active']) && $party['is_active'] ? 'アクティブ解除' : 'アクティブにする' ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>アクティブなパーティーがありません。</li>
            <?php endif; ?>
        </ul>
      </div>
      <!-- アイテム情報の表示 -->
      <div id="items" style="<?= $currentSection === 'items' ? 'display:block;' : 'display:none;' ?>">
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
    // セッションに現在のセクションを保存
    const url = new URL(window.location.href);
    url.searchParams.set('section', sectionId);
    window.location.href = url.toString(); // URLを更新してページをリロード
  }

  // キャラクターの詳細情報をトグル（表示/非表示）する関数
  function toggleCharacterDetails(characterId) {
    // すべてのキャラクター詳細を取得
    const allDetails = document.querySelectorAll('.character-details');
    
    // 他のキャラクターの詳細情報を非表示にする
    allDetails.forEach(detail => {
      // 他のキャラクターの詳細が表示されていれば非表示にする
      if (detail.id !== 'details-' + characterId) {
        detail.style.display = 'none';
      }
    });

    // クリックされたキャラクターの詳細をトグル
    const detailsDiv = document.getElementById('details-' + characterId);
    
    // 詳細情報の表示状態を切り替え
    if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
      detailsDiv.style.display = 'block';
    } else {
      detailsDiv.style.display = 'none';
    }
  }

  document.getElementById('command-form').addEventListener('submit', function(e) {
  e.preventDefault();

  const character = document.getElementById('character').value;
  const action = document.getElementById('action').value;

  // 仮のログメッセージを生成（本来はサーバーから結果を受け取る）
  const logMessage = `${character}が${action}を選択しました！`;
  
  const logContainer = document.getElementById('log-messages');
  const logEntry = document.createElement('div');
  logEntry.textContent = logMessage;
  logContainer.appendChild(logEntry);
  });

  document.addEventListener('DOMContentLoaded', () => {
    const toggleButtons = document.querySelectorAll('.toggle-active-btn');

    toggleButtons.forEach(button => {
        button.addEventListener('click', async () => {
            const partyId = button.getAttribute('data-party-id');
            const isActive = button.getAttribute('data-is-active') === '1';

            try {
                const response = await fetch('update_active_party.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ party_id: partyId, is_active: !isActive })
                });

                const result = await response.json();
                if (result.success) {
                    location.reload(); // 成功したらページをリロード
                } else {
                    alert('エラー: ' + result.message);
                }
            } catch (error) {
                alert('通信エラーが発生しました。');
            }
        });
    });
});

</script>
