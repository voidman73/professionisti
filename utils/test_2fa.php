<?php
/**
 * Script di test per il nuovo sistema 2FA TOTP
 * Mostra la generazione del segreto, il QR e la verifica codice
 */

require_once '../config/database.php';
require_once '../classes/Totp.php';

$database = new Database();
$db = $database->connect();

echo "<h1>🧪 Test Sistema 2FA TOTP</h1>";

// 1. Generazione segreto
$secret = Totp::generateSecret();
echo "<h2>1. Generazione Segreto</h2>";
echo "Segreto generato: <code>$secret</code><br>";
echo "Lunghezza: " . strlen($secret) . " caratteri Base32<br><br>";

// 2. Provisioning URI & QR
$testEmail = "test@example.com";
$issuer = "ZPeC Professionisti";
$uri = Totp::getProvisioningUri($testEmail, $secret, $issuer);
$qr = Totp::getQrCodeUrl($uri);

echo "<h2>2. Provisioning URI & QR</h2>";
echo "URI: <code>" . htmlspecialchars($uri) . "</code><br>";
echo "QR Code (Google Charts):<br>";
echo "<img src='" . htmlspecialchars($qr) . "' alt='QR Code' style='margin:15px 0;'>";
echo "<br><small>Scansiona con Google Authenticator per vedere il codice generato.</small><br><br>";

// 3. Codice corrente
$currentCode = Totp::getCode($secret);
echo "<h2>3. Codice Corrente</h2>";
echo "Codice generato ora: <strong>" . ($currentCode ?? 'N/D') . "</strong><br>";
echo "<small>Il codice cambia ogni 30 secondi. Usa questo valore per i test manuali.</small><br><br>";

// 4. Verifica codice
echo "<h2>4. Verifica Codice</h2>";
$isValid = $currentCode ? Totp::verifyCode($secret, $currentCode) : false;
echo "Verifica immediata: " . ($isValid ? '✅ OK' : '❌ Errore') . "<br>";
echo "<small>La verifica considera una finestra di ±30 secondi per tollerare piccoli sfasamenti orari.</small><br><br>";

// 5. Stato utenti amministratori
echo "<h2>5. Utenti amministratori attivi (prime 5 voci)</h2>";
$stmt = $db->query("SELECT id, username, email, ruolo, totp_enabled FROM utenti_admin WHERE attivo = 1 LIMIT 5");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($rows) {
    echo "<table border='1' cellpadding='6' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Ruolo</th><th>TOTP attivo</th></tr>";
    foreach ($rows as $row) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>" . htmlspecialchars($row['ruolo']) . "</td>";
        echo "<td>" . ($row['totp_enabled'] ? 'Sì' : 'No') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br><strong>💡 Suggerimento:</strong> usa una di queste email amministrative per provare il login 2FA.";
} else {
    echo "Nessun utente amministratore attivo trovato. Popola o abilita `utenti_admin` prima del test.";
}

// 6. Log di test
echo "<h2>6. Log di test</h2>";
try {
    $stmt = $db->prepare("INSERT INTO auth_logs (email, ip_address, user_agent, action, details) VALUES (?, ?, ?, 'TEST', ?)");
    $result = $stmt->execute([$testEmail, $_SERVER['REMOTE_ADDR'] ?? 'CLI', $_SERVER['HTTP_USER_AGENT'] ?? 'SCRIPT', 'Test automatico TOTP']);
    echo "Log inserito: " . ($result ? '✅' : '❌') . "<br>";
} catch (Exception $e) {
    echo "Impossibile inserire il log: " . htmlspecialchars($e->getMessage());
}

echo "<br><hr>";
echo "<h3>✅ Test Completato!</h3>";
echo "<p>Ora puoi aprire <a href='../backend/login_2fa.php'>backend/login_2fa.php</a> e testare il flusso completo con la tua app di autenticazione.</p>";
?>
