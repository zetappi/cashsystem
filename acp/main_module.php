<?php
/**
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 */

namespace marcozp\cash\acp;

class main_module
{
    /** @var string */
    public $u_action;

    /** @var string */
    public $tpl_name;

    /** @var string */
    public $page_title;

    /**
     * Main ACP module
     *
     * @param int    $id   The module ID
     * @param string $mode The module mode
     */
    public function main($id, $mode)
    {
        global $phpbb_container;

        // Get an instance of the admin controller
        $admin_controller = $phpbb_container->get('marcozp.cash.controller.acp');

        // Make the $u_action url available in the admin controller
        $admin_controller->set_page_url($this->u_action);

        // Load the display handle in the admin controller
        switch ($mode)
        {
            case 'settings':
                // Set the page title for our ACP page
                $this->page_title = 'ACP_CASH_SETTINGS';
                $this->tpl_name = 'acp_cash_settings';
                $admin_controller->display_settings();
            break;
            
            case 'manage_users':
                // Set the page title for our ACP page
                $this->page_title = 'ACP_CASH_MANAGE_USERS';
                $this->tpl_name = 'acp_cash_manage_users';
                $admin_controller->manage_users();
            break;
        }
    }
}
