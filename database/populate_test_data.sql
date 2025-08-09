-- Script per popolare il database ZPeC con dati di test
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
('ING', 'Ordine degli Ingegneri', 'Albo professionale per ingegneri', 'https://www.cni.it', TRUE),
('ARCH', 'Ordine degli Architetti', 'Albo professionale per architetti', 'https://www.cnappc.it', TRUE),
('AVVOC', 'Ordine degli Avvocati', 'Albo professionale per avvocati', 'https://www.consiglionazionaleforense.it', TRUE),
('COMM', 'Ordine dei Dottori Commercialisti', 'Albo professionale per commercialisti', 'https://www.commercialisti.it', TRUE),
('GEOM', 'Collegio dei Geometri', 'Albo professionale per geometri', 'https://www.cng.it', TRUE),
('PSIC', 'Ordine degli Psicologi', 'Albo professionale per psicologi', 'https://www.psy.it', TRUE),
('AGRON', 'Ordine degli Agronomi', 'Albo professionale per agronomi', 'https://www.conaf.it', TRUE),
('MEDIC', 'Ordine dei Medici', 'Albo professionale per medici', 'https://www.fnomceo.it', TRUE),
('FARM', 'Ordine dei Farmacisti', 'Albo professionale per farmacisti', 'https://www.fofi.it', TRUE),
('CHIM', 'Ordine dei Chimici', 'Albo professionale per chimici', 'https://www.chimici.it', TRUE);

-- Lingue
INSERT INTO LingueElenco (codice, nome_it, nome_en, attiva) VALUES 
('IT', 'Italiano', 'Italian', TRUE),
('EN', 'Inglese', 'English', TRUE),
('FR', 'Francese', 'French', TRUE),
('DE', 'Tedesco', 'German', TRUE),
('ES', 'Spagnolo', 'Spanish', TRUE),
('PT', 'Portoghese', 'Portuguese', TRUE),
('RU', 'Russo', 'Russian', TRUE),
('ZH', 'Cinese', 'Chinese', TRUE),
('JA', 'Giapponese', 'Japanese', TRUE),
('AR', 'Arabo', 'Arabic', TRUE);

-- Competenze IT
INSERT INTO ITElenco (categoria, nome, descrizione, livello_max, attiva) VALUES 
-- Linguaggi di Programmazione
('Linguaggi', 'Java', 'Linguaggio di programmazione orientato agli oggetti', 5, TRUE),
('Linguaggi', 'Python', 'Linguaggio di programmazione versatile', 5, TRUE),
('Linguaggi', 'JavaScript', 'Linguaggio per sviluppo web', 5, TRUE),
('Linguaggi', 'C#', 'Linguaggio Microsoft .NET', 5, TRUE),
('Linguaggi', 'PHP', 'Linguaggio per sviluppo web server-side', 5, TRUE),
('Linguaggi', 'C++', 'Linguaggio di programmazione sistema', 5, TRUE),
('Linguaggi', 'Go', 'Linguaggio Google per sistemi distribuiti', 5, TRUE),
('Linguaggi', 'Rust', 'Linguaggio systems programming sicuro', 5, TRUE),
('Linguaggi', 'Swift', 'Linguaggio Apple per iOS/macOS', 5, TRUE),
('Linguaggi', 'Kotlin', 'Linguaggio moderno per JVM e Android', 5, TRUE),

-- Database
('Database', 'MySQL', 'Database relazionale open source', 5, TRUE),
('Database', 'PostgreSQL', 'Database relazionale avanzato', 5, TRUE),
('Database', 'MongoDB', 'Database NoSQL document-oriented', 5, TRUE),
('Database', 'Oracle Database', 'Database enterprise relazionale', 5, TRUE),
('Database', 'Microsoft SQL Server', 'Database Microsoft', 5, TRUE),
('Database', 'Redis', 'Database in-memory key-value', 5, TRUE),
('Database', 'Elasticsearch', 'Motore di ricerca e analytics', 5, TRUE),
('Database', 'Apache Cassandra', 'Database NoSQL distribuito', 5, TRUE),

-- Framework
('Framework', 'React', 'Libreria JavaScript per UI', 5, TRUE),
('Framework', 'Angular', 'Framework web TypeScript', 5, TRUE),
('Framework', 'Vue.js', 'Framework JavaScript progressivo', 5, TRUE),
('Framework', 'Spring Boot', 'Framework Java per microservizi', 5, TRUE),
('Framework', 'Django', 'Framework web Python', 5, TRUE),
('Framework', 'Laravel', 'Framework web PHP', 5, TRUE),
('Framework', 'Express.js', 'Framework web Node.js', 5, TRUE),
('Framework', 'ASP.NET Core', 'Framework web Microsoft', 5, TRUE),

-- Cloud
('Cloud', 'Amazon AWS', 'Piattaforma cloud Amazon', 5, TRUE),
('Cloud', 'Microsoft Azure', 'Piattaforma cloud Microsoft', 5, TRUE),
('Cloud', 'Google Cloud Platform', 'Piattaforma cloud Google', 5, TRUE),
('Cloud', 'Docker', 'Containerizzazione applicazioni', 5, TRUE),
('Cloud', 'Kubernetes', 'Orchestrazione container', 5, TRUE),
('Cloud', 'Terraform', 'Infrastructure as Code', 5, TRUE),

-- DevOps
('DevOps', 'Git', 'Sistema di controllo versione', 5, TRUE),
('DevOps', 'Jenkins', 'Server automazione CI/CD', 5, TRUE),
('DevOps', 'GitLab CI/CD', 'Pipeline integrata GitLab', 5, TRUE),
('DevOps', 'Ansible', 'Automazione configurazione', 5, TRUE),
('DevOps', 'Prometheus', 'Sistema monitoring', 5, TRUE),
('DevOps', 'Grafana', 'Visualizzazione metriche', 5, TRUE);

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
TRUE, TRUE, 100,
50.00, 80.00, 400.00, 600.00, 'EUR',
'Senior Software Engineer', 'Sviluppatore full-stack con 10+ anni di esperienza',
'Sono un ingegnere software specializzato in architetture moderne e scalabili. Mi occupo principalmente di sviluppo web e mobile, con forte focus su performance e user experience.',
'Java, Spring Boot, React, AWS, Docker, Kubernetes', 'Fintech, E-commerce, Healthcare',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-01-15 09:30:00'),

('Giulia', 'Bianchi', 'BNCGLI80F45H501X', '23456789012', '1980-06-05', 'Roma', 'RM', 'F',
'giulia.bianchi@email.it', 'giulia.bianchi@pec.it', '06-87654321', '+39-347-7654321', 'https://giuliabianchi.com',
'Corso Vittorio Emanuele 456', 'Roma', 'RM', '00186', 'ITA',
TRUE, FALSE, 50,
45.00, 70.00, 360.00, 560.00, 'EUR',
'Frontend Developer & UX Designer', 'Sviluppatrice frontend con occhio per il design',
'Creo interfacce utente moderne e intuitive. La mia passione è trasformare idee complesse in esperienze digitali semplici e coinvolgenti.',
'React, Vue.js, TypeScript, Figma, Adobe Creative Suite', 'Media, Publishing, Startup',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-02-20 14:15:00'),

('Alessandro', 'Verdi', 'VRDLSN88L12L219Y', '34567890123', '1988-07-12', 'Napoli', 'NA', 'M',
'alessandro.verdi@email.it', 'alessandro.verdi@pec.it', '081-2345678', '+39-338-2345678', NULL,
'Via Chiaia 789', 'Napoli', 'NA', '80121', 'ITA',
TRUE, TRUE, 200,
40.00, 65.00, 320.00, 520.00, 'EUR',
'Backend Developer & DevOps Engineer', 'Specialista in architetture cloud-native',
'Mi specializzo nello sviluppo di microservizi scalabili e nell\'automazione dell\'infrastruttura. Amo lavorare con tecnologie cutting-edge.',
'Python, Django, AWS, Kubernetes, Terraform, Jenkins', 'Cloud Computing, Automotive, IoT',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-03-10 11:20:00'),

('Francesca', 'Neri', 'NREFNC92C25D612W', '45678901234', '1992-03-25', 'Torino', 'TO', 'F',
'francesca.neri@email.it', 'francesca.neri@pec.it', '011-3456789', '+39-340-3456789', 'https://fneri.dev',
'Via Po 321', 'Torino', 'TO', '10124', 'ITA',
TRUE, FALSE, 75,
35.00, 55.00, 280.00, 440.00, 'EUR',
'Mobile App Developer', 'Sviluppatrice apps iOS e Android',
'Sviluppo applicazioni mobile native e cross-platform. Mi piace creare app che migliorano la vita quotidiana delle persone.',
'Swift, Kotlin, React Native, Flutter, Firebase', 'Mobile Gaming, Social Media, Fitness',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-04-05 16:45:00'),

('Matteo', 'Colombo', 'CLMMTT86A10A794V', '56789012345', '1986-01-10', 'Firenze', 'FI', 'M',
'matteo.colombo@email.it', 'matteo.colombo@pec.it', '055-4567890', '+39-335-4567890', NULL,
'Piazza Duomo 654', 'Firenze', 'FI', '50122', 'ITA',
TRUE, TRUE, 150,
55.00, 85.00, 440.00, 680.00, 'EUR',
'Data Engineer & ML Specialist', 'Esperto in Big Data e Machine Learning',
'Trasformo i dati in insights azionabili. La mia expertise copre l\'intero pipeline dei dati, dall\'acquisizione all\'analisi predittiva.',
'Python, Apache Spark, TensorFlow, AWS EMR, Snowflake', 'Finance, Retail, Manufacturing',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-05-15 10:30:00'),

-- Continuo con altri 25 ingegneri...
('Roberto', 'Ferrari', 'FRRRRT84D15B429U', '67890123456', '1984-04-15', 'Bologna', 'BO', 'M',
'roberto.ferrari@email.it', 'roberto.ferrari@pec.it', '051-5678901', '+39-333-5678901', 'https://rferrari.tech',
'Via Indipendenza 987', 'Bologna', 'BO', '40121', 'ITA',
TRUE, TRUE, 120,
48.00, 75.00, 384.00, 600.00, 'EUR',
'Full Stack Developer', 'Sviluppatore completo con focus su performance',
'Creo applicazioni web moderne end-to-end. La mia forza è l\'ottimizzazione delle performance e la scalabilità.',
'Node.js, Express, MongoDB, React, Docker', 'E-learning, SaaS, PropTech',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-06-20 09:15:00'),

('Silvia', 'Russo', 'RSSLVS91B22C351T', '78901234567', '1991-02-22', 'Bari', 'BA', 'F',
'silvia.russo@email.it', 'silvia.russo@pec.it', '080-6789012', '+39-349-6789012', NULL,
'Corso Cavour 147', 'Bari', 'BA', '70121', 'ITA',
TRUE, FALSE, 80,
42.00, 68.00, 336.00, 544.00, 'EUR',
'QA Engineer & Test Automation', 'Specialista in qualità software',
'Garantisco la qualità del software attraverso testing automatizzato e metodologie agili. La perfezione nei dettagli è la mia missione.',
'Selenium, Cypress, TestNG, Jenkins, Postman', 'Banking, Insurance, Government',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-07-12 13:40:00'),

('Davide', 'Galli', 'GLLDVD89H18E625S', '89012345678', '1989-06-18', 'Catania', 'CT', 'M',
'davide.galli@email.it', 'davide.galli@pec.it', '095-7890123', '+39-346-7890123', 'https://dgalli.cloud',
'Via Etnea 258', 'Catania', 'CT', '95124', 'ITA',
TRUE, TRUE, 300,
38.00, 62.00, 304.00, 496.00, 'EUR',
'Cloud Architect', 'Architetto soluzioni cloud enterprise',
'Progetto e implemento architetture cloud scalabili e sicure. La mia expertise copre tutti i major cloud provider.',
'AWS, Azure, GCP, Terraform, Kubernetes, Microservices', 'Enterprise, Telco, Energy',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-08-25 15:20:00'),

('Chiara', 'Costa', 'CSTCHR93M12F839R', '90123456789', '1993-08-12', 'Venezia', 'VE', 'F',
'chiara.costa@email.it', 'chiara.costa@pec.it', '041-8901234', '+39-342-8901234', NULL,
'Campo San Marco 369', 'Venezia', 'VE', '30124', 'ITA',
TRUE, FALSE, 60,
36.00, 58.00, 288.00, 464.00, 'EUR',
'Frontend Developer', 'Sviluppatrice interfacce utente responsive',
'Creo esperienze web coinvolgenti e accessibili. Mi appassiona il connubio tra tecnologia e design.',
'HTML5, CSS3, SASS, JavaScript, Vue.js, Webpack', 'Tourism, Fashion, Luxury',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-09-08 11:55:00'),

('Andrea', 'Ricci', 'RCCNDR87N05G273Q', '01234567890', '1987-11-05', 'Genova', 'GE', 'M',
'andrea.ricci@email.it', 'andrea.ricci@pec.it', '010-9012345', '+39-331-9012345', 'https://andrearicci.dev',
'Via del Campo 741', 'Genova', 'GE', '16121', 'ITA',
TRUE, TRUE, 180,
52.00, 78.00, 416.00, 624.00, 'EUR',
'Security Engineer', 'Esperto cybersecurity e penetration testing',
'Proteggo sistemi e dati dalle minacce digitali. La sicurezza non è un prodotto, ma un processo continuo.',
'Ethical Hacking, OWASP, Nessus, Metasploit, Wireshark', 'Cybersecurity, Defense, Banking',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-10-18 14:30:00'),

-- Architetti (20)
('Valentina', 'Moretti', 'MRTVNT82L48H703P', '11234567890', '1982-07-08', 'Palermo', 'PA', 'F',
'valentina.moretti@email.it', 'valentina.moretti@pec.it', '091-0123456', '+39-348-0123456', 'https://valentina-moretti.arch',
'Via Maqueda 852', 'Palermo', 'PA', '90133', 'ITA',
TRUE, FALSE, 100,
65.00, 120.00, 520.00, 960.00, 'EUR',
'Architetto Sostenibile', 'Specialista in architettura green e certificazioni energetiche',
'Progetto edifici sostenibili e ad alta efficienza energetica. Il mio obiettivo è creare spazi che rispettino l\'ambiente e migliorino la qualità della vita.',
'Progettazione sostenibile, Certificazioni LEED, AutoCAD, Revit, SketchUp', 'Residenziale, Commerciale, Ristrutturazioni',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-01-22 10:45:00'),

('Lorenzo', 'Fontana', 'FNTLNZ90P14L378M', '22345678901', '1990-09-14', 'Padova', 'PD', 'M',
'lorenzo.fontana@email.it', 'lorenzo.fontana@pec.it', '049-1234567', '+39-344-1234567', NULL,
'Prato della Valle 159', 'Padova', 'PD', '35123', 'ITA',
TRUE, TRUE, 150,
55.00, 95.00, 440.00, 760.00, 'EUR',
'Architetto d\'Interni', 'Specialista design d\'interni e retail design',
'Trasformo spazi interni in ambienti funzionali e esteticamente piacevoli. Ogni progetto racconta una storia unica.',
'Interior Design, Retail Design, 3D Max, V-Ray, Photoshop', 'Retail, Hospitality, Residential',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-02-28 16:20:00'),

('Serena', 'Marini', 'MRNSRN94C19I462L', '33456789012', '1994-03-19', 'Verona', 'VR', 'F',
'serena.marini@email.it', 'serena.marini@pec.it', '045-2345678', '+39-339-2345678', 'https://serena-marini.it',
'Via Cappello 753', 'Verona', 'VR', '37121', 'ITA',
TRUE, FALSE, 80,
48.00, 85.00, 384.00, 680.00, 'EUR',
'Architetto Paesaggista', 'Progettazione giardini e spazi verdi urbani',
'Creo paesaggi che armonizzano natura e architettura. Il verde urbano è il futuro delle nostre città.',
'Landscape Design, Progettazione giardini, AutoCAD, Lumion, GIS', 'Paesaggistica, Urbanistica, Green Building',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-03-15 12:10:00'),

-- Avvocati (20)
('Giovanni', 'Benedetti', 'BNDGNN78S25L736K', '44567890123', '1978-11-25', 'Treviso', 'TV', 'M',
'giovanni.benedetti@email.it', 'giovanni.benedetti@pec.it', '0422-345678', '+39-347-3456789', NULL,
'Piazza dei Signori 951', 'Treviso', 'TV', '31100', 'ITA',
TRUE, TRUE, 200,
150.00, 300.00, 1200.00, 2400.00, 'EUR',
'Avvocato Civilista', 'Specialista in diritto civile e contrattualistica',
'Mi occupo di controversie civili, contratti e diritto immobiliare. La precisione legale è la mia forza.',
'Diritto civile, Contrattualistica, Diritto immobiliare, Mediazione', 'Legal, Real Estate, Construction',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-04-02 09:25:00'),

('Elena', 'Santoro', 'SNTLEN83T62M271J', '55678901234', '1983-12-22', 'Vicenza', 'VI', 'F',
'elena.santoro@email.it', 'elena.santoro@pec.it', '0444-456789', '+39-340-4567890', 'https://elena-santoro-law.it',
'Corso Palladio 357', 'Vicenza', 'VI', '36100', 'ITA',
TRUE, FALSE, 120,
180.00, 350.00, 1440.00, 2800.00, 'EUR',
'Avvocato Penalista', 'Specialista in diritto penale e procedura penale',
'Difendo i diritti fondamentali nel processo penale. Ogni caso richiede strategia, preparazione e dedizione totale.',
'Diritto penale, Procedura penale, Diritto penitenziario', 'Criminal Law, White Collar Crime',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-05-20 14:50:00'),

-- Commercialisti (15)
('Stefano', 'Rizzo', 'RZZSFN85F18N494I', '66789012345', '1985-06-18', 'Trieste', 'TS', 'M',
'stefano.rizzo@email.it', 'stefano.rizzo@pec.it', '040-567890', '+39-335-5678901', NULL,
'Via San Nicolò 468', 'Trieste', 'TS', '34121', 'ITA',
TRUE, TRUE, 100,
80.00, 150.00, 640.00, 1200.00, 'EUR',
'Dottore Commercialista', 'Consulenza fiscale e aziendale per PMI',
'Aiuto le aziende a crescere attraverso strategie fiscali ottimali e consulenza gestionale. I numeri raccontano sempre la verità.',
'Fiscalità d\'impresa, Bilanci, Revisione legale, Consulenza aziendale', 'SME, Consulting, Manufacturing',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-06-08 11:15:00'),

('Paola', 'Greco', 'GRCPLA88A45O662H', '77890123456', '1988-01-05', 'Udine', 'UD', 'F',
'paola.greco@email.it', 'paola.greco@pec.it', '0432-678901', '+39-348-6789012', 'https://paola-greco-consulting.it',
'Via Mercatovecchio 174', 'Udine', 'UD', '33100', 'ITA',
TRUE, FALSE, 150,
75.00, 140.00, 600.00, 1120.00, 'EUR',
'Consulente Fiscale', 'Specialista in fiscalità internazionale',
'Mi occupo di operazioni cross-border e ottimizzazione fiscale per multinazionali. La complessità è il mio pane quotidiano.',
'Fiscalità internazionale, Transfer pricing, Tax planning, Compliance', 'Multinational, Finance, Tech',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-07-25 15:35:00'),

-- Geometri (10)
('Luca', 'Pellegrini', 'PLLRLC91D08P447G', '88901234567', '1991-04-08', 'Trento', 'TN', 'M',
'luca.pellegrini@email.it', 'luca.pellegrini@pec.it', '0461-789012', '+39-342-7890123', NULL,
'Via Belenzani 285', 'Trento', 'TN', '38122', 'ITA',
TRUE, TRUE, 80,
45.00, 75.00, 360.00, 600.00, 'EUR',
'Geometra Esperto', 'Rilievi topografici e pratiche catastali',
'Realizzo rilievi di precisione e gestisco pratiche edilizie. La precisione millimetrica è il mio standard.',
'Topografia, Catasto, Pratiche edilizie, AutoCAD, GPS/GNSS', 'Construction, Surveying, Real Estate',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-08-12 13:20:00'),

-- Psicologi (10)
('Anna', 'De Angelis', 'DNGNNA86H41Q123F', '99012345678', '1986-06-01', 'Bolzano', 'BZ', 'F',
'anna.deangelis@email.it', 'anna.deangelis@pec.it', '0471-890123', '+39-333-8901234', 'https://anna-deangelis-psi.it',
'Via dei Portici 392', 'Bolzano', 'BZ', '39100', 'ITA',
TRUE, FALSE, 50,
60.00, 100.00, 480.00, 800.00, 'EUR',
'Psicologa Clinica', 'Specialista in terapia cognitivo-comportamentale',
'Aiuto le persone a superare difficoltà emotive e comportamentali. Ogni percorso terapeutico è unico e personalizzato.',
'Terapia cognitivo-comportamentale, Disturbi d\'ansia, Depressione, EMDR', 'Mental Health, Clinical Psychology',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-09-30 10:40:00'),

-- Agronomi (5)
('Michele', 'Carbone', 'CRBMHL79M12R456E', '00123456789', '1979-08-12', 'Aosta', 'AO', 'M',
'michele.carbone@email.it', 'michele.carbone@pec.it', '0165-901234', '+39-349-9012345', NULL,
'Via Croix de Ville 507', 'Aosta', 'AO', '11100', 'ITA',
TRUE, TRUE, 300,
55.00, 90.00, 440.00, 720.00, 'EUR',
'Agronomo Specialista', 'Consulente agricoltura sostenibile e biologica',
'Supporto aziende agricole nella transizione verso pratiche sostenibili. L\'agricoltura del futuro è green.',
'Agricoltura biologica, Sostenibilità, Certificazioni, GIS agricolo', 'Agriculture, Sustainability, Organic',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-10-05 16:25:00'),

-- Medici (5)
('Claudia', 'Esposito', 'SPSCLD84P55S567D', '11123456789', '1984-09-15', 'Milano', 'MI', 'F',
'claudia.esposito@email.it', 'claudia.esposito@pec.it', '02-0123456', '+39-347-0123456', 'https://claudia-esposito-md.it',
'Corso Buenos Aires 678', 'Milano', 'MI', '20124', 'ITA',
TRUE, FALSE, 30,
100.00, 200.00, 800.00, 1600.00, 'EUR',
'Medico Specialista', 'Cardiologo con focus su prevenzione',
'Mi dedico alla cura e prevenzione delle malattie cardiovascolari. La prevenzione è la migliore medicina.',
'Cardiologia, ECG, Ecocardiografia, Prevenzione cardiovascolare', 'Healthcare, Cardiology, Prevention',
'APPROVATO', TRUE, TRUE, TRUE,
'2023-11-15 12:00:00'),

-- Farmacisti (3)
('Riccardo', 'Lombardi', 'LMBRCC89E22T678C', '22234567890', '1989-05-22', 'Roma', 'RM', 'M',
'riccardo.lombardi@email.it', 'riccardo.lombardi@pec.it', '06-1234567', '+39-340-1234567', NULL,
'Via Nazionale 789', 'Roma', 'RM', '00184', 'ITA',
TRUE, FALSE, 40,
45.00, 70.00, 360.00, 560.00, 'EUR',
'Farmacista Clinico', 'Specialista in farmacologia clinica',
'Garantisco l\'uso appropriato dei farmaci e la sicurezza del paziente. La farmacologia è una scienza in continua evoluzione.',
'Farmacologia clinica, Farmacovigilanza, Consulenza farmacologica', 'Pharmacy, Healthcare, Clinical',
'APPROVATO', TRUE, TRUE, FALSE,
'2023-12-01 14:45:00'),

-- Chimici (2)
('Martina', 'Romano', 'RMNMTN92R63U789B', '33345678901', '1992-10-23', 'Napoli', 'NA', 'F',
'martina.romano@email.it', 'martina.romano@pec.it', '081-2345678', '+39-335-2345678', 'https://martina-romano-chem.it',
'Via Toledo 890', 'Napoli', 'NA', '80134', 'ITA',
TRUE, TRUE, 200,
50.00, 85.00, 400.00, 680.00, 'EUR',
'Chimica Industriale', 'Specialista in processi industriali e qualità',
'Ottimizo processi chimici industriali per efficienza e sostenibilità. La chimica è l\'arte di trasformare la materia.',
'Chimica industriale, Controllo qualità, Processi sostenibili, Analisi strumentale', 'Chemical, Manufacturing, Quality',
'APPROVATO', TRUE, TRUE, TRUE,
'2024-01-10 09:30:00');

-- =============================================
-- RELAZIONI PROFILI-ALBI
-- =============================================

INSERT INTO AlboProfili (profilo_id, albo_id, numero_iscrizione, data_iscrizione, data_scadenza, attiva) VALUES
-- Ingegneri (primi 30 profili)
(1, 1, 'ING-MI-12345', '2015-06-15', '2025-06-15', TRUE),
(2, 1, 'ING-RM-23456', '2018-03-20', '2025-03-20', TRUE),
(3, 1, 'ING-NA-34567', '2016-09-10', '2025-09-10', TRUE),
(4, 1, 'ING-TO-45678', '2019-02-05', '2025-02-05', TRUE),
(5, 1, 'ING-FI-56789', '2017-07-15', '2025-07-15', TRUE),
(6, 1, 'ING-BO-67890', '2020-01-20', '2025-01-20', TRUE),
(7, 1, 'ING-BA-78901', '2018-05-12', '2025-05-12', TRUE),
(8, 1, 'ING-CT-89012', '2019-08-25', '2025-08-25', TRUE),
(9, 1, 'ING-VE-90123', '2021-03-08', '2025-03-08', TRUE),
(10, 1, 'ING-GE-01234', '2017-10-18', '2025-10-18', TRUE),

-- Architetti (profili 31-50)
(31, 2, 'ARCH-PA-11111', '2012-01-22', '2025-01-22', TRUE),
(32, 2, 'ARCH-PD-22222', '2018-02-28', '2025-02-28', TRUE),
(33, 2, 'ARCH-VR-33333', '2020-03-15', '2025-03-15', TRUE),

-- Avvocati (profili 51-70)
(51, 3, 'AVV-TV-44444', '2008-04-02', '2025-04-02', TRUE),
(52, 3, 'AVV-VI-55555', '2013-05-20', '2025-05-20', TRUE),

-- Commercialisti (profili 71-85)
(71, 4, 'COMM-TS-66666', '2015-06-08', '2025-06-08', TRUE),
(72, 4, 'COMM-UD-77777', '2018-07-25', '2025-07-25', TRUE),

-- Geometri (profili 86-95)
(86, 5, 'GEOM-TN-88888', '2019-08-12', '2025-08-12', TRUE),

-- Psicologi (profili 96-105)
(96, 6, 'PSI-BZ-99999', '2016-09-30', '2025-09-30', TRUE),

-- Agronomi (profili 106-110)
(106, 7, 'AGRO-AO-00000', '2009-10-05', '2025-10-05', TRUE),

-- Medici (profili 111-115)
(111, 8, 'MED-MI-11223', '2014-11-15', '2025-11-15', TRUE),

-- Farmacisti (profili 116-118)
(116, 9, 'FARM-RM-22334', '2019-12-01', '2025-12-01', TRUE),

-- Chimici (profili 119-120)
(119, 10, 'CHIM-NA-33445', '2022-01-10', '2025-01-10', TRUE);

-- =============================================
-- ISTRUZIONE
-- =============================================

INSERT INTO Istruzione (profilo_id, tipo, titolo, istituto, citta, provincia, nazione, data_conseguimento, voto, descrizione) VALUES
-- Esempi per i primi 20 profili
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
(1, 'TechCorp SPA', 'Sviluppo piattaforma e-commerce scalabile', 'E-commerce', 'Senior Developer', 'Milano', 'MI', 'ITA', '2022-01-15', '2023-06-30', FALSE, 45000.00, 'EUR', 'Java, Spring Boot, React, AWS', 'Incremento performance del 40%, riduzione tempi di caricamento del 60%'),

(1, 'Startup Innovativa', 'Architettura microservizi per fintech', 'Fintech', 'Tech Lead', 'Milano', 'MI', 'ITA', '2023-07-01', NULL, TRUE, 60000.00, 'EUR', 'Kubernetes, Docker, Python, PostgreSQL', 'Sistema in produzione con 99.9% uptime'),

(2, 'Design Agency Roma', 'Redesign completo applicazione mobile banking', 'Banking', 'UX/UI Designer', 'Roma', 'RM', 'ITA', '2021-03-01', '2022-12-15', FALSE, 25000.00, 'EUR', 'Figma, Adobe XD, React, User Research', 'Aumento engagement utenti del 35%, riduzione customer support del 50%'),

(3, 'CloudTech Solutions', 'Migrazione infrastruttura legacy su AWS', 'Technology', 'Cloud Engineer', 'Napoli', 'NA', 'ITA', '2020-09-01', '2023-02-28', FALSE, 80000.00, 'EUR', 'AWS, Terraform, Jenkins, Python', 'Riduzione costi infrastruttura del 30%, miglioramento resilienza sistema');

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
-- LINGUE
-- =============================================

INSERT INTO Lingue (profilo_id, lingua_codice, livello_scritto, livello_parlato, certificazioni) VALUES
-- Esempi per alcuni profili
(1, 'IT', 'MADRELINGUA', 'MADRELINGUA', NULL),
(1, 'EN', 'AVANZATO', 'AVANZATO', 'IELTS 7.5'),
(1, 'FR', 'INTERMEDIO', 'INTERMEDIO', NULL),

(2, 'IT', 'MADRELINGUA', 'MADRELINGUA', NULL),
(2, 'EN', 'AVANZATO', 'AVANZATO', 'Cambridge CAE'),
(2, 'ES', 'BASE', 'BASE', NULL),

(3, 'IT', 'MADRELINGUA', 'MADRELINGUA', NULL),
(3, 'EN', 'INTERMEDIO', 'INTERMEDIO', 'TOEIC 750'),

(4, 'IT', 'MADRELINGUA', 'MADRELINGUA', NULL),
(4, 'EN', 'AVANZATO', 'INTERMEDIO', 'TOEFL 95'),

(5, 'IT', 'MADRELINGUA', 'MADRELINGUA', NULL),
(5, 'EN', 'AVANZATO', 'AVANZATO', 'IELTS 8.0'),
(5, 'DE', 'INTERMEDIO', 'BASE', 'Goethe B1');

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
