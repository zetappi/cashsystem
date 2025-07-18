<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = [];
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

$lang = array_merge($lang, [
    // ACP Modules
    'ACP_CASH'                      => 'Cash Points System',
    'ACP_CASH_SETTINGS'             => 'Cash Points Settings',
    'ACP_CASH_MANAGE_USERS'         => 'Manage User Points',
]);
