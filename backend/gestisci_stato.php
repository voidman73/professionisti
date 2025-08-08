<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/Professionista.php';

// Controllo autenticazione
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'error' => 'Non autorizzato']);
    exit;
}

header('Content-Type: application/json');

try {
    $professionista = new Professionista();
    
    if (!isset($_POST['stato'])) {
        throw new Exception("Stato non specificato");
    }
    
    $nuovo_stato = $_POST['stato'];
    $note = $_POST['note'] ?? '';
    
    // Validazione stato
    $stati_validi = ['PENDENTE', 'APPROVATO', 'RESPINTO', 'SOSPESO'];
    if (!in_array($nuovo_stato, $stati_validi)) {
        throw new Exception("Stato non valido");
    }
    
    // Gestione singolo ID o multipli IDs
    if (isset($_POST['id'])) {
        // Singolo professionista
        $id = (int)$_POST['id'];
        $result = $professionista->aggiornaStato($id, $nuovo_stato, $note);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Stato aggiornato con successo']);
        } else {
            throw new Exception("Errore nell'aggiornamento dello stato");
        }
    } elseif (isset($_POST['ids'])) {
        // Multipli professionisti
        $ids = explode(',', $_POST['ids']);
        $successi = 0;
        $errori = 0;
        
        foreach ($ids as $id) {
            $id = (int)trim($id);
            if ($id > 0) {
                if ($professionista->aggiornaStato($id, $nuovo_stato, $note)) {
                    $successi++;
                } else {
                    $errori++;
                }
            }
        }
        
        if ($errori > 0) {
            echo json_encode([
                'success' => false, 
                'error' => "Aggiornati $successi, errori $errori"
            ]);
        } else {
            echo json_encode([
                'success' => true, 
                'message' => "Aggiornati con successo $successi professionisti"
            ]);
        }
    } else {
        throw new Exception("ID professionista non specificato");
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>