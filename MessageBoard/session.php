<?php

    session_start();
    $sitehash = md5(__FILE__);
    if (!isset($_SESSION['sitehash'])) {
        $_SESSION['sitehash'] = $sitehash;
    }

    // Check and see if the current session is for this site, if not
    // clear it.
    if ($sitehash != $_SESSION['sitehash']) {
        foreach (array_keys($_SESSION) as $key) {
            unset($_SESSION[$key]);
        }
        $_SESSION['sitehash'] = $sitehash;
    }