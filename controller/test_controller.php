<?php
namespace marcozp\cash\controller;

class test_controller
{
    protected $auth;
    protected $request;
    protected $template;
    protected $user;
    protected $helper;
    protected $root_path;
    protected $php_ext;
    
    public function __construct(\phpbb\auth\auth $auth, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, \phpbb\controller\helper $helper, $root_path, $php_ext)
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->helper = $helper;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
    }
    
    public function handle()
    {
        if (!$this->user->data['is_registered'] || !$this->auth->acl_get('a_')) {
            login_box('', $this->user->lang['NOT_AUTHORISED']);
        }
        
        $output = '';
        
        try {
            global $phpbb_container;
            
            if (!$phpbb_container->has('marcozp.cash.api.points')) {
                throw new \RuntimeException("Servizio API non disponibile");
            }
            
            $pointsApi = $phpbb_container->get('marcozp.cash.api.points');
            $testUserId = $this->user->data['user_id'];
            
            $print = function($title, $content) use (&$output) {
                $output .= "<h3>$title</h3><pre>$content</pre><hr>";
            };
            
            // Test permessi
            $canModify = $pointsApi->canUserModifyPoints($testUserId, $testUserId);
            $print("Permessi utente", "Può modificare i punti: " . ($canModify ? "SÌ" : "NO"));
            
            // Test lettura punti
            $points = $pointsApi->getUserPoints($testUserId);
            $print("Punti attuali", $points);
            
            // Test aggiunta punti
            if ($this->request->is_set_post('add_points')) {
                $pointsApi->addPoints($testUserId, 10, 'test.add_points', 'Test');
                $print("Aggiunti punti", "Nuovo totale: " . $pointsApi->getUserPoints($testUserId));
            }
            
        } catch (\Exception $e) {
            $output = "<div class='error'>ERRORE: " . htmlspecialchars($e->getMessage()) . "</div>" . $output;
        }
        
        $this->template->assign_vars([
            'OUTPUT' => $output,
            'U_ACTION' => $this->helper->route('marcozp_cash_testapi')
        ]);
        
        page_header('Test API Punti');
        $this->template->set_filenames(['body' => 'test_api_body.html']);
        page_footer();
    }
}
