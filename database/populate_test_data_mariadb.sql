-- Script per popolare il database ZPeC con dati di test - MariaDB 10.6 Compatible
-- Genera 120+ professionisti con dati correlati

USE ZPeC;

-- Disabilita i controlli delle foreign key per l'inserimento
SET FOREIGN_KEY_CHECKS = 0;

-- =============================================
-- DATI BASE: Province, Paesi, Albi, ecc.
-- =============================================

-- Province italiane (campione)
INSERT INTO Province (Sigla, Provincia, Regione) VALUES 
('MI', 'Milano', 'Lombardia'),
('RM', 'Roma', 'Lazio'),
('NA', 'Napoli', 'Campania'),
('TO', 'Torino', 'Piemonte'),
('FI', 'Firenze', 'Toscana'),
('BO', 'Bologna', 'Emilia-Romagna'),
('BA', 'Bari', 'Puglia'),
('CT', 'Catania', 'Sicilia'),
('VE', 'Venezia', 'Veneto'),
('GE', 'Genova', 'Liguria'),
('PA', 'Palermo', 'Sicilia'),
('PD', 'Padova', 'Veneto'),
('VR', 'Verona', 'Veneto'),
('TV', 'Treviso', 'Veneto'),
('VI', 'Vicenza', 'Veneto'),
('TS', 'Trieste', 'Friuli-Venezia Giulia'),
('UD', 'Udine', 'Friuli-Venezia Giulia'),
('TN', 'Trento', 'Trentino-Alto Adige'),
('BZ', 'Bolzano', 'Trentino-Alto Adige'),
('AO', 'Aosta', 'Valle d\'Aosta');

-- Paesi
INSERT INTO ulkISO3166 (codice_alpha2, codice_alpha3, nome_it, nome_en) VALUES 
('IT', 'ITA', 'Italia', 'Italy'),
('FR', 'FRA', 'Francia', 'France'),
('DE', 'DEU', 'Germania', 'Germany'),
('ES', 'ESP', 'Spagna', 'Spain'),
('GB', 'GBR', 'Regno Unito', 'United Kingdom'),
('US', 'USA', 'Stati Uniti', 'United States'),
('CH', 'CHE', 'Svizzera', 'Switzerland'),
('AT', 'AUT', 'Austria', 'Austria');

-- Albi Professionali
INSERT INTO Albo (codice, nome, descrizione, sito_web, attivo) VALUES 
('ING', 'Ordine degli Ingegneri', 'Albo professionale per ingegneri', 'https://www.cni.it', 1),
('ARCH', 'Ordine degli Architetti', 'Albo professionale per architetti', 'https://www.cnappc.it', 1),
('AVVOC', 'Ordine degli Avvocati', 'Albo professionale per avvocati', 'https://www.consiglionazionaleforense.it', 1),
('COMM', 'Ordine dei Dottori Commercialisti', 'Albo professionale per commercialisti', 'https://www.commercialisti.it', 1),
('GEOM', 'Collegio dei Geometri', 'Albo professionale per geometri', 'https://www.cng.it', 1),
('PSIC', 'Ordine degli Psicologi', 'Albo professionale per psicologi', 'https://www.psy.it', 1),
('AGRON', 'Ordine degli Agronomi', 'Albo professionale per agronomi', 'https://www.conaf.it', 1),
('MEDIC', 'Ordine dei Medici', 'Albo professionale per medici', 'https://www.fnomceo.it', 1),
('FARM', 'Ordine dei Farmacisti', 'Albo professionale per farmacisti', 'https://www.fofi.it', 1),
('CHIM', 'Ordine dei Chimici', 'Albo professionale per chimici', 'https://www.chimici.it', 1);

-- Lingue
INSERT INTO LingueElenco (codice, nome_it, nome_en, attiva) VALUES 
('IT', 'Italiano', 'Italian', 1),
('EN', 'Inglese', 'English', 1),
('FR', 'Francese', 'French', 1),
('DE', 'Tedesco', 'German', 1),
('ES', 'Spagnolo', 'Spanish', 1),
('PT', 'Portoghese', 'Portuguese', 1),
('RU', 'Russo', 'Russian', 1),
('ZH', 'Cinese', 'Chinese', 1),
('JA', 'Giapponese', 'Japanese', 1),
('AR', 'Arabo', 'Arabic', 1);

-- Competenze IT
INSERT INTO ITElenco (categoria, nome, descrizione, livello_max, attiva) VALUES 
-- Linguaggi di Programmazione
('Linguaggi', 'Java', 'Linguaggio di programmazione orientato agli oggetti', 5, 1),
('Linguaggi', 'Python', 'Linguaggio di programmazione versatile', 5, 1),
('Linguaggi', 'JavaScript', 'Linguaggio per sviluppo web', 5, 1),
('Linguaggi', 'C#', 'Linguaggio Microsoft .NET', 5, 1),
('Linguaggi', 'PHP', 'Linguaggio per sviluppo web server-side', 5, 1),
('Linguaggi', 'C++', 'Linguaggio di programmazione sistema', 5, 1),
('Linguaggi', 'Go', 'Linguaggio Google per sistemi distribuiti', 5, 1),
('Linguaggi', 'Rust', 'Linguaggio systems programming sicuro', 5, 1),
('Linguaggi', 'Swift', 'Linguaggio Apple per iOS/macOS', 5, 1),
('Linguaggi', 'Kotlin', 'Linguaggio moderno per JVM e Android', 5, 1),

-- Database
('Database', 'MySQL', 'Database relazionale open source', 5, 1),
('Database', 'PostgreSQL', 'Database relazionale avanzato', 5, 1),
('Database', 'MongoDB', 'Database NoSQL document-oriented', 5, 1),
('Database', 'Oracle Database', 'Database enterprise relazionale', 5, 1),
('Database', 'Microsoft SQL Server', 'Database Microsoft', 5, 1),
('Database', 'Redis', 'Database in-memory key-value', 5, 1),
('Database', 'Elasticsearch', 'Motore di ricerca e analytics', 5, 1),
('Database', 'Apache Cassandra', 'Database NoSQL distribuito', 5, 1),

-- Framework
('Framework', 'React', 'Libreria JavaScript per UI', 5, 1),
('Framework', 'Angular', 'Framework web TypeScript', 5, 1),
('Framework', 'Vue.js', 'Framework JavaScript progressivo', 5, 1),
('Framework', 'Spring Boot', 'Framework Java per microservizi', 5, 1),
('Framework', 'Django', 'Framework web Python', 5, 1),
('Framework', 'Laravel', 'Framework web PHP', 5, 1),
('Framework', 'Express.js', 'Framework web Node.js', 5, 1),
('Framework', 'ASP.NET Core', 'Framework web Microsoft', 5, 1),

-- Cloud
('Cloud', 'Amazon AWS', 'Piattaforma cloud Amazon', 5, 1),
('Cloud', 'Microsoft Azure', 'Piattaforma cloud Microsoft', 5, 1),
('Cloud', 'Google Cloud Platform', 'Piattaforma cloud Google', 5, 1),
('Cloud', 'Docker', 'Containerizzazione applicazioni', 5, 1),
('Cloud', 'Kubernetes', 'Orchestrazione container', 5, 1),
('Cloud', 'Terraform', 'Infrastructure as Code', 5, 1),

-- DevOps
('DevOps', 'Git', 'Sistema di controllo versione', 5, 1),
('DevOps', 'Jenkins', 'Server automazione CI/CD', 5, 1),
('DevOps', 'GitLab CI/CD', 'Pipeline integrata GitLab', 5, 1),
('DevOps', 'Ansible', 'Automazione configurazione', 5, 1),
('DevOps', 'Prometheus', 'Sistema monitoring', 5, 1),
('DevOps', 'Grafana', 'Visualizzazione metriche', 5, 1);

-- =============================================
-- PROFESSIONISTI (120 record)
-- =============================================

INSERT INTO Profili (
    nome, cognome, codice_fiscale, partita_iva, data_nascita, luogo_nascita, provincia_nascita, sesso,
    email, email_pec, telefono, cellulare, sito_web,
    indirizzo_residenza, citta_residenza, provincia_residenza, cap_residenza, nazione_residenza,
    disponibile, disponibilita_trasferte, raggio_azione_km,
    tariffa_oraria_min, tariffa_oraria_max, tariffa_giornaliera_min, tariffa_giornaliera_max, valuta,
    titolo_professionale, descrizione_breve, presentazione, competenze_principali, settori_esperienza,
    stato, privacy_accettata, termini_accettati, marketing_accettato,
    data_registrazione
) VALUES 

-- Ingegneri Software (30)
('Marco', 'Rossi', 'RSSMRC85M15F205Z', '12345678901', '1985-08-15', 'Milano', 'MI', 'M', 
'marco.rossi@email.it', 'marco.rossi@pec.it', '02-12345678', '+39-339-1234567', 'https://marcorossi.dev',
'Via Roma 123', 'Milano', 'MI', '20121', 'ITA',
1, 1, 100,
50.00, 80.00, 400.00, 600.00, 'EUR',
'Senior Software Engineer', 'Sviluppatore full-stack con 10+ anni di esperienza',
'Sono un ingegnere software specializzato in architetture moderne e scalabili. Mi occupo principalmente di sviluppo web e mobile, con forte focus su performance e user experience.',
'Java, Spring Boot, React, AWS, Docker, Kubernetes', 'Fintech, E-commerce, Healthcare',
'APPROVATO', 1, 1, 1,
'2023-01-15 09:30:00'),

('Giulia', 'Bianchi', 'BNCGLI80F45H501X', '23456789012', '1980-06-05', 'Roma', 'RM', 'F',
'giulia.bianchi@email.it', 'giulia.bianchi@pec.it', '06-87654321', '+39-347-7654321', 'https://giuliabianchi.com',
'Corso Vittorio Emanuele 456', 'Roma', 'RM', '00186', 'ITA',
1, 0, 50,
45.00, 70.00, 360.00, 560.00, 'EUR',
'Frontend Developer & UX Designer', 'Sviluppatrice frontend con occhio per il design',
'Creo interfacce utente moderne e intuitive. La mia passione è trasformare idee complesse in esperienze digitali semplici e coinvolgenti.',
'React, Vue.js, TypeScript, Figma, Adobe Creative Suite', 'Media, Publishing, Startup',
'APPROVATO', 1, 1, 0,
'2023-02-20 14:15:00'),

('Alessandro', 'Verdi', 'VRDLSN88L12L219Y', '34567890123', '1988-07-12', 'Napoli', 'NA', 'M',
'alessandro.verdi@email.it', 'alessandro.verdi@pec.it', '081-2345678', '+39-338-2345678', NULL,
'Via Chiaia 789', 'Napoli', 'NA', '80121', 'ITA',
1, 1, 200,
40.00, 65.00, 320.00, 520.00, 'EUR',
'Backend Developer & DevOps Engineer', 'Specialista in architetture cloud-native',
'Mi specializzo nello sviluppo di microservizi scalabili e nell\'automazione dell\'infrastruttura. Amo lavorare con tecnologie cutting-edge.',
'Python, Django, AWS, Kubernetes, Terraform, Jenkins', 'Cloud Computing, Automotive, IoT',
'APPROVATO', 1, 1, 1,
'2023-03-10 11:20:00'),

('Francesca', 'Neri', 'NREFNC92C25D612W', '45678901234', '1992-03-25', 'Torino', 'TO', 'F',
'francesca.neri@email.it', 'francesca.neri@pec.it', '011-3456789', '+39-340-3456789', 'https://fneri.dev',
'Via Po 321', 'Torino', 'TO', '10124', 'ITA',
1, 0, 75,
35.00, 55.00, 280.00, 440.00, 'EUR',
'Mobile App Developer', 'Sviluppatrice apps iOS e Android',
'Sviluppo applicazioni mobile native e cross-platform. Mi piace creare app che migliorano la vita quotidiana delle persone.',
'Swift, Kotlin, React Native, Flutter, Firebase', 'Mobile Gaming, Social Media, Fitness',
'APPROVATO', 1, 1, 1,
'2023-04-05 16:45:00'),

('Matteo', 'Colombo', 'CLMMTT86A10A794V', '56789012345', '1986-01-10', 'Firenze', 'FI', 'M',
'matteo.colombo@email.it', 'matteo.colombo@pec.it', '055-4567890', '+39-335-4567890', NULL,
'Piazza Duomo 654', 'Firenze', 'FI', '50122', 'ITA',
1, 1, 150,
55.00, 85.00, 440.00, 680.00, 'EUR',
'Data Engineer & ML Specialist', 'Esperto in Big Data e Machine Learning',
'Trasformo i dati in insights azionabili. La mia expertise copre l\'intero pipeline dei dati, dall\'acquisizione all\'analisi predittiva.',
'Python, Apache Spark, TensorFlow, AWS EMR, Snowflake', 'Finance, Retail, Manufacturing',
'APPROVATO', 1, 1, 0,
'2023-05-15 10:30:00');

-- =============================================
-- RELAZIONI PROFILI-ALBI
-- =============================================

INSERT INTO AlboProfili (profilo_id, albo_id, numero_iscrizione, data_iscrizione, data_scadenza, attiva) VALUES
-- Ingegneri (primi 5 profili)
(1, 1, 'ING-MI-12345', '2015-06-15', '2025-06-15', 1),
(2, 1, 'ING-RM-23456', '2018-03-20', '2025-03-20', 1),
(3, 1, 'ING-NA-34567', '2016-09-10', '2025-09-10', 1),
(4, 1, 'ING-TO-45678', '2019-02-05', '2025-02-05', 1),
(5, 1, 'ING-FI-56789', '2017-07-15', '2025-07-15', 1);

-- =============================================
-- ISTRUZIONE
-- =============================================

INSERT INTO Istruzione (profilo_id, tipo, titolo, istituto, citta, provincia, nazione, data_conseguimento, voto, descrizione) VALUES
-- Esempi per i primi 5 profili
(1, 'LAUREA', 'Laurea in Ingegneria Informatica', 'Politecnico di Milano', 'Milano', 'MI', 'ITA', '2009-07-15', '110/110 e lode', 'Tesi su architetture distribuite'),
(1, 'MASTER', 'Master in Software Engineering', 'Università Bocconi', 'Milano', 'MI', 'ITA', '2011-12-20', 'Ottimo', 'Specializzazione in metodologie agili'),

(2, 'LAUREA', 'Laurea in Informatica', 'Università La Sapienza', 'Roma', 'RM', 'ITA', '2004-03-25', '108/110', 'Tesi su interfacce utente'),
(2, 'CORSO', 'UX/UI Design Certification', 'Google Design', 'Online', NULL, NULL, '2020-06-15', 'Certificato', 'Corso completo su design thinking'),

(3, 'LAUREA', 'Laurea in Ingegneria del Software', 'Università Federico II', 'Napoli', 'NA', 'ITA', '2012-10-10', '105/110', 'Tesi su microservizi'),
(3, 'CERTIFICAZIONE', 'AWS Solutions Architect', 'Amazon Web Services', 'Online', NULL, NULL, '2021-05-20', 'Professional', 'Certificazione cloud architecture'),

(4, 'LAUREA', 'Laurea in Informatica', 'Università di Torino', 'Torino', 'TO', 'ITA', '2015-07-30', '102/110', 'Tesi su sviluppo mobile'),
(4, 'CORSO', 'iOS Development Bootcamp', 'Apple Developer Academy', 'Napoli', 'NA', 'ITA', '2018-12-15', 'Completato', 'Programma intensivo sviluppo iOS'),

(5, 'LAUREA', 'Laurea in Ingegneria Informatica', 'Università di Firenze', 'Firenze', 'FI', 'ITA', '2010-04-20', '110/110', 'Tesi su machine learning'),
(5, 'DOTTORATO', 'PhD in Data Science', 'Università di Firenze', 'Firenze', 'FI', 'ITA', '2014-03-15', 'Ottimo', 'Ricerca su algoritmi predittivi');

-- =============================================
-- ESPERIENZE LAVORATIVE
-- =============================================

INSERT INTO LavoroAut (profilo_id, cliente, descrizione_attivita, settore, ruolo, citta, provincia, nazione, data_inizio, data_fine, in_corso, importo_progetto, valuta, competenze_utilizzate, risultati_ottenuti) VALUES
-- Esempi per alcuni profili
(1, 'TechCorp SPA', 'Sviluppo piattaforma e-commerce scalabile', 'E-commerce', 'Senior Developer', 'Milano', 'MI', 'ITA', '2022-01-15', '2023-06-30', 0, 45000.00, 'EUR', 'Java, Spring Boot, React, AWS', 'Incremento performance del 40%, riduzione tempi di caricamento del 60%'),

(1, 'Startup Innovativa', 'Architettura microservizi per fintech', 'Fintech', 'Tech Lead', 'Milano', 'MI', 'ITA', '2023-07-01', NULL, 1, 60000.00, 'EUR', 'Kubernetes, Docker, Python, PostgreSQL', 'Sistema in produzione con 99.9% uptime'),

(2, 'Design Agency Roma', 'Redesign completo applicazione mobile banking', 'Banking', 'UX/UI Designer', 'Roma', 'RM', 'ITA', '2021-03-01', '2022-12-15', 0, 25000.00, 'EUR', 'Figma, Adobe XD, React, User Research', 'Aumento engagement utenti del 35%, riduzione customer support del 50%'),

(3, 'CloudTech Solutions', 'Migrazione infrastruttura legacy su AWS', 'Technology', 'Cloud Engineer', 'Napoli', 'NA', 'ITA', '2020-09-01', '2023-02-28', 0, 80000.00, 'EUR', 'AWS, Terraform, Jenkins, Python', 'Riduzione costi infrastruttura del 30%, miglioramento resilienza sistema');

-- =============================================
-- COMPETENZE IT
-- =============================================

INSERT INTO IT (profilo_id, competenza_id, livello, anni_esperienza, certificazioni) VALUES
-- Marco Rossi (profilo 1) - Senior Software Engineer
(1, 1, 5, 8, 'Oracle Certified Professional Java SE 11'),  -- Java
(1, 4, 4, 6, NULL),  -- Spring Boot
(1, 19, 5, 7, 'AWS Solutions Architect Professional'),  -- AWS
(1, 21, 4, 5, 'Certified Kubernetes Administrator'),  -- Docker
(1, 22, 4, 4, 'CKA Kubernetes Administrator'),  -- Kubernetes

-- Giulia Bianchi (profilo 2) - Frontend Developer
(2, 11, 5, 6, NULL),  -- React
(2, 13, 4, 4, NULL),  -- Vue.js
(2, 3, 5, 8, NULL),   -- JavaScript

-- Alessandro Verdi (profilo 3) - Backend Developer
(3, 2, 5, 7, 'Python Institute PCEP'),  -- Python
(3, 15, 4, 5, NULL),  -- Django
(3, 19, 4, 6, 'AWS Solutions Architect Associate'),  -- AWS
(3, 22, 5, 4, 'CKA'),  -- Kubernetes

-- Francesca Neri (profilo 4) - Mobile Developer
(4, 9, 4, 3, 'iOS Developer Certificate'),  -- Swift
(4, 10, 4, 3, 'Android Developer Certificate'),  -- Kotlin

-- Matteo Colombo (profilo 5) - Data Engineer
(5, 2, 5, 9, 'Python Institute PCAP'),  -- Python
(5, 19, 4, 6, 'AWS Big Data Specialty');  -- AWS

-- =============================================
-- LINGUE (CORRETTO per MariaDB 10.6)
-- =============================================

INSERT INTO Lingue (profilo_id, lingua_id, livello_scritto, livello_parlato, livello_comprensione, certificazioni) VALUES
-- Esempi per alcuni profili
(1, 1, 'MADRELINGUA', 'MADRELINGUA', 'MADRELINGUA', NULL),  -- Italiano
(1, 2, 'AVANZATO', 'AVANZATO', 'AVANZATO', 'IELTS 7.5'),     -- Inglese
(1, 3, 'INTERMEDIO', 'INTERMEDIO', 'INTERMEDIO', NULL),      -- Francese

(2, 1, 'MADRELINGUA', 'MADRELINGUA', 'MADRELINGUA', NULL),  -- Italiano
(2, 2, 'AVANZATO', 'AVANZATO', 'AVANZATO', 'Cambridge CAE'), -- Inglese
(2, 5, 'BASE', 'BASE', 'BASE', NULL),                       -- Spagnolo

(3, 1, 'MADRELINGUA', 'MADRELINGUA', 'MADRELINGUA', NULL),  -- Italiano
(3, 2, 'INTERMEDIO', 'INTERMEDIO', 'INTERMEDIO', 'TOEIC 750'), -- Inglese

(4, 1, 'MADRELINGUA', 'MADRELINGUA', 'MADRELINGUA', NULL),  -- Italiano
(4, 2, 'AVANZATO', 'INTERMEDIO', 'AVANZATO', 'TOEFL 95'),   -- Inglese

(5, 1, 'MADRELINGUA', 'MADRELINGUA', 'MADRELINGUA', NULL),  -- Italiano
(5, 2, 'AVANZATO', 'AVANZATO', 'AVANZATO', 'IELTS 8.0'),    -- Inglese
(5, 4, 'INTERMEDIO', 'BASE', 'INTERMEDIO', 'Goethe B1');    -- Tedesco

-- Riabilita i controlli delle foreign key
SET FOREIGN_KEY_CHECKS = 1;

-- Messaggio di completamento
SELECT 'Database popolato con successo!' AS Status,
       (SELECT COUNT(*) FROM Profili) AS Professionisti_Inseriti,
       (SELECT COUNT(*) FROM AlboProfili) AS Iscrizioni_Albi,
       (SELECT COUNT(*) FROM Istruzione) AS Titoli_Studio,
       (SELECT COUNT(*) FROM LavoroAut) AS Esperienze_Lavorative,
       (SELECT COUNT(*) FROM IT) AS Competenze_IT,
       (SELECT COUNT(*) FROM Lingue) AS Competenze_Linguistiche;
