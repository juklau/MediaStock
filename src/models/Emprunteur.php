<?php
namespace Models;

class Emprunteur extends BaseModel {
    protected $table = 'Emprunteur';

    /**
     * Obtenir tous les emprunteurs avec leurs informations de formation
     * 
     * @return array
     */
    public function getAllWithFormation():array {
        $sql = "SELECT e.*, f.formation 
                 FROM {$this->table} e
                 JOIN Formation f ON e.formation_id = f.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Obtenir un emprunteur avec des informations de formation
     * 
     * @param int $id
     * @return array|false
     */
    public function getWithFormation(int $id):array|false {
        $sql = "SELECT e.*, f.formation 
                 FROM {$this->table} e
                 JOIN Formation f ON e.formation_id = f.id
                 WHERE e.id = :id";
                //  WHERE e.{$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        // $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute([
            ':id' => $id
        ]);
        return $stmt->fetch();
    }

    /**
     * récupérer tous les emprunteurs liés à une formation donnée via son formation_id
     * interroge la base
     * @param int $formationId
     * @return array
     */
    public function getByFormation(int $formationId):array {
        return $this->findBy('formation_id', $formationId);
    }

    /**
     * Obtenir les emprunteurs par rôle
     * 
     * @param string $role ('etudiant(e)', 'intervenant')
     * @return array
     */
    public function getByRole(string $role):array {
        return $this->findBy('role', $role);
    }

    //OU

    public function getByRole2(string $role): array{
        $sql = "SELECT * 
                FROM {$this->table}
                WHERE role = :role";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }


    /**
     * Rechercher des emprunteurs par nom ou prénom.... =>searchTerm p.ex %Mar%
     * 
     * @param string $searchTerm
     * @return array
     */
    public function searchByName(string $searchTerm):array {
        $searchTerm = "%{$searchTerm}%";
        $sql = "SELECT e.*, f.formation 
                 FROM {$this->table} e
                 JOIN Formation f ON e.formation_id = f.id
                 WHERE e.emprunteur_nom LIKE :search_term_nom 
                 OR e.emprunteur_prenom LIKE :search_term_prenom";
        $stmt = $this->db->prepare($sql);
        // $stmt->bindParam(':search_term_nom', $searchTerm);
        // $stmt->bindParam(':search_term_prenom', $searchTerm);
        // $stmt->execute();
        $stmt->execute([
            ':search_term_nom' => $searchTerm,
            ':search_term_prenom' => $searchTerm
        ]);
        return $stmt->fetchAll();
    }


    /**
     * Obtenir des prêts actifs pour un emprunteur
     * 
     * @param int $emprunteurId
     * @return array|false
     */
    public function getActiveLoans(int $emprunteurId):array|false {
        $pretModel = new Pret();
        $sql = "SELECT p.*, i.nom as item_nom, i.qr_code
                 FROM {$pretModel->getTable()} p
                 JOIN Item i ON p.item_id = i.id
                 WHERE p.emprunteur_id = :emprunteur_id
                 AND p.date_retour_effective IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emprunteur_id', $emprunteurId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // OU

    public function getActiveLoans2(int $emprunteurId):array {
        $pretModel = new Pret();
        $sql = "SELECT p.*, e.emprunteur_nom, e.emprunteur_prenom, i.nom as item_nom, i.qr_code
                 FROM {$pretModel->getTable()} p
                 JOIN Item i ON p.item_id = i.id
                 JOIN Emprunteur e ON p.emprunteur_id = e.id
                 WHERE p.emprunteur_id = :emprunteur_id
                 AND p.date_retour_effective IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':emprunteur_id', $emprunteurId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Obtenir l'historique des prêts d'un emprunteur => commençant avec les plus récents
     * 
     * @param int $emprunteurId
     * @return array
     */
    public function getLoanHistory(int $emprunteurId):array {
        $pretModel = new Pret();
        $query = "SELECT p.*, i.nom as item_nom, i.qr_code
                 FROM {$pretModel->getTable()} p
                 JOIN Item i ON p.item_id = i.id
                 WHERE p.emprunteur_id = :emprunteur_id
                 ORDER BY p.date_sortie DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':emprunteur_id', $emprunteurId, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
     /**
     * ajouter un emprunteur (formation_id facultatif pour intervenant) => OK
     * 
     * @param string $nom
     * @param string $prenom,
     * @param string $role ('etudiant(e)' ou 'intervenant')
     * @param int $formation_id
     * @return int ID de l'emprunteur ajouté
     */
    public function addEmprunteur(string $nom, string $prenom, string $role, ?int $formation_id): int {
        
        //Valider le rôle (ENUM)
        $allowed = ['etudiant(e)', 'intervenant'];
        if (!in_array($role, $allowed, true)) {
            throw new \InvalidArgumentException("role doit être 'etudiant(e)' ou 'intervenant'");
        }

        //Règle métier : formation obligatoire pour les étudiants
        if ($role === 'etudiant(e)' && $formation_id === null) {
            throw new \InvalidArgumentException("formation_id est requis pour un(e) etudiant(e).");
        }

        //INSERT (on passe NULL pour intervenant)
        $sql = "INSERT INTO {$this->table} (emprunteur_nom, emprunteur_prenom, role, formation_id)
                VALUES (:nom, :prenom, :role, :formation_id)";
        $stmt = $this->db->prepare($sql);
        //explication dans le doc réalisation MediaStock
        $stmt->bindValue(':nom', $nom, \PDO::PARAM_STR);
        $stmt->bindValue(':prenom', $prenom,  \PDO::PARAM_STR);
        $stmt->bindValue(':role', $role, \PDO::PARAM_STR);
        // si intervenant → formation_id NULL
        if ($formation_id === null) {
            $stmt->bindValue(':formation_id', null, \PDO::PARAM_NULL);
        } else {
            $stmt->bindValue(':formation_id', $formation_id, \PDO::PARAM_INT);
        }
        $stmt->execute();

        return (int)$this->db->lastInsertId();
    }


    /**
     * Supprimer un emprunteur
     * 
     * @param int $id => emprunteurId
     * @return bool
     */
    public function deleteEmprunteur(int $id): bool {
        $sql = "DELETE FROM {$this->table}
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":id" => $id
        ]);
    }


    /**
     * Archiver un emprunteur
     * 
     * @param int $id => emprunteurId
     * @return bool
     */
    public function archiveEmprunteur(int $id): bool {
         $sql = "UPDATE {$this->table} 
                SET archived = 1 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":id" => $id
        ]);
    }

    
    /**
     * Obtenir le nom de la tabla
     * 
     * @return string
     */
    public function getTable():string {
        return $this->table;
    }
}
