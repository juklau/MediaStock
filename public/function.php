<?php

/**
 * Connexion à la base de données MySQL via PDO
 */
function getPDOConnection(): PDO {
    $servername = 'mysql'; // Important : nom du service Docker, pas localhost
    $username = getenv('DB_USER') ?: 'mediastock';
    $password = getenv('DB_PASSWORD') ?: '5247_Juklau+123!';
    $dbname = getenv('DB_NAME') ?: 'mediastock';

    try {
        $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // gestion d’erreurs
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // fetch propre
            PDO::ATTR_EMULATE_PREPARES => false, // meilleure sécurité
        ];
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        die("Erreur de connexion à la base de données : " . $e->getMessage());
    }
}

/**
 * Récupère toutes les catégories depuis la table `Categorie`
 */
function getAllCategories(): array {
    $pdo = getPDOConnection();

    $sql = "SELECT id, categorie FROM Categorie ORDER BY categorie ASC";
    $stmt = $pdo->query($sql);

    $categories = $stmt->fetchAll();

    return $categories ?: []; // retourne un tableau vide si aucune donnée
}

/**
 * Récupère toutes les formations depuis la table `Formation`
 */
function getAllFormations(): array {
    $pdo = getPDOConnection();

    $sql = "SELECT id, formation FROM Formation ORDER BY formation ASC";
    $stmt = $pdo->query($sql);

    $formations = $stmt->fetchAll();

    return $formations ?: []; // retourne un tableau vide si aucune donnée
}

/**
 * Récupère toutes les sous-catégories depuis la table `Sous_categorie`
 */
function getAllSousCategories($id): array {
    $pdo = getPDOConnection();

    $sql = "SELECT id, sous_categorie, categorie_id FROM Sous_categorie WHERE categorie_id = :id ORDER BY sous_categorie ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    $sousCategories = $stmt->fetchAll();

    return $sousCategories ?: []; // retourne un tableau vide si aucune donnée
}

/**
 * Crée un nouveau prêt dans la table `Pret`
 *
 * @param int $item_id                ID de l'objet emprunté
 * @param int $emprenteur_id          ID de l'emprunteur
 * @param string $date_sortie         Date de sortie (format 'Y-m-d')
 * @param string $date_retour_prevue  Date prévue de retour (format 'Y-m-d')
 * @param string|null $date_retour_effective  Date réelle de retour (ou null si pas encore rendu)
 * @param string $note_debut          État ou commentaire avant l'emprunt
 * @param string $note_fin            État ou commentaire après le retour
 * @param int $preteur_id             ID de l’administrateur (prêteur)
 *
 * @return bool true si insertion réussie, false sinon
 */
function createPret(
    int $item_id ,
    int $emprenteur_id,
    string $date_sortie,
    string $date_retour_prevue,
    ?string $date_retour_effective,
    string $note_debut,
    string $note_fin,
    int $preteur_id
): bool {
    $pdo = getPDOConnection();

    //Blocage si l’article est déjà en prêt
    if (isItemEnPret($item_id)) {
    echo "❌ Cet article est déjà en prêt.";
    return false;
    }

    $sql = "INSERT INTO Pret (
                item_id, emprenteur_id, date_sortie, date_retour_prévue,
                date_retour_effective, note_debut, note_fin, preteur_id
            ) VALUES (
                :item_id, :emprenteur_id, :date_sortie, :date_retour_prevue,
                :date_retour_effective, :note_debut, :note_fin, :preteur_id
            )";

    $stmt = $pdo->prepare($sql);

    try {
        return $stmt->execute([
            ':item_id' => $item_id,
            ':emprenteur_id' => $emprenteur_id,
            ':date_sortie' => $date_sortie,
            ':date_retour_prevue' => $date_retour_prevue,
            ':date_retour_effective' => $date_retour_effective,
            ':note_debut' => $note_debut,
            ':note_fin' => $note_fin,
            ':preteur_id' => $preteur_id
        ]);
    } catch (PDOException $e) {
        error_log("Erreur lors de la création du prêt : " . $e->getMessage());
        return false;
    }
}

/**
 * Vérifie si un article est actuellement en prêt
 *
 * @param int $item_id  ID de l'article à vérifier
 * @return bool true si l'article est en prêt, false sinon
 */
function isItemEnPret(int $item_id): bool {
    $pdo = getPDOConnection();

    $sql = "SELECT COUNT(*) as nb 
            FROM Pret 
            WHERE item_id = :item_id 
              AND date_retour_effective IS NULL";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':item_id' => $item_id]);
    $result = $stmt->fetch();

    // Si au moins un prêt sans date de retour, l’article est encore emprunté
    return $result && $result['nb'] > 0;
}

?>