<?php
require_once '../config/database.php';

class Professionista {
    private $conn;
    private $table = 'Profili';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Inserisce un nuovo professionista nel database
     */
    public function inserisci($dati) {
        try {
            $query = "INSERT INTO " . $this->table . " 
                     (codice_fiscale, partita_iva, nome, cognome, data_nascita, luogo_nascita, sesso, 
                      email, telefono, cellulare, indirizzo_residenza, citta_residenza, provincia_residenza, 
                      cap_residenza, indirizzo_domicilio, citta_domicilio, provincia_domicilio, cap_domicilio,
                      disponibile, disponibilita_trasferte, raggio_azione_km, tariffa_oraria_min, tariffa_giornaliera_min, 
                      note_tariffe, competenze_principali, titolo_professionale, privacy_accettata, termini_accettati) 
                     VALUES 
                     (:codice_fiscale, :partita_iva, :nome, :cognome, :data_nascita, :luogo_nascita, :sesso,
                      :email, :telefono, :cellulare, :indirizzo_residenza, :citta_residenza, :provincia_residenza,
                      :cap_residenza, :indirizzo_domicilio, :citta_domicilio, :provincia_domicilio, :cap_domicilio,
                      :disponibile, :disponibilita_trasferte, :raggio_azione_km, :tariffa_oraria_min, :tariffa_giornaliera_min,
                      :note_tariffe, :competenze_principali, :titolo_professionale, :privacy_accettata, :termini_accettati)";

            $stmt = $this->conn->prepare($query);
            
            // Binding dei parametri
            foreach($dati as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception("Errore nell'inserimento: " . $e->getMessage());
        }
    }

    /**
     * Ottiene tutti i professionisti con filtri opzionali
     */
    public function ottieniTutti($filtri = []) {
        try {
            $query = "SELECT p.*, pr_res.Provincia as provincia_residenza_nome, pr_dom.Provincia as provincia_domicilio_nome
                     FROM " . $this->table . " p
                     LEFT JOIN Province pr_res ON p.provincia_residenza = pr_res.Sigla
                     LEFT JOIN Province pr_dom ON p.provincia_domicilio = pr_dom.Sigla
                     WHERE 1=1";

            $params = [];

            // Applicazione filtri
            if (!empty($filtri['albo_id'])) {
                $query .= " AND EXISTS (SELECT 1 FROM AlboProfili ap WHERE ap.profilo_id = p.id AND ap.albo_id = :albo_id AND ap.attiva = 1)";
                $params['albo_id'] = $filtri['albo_id'];
            }

            if (!empty($filtri['stato'])) {
                $query .= " AND p.stato = :stato";
                $params['stato'] = $filtri['stato'];
            }

            if (!empty($filtri['disponibile'])) {
                $query .= " AND p.disponibile = :disponibile";
                $params['disponibile'] = $filtri['disponibile'];
            }

            if (!empty($filtri['citta'])) {
                $query .= " AND (p.citta_residenza LIKE :citta OR p.citta_domicilio LIKE :citta)";
                $params['citta'] = '%' . $filtri['citta'] . '%';
            }
            
            if (!empty($filtri['provincia'])) {
                $query .= " AND (p.provincia_residenza = :provincia OR p.provincia_domicilio = :provincia)";
                $params['provincia'] = strtoupper($filtri['provincia']);
            }

            if (!empty($filtri['competenze'])) {
                $query .= " AND (p.competenze_principali LIKE :competenze OR p.settori_esperienza LIKE :competenze)";
                $params['competenze'] = '%' . $filtri['competenze'] . '%';
            }

            // TODO: Implementare filtro anni esperienza calcolando dalle tabelle LavoroAut e LavoroSub
            // Per ora disabilitato per evitare query complesse
            /*
            if (!empty($filtri['anni_esperienza_min'])) {
                $query .= " AND (SELECT COALESCE(SUM(YEAR(COALESCE(la.data_fine, CURDATE())) - YEAR(la.data_inizio)), 0) + 
                                COALESCE(SUM(YEAR(COALESCE(ls.data_fine, CURDATE())) - YEAR(ls.data_inizio)), 0)
                                FROM LavoroAut la LEFT JOIN LavoroSub ls ON la.profilo_id = ls.profilo_id 
                                WHERE la.profilo_id = p.id OR ls.profilo_id = p.id) >= :anni_esperienza_min";
                $params['anni_esperienza_min'] = $filtri['anni_esperienza_min'];
            }
            */

            if (!empty($filtri['tariffa_max'])) {
                $query .= " AND (p.tariffa_oraria_min <= :tariffa_max OR p.tariffa_giornaliera_min <= :tariffa_max_day)";
                $params['tariffa_max'] = $filtri['tariffa_max'];
                $params['tariffa_max_day'] = $filtri['tariffa_max'] * 8; // Assumendo 8 ore lavorative
            }

            $query .= " ORDER BY p.data_registrazione DESC";

            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nella ricerca: " . $e->getMessage());
        }
    }

    /**
     * Ottiene un professionista per ID
     */
    public function ottieniPerId($id) {
        try {
            $query = "SELECT p.*, pr_res.Provincia as provincia_residenza_nome, pr_dom.Provincia as provincia_domicilio_nome,
                            pr_nas.Provincia as provincia_nascita_nome, n_res.nome_it as nazione_residenza_nome,
                            n_dom.nome_it as nazione_domicilio_nome
                     FROM " . $this->table . " p
                     LEFT JOIN Province pr_res ON p.provincia_residenza = pr_res.Sigla
                     LEFT JOIN Province pr_dom ON p.provincia_domicilio = pr_dom.Sigla
                     LEFT JOIN Province pr_nas ON p.provincia_nascita = pr_nas.Sigla
                     LEFT JOIN ulkISO3166 n_res ON p.nazione_residenza = n_res.codice_alpha3
                     LEFT JOIN ulkISO3166 n_dom ON p.nazione_domicilio = n_dom.codice_alpha3
                     WHERE p.id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero professionista: " . $e->getMessage());
        }
    }

    /**
     * Aggiorna lo stato di un professionista
     */
    public function aggiornaStato($id, $stato, $note = '') {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET stato = :stato, note_amministrative = :note 
                     WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':stato', $stato);
            $stmt->bindValue(':note', $note);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'aggiornamento stato: " . $e->getMessage());
        }
    }

    /**
     * Verifica se email o codice fiscale esistono già
     */
    public function verificaUnivocita($email, $codice_fiscale, $partita_iva = null) {
        try {
            $query = "SELECT id FROM " . $this->table . " 
                     WHERE email = :email OR codice_fiscale = :codice_fiscale";
            
            $params = [
                'email' => $email,
                'codice_fiscale' => $codice_fiscale
            ];

            if ($partita_iva) {
                $query .= " OR partita_iva = :partita_iva";
                $params['partita_iva'] = $partita_iva;
            }

            $stmt = $this->conn->prepare($query);
            
            foreach($params as $key => $value) {
                $stmt->bindValue(':' . $key, $value);
            }

            $stmt->execute();
            return $stmt->fetch() ? true : false;
        } catch(PDOException $e) {
            throw new Exception("Errore nella verifica univocità: " . $e->getMessage());
        }
    }

    /**
     * Ottiene professionisti per IDs specifici (per esportazione)
     */
    public function ottieniPerIds($ids) {
        try {
            if (empty($ids)) {
                return [];
            }
            
            $placeholders = str_repeat('?,', count($ids) - 1) . '?';
            
            $query = "SELECT p.*, cp.nome as categoria_nome, s.nome as specializzazione_nome 
                     FROM " . $this->table . " p
                     LEFT JOIN categorie_professionali cp ON p.categoria_id = cp.id
                     LEFT JOIN specializzazioni s ON p.specializzazione_id = s.id
                     WHERE p.id IN ($placeholders)
                     ORDER BY p.data_iscrizione DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute($ids);
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero professionisti: " . $e->getMessage());
        }
    }

    /**
     * Ottiene statistiche sui professionisti
     */
    public function ottieniStatistiche() {
        try {
            $stats = [];

            // Totale professionisti
            $query = "SELECT COUNT(*) as totale FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['totale'] = $stmt->fetch()['totale'];

            // Per stato
            $query = "SELECT stato, COUNT(*) as numero FROM " . $this->table . " GROUP BY stato";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['per_stato'] = $stmt->fetchAll();

            // Per albo professionale
            $query = "SELECT a.nome, COUNT(DISTINCT ap.profilo_id) as numero 
                     FROM Albo a
                     LEFT JOIN AlboProfili ap ON a.id = ap.albo_id AND ap.attiva = 1
                     LEFT JOIN " . $this->table . " p ON ap.profilo_id = p.id
                     WHERE a.attivo = 1
                     GROUP BY a.id, a.nome
                     ORDER BY numero DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['per_albo'] = $stmt->fetchAll();

            // Nuove registrazioni ultimo mese
            $query = "SELECT COUNT(*) as numero FROM " . $this->table . " 
                     WHERE data_registrazione >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['nuove_ultimo_mese'] = $stmt->fetch()['numero'];

            // Professionisti disponibili
            $query = "SELECT COUNT(*) as numero FROM " . $this->table . " 
                     WHERE disponibile = 1 AND stato = 'APPROVATO'";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['disponibili'] = $stmt->fetch()['numero'];

            // Professionisti per provincia
            $query = "SELECT p.provincia_residenza, pr.Provincia, COUNT(*) as numero 
                     FROM " . $this->table . " p
                     LEFT JOIN Province pr ON p.provincia_residenza = pr.Sigla
                     WHERE p.provincia_residenza IS NOT NULL
                     GROUP BY p.provincia_residenza, pr.Provincia
                     ORDER BY numero DESC
                     LIMIT 10";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['per_provincia'] = $stmt->fetchAll();

            return $stats;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero statistiche: " . $e->getMessage());
        }
    }
}
?>