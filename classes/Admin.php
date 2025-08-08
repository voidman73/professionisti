<?php
require_once '../config/database.php';

class Admin {
    private $conn;
    private $table = 'utenti_admin';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    /**
     * Autentica un utente amministratore
     */
    public function autenticaUtente($username, $password) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                     WHERE username = :username AND attivo = 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':username', $username);
            $stmt->execute();
            
            $utente = $stmt->fetch();
            
            if ($utente && password_verify($password, $utente['password_hash'])) {
                return $utente;
            }
            
            return false;
        } catch(PDOException $e) {
            throw new Exception("Errore nell'autenticazione: " . $e->getMessage());
        }
    }

    /**
     * Crea un nuovo utente amministratore
     */
    public function creaUtente($dati) {
        try {
            $password_hash = password_hash($dati['password'], PASSWORD_DEFAULT);
            
            $query = "INSERT INTO " . $this->table . " 
                     (username, password_hash, email, nome, cognome, ruolo) 
                     VALUES (:username, :password_hash, :email, :nome, :cognome, :ruolo)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':username', $dati['username']);
            $stmt->bindValue(':password_hash', $password_hash);
            $stmt->bindValue(':email', $dati['email']);
            $stmt->bindValue(':nome', $dati['nome']);
            $stmt->bindValue(':cognome', $dati['cognome']);
            $stmt->bindValue(':ruolo', $dati['ruolo']);
            
            if($stmt->execute()) {
                return $this->conn->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            throw new Exception("Errore nella creazione utente: " . $e->getMessage());
        }
    }

    /**
     * Aggiorna ultimo accesso
     */
    public function aggiornaUltimoAccesso($id) {
        try {
            $query = "UPDATE " . $this->table . " SET ultimo_accesso = NOW() WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    /**
     * Ottiene tutti gli utenti amministratori
     */
    public function ottieniTutti() {
        try {
            $query = "SELECT id, username, email, nome, cognome, ruolo, attivo, ultimo_accesso, data_creazione 
                     FROM " . $this->table . " ORDER BY data_creazione DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero utenti: " . $e->getMessage());
        }
    }

    /**
     * Ottiene un utente per ID
     */
    public function ottieniPerId($id) {
        try {
            $query = "SELECT id, username, email, nome, cognome, ruolo, attivo, ultimo_accesso, data_creazione 
                     FROM " . $this->table . " WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch(PDOException $e) {
            throw new Exception("Errore nel recupero utente: " . $e->getMessage());
        }
    }

    /**
     * Modifica password utente
     */
    public function modificaPassword($id, $nuova_password) {
        try {
            $password_hash = password_hash($nuova_password, PASSWORD_DEFAULT);
            
            $query = "UPDATE " . $this->table . " SET password_hash = :password_hash WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            $stmt->bindValue(':password_hash', $password_hash);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nella modifica password: " . $e->getMessage());
        }
    }

    /**
     * Attiva/Disattiva un utente
     */
    public function toggleAttivo($id) {
        try {
            $query = "UPDATE " . $this->table . " SET attivo = NOT attivo WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':id', $id);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            throw new Exception("Errore nell'aggiornamento utente: " . $e->getMessage());
        }
    }

    /**
     * Verifica se username o email esistono già
     */
    public function verificaUnivocita($username, $email, $escludiId = null) {
        try {
            $query = "SELECT id FROM " . $this->table . " WHERE username = :username OR email = :email";
            $params = ['username' => $username, 'email' => $email];

            if ($escludiId) {
                $query .= " AND id != :escludi_id";
                $params['escludi_id'] = $escludiId;
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
}

// Crea utente admin di default se non esiste
function creaAdminDefault() {
    try {
        $admin = new Admin();
        
        // Verifica se esiste già un amministratore
        $utenti = $admin->ottieniTutti();
        if (empty($utenti)) {
            $dati = [
                'username' => 'admin',
                'password' => 'admin123',
                'email' => 'admin@professionisti.local',
                'nome' => 'Amministratore',
                'cognome' => 'Sistema',
                'ruolo' => 'ADMIN'
            ];
            
            $admin->creaUtente($dati);
        }
    } catch (Exception $e) {
        // Ignora errori
    }
}

// Esegui la creazione dell'admin di default
creaAdminDefault();
?>