<?php
/**
 *
 * Cash Points System Extension for phpBB.
 *
 * @package marcozp/cash
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace marcozp\cash\service\api\exception;

/**
 * Eccezione lanciata quando un utente non viene trovato
 */
class UserNotFoundException extends \RuntimeException
{
    /**
     * Costruttore
     *
     * @param string $message Messaggio personalizzato (opzionale)
     * @param int $code Codice errore (opzionale)
     * @param \Throwable|null $previous Eccezione precedente (opzionale)
     */
    public function __construct($message = "", $code = 0, \Throwable $previous = null)
    {
        parent::__construct(
            $message ?: 'Utente non trovato',
            $code,
            $previous
        );
    }
}
