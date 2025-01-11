<?php
class Item {
    private $pdo;
    private $userId;
    public $items;

    public function __construct($pdo, $userId) {
        $this->pdo = $pdo;
        $this->userId = $userId;
        $this->loadItems();
    }

    private function loadItems() {
        $sql = "SELECT i.item_name, iv.quantity 
                FROM items_inventory iv 
                JOIN items i ON iv.item_id = i.id 
                WHERE iv.user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $this->userId]);
        $this->items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getItems() {
        return $this->items;
    }
}
?>
