<?php
class User {
    private $pdo;
    private $userId;
    public $username;
    public $gold;

    public function __construct($pdo, $userId) {
        $this->pdo = $pdo;
        $this->userId = $userId;  // コンストラクタ内で$userIdを直接設定
        $this->loadUserData();
    }

    private function loadUserData() {
        // userIdを使ってユーザー情報を取得
        $sql = "SELECT id, username, gold FROM users WHERE id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $this->userId]);  // userIdを使って検索
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $this->userId = $result['id'];  // userIdをセット
            $this->username = $result['username']; // ユーザー名をセット
            $this->gold = $result['gold']; // 所持金をセット
        } else {
            throw new Exception("User not found");
        }
    }

    public function getUserId() {
        return $this->userId;
    }
}

?>
