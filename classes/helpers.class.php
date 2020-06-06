<?php

declare(strict_types=1);

require_once('config/config.php');


class Helpers
{
    /**
        * Class wrapper for static helper methods.
        *
        * @author       Martin Latter
        * @copyright    Martin Latter 03/04/2015
        * @version      0.37
        * @license      GNU GPL version 3.0 (GPL v3); http://www.gnu.org/licenses/gpl.html
        * @link         https://github.com/Tinram/Fridge-Mon.git
    */


    /** @const integer CACHE_EXPIRY */
    const CACHE_EXPIRY = CONFIG_CACHE_EXPIRY;

    /** @const integer SESSION_TIMEOUT */
    const SESSION_TIMEOUT = CONFIG_SESSION_TIMEOUT;

    /** @const string CONFIG_ENCODING */
    const ENCODING = CONFIG_ENCODING;

    /** @const string HASH */
    const HASH = CONFIG_HASH;


    /**
        * Validate user access for editing pages.
        *
        * @return  void
    */

    public static function validateUser(): void
    {
        ini_set('session.use_only_cookies', 'On');
        ini_set('session.use_strict_mode', 'On');
        ini_set('session.use_trans_sid', 'Off');
        ini_set('session.cookie_httponly', 'On');
        ini_set('session.cookie_lifetime', (string) self::SESSION_TIMEOUT);
        //ini_set('session.cookie_secure', 'On'); /* enable if on HTTPS */
        ini_set('session.cookie_samesite', 'Strict'); /* PHP v.7.3+ */
        ini_set('session.gc_divisor', '5');
        ini_set('session.gc_maxlifetime', (string) self::SESSION_TIMEOUT);
        ini_set('session.cache_expire', (string) self::CACHE_EXPIRY);
        ini_set('session.sid_length', '48');
        ini_set('session.sid_bits_per_character', '6');
        ini_set('session.hash_function', 'sha256');

        session_start();

        if ( ! isset($_SESSION['sVerifiedName']))
        {
            self::sessionDeath();
            die('<p style="color:#c00;font-weight:bold;"><a href="index.php">' . CONFIG_APP_NAME . ':</a> no access authorisation!</p>');
        }

        if (time() > ($_SESSION['iLastClick'] + (self::CACHE_EXPIRY * 60)))
        {
            self::sessionDeath();
            die('<a href="index.php">' . CONFIG_APP_NAME . ':</a> session expired.');
        }

        if ($_SESSION['sLoginToken'] !== hash(self::HASH, $_SESSION['sVerifiedName'] . $_SESSION['sLoginNonce']))
        {
            self::sessionDeath();
            exit;
        }

        # check browser hash for session hijack
        if ($_SESSION['sBrowser'] !== hash(self::HASH, $_SERVER['HTTP_USER_AGENT']))
        {
            self::sessionDeath();
            exit;
        }

        $_SESSION['iLastClick'] = time();
        setcookie(session_name(), '', time() + self::CACHE_EXPIRY * 60);
    }


    /**
        * Used in self::validateUser()
        *
        * @return  void
    */

    private static function sessionDeath(): void
    {
        $_SESSION = [];

        if (isset($_COOKIE[session_name()]))
        {
            setcookie(session_name(), '', time() - 42000, '/');
        }

        session_destroy();
    }


    /**
        * Sanitize strings from most (not all) XSS.
        *
        * @param   string $sTainted
        *
        * @return  string
    */

    public static function webSafe(string $sTainted): string
    {
        return htmlentities(strip_tags(stripslashes(trim($sTainted))), ENT_QUOTES, self::ENCODING);
    }


    /**
        * Sanitize PHP_SELF.
        *
        * @return  string
    */

    public static function selfSafe(): string
    {
        return htmlentities(strip_tags($_SERVER['PHP_SELF']), ENT_QUOTES, self::ENCODING);
    }
}
