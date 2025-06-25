<?php
/**
 *
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace marcozp\cash\service\api;

use marcozp\cash\service\api\exception\NotEnoughPointsException;
use marcozp\cash\service\api\exception\UserNotFoundException;

/**
 * Gestisce le operazioni API per i punti utente
 */
class ApiManager implements PointsApiInterface
{
    /** @var \marcozp\cash\service\points_manager */
    protected $pointsManager;
    
    /** @var \phpbb\auth\auth */
    protected $auth;
    
    /** @var \phpbb\user */
    protected $user;
    
    /** @var \phpbb\db\driver\driver_interface */
    protected $db;
    
    /** @var string Tabella degli utenti */
    protected $usersTable;
    
    /**
     * Costruttore
     *
     * @param \marcozp\cash\service\points_manager $pointsManager
     * @param \phpbb\auth\auth $auth
     * @param \phpbb\user $user
     * @param \phpbb\db\driver\driver_interface $db
     * @param string $usersTable Nome della tabella degli utenti
     */
    public function __construct(
        \marcozp\cash\service\points_manager $pointsManager,
        \phpbb\auth\auth $auth,
        \phpbb\user $user,
        \phpbb\db\driver\driver_interface $db,
        $usersTable
    ) {
        $this->pointsManager = $pointsManager;
        $this->auth = $auth;
        $this->user = $user;
        $this->db = $db;
        $this->usersTable = $usersTable;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUserPoints(int $userId): int
    {
        $this->validateUserExists($userId);
        $data = $this->pointsManager->get_user_points($userId);
        return (int)($data['points_total'] ?? 0);
    }
    
    /**
     * {@inheritdoc}
     */
    public function addPoints(int $userId, int $points, string $action, string $data = ''): bool
    {
        $this->validateUserExists($userId);
        
        if ($points === 0) {
            return true; // Nessuna operazione necessaria
        }
        
        if ($points > 0) {
            $this->pointsManager->add_points($userId, $points, $action, $data);
        } else {
            // Verifica che ci siano abbastanza punti prima di sottrarre
            $currentPoints = $this->getUserPoints($userId);
            if ($currentPoints < abs($points)) {
                throw new NotEnoughPointsException();
            }
            $this->pointsManager->subtract_points($userId, abs($points), $action, $data);
        }
        
        return true;
    }
    
    /**
     * {@inheritdoc}
     */
    public function canUserModifyPoints(int $modifierId, int $targetUserId): bool
    {
        // Gli amministratori possono modificare i punti di chiunque
        if ($this->auth->acl_gets('a_', 'm_')) {
            return true;
        }
        
        // Gli utenti possono modificare solo i propri punti
        return $modifierId === $targetUserId;
    }
    
    /**
     * Verifica che un utente esista
     * 
     * @param int $userId ID dell'utente da verificare
     * @throws UserNotFoundException Se l'utente non esiste
     */
    protected function validateUserExists(int $userId): void
    {
        if ($userId <= 1) { // 1 = ANONYMOUS
            throw new UserNotFoundException();
        }
        
        $sql = 'SELECT user_id FROM ' . $this->usersTable . ' 
                WHERE user_id = ' . (int)$userId . ' 
                AND user_type <> ' . USER_IGNORE;
        
        $result = $this->db->sql_query_limit($sql, 1);
        $userExists = (bool)$this->db->sql_fetchfield('user_id');
        $this->db->sql_freeresult($result);
        
        if (!$userExists) {
            throw new UserNotFoundException();
        }
    }
}
