<?php

declare(strict_types=1);

require('classes/sqlite.class.php');


final class FridgeMon extends SQLA
{
    /**
        * Fridge Monitor.
        *
        * @author       Martin Latter
        * @copyright    Martin Latter 21/05/2020
        * @version      0.03
        * @license      GNU GPL version 3.0 (GPL v3); http://www.gnu.org/licenses/gpl.html
        * @link         https://github.com/Tinram/Fridge-Mon.git
    */

    /** @var array<mixed> $aItemDetails */
    private $aItemDetails = [];


    /**
        * Show table of all food items.
        *
        * @return  void
    */
    private function showAllFood(): void
    {
        $i = 0;
        $sRowType = '';

        $sAllQuery = '
            SELECT id, item, expiry, notes, creator
            FROM ' . $this->sTableName . '
            ORDER BY expiry ASC';

        $rResults = $this->query($sAllQuery);

        echo '<div id="today">' . date('d M', time()) . '</div>';
?>

            <table id="allitems">
                <col id="nm"><col><col id="notes"><col><col>
                <thead>
                    <tr>
                        <th>item</th>
                        <th>expiry</th>
                        <th>notes</th>
                        <th>creator</th>
                    </tr>
                </thead>
                <tbody>
<?php
        while ($aRow = $rResults->fetchArray(SQLITE3_ASSOC))
        {
            $sRowType = ($i % 2) ? 'teven' : 'todd';

            $aRow['expiry'] = (is_null($aRow['expiry'])) ? '-' : $aRow['expiry'];

            $bExpired = time() > strtotime($aRow['expiry']) ? true : false;

            $bInvalidDate = ( ! preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/i', $aRow['expiry'])) ? true : false;
            $d = ($bInvalidDate) ? '' : date('D d M', strtotime($aRow['expiry']));

            echo '
                    <tr class="' . $sRowType . '">
                        <td><a href="' . Helpers::selfSafe() . '?id=' . $aRow['id'] . '"' . ($bExpired ? ' class="expired"' : '') . '>' . $aRow['item'] . '</a></td>
                        <td><a href="' . Helpers::selfSafe() . '?id=' . $aRow['id'] . '"' . ($bExpired ? ' class="expired"' : '') . '>' . $d . '</a></td>
                        <td><textarea readonly class="' . $sRowType . '">' . $aRow['notes'] . '</textarea></td>
                        <td>' . $aRow['creator'] . '</td>
                    </tr>';
            $i++;
        }
?>

                </tbody>
                <tfoot></tfoot>
            </table>
<?php
    }


    /**
        * Form logic.
        *
        * @return  void
    */
    public function processFormData(): void
    {
        $bResult = false;

        if ( ! isset($_POST['delete']))
        {
            $_SESSION['bDeleteFlag'] = false;
        }

        $this->displaySearchForms();

        if (isset($_GET['id']))
        {
            $iCleanID = (int) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

            if ($iCleanID === 0)
            {
                die('<p class="error">Nice try at hacking the URL!</p>');
            }

            $bResult = $this->getSpecificItem($iCleanID);

            if ($bResult)
            {
                $this->displayItem();
            }
        }
        else if (isset($_POST['save']))
        {
            $aSaveItem = $this->saveItem();
            echo $aSaveItem[1];

            if ($aSaveItem[0])
            {
                $this->showAllFood();
            }
        }
        else if (isset($_POST['new_item']))
        {
            $this->displayItem();
        }
        else if (isset($_POST['d']))
        {
            echo $this->deleteItem();
            $this->showAllFood();
        }
        else
        {
            $this->showAllFood();
        }
    }


    /**
        * Display top buttons.
        *
        * @return  void
    */
    private function displaySearchForms(): void
    {
?>

            <div id="searchcont">
                <input type="text" name="search_item" id="search_item" maxlength="40" title="search on name and date" placeholder="search">
            </div>

            <div id="newcont">
                <form id="createnewitem" accept-charset="utf8" method="post" action="<?php echo Helpers::selfSafe(); ?>">
                    <input type="hidden" name="new_item">
                    <div><input type="submit" name="new" id="new" value="new" title="create new item" class="updatebutton"></div>
                </form>
            </div>

            <div class="formspacer"></div>

            <div id="submissionerrors" class="error"></div>
<?php

    }


    /**
        * Get item info from ID passed and populate class variable $aItemDetails if successful.
        *
        * @param   integer $iID
        *
        * @return  boolean
    */
    private function getSpecificItem(int $iID): bool
    {
        if ($iID === 0)
        {
            die('<p class="error">Nice try at hacking the URL!</p>');
        }

        $sItemQuery = '
            SELECT *
            FROM ' . $this->sTableName . '
            WHERE id = :id';

        $oStmt = $this->prepare($sItemQuery);
        $oStmt->bindValue(':id', $iID, SQLITE3_INTEGER);
        $rResult = $oStmt->execute();
        $this->aItemDetails = $rResult->fetchArray(SQLITE3_ASSOC);
        $rResult->finalize();

        if (count($this->aItemDetails) === 0)
        {
            echo '<p id="complete" class="error">No such food item found.</p>';
            return false;
        }
        else
        {
            return true;
        }
    }


    /**
        * Display information form for new / existing item.
        *
        * @return  void
    */
    private function displayItem(): void
    {
        $_SESSION['bAddedItem'] = false;
        $_SESSION['bDeleted'] = false;

        $bExistItem = (count($this->aItemDetails) !== 0) ? true : false;
?>
            <div id="itemcont">

                <form id="itemform" accept-charset="utf-8" method="post" action="<?php echo Helpers::selfSafe(); ?>">

                    <div><label for="item">item <span>*</span></label> <input type="text" name="item" id="item" maxlength="30" value="<?php if ($bExistItem) {echo $this->aItemDetails['item'];} ?>"></div>
                    <div><label for="expiry">expiry <span>*</span></label> <input type="text" name="expiry" id="expiry" maxlength="10" value="<?php if ($bExistItem) {echo $this->aItemDetails['expiry'];} ?>"><script type="text/javascript">new tcal({formname:'itemform', controlname:'expiry'});</script></div>
                    <div><label for="notes">notes</label> <textarea name="notes" id="notes" maxlength="512"><?php if ($bExistItem) {echo str_replace('~~', "\r\n", $this->aItemDetails['notes']);} ?></textarea></div>

                <?php
                    if ($bExistItem)
                    {
                ?>
                    <div class="info"><label for="added">added</label> <input type="text" id="added" value="<?php echo $this->aItemDetails['added']; ?>" readonly></div>
                    <div class="info"><label for="updated">updated</label> <input type="text" id="updated" value="<?php echo $this->aItemDetails['updated']; ?>" readonly></div>
                    <div class="info"><label for="updater">updater</label> <input type="text" id="updater" value="<?php echo $this->aItemDetails['updater']; ?>" readonly></div>

                    <input type="hidden" name="update_item">
                    <input type="hidden" name="id" id="id" value="<?php echo $this->aItemDetails['id']; ?>">
                <?php

                    }
                    else
                    {
                        echo '  <input type="hidden" name="new_item" id="new_item">';
                    }
                ?>

                    <input type="hidden" name="save">
                    <div><input type="submit" id="savebutton" value="save" class="updatebutton"></div>

                </form>

                <div id="quit"><a href="<?php echo Helpers::selfSafe(); ?>">quit</a></div>

            </div>

<?php
        if ($bExistItem)
        {
?>
            <div id="itemdelete">
                <form id="delete" accept-charset="utf8" method="post" action="<?php echo Helpers::selfSafe(); ?>">
                    <input type="hidden" name="id" id="id" value="<?php echo $this->aItemDetails['id']; ?>">
                    <input type="hidden" name="d">
                    <div><input type="submit" id="deletebutton" value="delete" title="delete this item!" class="updatebutton"></div>
                </form>
            </div>
<?php
        }
    }


    /**
        * Save item details to database.
        *
        * @return  array
    */
    private function saveItem(): array
    {
        if (isset($_SESSION['bAddedItem']) && $_SESSION['bAddedItem'] === true)
        {
            return [ false, '<p class="error">Did you refresh this page? &ndash; not allowing duplicate data to be added to the database!</p>
            <p class="success">Return to <a href="' . Helpers::selfSafe() . '">editing</a>.</p>' ];
        }

        $aFilters =
        [
            'id'     => FILTER_SANITIZE_NUMBER_INT,
            'item'   => FILTER_SANITIZE_STRING,
            'expiry' => FILTER_SANITIZE_STRING,
            'notes'  => FILTER_SANITIZE_STRING
        ];

        $aCleanPosts = filter_input_array(INPUT_POST, $aFilters);

        $aCleanPosts['notes'] = str_replace('\r\n', '~~', $aCleanPosts['notes']);

        if ($aCleanPosts['item'] === '')
        {
            return [ false, '<p class="error">Item is blank!<br>Please click the back button and amend the details.</p>' ];
        }

        if (isset($_POST['update_item'])) # update existing item details
        {
            $iID = (int) $aCleanPosts['id'];

            $sUpdateItem = '
                UPDATE ' . $this->sTableName . '
                SET
                    item = :i,
                    expiry = :e,
                    notes = :n,
                    updater = :u,
                    updated = DATETIME("NOW", "localtime")
                WHERE id = :id';

            $oStmt = $this->prepare($sUpdateItem);
            $oStmt->bindValue(':i', trim($aCleanPosts['item']), SQLITE3_TEXT);
            $oStmt->bindValue(':e', trim($aCleanPosts['expiry']), SQLITE3_TEXT);
            $oStmt->bindValue(':n', trim($aCleanPosts['notes']), SQLITE3_TEXT);
            $oStmt->bindValue(':u', $_SESSION['sVerifiedName'], SQLITE3_TEXT);
            $oStmt->bindValue(':id', $iID, SQLITE3_INTEGER);
            $rResult = $oStmt->execute();

            if ($rResult !== false)
            {
                $_SESSION['bAddedItem'] = true;
                return [ true, '<p id="complete" class="success">Item details saved.</p>' ];
            }
            else {
                return [ false, '<p id="complete" class="error">No changes made.</p>' ];
            }
        }
        else if (isset($_POST['new_item']))
        {
            $aResult = [];
            $sItem = trim(strtolower($aCleanPosts['item']));

            # check for duplicate
            $sExistingItem = '
                SELECT id
                FROM ' . $this->sTableName . '
                WHERE
                    LOWER(item) = :i
                LIMIT 1';

            $oStmt = $this->prepare($sExistingItem);
            $oStmt->bindValue(':i', $sItem, SQLITE3_TEXT);
            $rResult = $oStmt->execute();
            $aResult = $rResult->fetchArray(SQLITE3_ASSOC);
            $rResult->finalize();

            if ($aResult !== false)
            {
                return [ false, '<p class="error">Item already exists with the same name!<br>Please click the back button and amend the details.</p>'];
            }

            $sInsert = '
                INSERT INTO ' . $this->sTableName . '
                (item, expiry, notes, creator, added)
                VALUES (:i, :e, :n, :c, DATETIME("NOW", "localtime"))';

            if (empty(strtotime($aCleanPosts['expiry'])))
            {
                $aCleanPosts['expiry'] = null;
            }

            $oStmt = $this->prepare($sInsert);
            $oStmt->bindValue(':i', trim($aCleanPosts['item']), SQLITE3_TEXT);
            $oStmt->bindValue(':e', $aCleanPosts['expiry'], SQLITE3_TEXT);
            $oStmt->bindValue(':n', trim($aCleanPosts['notes']), SQLITE3_TEXT);
            $oStmt->bindValue(':c', $_SESSION['sVerifiedName'], SQLITE3_TEXT);
            $rResult = $oStmt->execute();

            if ($rResult !== false)
            {
                $_SESSION['bAddedItem'] = true;
                return [ true, '<p id="complete" class="success">Item saved.</p>' ];
            }
            else
            {
                return [ false, '<p class="error">Item insertion failure.</p>' ];
            }
        }
    }


    /**
        * Delete item.
        *
        * @return  string
    */
    private function deleteItem(): string
    {
        if (isset($_SESSION['bDeleted']) && $_SESSION['bDeleted'] === true)
        {
            return '<p class="error">Did you refresh this page? &ndash; not allowing a duplicate delete attempt.</p>';
        }

        $iID = (int) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($iID === 0)
        {
            return '<p class="error">Seems like hacking &ndash; abort!</p>';
        }

        $sDelete = '
            DELETE FROM ' . $this->sTableName . '
            WHERE id = :id';

        $oStmt = $this->prepare($sDelete);
        $oStmt->bindValue(':id', $iID, SQLITE3_INTEGER);
        $rResult = $oStmt->execute();

        if ($rResult !== false)
        {
            $_SESSION['bDeleted'] = true;
            return '<p id="complete" class="success">Item deleted.</p>';
        }
        else
        {
            return '<p class="error">Item deletion failure.</p>';
        }
    }
}
