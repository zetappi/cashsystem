<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash\acp;

class main_info
{
    public function module()
    {
        return [
            'filename'    => '\marcozp\cash\acp\main_module',
            'title'       => 'ACP_CASH',
            'modes'       => [
                'settings'    => [
                    'title' => 'ACP_CASH_SETTINGS',
                    'auth'  => 'ext_marcozp/cash && acl_a_board',
                    'cat'   => ['ACP_CASH'],
                ],
                'manage_users'    => [
                    'title' => 'ACP_CASH_MANAGE_USERS',
                    'auth'  => 'ext_marcozp/cash && acl_a_board',
                    'cat'   => ['ACP_CASH'],
                ],
            ],
        ];
    }
}
