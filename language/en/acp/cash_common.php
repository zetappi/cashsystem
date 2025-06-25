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

$lang = array_merge($lang, array(
    'ACP_CASH'                          => 'Cash Points System',
    'ACP_CASH_SETTINGS'                 => 'Settings',
    'ACP_CASH_MANAGE_USERS'             => 'Manage Users Points',
    'ACP_CASH_MANAGE_USERS_EXPLAIN'     => 'From here you can add or subtract points from users.',
    'ACP_CASH_MANAGE_USER_POINTS'       => 'Manage User Points',
    'ACP_CASH_MANAGE_USER_NAME_EXPLAIN' => 'Enter the username whose points you want to modify',
    'ACP_CASH_POINTS_AMOUNT'            => 'Points Amount',
    'ACP_CASH_POINTS_AMOUNT_EXPLAIN'    => 'Enter the number of points to add or subtract',
    'ACP_CASH_POINTS_ACTION'            => 'Action',
    'ACP_CASH_POINTS_ACTION_EXPLAIN'    => 'Choose whether to add or subtract points',
    'ACP_CASH_POINTS_ADD'               => 'Add',
    'ACP_CASH_POINTS_SUBTRACT'          => 'Subtract',
    'ACP_CASH_POINTS_REASON'            => 'Reason',
    'ACP_CASH_POINTS_REASON_EXPLAIN'    => 'Enter a reason for this change (optional)',
    'ACP_CASH_POINTS_ADDED'             => 'Points successfully added',
    'ACP_CASH_POINTS_SUBTRACTED'        => 'Points successfully subtracted',
    'ACP_CASH_POINTS_ERROR'             => 'An error occurred while updating points',
    'ACP_CASH_TOP_USERS'                => 'Users Ranking',
    'POINTS'                            => 'Points',
    // Actions
    'ACTIONS'           => 'Actions',
    'EDIT_POINTS'       => 'Edit Points',
    'PAGE_OF'           => 'Page %1$d of %2$d',
    'PREVIOUS'          => 'Previous',
    'NEXT'              => 'Next',
));
