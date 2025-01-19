<?php
class User {
    private $pdo;
    private $id;
    public $username;
    public $gold;
    public $level;
    public $xp;

    public function __construct($pdo, $id) {
        $this->pdo = $pdo;
        $this->id = $id;
        $this->loadUser();
    }

    private function loadUser() {
        if (empty($this->id)) {
            throw new Exception("ユーザーIDが設定されていません");
        }

        $stmt = $this->pdo->prepare("SELECT username, gold, level, xp FROM users WHERE id = :id");
        $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            throw new Exception("クエリの実行に失敗しました: " . implode(", ", $stmt->errorInfo()));
        }

        if ($stmt->rowCount() === 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->username = $user['username'];
            $this->gold = $user['gold'];
            $this->level = $user['level'];
            $this->xp = $user['xp'];
        } else {
            throw new Exception("ユーザーが見つかりません");
        }
    }

    public function getUsername() {
        return $this->username;
    }

    public function getGold() {
        return $this->gold;
    }

    public function getLevel() {
        return $this->level;
    }

    public function getXp() {
        return $this->xp;
    }
}
