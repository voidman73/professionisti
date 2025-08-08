<?php
/**
 * Script per correggere la password dell'amministratore
 * Genera l'hash corretto per la password "admin123"
 */

require_once '../config/database.php';

try {
    echo "<h2>🔧 Correzione Password Amministratore</h2>\n";
    echo "<style>body{font-family:Arial,sans-serif;margin:40px;} .ok{color:green;} .error{color:red;}</style>\n";
    
    // Connessione al database
    $db = new Database();
    $conn = $db->connect();
    
    // Hash corretto per la password "admin123"
    $password = 'admin123';
    $new_hash = '$2y$10$g9u268hzKa25HtpUdedqn.r/Jn0aVBZaoOtNe2dD2hagLv930Bw6q';
    
    echo "<p><strong>Password:</strong> $password</p>\n";
    echo "<p><strong>Nuovo Hash:</strong> $new_hash</p>\n";
    
    // Aggiorna la password nel database
    $query = "UPDATE utenti_admin SET password_hash = :password_hash WHERE username = 'admin'";
    $stmt = $conn->prepare($query);
    $stmt->bindValue(':password_hash', $new_hash);
    
    if ($stmt->execute()) {
        echo "<p class='ok'>✅ Password amministratore aggiornata con successo!</p>\n";
        
        // Verifica che l'aggiornamento sia andato a buon fine
        $verify_query = "SELECT username, email, nome, cognome, ruolo FROM utenti_admin WHERE username = 'admin'";
        $verify_stmt = $conn->prepare($verify_query);
        $verify_stmt->execute();
        $admin = $verify_stmt->fetch();
        
        if ($admin) {
            echo "<h3>📋 Dati Amministratore Aggiornati:</h3>\n";
            echo "<ul>\n";
            echo "<li><strong>Username:</strong> " . htmlspecialchars($admin['username']) . "</li>\n";
            echo "<li><strong>Email:</strong> " . htmlspecialchars($admin['email']) . "</li>\n";
            echo "<li><strong>Nome:</strong> " . htmlspecialchars($admin['nome'] . ' ' . $admin['cognome']) . "</li>\n";
            echo "<li><strong>Ruolo:</strong> " . htmlspecialchars($admin['ruolo']) . "</li>\n";
            echo "</ul>\n";
            
            echo "<h3>🔑 Credenziali di Accesso:</h3>\n";
            echo "<div style='background:#f0f8ff;padding:15px;border-radius:5px;'>\n";
            echo "<p><strong>URL:</strong> <a href='../backend/login.php'>../backend/login.php</a></p>\n";
            echo "<p><strong>Username:</strong> <code>admin</code></p>\n";
            echo "<p><strong>Password:</strong> <code>admin123</code></p>\n";
            echo "</div>\n";
        }
        
    } else {
        echo "<p class='error'>❌ Errore nell'aggiornamento della password</p>\n";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>❌ Errore: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}

echo "<h3>⚠️ Importante:</h3>\n";
echo "<p>Per sicurezza, <strong>cambia questa password</strong> dopo il primo accesso!</p>\n";
?>