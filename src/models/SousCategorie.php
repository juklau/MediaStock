<?php
namespace Models;

class SousCategorie extends BaseModel {
    protected $table = 'sous_categorie';

    /**
     * Obtenir toutes les sous-catégories avec les informations de leur catégorie parente
     * 
     * @return array
     */
    public function getAllWithCategory():array {
        $sql = "SELECT sc.*, c.categorie 
                FROM {$this->table} sc
                JOIN categorie c ON sc.categorie_id = c.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }


    /**
     * Obtenir une sous-catégorie avec les informations de sa catégorie parente
     * 
     * @param int $id
     * @return array|false
     */
    public function getWithCategory(int $id): array|false {
        $sql = "SELECT sc.*, c.categorie 
                 FROM {$this->table} sc
                 JOIN categorie c ON sc.categorie_id = c.id
                 WHERE sc.id = :id";
                //  WHERE sc.{$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }


    /**
     * récupérer tous les Item liés à la même catégorie que celle associée à une sous-catégorie donnée.
     * 
     * @param int $subcategoryId
     * @return array
     */
    public function getSubcategoryItems(int $subcategoryId):array {

        // Tout d'abord, récupérez la sous-catégorie pour trouver son category_id
        $subcategory = $this->getById($subcategoryId);

        if (!$subcategory) {
            return [];
        }

        // Obtenir des articles appartenant à la même catégorie que cette sous-catégorie
        $itemModel = new Item();
        return $itemModel->findBy('categorie_id', $subcategory['categorie_id']);
    }

    //OU

    public function getSubcategoryItems2(int $subcategoryId): array{  //????
        $itemModel = new Item();
        $sql = "SELECT i.*
                FROM {$itemModel->getTable()} i
                JOIN sous_categorie sc ON i.categorie_id = sc.categorie_id
                WHERE sc.id = :subcategoryId";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":subcategoryId" => $subcategoryId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obtenir des sous-catégories par catégorie
     * 
     * @param int $categoryId
     * @return array
     */
    public function getByCategory(int $categoryId):array {
        return $this->findBy('categorie_id', $categoryId);
    }

    //OU 

    public function getByCategory2(int $categoryId):array {
       
        $sql = "SELECT sc.*
                FROM {$this->table} sc
                WHERE sc.categorie_id = :categoryId";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":categoryId" => $categoryId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Créer une nouvelle sous-catégorie
     * 
     * @param string $name
     * @param int $categoryId
     * @return int|false
     */
    public function createSubcategory(string $name, int $categoryId): int|false {
        $data = [
            'sous_categorie' => $name,
            'categorie_id' => $categoryId
        ];

        return $this->create($data);
    }

    //OU

    public function createSubcategory2(string $name, int $categoryId): int|false{
        $sql = "INSERT INTO sous_categorie (sous_categorie, categorie_id) 
                VALUES (:name, :categorie_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':categorie_id' => $categoryId
        ]);
        return (int) $this->db->lastInsertId(); //=>comme create()
    }

    /**
     * Mettre à jour la catégorie d'une sous-catégorie
     * 
     * @param int $id
     * @param int $newCategoryId
     * @return bool
     */
    public function updateCategory(int $id, int $newCategoryId):bool {
        $data = [
            'categorie_id' => $newCategoryId
        ];

        return $this->update($id, $data);
    }

    //OU

    public function updateCategory2(int $id, int $newCategoryId): bool {
        $sql = "UPDATE {$this->table} 
                SET categorie_id = :newCategoryId 
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':newCategoryId' => $newCategoryId,
            ':id' => $id
        ]);
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
