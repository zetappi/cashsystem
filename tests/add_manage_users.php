<?php
// Questo script aggiunge manualmente la modalità "manage_users" al modulo ACP esistente

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../../../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Avvia la sessione
$user->session_begin();
$auth->acl($user->data);
$user->setup('acp/common');

// Verifica se l'utente è un amministratore
if (!$auth->acl_get('a_'))
{
    trigger_error('NOT_ADMIN');
}

// Trova l'ID del modulo ACP_CASH
$sql = 'SELECT module_id FROM ' . MODULES_TABLE . "
        WHERE module_langname = 'ACP_CASH'
        AND module_class = 'acp'";
$result = $db->sql_query($sql);
$parent_id = (int) $db->sql_fetchfield('module_id');
$db->sql_freeresult($result);

if (!$parent_id)
{
    trigger_error('Modulo ACP_CASH non trovato.');
}

// Verifica se la modalità manage_users esiste già
$sql = 'SELECT module_id FROM ' . MODULES_TABLE . "
        WHERE module_langname = 'ACP_CASH_MANAGE_USERS'
        AND parent_id = " . $parent_id;
$result = $db->sql_query($sql);
$exists = $db->sql_fetchfield('module_id');
$db->sql_freeresult($result);

if ($exists)
{
    trigger_error('La modalità manage_users esiste già.');
}

// Ottieni il left_id e right_id per il nuovo modulo
$sql = 'SELECT right_id FROM ' . MODULES_TABLE . '
        WHERE module_id = ' . $parent_id;
$result = $db->sql_query($sql);
$right_id = (int) $db->sql_fetchfield('right_id');
$db->sql_freeresult($result);

// Aggiorna i right_id e left_id di tutti i moduli successivi
$sql = 'UPDATE ' . MODULES_TABLE . '
        SET right_id = right_id + 2
        WHERE right_id >= ' . $right_id;
$db->sql_query($sql);

$sql = 'UPDATE ' . MODULES_TABLE . '
        SET left_id = left_id + 2
        WHERE left_id >= ' . $right_id;
$db->sql_query($sql);

// Inserisci il nuovo modulo
$module_data = array(
    'module_enabled'    => 1,
    'module_display'    => 1,
    'module_basename'   => '\marcozp\cash\acp\main_module',
    'module_class'      => 'acp',
    'parent_id'         => $parent_id,
    'left_id'           => $right_id,
    'right_id'          => $right_id + 1,
    'module_langname'   => 'ACP_CASH_MANAGE_USERS',
    'module_mode'       => 'manage_users',
    'module_auth'       => 'ext_marcozp/cash && acl_a_board',
);

$sql = 'INSERT INTO ' . MODULES_TABLE . ' ' . $db->sql_build_array('INSERT', $module_data);
$db->sql_query($sql);

// Svuota la cache
$cache->purge();

trigger_error('Modalità manage_users aggiunta con successo al modulo ACP_CASH.');
