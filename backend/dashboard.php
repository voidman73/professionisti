<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/Professionista.php';
require_once '../classes/Albo.php';
require_once '../classes/Provincia.php';
// require_once '../classes/Categoria.php'; // RIMOSSO: sostituito con Albo.php

// Controllo autenticazione
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

$professionista = new Professionista();
$albo = new Albo();

// Ottieni statistiche
$stats_professionisti = $professionista->ottieniStatistiche();
$stats_albi = $albo->ottieniStatistiche();

// Filtri per la ricerca
$filtri = [];
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

// Ottieni professionisti con filtri
$professionisti = $professionista->ottieniTutti($filtri);
$albi = $albo->ottieniTutti();
$page_title = 'Dashboard Amministrazione - Professionisti';
$css_path = '../assets/style.css';
$body_class = 'bg-light';
$include_datatables = true;
$extra_css = [];
$inline_scripts = '';
include '../includes/header.php'; 
?>

<style>
    .sidebar {
        min-height: 100vh;
        background: linear-gradient(180deg, var(--zpec-primary-dark) 0%, var(--zpec-primary) 100%);
    }
    .sidebar .nav-link {
        color: rgba(255,255,255,0.8);
        padding: 15px 20px;
        border-radius: 0;
    }
    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background: rgba(255,255,255,0.1);
        color: white;
    }
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        transition: transform 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
    }
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
    .badge-stato {
        font-size: 0.8em;
        padding: 5px 10px;
    }
    .search-panel {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center py-4">
                        <i class="fas fa-users-cog fa-3x text-white mb-3"></i>
                        <h5 class="text-white">Admin Panel</h5>
                        <small class="text-white-50">
                            Benvenuto, <?= htmlspecialchars($_SESSION['admin_nome']) ?>
                        </small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#dashboard">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#professionisti">
                                <i class="fas fa-users me-2"></i>
                                Professionisti
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#categorie">
                                <i class="fas fa-tags me-2"></i>
                                Categorie
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#progetti">
                                <i class="fas fa-project-diagram me-2"></i>
                                Progetti
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#rapporti">
                                <i class="fas fa-chart-bar me-2"></i>
                                Rapporti
                            </a>
                        </li>
                        <li class="nav-item mt-4">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard Amministrazione</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-download me-2"></i>Esporta Dati
                        </button>
                    </div>
                </div>

                <!-- Statistiche -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= $stats_professionisti['totale'] ?></h3>
                                    <small class="text-muted">Totale Professionisti</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success me-3">
                                    <i class="fas fa-user-check"></i>
                                </div>
                                <div>
                                    <?php
                                    $approvati = 0;
                                    foreach($stats_professionisti['per_stato'] as $stato) {
                                        if($stato['stato'] == 'APPROVATO') $approvati = $stato['numero'];
                                    }
                                    ?>
                                    <h3 class="mb-0"><?= $approvati ?></h3>
                                    <small class="text-muted">Approvati</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-warning me-3">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div>
                                    <?php
                                    $pendenti = 0;
                                    foreach($stats_professionisti['per_stato'] as $stato) {
                                        if($stato['stato'] == 'PENDENTE') $pendenti = $stato['numero'];
                                    }
                                    ?>
                                    <h3 class="mb-0"><?= $pendenti ?></h3>
                                    <small class="text-muted">In Attesa</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info me-3">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0"><?= $stats_professionisti['nuove_ultimo_mese'] ?></h3>
                                    <small class="text-muted">Ultimo Mese</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filtri di Ricerca -->
                <div class="search-panel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">
                            <i class="fas fa-search me-2"></i>Filtri Rapidi
                        </h5>
                        <a href="ricerca_avanzata.php" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search-plus me-2"></i>Ricerca Avanzata
                        </a>
                    </div>
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
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
                        <div class="col-md-2">
                            <label class="form-label">Stato</label>
                            <select class="form-select form-select-sm" name="stato">
                                <option value="">Tutti gli stati</option>
                                <option value="PENDENTE" <?= ($_GET['stato'] ?? '') == 'PENDENTE' ? 'selected' : '' ?>>Pendente</option>
                                <option value="APPROVATO" <?= ($_GET['stato'] ?? '') == 'APPROVATO' ? 'selected' : '' ?>>Approvato</option>
                                <option value="RESPINTO" <?= ($_GET['stato'] ?? '') == 'RESPINTO' ? 'selected' : '' ?>>Respinto</option>
                                <option value="SOSPESO" <?= ($_GET['stato'] ?? '') == 'SOSPESO' ? 'selected' : '' ?>>Sospeso</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Disponibilità</label>
                            <select class="form-select form-select-sm" name="disponibile">
                                <option value="">Tutti</option>
                                <option value="1" <?= ($_GET['disponibile'] ?? '') == '1' ? 'selected' : '' ?>>Disponibili</option>
                                <option value="0" <?= ($_GET['disponibile'] ?? '') == '0' ? 'selected' : '' ?>>Non Disponibili</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Città</label>
                            <input type="text" class="form-control form-control-sm" name="citta" 
                                   value="<?= htmlspecialchars($_GET['citta'] ?? '') ?>" placeholder="Nome città">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Provincia</label>
                            <select class="form-select form-select-sm" name="provincia">
                                <option value="">Tutte le province</option>
                                <?php foreach ($province as $prov): ?>
                                    <option value="<?= $prov['Sigla'] ?>" <?= ($_GET['provincia'] ?? '') === $prov['Sigla'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($prov['Sigla']) ?> - <?= htmlspecialchars($prov['Provincia']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <input type="text" class="form-control form-control-sm" name="competenze" 
                                   value="<?= htmlspecialchars($_GET['competenze'] ?? '') ?>" 
                                   placeholder="Cerca nelle competenze..." form="search-form">
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">
                                <strong>Trovati <?= count($professionisti) ?> professionisti</strong>
                                <?php if (!empty($filtri)): ?>
                                    - <a href="dashboard.php" class="text-decoration-none">Rimuovi filtri</a>
                                <?php endif; ?>
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Tabella Professionisti -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>Elenco Professionisti
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="approvaSelezionati()">
                                <i class="fas fa-check me-1"></i>Approva Selezionati
                            </button>
                            <button class="btn btn-outline-secondary" onclick="esportaSelezionati()">
                                <i class="fas fa-download me-1"></i>Esporta
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="professionisti-table">
                                <thead class="table-light">
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                        </th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th>Albi</th>
                                        <th>Città</th>
                                        <th>Disponibilità</th>
                                        <th>Stato</th>
                                        <th>Data Registrazione</th>
                                        <th width="120">Azioni</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($professionisti as $prof): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="form-check-input prof-checkbox" 
                                                       value="<?= $prof['id'] ?>">
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($prof['nome'] . ' ' . $prof['cognome']) ?></strong>
                                                <br><small class="text-muted"><?= htmlspecialchars($prof['codice_fiscale']) ?></small>
                                            </td>
                                            <td>
                                                <a href="mailto:<?= htmlspecialchars($prof['email']) ?>" class="text-decoration-none">
                                                    <?= htmlspecialchars($prof['email']) ?>
                                                </a>
                                                <?php if ($prof['cellulare']): ?>
                                                    <br><small class="text-muted">
                                                        <i class="fas fa-phone fa-xs"></i> <?= htmlspecialchars($prof['cellulare']) ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                // Ottieni albi del professionista
                                                try {
                                                    $albi_prof = $albo->ottieniAlbiProfessionista($prof['id']);
                                                    if (!empty($albi_prof)) {
                                                        foreach ($albi_prof as $ap) {
                                                            echo '<span class="badge bg-primary me-1" title="' . htmlspecialchars($ap['numero_iscrizione']) . '">' . htmlspecialchars($ap['nome']) . '</span>';
                                                        }
                                                    } else {
                                                        echo '<small class="text-muted">Nessun albo</small>';
                                                    }
                                                } catch (Exception $e) {
                                                    echo '<small class="text-muted">Errore: ' . htmlspecialchars($e->getMessage()) . '</small>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($prof['citta_residenza'] ?: $prof['citta_domicilio'] ?: 'N/D') ?>
                                                <?php if ($prof['provincia_residenza']): ?>
                                                    <small class="text-muted">(<?= htmlspecialchars($prof['provincia_residenza']) ?>)</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($prof['disponibile']): ?>
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle fa-xs"></i> Disponibile
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-times-circle fa-xs"></i> Non Disponibile
                                                    </span>
                                                <?php endif; ?>
                                                <?php if ($prof['disponibilita_trasferte']): ?>
                                                    <br><small class="text-info">
                                                        <i class="fas fa-plane fa-xs"></i> Trasferte OK
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php
                                                $badge_class = match($prof['stato']) {
                                                    'PENDENTE' => 'bg-warning',
                                                    'APPROVATO' => 'bg-success',
                                                    'RESPINTO' => 'bg-danger',
                                                    'SOSPESO' => 'bg-secondary',
                                                    default => 'bg-light text-dark'
                                                };
                                                ?>
                                                <span class="badge badge-stato <?= $badge_class ?>">
                                                    <?= htmlspecialchars($prof['stato']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small><?= date('d/m/Y', strtotime($prof['data_registrazione'])) ?></small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary btn-sm" 
                                                            onclick="visualizzaProfessionista(<?= $prof['id'] ?>)" 
                                                            title="Visualizza">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success btn-sm" 
                                                            onclick="gestisciStato(<?= $prof['id'] ?>, 'APPROVATO')" 
                                                            title="Approva">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" 
                                                            onclick="gestisciStato(<?= $prof['id'] ?>, 'RESPINTO')" 
                                                            title="Respingi">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal Dettagli Professionista -->
    <div class="modal fade" id="modalDettagli" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" id="modalDettagliContent">
                <!-- Contenuto caricato via AJAX -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Inizializza DataTables
        $(document).ready(function() {
            $('#professionisti-table').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/it-IT.json"
                },
                "pageLength": 25,
                "order": [[7, "desc"]], // Ordina per data iscrizione
                "columnDefs": [
                    { "orderable": false, "targets": [0, 8] } // Checkbox e azioni non ordinabili
                ]
            });
        });

        // Select All checkbox
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.prof-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // Funzioni per gestione professionisti
        function visualizzaProfessionista(id) {
            fetch(`dettagli_professionista.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modalDettagliContent').innerHTML = html;
                    new bootstrap.Modal(document.getElementById('modalDettagli')).show();
                });
        }

        function gestisciStato(id, nuovoStato) {
            if (confirm(`Confermi di voler cambiare lo stato in: ${nuovoStato}?`)) {
                fetch('gestisci_stato.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&stato=${nuovoStato}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore: ' + data.error);
                    }
                });
            }
        }

        function approvaSelezionati() {
            const selezionati = document.querySelectorAll('.prof-checkbox:checked');
            if (selezionati.length === 0) {
                alert('Seleziona almeno un professionista');
                return;
            }
            
            if (confirm(`Confermi di voler approvare ${selezionati.length} professionisti?`)) {
                const ids = Array.from(selezionati).map(cb => cb.value);
                
                fetch('gestisci_stato.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `ids=${ids.join(',')}&stato=APPROVATO`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Errore: ' + data.error);
                    }
                });
            }
        }

        function esportaSelezionati() {
            const selezionati = document.querySelectorAll('.prof-checkbox:checked');
            if (selezionati.length === 0) {
                alert('Seleziona almeno un professionista');
                return;
            }
            
            const ids = Array.from(selezionati).map(cb => cb.value);
            window.open(`esporta.php?ids=${ids.join(',')}`);
        }
    </script>

<?php include '../includes/footer.php'; ?>