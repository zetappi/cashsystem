<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class main_listener implements EventSubscriberInterface
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

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var string */
    protected $php_ext;

    /** @var string */
    protected $root_path;

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
     * @param \phpbb\request\request              $request        Request object
     * @param \phpbb\auth\auth                    $auth           Auth object
     * @param string                              $php_ext        PHP extension
     * @param string                              $root_path      phpBB root path
     * @param string                              $table_prefix   Table prefix
     * @param \marcozp\cash\service\points_manager $points_manager Points manager service
     */
    public function __construct(\phpbb\config\config $config, \phpbb\controller\helper $helper, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, \phpbb\request\request $request, \phpbb\auth\auth $auth, $php_ext, $root_path, $table_prefix, \marcozp\cash\service\points_manager $points_manager)
    {
        $this->config = $config;
        $this->helper = $helper;
        $this->template = $template;
        $this->user = $user;
        $this->db = $db;
        $this->request = $request;
        $this->auth = $auth;
        $this->php_ext = $php_ext;
        $this->root_path = $root_path;
        $this->table_prefix = $table_prefix;
        $this->points_manager = $points_manager;
    }

    /**
     * Assign functions defined in this class to event listeners in the core
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'core.user_setup'                    => 'load_language_on_setup',
            'core.page_header'                   => 'add_page_header_link',
            'core.page_footer'                   => 'award_page_view',
            'core.submit_post_end'               => 'award_post_points',
            'core.viewtopic_modify_post_row'     => 'modify_post_row',
            'core.memberlist_view_profile'       => 'add_points_to_profile',
            'core.viewtopic_modify_poll_data'    => 'award_poll_vote',
        ];
    }

    /**
     * Load language files during user setup
     *
     * @param \phpbb\event\data $event Event object
     */
    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = [
            'ext_name' => 'marcozp/cash',
            'lang_set' => 'common',
        ];
        $event['lang_set_ext'] = $lang_set_ext;
    }

    /**
     * Add a link to the billboard page in the header
     */
    public function add_page_header_link()
    {
        // Ottieni le impostazioni
        $settings = $this->points_manager->get_settings();
        
        // Controlla se il link deve essere mostrato
        if (isset($settings['show_billboard_link']) && (int) $settings['show_billboard_link'] === 1)
        {
            $this->template->assign_vars([
                'U_CASH_BILLBOARD' => $this->helper->route('marcozp_cash_billboard'),
                'S_SHOW_CASH_BILLBOARD' => true,
            ]);
        }
        else
        {
            // Assegna una variabile per nascondere il link
            $this->template->assign_vars([
                'S_SHOW_CASH_BILLBOARD' => false,
            ]);
        }
    }

    /**
     * Award points for page view
     */
    public function award_page_view()
    {
        if ($this->user->data['user_id'] != ANONYMOUS && !$this->user->data['is_bot'])
        {
            // Debug
            error_log('DEBUG: award_page_view chiamato per user_id: ' . $this->user->data['user_id']);
            $result = $this->points_manager->award_page_view($this->user->data['user_id']);
            error_log('DEBUG: award_page_view risultato: ' . ($result ? 'true' : 'false'));
        }
    }

    /**
     * Award points for posting
     *
     * @param \phpbb\event\data $event Event object
     */
    public function award_post_points($event)
    {
        // Debug
        error_log('DEBUG: award_post_points chiamato');
        
        $mode = $event['mode'];
        $data = $event['data'];
        $post_id = $event['post_id'];
        $topic_id = $event['topic_id'];
        
        // Debug
        error_log('DEBUG: mode=' . $mode . ', post_id=' . $post_id . ', topic_id=' . $topic_id . ', user_id=' . $this->user->data['user_id']);
        
        // Skip if user is anonymous or a bot
        if ($this->user->data['user_id'] == ANONYMOUS || $this->user->data['is_bot'])
        {
            error_log('DEBUG: Utente anonimo o bot, punti non assegnati');
            return;
        }
        
        // Award points based on post type
        if ($mode == 'post' || ($mode == 'reply' && !$data['topic_first_post_id']))
        {
            // New topic
            error_log('DEBUG: Assegnazione punti per nuovo topic');
            $result = $this->points_manager->award_topic($this->user->data['user_id'], $topic_id);
            error_log('DEBUG: award_topic risultato: ' . ($result ? 'true' : 'false'));
        }
        else
        {
            // Reply to topic
            error_log('DEBUG: Assegnazione punti per risposta');
            $result = $this->points_manager->award_post($this->user->data['user_id'], $post_id);
            error_log('DEBUG: award_post risultato: ' . ($result ? 'true' : 'false'));
        }
    }

    /**
     * Modify post row to display user points
     *
     * @param \phpbb\event\data $event Event object
     */
    public function modify_post_row($event)
    {
        $post_row = $event['post_row'];
        $poster_id = $event['row']['user_id'];
        
        if ($poster_id != ANONYMOUS)
        {
            $user_points = $this->points_manager->get_user_points($poster_id);
            $post_row['CASH_POINTS'] = $user_points['points_total'];
            $event['post_row'] = $post_row;
        }
    }

    /**
     * Add points to user profile
     *
     * @param \phpbb\event\data $event Event object
     */
    public function add_points_to_profile($event)
    {
        $member = $event['member'];
        $user_id = $member['user_id'];
        
        if ($user_id != ANONYMOUS)
        {
            $user_points = $this->points_manager->get_user_points($user_id);
            
            $this->template->assign_vars([
                'CASH_POINTS' => $user_points['points_total'],
            ]);
        }
    }

    /**
     * Award points for poll vote
     *
     * @param \phpbb\event\data $event Event object
     */
    public function award_poll_vote($event)
    {
        // Check if this is a vote action
        if ($this->request->is_set_post('update'))
        {
            $topic_id = $event['topic_id'];
            
            // Skip if user is anonymous or a bot
            if ($this->user->data['user_id'] == ANONYMOUS || $this->user->data['is_bot'])
            {
                return;
            }
            
            $this->points_manager->award_poll_vote($this->user->data['user_id'], $topic_id);
        }
    }
}
