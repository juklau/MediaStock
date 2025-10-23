<?php
namespace Models;

class Item extends BaseModel {
    protected $table = 'Item';

    /**
     * Obtenir DES articles avec leurs informations de catégorie
     * 
     * @return array
     */
    public function getAllWithCategory():array {
        $sql = "SELECT i.*, c.categorie 
                 FROM {$this->table} i
                 JOIN categorie c ON i.categorie_id = c.id
                 ORDER BY c.categorie, i.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    /**
     * lister tous les matériels => OK
     * 
     * @return array
     */
    public function getAllItems(): array{
        $sql = "SELECT * 
                FROM Item";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     *Obtenir UN article avec ses informations de catégorie
     * 
     * @param int $id
     * @return array|false
     */
    public function getWithCategory(int $id):array|false {
        $sql = "SELECT i.*, c.categorie 
                 FROM {$this->table} i
                 JOIN categorie c ON i.categorie_id = c.id
                 WHERE i.id = :id";
                //  WHERE i.{$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * Obtenir les items d'un catégorie
     * 
     * @param int $categoryId
     * @return array|false
     */
    public function getByCategory(int $categoryId):array|false {
        return $this->findBy('categorie_id', $categoryId);
    }

    //OU

    public function getByCategory2(int $categoryId):array {
        $sql= "SELECT i.*
                FROM {$this->table} i
                WHERE i.categorie_id = :categorie_id
                ORDER BY i.nom";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":categorie_id" => $categoryId
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    
    /**
     * Obtenir la liste des items par une condition (état)
     * 
     * @param string $condition ('bon', 'moyen', 'mauvais')
     * @return array|false
     */
    public function getByCondition(string $condition):array|false {
        return $this->findBy('etat', $condition);
    }

    //OU 

    public function getByCondition2(string $condition):array {
        $sql= "SELECT i.*
                FROM {$this->table} i
                WHERE i.etat = :condition
                ORDER BY i.nom";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":condition" => $condition
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * Trouver un item par son QR code
     * à modifier pour int !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * @param string $qrCode
     * @return array|false
     */
    public function findByQrCode(string $qrCode):array|false {
        $sql = "SELECT * 
                FROM {$this->table} 
                WHERE qr_code = :qr_code";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':qr_code', $qrCode);
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * Chercher item selon son nom
     * 
     * @param string $searchTerm
     * @return array
     */
    public function searchByName(string $searchTerm):array {
        $searchTerm = "%{$searchTerm}%";
        $sql = "SELECT * 
                FROM {$this->table} 
                WHERE nom 
                LIKE :search_term";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':search_term', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Obtenir les articles disponibles (pas actuellement en prêt)
     * 
     * @return array
     */
    public function getAvailableItems(): array {
        $sql = "SELECT i.* FROM {$this->table} i
                -- Cette clause permet de joindre uniquement les prêts en cours (ceux non encore retournés) à chaque item.
                 LEFT JOIN Pret p ON i.id = p.item_id AND p.date_retour_effective IS NULL
                 WHERE p.id IS NULL"; //=>filtre ceux sans prêt actif, donc disponibles
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Compter le nombre d'items disponibles par catégorie
     * 
     * @return array Tableau associatif avec categorie_name et nombre d'items disponibles
     */
    public function countAvailableItemsByCategory(): array {
        $sql = "SELECT i.categorie_id, c.categorie, COUNT(*) AS disponible_count
                FROM {$this->table} i
                LEFT JOIN Pret p ON i.id = p.item_id AND p.date_retour_effective IS NULL
                JOIN categorie c ON i.categorie_id = c.id
                WHERE p.id IS NULL
                GROUP BY i.categorie_id
                ORDER BY i.categorie_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    


    /**
     * Afficher les noms et modèles des items disponibles (non prêtés actuellement)
     * 
     * @return array|false
     */
    public function getAvailableItemNames(): array|false {
        $sql = "SELECT i.id, i.nom, i.model, i.image_url,
                FROM {$this->table} i
                -- si la sous-requête ne trouve aucune ligne correspondante.
                -- il faut que item ne soit pas dans ce liste
                WHERE NOT EXISTS (
                -- on test l'existance si item est prété
                    SELECT 1
                    FROM Pret p 
                    WHERE p.item_id = i.id
                    AND p.date_retour_effective IS NULL
                )
                ORDER BY i.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Afficher les items actuellement indisponibles (en prêt actif)
     * changé
     * @return array|false
     */
    public function afficheItemIndisponible(): array|false {
        $sql = "SELECT i.id, i.nom, i.model, i.image_url, p.date_retour_prevue
                FROM {$this->table} i
                JOIN Pret p ON i.id = p.item_id
                WHERE p.date_retour_effective IS NULL
                ORDER BY i.nom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    
    /**
     * afficher un seul matériel par ID
     * 
     * @param int $id
     * @return array|false
     */

    public function getItemByID(int $id): array|false{
        $sql = "SELECT * 
                FROM {$this->table} 
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":id" => $id
        ]);

        return $stmt->fetch();
    }


    

    /**
     * Vérifiez si un article est disponible pour le prêt
     * 
     * @param int $itemId
     * @return bool Renvoie vrai si l'élément est disponible, faux sinon
     */
    public function isAvailable(int $itemId):bool {

        $pretModel = new Pret();
        $sql = "SELECT COUNT(*) 
                FROM {$pretModel->getTable()} 
                WHERE item_id = :item_id 
                AND date_retour_effective IS NULL";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':item_id', $itemId, \PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        // Si le nombre est de 0, il n'y a pas de prêts actifs pour cet article, il est donc disponible
        // si le nombre est de 1 => il n'est pas disponible
        return ($count == 0);
    }


    /**
     * ajouter un Item =>OK
     * 
     * @param string $nom
     * @param string $model
     * @param string $qr_code
     * @param string $image_url
     * @param string $etat
     * @param int $categorie_id
     * @return bool Renvoie vrai si l'élément est ajouté
     */
    public function addItem(string $nom, ?string $model, string $qr_code, string $image_url, string $etat, int $categorie_id): bool{
        $sql = "INSERT INTO {$this->table} (nom, model, qr_code, image_url, etat, categorie_id)
                VALUES (:nom, :model, :qr_code, :image_url, :etat, :categorie_id)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ":nom" => $nom,
            ":model" => $model,
            ":qr_code" => $qr_code,
            ":image_url" => $image_url,
            ":etat" => $etat,
            ":categorie_id" => $categorie_id
        ]);
    }

    /**
     * Supprimer un item
     * 
     * @param int $itemId
     * @return bool
     */
    public function deleteItem(int $id): bool {
        $sql = "DELETE FROM {$this->table}
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
