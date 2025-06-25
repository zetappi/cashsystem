<?php
/**
 *
 * Cash Points System Extension for phpBB.
 * @package marcozp/cash
 *
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    // ACP
    'ACP_CASH'                          => 'Sistema Punti Cash',
    'ACP_CASH_SETTINGS'                 => 'Impostazioni Punti Cash',
    'ACP_CASH_SETTINGS_EXPLAIN'         => 'Qui puoi configurare l\'estensione Sistema Punti Cash.',
    'ACP_CASH_SETTINGS_UPDATED'         => 'Le impostazioni dei Punti Cash sono state aggiornate con successo.',
    'ACP_CASH_POINTS_RESET'             => 'Tutti i Punti Cash sono stati azzerati con successo.',
    'ACP_CASH_CONFIRM_RESET'            => 'Sei sicuro di voler azzerare tutti i Punti Cash? Questa azione non può essere annullata!',
    'ACP_CASH_MANAGE_USERS'             => 'Gestisci Punti Utenti',
    'ACP_CASH_MANAGE_USERS_EXPLAIN'     => 'Da qui puoi aggiungere o sottrarre punti agli utenti.',
    'ACP_CASH_MANAGE_USER_POINTS'       => 'Gestisci Punti Utente',
    'ACP_CASH_MANAGE_USER_NAME_EXPLAIN' => 'Inserisci il nome utente a cui vuoi modificare i punti',
    'ACP_CASH_TOP_USERS'                => 'Classifica Utenti',
    'POINTS'                            => 'Punti',
    
    // Settings
    'CASH_POINTS_PER_POST'              => 'Punti per messaggio',
    'CASH_POINTS_PER_POST_EXPLAIN'      => 'Numero di punti assegnati per ogni messaggio.',
    'CASH_POINTS_PER_TOPIC'             => 'Punti per discussione',
    'CASH_POINTS_PER_TOPIC_EXPLAIN'     => 'Numero di punti assegnati per ogni nuova discussione.',
    'CASH_POINTS_PER_POLL_VOTE'         => 'Punti per voto sondaggio',
    'CASH_POINTS_PER_POLL_VOTE_EXPLAIN' => 'Numero di punti assegnati per ogni voto nei sondaggi.',
    'CASH_POINTS_PER_PAGE_VIEW'         => 'Punti per visualizzazione pagina',
    'CASH_POINTS_PER_PAGE_VIEW_EXPLAIN' => 'Numero di punti assegnati per ogni visualizzazione di pagina.',
    'CASH_MAX_DAILY_POINTS'             => 'Punti giornalieri massimi',
    'CASH_MAX_DAILY_POINTS_EXPLAIN'     => 'Numero massimo di punti che un utente può guadagnare al giorno.',
    'CASH_SHOW_BILLBOARD_LINK'          => 'Mostra link Punti VCash',
    'CASH_SHOW_BILLBOARD_LINK_EXPLAIN'  => 'Scegli se mostrare o nascondere il link alla pagina Punti VCash nella barra di navigazione.',
    'CASH_RESET_POINTS'                 => 'Azzera tutti i punti',
    'CASH_RESET_POINTS_EXPLAIN'         => 'Questo azzererà i punti di tutti gli utenti.',
    
    // Points Management
    'ACP_CASH_POINTS_AMOUNT'            => 'Quantità di Punti',
    'ACP_CASH_POINTS_AMOUNT_EXPLAIN'    => 'Inserisci il numero di punti da aggiungere o sottrarre',
    'ACP_CASH_POINTS_ACTION'            => 'Azione',
    'ACP_CASH_POINTS_ACTION_EXPLAIN'    => 'Scegli se aggiungere o sottrarre i punti',
    'ACP_CASH_POINTS_ADD'               => 'Aggiungi',
    'ACP_CASH_POINTS_SUBTRACT'          => 'Sottrai',
    'ACP_CASH_POINTS_REASON'            => 'Motivo',
    'ACP_CASH_POINTS_REASON_EXPLAIN'    => 'Inserisci un motivo per questa modifica (opzionale)',
    'ACP_CASH_POINTS_ADDED'             => 'Punti aggiunti con successo',
    'ACP_CASH_POINTS_SUBTRACTED'        => 'Punti sottratti con successo',
    'ACP_CASH_POINTS_ERROR'             => 'Si è verificato un errore durante l\'aggiornamento dei punti',
    'ACP_CASH_NO_USER'                  => 'Devi specificare un nome utente.',
    'ACP_CASH_INVALID_POINTS'           => 'Devi inserire un numero di punti valido maggiore di zero.',
    'ACP_CASH_NOT_ENOUGH_POINTS'        => '%s non ha abbastanza punti.',
    
    // Billboard
    'CASH_BILLBOARD'                    => 'VCash',
    'CASH_YOUR_POINTS'                  => 'I tuoi punti',
    'CASH_RANK'                         => 'Posizione',
    'CASH_USERNAME'                     => 'Nome utente',
    'CASH_NO_USERS'                     => 'Nessun utente ha ancora guadagnato punti.',
    
    // Profile
    'CASH_PROFILE_POINTS'               => 'Punti VCash',
    // Actions
    'ACTIONS'           => 'Azioni',
    'EDIT_POINTS'       => 'Modifica Punti',
    'PAGE_OF'           => 'Pagina %1$d di %2$d',
    'PREVIOUS'          => 'Precedente',
    'NEXT'              => 'Successivo',
));
