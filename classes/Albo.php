<?php
require_once '../config/database.php';

class Albo {
    private $conn;
    private $table = 'Albo';
    private $table_profili = 'AlboProfili';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Ottiene tutti gli albi attivi
     */
    public function ottieniTutti($solo_attivi = true) {
        try {
            $query = "SELECT * FROM " . $this->table;
            if ($solo_attivi) {
                $query .= " WHERE attivo = 1";
            }
            $query .= " ORDER BY nome ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero albi: " . $e->getMessage());
        }
    }

    /**
     * Ottiene un albo per ID
     */
    public function ottieniPerId($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero albo: " . $e->getMessage());
        }
    }

    /**
     * Ottiene i professionisti iscritti a un albo
     */
    public function ottieniProfessionistiAlbo($albo_id, $solo_attivi = true) {
        try {
            $query = "SELECT ap.*, p.nome, p.cognome, p.email FROM " . $this->table_profili . " ap
                     JOIN Profili p ON ap.profilo_id = p.id 
                     WHERE ap.albo_id = :albo_id";
            if ($solo_attivi) {
                $query .= " AND ap.attiva = 1";
            }
            $query .= " ORDER BY p.cognome, p.nome ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':albo_id', $albo_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero professionisti albo: " . $e->getMessage());
        }
    }

    /**
     * Ottiene gli albi di un professionista
     */
    public function ottieniAlbiProfessionista($profilo_id, $solo_attivi = true) {
        try {
            $query = "SELECT ap.*, a.nome, a.codice, a.descrizione FROM " . $this->table_profili . " ap
                     JOIN " . $this->table . " a ON ap.albo_id = a.id 
                     WHERE ap.profilo_id = :profilo_id";
            if ($solo_attivi) {
                $query .= " AND ap.attiva = 1 AND a.attivo = 1";
            }
            $query .= " ORDER BY a.nome ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero albi professionista: " . $e->getMessage());
        }
    }

    /**
     * Inserisce un nuovo albo
     */
    public function inserisci($codice, $nome, $descrizione = '', $sito_web = '') {
        try {
            $query = "INSERT INTO " . $this->table . " (codice, nome, descrizione, sito_web) VALUES (:codice, :nome, :descrizione, :sito_web)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':codice', $codice);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':descrizione', $descrizione);
            $stmt->bindValue(':sito_web', $sito_web);
            
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception("Errore nell'inserimento albo: " . $e->getMessage());
        }
    }

    /**
     * Iscrive un professionista a un albo
     */
    public function iscriviProfessionista($profilo_id, $albo_id, $numero_iscrizione, $data_iscrizione = null, $data_scadenza = null, $note = '') {
        try {
            $query = "INSERT INTO " . $this->table_profili . " (profilo_id, albo_id, numero_iscrizione, data_iscrizione, data_scadenza, note) 
                     VALUES (:profilo_id, :albo_id, :numero_iscrizione, :data_iscrizione, :data_scadenza, :note)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->bindValue(':albo_id', $albo_id);
            $stmt->bindValue(':numero_iscrizione', $numero_iscrizione);
            $stmt->bindValue(':data_iscrizione', $data_iscrizione);
            $stmt->bindValue(':data_scadenza', $data_scadenza);
            $stmt->bindValue(':note', $note);
            
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception("Errore nell'iscrizione professionista: " . $e->getMessage());
        }
    }

    /**
     * Attiva/Disattiva un albo
     */
    public function toggleAttivo($id) {
        try {
            $query = "UPDATE " . $this->table . " SET attivo = NOT attivo WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'aggiornamento albo: " . $e->getMessage());
        }
    }

    /**
     * Ottiene statistiche sugli albi
     */
    public function ottieniStatistiche() {
        try {
            $stats = [];

            // Professionisti per albo
            $query = "SELECT a.nome, COUNT(ap.profilo_id) as numero_professionisti 
                     FROM " . $this->table . " a
                     LEFT JOIN " . $this->table_profili . " ap ON a.id = ap.albo_id AND ap.attiva = 1
                     WHERE a.attivo = 1
                     GROUP BY a.id, a.nome
                     ORDER BY numero_professionisti DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['professionisti_per_albo'] = $stmt->fetchAll();

            // Iscrizioni scadute
            $query = "SELECT a.nome, COUNT(ap.profilo_id) as numero_scadute
                     FROM " . $this->table . " a
                     JOIN " . $this->table_profili . " ap ON a.id = ap.albo_id 
                     WHERE a.attivo = 1 AND ap.attiva = 1 AND ap.data_scadenza < CURDATE()
                     GROUP BY a.id, a.nome
                     ORDER BY numero_scadute DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['iscrizioni_scadute'] = $stmt->fetchAll();

            return $stats;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero statistiche: " . $e->getMessage());
        }
    }
}
?>