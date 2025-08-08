<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/Professionista.php';

// Controllo autenticazione
if (!isset($_SESSION['admin_id'])) {
    die('Non autorizzato');
}

try {
    $professionista = new Professionista();
    
    // Determina quali professionisti esportare
    if (isset($_GET['ids']) && !empty($_GET['ids'])) {
        // Esporta solo i selezionati
        $ids = explode(',', $_GET['ids']);
        $filtri = ['ids' => $ids];
        $nome_file = 'professionisti_selezionati_' . date('Y-m-d_H-i-s') . '.csv';
    } else {
        // Esporta tutti con eventuali filtri dalla dashboard
        $filtri = [];
        
        // Applica gli stessi filtri della dashboard se presenti
        if (isset($_GET['categoria_id']) && !empty($_GET['categoria_id'])) {
            $filtri['categoria_id'] = $_GET['categoria_id'];
        }
        if (isset($_GET['stato']) && !empty($_GET['stato'])) {
            $filtri['stato'] = $_GET['stato'];
        }
        if (isset($_GET['disponibile']) && $_GET['disponibile'] !== '') {
            $filtri['disponibile'] = $_GET['disponibile'];
        }
        if (isset($_GET['citta']) && !empty($_GET['citta'])) {
            $filtri['citta'] = $_GET['citta'];
        }
        
        $nome_file = 'professionisti_export_' . date('Y-m-d_H-i-s') . '.csv';
    }
    
    // Modifica la classe Professionista per supportare esportazione per IDs
    if (isset($filtri['ids'])) {
        $dati = $professionista->ottieniPerIds($filtri['ids']);
    } else {
        $dati = $professionista->ottieniTutti($filtri);
    }
    
    // Se non ci sono dati
    if (empty($dati)) {
        die('Nessun dato da esportare');
    }
    
    // Headers per download CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nome_file . '"');
    header('Cache-Control: must-revalidate');
    
    // Crea il file CSV
    $output = fopen('php://output', 'w');
    
    // BOM per Excel UTF-8
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    
    // Headers CSV
    $headers = [
        'ID',
        'Nome',
        'Cognome',
        'Codice Fiscale',
        'Partita IVA',
        'Email',
        'Telefono',
        'Cellulare',
        'Data Nascita',
        'Luogo Nascita',
        'Sesso',
        'Indirizzo Residenza',
        'Città Residenza',
        'Provincia Residenza',
        'CAP Residenza',
        'Categoria',
        'Specializzazione',
        'Anni Esperienza',
        'Numero Albo',
        'Albo Professionale',
        'Data Iscrizione Albo',
        'Competenze',
        'Lingue Parlate',
        'Disponibile',
        'Disponibilità Trasferte',
        'Raggio Azione (km)',
        'Tariffa Oraria',
        'Tariffa Giornaliera',
        'Note Tariffe',
        'Stato',
        'Note Amministrative',
        'Data Iscrizione',
        'Privacy Accettata',
        'Termini Accettati'
    ];
    
    fputcsv($output, $headers, ';');
    
    // Dati
    foreach ($dati as $row) {
        $csv_row = [
            $row['id'],
            $row['nome'],
            $row['cognome'],
            $row['codice_fiscale'],
            $row['partita_iva'] ?: '',
            $row['email'],
            $row['telefono'] ?: '',
            $row['cellulare'] ?: '',
            $row['data_nascita'] ? date('d/m/Y', strtotime($row['data_nascita'])) : '',
            $row['luogo_nascita'] ?: '',
            $row['sesso'] == 'M' ? 'Maschio' : ($row['sesso'] == 'F' ? 'Femmina' : 'Non specificato'),
            $row['indirizzo_residenza'] ?: '',
            $row['citta_residenza'] ?: '',
            $row['provincia_residenza'] ?: '',
            $row['cap_residenza'] ?: '',
            $row['categoria_nome'] ?: '',
            $row['specializzazione_nome'] ?: '',
            $row['anni_esperienza'],
            $row['numero_iscrizione_albo'] ?: '',
            $row['albo_professionale'] ?: '',
            $row['data_iscrizione_albo'] ? date('d/m/Y', strtotime($row['data_iscrizione_albo'])) : '',
            str_replace(["\r\n", "\r", "\n"], ' | ', $row['competenze'] ?: ''),
            $row['lingue_parlate'] ?: '',
            $row['disponibile'] ? 'Sì' : 'No',
            $row['disponibilita_trasferte'] ? 'Sì' : 'No',
            $row['raggio_azione_km'],
            $row['tariffa_oraria'] ? '€ ' . number_format($row['tariffa_oraria'], 2, ',', '.') : '',
            $row['tariffa_giornaliera'] ? '€ ' . number_format($row['tariffa_giornaliera'], 2, ',', '.') : '',
            str_replace(["\r\n", "\r", "\n"], ' | ', $row['note_tariffe'] ?: ''),
            $row['stato'],
            str_replace(["\r\n", "\r", "\n"], ' | ', $row['note_amministrative'] ?: ''),
            date('d/m/Y H:i', strtotime($row['data_iscrizione'])),
            $row['privacy_accettata'] ? 'Sì' : 'No',
            $row['termini_accettati'] ? 'Sì' : 'No'
        ];
        
        fputcsv($output, $csv_row, ';');
    }
    
    fclose($output);
    
} catch (Exception $e) {
    die('Errore nell\'esportazione: ' . $e->getMessage());
}
?>