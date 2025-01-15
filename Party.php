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
}
?>
