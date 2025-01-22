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
  <!-- カスタムスクリプト -->
  <script src="scripts.js" defer></script>
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
      <div class="dungeon-list">
        <h3>ダンジョン一覧</h3>
        <?php foreach ($activeDungeons as $d): ?>
            <div id="dungeon-<?= $d['id'] ?>" class="dungeon-box" onclick="selectDungeon(<?= $d['id'] ?>)">
                <div class="dungeon-header">
                  <?= htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <div class="dungeon-info">
                    <span>必要レベル: <?= $d['required_level'] ?></span>
                    <span>フロア数: <?= $d['floor'] ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        <button id="challenge-dungeon-btn" style="display: none;" onclick="startChallenge()">挑戦する</button>
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
          <div class="party-head">
            <h3>パーティー一覧</h3>
            <!-- 新規登録ボタン -->
            <button id="add-party-btn" class="btn btn-primary" onclick="checkPartyLimit()">
              新規登録
            </button>
          </div>
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
          <button id="delete-party-btn" onclick="deleteParty()" style="display: none;">
            このパーティーを削除する
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
                <p id="modal-message">ここに動的メッセージを表示します。</p>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
              </div>
          </div>
      </div>
  </div>

  <!-- 新規パーティー登録フォーム -->
  <div id="party-registration-form-container" class="hidden-form">
  <div class="form-wrapper">
    <h3>新しいパーティーを登録</h3>
    <form id="party-registration-form">
      <div class="form-group">
        <label for="party-name">パーティー名</label>
        <input type="text" id="party-name" name="party_name" class="form-control" required>
      </div>
      <?php
      for ($i = 1; $i <= 4; $i++): ?>
        <div class="form-group">
          <label for="member<?= $i ?>">メンバー<?= $i ?></label>
          <select id="member<?= $i ?>" name="member<?= $i ?>" class="form-select character-select">
            <option value="">選択してください</option>
            <?php foreach ($characters as $character): ?>
              <option value="<?= $character['id'] ?>"><?= htmlspecialchars($character['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endfor; ?>
      <!-- メンバー2～4も同様に追加 -->
      <div class="form-group">
        <button type="button" class="btn btn-primary" onclick="submitPartyRegistration()">登録</button>
        <button type="button" class="btn btn-secondary" onclick="hidePartyRegistrationForm()">キャンセル</button>
      </div>
    </form>
  </div>
</div>

</div>


<!-- BootstrapのJSおよび依存ライブラリ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>



<script>
// 選択されたパーティーIDを保存する
let selectedPartyId = <?= isset($selectedPartyId) ? $selectedPartyId : 'null' ?>;
</script>
