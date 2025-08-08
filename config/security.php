<?php
/**
 * Configurazioni di sicurezza e validazione
 */

class SecurityConfig {
    
    // Configurazioni password
    const PASSWORD_MIN_LENGTH = 8;
    const PASSWORD_REQUIRE_UPPERCASE = false;
    const PASSWORD_REQUIRE_LOWERCASE = false;
    const PASSWORD_REQUIRE_NUMBER = false;
    const PASSWORD_REQUIRE_SPECIAL = false;
    
    // Configurazioni sessione
    const SESSION_TIMEOUT = 3600; // 1 ora in secondi
    const SESSION_REGENERATE_INTERVAL = 300; // 5 minuti
    
    // Configurazioni upload (per sviluppi futuri)
    const UPLOAD_MAX_SIZE = 5242880; // 5MB
    const ALLOWED_FILE_TYPES = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    
    // Rate limiting (per API future)
    const RATE_LIMIT_REQUESTS = 100;
    const RATE_LIMIT_WINDOW = 3600; // 1 ora
    
    /**
     * Valida una password secondo i criteri di sicurezza
     */
    public static function validatePassword($password) {
        $errors = [];
        
        if (strlen($password) < self::PASSWORD_MIN_LENGTH) {
            $errors[] = "La password deve essere di almeno " . self::PASSWORD_MIN_LENGTH . " caratteri";
        }
        
        if (self::PASSWORD_REQUIRE_UPPERCASE && !preg_match('/[A-Z]/', $password)) {
            $errors[] = "La password deve contenere almeno una lettera maiuscola";
        }
        
        if (self::PASSWORD_REQUIRE_LOWERCASE && !preg_match('/[a-z]/', $password)) {
            $errors[] = "La password deve contenere almeno una lettera minuscola";
        }
        
        if (self::PASSWORD_REQUIRE_NUMBER && !preg_match('/[0-9]/', $password)) {
            $errors[] = "La password deve contenere almeno un numero";
        }
        
        if (self::PASSWORD_REQUIRE_SPECIAL && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = "La password deve contenere almeno un carattere speciale";
        }
        
        return empty($errors) ? true : $errors;
    }
    
    /**
     * Sanitizza input utente
     */
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        // Rimuove spazi all'inizio e alla fine
        $input = trim($input);
        
        // Converte caratteri speciali in entità HTML
        $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        
        return $input;
    }
    
    /**
     * Valida codice fiscale italiano
     */
    public static function validateCodiceFiscale($cf) {
        $cf = strtoupper(trim($cf));
        
        if (strlen($cf) !== 16) {
            return false;
        }
        
        // Pattern per codice fiscale italiano
        $pattern = '/^[A-Z]{6}[0-9]{2}[A-Z][0-9]{2}[A-Z][0-9]{3}[A-Z]$/';
        
        if (!preg_match($pattern, $cf)) {
            return false;
        }
        
        // Verifica check digit (implementazione semplificata)
        $odd_chars = 'BAFHJNPRTVCESULDGIMOQKWZYX';
        $even_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $check_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        
        $sum = 0;
        
        for ($i = 0; $i < 15; $i++) {
            $char = $cf[$i];
            if ($i % 2 === 0) { // Posizione dispari (1-based)
                if (is_numeric($char)) {
                    $values = [1, 0, 5, 7, 9, 13, 15, 17, 19, 21];
                    $sum += $values[intval($char)];
                } else {
                    $sum += strpos($odd_chars, $char);
                }
            } else { // Posizione pari (1-based)
                if (is_numeric($char)) {
                    $sum += intval($char);
                } else {
                    $sum += strpos($even_chars, $char);
                }
            }
        }
        
        $check_digit = $check_chars[$sum % 26];
        return $cf[15] === $check_digit;
    }
    
    /**
     * Valida partita IVA italiana
     */
    public static function validatePartitaIVA($piva) {
        $piva = preg_replace('/[^0-9]/', '', $piva);
        
        if (strlen($piva) !== 11) {
            return false;
        }
        
        // Verifica check digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $digit = intval($piva[$i]);
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit = $digit - 9;
                }
            }
            $sum += $digit;
        }
        
        $check_digit = (10 - ($sum % 10)) % 10;
        return intval($piva[10]) === $check_digit;
    }
    
    /**
     * Valida email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Genera token CSRF
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verifica token CSRF
     */
    public static function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Controlla timeout sessione
     */
    public static function checkSessionTimeout() {
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
            return true;
        }
        
        if (time() - $_SESSION['last_activity'] > self::SESSION_TIMEOUT) {
            session_destroy();
            return false;
        }
        
        // Rigenera session ID periodicamente
        if (time() - $_SESSION['last_activity'] > self::SESSION_REGENERATE_INTERVAL) {
            session_regenerate_id(true);
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Log eventi di sicurezza
     */
    public static function logSecurityEvent($event, $details = []) {
        $log_entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'event' => $event,
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'details' => $details
        ];
        
        // In un ambiente di produzione, salvare in un file di log o database
        error_log("SECURITY: " . json_encode($log_entry));
    }
}

// Configurazioni sessione sicure
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.use_strict_mode', 1);
ini_set('session.cookie_samesite', 'Strict');

// Headers di sicurezza (se non gestiti dal web server)
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
}
?>