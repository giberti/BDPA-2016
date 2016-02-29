<?php

    // include the passwords and db configuration
    require __DIR__ . '/configuration.php';

    // connect to the db server
    $mysql = mysql_connect($db_server, $db_username, $db_password);
    if (!$mysql) {
        http_response_code(500);
        die("Unable to connect to database server '" . $db_server . '" with username "'  . $db_username . '"');
    }

    if (!mysql_select_db($db_schema, $mysql)) {
        http_response_code(500);
        die("Unable to find the database '" . $db_schema . "'");
    }
