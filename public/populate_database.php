<!-- il faut que je laisse ici: codes du prof -->

<?php
    /**
    * Script pour alimenter la base de données MediaStock avec des données de test
    *
    * Ce script va :
    * 1. Ajouter environ 50 éléments avec des catégories mixtes
    * 2. Ajouter 8 à 10 formations
    * 3. Créer au moins 20 prêts (terminés et en cours)
    */

    // Include the autoloader
    //__DIR__ => localisation actuelle du fichier
    require_once __DIR__ . '/autoload.php';
    

    // Set up error reporting
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // active le tampon de sortie (output buffering).
    // Au lieu d'envoyer tout de suite vers le navigateur les echo/print, 
    // PHP le stocke en mémoire jusqu’à ce que je décide de le faire
    ob_start();

    try {
        echo "<h1>Populating MediaStock Database</h1>";
        
        // récup la connexion de db
        $db = getDatabase();
        
        // créer des instances des modèles
        $itemModel = new Models\Item();
        $categorieModel = new Models\Categorie();
        $formationModel = new Models\Formation();
        $emprunteurModel = new Models\Emprunteur();
        $pretModel = new Models\Pret();
        $adminModel = new Models\Administrateur();
        
        //  récuperer tous les donées existants
        $categories = $categorieModel->getAll();
        $formations = $formationModel->getAll();
        $emprunteurs = $emprunteurModel->getAll();
        $admins = $adminModel->getAll();
        $items = $itemModel->getAll();
        
        // Vérifier s'il y a au moins 1 admin
        if (count($admins) == 0) {
            throw new Exception("No administrators found in the database. Please add at least one administrator.");
        }
        
        // 1. Peupler la table Formation s'il faut
        echo "<h2>Peupler la table Formation</h2>";
        
        $targetFormationCount = 10;
        $existingFormationCount = count($formations);
        $formationsToAdd = $targetFormationCount - $existingFormationCount;
        
        if ($formationsToAdd > 0) {
            $newFormations = [
                'BTS SIO SLAM',
                'BTS SIO SISR',
                'Licence Pro Développement Web',
                'Licence Pro Cybersécurité',
                'Master Informatique',
                'BTS Communication',
                'BTS Design Graphique',
                'BTS Audiovisuel',
                'Licence Arts Numériques',
                'Master Multimédia'
            ];
            
            // Only add formations that don't already exist
            $existingFormationNames = array_column($formations, 'formation');
            $formationsAdded = 0;
            
            foreach ($newFormations as $formation) {
                if (!in_array($formation, $existingFormationNames) && $formationsAdded < $formationsToAdd) {
                    $formationModel->create(['formation' => $formation]);
                    echo "<p>Formation ajouté: $formation</p>";
                    $formationsAdded++;
                }
            }
            
            // rafraîchir la formation
            $formations = $formationModel->getAll();
        } else {
            echo "<p>Des formations suffisantes existent déjà dans la base de données.</p>";
        }
        
        // 2. Peupler la table emprunteur s'il faut
        echo "<h2>Peupler la table emprunteur</h2>";
        
        $targetEmprunteurCount = 30;
        $existingEmprunteurCount = count($emprunteurs);
        $emprunteursToAdd = $targetEmprunteurCount - $existingEmprunteurCount;
        
        if ($emprunteursToAdd > 0) {
            $firstNames = ['Jean', 'Marie', 'Pierre', 'Sophie', 'Thomas', 'Julie', 'Nicolas', 'Emma', 'Lucas', 'Camille', 
                        'Antoine', 'Léa', 'Hugo', 'Chloé', 'Maxime', 'Sarah', 'Alexandre', 'Laura', 'Théo', 'Manon'];
            $lastNames = ['Martin', 'Bernard', 'Dubois', 'Thomas', 'Robert', 'Richard', 'Petit', 'Durand', 'Leroy', 'Moreau',
                        'Simon', 'Laurent', 'Lefebvre', 'Michel', 'Garcia', 'David', 'Bertrand', 'Roux', 'Vincent', 'Fournier'];
            $roles = ['etudiant(e)', 'intervenant'];
            
            for ($i = 0; $i < $emprunteursToAdd; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $role = $roles[array_rand($roles)];
                $formationId = $formations[array_rand($formations)]['id'];
                
                $emprunteurModel->create([
                    'emprunteur_nom' => $lastName,
                    'emprunteur_prenom' => $firstName,
                    'role' => $role,
                    'formation_id' => $formationId
                ]);
                
                echo "<p>Emprunteur ajouté: $firstName $lastName ($role)</p>";
            }
            
            //  rafraîchir la liste des emprunteurs
            $emprunteurs = $emprunteurModel->getAll();
        } else {
            echo "<p>Il existe déjà suffisamment d'emprunteurs dans la base de données.</p>";
        }
        
        // 3. Peupler la table Item
        echo "<h2>Peupler la table Item</h2>";
        
        $targetItemCount = 50;
        $existingItemCount = count($items);
        $itemsToAdd = $targetItemCount - $existingItemCount;
        
        if ($itemsToAdd > 0) {
            $itemTypes = [
                1 => [ // Informatique
                    'names' => ['Ordinateur portable', 'Souris sans fil', 'Clavier mécanique', 'Écran', 'Tablette', 'Webcam HD', 'Disque dur externe', 'Clé USB', 'Casque audio', 'Microphone'],
                    'models' => ['Dell XPS', 'HP Spectre', 'Logitech MX', 'Apple Magic', 'Samsung Galaxy', 'Microsoft Surface', 'Asus ZenBook', 'Acer Predator', 'Lenovo ThinkPad', 'Razer Blade']
                ],
                2 => [ // Audio
                    'names' => ['Microphone à condensateur', 'Casque studio', 'Enceinte bluetooth', 'Enregistreur portable', 'Table de mixage', 'Micro-cravate', 'Perche son', 'Amplificateur', 'Câble XLR', 'Carte son'],
                    'models' => ['Shure SM58', 'Audio-Technica AT2020', 'Rode NT1', 'Sennheiser HD650', 'Beyerdynamic DT990', 'JBL Flip', 'Zoom H4n', 'Tascam DR-40', 'Yamaha MG10', 'Focusrite Scarlett']
                ],
                3 => [ // Connectique
                    'names' => ['Câble HDMI', 'Adaptateur USB-C', 'Hub USB', 'Câble Ethernet', 'Adaptateur VGA', 'Câble audio', 'Multiprise', 'Rallonge', 'Adaptateur DisplayPort', 'Convertisseur HDMI-DVI'],
                    'models' => ['Belkin Pro', 'Anker PowerLine', 'Ugreen Premium', 'AmazonBasics', 'StarTech', 'Cable Matters', 'Monster Cable', 'Monoprice', 'BlueRigger', 'Tripp Lite']
                ],
                4 => [ // Autres
                    'names' => ['Vidéoprojecteur', 'Appareil photo', 'Trépied', 'Scanner', 'Imprimante', 'Tableau blanc interactif', 'Pointeur laser', 'Télécommande de présentation', 'Lampe de bureau', 'Chargeur portable'],
                    'models' => ['Epson EH-TW650', 'Canon EOS', 'Nikon D5600', 'Sony Alpha', 'Manfrotto MT190', 'Epson Perfection', 'HP LaserJet', 'Brother MFC', 'Logitech Spotlight', 'Anker PowerCore']
                ]
            ];
            
            $etats = ['bon', 'moyen', 'mauvais'];
            
            for ($i = 0; $i < $itemsToAdd; $i++) {
                // Sélectionnez une catégorie aléatoire
                $category = $categories[array_rand($categories)];
                $categoryId = $category['id'];
                
                // Obtenez les types d'articles appropriés pour cette catégorie
                $itemTypeData = isset($itemTypes[$categoryId]) ? $itemTypes[$categoryId] : $itemTypes[4]; // Default à "Autres"
                
                $name = $itemTypeData['names'][array_rand($itemTypeData['names'])];
                $model = $itemTypeData['models'][array_rand($itemTypeData['models'])];
                $qrCode = 'QR' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
                $imageUrl = 'images/' . strtolower(str_replace(' ', '_', $name)) . '.jpg';
                $etat = $etats[array_rand($etats)];
                
                $itemModel->create([
                    'nom' => $name,
                    'model' => $model,
                    'qr_code' => $qrCode,
                    'image_url' => $imageUrl,
                    'etat' => $etat,
                    'categorie_id' => $categoryId
                ]);
                
                echo "<p>Item ajouté: $name ($model) - Categorie: {$category['categorie']}</p>";
            }
            
            // rafraîchir la liste des items
            $items = $itemModel->getAll();
        } else {
            echo "<p>Il existe déjà suffisamment d'éléments dans la base de données.</p>";
        }
        
        // 4. Création des prêts
        echo "<h2>Création des prêts</h2>";
        
        // récuperer les prêts existants
        $prets = $pretModel->getAll();
        $targetPretCount = 20;
        $existingPretCount = count($prets);
        $pretsToAdd = $targetPretCount - $existingPretCount;
        
        if ($pretsToAdd > 0) {
            // Obtenir les articles disponibles (pas actuellement en prêt)
            $availableItems = $itemModel->getAvailableItems();
            
            // Si nous n'avons pas assez d'articles disponibles, nous réutiliserons certains articles
            if (count($availableItems) < $pretsToAdd) {
                $availableItems = $items;
            }
            
            // Notes pour les prêts
            $startNotes = [
                'Matériel en bon état',
                'Légères traces d\'usure',
                'Batterie à 80%',
                'Accessoires complets',
                'Emballage d\'origine',
                'Quelques rayures mineures',
                'Chargeur inclus',
                'Housse de protection fournie',
                'Vérification complète effectuée',
                'Dernière mise à jour installée'
            ];
            
            $endNotes = [
                'Retourné en bon état',
                'Légère détérioration constatée',
                'Batterie déchargée',
                'Accessoire manquant',
                'Rayures supplémentaires',
                'Problème technique signalé',
                'Nettoyage nécessaire',
                'Parfait état de fonctionnement',
                'Mise à jour effectuée par l\'emprunteur',
                'Réparation mineure nécessaire'
            ];
            
            // Créer un mélange de prêts terminés et en cours
            $today = new \DateTime();
            $adminId = $admins[0]['id']; // Utilisation le premier admin
            
            for ($i = 0; $i < $pretsToAdd; $i++) {
                $item = $availableItems[array_rand($availableItems)];
                $emprunteur = $emprunteurs[array_rand($emprunteurs)];
                
                //Date aléatoire dans le passé (entre 1 et 60 jours)
                $daysAgo = mt_rand(1, 60);
                $dateSortie = clone $today;
                $dateSortie->modify("-$daysAgo days");
                
                // Date de retour prévue (entre 7 et 30 jours après le paiement)
                $loanDuration = mt_rand(7, 30);
                $dateRetourPrevue = clone $dateSortie;
                $dateRetourPrevue->modify("+$loanDuration days");
                
                // Déterminer si ce prêt est terminé ou en cours
                $isCompleted = (mt_rand(0, 1) == 1) || ($dateRetourPrevue < $today);
                
                $noteDebut = $startNotes[array_rand($startNotes)];
                $noteFin = $isCompleted ? $endNotes[array_rand($endNotes)] : '';
                
                // Pour les prêts terminés, fixez une date de retour
                $dateRetourEffective = null;
                if ($isCompleted) {
                    // Date de retour entre la date de sortie et aujourd'hui (ou date de retour prévue si elle est passée)
                    $maxReturnDate = min($today, $dateRetourPrevue);
                    $daysUntilReturn = mt_rand(1, max(1, $dateSortie->diff($maxReturnDate)->days));
                    $dateRetourEffective = clone $dateSortie;
                    $dateRetourEffective->modify("+$daysUntilReturn days");
                }
                
                // Créer les prêts
                $pretData = [
                    'item_id' => $item['id'],
                    'emprunteur_id' => $emprunteur['id'],
                    'preteur_id' => $adminId,
                    'date_sortie' => $dateSortie->format('Y-m-d'),
                    'date_retour_prevue' => $dateRetourPrevue->format('Y-m-d'),
                    'date_retour_effective' => $isCompleted ? $dateRetourEffective->format('Y-m-d') : null,
                    'note_debut' => $noteDebut,
                    'note_fin' => $noteFin
                ];
                
                $pretId = $pretModel->create($pretData);
                
                $status = $isCompleted ? "completed" : "ongoing";
                $returnInfo = $isCompleted ? " (returned on " . $dateRetourEffective->format('Y-m-d') . ")" : " (due on " . $dateRetourPrevue->format('Y-m-d') . ")";
                
                echo "<p>Création $status loan: Item '{$item['nom']}' prété par {$emprunteur['emprunteur_prenom']} {$emprunteur['emprunteur_nom']} le " . 
                    $dateSortie->format('Y-m-d') . $returnInfo . "</p>";
            }
        } else {
            echo "<p>Il existe déjà suffisamment de prêts dans la base de données.</p>";
        }
        
        echo "<h2>Population de la base de données complète.</h2>";
        echo "<p>La base de données a été remplie avec succès avec des données de test.</p>";
        echo "<p><a href='index.php'>Retour à la page d'accueil</a></p>";
        
    } catch (Exception $e) {
        echo "<h2>Erreur</h2>";
        echo "<p>Une erreur s'est produite: " . $e->getMessage() . "</p>";
    }

    // End output buffering and display the page
    $output = ob_get_clean();
    echo $output;

?>