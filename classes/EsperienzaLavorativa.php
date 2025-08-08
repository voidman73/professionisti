<?php
require_once '../config/database.php';

/**
 * Classe per la gestione delle Esperienze Lavorative (autonomo e subordinato)
 */
class EsperienzaLavorativa {
    private $conn;
    private $table_aut = 'LavoroAut';
    private $table_sub = 'LavoroSub';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Inserisce una nuova esperienza lavorativa autonoma
     */
    public function inserisciAutonoma($dati) {
        try {
            $query = "INSERT INTO " . $this->table_aut . " 
                     (profilo_id, denominazione_cliente, tipo_attivita, settore, descrizione_incarico, 
                      data_inizio, data_fine, in_corso, compenso, valuta, note) 
                     VALUES (:profilo_id, :denominazione_cliente, :tipo_attivita, :settore, :descrizione_incarico,
                      :data_inizio, :data_fine, :in_corso, :compenso, :valuta, :note)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindValue(':profilo_id', $dati['profilo_id']);
            $stmt->bindValue(':denominazione_cliente', $dati['denominazione_cliente']);
            $stmt->bindValue(':tipo_attivita', $dati['tipo_attivita'] ?? null);
            $stmt->bindValue(':settore', $dati['settore'] ?? null);
            $stmt->bindValue(':descrizione_incarico', $dati['descrizione_incarico'] ?? null);
            $stmt->bindValue(':data_inizio', $dati['data_inizio']);
            $stmt->bindValue(':data_fine', $dati['data_fine'] ?? null);
            $stmt->bindValue(':in_corso', isset($dati['in_corso']) ? 1 : 0);
            $stmt->bindValue(':compenso', $dati['compenso'] ?? null);
            $stmt->bindValue(':valuta', $dati['valuta'] ?? 'EUR');
            $stmt->bindValue(':note', $dati['note'] ?? null);
            
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'inserimento esperienza autonoma: " . $e->getMessage());
        }
    }

    /**
     * Inserisce una nuova esperienza lavorativa subordinata
     */
    public function inserisciSubordinata($dati) {
        try {
            $query = "INSERT INTO " . $this->table_sub . " 
                     (profilo_id, denominazione_azienda, settore, posizione, descrizione_ruolo, 
                      data_inizio, data_fine, in_corso, tipo_contratto, ral_annua, note) 
                     VALUES (:profilo_id, :denominazione_azienda, :settore, :posizione, :descrizione_ruolo,
                      :data_inizio, :data_fine, :in_corso, :tipo_contratto, :ral_annua, :note)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindValue(':profilo_id', $dati['profilo_id']);
            $stmt->bindValue(':denominazione_azienda', $dati['denominazione_azienda']);
            $stmt->bindValue(':settore', $dati['settore'] ?? null);
            $stmt->bindValue(':posizione', $dati['posizione'] ?? null);
            $stmt->bindValue(':descrizione_ruolo', $dati['descrizione_ruolo'] ?? null);
            $stmt->bindValue(':data_inizio', $dati['data_inizio']);
            $stmt->bindValue(':data_fine', $dati['data_fine'] ?? null);
            $stmt->bindValue(':in_corso', isset($dati['in_corso']) ? 1 : 0);
            $stmt->bindValue(':tipo_contratto', $dati['tipo_contratto'] ?? null);
            $stmt->bindValue(':ral_annua', $dati['ral_annua'] ?? null);
            $stmt->bindValue(':note', $dati['note'] ?? null);
            
            $stmt->execute();
            return $this->conn->lastInsertId();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'inserimento esperienza subordinata: " . $e->getMessage());
        }
    }

    /**
     * Ottiene tutte le esperienze di un professionista
     */
    public function ottieniPerProfilo($profilo_id, $includi_subordinate = true) {
        try {
            $esperienze = [];
            
            // Esperienze autonome
            $query_aut = "SELECT *, 'AUTONOMA' as tipo_esperienza FROM " . $this->table_aut . " 
                         WHERE profilo_id = :profilo_id ORDER BY data_inizio DESC";
            $stmt = $this->conn->prepare($query_aut);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->execute();
            $esperienze = $stmt->fetchAll();
            
            if ($includi_subordinate) {
                // Esperienze subordinate
                $query_sub = "SELECT *, 'SUBORDINATA' as tipo_esperienza FROM " . $this->table_sub . " 
                             WHERE profilo_id = :profilo_id ORDER BY data_inizio DESC";
                $stmt = $this->conn->prepare($query_sub);
                $stmt->bindValue(':profilo_id', $profilo_id);
                $stmt->execute();
                $subordinate = $stmt->fetchAll();
                
                // Unisci e ordina per data
                $esperienze = array_merge($esperienze, $subordinate);
                usort($esperienze, function($a, $b) {
                    return strtotime($b['data_inizio']) - strtotime($a['data_inizio']);
                });
            }
            
            return $esperienze;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero esperienze: " . $e->getMessage());
        }
    }

    /**
     * Calcola gli anni di esperienza totali
     */
    public function calcolaAnniEsperienza($profilo_id) {
        try {
            $anni_totali = 0;
            
            // Calcola anni da esperienze autonome
            $query_aut = "SELECT SUM(DATEDIFF(COALESCE(data_fine, CURDATE()), data_inizio)) as giorni_aut 
                         FROM " . $this->table_aut . " WHERE profilo_id = :profilo_id";
            $stmt = $this->conn->prepare($query_aut);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->execute();
            $giorni_aut = $stmt->fetch()['giorni_aut'] ?? 0;
            
            // Calcola anni da esperienze subordinate
            $query_sub = "SELECT SUM(DATEDIFF(COALESCE(data_fine, CURDATE()), data_inizio)) as giorni_sub 
                         FROM " . $this->table_sub . " WHERE profilo_id = :profilo_id";
            $stmt = $this->conn->prepare($query_sub);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->execute();
            $giorni_sub = $stmt->fetch()['giorni_sub'] ?? 0;
            
            $giorni_totali = $giorni_aut + $giorni_sub;
            $anni_totali = round($giorni_totali / 365, 1);
            
            return $anni_totali;
        } catch(PDOException $e) {
            throw new Exception("Errore nel calcolo anni esperienza: " . $e->getMessage());
        }
    }

    /**
     * Aggiorna un'esperienza autonoma
     */
    public function aggiornaAutonoma($id, $dati) {
        return $this->_aggiorna($this->table_aut, $id, $dati);
    }

    /**
     * Aggiorna un'esperienza subordinata
     */
    public function aggiornaSubordinata($id, $dati) {
        return $this->_aggiorna($this->table_sub, $id, $dati);
    }

    /**
     * Metodo privato per aggiornamento generico
     */
    private function _aggiorna($table, $id, $dati) {
        try {
            $campi = [];
            $parametri = [':id' => $id];
            
            foreach ($dati as $campo => $valore) {
                if ($campo !== 'id') {
                    $campi[] = "$campo = :$campo";
                    $parametri[":$campo"] = $valore;
                }
            }
            
            if (empty($campi)) {
                throw new Exception("Nessun campo da aggiornare");
            }
            
            $query = "UPDATE $table SET " . implode(', ', $campi) . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            
            foreach ($parametri as $param => $valore) {
                $stmt->bindValue($param, $valore);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'aggiornamento esperienza: " . $e->getMessage());
        }
    }

    /**
     * Elimina un'esperienza autonoma
     */
    public function eliminaAutonoma($id) {
        return $this->_elimina($this->table_aut, $id);
    }

    /**
     * Elimina un'esperienza subordinata
     */
    public function eliminaSubordinata($id) {
        return $this->_elimina($this->table_sub, $id);
    }

    /**
     * Metodo privato per eliminazione generica
     */
    private function _elimina($table, $id) {
        try {
            $query = "DELETE FROM $table WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'eliminazione esperienza: " . $e->getMessage());
        }
    }

    /**
     * Ottiene statistiche sulle esperienze lavorative
     */
    public function ottieniStatistiche() {
        try {
            $stats = [];
            
            // Esperienze per settore
            $query = "SELECT settore, COUNT(*) as numero 
                     FROM (
                        SELECT settore FROM " . $this->table_aut . " WHERE settore IS NOT NULL
                        UNION ALL 
                        SELECT settore FROM " . $this->table_sub . " WHERE settore IS NOT NULL
                     ) as settori
                     GROUP BY settore ORDER BY numero DESC LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['per_settore'] = $stmt->fetchAll();
            
            // Esperienze attive
            $query_aut = "SELECT COUNT(*) as numero FROM " . $this->table_aut . " WHERE in_corso = 1";
            $stmt = $this->conn->prepare($query_aut);
            $stmt->execute();
            $autonome_attive = $stmt->fetch()['numero'];
            
            $query_sub = "SELECT COUNT(*) as numero FROM " . $this->table_sub . " WHERE in_corso = 1";
            $stmt = $this->conn->prepare($query_sub);
            $stmt->execute();
            $subordinate_attive = $stmt->fetch()['numero'];
            
            $stats['attive'] = ['autonome' => $autonome_attive, 'subordinate' => $subordinate_attive];
            
            return $stats;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero statistiche esperienze: " . $e->getMessage());
        }
    }

    /**
     * Cerca esperienze per termine
     */
    public function cerca($termine, $tipo = null) {
        try {
            $esperienze = [];
            
            if (!$tipo || $tipo === 'autonoma') {
                $query_aut = "SELECT la.*, p.nome, p.cognome, 'AUTONOMA' as tipo_esperienza 
                             FROM " . $this->table_aut . " la
                             JOIN Profili p ON la.profilo_id = p.id
                             WHERE la.denominazione_cliente LIKE :termine 
                             OR la.descrizione_incarico LIKE :termine 
                             OR la.settore LIKE :termine";
                $stmt = $this->conn->prepare($query_aut);
                $stmt->bindValue(':termine', '%' . $termine . '%');
                $stmt->execute();
                $esperienze = array_merge($esperienze, $stmt->fetchAll());
            }
            
            if (!$tipo || $tipo === 'subordinata') {
                $query_sub = "SELECT ls.*, p.nome, p.cognome, 'SUBORDINATA' as tipo_esperienza 
                             FROM " . $this->table_sub . " ls
                             JOIN Profili p ON ls.profilo_id = p.id
                             WHERE ls.denominazione_azienda LIKE :termine 
                             OR ls.descrizione_ruolo LIKE :termine 
                             OR ls.settore LIKE :termine";
                $stmt = $this->conn->prepare($query_sub);
                $stmt->bindValue(':termine', '%' . $termine . '%');
                $stmt->execute();
                $esperienze = array_merge($esperienze, $stmt->fetchAll());
            }
            
            return $esperienze;
        } catch(PDOException $e) {
            throw new Exception("Errore nella ricerca esperienze: " . $e->getMessage());
        }
    }
}
?>