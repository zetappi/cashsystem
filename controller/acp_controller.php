<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash\controller;

/**
 * ACP controller
 */
class acp_controller
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\language\language */
    protected $language;

    /** @var string */
    protected $root_path;

    /** @var string */
    protected $php_ext;

    /** @var string */
    protected $table_prefix;

    /** @var \marcozp\cash\service\points_manager */
    protected $points_manager;

    /**
     * Constructor
     *
     * @param \phpbb\config\config                $config         Config object
     * @param \phpbb\template\template            $template       Template object
     * @param \phpbb\user                         $user           User object
     * @param \phpbb\request\request              $request        Request object
     * @param \phpbb\db\driver\driver_interface   $db            Database object
     * @param \phpbb\language\language            $language       Language object
     * @param string                              $root_path      phpBB root path
     * @param string                              $php_ext        PHP extension
     * @param string                              $table_prefix   Table prefix
     * @param \marcozp\cash\service\points_manager $points_manager Points manager service
     */
    public function __construct(\phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\request\request $request, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, $root_path, $php_ext, $table_prefix, \marcozp\cash\service\points_manager $points_manager)
    {
        $this->config = $config;
        $this->template = $template;
        $this->user = $user;
        $this->request = $request;
        $this->db = $db;
        $this->language = $language;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
        $this->points_manager = $points_manager;
    }

    /**
     * Display the settings page
     *
     * @return void
     */
    public function display_settings()
    {
        $this->language->add_lang('common', 'marcozp/cash');
        
        // Set form key - Spostato all'inizio della funzione
        add_form_key('marcozp_cash_settings');
        
        // Get current settings
        $settings = $this->points_manager->get_settings();
        
        // Process form submission
        if ($this->request->is_set_post('submit'))
        {
            if (!check_form_key('marcozp_cash_settings'))
            {
                trigger_error('FORM_INVALID');
            }
            
            $new_settings = [
                'points_per_post'      => $this->request->variable('points_per_post', 0),
                'points_per_topic'     => $this->request->variable('points_per_topic', 0),
                'points_per_poll_vote' => $this->request->variable('points_per_poll_vote', 0),
                'points_per_page_view' => $this->request->variable('points_per_page_view', 0),
                'max_daily_points'     => $this->request->variable('max_daily_points', 0),
                'show_billboard_link'  => $this->request->variable('show_billboard_link', 1),
            ];
            
            // Update settings
            $this->points_manager->update_settings($new_settings);
            
            // Success message
            trigger_error($this->language->lang('ACP_CASH_SETTINGS_UPDATED') . adm_back_link($this->u_action));
        }
        
        // Reset all points
        if ($this->request->is_set_post('reset_points'))
        {
            // Quando si usa confirm_box, non verificare il form key qui
            // perché confirm_box gestisce internamente il proprio form key
            if (confirm_box(true))
            {
                $this->points_manager->reset_all_points();
                trigger_error($this->language->lang('ACP_CASH_POINTS_RESET') . adm_back_link($this->u_action));
            }
            else
            {
                // Verifica il form key solo prima di mostrare la conferma
                if (!check_form_key('marcozp_cash_settings'))
                {
                    trigger_error('FORM_INVALID' . adm_back_link($this->u_action));
                }
                
                confirm_box(false, $this->language->lang('ACP_CASH_CONFIRM_RESET'), build_hidden_fields([
                    'reset_points' => true,
                ]));
            }
        }
        
        // Assign template variables
        $this->template->assign_vars([
            'POINTS_PER_POST'       => $settings['points_per_post'],
            'POINTS_PER_TOPIC'      => $settings['points_per_topic'],
            'POINTS_PER_POLL_VOTE'  => $settings['points_per_poll_vote'],
            'POINTS_PER_PAGE_VIEW'  => $settings['points_per_page_view'],
            'MAX_DAILY_POINTS'      => $settings['max_daily_points'],
            'SHOW_BILLBOARD_LINK'   => (bool) $settings['show_billboard_link'],
            'U_ACTION'              => $this->u_action,
        ]);
    }

    /**
     * Set action URL for pagination
     *
     * @param string $u_action
     */
    public function set_page_url($u_action)
    {
        $this->u_action = $u_action;
    }
    
    /**
     * Manage user points
     *
     * @return void
     */
    public function manage_users()
    {
        $this->language->add_lang('common', 'marcozp/cash');
        
        // Process form submission
        if ($this->request->is_set_post('submit'))
        {
            if (!check_form_key('marcozp_cash_manage_users'))
            {
                trigger_error('FORM_INVALID');
            }
            
            $username = $this->request->variable('username', '', true);
            $points = $this->request->variable('points', 0);
            $action = $this->request->variable('action', '');
            $reason = $this->request->variable('reason', '', true);
            
            if (empty($username))
            {
                trigger_error($this->language->lang('ACP_CASH_NO_USER') . adm_back_link($this->u_action));
            }
            
            if ($points <= 0)
            {
                trigger_error($this->language->lang('ACP_CASH_INVALID_POINTS') . adm_back_link($this->u_action));
            }
            
            // Get user ID from username
            $sql = 'SELECT user_id
                    FROM ' . USERS_TABLE . "
                    WHERE username_clean = '" . $this->db->sql_escape(utf8_clean_string($username)) . "'";
            $result = $this->db->sql_query($sql);
            $user_id = (int) $this->db->sql_fetchfield('user_id');
            $this->db->sql_freeresult($result);
            
            if (!$user_id)
            {
                // Aggiunto E_USER_WARNING per rendere lo sfondo del messaggio rosso
                trigger_error($this->language->lang('NO_USER') . adm_back_link($this->u_action), E_USER_WARNING);
            }
            
            // Add or subtract points
            if ($action === 'add')
            {
                $success = $this->points_manager->add_points($user_id, $points, 'admin_add', $reason);
                $message = $this->language->lang('ACP_CASH_POINTS_ADDED', $points, $username);
            }
            else
            {
                // For subtraction, we need to get the current points first
                $user_points = $this->points_manager->get_user_points($user_id);
                
                if ($user_points['points_total'] < $points)
                {
                    trigger_error($this->language->lang('ACP_CASH_NOT_ENOUGH_POINTS', $username) . adm_back_link($this->u_action));
                }
                
                // Subtract points by adding a negative value
                $success = $this->points_manager->add_points($user_id, -$points, 'admin_subtract', $reason);
                $message = $this->language->lang('ACP_CASH_POINTS_SUBTRACTED', $points, $username);
            }
            
            if ($success)
            {
                trigger_error($message . adm_back_link($this->u_action));
            }
            else
            {
                trigger_error($this->language->lang('ACP_CASH_POINTS_ERROR') . adm_back_link($this->u_action));
            }
        }
        
        // Set form key
        add_form_key('marcozp_cash_manage_users');
        
        // Configurazione paginazione
        $users_per_page = 10; // Numero di utenti per pagina
        $start = $this->request->variable('start', 0);
        
        // Conta il numero totale di utenti con punti
        $sql = 'SELECT COUNT(user_id) as total FROM ' . $this->table_prefix . 'cash_points';
        $result = $this->db->sql_query($sql);
        $total_users = (int) $this->db->sql_fetchfield('total');
        $this->db->sql_freeresult($result);
        
        // Recupera gli utenti con paginazione
        $sql = 'SELECT u.user_id, u.username, u.user_colour, c.points_total 
                FROM ' . USERS_TABLE . ' u, ' . $this->table_prefix . 'cash_points c
                WHERE u.user_id = c.user_id
                ORDER BY c.points_total DESC';
        $result = $this->db->sql_query_limit($sql, $users_per_page, $start);
        
        $top_users = [];
        while ($row = $this->db->sql_fetchrow($result))
        {
            $top_users[] = [
                'USERNAME_FULL' => get_username_string('full', $row['user_id'], $row['username'], $row['user_colour']),
                'USERNAME'      => $row['username'], // Aggiunto per il link modifica
                'POINTS'        => (int) $row['points_total']
            ];
        }
        $this->db->sql_freeresult($result);
        
        // Calcola i dati per la paginazione
        $next_start = $start + $users_per_page;
        $prev_start = max(0, $start - $users_per_page);
        
        // Passa i dati al template
        $this->template->assign_vars([
            'TOP_USERS' => $top_users,
            'U_ACTION'  => $this->u_action,
            'U_FIND_USERNAME' => append_sid("{$this->root_path}memberlist.{$this->php_ext}", 'mode=searchuser&amp;form=acp_cash_manage_users&amp;field=username'),
            'PAGINATION' => true,
            'TOTAL_USERS' => $total_users,
            'CURRENT_PAGE' => floor($start / $users_per_page) + 1,
            'TOTAL_PAGES' => ceil($total_users / $users_per_page),
            'U_NEXT_PAGE' => $next_start < $total_users ? append_sid($this->u_action . '&amp;start=' . $next_start) : false,
            'U_PREV_PAGE' => $start > 0 ? append_sid($this->u_action . '&amp;start=' . $prev_start) : false,
        ]);
    }
}
