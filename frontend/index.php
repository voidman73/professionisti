<?php 
$page_title = 'Iscrizione Liberi Professionisti';
$css_path = '../assets/style.css';
$extra_css = [];
$inline_scripts = '';
include '../includes/header.php'; 
?>

<style>
    .hero-section {
        background: linear-gradient(135deg, var(--zpec-primary-dark) 0%, var(--zpec-primary) 100%);
        color: white;
        padding: 100px 0;
    }
    .feature-card {
        transition: transform 0.3s ease;
        height: 100%;
    }
    .feature-card:hover {
        transform: translateY(-5px);
    }
    .step-number {
        width: 50px;
        height: 50px;
        background: var(--zpec-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin: 0 auto 20px;
    }
    .navbar {
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
    }
</style>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-users-cog me-2"></i>
                Professionisti Partner
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#come-funziona">Come Funziona</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#vantaggi">Vantaggi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white px-3 ms-2" href="registrazione.php">Iscriviti Ora</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Entra nel nostro Network di Professionisti</h1>
                    <p class="lead mb-4">
                        Unisciti alla nostra rete di liberi professionisti qualificati e accedi a progetti stimolanti 
                        in diversi settori. La tua expertise è la nostra forza.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="registrazione.php" class="btn btn-light btn-lg">
                            <i class="fas fa-user-plus me-2"></i>Registrati Gratuitamente
                        </a>
                        <a href="#come-funziona" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-info-circle me-2"></i>Scopri di più
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-handshake" style="font-size: 300px; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Come Funziona -->
    <section id="come-funziona" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">Come Funziona</h2>
                    <p class="lead">Semplice, veloce e trasparente</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="step-number">1</div>
                        <h4>Registrati</h4>
                        <p>Compila il form di registrazione con i tuoi dati professionali, competenze e esperienza.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="step-number">2</div>
                        <h4>Valutazione</h4>
                        <p>Il nostro team valuterà il tuo profilo e le tue competenze per garantire la qualità del network.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="text-center">
                        <div class="step-number">3</div>
                        <h4>Inizia a Lavorare</h4>
                        <p>Una volta approvato, riceverai proposte di progetti in linea con le tue competenze.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Vantaggi -->
    <section id="vantaggi" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="display-5 fw-bold">Perché Scegliere Noi</h2>
                    <p class="lead">I vantaggi di far parte del nostro network</p>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100 border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-project-diagram fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Progetti di Qualità</h5>
                            <p class="card-text">Accesso a progetti selezionati e di alto livello da aziende certificate.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100 border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-euro-sign fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Pagamenti Sicuri</h5>
                            <p class="card-text">Pagamenti garantiti e puntuali, con condizioni contrattuali trasparenti.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100 border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-users fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Network Professionale</h5>
                            <p class="card-text">Entra a far parte di una community di professionisti qualificati.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100 border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Crescita Professionale</h5>
                            <p class="card-text">Opportunità di formazione e aggiornamento continuo.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100 border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                            <h5 class="card-title">Supporto Dedicato</h5>
                            <p class="card-text">Assistenza costante da parte del nostro team specializzato.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card feature-card h-100 border-0 shadow">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-clock fa-3x text-secondary mb-3"></i>
                            <h5 class="card-title">Flessibilità</h5>
                            <p class="card-text">Gestisci i tuoi tempi e scegli i progetti più adatti a te.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-4">Pronto a Iniziare?</h2>
                    <p class="lead mb-4">
                        Unisciti a centinaia di professionisti che hanno già scelto di far parte del nostro network.
                    </p>
                    <a href="registrazione.php" class="btn btn-light btn-lg">
                        <i class="fas fa-rocket me-2"></i>Inizia Ora - È Gratuito
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-users-cog me-2"></i>Professionisti Partner</h5>
                    <p>La piattaforma di riferimento per liberi professionisti qualificati.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; 2024 Professionisti Partner. Tutti i diritti riservati.</p>
                    <small>
                        <a href="#" class="text-white-50 me-3">Privacy Policy</a>
                        <a href="#" class="text-white-50 me-3">Termini di Servizio</a>
                        <a href="#" class="text-white-50">Contatti</a>
                    </small>
                </div>
            </div>
        </div>
    </footer>

<?php include '../includes/footer.php'; ?>