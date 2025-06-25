<?php
/**
 *
 * Cash Points System Extension for phpBB.
 * @package marcozp/cash
 *
 */


/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
    'ACP_CASH'                          => 'Sistema Punti Cash',
    'ACP_CASH_SETTINGS'                 => 'Impostazioni',
    'ACP_CASH_MANAGE_USERS'             => 'Gestisci Punti Utenti',
    'ACP_CASH_MANAGE_USERS_EXPLAIN'     => 'Da qui puoi aggiungere o sottrarre punti agli utenti.',
    'ACP_CASH_MANAGE_USER_POINTS'       => 'Gestisci Punti Utente',
    'ACP_CASH_MANAGE_USER_NAME_EXPLAIN' => 'Inserisci il nome utente a cui vuoi modificare i punti',
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
    'ACP_CASH_TOP_USERS'                => 'Classifica Utenti',
    'POINTS'                            => 'Punti',
    // Actions
    'ACTIONS'           => 'Azioni',
    'EDIT_POINTS'       => 'Modifica Punti',
    'PAGE_OF'           => 'Pagina %1$d di %2$d',
    'PREVIOUS'          => 'Precedente',
    'NEXT'              => 'Successivo',
));
