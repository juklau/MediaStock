<?php
    namespace Models;

    use Config\Database;

    // abstract: ne peut pas être instanciée directement, mais elle peut définir des méthodes et des 
    // propriétés communes que ses classes enfants doivent implémenter ou hériter.
    abstract class BaseModel {
        protected $db;
        protected $table;
        protected $primaryKey = 'id';
        
        public function __construct() {
            $this->db = Database::getInstance()->getConnection();
        }
        

        /**
         * Obtenir tous les enregistrements de la table
         * 
         * @return array
         */
        public function getAll() {
            $sql = "SELECT * 
                    FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        }
        

        /**
         * Obtenir les détails d'un élément (défini par primary key = id)
         * 
         * @param int $id
         * @return array|false
         */
        public function getById($id): int  {
            $sql = "SELECT * 
                    FROM {$this->table} 
                    WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql );
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        }
        

        /**
         * Créer un nouvel enregistrement
         * 
         * @param array $data
         * @return int|false L'ID de l'enregistrement nouvellement créé ou faux en cas d'échec
         */
        public function create(array $data) {

            // pour avoir une liste de colonne SQL pour l'insertion =>$columns = 'nom, email, role';
            $columns = implode(', ', array_keys($data));

            // pour avoir une liste de placeholders SQL pour les valeurs => $placeholders = ':nom, :email, :role';
            $placeholders = ':' . implode(', :', array_keys($data));
            
            $sql = "INSERT INTO {$this->table} ({$columns}) 
                    VALUES ({$placeholders})";
            $stmt = $this->db->prepare($sql);
            
            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            if ($stmt->execute()) {
                return $this->db->lastInsertId();
            }
            
            return false;
        }
        

        /**
         * Mettre à jour un enregistrement
         * 
         * @param int $id
         * @param array $data
         * @return bool
         */
        public function update($id, array $data) {
            $setClause = '';

            // array_keys($data) => récupère les noms des colonnes à modifier
            foreach (array_keys($data) as $key) {
                $setClause .= "{$key} = :{$key}, ";
            }
            $setClause = rtrim($setClause, ', ');
            
            $sql = "UPDATE {$this->table} 
                    SET {$setClause} 
                    WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            
            // Pour chaque colonne, on lie sa valeur au placeholder correspondant
            foreach ($data as $key => $value) {
                $stmt->bindValue(":{$key}", $value);
            }
            
            return $stmt->execute();
        }
        

        /**
         * Suppression d'un enregistrement
         * 
         * @param int $id
         * @return bool
         */
        public function delete($id) {
            $sql = "DELETE 
                    FROM {$this->table} 
                    WHERE {$this->primaryKey} = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
            return $stmt->execute();
        }
        

        /**
         * Rechercher des enregistrements par une valeur de champ spécifique
         * 
         * @param string $field
         * @param mixed $value
         * @return array
         */
        public function findBy($field, $value) {
            $sql = "SELECT * 
                    FROM {$this->table} 
                    WHERE {$field} = :value";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':value', $value);
            $stmt->execute();
            return $stmt->fetchAll();
        }
    }

?>