

<?php

    class ItemRepository{

        private PDO $pdo;

        public function __construct(PDO $pdo){
            $this->pdo = $pdo;

            // Bonnes pratiques PDO
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        }

       
        


        // Fonction liés au emprunteur

        // ajouter un emprunteur (formation_id facultatif pour intervenant) => OK
        public function addEmprunteur(string $nom, string $prenom, string $role, ?int $formation_id): int {
            
            //Valider le rôle (ENUM)
            $allowed = ['etudiant(e)', 'intervenant'];
            if (!in_array($role, $allowed, true)) {
                throw new InvalidArgumentException("role doit être 'etudiant(e)' ou 'intervenant'");
            }

            //Règle métier : formation obligatoire pour les étudiants
            if ($role === 'etudiant(e)' && $formation_id === null) {
                throw new InvalidArgumentException("formation_id est requis pour un(e) etudiant(e).");
            }

            //INSERT (on passe NULL pour intervenant)
            $sql = "INSERT INTO emprunteur (emprunteur_nom, emprunteur_prenom, role, formation_id)
                    VALUES (:nom, :prenom, :role, :formation_id)";
            $stmt = $this->pdo->prepare($sql);
            //explication dans le doc réalisation MediaStock
            $stmt->bindValue(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindValue(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindValue(':role', $role, PDO::PARAM_STR);
            // si intervenant → formation_id NULL
            if ($formation_id === null) {
                $stmt->bindValue(':formation_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':formation_id', $formation_id, PDO::PARAM_INT);
            }
            $stmt->execute();

            return (int)$this->pdo->lastInsertId();
        }


        //ajouter un Item =>OK
        public function addItem(String $nom, ?string $model, string $qr_code, string $image_url, string $etat, int $categorie_id): bool{
            $sql = "INSERT INTO Item (nom, model, qr_code, image_url, etat, categorie_id)
                    VALUES (:nom, :model, :qr_code, :image_url, :etat, :categorie_id)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ":nom" => $nom,
                ":model" => $model,
                ":qr_code" => $qr_code,
                ":image_url" => $image_url,
                ":etat" => $etat,
                ":categorie_id" => $categorie_id
            ]);
        }


        // lister tous les matériels => OK
        public function getAllItems(): array{
            $sql = "SELECT * 
                    FROM Item";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
 

        //afficher un seul matériel par ID
        public function getItemByID(int $id): array{
            $sql = "SELECT * 
                    FROM Item 
                    WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ":id" => $id
            ]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        }


       public function getWithSubcategories($id) {
            $category = $this->getById($id);

            if ($category) {
                $sousCategorie = new SousCategorie();
                $category['sous_categories'] = $sousCategorie->findBy('categorie_id', $category['id']);
            }

            return $category;
        }


        // //supprimer Item
        // function public deleteItem(int $id): bool {
        //     $sql = "DELETE FROM Item 
        //             WHERE id = :id";
        //     $stmt = $this->pdo->prepare($sql);
        //     return $stmt->execute([
        //         ":id" => $id
        //     ]);
        // }




        // //Fonction liés au Pret

        // //créer un nouveau prêt
        // public function nouveauPret(int $item_id, int $emprunteur_id, DateTime $date_sortie, DateTime $date_retour_prevue, string $note_debut, int $preteur_id): bool{
        //     $sql = "INSERT INTO Pret (item_id, emprunteur_id, date_sortie, date_retour_prevue, note_debut, note_fin; preteur_id)
        //             VALUES (:item_id, :emprunteur_id, :date_sortie, :date_retour_prevue, :note_debut, :note_fin :preteur_id)";
        //     $stmt = $this->pdo->prepare($sql);
        //     return $stmt->execute([
        //         ":item_id" => $item_id,
        //         ":emprunteur_id" => $emprunteur_id,
        //         ":date_sortie" => $date_sortie->format('Y-m-d'),
        //         ":date_retour_prevue" => $date_retour_prevue->format('Y-m-d'),
        //         ":note_debut" => $note_debut,
        //         ":note_fin"   => '',              // car NOT NULL dans le schéma
        //         ":preteur_id" => $preteur_id
        //     ]);
            
        // }

        // //Clôturer la fin du prêt
        // public function cloturerPret(int $pret_id, DateTime $date_retour_effective, string $note_fin): bool{
        //     $sql = "UPDATE Pret
        //             SET date_retour_effective = :dre, note_fin = :note_fin
        //             WHERE id = :id";
        //     $stmt = $this->pdo->prepare($sql);
        //     return $stmt->execute([
        //         ':dre'      => $date_retour_effective->format('Y-m-d'),
        //         ':note_fin' => $note_fin,
        //         ':id'       => $pret_id,
        //     ]);
        // }

        // //afficher les prêts qui ne sont pas rendu
        // public function affichePretPasRendu(): array{
        //     $sql = "SELECT p.*, i.nom AS item_nom, i.model AS item_model, e.emprunteur_nom, e.emprunteur_prenom
        //             FROM Pret p
        //             JOIN Item i ON i.id = p.item_id
        //             JOIN emprenteur e ON e.id = p.emprunteur_id
        //             WHERE date_retour_effective IS NULL
        //             ORDER BY p.date_sortie DESC, p.id DESC";
        //     $stmt = $this->pdo->prepare($sql);
        //     $stmt->execute();
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // }

        // //afficher les items disponible
        // public function afficheItemDisponible(): array{
        //     $sql = "SELECT i.nom, i.model 
        //             FROM Item i
        //             -- si la sous-requête ne trouve aucune ligne correspondante.
        //             -- il faut que item ne soit pas dans ce liste
        //             WHERE NOT EXISTS (
        //             -- on test l'existance si item est prété
        //                 SELECT 1
        //                 FROM Pret p 
        //                 WHERE p.item_id = i.id
        //                 AND p.date_retour_effective IS NULL
        //             )
        //             ORDER BY i.id DESC";
        //     $stmt = $this->pdo->prepare($sql);
        //     $stmt->execute();
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // }


        // //afficher les items indisponibles
        // public function afficheItemIndisponible(): array{
        //     $sql = "SELECT i.nom, i.model 
        //             FROM Item i
        //             -- si la sous-requête ne trouve aucune ligne correspondante.
        //             -- il faut que item soit dans ce liste
        //             WHERE EXISTS (
        //             -- on test l'existance si item n'est pas prété
        //                 SELECT 1
        //                 FROM Pret p 
        //                 WHERE p.item_id = i.id
        //                 AND p.date_retour_effective IS NOT NULL
        //             )
        //             ORDER BY i.nom ASC";
        //     $stmt = $this->pdo->prepare($sql);
        //     $stmt->execute();
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // }





        // //????
        // private function assertRole(string $rôle): void{
        //     $allowed =['etudiant(e), intervenant'];
        //     if(!in_array($role, $allowed, true)){
        //         throw new InvalidArgumentException("role doit être 'etudiant(e)' ou 'intervenant'");
        //     }
        // }


        // //lister des prêt faite par le rôle
        // public function getPretByRole(string $role): ?array{
        //     $this->assertRole($role);
        //     $sql = "SELECT p.*, i.nom AS item_nom, e.emprunteur_nom, e.emprunteur_prenom, e.role 
        //             FROM Pret p
        //             JOIN Item i ON i.id = p.item_id
        //             JOIN emprunteur e ON e.id = p.emprunteur_id
        //             WHERE e.role = :role
        //             ORDER BY p.date_sortie DESC";
        //     $stmt = $this->pdo->prepare($sql);
        //     $stmt->execute([
        //         ":role" => $role
        //     ]);
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // }

        // //lister des prêt faite par le nom et le prénom
        // public function getPretByName(string $nom): ?array{
        //     $sql = "SELECT p.*, i.nom AS item_nom, e.emprunteur_nom, e.emprunteur_prenom, e.role
        //             FROM Pret p
        //             JOIN Item i ON i.id = p.item_id
        //             JOIN emprunteur e ON e.id = p.emprunteur_id
        //             WHERE e.emprunteur_nom = :emprunteur_nom
        //             ORDER BY p.date_sortie DESC";
        //     $stmt = $this->pdo->prepare($sql);
        //     $stmt->execute([
        //         ":emprunteur_nom" => $nom
        //     ]);
        //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
        // }





    




        


        
        
        
        







    }





?>