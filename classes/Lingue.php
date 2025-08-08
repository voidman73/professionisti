<?php
require_once '../config/database.php';

/**
 * Classe per la gestione delle competenze linguistiche
 */
class Lingue {
    private $conn;
    private $table = 'Lingue';
    private $table_elenco = 'LingueElenco';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Inserisce una nuova competenza linguistica
     */
    public function inserisci($profilo_id, $lingua_id, $livello_parlato, $livello_scritto, $livello_comprensione, $certificazioni = null) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (profilo_id, lingua_id, livello_parlato, livello_scritto, livello_comprensione, certificazioni) 
                     VALUES (:profilo_id, :lingua_id, :livello_parlato, :livello_scritto, :livello_comprensione, :certificazioni)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->bindValue(':lingua_id', $lingua_id);
            $stmt->bindValue(':livello_parlato', $livello_parlato);
            $stmt->bindValue(':livello_scritto', $livello_scritto);
            $stmt->bindValue(':livello_comprensione', $livello_comprensione);
            $stmt->bindValue(':certificazioni', $certificazioni);
            
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'inserimento competenza linguistica: " . $e->getMessage());
        }
    }

    /**
     * Ottiene tutte le competenze linguistiche di un professionista
     */
    public function ottieniPerProfilo($profilo_id) {
        try {
            $query = "SELECT l.*, le.nome_it as lingua_nome, le.nome_en as lingua_nome_en, le.codice as lingua_codice
                     FROM " . $this->table . " l
                     JOIN " . $this->table_elenco . " le ON l.lingua_id = le.id
                     WHERE l.profilo_id = :profilo_id 
                     ORDER BY le.nome_it ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero competenze linguistiche: " . $e->getMessage());
        }
    }

    /**
     * Ottiene l'elenco delle lingue disponibili
     */
    public function ottieniElencoLingue() {
        try {
            $query = "SELECT * FROM " . $this->table_elenco . " WHERE attiva = 1 ORDER BY nome_it ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero elenco lingue: " . $e->getMessage());
        }
    }

    /**
     * Aggiorna una competenza linguistica
     */
    public function aggiorna($id, $livello_parlato = null, $livello_scritto = null, $livello_comprensione = null, $certificazioni = null) {
        try {
            $campi = [];
            $parametri = [':id' => $id];
            
            if ($livello_parlato !== null) {
                $campi[] = "livello_parlato = :livello_parlato";
                $parametri[':livello_parlato'] = $livello_parlato;
            }
            
            if ($livello_scritto !== null) {
                $campi[] = "livello_scritto = :livello_scritto";
                $parametri[':livello_scritto'] = $livello_scritto;
            }
            
            if ($livello_comprensione !== null) {
                $campi[] = "livello_comprensione = :livello_comprensione";
                $parametri[':livello_comprensione'] = $livello_comprensione;
            }
            
            if ($certificazioni !== null) {
                $campi[] = "certificazioni = :certificazioni";
                $parametri[':certificazioni'] = $certificazioni;
            }
            
            if (empty($campi)) {
                throw new Exception("Nessun campo da aggiornare");
            }
            
            $query = "UPDATE " . $this->table . " SET " . implode(', ', $campi) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            foreach ($parametri as $param => $valore) {
                $stmt->bindValue($param, $valore);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'aggiornamento competenza linguistica: " . $e->getMessage());
        }
    }

    /**
     * Elimina una competenza linguistica
     */
    public function elimina($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'eliminazione competenza linguistica: " . $e->getMessage());
        }
    }

    /**
     * Ottiene i livelli linguistici disponibili
     */
    public function ottieniLivelli() {
        return [
            'BASE' => 'Base',
            'INTERMEDIO' => 'Intermedio',
            'AVANZATO' => 'Avanzato',
            'MADRELINGUA' => 'Madrelingua'
        ];
    }

    /**
     * Cerca professionisti per competenza linguistica
     */
    public function cercaProfessionisti($lingua_id, $livello_minimo = 'BASE', $tipo_livello = 'parlato') {
        try {
            $livelli_ordine = ['BASE' => 1, 'INTERMEDIO' => 2, 'AVANZATO' => 3, 'MADRELINGUA' => 4];
            $livello_min_ordine = $livelli_ordine[$livello_minimo] ?? 1;
            
            $campo_livello = $tipo_livello === 'scritto' ? 'livello_scritto' : 
                            ($tipo_livello === 'comprensione' ? 'livello_comprensione' : 'livello_parlato');
            
            $query = "SELECT l.*, p.nome, p.cognome, p.email, le.nome_it as lingua_nome
                     FROM " . $this->table . " l
                     JOIN Profili p ON l.profilo_id = p.id
                     JOIN " . $this->table_elenco . " le ON l.lingua_id = le.id
                     WHERE l.lingua_id = :lingua_id
                     AND CASE 
                         WHEN l.$campo_livello = 'BASE' THEN 1
                         WHEN l.$campo_livello = 'INTERMEDIO' THEN 2
                         WHEN l.$campo_livello = 'AVANZATO' THEN 3
                         WHEN l.$campo_livello = 'MADRELINGUA' THEN 4
                         ELSE 0
                     END >= :livello_min_ordine
                     ORDER BY p.cognome, p.nome";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':lingua_id', $lingua_id);
            $stmt->bindValue(':livello_min_ordine', $livello_min_ordine);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nella ricerca professionisti per lingua: " . $e->getMessage());
        }
    }

    /**
     * Ottiene statistiche sulle competenze linguistiche
     */
    public function ottieniStatistiche() {
        try {
            $stats = [];
            
            // Lingue più conosciute
            $query = "SELECT le.nome_it, COUNT(*) as numero 
                     FROM " . $this->table . " l
                     JOIN " . $this->table_elenco . " le ON l.lingua_id = le.id
                     GROUP BY l.lingua_id, le.nome_it
                     ORDER BY numero DESC LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['piu_conosciute'] = $stmt->fetchAll();
            
            // Distribuzione livelli
            $query = "SELECT livello_parlato as livello, COUNT(*) as numero 
                     FROM " . $this->table . " 
                     GROUP BY livello_parlato 
                     ORDER BY CASE 
                         WHEN livello_parlato = 'BASE' THEN 1
                         WHEN livello_parlato = 'INTERMEDIO' THEN 2
                         WHEN livello_parlato = 'AVANZATO' THEN 3
                         WHEN livello_parlato = 'MADRELINGUA' THEN 4
                     END";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['distribuzione_livelli'] = $stmt->fetchAll();
            
            return $stats;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero statistiche lingue: " . $e->getMessage());
        }
    }

    /**
     * Verifica se un professionista ha già una competenza per una lingua
     */
    public function haCompetenza($profilo_id, $lingua_id) {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                     WHERE profilo_id = :profilo_id AND lingua_id = :lingua_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->bindValue(':lingua_id', $lingua_id);
            $stmt->execute();
            
            return $stmt->fetch()['count'] > 0;
        } catch(PDOException $e) {
            throw new Exception("Errore nella verifica competenza linguistica: " . $e->getMessage());
        }
    }
}
?>