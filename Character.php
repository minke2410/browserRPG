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

    public function getAllCharacters() {
        $stmt = $this->pdo->prepare("SELECT * FROM characters");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // キャラクターをcharacters_inventoryテーブルに追加
    public function addCharactersToInventory($userId) {
        // charactersテーブルから全てのキャラクターを取得
        $stmt = $this->pdo->query("SELECT character_id, name, level, hp, xp, attack FROM characters");
        $characters = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($characters) {
            // characters_inventoryテーブルに挿入
            $stmtInsert = $this->pdo->prepare("
                INSERT INTO characters_inventory (user_id, character_id, name, level, hp, xp, attack)
                VALUES (:user_id, :character_id, :name, :level, :hp, :xp, :attack)
            ");

            foreach ($characters as $character) {
                $stmtInsert->execute([
                    ':user_id' => $userId,
                    ':character_id' => $character['character_id'], // 修正: id -> character_id
                    ':name' => $character['name'],
                    ':level' => $character['level'],
                    ':hp' => $character['hp'],
                    ':xp' => $character['xp'],
                    ':attack' => $character['attack']
                ]);
            }
        }
    }

    public function hasSpecificCharacter($characterId, $name) {
        $stmt = $this->pdo->prepare("
            SELECT COUNT(*) FROM characters_inventory
            WHERE character_id = :character_id AND name = :name AND user_id = :user_id
        ");
        $stmt->execute([
            ':character_id' => $characterId,
            ':name' => $name,
            ':user_id' => $this->userId
        ]);
    
        return $stmt->fetchColumn() > 0;
    }

    public function getCharacterById($characterId) {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM characters_inventory 
            WHERE character_id = :character_id AND user_id = :user_id
        ");
        $stmt->execute([
            ':character_id' => $characterId,
            ':user_id' => $this->userId
        ]);
    
        return $stmt->fetch(PDO::FETCH_ASSOC); // キャラクター情報を1行返す
    }

    public function updateMainCharacterName($newName) {
        $sql = "
            UPDATE characters_inventory 
            SET name = :name 
            WHERE user_id = :user_id AND character_id = 0
        ";
        $stmt = $this->pdo->prepare($sql);
    
        $stmt->execute([
            ':name' => $newName,
            ':user_id' => $this->userId,
        ]);
    
        if ($stmt->rowCount() === 0) {
            throw new Exception('主人公が見つからないか、名前が変更されませんでした｡');
        }
    }
    
    
    
}
?>
