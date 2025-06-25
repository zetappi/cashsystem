# Struttura del Database - Cash Points System

Questo documento descrive la struttura delle tabelle del database utilizzate dall'estensione Cash Points System per phpBB. Queste informazioni sono fornite per facilitare l'integrazione con altre estensioni che potrebbero voler interagire con il sistema dei punti.

## Tabelle

L'estensione utilizza tre tabelle principali:

### 1. {table_prefix}cash_points

Questa tabella memorizza i punti totali e giornalieri di ciascun utente.

| Colonna | Tipo | Default | Descrizione |
|---------|------|---------|-------------|
| user_id | UINT | 0 | ID dell'utente (chiave primaria) |
| points_total | UINT | 0 | Punti totali accumulati dall'utente |
| points_today | UINT | 0 | Punti accumulati oggi dall'utente |
| last_activity | TIMESTAMP | 0 | Timestamp dell'ultima attività che ha generato punti |

### 2. {table_prefix}cash_logs

Questa tabella registra tutte le transazioni di punti (aggiunte e sottrazioni).

| Colonna | Tipo | Default | Descrizione |
|---------|------|---------|-------------|
| log_id | UINT | auto_increment | ID del log (chiave primaria) |
| user_id | UINT | 0 | ID dell'utente |
| log_time | TIMESTAMP | 0 | Timestamp della transazione |
| log_type | VARCHAR(32) | '' | Tipo di transazione (post, topic, poll_vote, page_view, admin_add, admin_subtract, ecc.) |
| log_points | INT(11) | 0 | Numero di punti aggiunti o sottratti (valore negativo per sottrazioni) |
| log_data | TEXT | '' | Dati aggiuntivi relativi alla transazione (es. ID del post, ID del topic, ecc.) |

Indici:
- user_id (INDEX)
- log_time (INDEX)
- log_type (INDEX)

### 3. {table_prefix}cash_settings

Questa tabella memorizza le impostazioni di configurazione del sistema dei punti.

| Colonna | Tipo | Default | Descrizione |
|---------|------|---------|-------------|
| setting_name | VARCHAR(255) | '' | Nome dell'impostazione (chiave primaria) |
| setting_value | VARCHAR(255) | '' | Valore dell'impostazione |

## Impostazioni predefinite

Le seguenti impostazioni sono create durante l'installazione dell'estensione e sono definite tramite ACP, possono variare:

| Impostazione | Valore predefinito | Descrizione |
|--------------|-------------------|-------------|
| points_per_post | 10 | Punti assegnati per ogni nuovo post |
| points_per_topic | 15 | Punti assegnati per ogni nuovo topic |
| points_per_poll_vote | 5 | Punti assegnati per ogni voto in un sondaggio |
| points_per_page_view | 1 | Punti assegnati per ogni visualizzazione di pagina |
| max_daily_points | 100 | Limite massimo di punti che un utente può guadagnare in un giorno |
| show_billboard_link | 1 | Mostra (1) o nasconde (0) il link alla classifica nella barra di navigazione |

## Integrazione con altre estensioni

### Accesso ai punti di un utente

Per accedere ai punti di un utente, è possibile utilizzare il servizio `points_manager`:

```php
// Ottieni i punti dell'utente
$user_data = $points_manager->get_user_points($user_id);
$points_total = $user_data['points_total'];
```

### Aggiunta di punti

Per aggiungere punti a un utente:

```php
// Aggiungi 50 punti all'utente
$points_manager->add_points($user_id, 50, 'custom_action', 'Dati aggiuntivi');
```

### Sottrazione di punti

Per sottrarre punti a un utente:

```php
// Sottrai 25 punti all'utente
$points_manager->subtract_points($user_id, 25, 'custom_action', 'Dati aggiuntivi');
```

### Ottenere la classifica

Per ottenere la classifica degli utenti con più punti:

```php
// Ottieni i primi 10 utenti
$top_users = $points_manager->get_top_users(10);
```

## Note importanti

1. Tutte le operazioni sui punti vengono registrate nella tabella `{table_prefix}cash_logs`.
2. Il sistema rispetta il limite giornaliero di punti configurato in `max_daily_points`.
3. Gli utenti anonimi (ANONYMOUS) sono esclusi dal sistema dei punti.
4. I punti giornalieri vengono azzerati automaticamente quando l'utente accede in un nuovo giorno.

---

Documento generato l'11 aprile 2025
