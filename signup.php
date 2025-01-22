<?php
session_start();

require_once 'db.php';
require_once 'Character.php';
require_once 'Dungeon.php';
require_once 'Item.php';
require_once 'Party.php';
require_once 'User.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ユーザー入力の取得
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        echo "ユーザー名とパスワードを入力してください。";
        exit;
    }

    try {
        // データベース接続
        $db = new Database();
        $pdo = $db->getConnection();

        // 新しいユーザーの作成
        $user = new User($pdo);
        $userId = $user->createUser($username, $password);

        // 初期キャラクターを`characters_inventory`に追加
        $character = new Character($pdo, $userId);
        $character->addCharactersToInventory($userId);

        // 初期アイテムを`items_inventory`に追加
        $item = new Item($pdo, $userId);
        $item->addItemsToInventory($userId);

        // ダンジョン進捗を`progress_dungeons`に追加
        $dungeon = new Dungeon($pdo, $userId);
        $dungeon->addProgressDungeons($userId);

        // 初期パーティーを`parties`に追加
        // $party = new Party($pdo, $userId);
        // $party->createDefaultParty($userId);

        $_SESSION['message'] = 'アカウントが作成されました！ログインしてください｡';
        header('Location: index.php');
    } catch (Exception $e) {
        echo "エラーが発生しました: " . $e->getMessage();
    }
}?>
