<?php
require_once '../config/database.php';

/**
 * Classe per la gestione dell'Istruzione dei professionisti
 */
class Istruzione {
    private $conn;
    private $table = 'Istruzione';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Inserisce un nuovo titolo di studio
     */
    public function inserisci($profilo_id, $tipo, $titolo, $istituto, $data_conseguimento, $voto = null, $descrizione = null, $citta = null, $provincia = null, $nazione = 'ITA', $durata_ore = null, $crediti_formativi = null) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (profilo_id, tipo, titolo, istituto, data_conseguimento, voto, descrizione, citta, provincia, nazione, durata_ore, crediti_formativi) 
                     VALUES (:profilo_id, :tipo, :titolo, :istituto, :data_conseguimento, :voto, :descrizione, :citta, :provincia, :nazione, :durata_ore, :crediti_formativi)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->bindValue(':tipo', $tipo);
            $stmt->bindValue(':titolo', $titolo);
            $stmt->bindValue(':istituto', $istituto);
            $stmt->bindValue(':data_conseguimento', $data_conseguimento);
            $stmt->bindValue(':voto', $voto);
            $stmt->bindValue(':descrizione', $descrizione);
            $stmt->bindValue(':citta', $citta);
            $stmt->bindValue(':provincia', $provincia);
            $stmt->bindValue(':nazione', $nazione);
            $stmt->bindValue(':durata_ore', $durata_ore);
            $stmt->bindValue(':crediti_formativi', $crediti_formativi);
            
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'inserimento titolo di studio: " . $e->getMessage());
        }
    }

    /**
     * Ottiene tutti i titoli di studio di un professionista
     */
    public function ottieniPerProfilo($profilo_id) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                     WHERE profilo_id = :profilo_id 
                     ORDER BY data_conseguimento DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero titoli di studio: " . $e->getMessage());
        }
    }

    /**
     * Ottiene un titolo di studio specifico
     */
    public function ottieniPerId($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero titolo di studio: " . $e->getMessage());
        }
    }

    /**
     * Aggiorna un titolo di studio
     */
    public function aggiorna($id, $dati) {
        try {
            $campi = [];
            $parametri = [':id' => $id];
            
            foreach ($dati as $campo => $valore) {
                if (in_array($campo, ['tipo', 'titolo', 'istituto', 'data_conseguimento', 'voto', 'descrizione', 'citta', 'provincia', 'nazione', 'durata_ore', 'crediti_formativi'])) {
                    $campi[] = "$campo = :$campo";
                    $parametri[":$campo"] = $valore;
                }
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
            throw new Exception("Errore nell'aggiornamento titolo di studio: " . $e->getMessage());
        }
    }

    /**
     * Elimina un titolo di studio
     */
    public function elimina($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'eliminazione titolo di studio: " . $e->getMessage());
        }
    }

    /**
     * Ottiene statistiche sui titoli di studio
     */
    public function ottieniStatistiche() {
        try {
            $stats = [];
            
            // Per tipo di titolo
            $query = "SELECT tipo, COUNT(*) as numero FROM " . $this->table . " 
                     GROUP BY tipo ORDER BY numero DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['per_tipo'] = $stmt->fetchAll();
            
            // Per anno di conseguimento
            $query = "SELECT YEAR(data_conseguimento) as anno, COUNT(*) as numero FROM " . $this->table . " 
                     WHERE data_conseguimento IS NOT NULL 
                     GROUP BY YEAR(data_conseguimento) ORDER BY anno DESC LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['per_anno'] = $stmt->fetchAll();
            
            // Con voto
            $query = "SELECT COUNT(*) as numero FROM " . $this->table . " WHERE voto IS NOT NULL AND voto != ''";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['con_voto'] = $stmt->fetch()['numero'];
            
            return $stats;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero statistiche istruzione: " . $e->getMessage());
        }
    }

    /**
     * Ottiene i tipi di titolo più comuni
     */
    public function ottieniTipiTitolo() {
        return [
            'LAUREA' => 'Laurea',
            'DIPLOMA' => 'Diploma di Scuola Superiore',
            'MASTER' => 'Master',
            'DOTTORATO' => 'Dottorato di Ricerca',
            'CORSO' => 'Corso Professionale',
            'CERTIFICAZIONE' => 'Certificazione Professionale'
        ];
    }

    /**
     * Cerca titoli di studio per termine
     */
    public function cerca($termine, $tipo = null) {
        try {
            $query = "SELECT i.*, p.nome, p.cognome FROM " . $this->table . " i
                     JOIN Profili p ON i.profilo_id = p.id
                     WHERE (i.titolo LIKE :termine OR i.istituto LIKE :termine)";
            
            $params = [':termine' => '%' . $termine . '%'];
            
            if ($tipo) {
                $query .= " AND i.tipo = :tipo";
                $params[':tipo'] = $tipo;
            }
            
            $query .= " ORDER BY i.data_conseguimento DESC";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $param => $valore) {
                $stmt->bindValue($param, $valore);
            }
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nella ricerca titoli di studio: " . $e->getMessage());
        }
    }
}
?>