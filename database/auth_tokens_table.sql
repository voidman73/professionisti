-- Tabella per gestione token autenticazione 2FA
USE ZPeC;

CREATE TABLE IF NOT EXISTS auth_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(6) NOT NULL,
    expiry TIMESTAMP NOT NULL,
    used TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL,
    
    INDEX idx_email (email),
    INDEX idx_token (token),
    INDEX idx_expiry (expiry),
    INDEX idx_used (used)
);

-- Tabella per log tentativi di accesso
CREATE TABLE IF NOT EXISTS auth_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    action ENUM('LOGIN_ATTEMPT', 'TOKEN_SENT', 'TOKEN_VERIFIED', 'LOGIN_SUCCESS', 'LOGIN_FAILED', 'LOGOUT') NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Tabella per configurazioni 2FA
CREATE TABLE IF NOT EXISTS auth_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(50) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserisci configurazioni di default
INSERT INTO auth_settings (setting_key, setting_value, description) VALUES
('2fa_enabled', '1', 'Abilita autenticazione a doppio fattore'),
('token_expiry_minutes', '5', 'Durata token in minuti'),
('max_login_attempts', '5', 'Numero massimo tentativi di login'),
('lockout_duration_minutes', '15', 'Durata blocco account in minuti'),
('email_from', 'noreply@zpec.it', 'Email mittente per token'),
('email_reply_to', 'supporto@zpec.it', 'Email di risposta per supporto');
