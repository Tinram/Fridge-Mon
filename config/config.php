<?php

/**
    * Configuration file for Fridgemon.
    * @author  Martin Latter
    * @link    https://github.com/Tinram/Fridge-Mon.git
*/

declare(strict_types=1);

###########################################################
## SQLITE DB
###########################################################
define('CONFIG_DATABASE', 'db/fridgemon.sqlite3');
define('CONFIG_TABLE', 'fridge');
###########################################################


###########################################################
## APP
###########################################################
define('TIMEZONE', 'Europe/London');
define('CONFIG_APP_NAME', 'Fridgemon');
define('CONFIG_APP_FILENAME', strtolower(CONFIG_APP_NAME));
ini_set('date.timezone', TIMEZONE);
###########################################################


###########################################################
## ENCODING
###########################################################
define('CONFIG_UNICODE', true);
define('CONFIG_ENCODING', 'UTF-8');
###########################################################


###########################################################
## SESSION
###########################################################
define('CONFIG_CACHE_EXPIRY', 30); # 30 mins
define('CONFIG_SESSION_TIMEOUT', 1800); # 30 mins
define('CONFIG_HACK_DELAY', 3600);
define('CONFIG_HASH', 'sha256');
###########################################################


###########################################################
## USERS
###########################################################
define('CONFIG_USER1', 'martin');
define('CONFIG_USER1_PASS', 'fd74bdd901857b89f5737e5352a2a8a2d1f000aa4bed4aee47c95afaa37d0f99'); # SHA-256
define('CONFIG_USER2', 'alison');
define('CONFIG_USER2_PASS', 'fd74bdd901857b89f5737e5352a2a8a2d1f000aa4bed4aee47c95afaa37d0f99');
###########################################################
