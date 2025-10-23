<?php
namespace Models;

class Pret extends BaseModel {
    protected $table = 'Pret';

    /**
     * Get all loans with related information (item, borrower, lender)
     * 
     * @return array
     */
    public function getAllWithDetails() {
        $query = "SELECT p.*, 
                        i.nom as item_nom, i.qr_code, i.etat,
                        e.emprunteur_nom, e.emprunteur_prenom, e.role,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a specific loan with related information
     * 
     * @param int $id
     * @return array|false
     */
    public function getWithDetails($id) {
        $query = "SELECT p.*, 
                        i.nom as item_nom, i.qr_code, i.etat,
                        e.emprunteur_nom, e.emprunteur_prenom, e.role,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id
                 WHERE p.{$this->primaryKey} = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Get active loans (not yet returned)
     * 
     * @return array
     */
    public function getActiveLoans() {
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

    /**
     * Get overdue loans
     * 
     * @return array
     */
    public function getOverdueLoans() {
        $today = date('Y-m-d');
        $query = "SELECT p.*, 
                        i.nom as item_nom, i.qr_code,
                        e.emprunteur_nom, e.emprunteur_prenom
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 WHERE p.date_retour_effective IS NULL 
                 AND p.date_retour_prevue < :today";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':today', $today);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get loans by borrower
     * 
     * @param int $emprunteurId
     * @return array
     */
    public function getLoansByBorrower($emprunteurId) {
        $query = "SELECT p.*, i.nom as item_nom, i.qr_code
                 FROM {$this->table} p
                 JOIN Item i ON p.item_id = i.id
                 WHERE p.emprunteur_id = :emprunteur_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':emprunteur_id', $emprunteurId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get loans by item
     * 
     * @param int $itemId
     * @return array
     */
    public function getLoansByItem($itemId) {
        $query = "SELECT p.*, 
                        e.emprunteur_nom, e.emprunteur_prenom,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id
                 WHERE p.item_id = :item_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':item_id', $itemId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * End a loan by setting the effective return date and final note
     * 
     * @param int $id
     * @param string $returnDate
     * @param string $finalNote
     * @return bool
     */
    public function endLoan($id, $returnDate = null, $finalNote = '') {
        if ($returnDate === null) {
            $returnDate = date('Y-m-d'); // Default to today
        }

        $data = [
            'date_retour_effective' => $returnDate,
            'note_fin' => $finalNote
        ];

        return $this->update($id, $data);
    }

    /**
     * Create a new loan
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
            $dateSortie = date('Y-m-d'); // Default to today
        }

        if ($dateRetourPrevue === null) {
            // Default to 2 weeks from today
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

    /**
     * Get loan history for an item
     * 
     * @param int $itemId
     * @return array
     */
    public function getItemLoanHistory($itemId) {
        $query = "SELECT p.*, 
                        e.emprunteur_nom, e.emprunteur_prenom,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id
                 WHERE p.item_id = :item_id
                 ORDER BY p.date_sortie DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':item_id', $itemId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get current active loan for an item (if it exists)
     * 
     * @param int $itemId
     * @return array|false Returns loan data if an active loan exists, false otherwise
     */
    public function getCurrentItemLoan($itemId) {
        $query = "SELECT p.*, 
                        e.emprunteur_nom, e.emprunteur_prenom,
                        a.login as preteur_login
                 FROM {$this->table} p
                 JOIN emprunteur e ON p.emprunteur_id = e.id
                 JOIN Administrateur a ON p.preteur_id = a.id
                 WHERE p.item_id = :item_id
                 AND p.date_retour_effective IS NULL";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':item_id', $itemId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(); // Return only one row (or false if none exists)
    }

    /**
     * Get the table name
     * 
     * @return string
     */
    public function getTable() {
        return $this->table;
    }
}
