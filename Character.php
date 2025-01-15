<?php
class Character {
    private $pdo;
    private $userId;
    public $characters;

    public function __construct($pdo, $userId) {
        $this->pdo = $pdo;
        $this->userId = $userId;
        $this->loadCharacters();
    }

    private function loadCharacters() {
        $sql = "SELECT id, name, level, hp, attack, xp, updated_at
                FROM characters_inventory 
                WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $this->userId]);
        $this->characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCharacters() {
        return $this->characters;
    }

    public function createMainCharacter($mcName) {
        try {
            // トランザクション開始
            $this->pdo->beginTransaction();

            // characters_inventory テーブルに主人公を挿入
            $stmt = $this->pdo->prepare('
                INSERT INTO characters_inventory (user_id, character_id, name, level, hp, xp, attack) 
                VALUES (:user_id, 0, :name, 1, 100, 0, 5)
            ');
            $stmt->execute([ ':user_id' => $this->userId, ':name' => $mcName ]);

            // トランザクションコミット
            $this->pdo->commit();

            return '主人公「' . $mcName . '」が作成されました｡';
        } catch (PDOException $e) {
            // トランザクションロールバック
            $this->pdo->rollBack();
            return 'データベースエラー: ' . $e->getMessage();
        }
    }
}
?>
