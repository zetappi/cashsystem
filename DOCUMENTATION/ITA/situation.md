# Analisi Estensione Cash Points

## Registrazione nel Sistema
- **File principale**: `ext.php`
- **Classe**: `marcozp\cash\ext`
- **Metodi chiave**:
  - `is_enableable()`: Verifica compatibilità phpBB
  - `enable_step()`: Creazione tabelle e impostazioni
  - `purge_step()`: Rimozione tabelle

## Procedure di Migrazione

### `install_acp_module.php`
- Aggiunge modulo ACP per le impostazioni
- Imposta versione estensione (1.0.0)
- Dipendenze: `phpbb\db\migration\data\v330\v330`

### `add_manage_users_module.php`
- Aggiunge modulo ACP per gestione utenti
- Dipendenze: `install_acp_module`

## Struttura Database

### Tabelle Principali
1. `phpbb_cash_points`
   - `user_id` (chiave primaria)
   - `points_total`
   - `points_today`
   - `last_activity`

2. `phpbb_cash_logs`
   - `log_id` (auto increment)
   - `user_id`
   - `log_time`
   - `log_type`
   - `log_points`
   - `log_data`

3. `phpbb_cash_settings`
   - `setting_name` (chiave primaria)
   - `setting_value`

## Impostazioni Predefinite
```yaml
points_per_post: 10
points_per_topic: 15
points_per_poll_vote: 5
points_per_page_view: 1
max_daily_points: 100
show_billboard_link: 1
```

## Struttura Directory
- `ext/
  └── marcozp/
      └── cash/
          ├── add_manage_users.php    # Script per l'aggiunta della gestione utenti
          ├── clean_tables.php        # Script per la pulizia delle tabelle
          ├── composer.json           # Configurazione Composer
          ├── database_structure.md   # Documentazione struttura database
          ├── ext.php                 # File principale dell'estensione
          ├── installer.php           # Script di installazione
          ├── situation.md           # Questo file di documentazione
          ├── acp/                    # Moduli ACP
          │   ├── main_info.php      # Informazioni del modulo ACP
          │   └── main_module.php    # Implementazione del modulo ACP
          ├── adm/                    # File di amministrazione
          │   └── style/
          │       ├── acp_cash_manage_users.html  # Template gestione utenti
          │       └── acp_cash_settings.html   # Template impostazioni estensione
          ├── config/                 # File di configurazione
          │   ├── routing.yml        # Configurazione delle rotte
          │   └── services.yml       # Configurazione dei servizi
          ├── controller/             # Controller
          │   ├── acp_controller.php  # Controller ACP (Gestione punti utente)
          │   └── billboard_controller.php  # Controller bacheca punti
          ├── event/                  # Listener eventi
          │   └── main_listener.php   # Gestione degli eventi dell'estensione
          ├── language/               # File di lingua
          │   ├── en/                 # Inglese
          │   │   ├── acp/
          │   │   │   └── cash_common.php  # Testi ACP in inglese
          │   │   ├── common.php      # Testi comuni in inglese
          │   │   └── info_acp_cash.php  # Informazioni ACP in inglese
          │   └── it/                 # Italiano
          │       ├── acp/
          │       │   └── cash_common.php  # Testi ACP in italiano
          │       ├── common.php      # Testi comuni in italiano
          │       └── info_acp_cash.php  # Informazioni ACP in italiano
          ├── migrations/             # Migrazioni database
          │   ├── add_manage_users_module.php  # Aggiunge il modulo di gestione utenti
          │   └── install_acp_module.php       # Installa il modulo ACP
          ├── service/                # Logica business
          │   └── points_manager.php  # Gestione logica dei punti utente
          └── styles/                 # Stili e template
              └── all/
                  └── template/event/
                      ├── memberlist_view_user_statistics_after.html  # Template per visualizzazione punti nel profilo utente
                      ├── overall_header_navigation_append.html       # Aggiunta link nel menu di navigazione
                      └── viewtopic_body_postrow_custom_fields_after.html  # Visualizzazione punti nei post del forum

## Situazione Attuale Estensione Cash

## Funzionalità Implementate

### Gestione Punti Utente
- Aggiunta e sottrazione punti utente
- Cron per l'aggiornamento giornaliero dei punti
- Classifica utenti con punti più alti
- Gestione punti tramite ACP

### Pannello di Amministrazione (ACP)
- Interfaccia per la gestione dei punti utente
- Visualizzazione classifica utenti con paginazione
- Form per l'aggiunta/sottrazione punti
- Ricerca utenti con autocompletamento

### Miglioramenti Recenti (Giugno 2025)

#### Interfaccia Utente
- Tabella classifica utenti ridimensionata al 50% della larghezza
- Miglioramento della leggibilità con colorazione a righe alternate
- Aggiunta icona di modifica rapida punti
- Paginazione migliorata con navigazione next/previous
- Formattazione numeri per migliore leggibilità

#### Funzionalità
- Paginazione della classifica utenti (20 utenti per pagina)
- Pulsante di modifica rapida punti con precompilazione automatica
- Ordinamento utenti per punteggio decrescente
- Gestione degli stati disabilitati per i pulsanti di navigazione

#### Performance
- Ottimizzazione query per la paginazione
- Ridotto il numero di query al database
- Migliorata la gestione della cache

## Prossimi Sviluppi
- [ ] Aggiunta di filtri per la ricerca utenti
- [ ] Esportazione classifica in CSV/PDF
- [ ] Statistiche avanzate sui punti
- [ ] Log delle transazioni dettagliato

## Note
- L'estensione è attualmente in fase di sviluppo attivo
- La documentazione è in costante aggiornamento
- Si consiglia di effettuare backup regolari durante gli aggiornamenti
