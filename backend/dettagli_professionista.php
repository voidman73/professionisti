<?php
session_start();
require_once '../classes/Admin.php';
require_once '../classes/Professionista.php';
require_once '../classes/Albo.php';
require_once '../classes/Provincia.php';
require_once '../classes/Istruzione.php';
require_once '../classes/EsperienzaLavorativa.php';
require_once '../classes/Lingue.php';
require_once '../classes/CompetenzeIT.php';
require_once '../classes/Allegato.php';

// Controllo autenticazione
if (!isset($_SESSION['admin_id'])) {
    echo '<div class="alert alert-danger">Non autorizzato</div>';
    exit;
}

if (!isset($_GET['id'])) {
    echo '<div class="alert alert-danger">ID professionista non specificato</div>';
    exit;
}

try {
    $professionista = new Professionista();
    $dati = $professionista->ottieniPerId($_GET['id']);
    
    if (!$dati) {
        echo '<div class="alert alert-danger">Professionista non trovato</div>';
        exit;
    }
    
    // Calcola età
    $data_nascita = new DateTime($dati['data_nascita']);
    $oggi = new DateTime();
    $eta = $oggi->diff($data_nascita)->y;
    
    // Carica dati aggiuntivi ZPeC
    $albo = new Albo();
    $provincia = new Provincia();
    $istruzione = new Istruzione();
    $esperienza = new EsperienzaLavorativa();
    $lingue = new Lingue();
    $competenzeIT = new CompetenzeIT();
    $allegato = new Allegato();
    
    // Recupera albi del professionista
    $albi_professionista = $albo->ottieniAlbiProfessionista($_GET['id']);
    
    // Recupera titoli di studio
    $titoli_studio = $istruzione->ottieniPerProfilo($_GET['id']);
    
    // Recupera esperienze lavorative
    $esperienze = $esperienza->ottieniPerProfilo($_GET['id']);
    $anni_esperienza = $esperienza->calcolaAnniEsperienza($_GET['id']);
    
    // Recupera competenze linguistiche
    $competenze_lingue = $lingue->ottieniPerProfilo($_GET['id']);
    
    // Recupera competenze IT
    $competenze_it = $competenzeIT->ottieniCompetenzePerCategoria($_GET['id']);
    
    // Recupera allegati
    $allegati = $allegato->ottieniPerProfilo($_GET['id']);
    
    ?>
    <div class="modal-header">
        <h5 class="modal-title">
            <i class="fas fa-user me-2"></i>
            Dettagli Professionista: <?= htmlspecialchars($dati['nome'] . ' ' . $dati['cognome']) ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    
    <div class="modal-body">
        <div class="row">
            <!-- Dati Anagrafici -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-id-card me-2"></i>Dati Anagrafici</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5">Nome:</dt>
                            <dd class="col-7"><?= htmlspecialchars($dati['nome']) ?></dd>
                            
                            <dt class="col-5">Cognome:</dt>
                            <dd class="col-7"><?= htmlspecialchars($dati['cognome']) ?></dd>
                            
                            <dt class="col-5">Codice Fiscale:</dt>
                            <dd class="col-7"><code><?= htmlspecialchars($dati['codice_fiscale']) ?></code></dd>
                            
                            <?php if ($dati['partita_iva']): ?>
                            <dt class="col-5">P.IVA:</dt>
                            <dd class="col-7"><code><?= htmlspecialchars($dati['partita_iva']) ?></code></dd>
                            <?php endif; ?>
                            
                            <dt class="col-5">Data Nascita:</dt>
                            <dd class="col-7">
                                <?= date('d/m/Y', strtotime($dati['data_nascita'])) ?>
                                <small class="text-muted">(<?= $eta ?> anni)</small>
                            </dd>
                            
                            <?php if ($dati['luogo_nascita']): ?>
                            <dt class="col-5">Luogo Nascita:</dt>
                            <dd class="col-7"><?= htmlspecialchars($dati['luogo_nascita']) ?></dd>
                            <?php endif; ?>
                            
                            <dt class="col-5">Sesso:</dt>
                            <dd class="col-7">
                                <?= $dati['sesso'] == 'M' ? 'Maschio' : ($dati['sesso'] == 'F' ? 'Femmina' : 'Non specificato') ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
            
            <!-- Contatti -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-phone me-2"></i>Contatti</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-4">Email:</dt>
                            <dd class="col-8">
                                <a href="mailto:<?= htmlspecialchars($dati['email']) ?>">
                                    <?= htmlspecialchars($dati['email']) ?>
                                </a>
                            </dd>
                            
                            <?php if ($dati['telefono']): ?>
                            <dt class="col-4">Telefono:</dt>
                            <dd class="col-8">
                                <a href="tel:<?= htmlspecialchars($dati['telefono']) ?>">
                                    <?= htmlspecialchars($dati['telefono']) ?>
                                </a>
                            </dd>
                            <?php endif; ?>
                            
                            <?php if ($dati['cellulare']): ?>
                            <dt class="col-4">Cellulare:</dt>
                            <dd class="col-8">
                                <a href="tel:<?= htmlspecialchars($dati['cellulare']) ?>">
                                    <?= htmlspecialchars($dati['cellulare']) ?>
                                </a>
                            </dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Residenza -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-home me-2"></i>Residenza</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($dati['indirizzo_residenza']): ?>
                            <address class="mb-0">
                                <?= htmlspecialchars($dati['indirizzo_residenza']) ?><br>
                                <?= htmlspecialchars($dati['cap_residenza']) ?> 
                                <?= htmlspecialchars($dati['citta_residenza']) ?>
                                <?php if ($dati['provincia_residenza']): ?>
                                    (<?= htmlspecialchars($dati['provincia_residenza']) ?>)
                                <?php endif; ?>
                            </address>
                        <?php else: ?>
                            <small class="text-muted">Non specificato</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Domicilio -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Domicilio</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($dati['indirizzo_domicilio']): ?>
                            <address class="mb-0">
                                <?= htmlspecialchars($dati['indirizzo_domicilio']) ?><br>
                                <?= htmlspecialchars($dati['cap_domicilio']) ?> 
                                <?= htmlspecialchars($dati['citta_domicilio']) ?>
                                <?php if ($dati['provincia_domicilio']): ?>
                                    (<?= htmlspecialchars($dati['provincia_domicilio']) ?>)
                                <?php endif; ?>
                            </address>
                        <?php else: ?>
                            <small class="text-muted">Uguale alla residenza</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Dati Professionali -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-briefcase me-2"></i>Dati Professionali</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <?php if (isset($dati['categoria_nome']) && $dati['categoria_nome']): ?>
                            <dt class="col-5">Categoria:</dt>
                            <dd class="col-7">
                                <span class="badge bg-primary"><?= htmlspecialchars($dati['categoria_nome']) ?></span>
                            </dd>
                            <?php endif; ?>
                            
                            <?php if (isset($dati['specializzazione_nome']) && $dati['specializzazione_nome']): ?>
                            <dt class="col-5">Specializzazione:</dt>
                            <dd class="col-7">
                                <span class="badge bg-info"><?= htmlspecialchars($dati['specializzazione_nome']) ?></span>
                            </dd>
                            <?php endif; ?>
                            
                            <?php if (isset($dati['anni_esperienza']) && $dati['anni_esperienza']): ?>
                            <dt class="col-5">Esperienza:</dt>
                            <dd class="col-7">
                                <strong><?= $dati['anni_esperienza'] ?> anni</strong>
                            </dd>
                            <?php endif; ?>
                            
                            <?php if (isset($dati['numero_iscrizione_albo']) && $dati['numero_iscrizione_albo']): ?>
                            <dt class="col-5">N. Albo:</dt>
                            <dd class="col-7"><?= htmlspecialchars($dati['numero_iscrizione_albo']) ?></dd>
                            
                            <dt class="col-5">Albo:</dt>
                            <dd class="col-7"><?= htmlspecialchars($dati['albo_professionale']) ?></dd>
                            
                            <?php if (isset($dati['data_iscrizione_albo']) && $dati['data_iscrizione_albo']): ?>
                            <dt class="col-5">Iscrizione:</dt>
                            <dd class="col-7"><?= date('d/m/Y', strtotime($dati['data_iscrizione_albo'])) ?></dd>
                            <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if (isset($dati['lingue_parlate']) && $dati['lingue_parlate']): ?>
                            <dt class="col-5">Lingue:</dt>
                            <dd class="col-7"><?= htmlspecialchars($dati['lingue_parlate']) ?></dd>
                            <?php endif; ?>
                        </dl>
                    </div>
                </div>
            </div>
            
            <!-- Disponibilità e Tariffe -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-euro-sign me-2"></i>Disponibilità e Tariffe</h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-5">Disponibile:</dt>
                            <dd class="col-7">
                                <?php if ($dati['disponibile']): ?>
                                    <span class="badge bg-success">Sì</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">No</span>
                                <?php endif; ?>
                            </dd>
                            
                            <dt class="col-5">Trasferte:</dt>
                            <dd class="col-7">
                                <?php if ($dati['disponibilita_trasferte']): ?>
                                    <span class="badge bg-info">Disponibile</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Non disponibile</span>
                                <?php endif; ?>
                            </dd>
                            
                            <?php if (isset($dati['raggio_azione_km']) && $dati['raggio_azione_km']): ?>
                            <dt class="col-5">Raggio azione:</dt>
                            <dd class="col-7"><?= $dati['raggio_azione_km'] ?> km</dd>
                            <?php endif; ?>
                            
                            <?php if (isset($dati['tariffa_oraria_min']) && $dati['tariffa_oraria_min']): ?>
                            <dt class="col-5">Tariffa oraria:</dt>
                            <dd class="col-7">
                                <strong>€ <?= number_format($dati['tariffa_oraria_min'], 2, ',', '.') ?></strong>
                                <?php if (isset($dati['tariffa_oraria_max']) && $dati['tariffa_oraria_max']): ?>
                                    - <strong>€ <?= number_format($dati['tariffa_oraria_max'], 2, ',', '.') ?></strong>
                                <?php endif; ?>
                            </dd>
                            <?php endif; ?>
                            
                            <?php if (isset($dati['tariffa_giornaliera_min']) && $dati['tariffa_giornaliera_min']): ?>
                            <dt class="col-5">Tariffa giornaliera:</dt>
                            <dd class="col-7">
                                <strong>€ <?= number_format($dati['tariffa_giornaliera_min'], 2, ',', '.') ?></strong>
                                <?php if (isset($dati['tariffa_giornaliera_max']) && $dati['tariffa_giornaliera_max']): ?>
                                    - <strong>€ <?= number_format($dati['tariffa_giornaliera_max'], 2, ',', '.') ?></strong>
                                <?php endif; ?>
                            </dd>
                            <?php endif; ?>
                        </dl>
                        
                        <?php if (isset($dati['note_tariffe']) && $dati['note_tariffe']): ?>
                        <div class="mt-3">
                            <strong>Note tariffe:</strong>
                            <p class="small mb-0"><?= nl2br(htmlspecialchars($dati['note_tariffe'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Competenze -->
        <?php if (isset($dati['competenze_principali']) && $dati['competenze_principali']): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Competenze e Specialità</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0"><?= nl2br(htmlspecialchars($dati['competenze_principali'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Albi Professionali -->
        <?php if (!empty($albi_professionista)): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-certificate me-2"></i>Albi Professionali</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($albi_professionista as $albo_item): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <strong><?= htmlspecialchars($albo_item['nome']) ?></strong>
                                <?php if ($albo_item['numero_iscrizione']): ?>
                                    <br><small class="text-muted">N. Iscrizione: <?= htmlspecialchars($albo_item['numero_iscrizione']) ?></small>
                                <?php endif; ?>
                                <?php if ($albo_item['data_iscrizione']): ?>
                                    <br><small class="text-muted">Dal: <?= date('d/m/Y', strtotime($albo_item['data_iscrizione'])) ?></small>
                                <?php endif; ?>
                            </div>
                            <span class="badge bg-<?= $albo_item['attiva'] ? 'success' : 'secondary' ?>">
                                <?= $albo_item['attiva'] ? 'Attiva' : 'Non Attiva' ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Titoli di Studio -->
        <?php if (!empty($titoli_studio)): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Istruzione</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($titoli_studio as $titolo): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <strong><?= htmlspecialchars($titolo['titolo']) ?></strong>
                            <span class="badge bg-info ms-2"><?= htmlspecialchars($titolo['tipo_titolo']) ?></span>
                            <br><small class="text-muted"><?= htmlspecialchars($titolo['istituto']) ?></small>
                            <?php if ($titolo['anno_conseguimento']): ?>
                                <br><small class="text-muted">Anno: <?= $titolo['anno_conseguimento'] ?></small>
                            <?php endif; ?>
                            <?php if ($titolo['voto']): ?>
                                <br><small class="text-muted">Voto: <?= $titolo['voto'] ?><?= $titolo['lode'] ? ' e lode' : '' ?></small>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Esperienze Lavorative -->
        <?php if (!empty($esperienze)): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="mb-0"><i class="fas fa-briefcase me-2"></i>Esperienze Lavorative</h6>
                        <span class="badge bg-primary">Totale: <?= $anni_esperienza ?> anni</span>
                    </div>
                    <div class="card-body">
                        <?php foreach ($esperienze as $exp): ?>
                        <div class="border-bottom pb-2 mb-2">
                            <div class="d-flex justify-content-between">
                                <strong>
                                    <?= $exp['tipo_esperienza'] === 'AUTONOMA' ? 
                                        htmlspecialchars($exp['denominazione_cliente']) : 
                                        htmlspecialchars($exp['denominazione_azienda']) ?>
                                </strong>
                                <span class="badge bg-<?= $exp['tipo_esperienza'] === 'AUTONOMA' ? 'success' : 'primary' ?>">
                                    <?= $exp['tipo_esperienza'] ?>
                                </span>
                            </div>
                            <?php if ($exp['tipo_esperienza'] === 'AUTONOMA'): ?>
                                <?php if ($exp['tipo_attivita']): ?>
                                    <small class="text-muted d-block"><?= htmlspecialchars($exp['tipo_attivita']) ?></small>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php if ($exp['posizione']): ?>
                                    <small class="text-muted d-block"><?= htmlspecialchars($exp['posizione']) ?></small>
                                <?php endif; ?>
                            <?php endif; ?>
                            <small class="text-muted">
                                <?= date('m/Y', strtotime($exp['data_inizio'])) ?> - 
                                <?= $exp['in_corso'] ? 'In corso' : date('m/Y', strtotime($exp['data_fine'])) ?>
                            </small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Competenze Linguistiche -->
        <?php if (!empty($competenze_lingue)): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-language me-2"></i>Competenze Linguistiche</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($competenze_lingue as $lingua): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong><?= htmlspecialchars($lingua['lingua_nome']) ?></strong>
                                    <div>
                                        <span class="badge bg-info me-1">Parlato: <?= $lingua['livello_parlato'] ?></span>
                                        <span class="badge bg-info">Scritto: <?= $lingua['livello_scritto'] ?></span>
                                    </div>
                                </div>
                                <?php if ($lingua['certificazioni']): ?>
                                    <small class="text-muted"><?= htmlspecialchars($lingua['certificazioni']) ?></small>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Competenze Informatiche -->
        <?php if (!empty($competenze_it)): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-laptop-code me-2"></i>Competenze Informatiche</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($competenze_it as $categoria => $competenze): ?>
                        <h6 class="text-primary"><?= htmlspecialchars($categoria) ?></h6>
                        <div class="row mb-3">
                            <?php foreach ($competenze as $comp): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between">
                                    <span><?= htmlspecialchars($comp['competenza_nome']) ?></span>
                                    <div>
                                        <span class="badge bg-<?= 
                                            $comp['livello'] === 'ESPERTO' ? 'success' : 
                                            ($comp['livello'] === 'AVANZATO' ? 'info' : 
                                            ($comp['livello'] === 'INTERMEDIO' ? 'warning' : 'secondary')) 
                                        ?>"><?= $comp['livello'] ?></span>
                                        <?php if ($comp['anni_esperienza']): ?>
                                            <small class="text-muted">(<?= $comp['anni_esperienza'] ?> anni)</small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Allegati -->
        <?php if (!empty($allegati)): ?>
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Allegati (<?= count($allegati) ?>)</h6>
                    </div>
                    <div class="card-body">
                        <?php foreach ($allegati as $all): ?>
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <strong><?= htmlspecialchars($all['nome_originale']) ?></strong>
                                <span class="badge bg-secondary ms-2"><?= htmlspecialchars($all['tipo']) ?></span>
                                <br><small class="text-muted">
                                    <?= $allegato->formatDimensione($all['dimensione']) ?> • 
                                    <?= date('d/m/Y', strtotime($all['data_upload'])) ?>
                                </small>
                                <?php if ($all['descrizione']): ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($all['descrizione']) ?></small>
                                <?php endif; ?>
                            </div>
                            <a href="../api/index.php?endpoint=allegati&profilo_id=<?= $_GET['id'] ?>&download=<?= $all['id'] ?>" 
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-download"></i> Scarica
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Stato e Note Amministrative -->
        <div class="row">
            <div class="col-12">
                <div class="card border-<?= $dati['stato'] == 'APPROVATO' ? 'success' : ($dati['stato'] == 'PENDENTE' ? 'warning' : 'danger') ?>">
                    <div class="card-header bg-<?= $dati['stato'] == 'APPROVATO' ? 'success' : ($dati['stato'] == 'PENDENTE' ? 'warning' : 'danger') ?> text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Stato: <?= htmlspecialchars($dati['stato']) ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-3">Data iscrizione:</dt>
                            <dd class="col-9"><?= date('d/m/Y H:i', strtotime($dati['data_registrazione'])) ?></dd>
                            
                            <dt class="col-3">Ultima modifica:</dt>
                            <dd class="col-9"><?= date('d/m/Y H:i', strtotime($dati['data_ultima_modifica'])) ?></dd>
                            
                            <dt class="col-3">Privacy accettata:</dt>
                            <dd class="col-9">
                                <?= $dati['privacy_accettata'] ? '<span class="text-success">Sì</span>' : '<span class="text-danger">No</span>' ?>
                            </dd>
                            
                            <dt class="col-3">Termini accettati:</dt>
                            <dd class="col-9">
                                <?= $dati['termini_accettati'] ? '<span class="text-success">Sì</span>' : '<span class="text-danger">No</span>' ?>
                            </dd>
                        </dl>
                        
                        <?php if ($dati['note_amministrative']): ?>
                        <div class="mt-3">
                            <strong>Note amministrative:</strong>
                            <div class="bg-light p-2 rounded mt-2">
                                <?= nl2br(htmlspecialchars($dati['note_amministrative'])) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal-footer">
        <div class="btn-group me-auto">
            <button type="button" class="btn btn-success" onclick="gestisciStato(<?= $dati['id'] ?>, 'APPROVATO')">
                <i class="fas fa-check me-2"></i>Approva
            </button>
            <button type="button" class="btn btn-warning" onclick="gestisciStato(<?= $dati['id'] ?>, 'SOSPESO')">
                <i class="fas fa-pause me-2"></i>Sospendi
            </button>
            <button type="button" class="btn btn-danger" onclick="gestisciStato(<?= $dati['id'] ?>, 'RESPINTO')">
                <i class="fas fa-times me-2"></i>Respingi
            </button>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
    </div>
    
    <?php
} catch (Exception $e) {
    echo '<div class="alert alert-danger">Errore: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
?>