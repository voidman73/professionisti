<?php
require_once '../classes/Professionista.php';
require_once '../classes/Albo.php';
require_once '../classes/Provincia.php';

$professionista = new Professionista();
$albo = new Albo();

$albi = $albo->ottieniTutti();

$provincia = new Provincia();
$province = $provincia->ottieniTutte();
$specializzazioni = [];
$messaggio = '';
$errore = '';

// Gestione invio form
if ($_POST) {
    try {
                                // Validazione base
        $required_fields = ['nome', 'cognome', 'email', 'codice_fiscale', 'data_nascita'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Il campo " . ucfirst(str_replace('_', ' ', $field)) . " è obbligatorio");
            }
        }

        // Verifica univocità
        if ($professionista->verificaUnivocita($_POST['email'], $_POST['codice_fiscale'], $_POST['partita_iva'] ?? null)) {
            throw new Exception("Email, Codice Fiscale o Partita IVA già presenti nel sistema");
        }

        // Preparazione dati
        $dati = [
            'codice_fiscale' => strtoupper(trim($_POST['codice_fiscale'])),
            'partita_iva' => !empty($_POST['partita_iva']) ? trim($_POST['partita_iva']) : null,
            'nome' => trim($_POST['nome']),
            'cognome' => trim($_POST['cognome']),
            'data_nascita' => $_POST['data_nascita'],
            'luogo_nascita' => trim($_POST['luogo_nascita']) ?: null,
            'sesso' => $_POST['sesso'] ?? 'ND',
            'email' => strtolower(trim($_POST['email'])),
            'telefono' => trim($_POST['telefono']) ?: null,
            'cellulare' => trim($_POST['cellulare']) ?: null,
            'indirizzo_residenza' => trim($_POST['indirizzo_residenza']) ?: null,
            'citta_residenza' => trim($_POST['citta_residenza']) ?: null,
            'provincia_residenza' => strtoupper(trim($_POST['provincia_residenza'])) ?: null,
            'cap_residenza' => trim($_POST['cap_residenza']) ?: null,
            'indirizzo_domicilio' => trim($_POST['indirizzo_domicilio']) ?: null,
            'citta_domicilio' => trim($_POST['citta_domicilio']) ?: null,
            'provincia_domicilio' => strtoupper(trim($_POST['provincia_domicilio'])) ?: null,
            'cap_domicilio' => trim($_POST['cap_domicilio']) ?: null,
            'disponibile' => isset($_POST['disponibile']) ? 1 : 0,
            'disponibilita_trasferte' => isset($_POST['disponibilita_trasferte']) ? 1 : 0,
            'raggio_azione_km' => (int)($_POST['raggio_azione_km'] ?? 50),
            'tariffa_oraria_min' => !empty($_POST['tariffa_oraria']) ? (float)$_POST['tariffa_oraria'] : null,
            'tariffa_giornaliera_min' => !empty($_POST['tariffa_giornaliera']) ? (float)$_POST['tariffa_giornaliera'] : null,
            'note_tariffe' => trim($_POST['note_tariffe']) ?: null,
            'competenze_principali' => trim($_POST['competenze']) ?: null,
            'titolo_professionale' => trim($_POST['titolo_professionale']) ?: null,
            'privacy_accettata' => isset($_POST['privacy_accettata']) ? 1 : 0,
            'termini_accettati' => isset($_POST['termini_accettati']) ? 1 : 0
        ];

        // Validazione privacy e termini
        if (!$dati['privacy_accettata'] || !$dati['termini_accettati']) {
            throw new Exception("È necessario accettare la Privacy Policy e i Termini di Servizio");
        }

        // Inserimento nel database
        $id_professionista = $professionista->inserisci($dati);
        
        if ($id_professionista) {
            // Se è stato specificato un albo, iscrive il professionista
            if (!empty($_POST['albo_id']) && !empty($_POST['numero_iscrizione_albo'])) {
                $albo->iscriviProfessionista(
                    $id_professionista, 
                    $_POST['albo_id'], 
                    trim($_POST['numero_iscrizione_albo']),
                    !empty($_POST['data_iscrizione_albo']) ? $_POST['data_iscrizione_albo'] : null
                );
            }
            
            $messaggio = "Registrazione completata con successo! Il tuo profilo è in valutazione e riceverai una email di conferma entro 48 ore.";
            // Reset form
            $_POST = [];
        } else {
            throw new Exception("Errore durante la registrazione");
        }

    } catch (Exception $e) {
        $errore = $e->getMessage();
    }
}

// Caricamento professionisti iscritti a un albo via AJAX
if (isset($_GET['albo_id']) && !empty($_GET['albo_id'])) {
    $professionisti_albo = $albo->ottieniProfessionistiAlbo($_GET['albo_id']);
    echo json_encode($professionisti_albo);
    exit;
}

$page_title = 'Registrazione Professionista';
$css_path = '../assets/style.css';
$body_class = 'bg-light';
$extra_css = [];
$inline_scripts = '';
include '../includes/header.php'; 
?>

<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 25px;
        border-left: 4px solid var(--zpec-primary);
    }
    .required {
        color: #dc3545;
    }
    .progress-step {
        display: none;
    }
    .progress-step.active {
        display: block;
    }
    .step-indicator {
        background: #e9ecef;
        height: 4px;
        border-radius: 2px;
        position: relative;
        margin-bottom: 30px;
    }
    .step-progress {
        background: var(--zpec-primary);
        height: 100%;
        border-radius: 2px;
        transition: width 0.3s ease;
    }
</style>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-users-cog me-2"></i>
                Professionisti Partner
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home me-1"></i>Torna alla Home
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="text-center mb-5">
                    <h1 class="display-5 fw-bold text-primary">
                        <i class="fas fa-user-plus me-3"></i>Registrazione Professionista
                    </h1>
                    <p class="lead">Compila tutti i campi per unirti al nostro network di professionisti qualificati</p>
                </div>

                <!-- Progress Indicator -->
                <div class="step-indicator">
                    <div class="step-progress" style="width: 33.33%"></div>
                </div>

                <!-- Messaggi -->
                <?php if ($messaggio): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($messaggio) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($errore): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($errore) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Registrazione -->
                <form method="POST" enctype="multipart/form-data" id="registrationForm">
                    
                    <!-- Step 1: Dati Anagrafici -->
                    <div class="progress-step active" data-step="1">
                        <div class="form-section">
                            <h3 class="mb-4">
                                <i class="fas fa-user me-2 text-primary"></i>
                                Dati Anagrafici
                            </h3>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nome <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($_POST['nome'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cognome <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="cognome" value="<?= htmlspecialchars($_POST['cognome'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Codice Fiscale <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="codice_fiscale" maxlength="16" value="<?= htmlspecialchars($_POST['codice_fiscale'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Partita IVA</label>
                                    <input type="text" class="form-control" name="partita_iva" maxlength="11" value="<?= htmlspecialchars($_POST['partita_iva'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Data di Nascita <span class="required">*</span></label>
                                    <input type="date" class="form-control" name="data_nascita" value="<?= htmlspecialchars($_POST['data_nascita'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label class="form-label">Luogo di Nascita</label>
                                    <input type="text" class="form-control" name="luogo_nascita" value="<?= htmlspecialchars($_POST['luogo_nascita'] ?? '') ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Sesso</label>
                                    <select class="form-select" name="sesso">
                                        <option value="A" <?= ($_POST['sesso'] ?? 'A') == 'A' ? 'selected' : '' ?>>Non specificato</option>
                                        <option value="M" <?= ($_POST['sesso'] ?? '') == 'M' ? 'selected' : '' ?>>Maschio</option>
                                        <option value="F" <?= ($_POST['sesso'] ?? '') == 'F' ? 'selected' : '' ?>>Femmina</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Contatti -->
                        <div class="form-section">
                            <h4 class="mb-4">
                                <i class="fas fa-phone me-2 text-primary"></i>
                                Contatti
                            </h4>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email <span class="required">*</span></label>
                                    <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Telefono</label>
                                    <input type="tel" class="form-control" name="telefono" value="<?= htmlspecialchars($_POST['telefono'] ?? '') ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Cellulare</label>
                                    <input type="tel" class="form-control" name="cellulare" value="<?= htmlspecialchars($_POST['cellulare'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary btn-lg next-step">
                                Avanti <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Indirizzi -->
                    <div class="progress-step" data-step="2">
                        <div class="form-section">
                            <h3 class="mb-4">
                                <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                Indirizzo di Residenza
                            </h3>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Indirizzo</label>
                                    <input type="text" class="form-control" name="indirizzo_residenza" value="<?= htmlspecialchars($_POST['indirizzo_residenza'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CAP</label>
                                    <input type="text" class="form-control" name="cap_residenza" maxlength="5" value="<?= htmlspecialchars($_POST['cap_residenza'] ?? '') ?>">
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Città</label>
                                    <input type="text" class="form-control" name="citta_residenza" value="<?= htmlspecialchars($_POST['citta_residenza'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Provincia</label>
                                    <select class="form-select" name="provincia_residenza">
                                    <option value="">Seleziona provincia...</option>
                                    <?php foreach ($province as $prov): ?>
                                        <option value="<?= $prov['Sigla'] ?>" <?= ($_POST['provincia_residenza'] ?? '') === $prov['Sigla'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prov['Sigla']) ?> - <?= htmlspecialchars($prov['Provincia']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <h4 class="mb-4">
                                <i class="fas fa-home me-2 text-primary"></i>
                                Domicilio (se diverso dalla residenza)
                            </h4>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Indirizzo</label>
                                    <input type="text" class="form-control" name="indirizzo_domicilio" value="<?= htmlspecialchars($_POST['indirizzo_domicilio'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">CAP</label>
                                    <input type="text" class="form-control" name="cap_domicilio" maxlength="5" value="<?= htmlspecialchars($_POST['cap_domicilio'] ?? '') ?>">
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Città</label>
                                    <input type="text" class="form-control" name="citta_domicilio" value="<?= htmlspecialchars($_POST['citta_domicilio'] ?? '') ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Provincia</label>
                                    <select class="form-select" name="provincia_domicilio">
                                    <option value="">Seleziona provincia...</option>
                                    <?php foreach ($province as $prov): ?>
                                        <option value="<?= $prov['Sigla'] ?>" <?= ($_POST['provincia_domicilio'] ?? '') === $prov['Sigla'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prov['Sigla']) ?> - <?= htmlspecialchars($prov['Provincia']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-lg prev-step">
                                <i class="fas fa-arrow-left me-2"></i> Indietro
                            </button>
                            <button type="button" class="btn btn-primary btn-lg next-step">
                                Avanti <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Dati Professionali -->
                    <div class="progress-step" data-step="3">
                        <div class="form-section">
                            <h3 class="mb-4">
                                <i class="fas fa-briefcase me-2 text-primary"></i>
                                Dati Professionali
                            </h3>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Albo Professionale</label>
                                    <select class="form-select" name="albo_id" id="albo_id">
                                        <option value="">Seleziona albo...</option>
                                        <?php foreach ($albi as $albo_item): ?>
                                            <option value="<?= $albo_item['id'] ?>" <?= ($_POST['albo_id'] ?? '') == $albo_item['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($albo_item['nome']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Numero Iscrizione</label>
                                    <input type="text" class="form-control" name="numero_iscrizione_albo" value="<?= htmlspecialchars($_POST['numero_iscrizione_albo'] ?? '') ?>" placeholder="Es. 12345">
                                </div>
                                                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data Iscrizione Albo</label>
                            <input type="date" class="form-control" name="data_iscrizione_albo" value="<?= htmlspecialchars($_POST['data_iscrizione_albo'] ?? '') ?>">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Titolo Professionale</label>
                            <input type="text" class="form-control" name="titolo_professionale" value="<?= htmlspecialchars($_POST['titolo_professionale'] ?? '') ?>" placeholder="Es. Ingegnere, Architetto, Avvocato...">
                            <div class="form-text">Specifica il tuo titolo professionale principale</div>
                        </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Competenze e Specialità</label>
                                    <textarea class="form-control" name="competenze" rows="4" placeholder="Descrivi le tue principali competenze e specialità..."><?= htmlspecialchars($_POST['competenze'] ?? '') ?></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lingue Parlate</label>
                                    <input type="text" class="form-control" name="lingue_parlate" placeholder="es. Italiano, Inglese, Francese" value="<?= htmlspecialchars($_POST['lingue_parlate'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Disponibilità e Tariffe -->
                        <div class="form-section">
                            <h4 class="mb-4">
                                <i class="fas fa-calendar-check me-2 text-primary"></i>
                                Disponibilità e Tariffe
                            </h4>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disponibile" id="disponibile" <?= isset($_POST['disponibile']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="disponibile">
                                            Attualmente disponibile per nuovi progetti
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disponibilita_trasferte" id="disponibilita_trasferte" <?= isset($_POST['disponibilita_trasferte']) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="disponibilita_trasferte">
                                            Disponibile per trasferte
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Raggio d'azione (km)</label>
                                    <input type="number" class="form-control" name="raggio_azione_km" min="0" max="1000" value="<?= htmlspecialchars($_POST['raggio_azione_km'] ?? '50') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tariffa Oraria (€)</label>
                                    <input type="number" class="form-control" name="tariffa_oraria" step="0.01" min="0" value="<?= htmlspecialchars($_POST['tariffa_oraria'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tariffa Giornaliera (€)</label>
                                    <input type="number" class="form-control" name="tariffa_giornaliera" step="0.01" min="0" value="<?= htmlspecialchars($_POST['tariffa_giornaliera'] ?? '') ?>">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Note sulle Tariffe</label>
                                    <textarea class="form-control" name="note_tariffe" rows="3" placeholder="Eventuali note o specificazioni sulle tariffe..."><?= htmlspecialchars($_POST['note_tariffe'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Privacy e Termini -->
                        <div class="form-section">
                            <h4 class="mb-4">
                                <i class="fas fa-shield-alt me-2 text-primary"></i>
                                Privacy e Termini di Servizio
                            </h4>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="privacy_accettata" id="privacy_accettata" required <?= isset($_POST['privacy_accettata']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="privacy_accettata">
                                    <span class="required">*</span> Accetto la <a href="#" target="_blank">Privacy Policy</a>
                                </label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="termini_accettati" id="termini_accettati" required <?= isset($_POST['termini_accettati']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="termini_accettati">
                                    <span class="required">*</span> Accetto i <a href="#" target="_blank">Termini di Servizio</a>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-lg prev-step">
                                <i class="fas fa-arrow-left me-2"></i> Indietro
                            </button>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-check me-2"></i> Completa Registrazione
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Multi-step form functionality
        let currentStep = 1;
        const totalSteps = 3;

        function updateProgress() {
            const progress = (currentStep / totalSteps) * 100;
            document.querySelector('.step-progress').style.width = progress + '%';
        }

        function showStep(step) {
            document.querySelectorAll('.progress-step').forEach(el => el.classList.remove('active'));
            document.querySelector(`[data-step="${step}"]`).classList.add('active');
            updateProgress();
        }

        // Next step buttons
        document.querySelectorAll('.next-step').forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentStep < totalSteps) {
                    // Validate current step
                    const currentStepEl = document.querySelector(`[data-step="${currentStep}"]`);
                    const requiredFields = currentStepEl.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });

                    if (isValid) {
                        currentStep++;
                        showStep(currentStep);
                    }
                }
            });
        });

        // Previous step buttons
        document.querySelectorAll('.prev-step').forEach(btn => {
            btn.addEventListener('click', function() {
                if (currentStep > 1) {
                    currentStep--;
                    showStep(currentStep);
                }
            });
        });

        // Validate albo selection when changed
        document.getElementById('albo_id').addEventListener('change', function() {
            const alboId = this.value;
            const numeroIscrizione = document.querySelector('input[name="numero_iscrizione_albo"]');
            
            if (alboId) {
                numeroIscrizione.setAttribute('placeholder', 'Inserisci numero iscrizione per questo albo');
            } else {
                numeroIscrizione.setAttribute('placeholder', 'Es. 12345');
            }
        });

        // Initialize
        updateProgress();
    </script>

<?php include '../includes/footer.php'; ?>