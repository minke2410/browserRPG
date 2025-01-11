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
        $sql = "SELECT id, name, level, hp, attack, xp 
                FROM characters_inventory 
                WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $this->userId]);
        $this->characters = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCharacters() {
        return $this->characters;
    }
}
?>
