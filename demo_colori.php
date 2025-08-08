<?php 
$page_title = 'Demo Nuova Colorazione ZPeC';
$css_path = 'assets/style.css';
$body_class = 'bg-light';
$extra_css = [];
$inline_scripts = '';
include 'includes/header.php'; 
?>

<style>
    .demo-section {
        margin: 2rem 0;
        padding: 2rem;
        border-radius: 12px;
        background: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="text-center mb-5">
                <h1 class="display-4 text-primary">🎨 Demo Nuova Colorazione ZPeC</h1>
                <p class="lead">Sistema Professionisti con tema <strong>Rosso Scuro</strong></p>
            </div>
            
            <!-- Buttons Demo -->
            <div class="demo-section">
                <h3 class="text-primary mb-4">🔘 Bottoni e Componenti</h3>
                <div class="d-flex flex-wrap gap-3 mb-4">
                    <button class="btn btn-primary">Bottone Principale</button>
                    <button class="btn btn-outline-primary">Bottone Outline</button>
                    <button class="btn btn-primary btn-lg">Bottone Grande</button>
                    <button class="btn btn-primary btn-sm">Bottone Piccolo</button>
                </div>
                
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-primary">Badge Principale</span>
                    <span class="badge bg-secondary">Badge Secondario</span>
                    <span class="badge bg-success">Badge Successo</span>
                    <span class="badge bg-warning text-dark">Badge Avviso</span>
                    <span class="badge bg-danger">Badge Errore</span>
                </div>
            </div>
            
            <!-- Cards Demo -->
            <div class="demo-section">
                <h3 class="text-primary mb-4">📊 Cards e Statistiche</h3>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-primary me-3">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">125</h3>
                                    <small class="text-muted">Professionisti</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-success me-3">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">89</h3>
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
                                    <h3 class="mb-0">24</h3>
                                    <small class="text-muted">In Attesa</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stat-card">
                            <div class="d-flex align-items-center">
                                <div class="stat-icon bg-info me-3">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div>
                                    <h3 class="mb-0">12</h3>
                                    <small class="text-muted">Questo Mese</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Demo -->
            <div class="demo-section">
                <h3 class="text-primary mb-4">📝 Elementi Form</h3>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Input Text</label>
                            <input type="text" class="form-control" placeholder="Scrivi qualcosa...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Select</label>
                            <select class="form-select">
                                <option>Opzione 1</option>
                                <option>Opzione 2</option>
                                <option>Opzione 3</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Textarea</label>
                            <textarea class="form-control" rows="3" placeholder="Inserisci un messaggio..."></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked>
                            <label class="form-check-label">Checkbox attivo</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alerts Demo -->
            <div class="demo-section">
                <h3 class="text-primary mb-4">🚨 Alert e Messaggi</h3>
                <div class="alert alert-primary" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    Alert principale con il nuovo colore rosso scuro!
                </div>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    Operazione completata con successo!
                </div>
                <div class="alert alert-warning" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Attenzione: verifica i dati inseriti.
                </div>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    Errore: operazione non riuscita.
                </div>
            </div>
            
            <!-- Navigation Demo -->
            <div class="demo-section">
                <h3 class="text-primary mb-4">🧭 Navigazione</h3>
                <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, var(--zpec-primary-dark), var(--zpec-primary)); border-radius: 8px;">
                    <div class="container-fluid">
                        <a class="navbar-brand" href="#"><i class="fas fa-building me-2"></i>ZPeC</a>
                        <div class="navbar-nav">
                            <a class="nav-link active" href="#">Dashboard</a>
                            <a class="nav-link" href="#">Professionisti</a>
                            <a class="nav-link" href="#">Albi</a>
                            <a class="nav-link" href="#">Ricerca</a>
                        </div>
                    </div>
                </nav>
            </div>
            
            <!-- Color Palette -->
            <div class="demo-section">
                <h3 class="text-primary mb-4">🎨 Palette Colori</h3>
                <div class="row">
                    <div class="col-md-6">
                        <h5>Colori Principali</h5>
                        <div class="d-flex mb-3">
                            <div class="p-3 me-2" style="background: var(--zpec-primary); color: white; border-radius: 8px; min-width: 120px;">
                                <strong>Primario</strong><br>
                                <small>#8B0000</small>
                            </div>
                            <div class="p-3 me-2" style="background: var(--zpec-primary-light); color: white; border-radius: 8px; min-width: 120px;">
                                <strong>Chiaro</strong><br>
                                <small>#A52A2A</small>
                            </div>
                            <div class="p-3" style="background: var(--zpec-primary-dark); color: white; border-radius: 8px; min-width: 120px;">
                                <strong>Scuro</strong><br>
                                <small>#660000</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h5>Colori di Supporto</h5>
                        <div class="d-flex mb-3">
                            <div class="p-3 me-2 bg-success text-white" style="border-radius: 8px; min-width: 120px;">
                                <strong>Successo</strong><br>
                                <small>#28a745</small>
                            </div>
                            <div class="p-3 me-2 bg-warning text-dark" style="border-radius: 8px; min-width: 120px;">
                                <strong>Avviso</strong><br>
                                <small>#ffc107</small>
                            </div>
                            <div class="p-3 bg-danger text-white" style="border-radius: 8px; min-width: 120px;">
                                <strong>Errore</strong><br>
                                <small>#dc3545</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Links -->
            <div class="demo-section text-center">
                <h3 class="text-primary mb-4">🔗 Links di Test</h3>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="frontend/index.php" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Frontend Homepage
                    </a>
                    <a href="frontend/registrazione.php" class="btn btn-outline-primary">
                        <i class="fas fa-user-plus me-2"></i>Form Registrazione
                    </a>
                    <a href="backend/login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login Backend
                    </a>
                    <a href="backend/ricerca_avanzata.php" class="btn btn-outline-primary">
                        <i class="fas fa-search me-2"></i>Ricerca Avanzata
                    </a>
                </div>
                
                <div class="mt-4">
                    <p class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Login Backend: <strong>admin</strong> / <strong>admin123</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>