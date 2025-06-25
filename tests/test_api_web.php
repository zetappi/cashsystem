<?php
/**
 * Controller per testare l'API dei punti via web
 * Accessibile solo agli amministratori
 * 
 * URL: /app.php/cash/testapi
 */

namespace marcozp\cash\controller;

class test_controller
{
    /* @var \phpbb\auth\auth */
    protected $auth;
    
    /* @var \phpbb\request\request */
    protected $request;
    
    /* @var \phpbb\template\template */
    protected $template;
    
    /* @var \phpbb\user */
    protected $user;
    
    /* @var \phpbb\controller\helper */
    protected $helper;
    
    /* @var string */
    protected $root_path;
    
    /* @var string */
    protected $php_ext;
    
    /**
     * Constructor
     */
    public function __construct(
        \phpbb\auth\auth $auth,
        \phpbb\request\request $request,
        \phpbb\template\template $template,
        \phpbb\user $user,
        \phpbb\controller\helper $helper,
        $root_path,
        $php_ext
    ) {
        $this->auth = $auth;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->helper = $helper;
        $this->root_path = $root_path;
        $this->php_ext = $php_ext;
    }
    
    /**
     * Pagina di test dell'API
     */
    public function handle()
    {
        // Verifica che l'utente sia loggato e sia un amministratore
        if (!$this->user->data['is_registered'] || !$this->auth->acl_get('a_')) {
            login_box('', $this->user->lang['NOT_AUTHORISED']);
        }
        
        $output = '';
        
        try {
            // Ottieni l'istanza del container
            global $phpbb_container;
            
            if (!$phpbb_container->has('marcozp.cash.api.points')) {
                throw new \RuntimeException("Il servizio API non è disponibile. Assicurati che l'estensione sia abilitata correttamente.");
            }
            
            $pointsApi = $phpbb_container->get('marcozp.cash.api.points');
            $testUserId = $this->user->data['user_id'];
            
            // Funzione di utilità per formattare l'output
            $print_header = function($title) use (&$output) {
                $output .= "<h3>" . htmlspecialchars($title) . "</h3>\n<pre>";
            };
            
            $print_footer = function() use (&$output) {
                $output .= "</pre><hr>";
            };
            
            // Test 1: Verifica permessi
            $print_header("Test permessi utente");
            $canModify = $pointsApi->canUserModifyPoints($testUserId, $testUserId);
            $output .= "L'utente PUO' " . ($canModify ? "" : "NON ") . "modificare i propri punti\n";
            $print_footer();
            
            // Test 2: Ottieni punti attuali
            $print_header("Lettura punti utente");
            $currentPoints = $pointsApi->getUserPoints($testUserId);
            $output .= "Punti attuali: {$currentPoints}\n";
            $print_footer();
            
            // Test 3: Aggiungi punti (solo se richiesto esplicitamente)
            if ($this->request->is_set_post('add_points')) {
                $print_header("Aggiunta punti");
                $pointsToAdd = 10;
                $output .= "Aggiungo {$pointsToAdd} punti...\n";
                $pointsApi->addPoints($testUserId, $pointsToAdd, 'test.add_points', 'Test aggiunta punti');
                $newPoints = $pointsApi->getUserPoints($testUserId);
                $output .= "Nuovo totale: {$newPoints}\n";
                $print_footer();
            }
            
            // Test 4: Gestione errori
            $print_header("Test gestione errori");
            try {
                $pointsApi->getUserPoints(999999);
                $output .= "ERRORE: L'eccezione UserNotFoundException non è stata lanciata\n";
            } catch (\marcozp\cash\service\api\exception\UserNotFoundException $e) {
                $output .= "SUCCESSO: Eccezione UserNotFoundException catturata correttamente\n";
                $output .= "Messaggio: " . htmlspecialchars($e->getMessage()) . "\n";
            }
            $print_footer();
            
        } catch (\Exception $e) {
            $output .= "<div class='error'>";
            $output .= "<strong>ERRORE:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
            $output .= "<small>" . htmlspecialchars($e->getFile()) . " (" . $e->getLine() . ")</small>";
            $output .= "</div>";
        }
        
        // Output della pagina
        page_header('Test API Punti');
        
        $this->template->set_filenames(array(
            'body' => 'test_api_body.html',
        ));
        
        $this->template->assign_vars(array(
            'OUTPUT'    => $output,
            'U_ACTION'  => $this->helper->route('marcozp_cash_testapi'),
        ));
        
        page_footer();
    }
}
