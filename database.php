<?php
$host = 'localhost';     // MySQLのホスト名（通常はlocalhost）
$dbname = 'ブラウザRPG';     // データベース名
$username = 'root';      // MySQLのユーザー名
$password = 'Usami71524';          // MySQLのパスワード（XAMPPなどでは空の場合もあります）

try {
    // PDOでデータベース接続
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // 接続のエラーモードを設定（例外をスロー）
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // 接続失敗時のエラーメッセージ
    die("接続に失敗しました: " . $e->getMessage());
}

class User {
    private $pdo;
    private $userId;
    public $username;
    public $gold;
    public $characters;
    public $items;

    // コンストラクタ
    public function __construct($pdo, $username) {
        $this->pdo = $pdo;
        $this->userId = $username;  // セッションからのユーザー名を受け取る
        $this->loadUserData();
        $this->loadCharacters();
        $this->loadItems();
    }

    // ユーザー情報をデータベースから取得
    private function loadUserData() {
        // ユーザー名からidとgoldを取得
        $sql = "SELECT id, username, gold FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $this->userId]);  // ユーザー名をクエリに使用
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $this->userId = $result['id'];  // idを設定
            $this->username = $result['username'];
            $this->gold = $result['gold'];
        } else {
            throw new Exception("User not found");
        }
    }

    // ユーザーのキャラクターをデータベースから取得
    private function loadCharacters() {
        // ユーザーのキャラクター情報を取得
        $sql = "SELECT id, name, level, hp, attack, xp 
                FROM characters_inventory 
                WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $this->userId]);  // user_idを使ってキャラクターを取得
        $this->characters = $stmt->fetchAll(PDO::FETCH_ASSOC);  // 詳細情報をすべて取得
    }

    private function loadItems() {
        // items_inventory テーブルと items テーブルを結合して詳細情報を取得
        $sql = "SELECT i.item_name, iv.quantity 
                FROM items_inventory iv 
                JOIN items i ON iv.item_id = i.id 
                WHERE iv.user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $this->userId]);
        $this->items = $stmt->fetchAll(PDO::FETCH_ASSOC);  // 詳細情報を全て取得
    }
}

?>
