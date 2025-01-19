<?php
class Dungeon {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ユーザーごとにアクティブなダンジョンを取得
    public function getActiveDungeonsByUser($userId) {
        $stmt = $this->pdo->prepare("
            SELECT d.*
            FROM dungeons d
            INNER JOIN progress_dungeons pd ON d.id = pd.dungeon_id
            WHERE pd.user_id = :user_id AND pd.is_active = 1
            ORDER BY d.id ASC
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
