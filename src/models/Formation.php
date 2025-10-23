<?php
namespace Models;

class Formation extends BaseModel {
    protected $table = 'Formation';
    
    /**
     * Obtenir toutes les formations avec le nombre d'emprunteurs dans chacune
     * les formations même sans emprunteur => le LEFT JOIN
     * @return array
     */
    public function getAllWithBorrowerCount(): array {
        $sql = "SELECT f.id, f.formation, COUNT(e.id) AS nombre_emprunteur 
                 FROM {$this->table} f
                 LEFT JOIN emprunteur e ON f.id = e.formation_id
                 GROUP BY f.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    /**
     * récupérer tous les emprunteurs liés à une formation donnée via son formation_id.
     * délégue au modèle
     * @param int $formationId
     * @return array
     */
    public function getFormationBorrowers(int $formationId):array {
        $emprunteurModel = new Emprunteur();
        return $emprunteurModel->getByFormation($formationId);
    }
    

    /**
     * Obtenir tous les emprunteurs liés à une formation avec des détails
     * 
     * @param int $formationId
     * @return array
     */
    public function getFormationBorrowersWithDetails(int $formationId):array {
        $sql = "SELECT e.*, f.formation,
                        (SELECT COUNT(*) 
                        FROM Pret p 
                        WHERE p.emprunteur_id = e.id 
                        AND p.date_retour_effective IS NULL) as prêts_actifs
                 FROM emprunteur e
                 JOIN {$this->table} f ON e.formation_id = f.id
                 WHERE e.formation_id = :formation_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':formation_id', $formationId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    /**
     * Obtenir les statistiques sur les prêts par formation
     * Nombre total de prêts associés aux emprunteurs de cette formation
     * Nombre de prêts non retournés
     * Nombre de prêts en retard
     * 
     * p.date_retour_prevue < CURDATE() => la date de retour prévue est dépassée
     * @return array
     */
    public function getLoanStatsByFormation():array {
        $sql = "SELECT f.id, f.formation, 
                        COUNT(p.id) as total_prêts,
                        SUM(CASE WHEN p.date_retour_effective IS NULL THEN 1 ELSE 0 END) as prêts_actifs,
                        SUM(CASE WHEN p.date_retour_effective IS NULL AND p.date_retour_prevue < CURDATE() THEN 1 ELSE 0 END) as prêts_en_retard
                 FROM {$this->table} f
                 LEFT JOIN emprunteur e ON f.id = e.formation_id
                 LEFT JOIN Pret p ON e.id = p.emprunteur_id
                 GROUP BY f.id, f.formation";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Créer une nouvelle formation
     * 
     * @param string $name
     * @return int|false
     */
    public function createFormation(string $name): int|false {
        $data = [
            'formation' => $name
        ];

        return $this->create($data);
    }

    
    /**
     * Obtenir le nom de table
     * 
     * @return string
     */
    public function getTable():string {
        return $this->table;
    }
}