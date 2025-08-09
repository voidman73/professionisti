<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/Professionista.php';
require_once '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

// Controllo autenticazione
if (!isset($_SESSION['admin_id'])) {
    die('Non autorizzato');
}

try {
    $professionista = new Professionista();
    
    // Determina il formato di esportazione
    $formato = $_GET['formato'] ?? 'csv';
    if (!in_array($formato, ['csv', 'xlsx', 'pdf'])) {
        $formato = 'csv';
    }
    
    // Determina quali professionisti esportare
    if (isset($_GET['ids']) && !empty($_GET['ids'])) {
        // Esporta solo i selezionati
        $ids = explode(',', $_GET['ids']);
        $filtri = ['ids' => $ids];
        $nome_file = 'professionisti_selezionati_' . date('Y-m-d_H-i-s');
    } else {
        // Esporta tutti con eventuali filtri dalla dashboard
        $filtri = [];
        
        // Applica gli stessi filtri della dashboard se presenti
        if (isset($_GET['albo_id']) && !empty($_GET['albo_id'])) {
            $filtri['albo_id'] = $_GET['albo_id'];
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
        if (isset($_GET['competenze']) && !empty($_GET['competenze'])) {
            $filtri['competenze'] = $_GET['competenze'];
        }
        if (isset($_GET['anni_esperienza_min']) && !empty($_GET['anni_esperienza_min'])) {
            $filtri['anni_esperienza_min'] = $_GET['anni_esperienza_min'];
        }
        
        $nome_file = 'professionisti_export_' . date('Y-m-d_H-i-s');
    }
    
    // Ottieni i dati
    if (isset($filtri['ids'])) {
        $dati = $professionista->ottieniPerIds($filtri['ids']);
    } else {
        $dati = $professionista->ottieniTutti($filtri);
    }
    
    // Se non ci sono dati
    if (empty($dati)) {
        die('Nessun dato da esportare');
    }
    
    // Headers per i dati
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
        'Indirizzo Domicilio',
        'Città Domicilio',
        'Provincia Domicilio',
        'CAP Domicilio',
        'Competenze Principali',
        'Titolo Professionale',
        'Disponibile',
        'Disponibilità Trasferte',
        'Raggio Azione (km)',
        'Tariffa Oraria Min',
        'Tariffa Oraria Max',
        'Tariffa Giornaliera Min',
        'Tariffa Giornaliera Max',
        'Note Tariffe',
        'Stato',
        'Data Registrazione',
        'Privacy Accettata',
        'Termini Accettati'
    ];
    
    // Prepara i dati per l'esportazione
    $dati_esportazione = [];
    foreach ($dati as $row) {
        $dati_esportazione[] = [
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
            $row['indirizzo_domicilio'] ?: '',
            $row['citta_domicilio'] ?: '',
            $row['provincia_domicilio'] ?: '',
            $row['cap_domicilio'] ?: '',
            str_replace(["\r\n", "\r", "\n"], ' | ', $row['competenze_principali'] ?: ''),
            $row['titolo_professionale'] ?: '',
            $row['disponibile'] ? 'Sì' : 'No',
            $row['disponibilita_trasferte'] ? 'Sì' : 'No',
            $row['raggio_azione_km'] ?: '',
            $row['tariffa_oraria_min'] ? '€ ' . number_format($row['tariffa_oraria_min'], 2, ',', '.') : '',
            $row['tariffa_oraria_max'] ? '€ ' . number_format($row['tariffa_oraria_max'], 2, ',', '.') : '',
            $row['tariffa_giornaliera_min'] ? '€ ' . number_format($row['tariffa_giornaliera_min'], 2, ',', '.') : '',
            $row['tariffa_giornaliera_max'] ? '€ ' . number_format($row['tariffa_giornaliera_max'], 2, ',', '.') : '',
            str_replace(["\r\n", "\r", "\n"], ' | ', $row['note_tariffe'] ?: ''),
            $row['stato'] ?: '',
            $row['data_registrazione'] ? date('d/m/Y H:i', strtotime($row['data_registrazione'])) : '',
            $row['privacy_accettata'] ? 'Sì' : 'No',
            $row['termini_accettati'] ? 'Sì' : 'No'
        ];
    }
    
    // Esporta nel formato richiesto
    switch ($formato) {
        case 'xlsx':
            esportaXLSX($headers, $dati_esportazione, $nome_file);
            break;
        case 'pdf':
            esportaPDF($headers, $dati_esportazione, $nome_file);
            break;
        default:
            esportaCSV($headers, $dati_esportazione, $nome_file);
            break;
    }
    
} catch (Exception $e) {
    die('Errore nell\'esportazione: ' . $e->getMessage());
}

/**
 * Esporta in formato CSV
 */
function esportaCSV($headers, $dati, $nome_file) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $nome_file . '.csv"');
    header('Cache-Control: must-revalidate');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
    
    fputcsv($output, $headers, ';');
    
    foreach ($dati as $row) {
        fputcsv($output, $row, ';');
    }
    
    fclose($output);
}

/**
 * Esporta in formato XLSX
 */
function esportaXLSX($headers, $dati, $nome_file) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Imposta il titolo del foglio
    $sheet->setTitle('Professionisti');
    
    // Prepara i dati per fromArray (headers + dati)
    $allData = array_merge([$headers], $dati);
    
    // Popola il foglio con fromArray
    $sheet->fromArray($allData, null, 'A1');
    
    // Stile per gli headers
    $headerStyle = [
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF'],
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];
    
    $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
    
    // Auto-dimensiona le colonne
    foreach (range('A', $sheet->getHighestColumn()) as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Headers per download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $nome_file . '.xlsx"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
}

/**
 * Esporta in formato PDF
 */
function esportaPDF($headers, $dati, $nome_file) {
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isPhpEnabled', true);
    
    $dompdf = new Dompdf($options);
    
    // Crea l'HTML per il PDF
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Esportazione Professionisti</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            th, td { border: 1px solid #ddd; padding: 4px; text-align: left; }
            th { background-color: #4472C4; color: white; font-weight: bold; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            .header { text-align: center; margin-bottom: 20px; }
            .header h1 { color: #4472C4; margin: 0; }
            .header p { color: #666; margin: 5px 0; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>Elenco Professionisti</h1>
            <p>Data esportazione: ' . date('d/m/Y H:i') . '</p>
            <p>Totale professionisti: ' . count($dati) . '</p>
        </div>
        <table>
            <thead>
                <tr>';
    
    foreach ($headers as $header) {
        $html .= '<th>' . htmlspecialchars($header) . '</th>';
    }
    
    $html .= '</tr></thead><tbody>';
    
    foreach ($dati as $row) {
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= '<td>' . htmlspecialchars($cell) . '</td>';
        }
        $html .= '</tr>';
    }
    
    $html .= '</tbody></table></body></html>';
    
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    
    // Headers per download
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $nome_file . '.pdf"');
    header('Cache-Control: private, max-age=0, must-revalidate');
    
    echo $dompdf->output();
}
?>