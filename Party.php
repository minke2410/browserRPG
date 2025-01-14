<?php
class Party {
    private $pdo;
    private $userId;
    public $parties;

    public function __construct($pdo, $userId) {
        $this->pdo = $pdo;
        $this->userId = $userId;
        $this->loadParties();
    }

    private function loadParties() {
        $sql = "SELECT id, user_id, party_name, member1, member2, member3, member4, is_active, uploaded_at
                FROM parties 
                WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['user_id' => $this->userId]);
        $this->parties = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParties() {
        return $this->parties;
    }
}
?>
