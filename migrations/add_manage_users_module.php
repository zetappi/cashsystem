<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash\migrations;

class add_manage_users_module extends \phpbb\db\migration\migration
{
    public function effectively_installed()
    {
        $sql = 'SELECT module_id
                FROM ' . $this->table_prefix . "modules
                WHERE module_class = 'acp'
                    AND module_langname = 'ACP_CASH_MANAGE_USERS'";
        $result = $this->db->sql_query($sql);
        $module_id = $this->db->sql_fetchfield('module_id');
        $this->db->sql_freeresult($result);

        return $module_id !== false;
    }

    static public function depends_on()
    {
        return ['\marcozp\cash\migrations\install_acp_module'];
    }

    public function update_data()
    {
        return [
            ['module.add', [
                'acp',
                'ACP_CASH', // The parent module
                [
                    'module_basename'   => '\marcozp\cash\acp\main_module',
                    'modes'             => ['manage_users'],
                ],
            ]],
        ];
    }
    
    public function revert_data()
    {
        return [
            ['module.remove', [
                'acp',
                'ACP_CASH',
                [
                    'module_basename'   => '\marcozp\cash\acp\main_module',
                    'modes'             => ['manage_users'],
                ],
            ]],
        ];
    }
}
