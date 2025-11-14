# 🔐 Sistema Autenticazione 2FA - ZPeC Professionisti

## Panoramica

Il sistema implementa un'autenticazione a doppio fattore (2FA) basata su TOTP (Time-based One Time Password) compatibile con le app come Google Authenticator o Microsoft Authenticator. L'utente scansiona un QR code, associa l'app e inserisce il codice temporaneo per completare l'accesso.

## 🚀 Installazione

### 1. Aggiornare la Tabella `utenti_admin`

Il login del backend utilizza esclusivamente gli amministratori presenti in `utenti_admin`. Aggiungi i campi necessari al TOTP (se non l’hai già fatto):

```sql
ALTER TABLE utenti_admin 
    ADD COLUMN totp_secret VARCHAR(64) NULL AFTER ruolo,
    ADD COLUMN totp_enabled TINYINT(1) NOT NULL DEFAULT 0 AFTER totp_secret;
```

### 2. Test del Sistema

Esegui lo script di test per verificare il funzionamento:

```
http://localhost/professionisti/utils/test_2fa.php
```

## 📋 Funzionalità

### 🔑 Generazione codici TOTP
- Segreto Base32 generato per ogni utente
- QR code provisioning compatibile con tutte le app Authenticator
- Codici di 6 cifre con periodo di 30 secondi e tolleranza ±1 intervallo

### 🛡️ Sicurezza
- Log di tutti i tentativi di accesso in `auth_logs`
- Conferma iniziale del dispositivo tramite codice
- Sessione applicativa con timeout (8 ore)

### 📊 Logging
- Registrazione IP e User Agent
- Tracciamento azioni utente (`logAction`)
- Storico accessi, logout e tentativi errati

## 🔧 Utilizzo

### Per gli Utenti

1. **Primo accesso (setup):**
   - Vai a `http://localhost/professionisti/backend/login_2fa.php`
   - Inserisci l’email di un amministratore attivo (`utenti_admin.attivo = 1`)
   - Scansiona il QR code con l'app Authenticator
   - Inserisci il codice generato per confermare e attivare il dispositivo

2. **Accessi successivi:**
   - Inserisci la stessa email amministrativa
   - L'app genera automaticamente il codice da digitare nel form
   - Il login avviene subito dopo la verifica

### Per gli Amministratori

1. **Protezione Pagine:**
   ```php
   require_once '../includes/auth_middleware.php';
   requireAuth(); // Richiede autenticazione
   ```

2. **Logging Azioni:**
   ```php
   logAction('AZIONE', 'Dettagli azione');
   ```

3. **Informazioni Utente:**
   ```php
   $user = getCurrentUser();
   echo $user['name']; // Nome utente
   echo $user['email']; // Email utente
   ```

## 🗄️ Struttura Database

### Tabella `utenti_admin` (estratto TOTP)
```sql
ALTER TABLE utenti_admin 
    ADD COLUMN totp_secret VARCHAR(64) NULL,
    ADD COLUMN totp_enabled TINYINT(1) NOT NULL DEFAULT 0;
```

### Tabella `auth_logs`
```sql
CREATE TABLE auth_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    action ENUM('LOGIN_ATTEMPT', 'TOKEN_SENT', 'TOKEN_VERIFIED', 'LOGIN_SUCCESS', 'LOGIN_FAILED', 'LOGOUT') NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🔒 Configurazioni di Sicurezza

- Periodo TOTP: 30 secondi
- Numero cifre: 6
- Finestra di validazione: ±1 step (tolleranza 30s)
- Session lifetime: 8 ore (estesa on activity)

## 🚨 Troubleshooting

### Email non inviate
1. Verifica configurazione SMTP in `php.ini`
2. Controlla log errori PHP
3. Testa con script di test

### Codici non validi
1. Verifica orario di server e dispositivo (devono essere sincronizzati)
2. Controlla che il segreto (`totp_secret`) corrisponda a quello importato nell'app
3. Se necessario reimposta il segreto cancellando `totp_secret`/`totp_enabled` per l'utente e rifacendo il setup

### Errori di sessione
1. Verifica permessi cartella sessioni
2. Controlla configurazione PHP session
3. Verifica timeout sessione

## 📈 Monitoraggio

### Query Utili

**Amministratori con TOTP attivo:**
```sql
SELECT id, username, email, ruolo 
FROM utenti_admin 
WHERE totp_enabled = 1
ORDER BY id DESC;
```

**Tentativi di Accesso:**
```sql
SELECT email, action, created_at, ip_address 
FROM auth_logs 
WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
ORDER BY created_at DESC;
```

**Statistiche Accessi:**
```sql
SELECT action, COUNT(*) as count 
FROM auth_logs 
WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
GROUP BY action;
```

## 🔄 Manutenzione

### Pulizia Log
```sql
DELETE FROM auth_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Backup
Includi le tabelle di autenticazione nel backup:

```bash
mysqldump -u root -p ZPeC utenti_admin auth_logs > auth_backup.sql
```

## 📞 Supporto

Per problemi o domande:
- Controlla i log PHP e MySQL
- Esegui lo script di test
- Verifica configurazioni email
- Controlla permessi file e cartelle

---

**⚠️ Importante:** Questo sistema sostituisce il vecchio sistema di login. Assicurati di aggiornare tutti i link che puntavano al vecchio `login.php`.
