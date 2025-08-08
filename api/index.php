<?php
/**
 * API REST semplice per il sistema professionisti
 * Endpoint base per integrazioni future
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Gestione preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once '../config/database.php';
require_once '../config/security.php';
require_once '../classes/Professionista.php';
require_once '../classes/Albo.php';
require_once '../classes/Provincia.php';
require_once '../classes/Istruzione.php';
require_once '../classes/EsperienzaLavorativa.php';
require_once '../classes/Lingue.php';
require_once '../classes/CompetenzeIT.php';
require_once '../classes/Allegato.php';

// Classe per gestire le risposte API
class APIResponse {
    public static function success($data = null, $message = 'Success') {
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('c')
        ]);
        exit;
    }
    
    public static function error($message = 'Error', $code = 400, $details = null) {
        http_response_code($code);
        echo json_encode([
            'success' => false,
            'message' => $message,
            'details' => $details,
            'timestamp' => date('c')
        ]);
        exit;
    }
}

// Router semplice
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// Rimuovi 'api' dal path
if ($path_parts[0] === 'api' || end($path_parts) === 'index.php') {
    array_shift($path_parts);
    if (end($path_parts) === 'index.php') {
        array_pop($path_parts);
    }
}

$method = $_SERVER['REQUEST_METHOD'];
$endpoint = $path_parts[0] ?? '';

try {
    switch ($endpoint) {
        case 'albi':
            handleAlbiAPI($method, $path_parts);
            break;
            
        case 'professionisti':
            handleProfessionistiAPI($method, $path_parts);
            break;
            
        case 'province':
            handleProvinceAPI($method, $path_parts);
            break;
            
        case 'istruzione':
            handleIstruzioneAPI($method, $path_parts);
            break;
            
        case 'esperienze':
            handleEsperienzeAPI($method, $path_parts);
            break;
            
        case 'lingue':
            handleLingueAPI($method, $path_parts);
            break;
            
        case 'competenze-it':
            handleCompetenzeITAPI($method, $path_parts);
            break;
            
        case 'allegati':
            handleAllegatiAPI($method, $path_parts);
            break;
            
        case 'status':
            APIResponse::success([
                'version' => '2.0.0',
                'service' => 'ZPeC Professionisti API',
                'endpoints' => [
                    'GET /api/albi' => 'Lista albi professionali',
                    'GET /api/albi/{id}/professionisti' => 'Professionisti iscritti a un albo',
                    'GET /api/professionisti' => 'Lista professionisti (con filtri)',
                    'POST /api/professionisti' => 'Crea nuovo professionista',
                    'GET /api/professionisti/{id}' => 'Dettagli professionista',
                    'GET /api/province' => 'Lista province italiane',
                    'GET /api/province/{sigla}' => 'Dettagli provincia',
                    'GET /api/istruzione/{profilo_id}' => 'Titoli di studio professionista',
                    'GET /api/esperienze/{profilo_id}' => 'Esperienze lavorative professionista',
                    'GET /api/lingue/{profilo_id}' => 'Competenze linguistiche professionista',
                    'GET /api/competenze-it/{profilo_id}' => 'Competenze informatiche professionista',
                    'GET /api/allegati/{profilo_id}' => 'Allegati professionista'
                ]
            ], 'ZPeC API Service Online');
            break;
            
        default:
            APIResponse::error('Endpoint non trovato', 404);
    }
    
} catch (Exception $e) {
    SecurityConfig::logSecurityEvent('api_error', [
        'endpoint' => $endpoint,
        'method' => $method,
        'error' => $e->getMessage()
    ]);
    
    APIResponse::error('Errore interno del server', 500, $e->getMessage());
}

/**
 * Gestisce API per albi professionali
 */
function handleAlbiAPI($method, $path_parts) {
    $albo = new Albo();
    
    switch ($method) {
        case 'GET':
            if (isset($path_parts[1]) && is_numeric($path_parts[1])) {
                // GET /api/albi/{id}
                if (isset($path_parts[2]) && $path_parts[2] === 'professionisti') {
                    // GET /api/albi/{id}/professionisti
                    $professionisti = $albo->ottieniProfessionistiAlbo($path_parts[1]);
                    APIResponse::success($professionisti, 'Professionisti albo recuperati');
                } else {
                    // GET /api/albi/{id}
                    $albo_item = $albo->ottieniPerId($path_parts[1]);
                    if ($albo_item) {
                        APIResponse::success($albo_item, 'Albo recuperato');
                    } else {
                        APIResponse::error('Albo non trovato', 404);
                    }
                }
            } else {
                // GET /api/albi
                $albi = $albo->ottieniTutti();
                APIResponse::success($albi, 'Albi recuperati');
            }
            break;
            
        default:
            APIResponse::error('Metodo non supportato', 405);
    }
}

/**
 * Gestisce API per professionisti
 */
function handleProfessionistiAPI($method, $path_parts) {
    $professionista = new Professionista();
    
    switch ($method) {
        case 'GET':
            if (isset($path_parts[1]) && is_numeric($path_parts[1])) {
                // GET /api/professionisti/{id}
                $prof = $professionista->ottieniPerId($path_parts[1]);
                if ($prof) {
                    // Rimuovi dati sensibili per API pubblica
                    unset($prof['codice_fiscale'], $prof['partita_iva'], $prof['indirizzo_residenza'], 
                          $prof['indirizzo_domicilio'], $prof['note_amministrative']);
                    APIResponse::success($prof, 'Professionista recuperato');
                } else {
                    APIResponse::error('Professionista non trovato', 404);
                }
            } else {
                // GET /api/professionisti con filtri
                $filtri = [];
                
                // Applica filtri da query string
                if (isset($_GET['albo_id'])) {
                    $filtri['albo_id'] = $_GET['albo_id'];
                }
                if (isset($_GET['disponibile'])) {
                    $filtri['disponibile'] = $_GET['disponibile'];
                }
                if (isset($_GET['citta'])) {
                    $filtri['citta'] = $_GET['citta'];
                }
                if (isset($_GET['provincia'])) {
                    $filtri['provincia'] = $_GET['provincia'];
                }
                
                // Solo professionisti approvati per API pubblica
                $filtri['stato'] = 'APPROVATO';
                
                $professionisti = $professionista->ottieniTutti($filtri);
                
                // Rimuovi dati sensibili
                foreach ($professionisti as &$prof) {
                    unset($prof['codice_fiscale'], $prof['partita_iva'], $prof['indirizzo_residenza'], 
                          $prof['indirizzo_domicilio'], $prof['note_amministrative']);
                }
                
                APIResponse::success($professionisti, 'Professionisti recuperati');
            }
            break;
            
        case 'POST':
            // POST /api/professionisti - Registrazione
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                APIResponse::error('Dati JSON non validi', 400);
            }
            
            // Validazione base
            $required_fields = ['nome', 'cognome', 'email', 'codice_fiscale'];
            foreach ($required_fields as $field) {
                if (empty($input[$field])) {
                    APIResponse::error("Campo $field obbligatorio", 400);
                }
            }
            
            // Validazioni di sicurezza
            if (!SecurityConfig::validateEmail($input['email'])) {
                APIResponse::error('Email non valida', 400);
            }
            
            if (!SecurityConfig::validateCodiceFiscale($input['codice_fiscale'])) {
                APIResponse::error('Codice fiscale non valido', 400);
            }
            
            if (isset($input['partita_iva']) && !empty($input['partita_iva'])) {
                if (!SecurityConfig::validatePartitaIVA($input['partita_iva'])) {
                    APIResponse::error('Partita IVA non valida', 400);
                }
            }
            
            // Verifica univocità
            if ($professionista->verificaUnivocita($input['email'], $input['codice_fiscale'], $input['partita_iva'] ?? null)) {
                APIResponse::error('Email, Codice Fiscale o Partita IVA già presenti', 409);
            }
            
            // Sanitizza i dati
            $dati_sanitizzati = SecurityConfig::sanitizeInput($input);
            
            // Prepara dati per inserimento
            $dati = [
                'nome' => $dati_sanitizzati['nome'],
                'cognome' => $dati_sanitizzati['cognome'],
                'email' => strtolower($dati_sanitizzati['email']),
                'codice_fiscale' => strtoupper($dati_sanitizzati['codice_fiscale']),
                'partita_iva' => $dati_sanitizzati['partita_iva'] ?? null,
                'telefono' => $dati_sanitizzati['telefono'] ?? null,
                'cellulare' => $dati_sanitizzati['cellulare'] ?? null,
                'data_nascita' => $dati_sanitizzati['data_nascita'] ?? null,
                'competenze_principali' => $dati_sanitizzati['competenze'] ?? null,
                'privacy_accettata' => 1,
                'termini_accettati' => 1
            ];
            
            try {
                $id_professionista = $professionista->inserisci($dati);
                
                if ($id_professionista) {
                    SecurityConfig::logSecurityEvent('api_registration', [
                        'professionista_id' => $id_professionista,
                        'email' => $dati['email']
                    ]);
                    
                    APIResponse::success([
                        'id' => $id_professionista,
                        'message' => 'Registrazione completata. Il profilo è in valutazione.'
                    ], 'Professionista registrato con successo');
                } else {
                    APIResponse::error('Errore durante la registrazione', 500);
                }
            } catch (Exception $e) {
                APIResponse::error('Errore durante la registrazione: ' . $e->getMessage(), 500);
            }
            break;
            
        default:
            APIResponse::error('Metodo non supportato', 405);
    }
}

/**
 * Middleware di autenticazione per API protette (future)
 */
function authenticateAPI() {
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? '';
    
    if (!str_starts_with($auth_header, 'Bearer ')) {
        APIResponse::error('Token di autenticazione richiesto', 401);
    }
    
    $token = substr($auth_header, 7);
    
    // Qui implementare la validazione del token JWT
    // Per ora ritorna sempre false
    return false;
}

/**
 * Handler per API Province
 */
function handleProvinceAPI($method, $path_parts) {
    $provincia = new Provincia();
    
    switch ($method) {
        case 'GET':
            if (isset($path_parts[1])) {
                // GET /api/province/{sigla}
                $sigla = strtoupper($path_parts[1]);
                $provincia_data = $provincia->ottieniPerSigla($sigla);
                if ($provincia_data) {
                    APIResponse::success($provincia_data, 'Provincia recuperata');
                } else {
                    APIResponse::error('Provincia non trovata', 404);
                }
            } else {
                // GET /api/province
                $regione = $_GET['regione'] ?? null;
                if ($regione) {
                    $province = $provincia->ottieniPerRegione($regione);
                } else {
                    $province = $provincia->ottieniTutte();
                }
                APIResponse::success($province, 'Province recuperate');
            }
            break;
        default:
            APIResponse::error('Metodo non supportato', 405);
    }
}

/**
 * Handler per API Istruzione
 */
function handleIstruzioneAPI($method, $path_parts) {
    $istruzione = new Istruzione();
    
    switch ($method) {
        case 'GET':
            if (isset($path_parts[1]) && is_numeric($path_parts[1])) {
                // GET /api/istruzione/{profilo_id}
                $profilo_id = $path_parts[1];
                $titoli = $istruzione->ottieniPerProfilo($profilo_id);
                APIResponse::success($titoli, 'Titoli di studio recuperati');
            } else {
                APIResponse::error('ID profilo richiesto', 400);
            }
            break;
        default:
            APIResponse::error('Metodo non supportato', 405);
    }
}

/**
 * Handler per API Esperienze Lavorative
 */
function handleEsperienzeAPI($method, $path_parts) {
    $esperienza = new EsperienzaLavorativa();
    
    switch ($method) {
        case 'GET':
            if (isset($path_parts[1]) && is_numeric($path_parts[1])) {
                // GET /api/esperienze/{profilo_id}
                $profilo_id = $path_parts[1];
                $esperienze = $esperienza->ottieniPerProfilo($profilo_id);
                
                // Aggiungi calcolo anni esperienza
                $anni_esperienza = $esperienza->calcolaAnniEsperienza($profilo_id);
                
                APIResponse::success([
                    'esperienze' => $esperienze,
                    'anni_totali' => $anni_esperienza
                ], 'Esperienze lavorative recuperate');
            } else {
                APIResponse::error('ID profilo richiesto', 400);
            }
            break;
        default:
            APIResponse::error('Metodo non supportato', 405);
    }
}

/**
 * Handler per API Lingue
 */
function handleLingueAPI($method, $path_parts) {
    $lingue = new Lingue();
    
    switch ($method) {
        case 'GET':
            if (isset($path_parts[1])) {
                if ($path_parts[1] === 'elenco') {
                    // GET /api/lingue/elenco
                    $elenco = $lingue->ottieniElencoLingue();
                    APIResponse::success($elenco, 'Elenco lingue recuperato');
                } elseif (is_numeric($path_parts[1])) {
                    // GET /api/lingue/{profilo_id}
                    $profilo_id = $path_parts[1];
                    $competenze = $lingue->ottieniPerProfilo($profilo_id);
                    APIResponse::success($competenze, 'Competenze linguistiche recuperate');
                } else {
                    APIResponse::error('Parametro non valido', 400);
                }
            } else {
                // GET /api/lingue - statistiche
                $stats = $lingue->ottieniStatistiche();
                APIResponse::success($stats, 'Statistiche lingue recuperate');
            }
            break;
        default:
            APIResponse::error('Metodo non supportato', 405);
    }
}

/**
 * Handler per API Competenze IT
 */
function handleCompetenzeITAPI($method, $path_parts) {
    $competenzeIT = new CompetenzeIT();
    
    switch ($method) {
        case 'GET':
            if (isset($path_parts[1])) {
                if ($path_parts[1] === 'elenco') {
                    // GET /api/competenze-it/elenco
                    $categoria = $_GET['categoria'] ?? null;
                    $elenco = $competenzeIT->ottieniElencoCompetenze($categoria);
                    APIResponse::success($elenco, 'Elenco competenze IT recuperato');
                } elseif ($path_parts[1] === 'categorie') {
                    // GET /api/competenze-it/categorie
                    $categorie = $competenzeIT->ottieniCategorie();
                    APIResponse::success($categorie, 'Categorie competenze recuperate');
                } elseif (is_numeric($path_parts[1])) {
                    // GET /api/competenze-it/{profilo_id}
                    $profilo_id = $path_parts[1];
                    $competenze = $competenzeIT->ottieniCompetenzePerCategoria($profilo_id);
                    APIResponse::success($competenze, 'Competenze IT recuperate');
                } else {
                    APIResponse::error('Parametro non valido', 400);
                }
            } else {
                // GET /api/competenze-it - statistiche
                $stats = $competenzeIT->ottieniStatistiche();
                APIResponse::success($stats, 'Statistiche competenze IT recuperate');
            }
            break;
        default:
            APIResponse::error('Metodo non supportato', 405);
    }
}

/**
 * Handler per API Allegati
 */
function handleAllegatiAPI($method, $path_parts) {
    $allegato = new Allegato();
    
    switch ($method) {
        case 'GET':
            if (isset($path_parts[1]) && is_numeric($path_parts[1])) {
                if (isset($path_parts[2]) && $path_parts[2] === 'download' && isset($path_parts[3])) {
                    // GET /api/allegati/{profilo_id}/download/{allegato_id}
                    $profilo_id = $path_parts[1];
                    $allegato_id = $path_parts[3];
                    try {
                        $allegato->download($allegato_id, $profilo_id);
                    } catch (Exception $e) {
                        APIResponse::error($e->getMessage(), 404);
                    }
                } else {
                    // GET /api/allegati/{profilo_id}
                    $profilo_id = $path_parts[1];
                    $tipo = $_GET['tipo'] ?? null;
                    $allegati = $allegato->ottieniPerProfilo($profilo_id, $tipo);
                    
                    // Formatta dimensioni
                    foreach ($allegati as &$all) {
                        $all['dimensione_formattata'] = $allegato->formatDimensione($all['dimensione']);
                    }
                    
                    APIResponse::success($allegati, 'Allegati recuperati');
                }
            } elseif (isset($path_parts[1]) && $path_parts[1] === 'tipi') {
                // GET /api/allegati/tipi
                $tipi = $allegato->ottieniTipiAllegato();
                APIResponse::success($tipi, 'Tipi allegato recuperati');
            } else {
                APIResponse::error('ID profilo richiesto', 400);
            }
            break;
        default:
            APIResponse::error('Metodo non supportato', 405);
    }
}
?>