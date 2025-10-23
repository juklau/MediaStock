<?php
namespace Models;

class Categorie extends BaseModel {
    protected $table = 'categorie';

    /**
     *Obtenir toutes les catégories avec leurs sous-catégories
     * 
     * @return array
     */
    public function getAllWithSubcategories() {
        $categories = $this->getAll();
        $sousCategorie = new SousCategorie();

         // sans '&' => $category est encore une référence → peut causer des bugs
        foreach ($categories as &$category) { //=>modifie par référence
            $category['sous_categories'] = $sousCategorie->findBy('categorie_id', $category['id']);
        }

        return $categories;
    }

    public function getAllWithSubcategories2(){
        $sql = "SELECT c.id AS cat_id, c.categorie , sc.id AS sous_cat_id, sc.sous_categorie
                FROM categorie c 
                JOIN sous_categorie sc ON c.id = sc.categorie_id 
                ORDER BY c.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Obtenir une catégorie avec ses sous-catégories????
     * 
     * @param int $id
     * @return array|false
     */
    public function getWithSubcategories(int $id) {
        $category = $this->getById($id);

        if ($category) {
            $sousCategorie = new SousCategorie();
            $category['sous_categories'] = $sousCategorie->findBy('categorie_id', $category['id']);
        }

        return $category;
    }

    /**
     * Obtenir tous les éléments d'une catégorie
     * 
     * @param int $categoryId
     * @return array
     */
    public function getCategoryItems(int $categoryId) {
        $itemModel = new Item();
        return $itemModel->getByCategory($categoryId);
    }

    public function getCategoryItems2(int $categoryId){
        $sql = "SELECT i.* 
                FROM Item i
                JOIN categorie c ON i.categorie_id = c.id
                WHERE i.categorie_id = :categorieId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ":categorieId" => $categoryId
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


    /**
     * Obtenir tous les éléments d'une catégorie et de ses sous-catégories
     * 
     * @param int $categoryId
     * @return array
     */
    public function getAllCategoryItems(int $categoryId) {
        $sql = "SELECT i.* 
                 FROM Item i
                 WHERE i.categorie_id = :category_id_1
                 UNION
                 SELECT i.* 
                 FROM Item i
                 JOIN sous_categorie sc ON i.categorie_id = sc.id
                 WHERE sc.categorie_id = :category_id_2"; 
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':category_id_1', $categoryId, \PDO::PARAM_INT);
        $stmt->bindParam(':category_id_2', $categoryId, \PDO::PARAM_INT);
        $stmt->execute();
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Créer une nouvelle catégorie avec des sous-catégories
     * 
     * @param string $categoryName
     * @param array $subcategories
     * @return int|false
     */
    public function createWithSubcategories(string $categoryName, array $subcategories = []) {
        $this->db->beginTransaction();

        try {
            $categoryId = $this->create(['categorie' => $categoryName]);

            if ($categoryId && !empty($subcategories)) {
                $sousCategorie = new SousCategorie();

                foreach ($subcategories as $subcategoryName) {
                    $sousCategorie->create([
                        'sous_categorie' => $subcategoryName,
                        'categorie_id' => $categoryId
                    ]);
                }
            }

            $this->db->commit();
            return $categoryId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function createWithSubcategories2(string $categoryName, array $subcategories = []){

        $this->db->beginTransaction();

        try {
            // Insérer la catégorie
            $sqlCategory = "INSERT INTO categorie (categorie) VALUES (:categorie)";
            $stmtCategory = $this->db->prepare($sqlCategory);
            $stmtCategory->execute([
                ":categorie" => $categoryName
            ]);

            $categoryId = (int) $this->db->lastInsertId();

            // Insérer les sous-catégories si présentes
            if (!empty($subcategories)) {
                $sqlSubCat = "INSERT INTO sous_categorie (sous_categorie, categorie_id) 
                VALUES (:sous_categorie, :categorie_id)";
                $stmtSub = $this->db->prepare($sqlSubCat);

                foreach ($subcategories as $subcategoryName) {
                    $stmtSub->execute([
                        ":sous_categorie" => $subcategoryName,
                        ":categorie_id" => $categoryId 
                    ]);
                }
            }

            // Valider la transaction
            $this->db->commit();
            return $categoryId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }


    /**
     * Supprimer une catégorie et toutes ses sous-catégories
     * 
     * @param int $id
     * @return bool
     */
    public function deleteWithSubcategories($id){

        $this->db->beginTransaction();

        try {
            // Supprimer les sous-catégories
            $sousCategorie = new SousCategorie();
            $sqlSub = "DELETE FROM {$sousCategorie->getTable()} WHERE categorie_id = :id";
            $stmtSub = $this->db->prepare($sqlSub);
            $stmtSub->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmtSub->execute();

            // Supprimer la catégorie
            $sqlCat = "DELETE FROM categorie WHERE id = :id";
            $stmtCat = $this->db->prepare($sqlCat);
            $stmtCat->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmtCat->execute();

            // Valider la transaction
            $this->db->commit();
            return true;

        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }


    /**
     * Obtenir le nom de table
     * 
     * @return string
     */
    public function getTable() {
        return $this->table;
    }
}
