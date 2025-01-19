<?php
session_start();

require_once 'db.php';         // Databaseクラスを読み込み
require_once 'User.php';       // Userクラスを読み込み
require_once 'Character.php';  // Characterクラスを読み込み
require_once 'Item.php';       // Itemクラスを読み込み
require_once 'Enemy.php';      // Enemyクラスを読み込み
require_once 'Party.php';      // Partyクラスを読み込み
require_once 'Dungeon.php';    // Dungeonクラスを読み込み

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

  // Dungeonクラスのインスタンスを作成
  $dungeon = new Dungeon($pdo);

  // アクティブなダンジョンを取得
  $activeDungeons = $dungeon->getActiveDungeonsByUser($userId);

} catch (Exception $e) {
  die("エラー: " . $e->getMessage());
}

// デフォルトセクションを設定（セッションが空の場合）
$currentSection = isset($_SESSION['section']) ? $_SESSION['section'] : 'characters';

?>

<head>
  <link rel="stylesheet" type="text/css" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>

<div class="content-wrapper">
  <!-- 左側: ユーザー情報･戦闘画面 -->
  <div class="left-panel">
    <!-- 上部:ユーザー情報 -->
    <div class="user-info" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px; background-color: #f9f9f9; display: flex; justify-content: space-between; align-items: center;">
      <div style="flex: 1; text-align: center; font-weight: bold;">ユーザー名 <?= htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') ?></div>
      <div style="flex: 1; text-align: center; font-weight: bold;">所持金 <?= number_format($user->getGold()) ?></div>
      <div style="flex: 1; text-align: center; font-weight: bold;">レベル <?= $user->getLevel() ?></div>
      <div style="flex: 2; display: flex; align-items: center; gap: 10px;">
        <span style="font-weight: bold; white-space: nowrap;">経験値</span>
        <div class="progress-bar-container" style="flex-grow: 1;">
          <div class="progress-bar" style="width: <?= min(100, ($user->getXp() % 100)) ?>%;" style="width: <?= ($user->getXp() % 100) ?>%;"></div>
          <div class="progress-bar-text">
            <?= $user->getXp() ?>
          </div>
        </div>
      </div>
    </div>

    <!-- 下部:ダンジョン情報 -->
    <div class="dungeons-container">
    <h3>ダンジョン一覧</h3>
      <ul>
        <?php foreach ($activeDungeons as $dungeon): ?>
          <li>
            <a href="#" onclick="showDungeonDetails(<?= $dungeon['id'] ?>)">
              <?= htmlspecialchars($dungeon['name'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
      <div id="dungeon-details" style="display: none;">
        <!-- ダンジョン詳細情報をここに表示 -->
      </div>
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
      <!-- キャラクター情報 -->
      <div id="characters" class="<?= $currentSection === 'characters' ? '' : 'hidden' ?>">
          <h3>所持キャラクター一覧</h3>
          <ul>
              <?php foreach ($characters as $character): ?>
                  <li>
                      <strong class="character-name" onclick="toggleCharacterDetails(<?= intval($character['id']) ?>)">
                          <?= htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8') ?>
                      </strong>
                      <div id="details-<?= $character['id'] ?>" class="character-details hidden">
                          <strong>レベル:</strong> <?= htmlspecialchars($character['level'], ENT_QUOTES, 'UTF-8') ?><br>
                          <strong>HP:</strong> <?= htmlspecialchars($character['hp'], ENT_QUOTES, 'UTF-8') ?><br>
                          <strong>攻撃力:</strong> <?= htmlspecialchars($character['attack'], ENT_QUOTES, 'UTF-8') ?><br>
                          <strong>経験値:</strong> <?= htmlspecialchars($character['xp'], ENT_QUOTES, 'UTF-8') ?><br>
                      </div>
                  </li>
              <?php endforeach; ?>
          </ul>
      </div>

      <!-- パーティー情報 -->
      <div id="parties" class="<?= $currentSection === 'parties' ? '' : 'hidden' ?>">
          <h3>パーティー一覧</h3>
          <div class="party-list">
              <?php foreach ($parties as $party): ?>
                  <div id="party-<?= $party['id'] ?>"
                       class="party-box party-item <?= isset($selectedPartyId) && $selectedPartyId == $party['id'] ? 'selected' : '' ?>"
                       onclick="selectParty(<?= $party['id'] ?>)">
                      <div class="party-header">
                          <?= htmlspecialchars($party['party_name'], ENT_QUOTES, 'UTF-8') ?>
                      </div>
                      <div class="party-members">
                          <div class="party-member"><?= htmlspecialchars($party['member1_name'] ?? '不明', ENT_QUOTES, 'UTF-8') ?></div>
                          <div class="party-member"><?= htmlspecialchars($party['member2_name'] ?? '不明', ENT_QUOTES, 'UTF-8') ?></div>
                          <div class="party-member"><?= htmlspecialchars($party['member3_name'] ?? '不明', ENT_QUOTES, 'UTF-8') ?></div>
                          <div class="party-member"><?= htmlspecialchars($party['member4_name'] ?? '不明', ENT_QUOTES, 'UTF-8') ?></div>
                      </div>
                  </div>
              <?php endforeach; ?>
          </div>
          <button id="change-party-btn" onclick="changeParty()" style="display: none;">
            このパーティーで出場する
          </button>
      </div>

      <!-- アイテム情報 -->
      <div id="items" class="<?= $currentSection === 'items' ? '' : 'hidden' ?>">
          <h3>アイテム一覧</h3>
          <ul class="item-list">
              <?php foreach ($items as $item): ?>
                  <li>
                      <div class="item-info">
                          <strong><?= htmlspecialchars($item['item_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                          <span>x<?= htmlspecialchars($item['quantity'], ENT_QUOTES, 'UTF-8') ?></span>
                      </div>
                  </li>
              <?php endforeach; ?>
          </ul>
      </div>
    </div>
  </div>
  <!-- モーダルのHTML構造 -->
  <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="myModalLabel"></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
              </div>
              <div class="modal-body">
                  出場パーティーが変更されました｡
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
              </div>
          </div>
      </div>
  </div>
</div>


<!-- BootstrapのJSおよび依存ライブラリ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- カスタムスクリプト -->
<script src="scripts.js" defer></script>


<script>
// 選択されたパーティーIDを保存する
let selectedPartyId = <?= isset($selectedPartyId) ? $selectedPartyId : 'null' ?>;
</script>
