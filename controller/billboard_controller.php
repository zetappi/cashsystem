<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash\controller;

/**
 * Billboard controller
 */
class billboard_controller
{
    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\language\language */
    protected $language;

    /** @var \phpbb\auth\auth */
    protected $auth;
    
    /** @var \phpbb\request\request */
    protected $request;

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
     * @param \phpbb\controller\helper            $helper         Controller helper object
     * @param \phpbb\template\template            $template       Template object
     * @param \phpbb\user                         $user           User object
     * @param \phpbb\db\driver\driver_interface   $db            Database object
     * @param \phpbb\language\language            $language       Language object
     * @param \phpbb\auth\auth                    $auth           Auth object
     * @param \phpbb\request\request              $request        Request object
     * @param string                              $root_path      phpBB root path
     * @param string                              $php_ext        PHP extension
     * @param string                              $table_prefix   Table prefix
     * @param \marcozp\cash\service\points_manager $points_manager Points manager service
     */
    public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, \phpbb\auth\auth $auth, \phpbb\request\request $request, $root_path, $php_ext, $table_prefix, \marcozp\cash\service\points_manager $points_manager)
    {
        $this->config = $config;
        $this->helper = $helper;
        $this->template = $template;
        $this->user = $user;
        $this->db = $db;
        $this->language = $language;
        $this->auth = $auth;
        $this->request = $request;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
        $this->table_prefix = $table_prefix;
        $this->points_manager = $points_manager;
    }

    /**
     * Display the billboard page
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function display()
    {
        // Add language file
        $this->language->add_lang('common', 'marcozp/cash');
        
        // Set page title and template
        $this->template->assign_var('PAGE_TITLE', $this->language->lang('CASH_BILLBOARD'));
        
        // Get total users with points
        $total_users = $this->points_manager->get_total_users();
        
        // Get top users
        $top_users = $this->points_manager->get_top_users(20);
        
        // Get current user's points
        $user_points = 0;
        if ($this->user->data['user_id'] != ANONYMOUS)
        {
            $user_data = $this->points_manager->get_user_points($this->user->data['user_id']);
            $user_points = $user_data['points_total'];
        }
        
        // Assign template variables
        $this->template->assign_vars([
            'CASH_USER_POINTS' => $user_points,
            'TOTAL_USERS' => $total_users,
        ]);
        
        // Assign users to template
        $rank = 1;
        foreach ($top_users as $user)
        {
            $this->template->assign_block_vars('users', [
                'RANK' => $rank,
                'USERNAME' => get_username_string('full', $user['user_id'], $user['username'], $user['user_colour']),
                'POINTS' => $user['points_total'],
            ]);
            $rank++;
        }
        
        // Return the template
        return $this->helper->render('@marcozp_cash/billboard.html', $this->language->lang('CASH_BILLBOARD'));
    }
}
