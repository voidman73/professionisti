<?php
/**
 * Classe per gestione Autenticazione a Doppio Fattore (2FA)
 * Sistema di sicurezza con token inviato via email
 */

class Auth2FA {
    private $db;
    private $token_expiry = 300; // 5 minuti in secondi
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Genera un token casuale di 6 cifre
     */
    public function generateToken() {
        return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Salva il token nel database con timestamp
     */
    public function saveToken($email, $token) {
        $expiry = date('Y-m-d H:i:s', time() + $this->token_expiry);
        
        // Rimuovi token precedenti per questa email
        $stmt = $this->db->prepare("DELETE FROM auth_tokens WHERE email = ?");
        $stmt->execute([$email]);
        
        // Inserisci nuovo token
        $stmt = $this->db->prepare("
            INSERT INTO auth_tokens (email, token, expiry, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$email, $token, $expiry]);
    }
    
    /**
     * Verifica se il token è valido
     */
    public function verifyToken($email, $token) {
        $stmt = $this->db->prepare("
            SELECT * FROM auth_tokens 
            WHERE email = ? AND token = ? AND expiry > NOW() 
            AND used = 0
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$email, $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            // Marca il token come utilizzato
            $this->markTokenAsUsed($result['id']);
            return true;
        }
        
        return false;
    }
    
    /**
     * Marca il token come utilizzato
     */
    private function markTokenAsUsed($token_id) {
        $stmt = $this->db->prepare("UPDATE auth_tokens SET used = 1 WHERE id = ?");
        $stmt->execute([$token_id]);
    }
    
    /**
     * Invia email con token
     */
    public function sendTokenEmail($email, $token, $nome = '') {
        $subject = "Codice di Verifica - Sistema Professionisti ZPeC";
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f8f9fa; }
                .token { font-size: 32px; font-weight: bold; text-align: center; 
                         background: #28a745; color: white; padding: 20px; margin: 20px 0; 
                         border-radius: 5px; letter-spacing: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; 
                          border-radius: 5px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Verifica Account</h1>
                    <p>Sistema Professionisti ZPeC</p>
                </div>
                
                <div class='content'>
                    <p>Ciao " . htmlspecialchars($nome ?: 'Utente') . ",</p>
                    
                    <p>Abbiamo ricevuto una richiesta di accesso al tuo account. 
                    Per completare l'accesso, inserisci il seguente codice di verifica:</p>
                    
                    <div class='token'>" . $token . "</div>
                    
                    <p><strong>Questo codice scadrà tra 5 minuti.</strong></p>
                    
                    <div class='warning'>
                        <strong>⚠️ Sicurezza:</strong><br>
                        • Non condividere mai questo codice con nessuno<br>
                        • Il nostro staff non ti chiederà mai questo codice<br>
                        • Se non hai richiesto tu questo accesso, ignora questa email
                    </div>
                    
                    <p>Se hai problemi con l'accesso, contatta il supporto tecnico.</p>
                </div>
                
                <div class='footer'>
                    <p>© " . date('Y') . " Sistema Professionisti ZPeC - Tutti i diritti riservati</p>
                    <p>Questa email è stata inviata automaticamente, non rispondere a questo messaggio.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: Sistema ZPeC <noreply@zpec.it>',
            'Reply-To: supporto@zpec.it',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        return mail($email, $subject, $message, implode("\r\n", $headers));
    }
    
    /**
     * Pulisce i token scaduti
     */
    public function cleanupExpiredTokens() {
        $stmt = $this->db->prepare("DELETE FROM auth_tokens WHERE expiry < NOW()");
        return $stmt->execute();
    }
    
    /**
     * Controlla se l'utente ha già un token valido
     */
    public function hasValidToken($email) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM auth_tokens 
            WHERE email = ? AND expiry > NOW() AND used = 0
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }
    
    /**
     * Ottieni il tempo rimanente per il token
     */
    public function getTokenTimeRemaining($email) {
        $stmt = $this->db->prepare("
            SELECT TIMESTAMPDIFF(SECOND, NOW(), expiry) as remaining 
            FROM auth_tokens 
            WHERE email = ? AND expiry > NOW() AND used = 0
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? max(0, $result['remaining']) : 0;
    }
}
?>
