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

interface PointsApiInterface
{
    /**
     * Restituisce il punteggio totale di un utente
     * 
     * @param int $userId ID dell'utente
     * @return int Punteggio totale
     * @throws UserNotFoundException Se l'utente non esiste
     */
    public function getUserPoints(int $userId): int;
    
    /**
     * Aggiunge o sottrae punti a un utente
     * 
     * @param int $userId ID dell'utente
     * @param int $points Punti da aggiungere (valori negativi per sottrarre)
     * @param string $action Identificativo univoco dell'azione
     * @param string $data Dati aggiuntivi opzionali
     * @return bool True in caso di successo
     * @throws NotEnoughPointsException Se non ci sono abbastanza punti da sottrarre
     * @throws UserNotFoundException Se l'utente non esiste
     */
    public function addPoints(int $userId, int $points, string $action, string $data = ''): bool;
    
    /**
     * Verifica se un utente può modificare i punti di un altro utente
     * 
     * @param int $modifierId ID dell'utente che richiede la modifica
     * @param int $targetUserId ID dell'utente target
     * @return bool True se l'utente può modificare i punti
     */
    public function canUserModifyPoints(int $modifierId, int $targetUserId): bool;
}
