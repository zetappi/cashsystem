<?php
/**
 * Script di test per l'API dei punti
 * 
 * Utilizzo: php test_api.php
 * Deve essere eseguito dalla directory principale di phpBB
 */

// Percorso assoluto alla root di phpBB
$phpbb_root_path = '/var/www/html/web/';

// Verifica se siamo in un'installazione valida di phpBB
if (!file_exists($phpbb_root_path . 'config.php')) {
    die("ERRORE: Impossibile trovare config.php in {$phpbb_root_path}. Verifica il percorso.\n");
}

// Imposta l'ambiente phpBB
define('IN_PHPBB', true);
$phpEx = 'php';

try {
    // Carica l'ambiente phpBB
    require($phpbb_root_path . 'common.' . $phpEx);
    require($phpbb_root_path . 'includes/functions_display.' . $phpEx);

    // Avvia la sessione
    $user->session_begin();
    $auth->acl($user->data);

    // Verifica i permessi di amministratore
    if ($user->data['user_type'] != USER_FOUNDER) {
        die("ERRORE: Devi essere un amministratore per eseguire questo test\n");
    }

    // Funzione di utilità per formattare l'output
    function print_header($title) {
        echo "\n\n" . str_repeat("=", 80) . "\n";
        echo "TEST: $title\n";
        echo str_repeat("=", 80) . "\n";
    }

    // Ottieni l'istanza del container
    global $phpbb_container;
    if (!isset($phpbb_container)) {
        throw new \RuntimeException("Impossibile accedere al container di phpBB");
    }
    
    // Verifica se il servizio esiste
    if (!$phpbb_container->has('marcozp.cash.api.points')) {
        throw new \RuntimeException("Il servizio 'marcozp.cash.api.points' non è stato trovato. Assicurati che l'estensione sia abilitata.");
    }
    
    $pointsApi = $phpbb_container->get('marcozp.cash.api.points');
    
    // ID utente di test (usa l'utente corrente)
    $testUserId = $user->data['user_id'];
    $testUsername = $user->data['username_clean'];
    
    echo "=== TEST API PUNTI UTENTE ===\n";
    echo "Utente: {$testUsername} (ID: {$testUserId})\n\n";
    
    // Test 1: Verifica permessi
    print_header("Test permessi utente");
    $canModify = $pointsApi->canUserModifyPoints($testUserId, $testUserId);
    echo "L'utente PUO' " . ($canModify ? "" : "NON ") . "modificare i propri punti\n";
    
    // Test 2: Ottieni punti attuali
    print_header("Lettura punti utente");
    $currentPoints = $pointsApi->getUserPoints($testUserId);
    echo "Punti attuali: {$currentPoints}\n";
    
    // Test 3: Aggiungi punti
    print_header("Aggiunta punti");
    $pointsToAdd = 10;
    echo "Aggiungo {$pointsToAdd} punti...\n";
    $pointsApi->addPoints($testUserId, $pointsToAdd, 'test.add_points', 'Test aggiunta punti');
    $newPoints = $pointsApi->getUserPoints($testUserId);
    echo "Nuovo totale: {$newPoints}\n";
    
    // Test 4: Sottrai punti
    print_header("Sottrazione punti");
    $pointsToSubtract = 5;
    echo "Sottraggo {$pointsToSubtract} punti...\n";
    $pointsApi->addPoints($testUserId, -$pointsToSubtract, 'test.subtract_points', 'Test sottrazione punti');
    $finalPoints = $pointsApi->getUserPoints($testUserId);
    echo "Punti finali: {$finalPoints}\n";
    
    // Test 5: Verifica eccezione per utente inesistente
    print_header("Test utente inesistente");
    try {
        $pointsApi->getUserPoints(999999);
        echo "ERRORE: L'eccezione UserNotFoundException non è stata lanciata\n";
    } catch (\marcozp\cash\service\api\exception\UserNotFoundException $e) {
        echo "SUCCESSO: Eccezione UserNotFoundException catturata correttamente\n";
        echo "Messaggio: " . $e->getMessage() . "\n";
    }
    
    // Test 6: Verifica eccezione per punti insufficienti
    print_header("Test punti insufficienti");
    try {
        $pointsApi->addPoints($testUserId, -999999, 'test.insufficient_points', 'Test punti insufficienti');
        echo "ERRORE: L'eccezione NotEnoughPointsException non è stata lanciata\n";
    } catch (\marcozp\cash\service\api\exception\NotEnoughPointsException $e) {
        echo "SUCCESSO: Eccezione NotEnoughPointsException catturata correttamente\n";
        echo "Messaggio: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== TUTTI I TEST SONO STATI COMPLETATI CON SUCCESSO ===\n";
    
} catch (\Exception $e) {
    echo "\n\nERRORE CRITICO DURANTE L'ESECUZIONE:\n";
    echo get_class($e) . ": " . $e->getMessage() . "\n";
    
    if (isset($phpbb_root_path) && is_dir($phpbb_root_path . 'includes/')) {
        // Mostra il trace solo in ambiente di sviluppo
        if (file_exists($phpbb_root_path . 'config_dev.php')) {
            echo "\nDettagli:\n";
            echo "File: " . $e->getFile() . " (Linea: " . $e->getLine() . ")\n";
            echo "\nTrace:\n" . $e->getTraceAsString() . "\n";
        }
    }
}

// Chiudi la sessione se esiste
if (isset($user) && is_object($user) && method_exists($user, 'session_kill')) {
    $user->session_kill();
}
?>
