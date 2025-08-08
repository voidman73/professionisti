<?php
require_once '../config/database.php';

/**
 * Classe per la gestione delle competenze informatiche
 */
class CompetenzeIT {
    private $conn;
    private $table = 'IT';
    private $table_elenco = 'ITElenco';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Inserisce una nuova competenza informatica
     */
    public function inserisci($profilo_id, $competenza_codice, $livello, $anni_esperienza = null, $certificazioni = null) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (profilo_id, competenza_codice, livello, anni_esperienza, certificazioni) 
                     VALUES (:profilo_id, :competenza_codice, :livello, :anni_esperienza, :certificazioni)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->bindValue(':competenza_codice', $competenza_codice);
            $stmt->bindValue(':livello', $livello);
            $stmt->bindValue(':anni_esperienza', $anni_esperienza);
            $stmt->bindValue(':certificazioni', $certificazioni);
            
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'inserimento competenza IT: " . $e->getMessage());
        }
    }

    /**
     * Ottiene tutte le competenze IT di un professionista
     */
    public function ottieniPerProfilo($profilo_id) {
        try {
            $query = "SELECT i.*, ie.nome as competenza_nome, ie.categoria, ie.descrizione
                     FROM " . $this->table . " i
                     JOIN " . $this->table_elenco . " ie ON i.competenza_codice = ie.codice
                     WHERE i.profilo_id = :profilo_id 
                     ORDER BY ie.categoria ASC, ie.nome ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero competenze IT: " . $e->getMessage());
        }
    }

    /**
     * Ottiene l'elenco delle competenze IT disponibili
     */
    public function ottieniElencoCompetenze($categoria = null) {
        try {
            $query = "SELECT * FROM " . $this->table_elenco;
            if ($categoria) {
                $query .= " WHERE categoria = :categoria";
            }
            $query .= " ORDER BY categoria ASC, nome ASC";
            
            $stmt = $this->conn->prepare($query);
            if ($categoria) {
                $stmt->bindValue(':categoria', $categoria);
            }
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero elenco competenze IT: " . $e->getMessage());
        }
    }

    /**
     * Ottiene le categorie di competenze IT
     */
    public function ottieniCategorie() {
        try {
            $query = "SELECT DISTINCT categoria FROM " . $this->table_elenco . " ORDER BY categoria ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero categorie IT: " . $e->getMessage());
        }
    }

    /**
     * Aggiorna una competenza IT
     */
    public function aggiorna($id, $livello = null, $anni_esperienza = null, $certificazioni = null) {
        try {
            $campi = [];
            $parametri = [':id' => $id];
            
            if ($livello !== null) {
                $campi[] = "livello = :livello";
                $parametri[':livello'] = $livello;
            }
            
            if ($anni_esperienza !== null) {
                $campi[] = "anni_esperienza = :anni_esperienza";
                $parametri[':anni_esperienza'] = $anni_esperienza;
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
            throw new Exception("Errore nell'aggiornamento competenza IT: " . $e->getMessage());
        }
    }

    /**
     * Elimina una competenza IT
     */
    public function elimina($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'eliminazione competenza IT: " . $e->getMessage());
        }
    }

    /**
     * Ottiene i livelli di competenza disponibili
     */
    public function ottieniLivelli() {
        return [
            'BASE' => 'Base',
            'INTERMEDIO' => 'Intermedio',
            'AVANZATO' => 'Avanzato',
            'ESPERTO' => 'Esperto'
        ];
    }

    /**
     * Cerca professionisti per competenza IT
     */
    public function cercaProfessionisti($competenza_codice, $livello_minimo = 'BASE', $anni_minimi = null) {
        try {
            $livelli_ordine = ['BASE' => 1, 'INTERMEDIO' => 2, 'AVANZATO' => 3, 'ESPERTO' => 4];
            $livello_min_ordine = $livelli_ordine[$livello_minimo] ?? 1;
            
            $query = "SELECT i.*, p.nome, p.cognome, p.email, ie.nome as competenza_nome
                     FROM " . $this->table . " i
                     JOIN Profili p ON i.profilo_id = p.id
                     JOIN " . $this->table_elenco . " ie ON i.competenza_codice = ie.codice
                     WHERE i.competenza_codice = :competenza_codice
                     AND CASE 
                         WHEN i.livello = 'BASE' THEN 1
                         WHEN i.livello = 'INTERMEDIO' THEN 2
                         WHEN i.livello = 'AVANZATO' THEN 3
                         WHEN i.livello = 'ESPERTO' THEN 4
                         ELSE 0
                     END >= :livello_min_ordine";
            
            $params = [
                ':competenza_codice' => $competenza_codice,
                ':livello_min_ordine' => $livello_min_ordine
            ];
            
            if ($anni_minimi !== null) {
                $query .= " AND i.anni_esperienza >= :anni_minimi";
                $params[':anni_minimi'] = $anni_minimi;
            }
            
            $query .= " ORDER BY CASE 
                           WHEN i.livello = 'ESPERTO' THEN 4
                           WHEN i.livello = 'AVANZATO' THEN 3
                           WHEN i.livello = 'INTERMEDIO' THEN 2
                           WHEN i.livello = 'BASE' THEN 1
                       END DESC, i.anni_esperienza DESC, p.cognome, p.nome";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $param => $valore) {
                $stmt->bindValue($param, $valore);
            }
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nella ricerca professionisti per competenza IT: " . $e->getMessage());
        }
    }

    /**
     * Ottiene statistiche sulle competenze IT
     */
    public function ottieniStatistiche() {
        try {
            $stats = [];
            
            // Competenze più richieste
            $query = "SELECT ie.nome, ie.categoria, COUNT(*) as numero 
                     FROM " . $this->table . " i
                     JOIN " . $this->table_elenco . " ie ON i.competenza_codice = ie.codice
                     GROUP BY i.competenza_codice, ie.nome, ie.categoria
                     ORDER BY numero DESC LIMIT 15";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['piu_richieste'] = $stmt->fetchAll();
            
            // Distribuzione per categoria
            $query = "SELECT ie.categoria, COUNT(*) as numero 
                     FROM " . $this->table . " i
                     JOIN " . $this->table_elenco . " ie ON i.competenza_codice = ie.codice
                     GROUP BY ie.categoria
                     ORDER BY numero DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['per_categoria'] = $stmt->fetchAll();
            
            // Distribuzione livelli
            $query = "SELECT livello, COUNT(*) as numero 
                     FROM " . $this->table . " 
                     GROUP BY livello 
                     ORDER BY CASE 
                         WHEN livello = 'BASE' THEN 1
                         WHEN livello = 'INTERMEDIO' THEN 2
                         WHEN livello = 'AVANZATO' THEN 3
                         WHEN livello = 'ESPERTO' THEN 4
                     END";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['distribuzione_livelli'] = $stmt->fetchAll();
            
            return $stats;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero statistiche competenze IT: " . $e->getMessage());
        }
    }

    /**
     * Verifica se un professionista ha già una competenza
     */
    public function haCompetenza($profilo_id, $competenza_codice) {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                     WHERE profilo_id = :profilo_id AND competenza_codice = :competenza_codice";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->bindValue(':competenza_codice', $competenza_codice);
            $stmt->execute();
            
            return $stmt->fetch()['count'] > 0;
        } catch(PDOException $e) {
            throw new Exception("Errore nella verifica competenza IT: " . $e->getMessage());
        }
    }

    /**
     * Ottiene competenze raggruppate per categoria
     */
    public function ottieniCompetenzePerCategoria($profilo_id) {
        try {
            $query = "SELECT i.*, ie.nome as competenza_nome, ie.categoria, ie.descrizione
                     FROM " . $this->table . " i
                     JOIN " . $this->table_elenco . " ie ON i.competenza_codice = ie.codice
                     WHERE i.profilo_id = :profilo_id 
                     ORDER BY ie.categoria ASC, ie.nome ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->execute();
            $competenze = $stmt->fetchAll();
            
            // Raggruppa per categoria
            $raggruppate = [];
            foreach ($competenze as $comp) {
                $categoria = $comp['categoria'];
                if (!isset($raggruppate[$categoria])) {
                    $raggruppate[$categoria] = [];
                }
                $raggruppate[$categoria][] = $comp;
            }
            
            return $raggruppate;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero competenze per categoria: " . $e->getMessage());
        }
    }
}
?>