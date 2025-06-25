# Guida all'Integrazione con l'API Punti

## Indice
1. [Introduzione](#introduzione)
2. [Installazione](#installazione)
3. [Utilizzo Base](#utilizzo-base)
4. [Metodi Disponibili](#metodi-disponibili)
5. [Gestione Errori](#gestione-errori)
6. [Esempi Pratici](#esempi-pratici)
7. [Sicurezza](#sicurezza)

## Introduzione
Questo documento descrive come integrare il sistema di gestione punti (Cash) in altre estensioni phpBB attraverso un'API dedicata. L'API fornisce metodi per gestire i punti degli utenti in modo sicuro e controllato.

## Installazione
L'API è inclusa nell'estensione Cash e viene registrata automaticamente. Assicurati che:
1. L'estensione Cash sia installata e abilitata
2. L'estensione che utilizza l'API abbia `marcozp/cash` come dipendenza in `composer.json`

## Utilizzo Base
Per utilizzare l'API in un'altra estensione:

```php
// Ottieni l'istanza dell'API
$pointsApi = $phpbb_container->get('marcozp.cash.api.points');

// Esempio: Aggiungi punti a un utente
$pointsApi->addPoints($userId, 10, 'extension_name.reward', 'Motivo del premio');
```

## Metodi Disponibili

### `getUserPoints(int $userId): int`
Restituisce il punteggio totale di un utente.

**Parametri:**
- `$userId`: ID dell'utente

**Ritorna:**
- `int` Punteggio totale dell'utente

---

### `addPoints(int $userId, int $points, string $action, string $data = ''): bool`
Aggiunge o sottrae punti a un utente.

**Parametri:**
- `$userId`: ID dell'utente
- `$points`: Punti da aggiungere (valori negativi per sottrarre)
- `$action`: Identificativo univoco dell'azione (es: 'forum.post', 'shop.purchase')
- `$data`: Dati aggiuntivi opzionali

**Ritorna:**
- `bool` True in caso di successo

**Eccezioni:**
- `NotEnoughPointsException` se l'utente non ha abbastanza punti
- `UserNotFoundException` se l'utente non esiste

---

### `canUserModifyPoints(int $modifierId, int $targetUserId): bool`
Verifica se un utente può modificare i punti di un altro utente.

**Parametri:**
- `$modifierId`: ID dell'utente che richiede la modifica
- `$targetUserId`: ID dell'utente target

**Ritorna:**
- `bool` True se l'utente può modificare i punti

## Gestione Errori
L'API utilizza eccezioni specifiche per la gestione degli errori:

```php
try {
    $pointsApi->addPoints($userId, -50, 'shop.purchase');
} catch (\marcozp\cash\service\api\exception\NotEnoughPointsException $e) {
    // Gestisci errore punti insufficienti
} catch (\marcozp\cash\service\api\exception\UserNotFoundException $e) {
    // Gestisci utente non trovato
}
```

## Esempi Pratici

### 1. Sistema di Premi per i Post
```php
// In un listener per l'evento submit_post_end
public function on_submit_post($event) {
    $postData = $event['data'];
    $pointsApi = $this->container->get('marcozp.cash.api.points');
    
    // Aggiungi 10 punti per un nuovo post
    if ($event['mode'] == 'post') {
        $pointsApi->addPoints(
            $postData['poster_id'],
            10,
            'forum.new_post',
            'post_id:' . $postData['post_id']
        );
    }
}
```

### 2. Negozio Virtuale
```php
public function purchaseItem($userId, $itemId, $itemCost) {
    $pointsApi = $this->container->get('marcozp.cash.api.points');
    
    try {
        // Sottrai i punti
        $pointsApi->addPoints($userId, -$itemCost, 'shop.purchase', 'item:' . $itemId);
        
        // Completa l'acquisto
        return $this->completePurchase($userId, $itemId);
        
    } catch (\marpozp\cash\service\api\exception\NotEnoughPointsException $e) {
        throw new \RuntimeException('Punti insufficienti per completare l\'acquisto');
    }
}
```

## Sicurezza
1. **Validazione degli Input**: Tutti gli input vengono validati
2. **Autorizzazioni**: Verifica sempre i permessi con `canUserModifyPoints()`
3. **Log**: Tutte le operazioni vengono registrate
4. **Transazioni**: Le operazioni critiche sono eseguite in transazioni atomiche

## Best Practice
1. **Prefissi Unici**: Usa prefissi univoci per le tue azioni (es: `tuoa_extension.azione`)
2. **Gestione Errori**: Implementa sempre la gestione delle eccezioni
3. **Performance**: Evita operazioni sui punti in loop o in operazioni batch
4. **Test**: Verifica sempre il comportamento con utenti con diversi livelli di permessi

## Supporto
Per problemi o domande, apri una issue sul repository ufficiale dell'estensione Cash.
