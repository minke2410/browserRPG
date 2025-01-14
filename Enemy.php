<?php
class Enemy {
    private $pdo;
    public $enemies;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->loadEnemies();
    }

    private function loadEnemies() {
        // enemiesテーブルから敵データを取得
        $sql = "SELECT id, enemy_id, name, hp, attack FROM enemies";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $this->enemies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEnemies() {
        return $this->enemies;
    }
}
?>
