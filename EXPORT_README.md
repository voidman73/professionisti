# Funzionalità di Esportazione - Sistema Professionisti

## Formati Supportati

Il sistema ora supporta l'esportazione dei dati in tre formati:

1. **CSV** - Comma Separated Values (separatore: punto e virgola)
2. **XLSX** - Excel Spreadsheet (formato moderno)
3. **PDF** - Portable Document Format

## Come Utilizzare

### Dashboard Principale

1. **Esporta Dati (Pulsante in alto a destra)**
   - Clicca sul pulsante "Esporta Dati"
   - Seleziona il formato desiderato dal menu a tendina
   - Il file verrà scaricato con i filtri attuali applicati

2. **Esporta Selezionati (Tabella professionisti)**
   - Seleziona uno o più professionisti usando le checkbox
   - Clicca su "Esporta Selezionati"
   - Scegli il formato dal menu a tendina
   - Solo i professionisti selezionati verranno esportati

3. **Esporta Tutti (Tabella professionisti)**
   - Clicca su "Esporta Tutti"
   - Scegli il formato dal menu a tendina
   - Tutti i professionisti (con filtri attuali) verranno esportati

### Ricerca Avanzata

- Dopo aver eseguito una ricerca, clicca su "Esporta"
- Seleziona il formato desiderato
- I risultati della ricerca verranno esportati

## URL Diretti

È possibile accedere direttamente all'esportazione tramite URL:

```
# Esporta tutti in CSV
http://localhost/professionisti/backend/esporta.php?formato=csv

# Esporta tutti in Excel
http://localhost/professionisti/backend/esporta.php?formato=xlsx

# Esporta tutti in PDF
http://localhost/professionisti/backend/esporta.php?formato=pdf

# Esporta con filtri
http://localhost/professionisti/backend/esporta.php?stato=APPROVATO&formato=xlsx

# Esporta professionisti specifici
http://localhost/professionisti/backend/esporta.php?ids=1,2,3&formato=pdf
```

## Dati Esportati

I seguenti campi vengono esportati in tutti i formati:

- ID
- Nome e Cognome
- Codice Fiscale
- Partita IVA
- Email, Telefono, Cellulare
- Data e Luogo di Nascita
- Sesso
- Indirizzi (Residenza e Domicilio)
- Competenze Principali
- Titolo Professionale
- Disponibilità e Trasferte
- Raggio d'Azione
- Tariffe (Orarie e Giornaliere)
- Note Tariffe
- Stato
- Data Registrazione
- Privacy e Termini Accettati

## Caratteristiche Specifiche per Formato

### CSV
- Separatore: punto e virgola (;)
- Codifica: UTF-8 con BOM
- Compatibile con Excel e altri fogli di calcolo

### XLSX (Excel)
- Formato moderno Excel
- Headers formattati con stile blu
- Colonne auto-dimensionate
- Compatibile con Excel 2007+

### PDF
- Orientamento: orizzontale (landscape)
- Formato A4
- Tabella formattata con stili
- Header con informazioni di esportazione
- Compatibile con tutti i dispositivi

## Requisiti Tecnici

- PHP 7.4+
- Librerie installate via Composer:
  - `phpoffice/phpspreadsheet` (per XLSX)
  - `dompdf/dompdf` (per PDF)

## Installazione

Le librerie sono già installate. Se necessario, esegui:

```bash
composer install
```

## Note

- L'autenticazione admin è richiesta per tutte le esportazioni
- I file vengono generati dinamicamente e non vengono salvati sul server
- I nomi dei file includono timestamp per evitare conflitti
- Tutti i formati supportano i filtri della dashboard
