# Documentazione API Gestione Punti

## Indice
1. [Introduzione](#introduzione)
2. [Prerequisiti](#prerequisiti)
3. [Ottenere l'istanza del servizio](#ottenere-listanza-del-servizio)
4. [Operazioni disponibili](#operazioni-disponibili)
   - [Ottenere i punti di un utente](#ottenere-i-punti-di-un-utente)
   - [Aggiungere punti](#aggiungere-punti)
   - [Rimuovere punti](#rimuovere-punti)
   - [Verificare i permessi](#verificare-i-permessi)
5. [Casi d'uso comuni](#casi-duso-comuni)
   - [Premio per nuovo post](#premio-per-nuovo-post)
   - [Acquisto nel negozio](#acquisto-nel-negozio)
6. [Best practice](#best-practice)
7. [Convenzioni per i nomi delle azioni](#convenzioni-per-i-nomi-delle-azioni)
8. [Esempio completo in un'estensione](#esempio-completo-in-unestensione)
9. [Risoluzione dei problemi](#risoluzione-dei-problemi)

## Introduzione
Questa documentazione spiega come integrare l'API di gestione punti in altre estensioni phpBB, permettendo di gestire i punti utente in modo semplice e sicuro.

## Prerequisiti
- Estensione `marcozp/cash` installata e attiva
- Permessi appropriati per modificare i punti utente

## Ottenere l'istanza del servizio

```php
global $phpbb_container;

// Verifica che il servizio esista
if ($phpbb_container->has('marcozp.cash.api.points')) {
    $pointsApi = $phpbb_container->get('marcozp.cash.api.points');
} else {
    // Gestisci l'errore
    trigger_error('Estensione Cash non disponibile');
}
```

## Operazioni disponibili

### Ottenere i punti di un utente

```php
try {
    $userId = 123; // ID dell'utente
    $points = $pointsApi->getUserPoints($userId);
} catch (\marcozp\cash\service\api\exception\UserNotFoundException $e) {
    // Gestisci l'errore
}
```

### Aggiungere punti

```php
try {
    $pointsApi->addPoints(
        $userId,           // ID utente
        10,                // Punti da aggiungere (numero positivo)
        'action.identifier', // Identificativo azione
        'Dettagli aggiuntivi' // Opzionale
    );
} catch (\Exception $e) {
    // Gestisci l'errore
}
```

### Rimuovere punti

```php
try {
    // Per rimuovere punti, usa un numero negativo
    $pointsApi->addPoints($userId, -5, 'action.identifier', 'Dettagli');
} catch (\marcozp\cash\service\api\exception\NotEnoughPointsException $e) {
    // Punti insufficienti
}
```

### Verificare i permessi

```php
if ($pointsApi->canUserModifyPoints($modifierId, $targetUserId)) {
    // Operazione consentita
}
```

## Casi d'uso comuni

### Premio per nuovo post

```php
// In un listener per l'evento 'core.submit_post_end'
public function onPostSubmit($event)
{
    global $user, $phpbb_container;
    
    if (($pointsApi = $this->getPointsApi()) !== null) {
        try {
            $pointsApi->addPoints(
                $user->data['user_id'], 
                5, 
                'post.created', 
                'Post #' . $event['data']['post_id']
            );
        } catch (\Exception $e) {
            error_log('Errore aggiunta punti: ' . $e->getMessage());
        }
    }
}
```

### Acquisto nel negozio

```php
public function purchaseItem($userId, $itemId, $itemName, $cost)
{
    if (($pointsApi = $this->getPointsApi()) === null) {
        throw new \Exception('Sistema punti non disponibile');
    }
    
    try {
        // Verifica punti disponibili
        $currentPoints = $pointsApi->getUserPoints($userId);
        if ($currentPoints < $cost) {
            throw new \Exception('Punti insufficienti');
        }
        
        // Sottrai i punti
        $pointsApi->addPoints(
            $userId, 
            -$cost, 
            'shop.purchase', 
            "Acquisto: $itemName (ID: $itemId)"
        );
        
        return true;
    } catch (\Exception $e) {
        error_log('Errore acquisto: ' . $e->getMessage());
        throw $e;
    }
}
```

## Best practice

1. **Gestione errori**: Implementa sempre il blocco try-catch
2. **Logging**: Registra le operazioni importanti
3. **Sicurezza**: Verifica i permessi
4. **Performance**: Riduci al minimo le chiamate all'API
5. **Documentazione**: Documenta le azioni nel parametro `$data`

## Convenzioni per i nomi delle azioni

Usa il formato `tipo.azione`:
- `post.created`
- `topic.replied`
- `user.registered`
- `shop.purchase`
- `moderator.award`

## Esempio completo in un'estensione

```php
namespace vendor\yourextension\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    protected $pointsApi;
    protected $container;
    
    public function __construct(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    static public function getSubscribedEvents()
    {
        return [
            'core.user_setup' => 'load_language_on_setup',
            'core.submit_post_end' => 'on_post_submit',
        ];
    }
    
    protected function getPointsApi()
    {
        if ($this->pointsApi === null && $this->container->has('marcozp.cash.api.points')) {
            $this->pointsApi = $this->container->get('marcozp.cash.api.points');
        }
        return $this->pointsApi;
    }
    
    public function on_post_submit($event)
    {
        if (($pointsApi = $this->getPointsApi()) === null) {
            return;
        }
        
        try {
            $pointsApi->addPoints(
                (int) $event['data']['poster_id'],
                5,
                'post.created',
                'Post #' . $event['data']['post_id']
            );
        } catch (\Exception $e) {
            error_log('Errore aggiunta punti: ' . $e->getMessage());
        }
    }
}
```

## Risoluzione dei problemi

1. **Estensione non trovata**:
   - Verifica che l'estensione sia abilitata
   - Controlla i log di errore

2. **Permessi insufficienti**:
   - Verifica i permessi dell'utente
   - Usa `canUserModifyPoints()`

3. **Errori generici**:
   - Controlla i log di PHP
   - Verifica che l'ID utente sia valido
   - Assicurati di avere abbastanza punti prima di sottrarre

Per ulteriore assistenza, consulta la documentazione ufficiale o contatta lo sviluppatore.
