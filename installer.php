<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash;

class installer
{
    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\db\tools\tools_interface */
    protected $db_tools;

    /** @var string */
    protected $table_prefix;

    /**
     * Constructor
     *
     * @param \phpbb\db\driver\driver_interface $db         Database object
     * @param \phpbb\db\tools\tools_interface   $db_tools   Database tools object
     * @param string                           $table_prefix Table prefix
     */
    public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\db\tools\tools_interface $db_tools, $table_prefix)
    {
        $this->db = $db;
        $this->db_tools = $db_tools;
        $this->table_prefix = $table_prefix;
    }

    /**
     * Install the extension
     *
     * @return void
     */
    public function install()
    {
        $this->create_tables();
    }

    /**
     * Create required tables
     *
     * @return void
     */
    protected function create_tables()
    {
        $tables = [
            $this->table_prefix . 'cash_points' => [
                'COLUMNS' => [
                    'user_id' => ['UINT', 0],
                    'points_total' => ['UINT', 0],
                    'points_today' => ['UINT', 0],
                    'last_activity' => ['TIMESTAMP', 0],
                ],
                'PRIMARY_KEY' => 'user_id',
            ],
            $this->table_prefix . 'cash_logs' => [
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
            $this->table_prefix . 'cash_settings' => [
                'COLUMNS' => [
                    'setting_name' => ['VCHAR:255', ''],
                    'setting_value' => ['VCHAR:255', ''],
                ],
                'PRIMARY_KEY' => 'setting_name',
            ],
        ];

        foreach ($tables as $table_name => $table_data)
        {
            $this->db_tools->sql_create_table($table_name, $table_data);
        }

        // Insert default settings
        $default_settings = [
            'points_per_post' => 10,
            'points_per_topic' => 15,
            'points_per_poll_vote' => 5,
            'points_per_page_view' => 1,
            'max_daily_points' => 100,
        ];

        foreach ($default_settings as $name => $value)
        {
            $sql = 'INSERT INTO ' . $this->table_prefix . 'cash_settings' . ' ' . $this->db->sql_build_array('INSERT', [
                'setting_name' => $name,
                'setting_value' => $value,
            ]);
            $this->db->sql_query($sql);
        }
    }

    /**
     * Uninstall the extension
     *
     * @return void
     */
    public function uninstall()
    {
        $this->drop_tables();
    }

    /**
     * Drop the extension's tables
     *
     * @return void
     */
    protected function drop_tables()
    {
        $tables = [
            $this->table_prefix . 'cash_points',
            $this->table_prefix . 'cash_logs',
            $this->table_prefix . 'cash_settings',
        ];

        foreach ($tables as $table)
        {
            $this->db_tools->sql_table_drop($table);
        }
    }
}
