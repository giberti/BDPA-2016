<?php

    // include the passwords and db configuration
    require 'configuration.php';

    // suppress deprecated warnings thrown by newer php
    error_reporting(E_ALL & ~E_DEPRECATED);

    // connect to the db server
    $mysql = mysql_connect($db_server, $db_username, $db_password);
    if (!$mysql) {
        die("Unable to connect to database server '" . $db_server . '" with username "'  . $db_username . '"');
    }

    if (!mysql_select_db($db_schema, $mysql)) {
        die("Unable to find the database '" . $db_schema . "'");
    }

    // Hacky and shallow way to remove slashes from array values
    function removeMagicQuotes(&$array) {
        foreach ($array as $key => $value) {
            $array[$key] = stripslashes($value);
        }
    }

    // Remove slashes from _GET and _POST if magic quotes are enabled
    if (get_magic_quotes_gpc()) {
        removeMagicQuotes($_POST);
        removeMagicQuotes($_GET);
    }
