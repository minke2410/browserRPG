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

    // アイテムをitems_inventoryテーブルに追加
    public function addItemsToInventory($userId) {
        // itemsテーブルから全てのアイテムを取得
        $stmt = $this->pdo->query("SELECT id, item_name, item_text, item_effect FROM items");
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($items) {
            // items_inventoryテーブルに挿入
            $stmtInsert = $this->pdo->prepare("
                INSERT INTO items_inventory (user_id, item_id, quantity)
                VALUES (:user_id, :item_id, :quantity)
            ");

            foreach ($items as $item) {
                $stmtInsert->execute([
                    ':user_id' => $userId,
                    ':item_id' => $item['id'], // itemsテーブルのid
                    ':quantity' => 1           // 初期値として1個
                ]);
            }
        }
    }
}
?>
