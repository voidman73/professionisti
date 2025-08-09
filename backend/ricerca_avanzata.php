<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/Professionista.php';
require_once '../classes/Albo.php';
require_once '../classes/Provincia.php';
require_once '../classes/Lingue.php';
require_once '../classes/CompetenzeIT.php';
require_once '../classes/EsperienzaLavorativa.php';

// Controllo autenticazione
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $professionista = new Professionista();
    $albo = new Albo();
    $provincia = new Provincia();
    $lingue = new Lingue();
    $competenzeIT = new CompetenzeIT();
    $esperienza = new EsperienzaLavorativa();
    
    // Carica dati per i filtri
    $albi = $albo->ottieniTutti();
    $province = $provincia->ottieniTutte();
    $regioni = $provincia->ottieniRegioni();
    $elenco_lingue = $lingue->ottieniElencoLingue();
    $elenco_competenze_it = $competenzeIT->ottieniElencoCompetenze();
    $categorie_it = $competenzeIT->ottieniCategorie();
    
    $risultati = [];
    $total_risultati = 0;
    
    // Gestione ricerca
    if ($_GET) {
        // Prepara filtri base
        $filtri = [
            'nome' => $_GET['nome'] ?? '',
            'cognome' => $_GET['cognome'] ?? '',
            'email' => $_GET['email'] ?? '',
            'stato' => $_GET['stato'] ?? '',
            'albo_id' => $_GET['albo_id'] ?? '',
            'citta' => $_GET['citta'] ?? '',
            'provincia' => $_GET['provincia'] ?? '',
            'regione' => $_GET['regione'] ?? '',
            'competenze' => $_GET['competenze'] ?? '',
            'disponibile' => $_GET['disponibile'] ?? '',
            'eta_min' => $_GET['eta_min'] ?? '',
            'eta_max' => $_GET['eta_max'] ?? ''
        ];
        
        // Rimuovi filtri vuoti
        $filtri = array_filter($filtri, function($value) { 
            return $value !== '' && $value !== null; 
        });
        
        $risultati = $professionista->ottieniTutti($filtri);
        $total_risultati = count($risultati);
        
        // Filtri avanzati (applicati post-query)
        if (!empty($_GET['lingua_codice'])) {
            $lingua_codice = $_GET['lingua_codice'];
            $livello_minimo = $_GET['livello_lingua'] ?? 'A1';
            
            $professionisti_lingua = $lingue->cercaProfessionisti($lingua_codice, $livello_minimo);
            $ids_lingua = array_column($professionisti_lingua, 'profilo_id');
            
            $risultati = array_filter($risultati, function($prof) use ($ids_lingua) {
                return in_array($prof['id'], $ids_lingua);
            });
        }
        
        if (!empty($_GET['competenza_it_codice'])) {
            $comp_codice = $_GET['competenza_it_codice'];
            $livello_min_it = $_GET['livello_it'] ?? 'BASE';
            
            $professionisti_it = $competenzeIT->cercaProfessionisti($comp_codice, $livello_min_it);
            $ids_it = array_column($professionisti_it, 'profilo_id');
            
            $risultati = array_filter($risultati, function($prof) use ($ids_it) {
                return in_array($prof['id'], $ids_it);
            });
        }
        
        $total_risultati = count($risultati);
    }
    
} catch (Exception $e) {
    $errore = "Errore durante la ricerca: " . $e->getMessage();
}

include '../includes/header.php';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-search me-2"></i>Ricerca Avanzata Professionisti ZPeC</h1>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Torna al Dashboard
                </a>
            </div>
            
            <?php if (isset($errore)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errore) ?></div>
            <?php endif; ?>
            
            <!-- Form di Ricerca Avanzata -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtri di Ricerca</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="search-form">
                        <!-- Dati Anagrafici -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Dati Anagrafici</h6>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Nome</label>
                                <input type="text" class="form-control form-control-sm" name="nome" 
                                       value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Cognome</label>
                                <input type="text" class="form-control form-control-sm" name="cognome" 
                                       value="<?= htmlspecialchars($_GET['cognome'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control form-control-sm" name="email" 
                                       value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Stato</label>
                                <select class="form-select form-select-sm" name="stato">
                                    <option value="">Tutti gli stati</option>
                                    <option value="PENDENTE" <?= ($_GET['stato'] ?? '') === 'PENDENTE' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="APPROVATO" <?= ($_GET['stato'] ?? '') === 'APPROVATO' ? 'selected' : '' ?>>Approvato</option>
                                    <option value="RIFIUTATO" <?= ($_GET['stato'] ?? '') === 'RIFIUTATO' ? 'selected' : '' ?>>Rifiutato</option>
                                    <option value="SOSPESO" <?= ($_GET['stato'] ?? '') === 'SOSPESO' ? 'selected' : '' ?>>Sospeso</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Età -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Età</h6>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Età Minima</label>
                                <input type="number" class="form-control form-control-sm" name="eta_min" 
                                       value="<?= htmlspecialchars($_GET['eta_min'] ?? '') ?>" min="18" max="80">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Età Massima</label>
                                <input type="number" class="form-control form-control-sm" name="eta_max" 
                                       value="<?= htmlspecialchars($_GET['eta_max'] ?? '') ?>" min="18" max="80">
                            </div>
                        </div>
                        
                        <!-- Localizzazione -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Localizzazione</h6>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Regione</label>
                                <select class="form-select form-select-sm" name="regione" id="regione">
                                    <option value="">Tutte le regioni</option>
                                    <?php foreach ($regioni as $reg): ?>
                                        <option value="<?= htmlspecialchars($reg) ?>" <?= ($_GET['regione'] ?? '') === $reg ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($reg) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Provincia</label>
                                <select class="form-select form-select-sm" name="provincia" id="provincia">
                                    <option value="">Tutte le province</option>
                                    <?php foreach ($province as $prov): ?>
                                        <option value="<?= $prov['Sigla'] ?>" data-regione="<?= htmlspecialchars($prov['Regione']) ?>" 
                                                <?= ($_GET['provincia'] ?? '') === $prov['Sigla'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prov['Sigla']) ?> - <?= htmlspecialchars($prov['Provincia']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Città</label>
                                <input type="text" class="form-control form-control-sm" name="citta" 
                                       value="<?= htmlspecialchars($_GET['citta'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Disponibilità</label>
                                <select class="form-select form-select-sm" name="disponibile">
                                    <option value="">Tutti</option>
                                    <option value="1" <?= ($_GET['disponibile'] ?? '') == '1' ? 'selected' : '' ?>>Disponibili</option>
                                    <option value="0" <?= ($_GET['disponibile'] ?? '') == '0' ? 'selected' : '' ?>>Non Disponibili</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Albi Professionali -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Albi Professionali</h6>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Albo</label>
                                <select class="form-select form-select-sm" name="albo_id">
                                    <option value="">Tutti gli albi</option>
                                    <?php foreach ($albi as $albo_item): ?>
                                        <option value="<?= $albo_item['id'] ?>" <?= ($_GET['albo_id'] ?? '') == $albo_item['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($albo_item['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Competenze -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Competenze</h6>
                            </div>
                            <div class="col-md-6 mb-2">
                                <label class="form-label">Competenze e Specialità</label>
                                <input type="text" class="form-control form-control-sm" name="competenze" 
                                       value="<?= htmlspecialchars($_GET['competenze'] ?? '') ?>" 
                                       placeholder="Cerca per parole chiave...">
                            </div>
                        </div>
                        
                        <!-- Competenze Linguistiche -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Competenze Linguistiche</h6>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Lingua</label>
                                <select class="form-select form-select-sm" name="lingua_codice">
                                    <option value="">Tutte le lingue</option>
                                    <?php foreach ($elenco_lingue as $lingua): ?>
                                        <option value="<?= $lingua['codice'] ?>" <?= ($_GET['lingua_codice'] ?? '') === $lingua['codice'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($lingua['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Livello Minimo</label>
                                <select class="form-select form-select-sm" name="livello_lingua">
                                    <?php foreach ($lingue->ottieniLivelli() as $codice => $nome): ?>
                                        <option value="<?= $codice ?>" <?= ($_GET['livello_lingua'] ?? 'A1') === $codice ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($nome) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Competenze Informatiche -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-primary border-bottom pb-2">Competenze Informatiche</h6>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="form-label">Competenza IT</label>
                                <select class="form-select form-select-sm" name="competenza_it_codice">
                                    <option value="">Tutte le competenze</option>
                                    <?php foreach ($elenco_competenze_it as $comp): ?>
                                        <option value="<?= $comp['codice'] ?>" <?= ($_GET['competenza_it_codice'] ?? '') === $comp['codice'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($comp['nome']) ?> (<?= htmlspecialchars($comp['categoria']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label class="form-label">Livello Minimo</label>
                                <select class="form-select form-select-sm" name="livello_it">
                                    <?php foreach ($competenzeIT->ottieniLivelli() as $codice => $nome): ?>
                                        <option value="<?= $codice ?>" <?= ($_GET['livello_it'] ?? 'BASE') === $codice ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($nome) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-2"></i>Cerca
                                    </button>
                                    <a href="ricerca_avanzata.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Risultati -->
            <?php if ($_GET): ?>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Risultati Ricerca 
                        <span class="badge bg-primary"><?= $total_risultati ?></span>
                    </h5>
                    <?php if ($total_risultati > 0): ?>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-download me-2"></i>Esporta
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="esporta.php?<?= http_build_query($_GET) ?>&formato=csv">
                                <i class="fas fa-file-csv me-2"></i>CSV
                            </a></li>
                            <li><a class="dropdown-item" href="esporta.php?<?= http_build_query($_GET) ?>&formato=xlsx">
                                <i class="fas fa-file-excel me-2"></i>Excel (XLSX)
                            </a></li>
                            <li><a class="dropdown-item" href="esporta.php?<?= http_build_query($_GET) ?>&formato=pdf">
                                <i class="fas fa-file-pdf me-2"></i>PDF
                            </a></li>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if ($total_risultati > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Albi</th>
                                    <th>Località</th>
                                    <th>Stato</th>
                                    <th>Disponibile</th>
                                    <th>Azioni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($risultati as $prof): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($prof['nome'] . ' ' . $prof['cognome']) ?></strong>
                                        <?php if ($prof['titolo_professionale']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($prof['titolo_professionale']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($prof['email']) ?>"><?= htmlspecialchars($prof['email']) ?></a>
                                        <?php if ($prof['cellulare']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($prof['cellulare']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        try {
                                            $albi_prof = $albo->ottieniAlbiProfessionista($prof['id']);
                                            foreach ($albi_prof as $ap):
                                        ?>
                                            <span class="badge bg-primary me-1"><?= htmlspecialchars($ap['nome']) ?></span>
                                        <?php 
                                            endforeach;
                                        } catch (Exception $e) {
                                            echo '<small class="text-muted">N/D</small>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($prof['citta_residenza'] ?: $prof['citta_domicilio'] ?: 'N/D') ?>
                                        <?php if ($prof['provincia_residenza']): ?>
                                            <br><small class="text-muted">(<?= htmlspecialchars($prof['provincia_residenza']) ?>)</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $prof['stato'] == 'APPROVATO' ? 'success' : ($prof['stato'] == 'PENDENTE' ? 'warning' : 'danger') ?>">
                                            <?= htmlspecialchars($prof['stato']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $prof['disponibile'] ? '<span class="text-success">Sì</span>' : '<span class="text-muted">No</span>' ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    onclick="mostraDettagli(<?= $prof['id'] ?>)" 
                                                    title="Visualizza dettagli">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nessun professionista trovato con i criteri di ricerca specificati.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal per dettagli professionista -->
<div class="modal fade" id="dettagliModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div id="dettagliContent">
                <!-- Contenuto caricato via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
// Filtro province per regione
document.getElementById('regione').addEventListener('change', function() {
    const selectedRegione = this.value;
    const provinciaSelect = document.getElementById('provincia');
    const options = provinciaSelect.querySelectorAll('option');
    
    options.forEach(option => {
        if (option.value === '') {
            option.style.display = 'block';
        } else if (selectedRegione === '' || option.dataset.regione === selectedRegione) {
            option.style.display = 'block';
        } else {
            option.style.display = 'none';
        }
    });
    
    if (selectedRegione !== '') {
        provinciaSelect.value = '';
    }
});

// Mostra dettagli professionista
function mostraDettagli(id) {
    const modal = new bootstrap.Modal(document.getElementById('dettagliModal'));
    
    fetch('dettagli_professionista.php?id=' + id)
        .then(response => response.text())
        .then(data => {
            document.getElementById('dettagliContent').innerHTML = data;
            modal.show();
        })
        .catch(error => {
            console.error('Errore:', error);
            alert('Errore nel caricamento dei dettagli');
        });
}
</script>

<?php include '../includes/footer.php'; ?>