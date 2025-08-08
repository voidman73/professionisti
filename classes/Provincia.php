<?php
require_once '../config/database.php';

/**
 * Classe per la gestione delle Province italiane
 */
class Provincia {
    private $conn;
    private $table = 'Province';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Ottiene tutte le province
     */
    public function ottieniTutte($ordina_per_nome = true) {
        try {
            $query = "SELECT * FROM " . $this->table;
            if ($ordina_per_nome) {
                $query .= " ORDER BY Provincia ASC";
            } else {
                $query .= " ORDER BY Sigla ASC";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero province: " . $e->getMessage());
        }
    }

    /**
     * Ottiene una provincia per sigla
     */
    public function ottieniPerSigla($sigla) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE Sigla = :sigla";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':sigla', $sigla);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero provincia: " . $e->getMessage());
        }
    }

    /**
     * Ottiene province per regione
     */
    public function ottieniPerRegione($regione) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE Regione = :regione ORDER BY Provincia ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':regione', $regione);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero province per regione: " . $e->getMessage());
        }
    }

    /**
     * Ottiene tutte le regioni
     */
    public function ottieniRegioni() {
        try {
            $query = "SELECT DISTINCT Regione FROM " . $this->table . " ORDER BY Regione ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero regioni: " . $e->getMessage());
        }
    }

    /**
     * Cerca province per nome
     */
    public function cerca($termine) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                     WHERE Provincia LIKE :termine OR Sigla LIKE :termine 
                     ORDER BY Provincia ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':termine', '%' . $termine . '%');
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nella ricerca province: " . $e->getMessage());
        }
    }

    /**
     * Ottiene statistiche per province (numero professionisti per provincia)
     */
    public function ottieniStatisticheProfessionisti() {
        try {
            $query = "SELECT p.Sigla, p.Provincia, p.Regione, 
                            COUNT(pr.id) as numero_professionisti,
                            COUNT(CASE WHEN pr.stato = 'APPROVATO' THEN 1 END) as approvati,
                            COUNT(CASE WHEN pr.disponibile = 1 THEN 1 END) as disponibili
                     FROM " . $this->table . " p
                     LEFT JOIN Profili pr ON p.Sigla = pr.provincia_residenza
                     GROUP BY p.Sigla, p.Provincia, p.Regione
                     ORDER BY numero_professionisti DESC, p.Provincia ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero statistiche province: " . $e->getMessage());
        }
    }

    /**
     * Valida una sigla provincia
     */
    public function validaSigla($sigla) {
        if (empty($sigla) || strlen($sigla) !== 2) {
            return false;
        }
        
        $provincia = $this->ottieniPerSigla(strtoupper($sigla));
        return $provincia !== false;
    }
}
?>