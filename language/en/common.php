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
    // ACP
    'ACP_CASH'                      => 'Cash Points System',
    'ACP_CASH_SETTINGS'             => 'Cash Points Settings',
    'ACP_CASH_SETTINGS_EXPLAIN'     => 'Here you can configure the Cash Points System extension.',
    'ACP_CASH_SETTINGS_UPDATED'     => 'Cash Points settings have been updated successfully.',
    'ACP_CASH_POINTS_RESET'         => 'All Cash Points have been reset successfully.',
    'ACP_CASH_CONFIRM_RESET'        => 'Are you sure you want to reset all Cash Points? This action cannot be undone!',
    'ACP_CASH_MANAGE_USERS'         => 'Manage User Points',
    'ACP_CASH_MANAGE_USERS_EXPLAIN' => 'Here you can add or subtract points from individual users.',
    'ACP_CASH_MANAGE_USER_POINTS'   => 'Manage User Points',
    'ACP_CASH_MANAGE_USER_NAME_EXPLAIN' => 'Enter the username of the user you want to manage points for.',
    'ACP_CASH_POINTS_AMOUNT'        => 'Points Amount',
    'ACP_CASH_POINTS_AMOUNT_EXPLAIN' => 'Number of points to add or subtract.',
    'ACP_CASH_POINTS_ACTION'        => 'Action',
    'ACP_CASH_POINTS_ACTION_EXPLAIN' => 'Choose whether to add or subtract points.',
    'ACP_CASH_POINTS_ADD'           => 'Add Points',
    'ACP_CASH_POINTS_SUBTRACT'      => 'Subtract Points',
    'ACP_CASH_POINTS_REASON'        => 'Reason',
    'ACP_CASH_POINTS_REASON_EXPLAIN' => 'Optional reason for adding or subtracting points.',
    'ACP_CASH_NO_USER'              => 'You must specify a username.',
    'ACP_CASH_INVALID_POINTS'       => 'The points amount must be greater than zero.',
    'ACP_CASH_POINTS_ADDED'         => '%1$s points have been added to %2$s successfully.',
    'ACP_CASH_POINTS_SUBTRACTED'    => '%1$s points have been subtracted from %2$s successfully.',
    'ACP_CASH_POINTS_ERROR'         => 'An error occurred while updating the points.',
    'ACP_CASH_NOT_ENOUGH_POINTS'    => 'The user %s does not have enough points.',
    
    // Settings
    'CASH_POINTS_PER_POST'          => 'Points per post',
    'CASH_POINTS_PER_POST_EXPLAIN'  => 'Number of points awarded for each post.',
    'CASH_POINTS_PER_TOPIC'         => 'Points per topic',
    'CASH_POINTS_PER_TOPIC_EXPLAIN' => 'Number of points awarded for each new topic.',
    'CASH_POINTS_PER_POLL_VOTE'     => 'Points per poll vote',
    'CASH_POINTS_PER_POLL_VOTE_EXPLAIN' => 'Number of points awarded for each poll vote.',
    'CASH_POINTS_PER_PAGE_VIEW'     => 'Points per page view',
    'CASH_POINTS_PER_PAGE_VIEW_EXPLAIN' => 'Number of points awarded for each page view.',
    'CASH_MAX_DAILY_POINTS'         => 'Maximum daily points',
    'CASH_MAX_DAILY_POINTS_EXPLAIN' => 'Maximum number of points a user can earn per day.',
    'CASH_SHOW_BILLBOARD_LINK'      => 'Show Cash Points link',
    'CASH_SHOW_BILLBOARD_LINK_EXPLAIN' => 'Choose whether to show or hide the Cash Points link in the navigation bar.',
    'CASH_RESET_POINTS'             => 'Reset all points',
    'CASH_RESET_POINTS_EXPLAIN'     => 'This will reset all users\' points to zero.',
    
    // Billboard
    'CASH_BILLBOARD'                => 'Cash Points Billboard',
    'CASH_YOUR_POINTS'              => 'Your points',
    'CASH_RANK'                     => 'Rank',
    'CASH_USERNAME'                 => 'Username',
    'CASH_POINTS'                   => 'Points',
    'CASH_NO_USERS'                 => 'No users have earned points yet.',
    
    // Profile
    'CASH_PROFILE_POINTS'           => 'Cash Points',
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

]);
