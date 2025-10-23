<?php
    namespace Models;

    class Administrateur extends BaseModel {
        protected $table = 'Administrateur';
        
        /**
         * authentification d'un admin
         * 
         * @param string $login
         * @param string $password
         * @return array|false Returns admin data if authentication successful, false otherwise
         */

        public function authenticate(string $login, string $password):array|false {
            $sql = "SELECT * 
                    FROM {$this->table} 
                    WHERE login = :login";
            $stmt = $this->db->prepare($sql);

            // bindParam() : méthode qui lie une variable PHP à un paramètre nommé dans la requête SQL
            $stmt->bindParam(':login',$login);
            $stmt->execute();
            
            $admin = $stmt->fetch(); // récupération d'UNE SEULE ligne
            
            // password_verify() => vérifier si un mot de passe en clair correspond à un hash sécurisé
            if ($admin && password_verify($password, $admin['mot_de_passe_hash'])) {

                // Supprime le hachage du mdp du tableau $admin renvoyées pour des raisons de sécurité
                unset($admin['mot_de_passe_hash']);
                return $admin; //=> retourne login_name
            }
            
            return false;
        }

        #récupération l'id
        #@param string $name
        #@return int|false*/
        public function getByName(string $name):int|false{
            $sql = "SELECT id
                    FROM {$this->table} 
                    WHERE login = :name";

            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':name', $name, \PDO::PARAM_STR);
            $stmt->execute();

            // fetch() renvoie un tableau associatif comme ['id' => 3]
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            // extraire id et le convertir en int ou sinon return false
            return $result ? (int)$result['id'] : false;
        }
        
        /**
         * Création d'un admin
         * 
         * @param string $login
         * @param string $password
         * @return int|false
         */

        public function createAdmin(string $login, string $password): int|false{

            // Vérification si le login existe déjà
            $sql = "SELECT COUNT(*) 
                    FROM {$this->table} 
                    WHERE login = :login";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':login', $login);
            $stmt->execute();

            if ($stmt->fetchColumn() > 0) {
                return false; // Login déjà utilisé
            }

            // Hachage du mot de passe
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insertion du nouvel admin
            $sql2 = "INSERT INTO {$this->table} (login, mot_de_passe_hash)
                    VALUES (:login, :password)";
            $stmt = $this->db->prepare($sql2);

            return $stmt->execute([
                ':login' => $login,
                ':password' => $passwordHash
            ]);
        }

        
        /**
         * Changer le mot de passe de l'admin
         * 
         * @param int $id
         * @param string $newPassword
         * @return bool
         */

        public function updatePassword(int $id, string $newPassword): bool{

            // Hachage du nouveau mot de passe
            $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);

            // Requête de mise à jour
            $sql = "UPDATE Administrateur 
                    SET mot_de_passe_hash = :pass 
                    WHERE login = :login 
                    LIMIT 1";


            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':login' => $id,
                ':pass' => $passwordHash
            ]);
        }



        /**
         * Récuperer tous les admin sans le MDP hashé
         * 
         * @return array
         */
        public function getAllSecure(): array{
            $sql = "SELECT * 
                    FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            $admins = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            //retirer le MDP hashé pour la raison de la sécurité
            foreach ($admins as &$admin) {
                unset($admin['mot_de_passe_hash']);
            }
            return $admins;
        }

        //OU

        public function getAllSecure2(): array{
            $sql = "SELECT id, login 
                    FROM {$this->table}";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        
        /**
         * Obtenir les prêts gérés par cet administrateur
         * 
         * @param int $adminId
         * @return array
         */
        public function getAdminLoans(int $adminId): array {
            $pretModel = new Pret();

            $sql = "SELECT p.*, i.nom as item_nom, 
                        e.emprunteur_nom, e.emprunteur_prenom
                    FROM {$pretModel->getTable()} p
                    JOIN Item i ON p.item_id = i.id
                    JOIN Emprunteur e ON p.emprunteur_id = e.id
                    WHERE p.preteur_id = :admin_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':admin_id', $adminId, \PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        /**
         * Supprimer un admin
         * 
         * @param int $id => adminId
         * @return bool
         */
        public function deleteAdmin(int $id): bool {
            $sql = "DELETE FROM {$this->table}
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ":id" => $id
            ]);
        }
        
        /**
         * Obtenir le nom de table
         * 
         * @return string
         */
        public function getTable(): string {
            return $this->table;
        }
    }
?>