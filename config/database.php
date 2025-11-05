<?php
namespace Config;

class Database {

    private static $instance = null;
    private $connection;

    private $host;
    private $db;
    private $user;
    private $pass;

    private function __construct() {

        // 🔹 Identifiants AlwaysData
        $this->host = 'mysql-mediastock.alwaysdata.net';
        $this->db   = 'mediastock_db';
        $this->user = '439141';
        $this->pass = '5247_Juklau+123!';

        try {
            // Connexion PDO à la base AlwaysData
            $this->connection = new \PDO(
                "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4",
                $this->user,
                $this->pass
            );

            // Options PDO
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        } catch(\PDOException $e) {
            // ❌ N'affiche pas le mot de passe en cas d'erreur
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    // Singleton : une seule instance de la connexion
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Pour récupérer la connexion PDO
    public function getConnection() {
        return $this->connection;
    }
}
?>
