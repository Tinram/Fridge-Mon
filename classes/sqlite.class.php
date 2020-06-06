<?php

declare(strict_types=1);

require_once('config/config.php');


abstract class SQLA extends SQLite3
{
    /**
        * SQLite database access class.
        *
        * @author       Martin Latter
        * @copyright    Martin Latter 31/03/2015
        * @version      0.22
        * @license      GNU GPL version 3.0 (GPL v3); http://www.gnu.org/licenses/gpl.html
        * @link         https://github.com/Tinram/Fridge-Mon.git
    */


    ###############################################################
    # SQLite database configuration from config.php constants.
    ###############################################################
    /** @var string $sDB, database name */
    private $sDB = CONFIG_DATABASE;
    /** @var string $sTableName, table name */
    protected $sTableName = CONFIG_TABLE;
    ###############################################################

    ###############################################################
    # App and encoding configuration.
    ###############################################################
    /** @var boolean $bUnicode, Unicode toggle */
    private $bUnicode = CONFIG_UNICODE;
    /** @var string $sEncoding, encoding used */
    private $sEncoding = CONFIG_ENCODING;
    ###############################################################


    public function __construct()
    {
        if ($this->bUnicode)
        {
            mb_internal_encoding($this->sEncoding);
        }

        if (file_exists($this->sDB))
        {
            $this->open($this->sDB);
        }
        else
        {
            die('<p class="error">' . __METHOD__ . '() &ndash; <em>' . $this->sDB . '</em> file not found!</p>');
        }
    }


    public function __destruct()
    {
        $this->close();
    }
}
