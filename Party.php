<?php
class Party {
    private $pdo;
    private $userId;

    public function __construct($pdo, $userId) {
        $this->pdo = $pdo;
        $this->userId = $userId;
    }

    public function getParties() {
        $sql = "
            SELECT 
                parties.id,
                parties.party_name,
                ci1.name AS member1_name,
                ci2.name AS member2_name,
                ci3.name AS member3_name,
                ci4.name AS member4_name
            FROM parties
            LEFT JOIN characters_inventory AS ci1 ON parties.member1 = ci1.id
            LEFT JOIN characters_inventory AS ci2 ON parties.member2 = ci2.id
            LEFT JOIN characters_inventory AS ci3 ON parties.member3 = ci3.id
            LEFT JOIN characters_inventory AS ci4 ON parties.member4 = ci4.id
            WHERE parties.user_id = :user_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $this->userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteParty($partyId, $userId) {
        try {
            // ユーザーのパーティーかどうかを確認
            $sql = "SELECT user_id FROM parties WHERE id = :party_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['party_id' => $partyId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // 結果が空の場合のエラー処理を追加
            if (!$result) {
                echo json_encode(['success' => false, 'message' => '指定されたパーティーは存在しません。']);
                exit;
            }

            if ($result['user_id'] !== $userId) {
                echo json_encode(['success' => false, 'message' => 'このパーティーを削除する権限がありません。']);
                exit;
            }

            // パーティーを削除
            $sql = "DELETE FROM parties WHERE id = :party_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['party_id' => $partyId]);

            return ['success' => true, 'message' => 'パーティーが削除されました。'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'エラーが発生しました: ' . $e->getMessage()];
        }
    }

    public function getPartyCount($userId) {
        $sql = "SELECT COUNT(*) as count FROM parties WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
    
}
?>
