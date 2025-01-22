<?php
class User {
    private $pdo;
    private $userId;

    public function __construct($pdo, $userId = null) {
        $this->pdo = $pdo;
        $this->userId = $userId;

        if ($this->userId) {
            $this->loadUser();
        }
    }

    private function loadUser() {
        if (!$this->userId) {
            throw new Exception("ユーザーIDが設定されていません");
        }

        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $this->userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
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

    public function createUser($username, $password) {
        try {
            // パスワードのハッシュ化
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
            // ユーザーをデータベースに挿入
            $stmt = $this->pdo->prepare("
                INSERT INTO users (username, password, gold, level, xp, last_save)
                VALUES (:username, :password, 0, 1, 0, NOW())
            ");
            $stmt->execute([
                ':username' => $username,
                ':password' => $hashedPassword,
            ]);
    
            // 挿入されたユーザーのIDを返す
            return $this->pdo->lastInsertId();
        } catch (Exception $e) {
            // エラー発生時は例外をスロー
            throw new Exception("ユーザーの作成に失敗しました: " . $e->getMessage());
        }
    }
    
}
