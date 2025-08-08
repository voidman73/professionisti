<?php
/**
 * Test rapido del sistema ZPeC dopo la migrazione
 */

require_once '../config/database.php';
require_once '../classes/Professionista.php';
require_once '../classes/Albo.php';
require_once '../classes/Admin.php';

echo "<h2>🧪 Test Sistema ZPeC</h2>\n";
echo "<style>
body{font-family:Arial,sans-serif;margin:40px;background:#f8f9fa;} 
.ok{color:#8B0000;font-weight:bold;} 
.error{color:#dc3545;} 
.warning{color:#ffc107;color:#000;}
h2{color:#8B0000;}
h3{color:#A52A2A;}
a{color:#8B0000;}
</style>\n";

try {
    // Test 1: Connessione Database
    echo "<h3>1. Test Connessione Database</h3>\n";
    $db = new Database();
    $conn = $db->connect();
    echo "<p class='ok'>✅ Connessione riuscita</p>\n";
    
    // Test 2: Classe Professionista
    echo "<h3>2. Test Classe Professionista</h3>\n";
    $prof = new Professionista();
    
    // Test statistiche
    try {
        $stats = $prof->ottieniStatistiche();
        echo "<p class='ok'>✅ Statistiche professionisti: " . $stats['totale'] . " totali</p>\n";
        echo "<p class='ok'>✅ Disponibili: " . $stats['disponibili'] . "</p>\n";
        echo "<p class='ok'>✅ Nuove registrazioni ultimo mese: " . $stats['nuove_ultimo_mese'] . "</p>\n";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Errore statistiche: " . $e->getMessage() . "</p>\n";
    }
    
    // Test lista professionisti
    try {
        $professionisti = $prof->ottieniTutti();
        echo "<p class='ok'>✅ Lista professionisti recuperata: " . count($professionisti) . " trovati</p>\n";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Errore lista professionisti: " . $e->getMessage() . "</p>\n";
    }
    
    // Test 3: Classe Albo
    echo "<h3>3. Test Classe Albo</h3>\n";
    $albo = new Albo();
    
    try {
        $albi = $albo->ottieniTutti();
        echo "<p class='ok'>✅ Albi professionali: " . count($albi) . " trovati</p>\n";
        
        foreach ($albi as $a) {
            echo "<p class='ok'>  - " . htmlspecialchars($a['nome']) . " (ID: " . $a['id'] . ")</p>\n";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Errore albi: " . $e->getMessage() . "</p>\n";
    }
    
    // Test statistiche albi
    try {
        $stats_albi = $albo->ottieniStatistiche();
        echo "<p class='ok'>✅ Statistiche albi recuperate</p>\n";
    } catch (Exception $e) {
        echo "<p class='error'>❌ Errore statistiche albi: " . $e->getMessage() . "</p>\n";
    }
    
    // Test 4: Classe Admin
    echo "<h3>4. Test Classe Admin</h3>\n";
    $admin = new Admin();
    
    try {
        $admins = $admin->ottieniTutti();
        echo "<p class='ok'>✅ Amministratori: " . count($admins) . " trovati</p>\n";
        
        foreach ($admins as $a) {
            echo "<p class='ok'>  - " . htmlspecialchars($a['username']) . " (" . $a['ruolo'] . ")</p>\n";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Errore admin: " . $e->getMessage() . "</p>\n";
    }
    
    // Test 5: Test Password Admin
    echo "<h3>5. Test Password Admin</h3>\n";
    try {
        $test_login = $admin->autenticaUtente('admin', 'admin123');
        if ($test_login) {
            echo "<p class='ok'>✅ Password admin123 funziona correttamente</p>\n";
        } else {
            echo "<p class='error'>❌ Password admin123 non funziona</p>\n";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Errore test login: " . $e->getMessage() . "</p>\n";
    }
    
    // Test 6: Verifica Tabelle Critiche
    echo "<h3>6. Verifica Tabelle ZPeC</h3>\n";
    $tabelle_critiche = [
        'Profili' => 'Professionisti registrati',
        'Albo' => 'Albi professionali',
        'AlboProfili' => 'Relazioni professionisti-albi',
        'Province' => 'Province italiane',
        'utenti_admin' => 'Amministratori sistema'
    ];
    
    foreach ($tabelle_critiche as $tabella => $descrizione) {
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `$tabella`");
            $stmt->execute();
            $count = $stmt->fetch()['count'];
            echo "<p class='ok'>✅ $descrizione ($tabella): $count record</p>\n";
        } catch (Exception $e) {
            echo "<p class='error'>❌ $descrizione ($tabella): " . $e->getMessage() . "</p>\n";
        }
    }
    
    echo "<h3>✅ Test Completato</h3>\n";
    echo "<p><strong>Sistema pronto per l'uso!</strong></p>\n";
    
    echo "<h3>🔗 Link Utili (Localhost)</h3>\n";
    echo "<ul>\n";
    echo "<li><a href='../frontend/index.php'>Homepage Frontend</a></li>\n";
    echo "<li><a href='../frontend/registrazione.php'>Form Registrazione</a></li>\n";
    echo "<li><a href='../backend/login.php'>Login Backend</a> (admin / admin123)</li>\n";
    echo "<li><a href='../backend/ricerca_avanzata.php'>Ricerca Avanzata</a></li>\n";
    echo "<li><a href='../api/index.php?status'>API Status</a></li>\n";
    echo "<li><a href='../demo_colori.php'>🎨 Demo Nuova Colorazione</a></li>\n";
    echo "</ul>\n";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Errore generale: " . $e->getMessage() . "</p>\n";
}
?>