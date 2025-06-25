<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash;

/**
 * Cash Points System Extension base
 *
 * @package marcozp/cash
 */
class ext extends \phpbb\extension\base
{
    /**
     * Check whether the extension can be enabled.
     *
     * @return bool
     */
    public function is_enableable()
    {
        $config = $this->container->get('config');
        return version_compare($config['version'], '3.3.0', '>=');
    }
    
    /**
     * Enable extension if phpBB version requirement is met
     *
     * @return bool
     * @access public
     */
    public function enable_step($old_state)
    {
        if ($old_state === false)
        {
            // Create tables directly
            $this->create_tables();
        }
        
        return parent::enable_step($old_state);
    }
    
    /**
     * Create required tables
     *
     * @return void
     */
    protected function create_tables()
    {
        $db = $this->container->get('dbal.conn');
        $db_tools = $this->container->get('dbal.tools.factory')->get($db);
        $table_prefix = $this->container->getParameter('core.table_prefix');
        
        $tables = [
            $table_prefix . 'cash_points' => [
                'COLUMNS' => [
                    'user_id' => ['UINT', 0],
                    'points_total' => ['UINT', 0],
                    'points_today' => ['UINT', 0],
                    'last_activity' => ['TIMESTAMP', 0],
                ],
                'PRIMARY_KEY' => 'user_id',
            ],
            $table_prefix . 'cash_logs' => [
                'COLUMNS' => [
                    'log_id' => ['UINT', null, 'auto_increment'],
                    'user_id' => ['UINT', 0],
                    'log_time' => ['TIMESTAMP', 0],
                    'log_type' => ['VCHAR:32', ''],
                    'log_points' => ['INT:11', 0],
                    'log_data' => ['TEXT', ''],
                ],
                'PRIMARY_KEY' => 'log_id',
                'KEYS' => [
                    'user_id' => ['INDEX', 'user_id'],
                    'log_time' => ['INDEX', 'log_time'],
                    'log_type' => ['INDEX', 'log_type'],
                ],
            ],
            $table_prefix . 'cash_settings' => [
                'COLUMNS' => [
                    'setting_name' => ['VCHAR:255', ''],
                    'setting_value' => ['VCHAR:255', ''],
                ],
                'PRIMARY_KEY' => 'setting_name',
            ],
        ];

        foreach ($tables as $table_name => $table_data)
        {
            $db_tools->sql_create_table($table_name, $table_data);
        }

        // Insert default settings
        $default_settings = [
            'points_per_post' => 10,
            'points_per_topic' => 15,
            'points_per_poll_vote' => 5,
            'points_per_page_view' => 1,
            'max_daily_points' => 100,
            'show_billboard_link' => 1, // 1 = mostra, 0 = nascondi
        ];

        foreach ($default_settings as $name => $value)
        {
            // Verifica se l'impostazione esiste già
            $sql = 'SELECT setting_name FROM ' . $table_prefix . 'cash_settings' . " WHERE setting_name = '" . $db->sql_escape($name) . "'";
            $result = $db->sql_query($sql);
            $exists = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);
            
            if (!$exists)
            {
                $sql = 'INSERT INTO ' . $table_prefix . 'cash_settings' . ' ' . $db->sql_build_array('INSERT', [
                    'setting_name' => $name,
                    'setting_value' => $value,
                ]);
                $db->sql_query($sql);
            }
        }
    }
    
    /**
     * Disable extension
     *
     * @return bool
     * @access public
     */
    public function disable_step($old_state)
    {
        return parent::disable_step($old_state);
    }
    
    /**
     * Purge extension
     *
     * @return bool
     * @access public
     */
    public function purge_step($old_state)
    {
        if ($old_state === false)
        {
            // Remove tables directly
            $this->drop_tables();
        }
        
        return parent::purge_step($old_state);
    }
    
    /**
     * Drop the extension's tables
     *
     * @return void
     */
    protected function drop_tables()
    {
        $db = $this->container->get('dbal.conn');
        $db_tools = $this->container->get('dbal.tools.factory')->get($db);
        $table_prefix = $this->container->getParameter('core.table_prefix');
        
        $tables = [
            $table_prefix . 'cash_points',
            $table_prefix . 'cash_logs',
            $table_prefix . 'cash_settings',
        ];

        foreach ($tables as $table)
        {
            $db_tools->sql_table_drop($table);
        }
    }
}
