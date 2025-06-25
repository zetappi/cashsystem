# Procedura di Test Funzionalità

Questa guida spiega come testare il funzionamento dell'estensione Cash tramite l'endpoint di test.

## Prerequisiti

- Forum phpBB installato e funzionante
- Estensione Cash installata e attivata
- Permessi di amministratore per accedere alle funzionalità di test

## Procedura di Test

1. **Accedi** al forum con un account amministratore

2. **Apri** il seguente URL nel browser:
   ```
   https://tuo-dominio.com/app.php/cash/testapi
   ```
   Sostituisci `tuo-dominio.com` con l'URL del tuo forum.

3. **Verifica** il risultato:
   - Se l'endpoint è configurato correttamente, vedrai un output JSON con i risultati del test
   - Se ricevi un errore 404, verifica che l'URL sia corretto e che l'estensione sia installata
   - Se ricevi un errore di permesso, verifica di essere loggato come amministratore

4. **Interpreta** i risultati:
   - `status`: Indica se il test è andato a buon fine (success/error)
   - `messages`: Contiene i dettagli di ogni operazione di test eseguita
   - `data`: Dettagli aggiuntivi sulle operazioni eseguite

## Test Automatici

Sono disponibili anche test automatici che possono essere eseguiti tramite riga di comando:

```bash
# Esegui tutti i test
php ../../phpBB/vendor/bin/phpunit ../../ext/marcozp/cash/tests/

# Esegui solo i test API
php ../../phpBB/vendor/bin/phpunit ../../ext/marcozp/cash/tests/test_api.php

# Esegui solo i test dell'interfaccia web
php ../../phpBB/vendor/bin/phpunit ../../ext/marcozp/cash/tests/test_api_web.php
```

## Risoluzione dei Problemi

### Endpoint non trovato (404)
- Verifica che l'URL sia corretto
- Controlla che l'estensione sia installata e abilitata
- Svuota la cache di phpBB

### Errore di permessi
- Assicurati di essere loggato come amministratore
- Verifica i permessi dell'utente nel pannello di amministrazione

### Errori durante i test
- Controlla i log di errore di phpBB
- Verifica che il database sia stato aggiornato correttamente
- Controlla che tutte le dipendenze siano installate

## Note

- I test non modificano i dati esistenti nel database
- Vengono utilizzati dati di test temporanei che vengono rimossi al termine dell'esecuzione
- Per problemi persistenti, consulta la documentazione ufficiale o apri una issue sul repository ufficiale
