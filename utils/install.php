<?php
/**
 * Script di installazione e verifica sistema
 * Esegui questo script per verificare che tutto sia configurato correttamente
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Verifica Installazione Sistema Professionisti</h1>\n";
echo "<style>
body{font-family:Arial,sans-serif;margin:40px;background:#f8f9fa;} 
.ok{color:#8B0000;font-weight:bold;} 
.error{color:#dc3545;} 
.warning{color:#ffc107;color:#000;}
h1{color:#8B0000;}
h2{color:#A52A2A;}
</style>\n";

// Verifica versione PHP
echo "<h2>📋 Verifica Requisiti Sistema</h2>\n";

echo "<h3>PHP</h3>\n";
$php_version = phpversion();
if (version_compare($php_version, '8.0.0', '>=')) {
    echo "<p class='ok'>✅ PHP $php_version (OK)</p>\n";
} else {
    echo "<p class='error'>❌ PHP $php_version - Richiesta versione 8.0+</p>\n";
}

// Verifica estensioni PHP
echo "<h3>Estensioni PHP</h3>\n";
$required_extensions = ['pdo', 'pdo_mysql', 'mbstring', 'openssl', 'json'];
foreach ($required_extensions as $ext) {
    if (extension_loaded($ext)) {
        echo "<p class='ok'>✅ $ext</p>\n";
    } else {
        echo "<p class='error'>❌ $ext - ESTENSIONE MANCANTE</p>\n";
    }
}

// Verifica configurazione database
echo "<h2>🗄️ Verifica Database</h2>\n";

try {
    require_once '../config/database.php';
    
    $db = new Database();
    $conn = $db->connect();
    echo "<p class='ok'>✅ Connessione al database riuscita</p>\n";
    
    // Verifica tabelle
    $tables = [
        'Profili',
        'Albo', 
        'AlboProfili',
        'Istruzione',
        'LavoroAut',
        'LavoroSub',
        'Lingue',
        'IT',
        'Allegati',
        'Province',
        'ulkISO3166',
        'utenti_admin'
    ];
    
    echo "<h3>Tabelle Database</h3>\n";
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SHOW TABLES LIKE :table");
        $stmt->bindValue(':table', $table);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            echo "<p class='ok'>✅ $table</p>\n";
        } else {
            echo "<p class='error'>❌ $table - TABELLA MANCANTE</p>\n";
        }
    }
    
    // Verifica dati di base
    echo "<h3>Dati di Base</h3>\n";
    
    // Albi
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM Albo");
    $stmt->execute();
    $count = $stmt->fetch()['count'];
    if ($count > 0) {
        echo "<p class='ok'>✅ Albi professionali: $count</p>\n";
    } else {
        echo "<p class='warning'>⚠️ Nessun albo professionale trovato</p>\n";
    }
    
    // Admin users
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM utenti_admin");
    $stmt->execute();
    $count = $stmt->fetch()['count'];
    if ($count > 0) {
        echo "<p class='ok'>✅ Utenti amministratori: $count</p>\n";
    } else {
        echo "<p class='warning'>⚠️ Nessun utente amministratore trovato</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Errore database: " . $e->getMessage() . "</p>\n";
}

// Verifica file e permessi
echo "<h2>📁 Verifica File e Permessi</h2>\n";

$files_to_check = [
    '../config/database.php' => 'File configurazione database',
    '../classes/Admin.php' => 'Classe Admin',
    '../classes/Professionista.php' => 'Classe Professionista',
    '../classes/Categoria.php' => 'Classe Categoria',
    '../frontend/index.php' => 'Homepage frontend',
    '../frontend/registrazione.php' => 'Form registrazione',
    '../backend/login.php' => 'Login backend',
    '../backend/dashboard.php' => 'Dashboard backend'
];

foreach ($files_to_check as $file => $description) {
    if (file_exists($file)) {
        if (is_readable($file)) {
            echo "<p class='ok'>✅ $description</p>\n";
        } else {
            echo "<p class='error'>❌ $description - NON LEGGIBILE</p>\n";
        }
    } else {
        echo "<p class='error'>❌ $description - FILE MANCANTE</p>\n";
    }
}

// Verifica configurazione web server
echo "<h2>🌐 Verifica Web Server</h2>\n";

$server_software = $_SERVER['SERVER_SOFTWARE'] ?? 'Sconosciuto';
echo "<p>Server: $server_software</p>\n";

// Verifica URL rewriting
if (isset($_SERVER['REQUEST_URI'])) {
    echo "<p class='ok'>✅ REQUEST_URI disponibile</p>\n";
} else {
    echo "<p class='warning'>⚠️ REQUEST_URI non disponibile</p>\n";
}

// Test di sicurezza
echo "<h2>🔒 Test di Sicurezza</h2>\n";

// Verifica che le directory sensibili non siano accessibili direttamente
$protected_dirs = ['../config/', '../classes/'];
foreach ($protected_dirs as $dir) {
    if (is_dir($dir)) {
        echo "<p class='warning'>⚠️ Directory $dir esistente - Assicurati che non sia accessibile via web</p>\n";
    }
}

// Raccomandazioni finali
echo "<h2>📝 Raccomandazioni</h2>\n";
echo "<ul>\n";
echo "<li>✅ Cambia la password dell'utente admin default</li>\n";
echo "<li>✅ Configura HTTPS per l'ambiente di produzione</li>\n";
echo "<li>✅ Proteggi le directory config/ e classes/ nel web server</li>\n";
echo "<li>✅ Configura backup automatici del database</li>\n";
echo "<li>✅ Disabilita display_errors in produzione</li>\n";
echo "</ul>\n";

// Link di test
echo "<h2>🔗 Link di Test</h2>\n";
$base_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI'], 2);
echo "<ul>\n";
echo "<li><a href='$base_url/frontend/index.php' target='_blank'>Homepage Frontend</a></li>\n";
echo "<li><a href='$base_url/frontend/registrazione.php' target='_blank'>Form Registrazione</a></li>\n";
echo "<li><a href='$base_url/backend/login.php' target='_blank'>Login Backend</a></li>\n";
echo "</ul>\n";

echo "<h2>✅ Installazione Completata</h2>\n";
echo "<p>Se tutti i controlli sono positivi, il sistema è pronto all'uso!</p>\n";
echo "<p><strong>Credenziali admin default:</strong> admin / admin123</p>\n";
?>