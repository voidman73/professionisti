<?php
require_once '../config/database.php';

/**
 * Classe per la gestione degli allegati/documenti
 */
class Allegato {
    private $conn;
    private $table = 'Allegati';
    private $upload_dir = '../uploads/';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
        
        // Crea directory upload se non esiste
        if (!file_exists($this->upload_dir)) {
            mkdir($this->upload_dir, 0755, true);
        }
    }

    /**
     * Upload e inserimento di un nuovo allegato
     */
    public function upload($profilo_id, $file, $tipo, $descrizione = null) {
        try {
            // Validazione file
            $errore = $this->validaFile($file);
            if ($errore) {
                throw new Exception($errore);
            }
            
            // Genera nome file sicuro
            $estensione = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $nome_file = $this->generaNomeFile($profilo_id, $tipo, $estensione);
            $percorso_completo = $this->upload_dir . $nome_file;
            
            // Upload del file
            if (!move_uploaded_file($file['tmp_name'], $percorso_completo)) {
                throw new Exception("Errore durante l'upload del file");
            }
            
            // Inserimento nel database
            $query = "INSERT INTO " . $this->table . " 
                     (profilo_id, tipo, nome_originale, nome_file, percorso, dimensione, mime_type, descrizione) 
                     VALUES (:profilo_id, :tipo, :nome_originale, :nome_file, :percorso, :dimensione, :mime_type, :descrizione)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':profilo_id', $profilo_id);
            $stmt->bindValue(':tipo', $tipo);
            $stmt->bindValue(':nome_originale', $file['name']);
            $stmt->bindValue(':nome_file', $nome_file);
            $stmt->bindValue(':percorso', $percorso_completo);
            $stmt->bindValue(':dimensione', $file['size']);
            $stmt->bindValue(':mime_type', $file['type']);
            $stmt->bindValue(':descrizione', $descrizione);
            
            $stmt->execute();
            return $this->conn->lastInsertId();
            
        } catch(Exception $e) {
            // Rimuovi file se upload db fallisce
            if (isset($percorso_completo) && file_exists($percorso_completo)) {
                unlink($percorso_completo);
            }
            throw $e;
        }
    }

    /**
     * Ottiene tutti gli allegati di un professionista
     */
    public function ottieniPerProfilo($profilo_id, $tipo = null) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE profilo_id = :profilo_id";
            $params = [':profilo_id' => $profilo_id];
            
            if ($tipo) {
                $query .= " AND tipo = :tipo";
                $params[':tipo'] = $tipo;
            }
            
            $query .= " ORDER BY data_upload DESC";
            
            $stmt = $this->conn->prepare($query);
            foreach ($params as $param => $valore) {
                $stmt->bindValue($param, $valore);
            }
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero allegati: " . $e->getMessage());
        }
    }

    /**
     * Ottiene un allegato specifico
     */
    public function ottieniPerId($id, $verifica_profilo = null) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
            if ($verifica_profilo) {
                $query .= " AND profilo_id = :profilo_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            if ($verifica_profilo) {
                $stmt->bindValue(':profilo_id', $verifica_profilo);
            }
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero allegato: " . $e->getMessage());
        }
    }

    /**
     * Elimina un allegato
     */
    public function elimina($id, $verifica_profilo = null) {
        try {
            // Ottieni informazioni allegato
            $allegato = $this->ottieniPerId($id, $verifica_profilo);
            if (!$allegato) {
                throw new Exception("Allegato non trovato");
            }
            
            // Elimina file fisico
            if (file_exists($allegato['percorso'])) {
                unlink($allegato['percorso']);
            }
            
            // Elimina record dal database
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            if ($verifica_profilo) {
                $query .= " AND profilo_id = :profilo_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            if ($verifica_profilo) {
                $stmt->bindValue(':profilo_id', $verifica_profilo);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'eliminazione allegato: " . $e->getMessage());
        }
    }

    /**
     * Aggiorna descrizione allegato
     */
    public function aggiornaDescrizione($id, $descrizione, $verifica_profilo = null) {
        try {
            $query = "UPDATE " . $this->table . " SET descrizione = :descrizione WHERE id = :id";
            if ($verifica_profilo) {
                $query .= " AND profilo_id = :profilo_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':descrizione', $descrizione);
            if ($verifica_profilo) {
                $stmt->bindValue(':profilo_id', $verifica_profilo);
            }
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'aggiornamento allegato: " . $e->getMessage());
        }
    }

    /**
     * Ottiene statistiche sugli allegati
     */
    public function ottieniStatistiche() {
        try {
            $stats = [];
            
            // Per tipo
            $query = "SELECT tipo, COUNT(*) as numero, SUM(dimensione) as dimensione_totale 
                     FROM " . $this->table . " 
                     GROUP BY tipo ORDER BY numero DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['per_tipo'] = $stmt->fetchAll();
            
            // Totale
            $query = "SELECT COUNT(*) as totale, SUM(dimensione) as dimensione_totale FROM " . $this->table;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['totale'] = $stmt->fetch();
            
            return $stats;
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero statistiche allegati: " . $e->getMessage());
        }
    }

    /**
     * Valida un file upload
     */
    private function validaFile($file) {
        $max_size = 5 * 1024 * 1024; // 5MB
        $tipi_consentiti = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'image/gif'
        ];
        $estensioni_consentite = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return "Errore durante l'upload del file";
        }
        
        if ($file['size'] > $max_size) {
            return "File troppo grande. Massimo 5MB consentiti";
        }
        
        $estensione = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($estensione, $estensioni_consentite)) {
            return "Tipo di file non consentito. Consentiti: " . implode(', ', $estensioni_consentite);
        }
        
        if (!in_array($file['type'], $tipi_consentiti)) {
            return "Tipo MIME non consentito";
        }
        
        return null;
    }

    /**
     * Genera nome file sicuro
     */
    private function generaNomeFile($profilo_id, $tipo, $estensione) {
        $timestamp = time();
        $random = substr(md5(uniqid()), 0, 8);
        return "prof_{$profilo_id}_{$tipo}_{$timestamp}_{$random}.{$estensione}";
    }

    /**
     * Ottiene i tipi di allegato consentiti
     */
    public function ottieniTipiAllegato() {
        return [
            'CV' => 'Curriculum Vitae',
            'CERTIFICATO' => 'Certificato Professionale',
            'DIPLOMA' => 'Diploma/Laurea',
            'PATENTE' => 'Patente di Guida',
            'DOCUMENTO' => 'Documento di Identità',
            'PORTFOLIO' => 'Portfolio Lavori',
            'REFERENZE' => 'Lettera di Referenze',
            'ALTRO' => 'Altro Documento'
        ];
    }

    /**
     * Download di un allegato
     */
    public function download($id, $verifica_profilo = null) {
        try {
            $allegato = $this->ottieniPerId($id, $verifica_profilo);
            if (!$allegato) {
                throw new Exception("Allegato non trovato");
            }
            
            if (!file_exists($allegato['percorso'])) {
                throw new Exception("File non trovato sul server");
            }
            
            // Headers per download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $allegato['nome_originale'] . '"');
            header('Content-Length: ' . filesize($allegato['percorso']));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            
            // Output del file
            readfile($allegato['percorso']);
            exit;
            
        } catch(Exception $e) {
            throw $e;
        }
    }

    /**
     * Ottiene la dimensione leggibile
     */
    public function formatDimensione($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
?>