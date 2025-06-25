<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash\service;

class points_manager
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var string */
    protected $table_prefix;

    /** @var array */
    protected $settings;

    /**
     * Constructor
     *
     * @param \phpbb\config\config             $config       Config object
     * @param \phpbb\db\driver\driver_interface $db          Database object
     * @param \phpbb\user                       $user        User object
     * @param \phpbb\auth\auth                  $auth        Auth object
     * @param string                           $table_prefix Table prefix
     */
    public function __construct(\phpbb\config\config $config, \phpbb\db\driver\driver_interface $db, \phpbb\user $user, \phpbb\auth\auth $auth, $table_prefix)
    {
        $this->config = $config;
        $this->db = $db;
        $this->user = $user;
        $this->auth = $auth;
        $this->table_prefix = $table_prefix;
        $this->settings = $this->get_settings();
    }

    /**
     * Get all settings from database
     *
     * @return array
     */
    public function get_settings()
    {
        $sql = 'SELECT setting_name, setting_value
                FROM ' . $this->table_prefix . 'cash_settings';
        $result = $this->db->sql_query($sql);
        
        $settings = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
        $this->db->sql_freeresult($result);
        
        return $settings;
    }

    /**
     * Update settings
     *
     * @param array $settings
     * @return void
     */
    public function update_settings($settings)
    {
        foreach ($settings as $name => $value)
        {
            $sql = 'UPDATE ' . $this->table_prefix . 'cash_settings
                    SET setting_value = ' . (int) $value . '
                    WHERE setting_name = \'' . $this->db->sql_escape($name) . '\'';
            $this->db->sql_query($sql);
        }
        
        // Refresh settings
        $this->settings = $this->get_settings();
    }

    /**
     * Add points to a user
     *
     * @param int    $user_id
     * @param int    $points
     * @param string $type
     * @param string $data
     * @return bool
     */
    public function add_points($user_id, $points, $type, $data = '')
    {
        // Se il tipo è admin_subtract, permettiamo valori negativi
        if ($type === 'admin_subtract')
        {
            return $this->subtract_points($user_id, abs($points), $type, $data);
        }
        
        if ($user_id == ANONYMOUS || $points <= 0)
        {
            return false;
        }

        // Check if user has reached daily maximum
        $user_points = $this->get_user_points($user_id);
        
        // Reset daily points if last activity was not today
        if (date('Y-m-d', $user_points['last_activity']) != date('Y-m-d'))
        {
            $user_points['points_today'] = 0;
        }
        
        // Check if adding these points would exceed daily maximum
        $max_daily = (int) $this->settings['max_daily_points'];
        if ($user_points['points_today'] >= $max_daily)
        {
            return false;
        }
        
        // Adjust points if it would exceed daily maximum
        if (($user_points['points_today'] + $points) > $max_daily)
        {
            $points = $max_daily - $user_points['points_today'];
            
            if ($points <= 0)
            {
                return false;
            }
        }
        
        // Update user points
        $sql_data = [
            'points_total'   => $user_points['points_total'] + $points,
            'points_today'   => $user_points['points_today'] + $points,
            'last_activity'  => time(),
        ];
        
        $sql = 'UPDATE ' . $this->table_prefix . 'cash_points
                SET ' . $this->db->sql_build_array('UPDATE', $sql_data) . '
                WHERE user_id = ' . (int) $user_id;
        $this->db->sql_query($sql);
        
        if (!$this->db->sql_affectedrows())
        {
            $sql_data['user_id'] = (int) $user_id;
            $sql = 'INSERT INTO ' . $this->table_prefix . 'cash_points ' . $this->db->sql_build_array('INSERT', $sql_data);
            $this->db->sql_query($sql);
        }
        
        // Log the points transaction
        $log_data = [
            'user_id'    => (int) $user_id,
            'log_time'   => time(),
            'log_type'   => $type,
            'log_points' => (int) $points,
            'log_data'   => ($data !== null && $data !== '') ? (string) $data : '',
        ];
        
        $sql = 'INSERT INTO ' . $this->table_prefix . 'cash_logs ' . $this->db->sql_build_array('INSERT', $log_data);
        $this->db->sql_query($sql);
        
        return true;
    }

    /**
     * Subtract points from a user
     *
     * @param int    $user_id
     * @param int    $points
     * @param string $type
     * @param string $data
     * @return bool
     */
    public function subtract_points($user_id, $points, $type, $data = '')
    {
        if ($user_id == ANONYMOUS || $points <= 0)
        {
            return false;
        }

        // Get user points
        $user_points = $this->get_user_points($user_id);
        
        // Check if user has enough points
        if ($user_points['points_total'] < $points)
        {
            return false;
        }
        
        // Update user points
        $sql_data = [
            'points_total'   => $user_points['points_total'] - $points,
            'last_activity'  => time(),
        ];
        
        $sql = 'UPDATE ' . $this->table_prefix . 'cash_points
                SET ' . $this->db->sql_build_array('UPDATE', $sql_data) . '
                WHERE user_id = ' . (int) $user_id;
        $this->db->sql_query($sql);
        
        if (!$this->db->sql_affectedrows())
        {
            return false;
        }
        
        // Log the points transaction
        $log_data = [
            'user_id'    => (int) $user_id,
            'log_time'   => time(),
            'log_type'   => $type,
            'log_points' => (int) -$points, // Negative value to indicate subtraction
            'log_data'   => ($data !== null && $data !== '') ? (string) $data : '',
        ];
        
        $sql = 'INSERT INTO ' . $this->table_prefix . 'cash_logs ' . $this->db->sql_build_array('INSERT', $log_data);
        $this->db->sql_query($sql);
        
        return true;
    }

    /**
     * Get user points
     *
     * @param int $user_id
     * @return array
     */
    public function get_user_points($user_id)
    {
        $sql = 'SELECT *
                FROM ' . $this->table_prefix . 'cash_points
                WHERE user_id = ' . (int) $user_id;
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);
        
        if (!$row)
        {
            return [
                'points_total'  => 0,
                'points_today'  => 0,
                'last_activity' => 0,
            ];
        }
        
        return $row;
    }

    /**
     * Get top users by points
     *
     * @param int $limit
     * @param int $start
     * @return array
     */
    public function get_top_users($limit = 50, $start = 0)
    {
        $sql = 'SELECT u.user_id, u.username, u.user_colour, p.points_total
                FROM ' . $this->table_prefix . 'cash_points p
                LEFT JOIN ' . USERS_TABLE . ' u ON (p.user_id = u.user_id)
                WHERE u.user_id <> ' . ANONYMOUS . '
                ORDER BY p.points_total DESC';
        $result = $this->db->sql_query_limit($sql, $limit, $start);
        
        $users = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            $users[] = $row;
        }
        $this->db->sql_freeresult($result);
        
        return $users;
    }

    /**
     * Get total number of users with points
     *
     * @return int
     */
    public function get_total_users()
    {
        $sql = 'SELECT COUNT(p.user_id) as total_users
                FROM ' . $this->table_prefix . 'cash_points p
                LEFT JOIN ' . USERS_TABLE . ' u ON (p.user_id = u.user_id)
                WHERE u.user_id <> ' . ANONYMOUS;
        $result = $this->db->sql_query($sql);
        $total_users = (int) $this->db->sql_fetchfield('total_users');
        $this->db->sql_freeresult($result);
        
        return $total_users;
    }

    /**
     * Reset all points
     *
     * @return void
     */
    public function reset_all_points()
    {
        // Truncate points table
        $sql = 'TRUNCATE TABLE ' . $this->table_prefix . 'cash_points';
        $this->db->sql_query($sql);
        
        // Truncate logs table
        $sql = 'TRUNCATE TABLE ' . $this->table_prefix . 'cash_logs';
        $this->db->sql_query($sql);
    }

    /**
     * Award points for page view
     *
     * @param int $user_id
     * @return bool
     */
    public function award_page_view($user_id)
    {
        if ($user_id == ANONYMOUS)
        {
            return false;
        }
        
        $points = (int) $this->settings['points_per_page_view'];
        if ($points <= 0)
        {
            return false;
        }
        
        return $this->add_points($user_id, $points, 'page_view');
    }

    /**
     * Award points for new post
     *
     * @param int $user_id
     * @param int $post_id
     * @return bool
     */
    public function award_post($user_id, $post_id)
    {
        if ($user_id == ANONYMOUS)
        {
            return false;
        }
        
        $points = (int) $this->settings['points_per_post'];
        if ($points <= 0)
        {
            return false;
        }
        
        return $this->add_points($user_id, $points, 'post', $post_id);
    }

    /**
     * Award points for new topic
     *
     * @param int $user_id
     * @param int $topic_id
     * @return bool
     */
    public function award_topic($user_id, $topic_id)
    {
        if ($user_id == ANONYMOUS)
        {
            return false;
        }
        
        $points = (int) $this->settings['points_per_topic'];
        if ($points <= 0)
        {
            return false;
        }
        
        return $this->add_points($user_id, $points, 'topic', $topic_id);
    }

    /**
     * Award points for poll vote
     *
     * @param int $user_id
     * @param int $topic_id
     * @return bool
     */
    public function award_poll_vote($user_id, $topic_id)
    {
        if ($user_id == ANONYMOUS)
        {
            return false;
        }
        
        $points = (int) $this->settings['points_per_poll_vote'];
        if ($points <= 0)
        {
            return false;
        }
        
        return $this->add_points($user_id, $points, 'poll_vote', $topic_id);
    }
}
