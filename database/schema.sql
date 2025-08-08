-- Database per Sistema Iscrizione Liberi Professionisti ZPeC
-- Migrazione da SQL Server a MySQL
-- Struttura basata su PROF_STRUCTURE.sql

CREATE DATABASE IF NOT EXISTS ZPeC CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE ZPeC;

-- Tabella Province italiane
CREATE TABLE Province (
    Sigla CHAR(2) PRIMARY KEY,
    Provincia VARCHAR(50) NOT NULL,
    Regione VARCHAR(50) NOT NULL
);

-- Tabella per i codici ISO dei paesi
CREATE TABLE ulkISO3166 (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codice_alpha2 CHAR(2) UNIQUE NOT NULL,
    codice_alpha3 CHAR(3) UNIQUE NOT NULL,
    nome_it VARCHAR(100) NOT NULL,
    nome_en VARCHAR(100) NOT NULL
);

-- Tabella principale Albi Professionali
CREATE TABLE Albo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codice VARCHAR(10) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descrizione TEXT,
    sito_web VARCHAR(200),
    attivo BOOLEAN DEFAULT TRUE,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabella Elenco Lingue
CREATE TABLE LingueElenco (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codice VARCHAR(5) UNIQUE NOT NULL,
    nome_it VARCHAR(50) NOT NULL,
    nome_en VARCHAR(50) NOT NULL,
    attiva BOOLEAN DEFAULT TRUE
);

-- Tabella Elenco Competenze IT
CREATE TABLE ITElenco (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(50) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descrizione TEXT,
    livello_max INT DEFAULT 5,
    attiva BOOLEAN DEFAULT TRUE
);

-- Tabella principale PROFILI (Professionisti)
CREATE TABLE Profili (
    id INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Dati anagrafici
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    codice_fiscale VARCHAR(16) UNIQUE NOT NULL,
    partita_iva VARCHAR(11) UNIQUE,
    data_nascita DATE,
    luogo_nascita VARCHAR(100),
    provincia_nascita CHAR(2),
    sesso ENUM('M', 'F', 'ND') DEFAULT 'ND',
    
    -- Contatti
    email VARCHAR(100) UNIQUE NOT NULL,
    email_pec VARCHAR(100),
    telefono VARCHAR(20),
    cellulare VARCHAR(20),
    sito_web VARCHAR(200),
    
    -- Residenza
    indirizzo_residenza VARCHAR(200),
    citta_residenza VARCHAR(100),
    provincia_residenza CHAR(2),
    cap_residenza VARCHAR(5),
    nazione_residenza VARCHAR(3) DEFAULT 'ITA',
    
    -- Domicilio (se diverso)
    indirizzo_domicilio VARCHAR(200),
    citta_domicilio VARCHAR(100), 
    provincia_domicilio CHAR(2),
    cap_domicilio VARCHAR(5),
    nazione_domicilio VARCHAR(3),
    
    -- Stato professionale
    disponibile BOOLEAN DEFAULT TRUE,
    disponibilita_trasferte BOOLEAN DEFAULT FALSE,
    raggio_azione_km INT DEFAULT 50,
    
    -- Tariffe
    tariffa_oraria_min DECIMAL(8,2),
    tariffa_oraria_max DECIMAL(8,2),
    tariffa_giornaliera_min DECIMAL(8,2),
    tariffa_giornaliera_max DECIMAL(8,2),
    valuta VARCHAR(3) DEFAULT 'EUR',
    note_tariffe TEXT,
    
    -- Presentazione
    titolo_professionale VARCHAR(200),
    descrizione_breve VARCHAR(500),
    presentazione TEXT,
    competenze_principali TEXT,
    settori_esperienza TEXT,
    
    -- Metadati
    stato ENUM('PENDENTE', 'APPROVATO', 'RESPINTO', 'SOSPESO', 'INCOMPLETO') DEFAULT 'PENDENTE',
    privacy_accettata BOOLEAN DEFAULT FALSE,
    termini_accettati BOOLEAN DEFAULT FALSE,
    marketing_accettato BOOLEAN DEFAULT FALSE,
    data_registrazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_ultima_modifica TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    data_ultima_attivita TIMESTAMP NULL,
    note_amministrative TEXT,
    
    FOREIGN KEY (provincia_nascita) REFERENCES Province(Sigla),
    FOREIGN KEY (provincia_residenza) REFERENCES Province(Sigla),
    FOREIGN KEY (provincia_domicilio) REFERENCES Province(Sigla),
    FOREIGN KEY (nazione_residenza) REFERENCES ulkISO3166(codice_alpha3),
    FOREIGN KEY (nazione_domicilio) REFERENCES ulkISO3166(codice_alpha3)
);

-- Tabella Relazione Profili-Albi
CREATE TABLE AlboProfili (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT NOT NULL,
    albo_id INT NOT NULL,
    numero_iscrizione VARCHAR(50) NOT NULL,
    data_iscrizione DATE,
    data_scadenza DATE,
    attiva BOOLEAN DEFAULT TRUE,
    note TEXT,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_profilo_albo (profilo_id, albo_id),
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE CASCADE,
    FOREIGN KEY (albo_id) REFERENCES Albo(id) ON DELETE CASCADE
);

-- Tabella Istruzione e Formazione
CREATE TABLE Istruzione (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT NOT NULL,
    tipo ENUM('LAUREA', 'DIPLOMA', 'MASTER', 'DOTTORATO', 'CORSO', 'CERTIFICAZIONE') NOT NULL,
    titolo VARCHAR(200) NOT NULL,
    istituto VARCHAR(200),
    citta VARCHAR(100),
    provincia CHAR(2),
    nazione VARCHAR(3) DEFAULT 'ITA',
    data_conseguimento DATE,
    voto VARCHAR(20),
    descrizione TEXT,
    durata_ore INT,
    crediti_formativi INT,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE CASCADE,
    FOREIGN KEY (provincia) REFERENCES Province(Sigla),
    FOREIGN KEY (nazione) REFERENCES ulkISO3166(codice_alpha3)
);

-- Tabella Esperienze Lavoro Autonomo
CREATE TABLE LavoroAut (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT NOT NULL,
    cliente VARCHAR(200) NOT NULL,
    descrizione_attivita TEXT NOT NULL,
    settore VARCHAR(100),
    ruolo VARCHAR(100),
    citta VARCHAR(100),
    provincia CHAR(2),
    nazione VARCHAR(3) DEFAULT 'ITA',
    data_inizio DATE NOT NULL,
    data_fine DATE,
    in_corso BOOLEAN DEFAULT FALSE,
    importo_progetto DECIMAL(10,2),
    valuta VARCHAR(3) DEFAULT 'EUR',
    competenze_utilizzate TEXT,
    risultati_ottenuti TEXT,
    referente_nome VARCHAR(100),
    referente_email VARCHAR(100),
    referente_telefono VARCHAR(20),
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE CASCADE,
    FOREIGN KEY (provincia) REFERENCES Province(Sigla),
    FOREIGN KEY (nazione) REFERENCES ulkISO3166(codice_alpha3)
);

-- Tabella Esperienze Lavoro Subordinato
CREATE TABLE LavoroSub (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT NOT NULL,
    azienda VARCHAR(200) NOT NULL,
    settore VARCHAR(100),
    ruolo VARCHAR(100) NOT NULL,
    descrizione_attivita TEXT,
    tipo_contratto ENUM('INDETERMINATO', 'DETERMINATO', 'STAGE', 'CONSULENZA', 'PARTTIME', 'ALTRO'),
    citta VARCHAR(100),
    provincia CHAR(2),
    nazione VARCHAR(3) DEFAULT 'ITA',
    data_inizio DATE NOT NULL,
    data_fine DATE,
    in_corso BOOLEAN DEFAULT FALSE,
    retribuzione_annua DECIMAL(10,2),
    valuta VARCHAR(3) DEFAULT 'EUR',
    competenze_acquisite TEXT,
    responsabilita TEXT,
    risultati_ottenuti TEXT,
    referente_nome VARCHAR(100),
    referente_email VARCHAR(100),
    referente_telefono VARCHAR(20),
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE CASCADE,
    FOREIGN KEY (provincia) REFERENCES Province(Sigla),
    FOREIGN KEY (nazione) REFERENCES ulkISO3166(codice_alpha3)
);

-- Tabella Competenze Linguistiche
CREATE TABLE Lingue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT NOT NULL,
    lingua_id INT NOT NULL,
    livello_parlato ENUM('BASE', 'INTERMEDIO', 'AVANZATO', 'MADRELINGUA') NOT NULL,
    livello_scritto ENUM('BASE', 'INTERMEDIO', 'AVANZATO', 'MADRELINGUA') NOT NULL,
    livello_comprensione ENUM('BASE', 'INTERMEDIO', 'AVANZATO', 'MADRELINGUA') NOT NULL,
    certificazioni VARCHAR(200),
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_profilo_lingua (profilo_id, lingua_id),
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE CASCADE,
    FOREIGN KEY (lingua_id) REFERENCES LingueElenco(id)
);

-- Tabella Competenze Informatiche
CREATE TABLE IT (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT NOT NULL,
    competenza_id INT NOT NULL,
    livello INT NOT NULL DEFAULT 1,
    anni_esperienza INT DEFAULT 0,
    certificazioni VARCHAR(200),
    note TEXT,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    UNIQUE KEY unique_profilo_competenza (profilo_id, competenza_id),
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE CASCADE,
    FOREIGN KEY (competenza_id) REFERENCES ITElenco(id),
    CHECK (livello >= 1 AND livello <= 5)
);

-- Tabella Allegati e Documenti
CREATE TABLE Allegati (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT NOT NULL,
    tipo ENUM('CV', 'PORTFOLIO', 'CERTIFICATO', 'DOCUMENTO_IDENTITA', 'ALTRO') NOT NULL,
    nome_file VARCHAR(255) NOT NULL,
    nome_originale VARCHAR(255) NOT NULL,
    path_file VARCHAR(500) NOT NULL,
    dimensione_kb INT NOT NULL,
    mime_type VARCHAR(100),
    descrizione TEXT,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE CASCADE
);

-- Tabella Commenti e Note
CREATE TABLE Commenti (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT NOT NULL,
    autore_tipo ENUM('PROFESSIONISTA', 'AMMINISTRATORE', 'SISTEMA') NOT NULL,
    autore_id INT,
    titolo VARCHAR(200),
    testo TEXT NOT NULL,
    tipo ENUM('NOTA', 'VALUTAZIONE', 'COMUNICAZIONE', 'ALERT') DEFAULT 'NOTA',
    visibile_a_professionista BOOLEAN DEFAULT FALSE,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE CASCADE
);

-- Tabella Ricerche salvate
CREATE TABLE Ricerche (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome_ricerca VARCHAR(100) NOT NULL,
    parametri_ricerca JSON NOT NULL,
    descrizione TEXT,
    pubblica BOOLEAN DEFAULT FALSE,
    creata_da_admin BOOLEAN DEFAULT TRUE,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    data_ultima_esecuzione TIMESTAMP NULL,
    numero_esecuzioni INT DEFAULT 0
);

-- Tabella Log operazioni professionisti
CREATE TABLE Logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profilo_id INT,
    azione VARCHAR(50) NOT NULL,
    dettagli TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    data_operazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (profilo_id) REFERENCES Profili(id) ON DELETE SET NULL
);

-- Tabella Log operazioni amministratori
CREATE TABLE LogsAdmin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_username VARCHAR(50) NOT NULL,
    azione VARCHAR(50) NOT NULL,
    target_profilo_id INT,
    dettagli TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    data_operazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (target_profilo_id) REFERENCES Profili(id) ON DELETE SET NULL
);

-- Tabella utenti amministratori (mantenuta dal sistema precedente)
CREATE TABLE utenti_admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    nome VARCHAR(50) NOT NULL,
    cognome VARCHAR(50) NOT NULL,
    ruolo ENUM('ADMIN', 'MANAGER', 'VIEWER') DEFAULT 'VIEWER',
    attivo BOOLEAN DEFAULT TRUE,
    ultimo_accesso TIMESTAMP NULL,
    data_creazione TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Indici per performance
CREATE INDEX idx_profili_stato ON Profili(stato);
CREATE INDEX idx_profili_disponibile ON Profili(disponibile);
CREATE INDEX idx_profili_email ON Profili(email);
CREATE INDEX idx_profili_cognome_nome ON Profili(cognome, nome);
CREATE INDEX idx_profili_provincia ON Profili(provincia_residenza);
CREATE INDEX idx_alboprofili_profilo ON AlboProfili(profilo_id);
CREATE INDEX idx_istruzione_profilo ON Istruzione(profilo_id);
CREATE INDEX idx_lavoroaut_profilo ON LavoroAut(profilo_id);
CREATE INDEX idx_lavorosub_profilo ON LavoroSub(profilo_id);
CREATE INDEX idx_lingue_profilo ON Lingue(profilo_id);
CREATE INDEX idx_it_profilo ON IT(profilo_id);
CREATE INDEX idx_allegati_profilo ON Allegati(profilo_id);
CREATE INDEX idx_logs_profilo ON Logs(profilo_id);
CREATE INDEX idx_logs_data ON Logs(data_operazione);

-- Inserimento dati di base

-- Province italiane (esempio principali)
INSERT INTO Province (Sigla, Provincia, Regione) VALUES
('RM', 'Roma', 'Lazio'),
('MI', 'Milano', 'Lombardia'),
('NA', 'Napoli', 'Campania'),
('TO', 'Torino', 'Piemonte'),
('PA', 'Palermo', 'Sicilia'),
('GE', 'Genova', 'Liguria'),
('BO', 'Bologna', 'Emilia-Romagna'),
('FI', 'Firenze', 'Toscana'),
('BA', 'Bari', 'Puglia'),
('CT', 'Catania', 'Sicilia'),
('VE', 'Venezia', 'Veneto'),
('VR', 'Verona', 'Veneto'),
('CA', 'Cagliari', 'Sardegna'),
('PD', 'Padova', 'Veneto'),
('BR', 'Brindisi', 'Puglia');

-- Paesi ISO (principali)
INSERT INTO ulkISO3166 (codice_alpha2, codice_alpha3, nome_it, nome_en) VALUES
('IT', 'ITA', 'Italia', 'Italy'),
('FR', 'FRA', 'Francia', 'France'), 
('DE', 'DEU', 'Germania', 'Germany'),
('ES', 'ESP', 'Spagna', 'Spain'),
('GB', 'GBR', 'Regno Unito', 'United Kingdom'),
('US', 'USA', 'Stati Uniti', 'United States'),
('CH', 'CHE', 'Svizzera', 'Switzerland'),
('AT', 'AUT', 'Austria', 'Austria'),
('BE', 'BEL', 'Belgio', 'Belgium'),
('NL', 'NLD', 'Paesi Bassi', 'Netherlands');

-- Albi professionali principali
INSERT INTO Albo (codice, nome, descrizione, sito_web) VALUES
('ING', 'Ordine degli Ingegneri', 'Albo professionale degli Ingegneri', 'https://www.cni.it'),
('ARCH', 'Ordine degli Architetti', 'Albo professionale degli Architetti', 'https://www.cnappc.it'),
('AVV', 'Ordine degli Avvocati', 'Albo professionale degli Avvocati', 'https://www.consiglionazionaleforense.it'),
('COMM', 'Ordine dei Commercialisti', 'Albo dei Dottori Commercialisti ed Esperti Contabili', 'https://www.commercialisti.it'),
('MED', 'Ordine dei Medici', 'Albo professionale dei Medici Chirurghi', 'https://portale.fnomceo.it'),
('GEOM', 'Collegio dei Geometri', 'Albo professionale dei Geometri', 'https://www.cng.it'),
('PERI', 'Collegio dei Periti', 'Albo dei Periti Industriali e dei Periti Industriali Laureati', 'https://www.cnpi.it'),
('AGRO', 'Ordine degli Agronomi', 'Albo professionale dei Dottori Agronomi e Forestali', 'https://www.conaf.it'),
('GIORN', 'Ordine dei Giornalisti', 'Albo professionale dei Giornalisti', 'https://www.odg.it'),
('PSIC', 'Ordine degli Psicologi', 'Albo professionale degli Psicologi', 'https://www.psy.it');

-- Lingue principali
INSERT INTO LingueElenco (codice, nome_it, nome_en) VALUES
('IT', 'Italiano', 'Italian'),
('EN', 'Inglese', 'English'),
('FR', 'Francese', 'French'),
('DE', 'Tedesco', 'German'),
('ES', 'Spagnolo', 'Spanish'),
('PT', 'Portoghese', 'Portuguese'),
('RU', 'Russo', 'Russian'),
('AR', 'Arabo', 'Arabic'),
('ZH', 'Cinese', 'Chinese'),
('JA', 'Giapponese', 'Japanese');

-- Competenze IT principali
INSERT INTO ITElenco (categoria, nome, descrizione) VALUES
('Linguaggi', 'PHP', 'Linguaggio di programmazione per sviluppo web'),
('Linguaggi', 'JavaScript', 'Linguaggio di programmazione client/server side'),
('Linguaggi', 'Python', 'Linguaggio di programmazione versatile'),
('Linguaggi', 'Java', 'Linguaggio di programmazione orientato agli oggetti'),
('Linguaggi', 'C#', 'Linguaggio di programmazione Microsoft .NET'),
('Database', 'MySQL', 'Sistema di gestione database relazionale'),
('Database', 'PostgreSQL', 'Sistema di gestione database avanzato'),
('Database', 'Oracle', 'Sistema di gestione database enterprise'),
('Database', 'SQL Server', 'Sistema di gestione database Microsoft'),
('Framework', 'Laravel', 'Framework PHP per sviluppo web'),
('Framework', 'React', 'Libreria JavaScript per interfacce utente'),
('Framework', 'Angular', 'Framework TypeScript per applicazioni web'),
('Framework', 'Vue.js', 'Framework JavaScript progressivo'),
('Tools', 'Git', 'Sistema di controllo versione distribuito'),
('Tools', 'Docker', 'Piattaforma di containerizzazione'),
('Cloud', 'AWS', 'Amazon Web Services'),
('Cloud', 'Azure', 'Microsoft Azure Cloud Platform'),
('Cloud', 'Google Cloud', 'Google Cloud Platform'),
('Office', 'Microsoft Office', 'Suite di produttività Microsoft'),
('Office', 'Adobe Creative Suite', 'Suite di creatività Adobe'),
('CAD', 'AutoCAD', 'Software di progettazione assistita'),
('CAD', 'SolidWorks', 'Software di modellazione 3D'),
('ERP', 'SAP', 'Sistema di gestione aziendale'),
('CRM', 'Salesforce', 'Piattaforma di gestione clienti');

-- Inserimento utente admin di default
-- Password: admin123 (CAMBIARE DOPO IL PRIMO ACCESSO!)
INSERT INTO utenti_admin (username, password_hash, email, nome, cognome, ruolo) VALUES
('admin', '$2y$10$g9u268hzKa25HtpUdedqn.r/Jn0aVBZaoOtNe2dD2hagLv930Bw6q', 'admin@zpec.local', 'Amministratore', 'Sistema', 'ADMIN');