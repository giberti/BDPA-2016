<?php


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

if (!function_exists('http_response_code')) {

    $current_http_response_code_value = 200; // default is 'OK'
    function http_response_code($code = null) {

        global $current_http_response_code_value;
        if (null === $code) {
            return $current_http_response_code_value;
        }

        switch ($code) {
            case 100: $message = 'Continue'; break;

            case 200: $message = 'OK'; break;
            case 201: $message = 'Created'; break;
            case 202: $message = 'Accepted'; break;
            case 204: $message = 'No content'; break;

            case 300: $message = 'Multiple choices'; break;
            case 301: $message = 'Moved temporarily'; break;
            case 302: $message = 'Moved permanently'; break;
            case 304: $message = 'Not modified'; break;

            case 400: $message = 'Bad request'; break;
            case 401: $message = 'Unauthorized'; break;
            case 403: $message = 'Forbidden'; break;
            case 404: $message = 'Not found'; break;
            case 409: $message = 'Conflict'; break;
            case 410: $message = 'Gone'; break;
            case 412: $message = 'Precondition failed'; break;

            case 500: $message = 'Server error'; break;
            case 501: $message = 'Not implemented'; break;

            default:
                throw new Exception('Unknown status code ' . $code );
        }

        $current_http_response_code_value = $code;
        $protocol                         = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';
        header("{$protocol} {$code} {$message}");

        return null;
    }
}