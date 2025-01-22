<?php
class Dungeon {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ダンジョンIDで特定のダンジョン情報を取得する
    public function getDungeonById($dungeonId) {
        $stmt = $this->pdo->prepare("SELECT * FROM dungeons WHERE id = :dungeonId");
        $stmt->bindParam(':dungeonId', $dungeonId, PDO::PARAM_INT);
        $stmt->execute();

        // 結果を返す
        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    // ダンジョンをprogress_dungeonsテーブルに追加
    public function addProgressDungeons($userId) {
        // dungeonsテーブルから全てのダンジョンを取得
        $stmt = $this->pdo->query("SELECT id FROM dungeons");
        $dungeons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($dungeons) {
            // progress_dungeonsテーブルに挿入
            $stmtInsert = $this->pdo->prepare("
                INSERT INTO progress_dungeons (user_id, dungeon_id, is_completed, is_active, current_floor)
                VALUES (:user_id, :dungeon_id, 0, 0, 1)
            ");

            foreach ($dungeons as $dungeon) {
                $stmtInsert->execute([
                    ':user_id' => $userId,
                    ':dungeon_id' => $dungeon['id'], // dungeonsテーブルのid
                ]);
            }
        }
    }

    
}
