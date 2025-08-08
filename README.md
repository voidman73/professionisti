# Sistema di Iscrizione Liberi Professionisti ZPeC

## Descrizione

Applicazione web PHP/MySQL per la gestione dell'iscrizione di liberi professionisti agli albi professionali. 
Il sistema permette ai professionisti di registrarsi attraverso un form online e agli amministratori di gestire le iscrizioni tramite un pannello di controllo avanzato.

**Migrato da ASP.NET/MSSQL Server a PHP/MySQL mantenendo la struttura originale del database ZPeC.**

## Caratteristiche

### Front-End (Professionisti)
- 🎨 Interfaccia moderna e responsive con Bootstrap 5
- 📝 Form di registrazione multi-step con validazione
- 📋 Gestione iscrizioni agli albi professionali
- 📱 Ottimizzato per dispositivi mobili
- 📄 Upload CV e documenti

### Back-End (Amministrazione)
- 🔐 Sistema di autenticazione sicuro
- 📊 Dashboard con statistiche in tempo reale
- 🔍 Ricerca avanzata con filtri multipli
- 📋 Gestione stati professionisti (Approvato, Respinto, Sospeso)
- 🏛️ Gestione albi professionali e iscrizioni
- 📤 Esportazione dati in formato CSV
- 📄 Visualizzazione dettagliata profili professionisti

### Database ZPeC
- 🗃️ Schema MySQL fedele alla struttura originale SQL Server
- 📈 Supporto completo per albi professionali
- 🎓 Gestione istruzione e formazione
- 💼 Esperienze lavorative (autonome e subordinate)
- 🌐 Competenze linguistiche e informatiche
- 📎 Gestione allegati e documenti
- 🔒 Validazione e integrità dei dati

## Requisiti di Sistema

- **PHP**: 8.0 o superiore
- **MySQL**: 5.7 o superiore (o MariaDB 10.2+)
- **Web Server**: Apache 2.4+ o Nginx 1.18+
- **Estensioni PHP necessarie**:
  - PDO MySQL
  - mbstring
  - openssl
  - json

## Installazione

### 1. Download e Setup

```bash
# Clona o scarica il progetto nella directory web del server
cd /xampp/htdocs  # o /var/www/html per server Linux
git clone [repository-url] professionisti
cd professionisti
```

### 2. Configurazione Database

```sql
-- Crea il database ZPeC
CREATE DATABASE ZPeC CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Crea un utente dedicato (opzionale ma consigliato)
CREATE USER 'zpec_user'@'localhost' IDENTIFIED BY 'password_sicura';
GRANT ALL PRIVILEGES ON ZPeC.* TO 'zpec_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Importa Schema Database

```bash
# Importa lo schema del database
mysql -u root -p ZPeC < database/schema.sql
```

### 4. Configurazione Applicazione

Modifica il file `config/database.php` con le tue credenziali:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ZPeC');
define('DB_USER', 'zpec_user');  // o 'root'
define('DB_PASS', 'password_sicura');      // la tua password
```

### 5. Configurazione Web Server

#### Apache (.htaccess)
```apache
# Nel root del progetto
RewriteEngine On
RewriteRule ^admin/?$ backend/dashboard.php [L]
RewriteRule ^login/?$ backend/login.php [L]

# Sicurezza
<Files "config/*">
    Require all denied
</Files>
```

## Configurazione e Primo Accesso (Localhost)

### 1. Utente Amministratore Default

L'applicazione crea automaticamente un utente amministratore:

- **Username**: `admin`
- **Password**: `admin123`

⚠️ **IMPORTANTE**: Cambia immediatamente la password dopo il primo accesso!

### 2. Accesso al Pannello Amministrativo

Vai a: `http://localhost/professionisti/backend/login.php`

### 3. Accesso Public (Registrazione)

Vai a: `http://localhost/professionisti/frontend/index.php`

### 4. Demo Nuova Colorazione 

Vai a: `http://localhost/professionisti/demo_colori.php`

> **📍 Nota**: Tutti i link sono configurati per **localhost**. Se usi XAMPP, assicurati che Apache sia avviato.

## Struttura del Database ZPeC

```
ZPeC/
├── 📋 Profili (tabella principale professionisti)
├── 🏛️ Albo (albi professionali)
├── 📜 AlboProfili (iscrizioni agli albi)
├── 🎓 Istruzione (formazione e titoli)
├── 💼 LavoroAut (esperienze lavoro autonomo)
├── 🏢 LavoroSub (esperienze lavoro subordinato)
├── 🌐 Lingue (competenze linguistiche)
├── 💻 IT (competenze informatiche)
├── 📎 Allegati (documenti e CV)
├── 💬 Commenti (note e valutazioni)
├── 🔍 Ricerche (ricerche salvate)
├── 📊 Logs (log operazioni)
└── 🛡️ utenti_admin (amministratori)
```

## Struttura del Progetto

```
professionisti/
├── 📄 README.md (documentazione completa)
├── 📄 .htaccess (configurazione sicurezza)
├── 🔧 config/
│   ├── database.php (connessione MySQL)
│   └── security.php (validazioni e sicurezza)
├── 🗃️ database/
│   └── schema.sql (schema MySQL ZPeC)
├── ⚙️ classes/
│   ├── Admin.php (gestione amministratori)
│   ├── Professionista.php (gestione profili)
│   └── Albo.php (gestione albi professionali)
├── 🌐 frontend/
│   ├── index.php (homepage pubblica)
│   └── registrazione.php (form multi-step)
├── 🛠️ backend/
│   ├── login.php (accesso amministratori)
│   ├── dashboard.php (pannello principale)
│   ├── dettagli_professionista.php
│   ├── gestisci_stato.php
│   ├── esporta.php (export CSV)
│   └── logout.php
├── 🔌 api/
│   └── index.php (API REST base)
└── 🛠️ utils/
    └── install.php (script verifica installazione)
```

## Utilizzo

### Per i Professionisti

1. **Registrazione**: Accedi alla homepage e clicca "Iscriviti Ora"
2. **Compilazione**: Compila il form multi-step con tutti i dati richiesti
3. **Iscrizione Albo**: Specifica gli albi professionali di appartenenza
4. **Invio**: Dopo l'invio, il profilo sarà in stato "PENDENTE"
5. **Approvazione**: Riceverai una notifica quando il profilo sarà approvato

### Per gli Amministratori

1. **Login**: Accedi al pannello amministrativo
2. **Dashboard**: Visualizza statistiche e professionisti registrati
3. **Ricerca**: Usa i filtri per trovare professionisti specifici
4. **Gestione**: Approva, respingi o sospendi i profili
5. **Albi**: Gestisci gli albi professionali e le iscrizioni
6. **Esportazione**: Esporta i dati in formato CSV

## Dati Precaricati

Il sistema include già:
- **10 albi professionali** (Ingegneri, Architetti, Avvocati, ecc.)
- **15 province italiane** principali
- **10 paesi** con codici ISO
- **24 competenze IT** suddivise per categoria
- **10 lingue** principali
- **Utente admin default** per accesso immediato

## Sicurezza

### Misure Implementate

- ✅ Password hash con `password_hash()` PHP
- ✅ Prepared statements PDO (protezione SQL injection)
- ✅ Validazione e sanitizzazione input
- ✅ Controllo autenticazione su pagine admin
- ✅ Protezione directory sensibili
- ✅ Validazione email e codice fiscale univoci
- ✅ Log di sicurezza per eventi critici

### Raccomandazioni Aggiuntive

1. **HTTPS**: Usa sempre HTTPS in produzione
2. **Password**: Cambia la password admin default
3. **Backup**: Configura backup automatici del database
4. **Aggiornamenti**: Mantieni PHP e MySQL aggiornati
5. **Firewall**: Configura un firewall web application

## Personalizzazione

### Aggiungere Nuovi Albi

```sql
INSERT INTO Albo (codice, nome, descrizione, sito_web) 
VALUES ('NUOVO', 'Nuovo Albo Professionale', 'Descrizione albo', 'https://esempio.it');
```

### Aggiungere Competenze IT

```sql
INSERT INTO ITElenco (categoria, nome, descrizione) 
VALUES ('Categoria', 'Nuova Competenza', 'Descrizione competenza');
```

### Modificare i Colori

Modifica le variabili CSS nei file frontend per cambiare lo schema colori.

## API (Sviluppo Futuro)

L'applicazione include API REST base:

- `GET /api/status` - Stato del servizio
- `GET /api/albi` - Lista albi professionali
- `GET /api/professionisti` - Lista professionisti (filtrata)
- `POST /api/professionisti` - Registrazione professionista

## Troubleshooting

### Problemi Comuni

#### "Errore di connessione al database"
- Verifica credenziali in `config/database.php`
- Controlla che MySQL sia in esecuzione
- Verifica che il database ZPeC esista

#### "Tabella non trovata"
- Assicurati di aver importato il database schema.sql
- Verifica che il database ZPeC sia selezionato

#### "Pagina non trovata"
- Controlla che mod_rewrite sia abilitato (Apache)
- Verifica la configurazione del web server
- Controlla i permessi delle directory

### Log degli Errori

Abilita i log PHP per debugging:

```php
// In development, aggiungi in config/database.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

## Migrazione da Sistema Originale

### Differenze Principali

**Database**:
- SQL Server → MySQL
- Struttura tabelle mantenuta (ZPeC)
- Tipi di dati adattati per MySQL

**Applicazione**:
- ASP.NET → PHP 8+
- Interfaccia modernizzata con Bootstrap 5
- API REST aggiunte

**Sicurezza**:
- Miglioramenti nelle validazioni
- Headers di sicurezza implementati
- Sistema di logging potenziato

## Supporto e Contributi

Per supporto o segnalazione bug:

1. Verifica la documentazione
2. Controlla i log degli errori
3. Crea un issue dettagliato con:
   - Versione PHP/MySQL
   - Steps per riprodurre l'errore
   - Log degli errori

## Licenza

Questo progetto è rilasciato sotto licenza MIT.

## Changelog

### v2.0.0 (2024) - Migrazione ZPeC
- ✅ Migrazione completa da ASP.NET/MSSQL a PHP/MySQL
- ✅ Struttura database ZPeC fedele all'originale
- ✅ Gestione albi professionali avanzata
- ✅ Sistema di competenze e esperienze
- ✅ Upload documenti e allegati
- ✅ API REST base implementate
- ✅ Sicurezza e validazioni migliorate

### Sviluppi Futuri
- 🔄 API REST complete
- 📧 Sistema notifiche email
- 📁 Gestione avanzata documenti
- 🔍 Ricerca full-text
- 📊 Reporting avanzato con grafici
- 🌐 Interfaccia multilingua
- 📱 App mobile companion

## 🎨 Tema e Colorazione

### Colore Principale: Rosso Scuro
Il sistema utilizza un **tema rosso scuro** come colore principale:

- **Primario**: `#8B0000` (Rosso scuro)
- **Chiaro**: `#A52A2A` (Rosso mattone)  
- **Scuro**: `#660000` (Rosso molto scuro)
- **Hover**: `#700000` (Rosso hover)
- **Accent**: `#DC143C` (Rosso brillante)

### CSS Personalizzato
Il file `assets/style.css` sovrascrive i colori Bootstrap predefiniti:
- Tutti i componenti `btn-primary`, `bg-primary`, `text-primary` usano il rosso scuro
- Gradients personalizzati per sidebar, login, hero sections
- Animazioni e effetti hover con il tema rosso

### Demo Colorazione
Visita `demo_colori.php` per vedere tutti i componenti con la nuova colorazione.

## 🧪 Test e Risoluzione Problemi

### Test Rapido Sistema
Dopo l'installazione, esegui questi test:

1. **Test Completo**: `http://localhost/professionisti/utils/test_sistema.php`
2. **Verifica Installazione**: `http://localhost/professionisti/utils/install.php`
3. **Correzione Password Admin**: `http://localhost/professionisti/utils/fix_admin_password.php`

### Problemi Comuni e Soluzioni

#### Password Admin Non Funziona
```bash
# Vai a questo URL per correggere la password
http://localhost/professionisti/utils/fix_admin_password.php
```

#### Errori Tabelle Database
```sql
-- Assicurati che il database sia importato correttamente
mysql -u root -p ZPeC < database/schema.sql
```

#### Test API
```bash
# Testa le API REST
http://localhost/professionisti/api/index.php?status
```

#### Demo Nuova Colorazione
```bash
# Visualizza tutti i componenti con tema rosso scuro
http://localhost/professionisti/demo_colori.php
```