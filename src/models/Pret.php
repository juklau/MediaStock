<?php
namespace Models;

class Pret extends BaseModel {
    protected $table = 'Pret';

    /**
     * Obtenez tous les prêts avec les informations associées (item, emprunteur, prêteur)
     * 
     * @return array
     */
    public function getAllWithDetails(): array {
        $sql = "SELECT p.*, 
                        i.nom as item_nom, i.qr_code, i.etat,
                        i.model, i.image_url,
                        e.emprunteur_nom, e.emprunteur_prenom, e.role,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Obtenir un prêt spécifique avec des informations connexes
     * 
     * @param int $id
     * @return array|false
     */
    public function getWithDetails(int $id): array {
        $sql = "SELECT p.*, 
                        i.nom as item_nom, i.qr_code, i.etat,
                        e.emprunteur_nom, e.emprunteur_prenom, e.role,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id
                 WHERE p.id = :id";
                //  WHERE p.{$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * Obtenir la liste des prêts actifs (pas encore retourné)
     * 
     * @return array
     */
    public function getActiveLoans():array { // => du prof
        $query = "SELECT p.*, 
                        i.nom as item_nom, i.qr_code,
                        e.emprunteur_nom, e.emprunteur_prenom
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 WHERE p.date_retour_effective IS NULL";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    //ou

    //afficher les prêts qui ne sont pas rendu
    public function affichePretPasRendu(): array{ // => mien
        $sql = "SELECT p.*, 
                    i.nom AS item_nom, i.model AS item_model, i.qr_code, 
                    e.emprunteur_nom, e.emprunteur_prenom
                FROM {$this->table} p
                JOIN Item i ON p.item_id = i.id
                JOIN emprunteur e ON p.emprunteur_id = e.id
                WHERE date_retour_effective IS NULL
                ORDER BY p.date_sortie DESC, p.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obtenir des prêts qui sont en retard
     * 
     * @return array|false
     */
    public function getOverdueLoans():array|false {
        $today = date('Y-m-d'); //p.ex: '2025-12-22'
        $sql = "SELECT p.*, i.id,
                        i.nom AS item_nom, i.model AS item_model, i.qr_code, i.image_url
                        e.emprunteur_nom, e.emprunteur_prenom
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 WHERE p.date_retour_effective IS NULL 
                 AND p.date_retour_prevue < :today";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Obtenir des prêts par emprunteur
     * 
     * @param int $emprunteurId
     * @return array
     */
    public function getLoansByBorrower(int $emprunteurId):array {
        $sql = "SELECT p.*, i.nom as item_nom, i.qr_code
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 WHERE p.emprunteur_id = :emprunteur_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emprunteur_id', $emprunteurId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    //ou

    public function getLoansByBorrower2(int $emprunteurId):array {
        $sql = "SELECT p.*, i.nom as item_nom, i.qr_code,
                    e.emprunteur_prenom, e.role
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN emprunteur e ON p.emprunteur_id = e.id 
                 WHERE p.emprunteur_id = :emprunteur_id";
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([
            ":emprunteur_id" => $emprunteurId
        ]);
        return $stmt->fetchAll();
    }


    /**
     * Obtenir la liste des prêts par item
     * 
     * @param int $itemId
     * @return array
     */
    public function getLoansByItem(int $itemId):array {
        $sql = "SELECT p.*, 
                        e.emprunteur_nom, e.emprunteur_prenom,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id
                 WHERE p.item_id = :item_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':item_id', $itemId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Mettre fin à un prêt en fixant la date de retour effective et la note finale
     * ?????????????????????????????????? pour le type de $returnDate
     * @param int $id
     * @param string $returnDate
     * @param string $finalNote
     * @return bool
     */
    public function endLoan(int $id, string $returnDate = null, string $finalNote = ''): bool {
        if ($returnDate === null) {
            $returnDate = date('Y-m-d'); // Default : aujourd'hui
        }

        $data = [
            'date_retour_effective' => $returnDate,
            'note_fin' => $finalNote
        ];

        return $this->update($id, $data);
    }

    //ou 
    
    /**
     * Clôturer la fin du prêt
     * ?????????????????????????????????? pour le type de $date_retour_effectiv
     * DateTime => date + heure => si dans la BDD date, je vais "perdre" l'heure...
     * @param int $pret_id
     * @param DateTime $date_retour_effective
     * @param string $note_fin
     * @return bool
     */
    public function cloturerPret(int $pret_id, DateTime $date_retour_effective, string $note_fin): bool{
        $sql = "UPDATE {$this->table}
                SET date_retour_effective = :dre, note_fin = :note_fin
                WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':dre'      => $date_retour_effective->format('Y-m-d'),
            ':note_fin' => $note_fin,
            ':id'       => $pret_id,
        ]);
    }




    /**
     * Créer un nouveau Prêt
     * 
     * @param int $itemId
     * @param int $emprunteurId
     * @param int $preteurId
     * @param string $dateSortie
     * @param string $dateRetourPrevue
     * @param string $noteDebut
     * @return int|false
     */
    public function createLoan($itemId, $emprunteurId, $preteurId, $dateSortie = null, $dateRetourPrevue = null, $noteDebut = '') {
        if ($dateSortie === null) {
            $dateSortie = date('Y-m-d'); // Default: aujourd'hui
        }

        if ($dateRetourPrevue === null) {
            // Par défaut, dans 2 semaines à compter d'aujourd'hui
            $dateRetourPrevue = date('Y-m-d', strtotime('+2 weeks'));
        }

        $data = [
            'item_id' => $itemId,
            'emprunteur_id' => $emprunteurId,
            'preteur_id' => $preteurId,
            'date_sortie' => $dateSortie,
            'date_retour_prevue' => $dateRetourPrevue,
            'note_debut' => $noteDebut,
            'note_fin' => '' // Empty initially
        ];

        return $this->create($data);
    }

    //ou 


    /**
     * Créer un nouveau prêt
     * 
     * @param int $item_id
     * @param int $emprunteur_id
     * @param int $preteur_id
     * @param DateTime|null $date_sortie
     * @param DateTime|null $date_retour_prevue
     * @param string $note_debut
     * @return int|false
     */
    public function nouveauPret(int $item_id, int $emprunteur_id,int $preteur_id, ?DateTime $date_sortie = null,
        ?DateTime $date_retour_prevue = null,string $note_debut = ''): int|false {

        if ($date_sortie === null) {
            $date_sortie = new DateTime(); // aujourd’hui
        }

        if ($date_retour_prevue === null) {
            $date_retour_prevue = (new DateTime())->modify('+2 weeks');
        }

        $sql = "INSERT INTO {$this->table} (
                    item_id, emprunteur_id, date_sortie, date_retour_prevue, note_debut, note_fin, preteur_id
                ) VALUES (
                    :item_id, :emprunteur_id, :date_sortie, :date_retour_prevue, :note_debut, :note_fin, :preteur_id
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':item_id' => $item_id,
            ':emprunteur_id' => $emprunteur_id,
            ':date_sortie' => $date_sortie->format('Y-m-d'),
            ':date_retour_prevue' => $date_retour_prevue->format('Y-m-d'),
            ':note_debut' => $note_debut,
            ':note_fin' => '',              // => il ne peut pas être NULL!!!!
            ':preteur_id' => $preteur_id
        ]);

        return (int) $this->db->lastInsertId();
    }


    /**
     * Obtenir l'historique de prêt d'un article
     * 
     * @param int $itemId
     * @return array
     */
    public function getItemLoanHistory(int $itemId): array{
        $sql = "SELECT p.*, 
                        e.emprunteur_nom, e.emprunteur_prenom,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id
                 WHERE p.item_id = :item_id
                 ORDER BY p.date_sortie DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':item_id', $itemId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    

    /**
     * Obtenir un prêt actif actuel pour un item (s'il existe)
     * 
     * @param int $itemId
     * @return array|false Renvoie les données de prêt si un prêt actif existe, sinon false
     */
    public function getCurrentItemLoan(int $itemId):array|false {
        $sql = "SELECT p.*, 
                        e.emprunteur_nom, e.emprunteur_prenom,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id
                 WHERE p.item_id = :item_id
                 AND p.date_retour_effective IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':item_id', $itemId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(); // Renvoie une seule ligne (ou false si aucune n'existe)
    }


    /**
     * Obtenir le nom de la table
     * 
     * @return string
     */
    public function getTable():string {
        return $this->table;
    }
}
