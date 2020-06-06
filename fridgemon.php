<?php

declare(strict_types=1);

############################################
require('classes/helpers.class.php');
Helpers::validateUser();
require('classes/fridgemon.class.php');
############################################

$oFM = new FridgeMon();

?><!DOCTYPE html>

<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?php echo CONFIG_APP_NAME; ?></title>
        <meta name="application-name" content="<?php echo CONFIG_APP_NAME; ?>">
        <meta name="copyright" content="&copy; <?php echo date('Y'); ?> Tinram">
        <link rel="stylesheet" type="text/css" href="css/calendar.css">
        <link rel="stylesheet" type="text/css" href="css/<?php echo CONFIG_APP_FILENAME; ?>.css">
        <script src="js/calendar.js"></script>
        <script src="js/<?php echo CONFIG_APP_FILENAME; ?>.js"></script>
    </head>

    <body>

        <p id="nojs">Please enable your browser's JavaScript.</p>

        <h1><?php echo '<a href="' . Helpers::selfSafe() . '">' . CONFIG_APP_NAME . '</a>'; ?></h1>

        <div id="maincont">
            <?php
                $oFM->processFormData();
            ?>

        </div>

    </body>

</html>